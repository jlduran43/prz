<?php

namespace App\Http\Controllers;

use App\Models\HorarioDisponible;
use Illuminate\Http\Request;
use App\Models\ServicioExperiencia;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class HorarioDisponibleController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->string('fecha')->toString();
        $estado = $request->query('estado');

        $query = HorarioDisponible::query();

        if ($fecha !== '') {
            $query->whereDate('fecha', $fecha);
        }

        if ($estado !== null && $estado !== '') {
            $query->where('activo', $estado);
        }

        $horarios = $query
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->paginate(10)
            ->withQueryString();

        return view(
            'horarios-disponibles.index',
            [
                'horarios' => $horarios,
                'fecha' => $fecha,
                'estado' => $estado,
            ]
        );
    }

    public function create()
    {

        $servicios = ServicioExperiencia::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'horarios-disponibles.create',
            compact('servicios')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'fecha' => [
                    'required',
                    'date',
                ],

                'hora_inicio' => [
                    'required',
                    'date_format:H:i',
                ],

                'hora_termino' => [
                    'required',
                    'date_format:H:i',
                    'after:hora_inicio',
                ],

                'servicios' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'servicios.*' => [
                    'integer',
                    'distinct',
                    'exists:servicios_experiencias,id',
                ],

                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ]
        );

        $existeDuplicado = HorarioDisponible::query()
            ->whereDate('fecha', $datos['fecha'])
            ->where('hora_inicio', $datos['hora_inicio'])
            ->where('hora_termino', $datos['hora_termino'])
            ->exists();

        if ($existeDuplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'hora_inicio' =>
                    'Ya existe esta franja horaria para la fecha seleccionada.',
                ]);
        }

        $horario = HorarioDisponible::create([
            'fecha' => $datos['fecha'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_termino' => $datos['hora_termino'],
            'activo' => $request->boolean('activo'),
        ]);

        $horario->servicios()->sync(
            $datos['servicios']
        );

        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                'El horario de atención fue creado correctamente.'
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

        $horario->load('servicios');

        $servicios = ServicioExperiencia::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'horarios-disponibles.edit',
            compact(
                'horario',
                'servicios'
            )
        );
    }

    public function update(
        Request $request,
        HorarioDisponible $horario
    ) {
        $datos = $request->validate(
            [
                'fecha' => [
                    'required',
                    'date',
                ],

                'hora_inicio' => [
                    'required',
                    'date_format:H:i',
                ],

                'hora_termino' => [
                    'required',
                    'date_format:H:i',
                    'after:hora_inicio',
                ],

                'servicios' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'servicios.*' => [
                    'integer',
                    'distinct',
                    'exists:servicios_experiencias,id',
                ],

                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [

                'fecha.required' =>
                'Debes seleccionar una fecha.',

                'hora_inicio.required' =>
                'Debes ingresar la hora de inicio.',

                'hora_termino.required' =>
                'Debes ingresar la hora de término.',

                'hora_termino.after' =>
                'La hora de término debe ser posterior a la hora de inicio.',
            ]
        );

        $existeDuplicado = HorarioDisponible::query()
            ->whereDate('fecha', $datos['fecha'])
            ->where('hora_inicio', $datos['hora_inicio'])
            ->where('hora_termino', $datos['hora_termino'])
            ->where('id', '!=', $horario->id)
            ->exists();

        if ($existeDuplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'hora_inicio' =>
                    'Ya existe esta franja horaria para la fecha seleccionada.'
                ]);
        }

        $datos['activo'] = $request->boolean('activo');

        $horario->update([
            'fecha' => $datos['fecha'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_termino' => $datos['hora_termino'],
            'activo' => $request->boolean('activo'),
        ]);

        $horario->servicios()->sync(
            $datos['servicios']
        );

        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                'El horario de atención fue actualizado correctamente.'
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

    public function generar()
    {
        $servicios = ServicioExperiencia::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'horarios-disponibles.generar',
            compact('servicios')
        );
    }

    public function guardarRecurrentes(Request $request)
    {
        $datos = $request->validate([
            'fecha_desde' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'fecha_hasta' => [
                'required',
                'date',
                'after_or_equal:fecha_desde',
            ],
            'dias_semana' => [
                'required',
                'array',
                'min:1',
            ],
            'dias_semana.*' => [
                'required',
                'integer',
                'between:0,6',
            ],
            'hora_inicio' => [
                'required',
                'date_format:H:i',
            ],
            'hora_termino' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio',
            ],
            'capacidad_maxima' => [
                'required',
                'integer',
                'min:1',
            ],
            'servicios' => [
                'required',
                'array',
                'min:1',
            ],
            'servicios.*' => [
                'integer',
                'distinct',
                'exists:servicios_experiencias,id',
            ],
        ]);

        $periodo = CarbonPeriod::create(
            $datos['fecha_desde'],
            $datos['fecha_hasta']
        );

        $diasSeleccionados = collect($datos['dias_semana'])
            ->map(fn($dia) => (int) $dia);

        $fechas = collect($periodo)
            ->filter(
                fn(Carbon $fecha) =>
                $diasSeleccionados->contains($fecha->dayOfWeek)
            );

        if ($fechas->isEmpty()) {
            throw ValidationException::withMessages([
                'dias_semana' =>
                'No existen fechas coincidentes dentro del rango seleccionado.',
            ]);
        }

        $creados = 0;
        $omitidos = 0;

        DB::transaction(function () use (
            $fechas,
            $datos,
            &$creados,
            &$omitidos
        ) {
            foreach ($fechas as $fecha) {
                $existe = HorarioDisponible::query()
                    ->whereDate('fecha', $fecha->toDateString())
                    ->where('hora_inicio', $datos['hora_inicio'])
                    ->where('hora_termino', $datos['hora_termino'])
                    ->exists();

                if ($existe) {
                    $omitidos++;
                    continue;
                }

                $horario = HorarioDisponible::create([
                    'fecha' => $fecha->toDateString(),
                    'hora_inicio' => $datos['hora_inicio'],
                    'hora_termino' => $datos['hora_termino'],
                    'capacidad_maxima' => $datos['capacidad_maxima'],
                    'activo' => true,
                ]);

                $horario->servicios()->sync(
                    $datos['servicios']
                );

                $creados++;
            }
        });

        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                "Se crearon {$creados} horarios. " .
                    "Se omitieron {$omitidos} horarios duplicados."
            );
    }
}
