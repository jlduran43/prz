<?php

namespace App\Http\Controllers;

use App\Models\HorarioDisponible;
use Illuminate\Http\Request;
use App\Models\ServicioExperiencia;

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
}
