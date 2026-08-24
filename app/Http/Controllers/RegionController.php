<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar'));

        $regiones = Region::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%");
                });
            })
            ->withCount('comunas')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('regiones.index', compact('regiones', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('regiones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'codigo' => [
                    'required',
                    'string',
                    'max:10',
                    'unique:regiones,codigo',
                ],
                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                ],
                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'codigo.required' => 'El código es obligatorio.',
                'codigo.max' => 'El código no puede superar los 10 caracteres.',
                'codigo.unique' => 'El código ingresado ya está registrado.',

                'nombre.required' => 'El nombre de la región es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 150 caracteres.',

                'activo.boolean' => 'El estado seleccionado no es válido.',
            ]
        );

        $datos['codigo'] = strtoupper(trim($datos['codigo']));
        $datos['nombre'] = trim($datos['nombre']);
        $datos['activo'] = $request->boolean('activo');

        Region::create($datos);

        return redirect()
            ->route('regiones.index')
            ->with('success', 'La región fue creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Region $region)
    {
        $region->load([
            'comunas' => fn($query) => $query->orderBy('nombre'),
        ]);

        return view('regiones.show', compact('region'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Region $region)
    {
        return view('regiones.edit', compact('region'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Region $region): RedirectResponse
    {
        $datos = $request->validate(
            [
                'codigo' => [
                    'required',
                    'string',
                    'max:10',
                    Rule::unique('regiones', 'codigo')->ignore($region->id),
                ],
                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                ],
                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'codigo.required' => 'El código es obligatorio.',
                'codigo.max' => 'El código no puede superar los 10 caracteres.',
                'codigo.unique' => 'El código ingresado ya está registrado.',

                'nombre.required' => 'El nombre de la región es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 150 caracteres.',

                'activo.boolean' => 'El estado seleccionado no es válido.',
            ]
        );

        $datos['codigo'] = strtoupper(trim($datos['codigo']));
        $datos['nombre'] = trim($datos['nombre']);
        $datos['activo'] = $request->boolean('activo');

        $region->update($datos);

        return redirect()
            ->route('regiones.index')
            ->with('success', 'La región fue actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region)
    {
        if ($region->comunas()->exists()) {
            return redirect()
                ->route('regiones.index')
                ->with(
                    'error',
                    'No se puede eliminar la región porque tiene comunas asociadas. Puede desactivarla.'
                );
        }

        $region->delete();

        return redirect()
            ->route('regiones.index')
            ->with('success', 'La región fue eliminada correctamente.');
    }

    public function cambiarEstado(Region $region): RedirectResponse 
    {
        $region->update([
            'activo' => ! $region->activo,
        ]);

        $mensaje = $region->activo
            ? 'La región fue activada correctamente.'
            : 'La región fue desactivada correctamente.';

        return redirect()
            ->route('regiones.index')
            ->with('success', $mensaje);
    }
}
