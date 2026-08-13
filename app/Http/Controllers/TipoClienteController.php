<?php

namespace App\Http\Controllers;

use App\Models\TipoCliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TipoClienteController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));

        $tiposCliente = TipoCliente::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('tipos-cliente.index', [
            'tiposCliente' => $tiposCliente,
            'buscar' => $buscar,
        ]);
    }

    public function create(): View
    {
        return view('tipos-cliente.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarDatos($request);

        $datos['codigo'] = strtoupper(
            str_replace(' ', '_', trim($datos['codigo']))
        );

        $datos['activo'] = $request->boolean('activo');

        TipoCliente::query()->create($datos);

        return redirect()
            ->route('tipos-cliente.index')
            ->with(
                'success',
                'El tipo de cliente fue creado correctamente.'
            );
    }

    public function show(TipoCliente $tipoCliente): View
    {
        return view('tipos-cliente.show', [
            'tipoCliente' => $tipoCliente,
        ]);
    }

    public function edit(TipoCliente $tipoCliente): View
    {
        return view('tipos-cliente.edit', [
            'tipoCliente' => $tipoCliente,
        ]);
    }

    public function update(Request $request, TipoCliente $tipoCliente): RedirectResponse {
        $datos = $this->validarDatos(
            $request,
            $tipoCliente
        );

        $datos['codigo'] = strtoupper(
            str_replace(' ', '_', trim($datos['codigo']))
        );

        $datos['activo'] = $request->boolean('activo');

        $tipoCliente->update($datos);

        return redirect()
            ->route('tipos-cliente.index')
            ->with(
                'success',
                'El tipo de cliente fue actualizado correctamente.'
            );
    }

    public function destroy() {
        //
    }

    public function cambiarEstado(TipoCliente $tipoCliente): RedirectResponse {
        $tipoCliente->update([
            'activo' => ! $tipoCliente->activo,
        ]);

        $mensaje = $tipoCliente->activo
            ? 'El tipo de cliente fue activado correctamente.'
            : 'El tipo de cliente fue desactivado correctamente.';

        return redirect()
            ->route('tipos-cliente.index')
            ->with('success', $mensaje);
    }

    private function validarDatos(Request $request, ?TipoCliente $tipoCliente = null): array {
        return $request->validate(
            [
                'codigo' => [
                    'required',
                    'string',
                    'max:40',
                    'regex:/^[A-Za-z0-9_ ]+$/',
                    Rule::unique(
                        'tipos_cliente',
                        'codigo'
                    )->ignore($tipoCliente?->id),
                ],

                'nombre' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(
                        'tipos_cliente',
                        'nombre'
                    )->ignore($tipoCliente?->id),
                ],

                'tipo_estructura' => [
                    'required',
                    Rule::in([
                        'PERSONA',
                        'ORGANIZACION',
                    ]),
                ],

                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'codigo.required' =>
                'Debes ingresar el código.',

                'codigo.unique' =>
                'Este código ya está registrado.',

                'codigo.regex' =>
                'El código solo puede contener letras, números, espacios y guiones bajos.',

                'nombre.required' =>
                'Debes ingresar el nombre.',

                'nombre.unique' =>
                'Este nombre ya está registrado.',

                'tipo_estructura.required' =>
                'Debes seleccionar el tipo de estructura.',

                'tipo_estructura.in' =>
                'El tipo de estructura seleccionado no es válido.',
            ]
        );
    }
}
