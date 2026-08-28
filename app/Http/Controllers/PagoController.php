<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Services\KhipuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;

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

    public function procesar(Request $request, Reserva $reserva, KhipuService $khipuService)
    {
        /*
            |--------------------------------------------------------------------------
            | VALIDAR MEDIO DE PAGO
            |--------------------------------------------------------------------------
        */

        $request->validate([
            'medio_pago' => [
                'required',
                'in:WEBPAY,KHIPU',
            ],
        ]);


        /*
            |--------------------------------------------------------------------------
            | VALIDAR ESTADO DE LA RESERVA
            |--------------------------------------------------------------------------
        */

        if ($reserva->estado !== 'PENDIENTE_PAGO') {

            return back()->with(
                'error',
                'Esta reserva ya no se encuentra pendiente de pago.'
            );
        }


        /*
            |--------------------------------------------------------------------------
            | VALIDAR TIEMPO DE PAGO
            |--------------------------------------------------------------------------
        */

        if (
            $reserva->pago_expira_at &&
            now()->greaterThan($reserva->pago_expira_at)
        ) {

            $reserva->update([
                'estado' => 'VENCIDA_PAGO',
            ]);

            return back()->with(
                'error',
                'El tiempo disponible para realizar el pago ha expirado.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | WEBPAY
    |--------------------------------------------------------------------------
    */

        if ($request->medio_pago === 'WEBPAY') {

            return $this->iniciarPagoWebpay(
                $reserva
            );
        }


        /*
    |--------------------------------------------------------------------------
    | KHIPU
    |--------------------------------------------------------------------------
    */

        if ($request->medio_pago === 'KHIPU') {

            try {

                $pago = $khipuService->crearPago(
                    $reserva
                );

                if (empty($pago['payment_url'])) {

                    return back()->with(
                        'error',
                        'No fue posible obtener la URL de pago de Khipu.'
                    );
                }

                return redirect()->away(
                    $pago['payment_url']
                );
            } catch (\Throwable $exception) {

                report($exception);

                return back()->with(
                    'error',
                    'No fue posible iniciar el pago mediante transferencia.'
                );
            }
        }


        /*
    |--------------------------------------------------------------------------
    | MEDIO NO VÁLIDO
    |--------------------------------------------------------------------------
    */

        return back()->with(
            'error',
            'El medio de pago seleccionado no es válido.'
        );
    }

    public function retornoWebpay(
        Request $request,
        Reserva $reserva
    ) {
        $token = $request->get('token_ws');

        if (!$token) {

            return redirect()
                ->route('reservas.pago', $reserva)
                ->with(
                    'error',
                    'No se recibió el token de Webpay.'
                );
        }


        $options = new Options(
            config('services.transbank.api_key'),
            config('services.transbank.commerce_code'),
            Options::ENVIRONMENT_INTEGRATION
        );

        $transaction = new Transaction($options);


        try {

            $response = $transaction->commit(
                $token
            );
        } catch (\Throwable $exception) {

            report($exception);

            return redirect()
                ->route('reservas.pago', $reserva)
                ->with(
                    'error',
                    'No fue posible confirmar el pago con Webpay.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDAR PAGO APROBADO
    |--------------------------------------------------------------------------
    */

        if (
            $response->getStatus() === 'AUTHORIZED'
            && $response->getResponseCode() === 0
        ) {

            $reserva->update([
                'estado' => 'PAGADA',
                'medio_pago' => 'WEBPAY',
                'pagada_at' => now(),
            ]);


            session()->forget([
                'webpay.reserva_id',
                'webpay.buy_order',
                'webpay.token',
            ]);


            return redirect()
                ->route(
                    'reservas.resultado',
                    $reserva
                )
                ->with(
                    'success',
                    'El pago fue realizado correctamente.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | PAGO RECHAZADO
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route(
                'reservas.resultado',
                $reserva
            )
            ->with(
                'error',
                'El pago con Webpay no fue aprobado.'
            );
    }
}
