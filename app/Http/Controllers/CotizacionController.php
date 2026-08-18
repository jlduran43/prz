<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\ConfiguracionCotizacion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function showPublico(Cotizacion $cotizacion)
    {
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

    public function anularPublico(Request $request, Cotizacion $cotizacion) {
        if ($cotizacion->estado !== 'EMITIDA') {

            return back()->with(
                'error',
                'Esta cotización ya no puede ser anulada.'
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
            'Debe indicar el motivo de la anulación.',
        ]);

        $cotizacion->update([
            'estado' => 'ANULADA',
            'anulada_at' => now(),
            'anulada_por_tipo' => 'CLIENTE',
            'anulada_por_user_id' => null,
            'motivo_anulacion' =>
            $request->motivo_anulacion,
        ]);

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
}
