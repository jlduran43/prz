<?php

namespace App\Http\Controllers;

use App\Models\HorarioDisponible;
use App\Models\ServicioExperiencia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HorarioDisponibleController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->query('buscar', '')
        );

        $estado = $request->query('estado');

        $servicioId = $request->query(
            'servicio_id'
        );

        $horarios = HorarioDisponible::query()
            ->with([
                'servicio.categoria',
            ])
            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {
                    $query->whereHas(
                        'servicio',
                        function ($servicioQuery) use ($buscar) {
                            $servicioQuery->where(
                                'nombre',
                                'like',
                                "%{$buscar}%"
                            );
                        }
                    );
                }
            )
            ->when(
                $servicioId,
                function ($query) use ($servicioId) {
                    $query->where(
                        'servicio_experiencia_id',
                        $servicioId
                    );
                }
            )
            ->when(
                $estado !== null && $estado !== '',
                function ($query) use ($estado) {
                    $query->where('activo', $estado);
                }
            )
            ->orderByDesc('fecha')
            ->orderBy('hora_inicio')
            ->paginate(15)
            ->withQueryString();

        $servicios = ServicioExperiencia::query()
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ]);

        return view(
            'horarios-disponibles.index',
            [
                'horarios' => $horarios,
                'servicios' => $servicios,
                'buscar' => $buscar,
                'servicioId' => $servicioId,
                'estado' => $estado
            ]
        );
    }

    public function create()
    {
        $servicios = ServicioExperiencia::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('horarios-disponibles.create', [
            'servicios' => $servicios,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'servicio_experiencia_id' => [
                'required',
                Rule::exists(
                    'servicios_experiencias',
                    'id'
                )->where('activo', true),
            ],

            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'hora_inicio' => [
                'required',
                'date_format:H:i',
                Rule::unique(
                    'horarios_disponibles',
                    'hora_inicio'
                )->where(function ($query) use ($request) {
                    return $query
                        ->where(
                            'servicio_experiencia_id',
                            $request->servicio_experiencia_id
                        )
                        ->whereDate(
                            'fecha',
                            $request->fecha
                        )
                        ->where(
                            'hora_termino',
                            $request->hora_termino
                        );
                }),
            ],

            'hora_termino' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],
        ], [
            'hora_inicio.unique' =>
            'Ya existe este horario para el servicio y la fecha seleccionados.',
        ]);

        $datos['activo'] =
            $request->boolean('activo');

        HorarioDisponible::query()->create($datos);

        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                'El horario fue creado correctamente.'
            );
    }

    public function show(HorarioDisponible $horario)
    {
        return view(
            'horarios-disponibles.show',
            compact('horario')
        );
    }

    public function edit(HorarioDisponible $horario)
    {
        $servicios = ServicioExperiencia::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('horarios-disponibles.edit', [
            'horario' => $horario,
            'servicios' => $servicios,
        ]);
    }

    public function update(
        Request $request,
        HorarioDisponible $horario
    ) {
        $datos = $request->validate([
            'servicio_experiencia_id' => [
                'required',
                Rule::exists(
                    'servicios_experiencias',
                    'id'
                ),
            ],

            'fecha' => [
                'required',
                'date',
            ],

            'hora_inicio' => [
                'required',
                'date_format:H:i',
            ],

            Rule::unique(
                'horarios_disponibles',
                'hora_inicio'
            )
                ->ignore($horario->id)
                ->where(function ($query) use ($request) {
                    return $query
                        ->where(
                            'servicio_experiencia_id',
                            $request->servicio_experiencia_id
                        )
                        ->whereDate(
                            'fecha',
                            $request->fecha
                        )
                        ->where(
                            'hora_termino',
                            $request->hora_termino
                        );
                }),

            'hora_termino' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],
        ], [
            'hora_inicio.unique' =>
            'Ya existe este horario para el servicio y la fecha seleccionados.',
        ]);

        $datos['activo'] =
            $request->boolean('activo');

        $horario->update($datos);

        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                'El horario fue actualizado correctamente.'
            );
    }

    public function destroy(HorarioDisponible $horario)
    {
        if (! $horario->activo) {
            return redirect()
                ->route('horarios-disponibles.index')
                ->with(
                    'error',
                    'El horario ya se encuentra desactivado.'
                );
        }

        $horario->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                'El horario de atención fue desactivado correctamente.'
            );
    }

    public function activar(HorarioDisponible $horario)
    {
        if ($horario->activo) {
            return redirect()
                ->route('horarios-disponibles.index')
                ->with(
                    'error',
                    'El horario de atención fue reactivado correctamente.'
                );
        }

        $horario->update([
            'activo' => true,
        ]);

        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                'El horario fue reactivado correctamente.'
            );
    }

    private function validar(
        Request $request,
        ?HorarioDisponible $horario = null
    ): array {
        $datos = $request->validate([
            'hora_inicio' => [
                'required',
                'date_format:H:i',
            ],

            'hora_termino' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio',
            ],
        ], [
            'hora_inicio.required' =>
            'La hora de inicio es obligatoria.',

            'hora_inicio.date_format' =>
            'La hora de inicio no tiene un formato válido.',

            'hora_termino.required' =>
            'La hora de término es obligatoria.',

            'hora_termino.after' =>
            'La hora de término debe ser posterior a la hora de inicio.',
        ]);

        if ($horario === null) {
            $datos['activo'] = true;
        }

        return $datos;
    }

    private function validarSolapamiento(
        array $datos,
        ?HorarioDisponible $horarioActual = null
    ): void {
        $existeSolapamiento = HorarioDisponible::query()
            ->when(
                $horarioActual,
                fn($query) => $query->whereKeyNot(
                    $horarioActual->id
                )
            )
            ->where(function ($query) use ($datos) {
                $query
                    ->where(
                        'hora_inicio',
                        '<',
                        $datos['hora_termino']
                    )
                    ->where(
                        'hora_termino',
                        '>',
                        $datos['hora_inicio']
                    );
            })
            ->exists();

        if ($existeSolapamiento) {
            throw ValidationException::withMessages([
                'hora_inicio' =>
                'El horario se superpone con otra franja existente.',
            ]);
        }
    }
}
