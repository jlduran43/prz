<?php

namespace App\Http\Controllers;

use App\Models\HorarioDisponible;
use Illuminate\Http\Request;
use App\Models\ServicioExperiencia;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;

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

        $horarios = HorarioDisponible::query()
            ->with([
                'servicios' => function ($query) {
                    $query->orderBy('nombre');
                },
            ])
            ->when(
                $fecha !== '',
                function ($query) use ($fecha) {
                    $query->whereDate('fecha', $fecha);
                }
            )
            ->when(
                $estado !== null && $estado !== '',
                function ($query) use ($estado) {
                    $query->where('activo', $estado);
                }
            )
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

    public function eventosCalendario(Request $request)
    {
        $query = HorarioDisponible::query()
            ->with([
                'servicios' => function ($query) {
                    $query->orderBy('nombre');
                },
            ]);

        /*
    |--------------------------------------------------------------------------
    | FullCalendar envía start y end automáticamente
    |--------------------------------------------------------------------------
    */

        if ($request->filled('start')) {
            $query->whereDate(
                'fecha',
                '>=',
                Carbon::parse($request->start)->toDateString()
            );
        }

        if ($request->filled('end')) {
            $query->whereDate(
                'fecha',
                '<',
                Carbon::parse($request->end)->toDateString()
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filtro de estado
    |--------------------------------------------------------------------------
    */

        if (
            $request->has('estado') &&
            $request->estado !== ''
        ) {
            $query->where(
                'activo',
                (bool) $request->estado
            );
        }

        $horarios = $query
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json(
            $horarios->map(function ($horario) {

                $servicios = $horario
                    ->servicios
                    ->pluck('nombre')
                    ->values();

                return [
                    'id' => $horario->id,

                    'title' =>
                    substr($horario->hora_inicio, 0, 5)
                        . ' - '
                        . substr($horario->hora_termino, 0, 5),

                    'start' =>
                    $horario->fecha->format('Y-m-d')
                        . 'T'
                        . substr($horario->hora_inicio, 0, 5),

                    'end' =>
                    $horario->fecha->format('Y-m-d')
                        . 'T'
                        . substr($horario->hora_termino, 0, 5),

                    'extendedProps' => [
                        'activo' =>
                        (bool) $horario->activo,

                        'servicios' =>
                        $servicios->all(),

                        'google' =>
                        $horario->google_sync_error
                            ? 'ERROR'
                            : (
                                $horario->google_event_id
                                ? 'SINCRONIZADO'
                                : 'PENDIENTE'
                            ),
                    ],
                ];
            })
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

        $superpuesto = HorarioDisponible::query()
            ->whereDate(
                'fecha',
                $datos['fecha']
            )
            ->where(
                'hora_inicio',
                '<',
                $datos['hora_termino']
            )
            ->where(
                'hora_termino',
                '>',
                $datos['hora_inicio']
            )
            ->exists();

        if ($superpuesto) {
            return back()
                ->withInput()
                ->withErrors([
                    'hora_inicio' =>
                    'El horario se superpone con otra franja existente '
                        . 'para esa fecha.',
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

        try {

            $googleCalendar = app(
                GoogleCalendarService::class
            );

            $googleEventId = $googleCalendar
                ->crearEvento($horario);

            $horario->update([
                'google_event_id' => $googleEventId,
                'google_synced_at' => now(),
                'google_sync_error' => null,
            ]);
        } catch (\Throwable $e) {

            Log::error(
                'Error sincronizando horario con Google Calendar',
                [
                    'horario_id' => $horario->id,
                    'error' => $e->getMessage(),
                ]
            );

            $horario->update([
                'google_sync_error' => $e->getMessage(),
            ]);
        }

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

    public function update(Request $request, HorarioDisponible $horario)
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

                'capacidad_maxima' => [
                    'required',
                    'integer',
                    'min:1',
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

        /*
            |--------------------------------------------------------------------------
            | Validar superposición
            |--------------------------------------------------------------------------
        */

        $superpuesto = HorarioDisponible::query()
            ->whereDate(
                'fecha',
                $datos['fecha']
            )
            ->where(
                'id',
                '!=',
                $horario->id
            )
            ->where(
                'hora_inicio',
                '<',
                $datos['hora_termino']
            )
            ->where(
                'hora_termino',
                '>',
                $datos['hora_inicio']
            )
            ->exists();


        if ($superpuesto) {

            return back()
                ->withInput()
                ->withErrors([
                    'hora_inicio' =>
                    'El horario se superpone con '
                        . 'otra franja existente '
                        . 'para esa fecha.',
                ]);
        }

        $datos['activo'] = $request->boolean('activo');

        $horario->update([
            'fecha' => $datos['fecha'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_termino' => $datos['hora_termino'],
            'capacidad_maxima' => $datos['capacidad_maxima'],
            'activo' => $request->boolean('activo'),
        ]);

        $horario->servicios()->sync(
            $datos['servicios']
        );

        /*
            |--------------------------------------------------------------------------
            | Sincronizar modificación con Google Calendar
            |--------------------------------------------------------------------------
        */

        try {

            $googleCalendar = app(
                GoogleCalendarService::class
            );

            $googleCalendar->actualizarEvento(
                $horario
            );

            $horario->update([
                'google_synced_at' => now(),
                'google_sync_error' => null,
            ]);
        } catch (\Throwable $e) {

            Log::error(
                'Error actualizando horario en Google Calendar',
                [
                    'horario_id' => $horario->id,
                    'google_event_id' => $horario->google_event_id,
                    'error' => $e->getMessage(),
                ]
            );

            $horario->update([
                'google_sync_error' => $e->getMessage(),
            ]);
        }

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

        try {

            app(GoogleCalendarService::class)
                ->actualizarEstadoEvento($horario);

            $horario->update([
                'google_synced_at' => now(),
                'google_sync_error' => null,
            ]);
        } catch (\Throwable $e) {

            Log::error(
                'Error desactivando horario en Google Calendar',
                [
                    'horario_id' => $horario->id,
                    'google_event_id' => $horario->google_event_id,
                    'error' => $e->getMessage(),
                ]
            );

            $horario->update([
                'google_sync_error' => $e->getMessage(),
            ]);
        }

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

        try {

            app(GoogleCalendarService::class)
                ->actualizarEstadoEvento($horario);

            $horario->update([
                'google_synced_at' => now(),
                'google_sync_error' => null,
            ]);
        } catch (\Throwable $e) {

            Log::error(
                'Error reactivando horario en Google Calendar',
                [
                    'horario_id' => $horario->id,
                    'google_event_id' => $horario->google_event_id,
                    'error' => $e->getMessage(),
                ]
            );

            $horario->update([
                'google_sync_error' => $e->getMessage(),
            ]);
        }

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

            'franjas' => [
                'required',
                'array',
                'min:1',
            ],

            'franjas.*.hora_inicio' => [
                'required',
                'date_format:H:i',
            ],

            'franjas.*.hora_termino' => [
                'required',
                'date_format:H:i',
            ],

            'franjas.*.capacidad_maxima' => [
                'required',
                'integer',
                'min:1',
            ],

            'franjas.*.servicios' => [
                'required',
                'array',
                'min:1',
            ],

            'franjas.*.servicios.*' => [
                'required',
                'integer',
                'distinct',
                'exists:servicios_experiencias,id',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Validar hora inicio < hora término
    |--------------------------------------------------------------------------
    */

        foreach ($datos['franjas'] as $indice => $franja) {

            if (
                $franja['hora_termino']
                <=
                $franja['hora_inicio']
            ) {
                throw ValidationException::withMessages([
                    "franjas.$indice.hora_termino" =>
                    'La hora de término debe ser posterior '
                        . 'a la hora de inicio.',
                ]);
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Validar franjas duplicadas
    |--------------------------------------------------------------------------
    */

        $franjasComparar = collect(
            $datos['franjas']
        )->map(function ($franja) {

            return $franja['hora_inicio']
                . '-'
                . $franja['hora_termino'];
        });


        if (
            $franjasComparar->unique()->count()
            !==
            $franjasComparar->count()
        ) {
            throw ValidationException::withMessages([
                'franjas' =>
                'No puedes agregar dos franjas '
                    . 'con el mismo horario.',
            ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Validar franjas superpuestas
    |--------------------------------------------------------------------------
    */

        $franjas = array_values(
            $datos['franjas']
        );


        for ($i = 0; $i < count($franjas); $i++) {

            for (
                $j = $i + 1;
                $j < count($franjas);
                $j++
            ) {

                $inicioA =
                    $franjas[$i]['hora_inicio'];

                $terminoA =
                    $franjas[$i]['hora_termino'];

                $inicioB =
                    $franjas[$j]['hora_inicio'];

                $terminoB =
                    $franjas[$j]['hora_termino'];


                $seSuperponen =
                    $inicioA < $terminoB
                    &&
                    $inicioB < $terminoA;


                if ($seSuperponen) {

                    throw ValidationException::withMessages([
                        'franjas' =>
                        "Las franjas "
                            . "{$inicioA} - {$terminoA} "
                            . "y "
                            . "{$inicioB} - {$terminoB} "
                            . "se superponen.",
                    ]);
                }
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Obtener fechas seleccionadas
    |--------------------------------------------------------------------------
    */

        $periodo = CarbonPeriod::create(
            $datos['fecha_desde'],
            $datos['fecha_hasta']
        );


        $diasSeleccionados = collect(
            $datos['dias_semana']
        )->map(
            fn($dia) => (int) $dia
        );


        $fechas = collect($periodo)
            ->filter(
                fn(Carbon $fecha) =>
                $diasSeleccionados->contains(
                    $fecha->dayOfWeek
                )
            );


        if ($fechas->isEmpty()) {

            throw ValidationException::withMessages([
                'dias_semana' =>
                'No existen fechas coincidentes '
                    . 'dentro del rango seleccionado.',
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

                foreach (
                    $datos['franjas'] as $franja
                ) {

                    /*
                        |--------------------------------------------------------------------------
                        | Revisar Superposición
                        |--------------------------------------------------------------------------
                    */

                    $existeSuperposicion = HorarioDisponible::query()
                        ->whereDate(
                            'fecha',
                            $fecha->toDateString()
                        )
                        ->where(function ($query) use ($franja) {

                            $query
                                ->where(
                                    'hora_inicio',
                                    '<',
                                    $franja['hora_termino']
                                )
                                ->where(
                                    'hora_termino',
                                    '>',
                                    $franja['hora_inicio']
                                );
                        })
                        ->exists();


                    if ($existeSuperposicion) {

                        $omitidos++;

                        continue;
                    }


                    /*
                |--------------------------------------------------------------------------
                | Crear horario
                |--------------------------------------------------------------------------
                */

                    $horario =
                        HorarioDisponible::create([
                            'fecha' =>
                            $fecha->toDateString(),

                            'hora_inicio' =>
                            $franja['hora_inicio'],

                            'hora_termino' =>
                            $franja['hora_termino'],

                            'capacidad_maxima' =>
                            $franja['capacidad_maxima'],

                            'activo' => true,
                        ]);


                    /*
                |--------------------------------------------------------------------------
                | Asociar servicios
                |--------------------------------------------------------------------------
                */

                    $horario
                        ->servicios()
                        ->sync(
                            $franja['servicios']
                        );


                    $creados++;


                    /*
                |--------------------------------------------------------------------------
                | Google Calendar
                |--------------------------------------------------------------------------
                */

                    try {

                        $googleCalendar = app(
                            GoogleCalendarService::class
                        );


                        $googleEventId =
                            $googleCalendar
                            ->crearEvento($horario);


                        $horario->update([
                            'google_event_id' =>
                            $googleEventId,

                            'google_synced_at' =>
                            now(),

                            'google_sync_error' =>
                            null,
                        ]);
                    } catch (\Throwable $e) {

                        Log::error(
                            'Error sincronizando horario '
                                . 'recurrente con Google Calendar',
                            [
                                'horario_id' =>
                                $horario->id,

                                'error' =>
                                $e->getMessage(),
                            ]
                        );


                        $horario->update([
                            'google_sync_error' =>
                            $e->getMessage(),
                        ]);
                    }
                }
            }
        });


        return redirect()
            ->route('horarios-disponibles.index')
            ->with(
                'success',
                "Se crearon {$creados} horarios. "
                    . "Se omitieron {$omitidos} horarios"
                    . "por duplicidad o superposición."
            );
    }
}
