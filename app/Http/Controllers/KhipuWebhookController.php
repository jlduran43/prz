<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KhipuWebhookController extends Controller
{
    public function recibir(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Obtener BODY ORIGINAL
        |--------------------------------------------------------------------------
        |
        | No debemos reconstruir el JSON antes de verificar la firma.
        |
        */

        $rawBody = $request->getContent();


        /*
        |--------------------------------------------------------------------------
        | 2. Verificar firma Khipu
        |--------------------------------------------------------------------------
        */

        if (!$this->firmaValida($request, $rawBody)) {

            Log::warning(
                'Webhook Khipu con firma inválida.'
            );

            return response()->json([
                'message' => 'Firma inválida',
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Leer JSON
        |--------------------------------------------------------------------------
        */

        $data = json_decode(
            $rawBody,
            true
        );


        if (!is_array($data)) {

            return response()->json([
                'message' => 'JSON inválido',
            ], 400);
        }


        $paymentId = $data['payment_id'] ?? null;

        $transactionId = $data['transaction_id'] ?? null;


        if (!$paymentId || !$transactionId) {

            return response()->json([
                'message' => 'Datos incompletos',
            ], 400);
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Procesar de forma atómica
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $data,
            $paymentId,
            $transactionId
        ) {

            $reserva = Reserva::where(
                'khipu_transaction_id',
                $transactionId
            )
                ->lockForUpdate()
                ->first();


            if (!$reserva) {

                Log::warning(
                    'Reserva Khipu no encontrada.',
                    [
                        'transaction_id' => $transactionId,
                    ]
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Evitar procesar dos veces
            |--------------------------------------------------------------------------
            */

            if ($reserva->estado === 'PAGADA') {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | 6. Comprobar ID Khipu
            |--------------------------------------------------------------------------
            */

            if (
                $reserva->khipu_payment_id !== $paymentId
            ) {

                Log::warning(
                    'payment_id de Khipu no coincide.',
                    [
                        'reserva' => $reserva->id,
                    ]
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | 7. Comprobar receiver
            |--------------------------------------------------------------------------
            */

            if (
                (string) ($data['receiver_id'] ?? '') !==
                (string) config('services.khipu.receiver_id')
            ) {

                Log::warning(
                    'Receiver ID Khipu incorrecto.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | 8. Comprobar moneda
            |--------------------------------------------------------------------------
            */

            if (($data['currency'] ?? '') !== 'CLP') {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | 9. Comprobar monto
            |--------------------------------------------------------------------------
            */

            $montoKhipu = (int) round(
                (float) ($data['amount'] ?? 0)
            );

            $montoReserva = (int) round(
                $reserva->precio_total
            );


            if ($montoKhipu !== $montoReserva) {

                Log::warning(
                    'Monto Khipu no coincide con la reserva.',
                    [
                        'reserva' => $reserva->id,
                        'khipu' => $montoKhipu,
                        'reserva_monto' => $montoReserva,
                    ]
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | 10. La conciliación confirma el pago
            |--------------------------------------------------------------------------
            */

            if (
                empty($data['conciliation_date'])
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | 11. Marcar reserva pagada
            |--------------------------------------------------------------------------
            */

            $reserva->update([

                'estado' => 'PAGADA',

                'medio_pago' => 'KHIPU',

                'pagada_at' => now(),

            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Khipu necesita HTTP 200
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'ok' => true,
        ]);
    }


    private function firmaValida(
        Request $request,
        string $rawBody
    ): bool {

        $header = $request->header(
            'x-khipu-signature'
        );


        if (!$header) {
            return false;
        }


        $timestamp = null;
        $signature = null;


        foreach (explode(',', $header) as $parte) {

            [$clave, $valor] = array_pad(
                explode('=', trim($parte), 2),
                2,
                null
            );


            if ($clave === 't') {
                $timestamp = $valor;
            }


            if ($clave === 's') {
                $signature = $valor;
            }
        }


        if (!$timestamp || !$signature) {
            return false;
        }


        /*
         * timestamp + "." + JSON original
         */

        $cadena = $timestamp . '.' . $rawBody;


        $firmaCalculada = base64_encode(
            hash_hmac(
                'sha256',
                $cadena,
                config('services.khipu.secret'),
                true
            )
        );


        return hash_equals(
            $firmaCalculada,
            $signature
        );
    }
}
