<?php

namespace App\Http\Controllers;

use App\Models\Comuna;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;

class ComunaController extends Controller
{
    /**
     * Mostrar el listado de comunas.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $comunas = Comuna::query()
            ->with('region')
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('codigo', 'like', "%{$buscar}%")
                        ->orWhereHas('region', function ($regionQuery) use ($buscar) {
                            $regionQuery->where('nombre', 'like', "%{$buscar}%");
                        });
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('comunas.index', compact('comunas', 'buscar'));
    }

    /**
     * Mostrar el formulario para crear una comuna.
     */
    public function create()
    {
        $regiones = Region::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('comunas.create', compact('regiones'));
    }

    /**
     * Guardar una nueva comuna.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'region_id' => [
                'required',
                'integer',
                Rule::exists('regiones', 'id'),
            ],
            'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique('comunas', 'codigo'),
            ],
            'nombre' => [
                'required',
                'string',
                'max:120',
            ],
            'activo' => [
                'required',
                'boolean',
            ],
        ], [
            'region_id.required' => 'Debes seleccionar una región.',
            'region_id.exists' => 'La región seleccionada no es válida.',
            'codigo.required' => 'El código de la comuna es obligatorio.',
            'codigo.unique' => 'El código ingresado ya está registrado.',
            'codigo.max' => 'El código no puede superar los 10 caracteres.',
            'nombre.required' => 'El nombre de la comuna es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 120 caracteres.',
            'activo.required' => 'Debes seleccionar el estado de la comuna.',
            'activo.boolean' => 'El estado seleccionado no es válido.',
        ]);

        Comuna::create($datos);

        return redirect()
            ->route('comunas.index')
            ->with('success', 'Comuna creada correctamente.');
    }

    /**
     * Mostrar el detalle de una comuna.
     */
    public function show(Comuna $comuna)
    {
        $comuna->load('region');

        return view('comunas.show', compact('comuna'));
    }

    /**
     * Mostrar el formulario para editar una comuna.
     */
    public function edit(Comuna $comuna)
    {
        $regiones = Region::query()
            ->where(function ($query) use ($comuna) {
                $query
                    ->where('activo', true)
                    ->orWhere('id', $comuna->region_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('comunas.edit', compact('comuna', 'regiones'));
    }

    /**
     * Actualizar una comuna.
     */
    public function update(Request $request, Comuna $comuna)
    {
        $datos = $request->validate([
            'region_id' => [
                'required',
                'integer',
                Rule::exists('regiones', 'id'),
            ],
            'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique('comunas', 'codigo')->ignore($comuna->id),
            ],
            'nombre' => [
                'required',
                'string',
                'max:120',
            ],
            'activo' => [
                'required',
                'boolean',
            ],
        ], [
            'region_id.required' => 'Debes seleccionar una región.',
            'region_id.exists' => 'La región seleccionada no es válida.',
            'codigo.required' => 'El código de la comuna es obligatorio.',
            'codigo.unique' => 'El código ingresado ya está registrado.',
            'codigo.max' => 'El código no puede superar los 10 caracteres.',
            'nombre.required' => 'El nombre de la comuna es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 120 caracteres.',
            'activo.required' => 'Debes seleccionar el estado de la comuna.',
            'activo.boolean' => 'El estado seleccionado no es válido.',
        ]);

        $comuna->update($datos);

        return redirect()
            ->route('comunas.index')
            ->with('success', 'Comuna actualizada correctamente.');
    }

    /**
     * Eliminar una comuna.
     */
    public function destroy(Comuna $comuna)
    {
        $comuna->delete();

        return redirect()
            ->route('comunas.index')
            ->with('success', 'Comuna eliminada correctamente.');
    }

    public function cambiarEstado(Comuna $comuna): RedirectResponse 
    {
        $comuna->update([
            'activo' => ! $comuna->activo,
        ]);

        $mensaje = $comuna->activo
            ? 'La comuna fue activada correctamente.'
            : 'La comuna fue desactivada correctamente.';

        return redirect()
            ->route('comunas.index')
            ->with('success', $mensaje);
    }
}
