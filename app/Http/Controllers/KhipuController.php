<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Services\KhipuService;
use Illuminate\Http\Request;

class KhipuController extends Controller
{
    public function iniciar(Reserva $reserva, KhipuService $khipu) 
    {
        if ($reserva->estado !== 'PENDIENTE_PAGO') {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Esta reserva no se encuentra pendiente de pago.'
                );
        }


        if (
            $reserva->pago_expira_at &&
            now()->greaterThan($reserva->pago_expira_at)
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'El tiempo disponible para realizar el pago ha expirado.'
                );
        }


        $pago = $khipu->crearPago($reserva);


        if (empty($pago['payment_url'])) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Khipu no entregó una URL de pago.'
                );
        }


        return redirect()->away(
            $pago['payment_url']
        );
    }


    public function retorno(Reserva $reserva)
    {
        /*
         * IMPORTANTE:
         *
         * Volver desde Khipu NO significa automáticamente
         * que el dinero esté conciliado.
         */

        return view(
            'reservas.pagos.khipu-retorno',
            compact('reserva')
        );
    }


    public function cancelar(Reserva $reserva)
    {
        return redirect()
            ->route('reservas.pago', $reserva)
            ->with(
                'warning',
                'El proceso de transferencia fue cancelado.'
            );
    }
}