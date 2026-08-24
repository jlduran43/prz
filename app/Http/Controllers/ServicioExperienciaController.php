<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use App\Models\ServicioExperiencia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ServicioExperienciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoriaId = $request->input('categoria_id');
        $estado = $request->input('estado');

        $servicios = ServicioExperiencia::query()
            ->with('categoria')
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->when($categoriaId, function ($query, $categoriaId) {
                $query->where(
                    'categoria_servicio_id',
                    $categoriaId
                );
            })
            ->when($estado !== null && $estado !== '', function ($query) use ($estado) {
                $query->where('activo', (bool) $estado);
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        // En el mantenedor se muestran categorías activas e inactivas
        // para poder filtrar los registros históricos.
        $categorias = CategoriaServicio::query()
            ->orderBy('nombre')
            ->get();

        return view(
            'servicios-experiencias.index',
            compact(
                'servicios',
                'categorias',
                'buscar',
                'categoriaId',
                'estado'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Para crear un servicio solo se pueden seleccionar
        // categorías activas.
        $categorias = CategoriaServicio::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'servicios-experiencias.create',
            compact('categorias')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'categoria_servicio_id' => [
                    'required',
                    'exists:categorias_servicio,id',
                ],

                'codigo' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:servicios_experiencias,codigo',
                ],

                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                ],

                'imagen' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ],

                'duracion_minutos' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'capacidad_minima' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'capacidad_maxima' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'gte:capacidad_minima',
                ],

                'precio' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'tipo_cobro' => [
                    'required',
                    Rule::in([
                        'POR_PERSONA',
                        'POR_GRUPO',
                    ]),
                ],

                'requiere_reserva' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'tipo_cobro.required' => 'Debes seleccionar el tipo de cobro.',
                'tipo_cobro.in' => 'El tipo de cobro seleccionado no es válido.',

                'imagen.image' => 'El archivo seleccionado debe ser una imagen.',
                'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WebP.',
                'imagen.max' => 'La imagen no puede superar los 2 MB.',
            ]
        );

        if ($request->hasFile('imagen')) {

            $datos['imagen'] = $request
                ->file('imagen')
                ->store(
                    'servicios',
                    'public'
                );
        }

        $datos['requiere_reserva'] =
            $request->boolean('requiere_reserva');

        $datos['activo'] = true;

        ServicioExperiencia::query()->create($datos);

        return redirect()
            ->route('servicios-experiencias.index')
            ->with(
                'success',
                'El servicio fue creado correctamente.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(ServicioExperiencia $servicio)
    {
        $servicio->load('categoria');

        return view(
            'servicios-experiencias.show',
            compact('servicio')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServicioExperiencia $servicio)
    {
        /*
         * Se incluyen las categorías activas y también la categoría
         * actualmente asociada, aunque haya sido desactivada.
         */
        $categorias = CategoriaServicio::query()
            ->where(function ($query) use ($servicio) {
                $query
                    ->where('activo', true)
                    ->orWhere(
                        'id',
                        $servicio->categoria_servicio_id
                    );
            })
            ->orderBy('nombre')
            ->get();

        return view(
            'servicios-experiencias.edit',
            compact('servicio', 'categorias')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServicioExperiencia $servicio) 
    {
        $datos = $request->validate(
            [
                'categoria_servicio_id' => [
                    'required',
                    'exists:categorias_servicio,id',
                ],

                'codigo' => [
                    'required',
                    'string',
                    'max:50',

                    Rule::unique(
                        'servicios_experiencias',
                        'codigo'
                    )->ignore($servicio->id),
                ],

                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                ],

                'imagen' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ],

                'duracion_minutos' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'capacidad_minima' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'capacidad_maxima' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'gte:capacidad_minima',
                ],

                'precio' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'tipo_cobro' => [
                    'required',

                    Rule::in([
                        'POR_PERSONA',
                        'POR_GRUPO',
                    ]),
                ],

                'requiere_reserva' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'categoria_servicio_id.required' =>
                'Debes seleccionar una categoría.',

                'codigo.required' =>
                'El código es obligatorio.',

                'codigo.unique' =>
                'El código ya se encuentra registrado.',

                'nombre.required' =>
                'El nombre es obligatorio.',

                'precio.required' =>
                'El precio es obligatorio.',

                'precio.numeric' =>
                'El precio debe ser un número.',

                'tipo_cobro.required' =>
                'Debes seleccionar el tipo de cobro.',

                'tipo_cobro.in' =>
                'El tipo de cobro seleccionado no es válido.',

                'imagen.image' =>
                'El archivo seleccionado debe ser una imagen.',

                'imagen.mimes' =>
                'La imagen debe ser JPG, JPEG, PNG o WebP.',

                'imagen.max' =>
                'La imagen no puede superar los 4 MB.',
            ]
        );

        if ($request->hasFile('imagen')) {

            if (
                $servicio->imagen &&
                Storage::disk('public')->exists(
                    $servicio->imagen
                )
            ) {
                Storage::disk('public')->delete(
                    $servicio->imagen
                );
            }

            $datos['imagen'] = $request
                ->file('imagen')
                ->store(
                    'servicios',
                    'public'
                );
        }

        $datos['requiere_reserva'] =
            $request->boolean('requiere_reserva');

        $servicio->update($datos);

        return redirect()
            ->route('servicios-experiencias.index')
            ->with(
                'success',
                'El servicio fue actualizado correctamente.'
            );
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServicioExperiencia $servicio)
    {
        if (! $servicio->activo) {
            return redirect()
                ->route('servicios-experiencias.index')
                ->with(
                    'error',
                    'El servicio ya se encuentra desactivado.'
                );
        }

        $servicio->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('servicios-experiencias.index')
            ->with(
                'success',
                'El servicio o experiencia fue desactivado correctamente.'
            );
    }

    public function activar(ServicioExperiencia $servicio)
    {
        if ($servicio->activo) {
            return redirect()
                ->route('servicios-experiencias.index')
                ->with(
                    'error',
                    'El servicio ya se encuentra activo.'
                );
        }

        if (! $servicio->categoria?->activo) {
            return redirect()
                ->route('servicios-experiencias.index')
                ->with(
                    'error',
                    'No se puede reactivar el servicio porque su categoría está inactiva.'
                );
        }

        $servicio->update([
            'activo' => true,
        ]);

        return redirect()
            ->route('servicios-experiencias.index')
            ->with(
                'success',
                'El servicio o experiencia fue reactivado correctamente.'
            );
    }
}
