<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionCotizacion;
use Illuminate\Http\Request;

class ConfiguracionCotizacionController extends Controller
{
    public function index()
    {
        $configuraciones = ConfiguracionCotizacion::query()
            ->orderByDesc('activo')
            ->orderByDesc('id')
            ->paginate(10);

        return view(
            'configuraciones-cotizacion.index',
            compact('configuraciones')
        );
    }

    public function create()
    {
        return view(
            'configuraciones-cotizacion.create'
        );
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        /*
         * Si esta configuración queda activa,
         * desactivamos cualquier otra.
         */
        if ($request->boolean('activo')) {

            ConfiguracionCotizacion::query()
                ->update([
                    'activo' => false,
                ]);
        }

        $datos['activo'] =
            $request->boolean('activo');

        ConfiguracionCotizacion::create($datos);

        return redirect()
            ->route(
                'configuraciones-cotizacion.index'
            )
            ->with(
                'success',
                'La configuración fue creada correctamente.'
            );
    }

    public function show(ConfiguracionCotizacion $configuracion)
    {
        return view(
            'configuraciones-cotizacion.show',
            compact('configuracion')
        );
    }

    public function edit(ConfiguracionCotizacion $configuracion)
    {
        return view(
            'configuraciones-cotizacion.edit',
            compact('configuracion')
        );
    }

    public function update(Request $request, ConfiguracionCotizacion $configuracion)
    {
        $datos = $this->validar($request);

        if ($request->boolean('activo')) {

            ConfiguracionCotizacion::query()
                ->where(
                    'id',
                    '!=',
                    $configuracion->id
                )
                ->update([
                    'activo' => false,
                ]);
        }

        $datos['activo'] =
            $request->boolean('activo');

        $configuracion->update($datos);

        return redirect()
            ->route(
                'configuraciones-cotizacion.index'
            )
            ->with(
                'success',
                'La configuración fue actualizada correctamente.'
            );
    }

    public function destroy(ConfiguracionCotizacion $configuracion)
    {
        $configuracion->activo = false;
        $configuracion->save();

        return redirect()
            ->route('configuraciones-cotizacion.index')
            ->with('success', 'Configuración desactivada correctamente.');
    }

    public function activar(ConfiguracionCotizacion $configuracion)
    {
        $configuracion->activo = true;
        $configuracion->save();

        return redirect()
            ->route('configuraciones-cotizacion.index')
            ->with('success', 'Configuración activada correctamente.');
    }

    private function validar(Request $request): array
    {
        return $request->validate(
            [
                'titulo' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'descripcion_tour' => [
                    'nullable',
                    'string',
                ],

                'condiciones_pago' => [
                    'nullable',
                    'string',
                ],

                'titular_cuenta' => [
                    'nullable',
                    'string',
                    'max:200',
                ],

                'rut_titular' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'banco' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'tipo_cuenta' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'numero_cuenta' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'correo_comprobantes' => [
                    'nullable',
                    'email',
                    'max:150',
                ],

                'politica_devoluciones' => [
                    'nullable',
                    'string',
                ],

                'condiciones_museo' => [
                    'nullable',
                    'string',
                ],

                'recomendaciones_museo' => [
                    'nullable',
                    'string',
                ],

                'recomendaciones_parque' => [
                    'nullable',
                    'string',
                ],

                'nota_importante' => [
                    'nullable',
                    'string',
                ],

                'correo_reservas' => [
                    'nullable',
                    'email',
                    'max:150',
                ],

                'telefono_reservas' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'horario_contacto' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'dias_validez' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:365',
                ],

                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'titulo.required' =>
                'El título es obligatorio.',

                'dias_validez.required' =>
                'Debe indicar los días de validez.',

                'dias_validez.integer' =>
                'La validez debe ser un número entero.',

                'correo_comprobantes.email' =>
                'El correo de comprobantes no es válido.',

                'correo_reservas.email' =>
                'El correo de reservas no es válido.',
            ]
        );
    }
}
