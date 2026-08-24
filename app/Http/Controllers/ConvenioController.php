<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConvenioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $convenios = Convenio::query()
            ->withCount('entidades')
            ->orderBy('nombre')
            ->paginate(10);

        return view(
            'convenios.index',
            compact('convenios')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('convenios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);

        DB::transaction(function () use ($datos) {

            $convenio = Convenio::create([
                'codigo' => mb_strtoupper(
                    trim($datos['codigo'])
                ),

                'nombre' => $datos['nombre'],

                'porcentaje_descuento' => $datos['porcentaje_descuento'],

                'fecha_inicio' => $datos['fecha_inicio'],

                'fecha_termino' => $datos['fecha_termino'] ?? null,

                'activo' => true,

                'observaciones' => $datos['observaciones'] ?? null,
            ]);


            foreach ($datos['entidades'] as $entidad) {

                $convenio->entidades()->create([
                    'nombre_entidad' => $entidad['nombre_entidad'],

                    'rut_entidad' => $entidad['rut_entidad'],

                    'rut_normalizado' => $this->normalizarRut(
                        $entidad['rut_entidad']
                    ),

                    'activo' => true,
                ]);
            }
        });

        return redirect()
            ->route('convenios.index')
            ->with(
                'success',
                'Convenio creado correctamente.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Convenio $convenio)
    {
        $convenio->load('entidades');

        return view('convenios.show', compact('convenio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Convenio $convenio)
    {
        $convenio->load('entidades');

        return view(
            'convenios.edit',
            compact('convenio')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Convenio $convenio)
    {
        $datos = $this->validarDatos(
            $request,
            $convenio->id
        );

        DB::transaction(function () use (
            $datos,
            $convenio
        ) {

            $convenio->update([
                'codigo' =>
                mb_strtoupper(
                    trim($datos['codigo'])
                ),

                'nombre' =>
                $datos['nombre'],

                'porcentaje_descuento' =>
                $datos['porcentaje_descuento'],

                'fecha_inicio' =>
                $datos['fecha_inicio'],

                'fecha_termino' =>
                $datos['fecha_termino'] ?? null,

                'observaciones' =>
                $datos['observaciones'] ?? null,
            ]);


            /*
             * Versión simple:
             * reemplazar los RUT asociados.
             */
            $convenio->entidades()->delete();


            foreach ($datos['entidades'] as $entidad) {

                $convenio->entidades()->create([
                    'nombre_entidad' =>
                    $entidad['nombre_entidad'],

                    'rut_entidad' =>
                    $entidad['rut_entidad'],

                    'rut_normalizado' =>
                    $this->normalizarRut(
                        $entidad['rut_entidad']
                    ),

                    'activo' =>
                    true,
                ]);
            }
        });

        return redirect()
            ->route('convenios.index')
            ->with(
                'success',
                'Convenio actualizado correctamente.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Convenio $convenio)
    {
        /*
         * Recomiendo desactivar en vez
         * de eliminar físicamente.
         */
        $convenio->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('convenios.index')
            ->with(
                'success',
                'Convenio desactivado correctamente.'
            );
    }

    private function validarDatos(Request $request, ?int $convenioId = null): array
    {

        return $request->validate([

            'codigo' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'convenios',
                    'codigo'
                )->ignore($convenioId),
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'porcentaje_descuento' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'fecha_inicio' => [
                'required',
                'date',
            ],

            'fecha_termino' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'entidades' => [
                'required',
                'array',
                'min:1',
            ],

            'entidades.*.nombre_entidad' => [
                'required',
                'string',
                'max:150',
            ],

            'entidades.*.rut_entidad' => [
                'required',
                'string',
                'max:20',
            ],
        ]);
    }

    private function normalizarRut(?string $rut): string
    {

        return mb_strtoupper(
            preg_replace(
                '/[^0-9kK]/',
                '',
                (string) $rut
            )
        );
    }

    public function activar(Convenio $convenio)
    {
        $convenio->activo = true;
        $convenio->save();

        return redirect()
            ->route('convenios.index')
            ->with('success', 'Convenio reactivado correctamente.');
    }
}
