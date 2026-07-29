<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $categorias = CategoriaServicio::query()
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view(
            'categorias-servicio.index',
            compact('categorias', 'buscar')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias-servicio.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $this->validar($request);

        CategoriaServicio::query()->create($datos);

        return redirect()
            ->route('categorias-servicio.index')
            ->with(
                'success',
                'La categoría fue creada correctamente.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoriaServicio $categoria)
    {
        return view(
            'categorias-servicio.show',
            compact('categoria')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoriaServicio $categoria)
    {
        return view(
            'categorias-servicio.edit',
            compact('categoria')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoriaServicio $categoria)
    {
        $datos = $this->validar($request, $categoria);

        $categoria->update($datos);

        return redirect()
            ->route('categorias-servicio.index')
            ->with(
                'success',
                'La categoría fue actualizada correctamente.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoriaServicio $categoria)
    {
        if (! $categoria->activo) {
            return redirect()
                ->route('categorias-servicio.index')
                ->with('error', 'La categoría ya se encuentra desactivada.');
        }

        $categoria->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('categorias-servicio.index')
            ->with(
                'success',
                'La categoría fue desactivada correctamente.'
            );
    }

    private function validar(
        Request $request,
        ?CategoriaServicio $categoria = null
    ): array {
        $datos = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'categorias_servicio',
                    'codigo'
                )->ignore($categoria?->id),
            ],

            'nombre' => [
                'required',
                'string',
                'max:100',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'El código ya se encuentra registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }

    public function activar(CategoriaServicio $categoria)
    {
        if ($categoria->activo) {
            return redirect()
                ->route('categorias-servicio.index')
                ->with('error', 'La categoría ya se encuentra activa.');
        }

        $categoria->update([
            'activo' => true,
        ]);

        return redirect()
            ->route('categorias-servicio.index')
            ->with(
                'success',
                'La categoría fue reactivada correctamente.'
            );
    }
}
