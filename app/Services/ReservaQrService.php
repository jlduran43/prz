<?php

namespace App\Services;

use App\Models\Reserva;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

class ReservaQrService
{
    public function generar(Reserva $reserva): string
    {
        /*
         * Si la reserva todavía no tiene token,
         * generamos uno.
         */
        if (!$reserva->token_verificacion) {

            do {
                $token = Str::random(64);
            } while (
                Reserva::where(
                    'token_verificacion',
                    $token
                )->exists()
            );

            $reserva->token_verificacion = $token;
            $reserva->save();
        }

        /*
         * URL que contendrá el QR.
         */
        $contenido = route(
            'reservas.verificar',
            [
                'token' => $reserva->token_verificacion,
            ]
        );

        /*
         * Generar QR.
         */
        $result = new Builder(
            writer: new PngWriter(),
            data: $contenido,
            size: 300,
            margin: 10
        );

        $result = $result->build();

        /*
         * Carpeta donde guardaremos los QR.
         */
        $directorio = storage_path(
            'app/public/qrs'
        );

        if (!is_dir($directorio)) {
            mkdir(
                $directorio,
                0755,
                true
            );
        }

        $folio = 'RES-' .
            str_pad(
                $reserva->id,
                6,
                '0',
                STR_PAD_LEFT
            );

        $nombreArchivo =
            'reserva-' . $folio . '.png';

        $rutaCompleta =
            $directorio .
            DIRECTORY_SEPARATOR .
            $nombreArchivo;

        $result->saveToFile(
            $rutaCompleta
        );

        return 'qrs/' . $nombreArchivo;
    }
}