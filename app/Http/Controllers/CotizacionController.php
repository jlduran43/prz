<?php

namespace App\Http\Controllers;

use App\Mail\CotizacionGeneradaMail;
use App\Models\ConfiguracionCotizacion;
use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CotizacionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $buscar =
            trim(
                (string)
                $request->input(
                    'buscar',
                    ''
                )
            );

        $cotizaciones =
            Cotizacion::query()
            ->with([
                'tipoCliente',
            ])

            ->when(
                $buscar !== '',
                function ($query) use (
                    $buscar
                ) {

                    $query->where(
                        function ($subquery) use (
                            $buscar
                        ) {

                            $subquery
                                ->where(
                                    'folio',
                                    'like',
                                    "%{$buscar}%"
                                )

                                ->orWhere(
                                    'nombres',
                                    'like',
                                    "%{$buscar}%"
                                )

                                ->orWhere(
                                    'apellidos',
                                    'like',
                                    "%{$buscar}%"
                                )

                                ->orWhere(
                                    'nombre_entidad',
                                    'like',
                                    "%{$buscar}%"
                                )

                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$buscar}%"
                                );
                        }
                    );
                }
            )

            ->orderByDesc('id')

            ->paginate(15)

            ->withQueryString();

        return view(
            'cotizaciones.index',
            compact(
                'cotizaciones',
                'buscar'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */
    public function show(Cotizacion $cotizacion)
    {

        $cotizacion->load([
            'tipoCliente',
            'region',
            'comuna',
            'servicios',
        ]);

        return view(
            'cotizaciones.show',
            compact('cotizacion')
        );
    }

    public function descargarPdf(Cotizacion $cotizacion)
    {
        $configuracion = ConfiguracionCotizacion::first();

        $cotizacion->load([
            'tipoCliente',
            'region',
            'comuna',
            'servicios',
        ]);

        $pdf = Pdf::loadView(
            'cotizaciones.pdf',
            [
                'cotizacion' => $cotizacion,
                'configuracion' => $configuracion,
            ]
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            $cotizacion->folio . '.pdf'
        );
    }

    public function showPublico(Cotizacion $cotizacion, string $token)
    {
        if (
            !$cotizacion->token_acceso ||
            !hash_equals(
                $cotizacion->token_acceso,
                $token
            )
        ) {
            abort(
                403,
                'No tienes autorización para acceder a esta cotización.'
            );
        }

        return view(
            'cotizaciones.show',
            compact('cotizacion')
        );
    }

    public function anularAdmin(Request $request, Cotizacion $cotizacion)
    {
        if ($cotizacion->estado !== 'EMITIDA') {

            return back()->with(
                'error',
                'Solo se pueden anular cotizaciones emitidas.'
            );
        }

        $request->validate([
            'motivo_anulacion' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'motivo_anulacion.required' =>
            'Debe ingresar el motivo de la anulación.',
        ]);

        $cotizacion->update([
            'estado' => 'ANULADA',
            'anulada_at' => now(),
            'anulada_por_tipo' => 'FUNCIONARIO',
            'anulada_por_user_id' => auth()->id(),
            'motivo_anulacion' =>
            $request->motivo_anulacion,
        ]);

        return redirect()
            ->route(
                'admin.cotizaciones.show',
                $cotizacion
            )
            ->with(
                'success',
                'La cotización fue anulada correctamente.'
            );
    }

    public function anularPublico(Request $request, Cotizacion $cotizacion, string $token)
    {
        /*
            |--------------------------------------------------------------------------
            | Validar token
            |--------------------------------------------------------------------------
        */

        if (
            !$cotizacion->token_acceso ||
            !hash_equals(
                $cotizacion->token_acceso,
                $token
            )
        ) {
            abort(
                403,
                'No tienes autorización para modificar esta cotización.'
            );
        }

        /*
            |--------------------------------------------------------------------------
            | Validar estado
            |--------------------------------------------------------------------------
        */

        if ($cotizacion->estado !== 'EMITIDA') {

            return back()->with(
                'error',
                'Esta cotización ya no puede ser anulada.'
            );
        }

        /*
            |--------------------------------------------------------------------------
            | Validar motivo
            |--------------------------------------------------------------------------
        */

        $request->validate([
            'motivo_anulacion' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'motivo_anulacion.required' =>
            'Debe indicar el motivo de la anulación.',
        ]);

        /*
            |--------------------------------------------------------------------------
            | Anular
            |--------------------------------------------------------------------------
        */

        $cotizacion->update([
            'estado' => 'ANULADA',
            'anulada_at' => now(),
            'anulada_por_tipo' => 'CLIENTE',
            'anulada_por_user_id' => null,
            'motivo_anulacion' =>
            $request->motivo_anulacion,
        ]);

        /*
            |--------------------------------------------------------------------------
            | Volver a la cotización
            |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'cotizaciones.resultado',
                $cotizacion
            )
            ->with(
                'success',
                'La cotización fue anulada correctamente.'
            );
    }

    public function descargarPdfPublico(Cotizacion $cotizacion, string $token)
    {
        if (
            !$cotizacion->token_acceso ||
            !hash_equals(
                $cotizacion->token_acceso,
                $token
            )
        ) {
            abort(
                403,
                'No tienes autorización para descargar esta cotización.'
            );
        }

        return $this->descargarPdf($cotizacion);
    }

    public function convertirEnReserva(
        Cotizacion $cotizacion,
        string $token
    ) {
        /*
    |--------------------------------------------------------------------------
    | Validar token
    |--------------------------------------------------------------------------
    */

        if (
            !$cotizacion->token_acceso ||
            !hash_equals(
                $cotizacion->token_acceso,
                $token
            )
        ) {
            abort(
                403,
                'No tienes autorización para acceder a esta cotización.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Solo se puede convertir una cotización EMITIDA
    |--------------------------------------------------------------------------
    */

        if (
            strtoupper($cotizacion->estado) !== 'EMITIDA'
        ) {
            return redirect()
                ->route(
                    'cotizaciones.resultado',
                    [
                        'cotizacion' => $cotizacion,
                        'token' => $cotizacion->token_acceso,
                    ]
                )
                ->with(
                    'error',
                    'Esta cotización ya no puede convertirse en reserva.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | Evitar crear una segunda reserva
    |--------------------------------------------------------------------------
    */

        if ($cotizacion->reserva()->exists()) {

            return redirect()
                ->route(
                    'cotizaciones.resultado',
                    [
                        'cotizacion' => $cotizacion,
                        'token' => $cotizacion->token_acceso,
                    ]
                )
                ->with(
                    'error',
                    'Esta cotización ya tiene una reserva asociada.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | Preparar conversión
    |--------------------------------------------------------------------------
    |
    | IMPORTANTE:
    | Todavía NO cambiamos el estado a ACEPTADA.
    |
    */

        session([
            'conversion_cotizacion_id' =>
            $cotizacion->id,

            'conversion_cotizacion_token' =>
            $cotizacion->token_acceso,

            'reserva.tipo_operacion' =>
            'RESERVA',
        ]);


        /*
            |--------------------------------------------------------------------------
            | Continuar con el wizard
            |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('reservas.cliente')
            ->with(
                'success',
                'Revisa los datos de la cotización para continuar con la reserva.'
            );
    }

    public function reenviarCorreo(Cotizacion $cotizacion)
    {
        try {

            /*
        |--------------------------------------------------------------------------
        | Validar que pueda reenviarse
        |--------------------------------------------------------------------------
        */

            if ($cotizacion->estado !== 'EMITIDA') {
                return back()->with(
                    'error',
                    'Solo se puede reenviar una cotización que se encuentre emitida.'
                );
            }

            if (empty($cotizacion->email)) {
                return back()->with(
                    'error',
                    'La cotización no tiene un correo electrónico registrado.'
                );
            }

            if (empty($cotizacion->token_acceso)) {
                return back()->with(
                    'error',
                    'La cotización no tiene un token de acceso válido.'
                );
            }

            /*
        |--------------------------------------------------------------------------
        | Generar nuevamente el enlace de conversión
        |--------------------------------------------------------------------------
        */

            $urlConvertir = action(
                [
                    CotizacionController::class,
                    'convertirEnReserva',
                ],
                [
                    'cotizacion' => $cotizacion->id,
                    'token' => $cotizacion->token_acceso,
                ]
            );

            /*
        |--------------------------------------------------------------------------
        | Reenviar correo
        |--------------------------------------------------------------------------
        */

            Mail::to($cotizacion->email)
                ->send(
                    new CotizacionGeneradaMail(
                        $cotizacion,
                        $urlConvertir
                    )
                );

            /*
        |--------------------------------------------------------------------------
        | Registrar último envío
        |--------------------------------------------------------------------------
        */

            $cotizacion->update([
                'correo_enviado_at' => now(),
                'correo_error' => null,
            ]);

            return back()->with(
                'success',
                "La cotización {$cotizacion->folio} fue reenviada correctamente a {$cotizacion->email}."
            );
        } catch (Throwable $exception) {

            $cotizacion->update([
                'correo_error' => $exception->getMessage(),
            ]);

            Log::error(
                'Error al reenviar cotización por correo.',
                [
                    'cotizacion_id' => $cotizacion->id,
                    'folio' => $cotizacion->folio,
                    'correo' => $cotizacion->email,
                    'error' => $exception->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'No fue posible reenviar la cotización por correo.'
            );
        }
    }
}
