<?php

namespace App\Http\Controllers;

use App\Mail\ReservaConfirmadaMail;
use App\Models\Reserva;
use App\Services\KhipuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;
use Illuminate\Support\Facades\Mail;

class PagoController extends Controller
{
    public function iniciarPagoWebpay(Reserva $reserva): View|RedirectResponse
    {
        if ($reserva->estado !== 'PENDIENTE_PAGO') {
            return redirect()
                ->route('reservas.resultado', $reserva);
        }

        if (
            $reserva->pago_expira_at &&
            $reserva->pago_expira_at->isPast()
        ) {
            $reserva->update([
                'estado' => 'VENCIDA_PAGO',
            ]);

            return redirect()
                ->route('reservas.resultado', $reserva)
                ->with(
                    'error',
                    'El tiempo para realizar el pago ha expirado.'
                );
        }

        $buyOrder = 'RES-' . $reserva->id . '-' . time();

        $sessionId = 'RESERVA-' . $reserva->id;

        $amount = (int) round($reserva->total);

        $returnUrl = route(
            'reservas.pago.webpay.retorno',
            $reserva
        );

        $options = new Options(
            config('services.transbank.api_key'),
            config('services.transbank.commerce_code'),
            Options::ENVIRONMENT_INTEGRATION
        );

        $transaction = new Transaction($options);

        try {
            $response = $transaction->create(
                $buyOrder,
                $sessionId,
                $amount,
                $returnUrl
            );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('reservas.pago', $reserva)
                ->with(
                    'error',
                    'No fue posible iniciar el pago con Webpay.'
                );
        }

        session([
            'webpay.reserva_id' => $reserva->id,
            'webpay.buy_order' => $buyOrder,
            'webpay.token' => $response->getToken(),
        ]);

        return view(
            'reservas.webpay-redireccion',
            [
                'url' => $response->getUrl(),
                'token' => $response->getToken(),
            ]
        );
    }

    public function procesar(Request $request, Reserva $reserva) 
    {
        $request->validate([
            'medio_pago' => 'required|in:WEBPAY,TRANSFERENCIA',
        ]);

        $medioPago = $request->input('medio_pago');

        if ($medioPago === 'WEBPAY') {

            return app(
                ReservaWizardController::class
            )->iniciarPagoWebpay(
                $reserva
            );
        }

        if ($medioPago === 'TRANSFERENCIA') {

            $khipuService = app(
                KhipuService::class
            );

            return $khipuService->crearPago(
                $reserva
            );
        }

        return back()->with(
            'error',
            'Medio de pago no válido.'
        );
    }
}
