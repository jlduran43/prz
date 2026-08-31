<?php

namespace App\Http\Controllers;

use App\Mail\ReservaConfirmadaMail;
use App\Models\Reserva;
use App\Services\KhipuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;

class PagoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PROCESAR MEDIO DE PAGO
    |--------------------------------------------------------------------------
    */

    public function procesar(
        Request $request,
        Reserva $reserva
    ) {
        $request->validate([
            'medio_pago' => 'required|in:WEBPAY,TRANSFERENCIA',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validar estado de la reserva
        |--------------------------------------------------------------------------
        */

        if ($reserva->estado !== 'PENDIENTE_PAGO') {
            return redirect()
                ->route('reservas.resultado', $reserva)
                ->with(
                    'error',
                    'La reserva ya no está pendiente de pago.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validar tiempo disponible
        |--------------------------------------------------------------------------
        */

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
                    'El tiempo disponible para pagar ha expirado.'
                );
        }

        $medioPago = $request->input('medio_pago');

        /*
        |--------------------------------------------------------------------------
        | WEBPAY
        |--------------------------------------------------------------------------
        */

        if ($medioPago === 'WEBPAY') {

            $reserva->update([
                'medio_pago' => 'WEBPAY',
            ]);

            return $this->iniciarPagoWebpay(
                $reserva
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSFERENCIA / KHIPU
        |--------------------------------------------------------------------------
        */

        if ($medioPago === 'TRANSFERENCIA') {

            $reserva->update([
                'medio_pago' => 'TRANSFERENCIA',
            ]);

            try {

                /*
                | Khipu solamente se instancia cuando realmente
                | se selecciona transferencia.
                */

                $khipuService = app(
                    KhipuService::class
                );

                $pago = $khipuService->crearPago(
                    $reserva
                );

                $url =
                    $pago['payment_url']
                    ?? $pago['simplified_transfer_url']
                    ?? $pago['url']
                    ?? null;

                if (!$url) {
                    throw new \RuntimeException(
                        'Khipu no devolvió una URL de pago.'
                    );
                }

                return redirect()->away(
                    $url
                );
            } catch (\Throwable $exception) {

                report($exception);

                return redirect()
                    ->route(
                        'reservas.pago',
                        $reserva
                    )
                    ->with(
                        'error',
                        'No fue posible iniciar el pago mediante transferencia.'
                    );
            }
        }

        return redirect()
            ->route(
                'reservas.pago',
                $reserva
            )
            ->with(
                'error',
                'El medio de pago seleccionado no es válido.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | INICIAR WEBPAY
    |--------------------------------------------------------------------------
    */

    public function iniciarPagoWebpay(Reserva $reserva): View|RedirectResponse
    {

        /*
        |--------------------------------------------------------------------------
        | Validar reserva
        |--------------------------------------------------------------------------
        */

        if ($reserva->estado !== 'PENDIENTE_PAGO') {
            return redirect()
                ->route(
                    'reservas.resultado',
                    $reserva
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validar vencimiento
        |--------------------------------------------------------------------------
        */

        if (
            $reserva->pago_expira_at &&
            $reserva->pago_expira_at->isPast()
        ) {
            $reserva->update([
                'estado' => 'VENCIDA_PAGO',
            ]);

            return redirect()
                ->route(
                    'reservas.resultado',
                    $reserva
                )
                ->with(
                    'error',
                    'El tiempo para realizar el pago ha expirado.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validar configuración Transbank
        |--------------------------------------------------------------------------
        */

        $apiKey = config(
            'services.transbank.api_key'
        );

        $commerceCode = config(
            'services.transbank.commerce_code'
        );

        if (!$apiKey || !$commerceCode) {

            return redirect()
                ->route(
                    'reservas.pago',
                    $reserva
                )
                ->with(
                    'error',
                    'Webpay no se encuentra configurado correctamente.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Datos transacción
        |--------------------------------------------------------------------------
        */

        $buyOrder =
            'RES-' .
            $reserva->id .
            '-' .
            time();

        $sessionId =
            'RESERVA-' .
            $reserva->id;

        $amount =
            (int) round(
                $reserva->total
            );

        $returnUrl = route(
            'reservas.pago.webpay.retorno',
            $reserva
        );

        /*
        |--------------------------------------------------------------------------
        | Crear transacción
        |--------------------------------------------------------------------------
        */

        try {

            $transaction =
                $this->webpayTransaction();

            $response =
                $transaction->create(
                    $buyOrder,
                    $sessionId,
                    $amount,
                    $returnUrl
                );

            /*
                |--------------------------------------------------------------------------
                | Guardar datos de la transacción Webpay
                |--------------------------------------------------------------------------
            */

            $reserva->update([
                'webpay_token' =>
                $response->getToken(),

                'webpay_buy_order' =>
                $buyOrder,
            ]);
        } catch (\Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'reservas.pago',
                    $reserva
                )
                ->with(
                    'error',
                    'No fue posible iniciar el pago con Webpay.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Guardar datos Webpay en sesión
        |--------------------------------------------------------------------------
        */

        session([
            'webpay.reserva_id' =>
            $reserva->id,

            'webpay.buy_order' =>
            $buyOrder,

            'webpay.token' =>
            $response->getToken(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirección hacia Transbank
        |--------------------------------------------------------------------------
        */

        return view(
            'reservas.webpay-redireccion',
            [
                'url' =>
                $response->getUrl(),

                'token' =>
                $response->getToken(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RETORNO WEBPAY
    |--------------------------------------------------------------------------
    */

    public function retornoWebpay(Request $request, Reserva $reserva)
    {
        $token = $request->get('token_ws');

        /*
        |--------------------------------------------------------------------------
        | Pago cancelado / retorno sin token
        |--------------------------------------------------------------------------
        */

        if (!$token) {

            return redirect()
                ->route(
                    'reservas.pago',
                    $reserva
                )
                ->with(
                    'error',
                    'El pago fue cancelado o no se recibió respuesta de Webpay.'
                );
        }

        /*
            |--------------------------------------------------------------------------
            | Guardar token devuelto por Webpay
            |--------------------------------------------------------------------------
        */

        $reserva->update([
            'webpay_token' => $token,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Confirmar transacción
        |--------------------------------------------------------------------------
        */

        try {

            $transaction =
                $this->webpayTransaction();

            $response =
                $transaction->commit(
                    $token
                );
        } catch (\Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'reservas.pago',
                    $reserva
                )
                ->with(
                    'error',
                    'No fue posible confirmar el pago con Webpay.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Pago aprobado
        |--------------------------------------------------------------------------
        */

        if (
            $response->getStatus() === 'AUTHORIZED' &&
            $response->getResponseCode() === 0
        ) {

            /*
            |--------------------------------------------------------------------------
            | Evitar procesar dos veces el mismo retorno
            |--------------------------------------------------------------------------
            */

            if ($reserva->estado !== 'PAGADA') {

                $reserva->update([
                    'estado' =>
                    'PAGADA',

                    'medio_pago' =>
                    'WEBPAY',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Enviar ticket / correo
            |--------------------------------------------------------------------------
            */

            if (!$reserva->ticket_enviado_at) {

                try {

                    Mail::to(
                        $reserva->email
                    )->send(
                        new ReservaConfirmadaMail(
                            $reserva
                        )
                    );

                    $reserva->update([
                        'ticket_enviado_at' =>
                        now(),

                        'ticket_email_error' =>
                        null,
                    ]);
                } catch (\Throwable $exception) {

                    report($exception);

                    $reserva->update([
                        'ticket_email_error' =>
                        $exception->getMessage(),
                    ]);
                }
            }

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
                    'Pago realizado correctamente.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Pago rechazado
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'reservas.pago',
                $reserva
            )
            ->with(
                'error',
                'El pago fue rechazado por Webpay.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN WEBPAY
    |--------------------------------------------------------------------------
    */

    private function webpayTransaction(): Transaction
    {
        $environment =
            config(
                'services.transbank.environment'
            ) === 'production'
            ? Options::ENVIRONMENT_PRODUCTION
            : Options::ENVIRONMENT_INTEGRATION;

        $options = new Options(
            config(
                'services.transbank.api_key'
            ),
            config(
                'services.transbank.commerce_code'
            ),
            $environment
        );

        return new Transaction(
            $options
        );
    }
}
