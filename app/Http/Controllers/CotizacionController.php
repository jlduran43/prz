<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
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
    public function show(
        Cotizacion $cotizacion
    ) {

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
        /*
     * Cargamos las mismas relaciones necesarias
     * para mostrar la cotización.
     */
        $cotizacion->load([
            'tipoCliente',
            'servicios',
        ]);

        $pdf = Pdf::loadView('cotizaciones.pdf', [
            'cotizacion' => $cotizacion,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            $cotizacion->folio . '.pdf'
        );
    }
}
