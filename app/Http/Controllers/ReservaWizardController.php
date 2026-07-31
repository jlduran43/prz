<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use App\Models\HorarioDisponible;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Reserva;
use App\Models\ServicioExperiencia;
use App\Models\TipoCliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ReservaWizardController extends Controller
{
    public function cliente()
    {
        $tiposCliente = TipoCliente::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $regiones = Region::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('reservas.paso1-cliente', [
            'paso' => 1,
            'tiposCliente' => $tiposCliente,
            'regiones' => $regiones,
            'datosCliente' => session('reserva.cliente', []),
        ]);
    }

    public function guardarCliente(Request $request)
    {
        $datos = $request->validate([
            'tipo_cliente_id' => [
                'required',
                'exists:tipos_cliente,id',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
            ],

            'telefono' => [
                'required',
                'string',
                'max:30',
            ],

            'region_id' => [
                'required',
                'exists:regiones,id',
            ],

            'comuna_id' => [
                'required',
                'exists:comunas,id',
            ],
        ]);

        $tipoCliente = TipoCliente::findOrFail(
            $datos['tipo_cliente_id']
        );

        $esPersona =
            $tipoCliente->tipo_estructura === 'PERSONA';

        $esEntidad =
            $tipoCliente->tipo_estructura === 'ORGANIZACION';

        if ($esPersona) {
            $datos += $request->validate([
                'nombres' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'apellidos' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'rut_persona' => [
                    'required',
                    'string',
                    'max:20',
                ],
            ]);
        }

        if ($esEntidad) {
            $datos += $request->validate([
                'nombre_entidad' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'rut_entidad' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'nombre_encargado' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'rut_encargado' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
            ]);
        }

        $datos['codigo_tipo_cliente'] =
            $tipoCliente->codigo;

        $comunaPerteneceRegion = DB::table('comunas')
            ->where('id', $datos['comuna_id'])
            ->where('region_id', $datos['region_id'])
            ->exists();

        if (! $comunaPerteneceRegion) {
            throw ValidationException::withMessages([
                'comuna_id' =>
                'La comuna seleccionada no pertenece a la región indicada.',
            ]);
        }

        session([
            'reserva.cliente' => $datos,
        ]);

        return redirect()->route('reservas.datos');
    }

    public function guardarReserva(Request $request)
    {
        $esEstablecimiento =
            session('reserva.cliente.codigo_tipo_cliente')
            === 'ESTABLECIMIENTO_EDUCACIONAL';

        $datos = $request->validate(
            [
                'servicios' => [
                    'required',
                    'array',
                    'min:1',
                    'max:2',
                ],

                'servicios.*.servicio_id' => [
                    'required',
                    'integer',
                    'distinct',
                    'exists:servicios_experiencias,id',
                ],

                'servicios.*.fecha' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                ],

                'servicios.*.horario_id' => [
                    'required',
                    'integer',
                    'exists:horarios_disponibles,id',
                ],

                'cantidad_asistentes' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'cantidad_alumnos' => [
                    Rule::requiredIf($esEstablecimiento),
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'cantidad_profesores' => [
                    Rule::requiredIf($esEstablecimiento),
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'nivel_educacional' => [
                    Rule::requiredIf($esEstablecimiento),
                    'nullable',
                    Rule::in([
                        'PARVULARIA',
                        'BASICA',
                        'MEDIA',
                        'ESPECIAL',
                        'SUPERIOR',
                        'ADULTOS',
                        'OTRO',
                    ]),
                ],

                'curso' => [
                    Rule::requiredIf($esEstablecimiento),
                    'nullable',
                    'string',
                    'max:100',
                ],
            ],
            [
                'servicios.required' =>
                'Debes seleccionar al menos un servicio.',

                'servicios.array' =>
                'Los servicios seleccionados no son válidos.',

                'servicios.min' =>
                'Debes seleccionar al menos un servicio.',

                'servicios.max' =>
                'Solo puedes seleccionar un máximo de dos servicios.',

                'servicios.*.servicio_id.required' =>
                'Debes seleccionar un servicio.',

                'servicios.*.servicio_id.distinct' =>
                'No puedes seleccionar dos veces el mismo servicio.',

                'servicios.*.servicio_id.exists' =>
                'Uno de los servicios seleccionados no existe.',

                'servicios.*.fecha.required' =>
                'Debes seleccionar una fecha para cada servicio.',

                'servicios.*.fecha.date' =>
                'La fecha seleccionada no es válida.',

                'servicios.*.fecha.after_or_equal' =>
                'La fecha del servicio no puede ser anterior a hoy.',

                'servicios.*.horario_id.required' =>
                'Debes seleccionar un horario para cada servicio.',

                'servicios.*.horario_id.exists' =>
                'Uno de los horarios seleccionados no existe.',

                'cantidad_asistentes.required' =>
                'Debes indicar la cantidad de asistentes.',

                'cantidad_asistentes.min' =>
                'La reserva debe tener al menos un asistente.',

                'cantidad_alumnos.required' =>
                'Debes indicar la cantidad de alumnos.',

                'cantidad_profesores.required' =>
                'Debes indicar la cantidad de profesores.',

                'nivel_educacional.required' =>
                'Debes seleccionar el nivel educacional.',

                'curso.required' =>
                'Debes indicar el curso.',
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Obtener los identificadores de horarios seleccionados
    |--------------------------------------------------------------------------
    */
        $horarioIds = collect($datos['servicios'])
            ->pluck('horario_id')
            ->unique()
            ->values()
            ->all();

        /*
    |--------------------------------------------------------------------------
    | Consultar los horarios
    |--------------------------------------------------------------------------
    */
        $horariosSeleccionados = HorarioDisponible::query()
            ->whereIn('id', $horarioIds)
            ->get([
                'id',
                'hora_inicio',
                'hora_termino',
            ])
            ->keyBy('id');

        /*
    |--------------------------------------------------------------------------
    | Comprobar que todos los horarios existan
    |--------------------------------------------------------------------------
    */
        if ($horariosSeleccionados->count() !== count($horarioIds)) {
            throw ValidationException::withMessages([
                'servicios' =>
                'Uno o más horarios seleccionados no están disponibles.',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Comprobar superposición de horarios
    |--------------------------------------------------------------------------
    | Solamente se comparan horarios que estén en la misma fecha.
    */
        $serviciosSeleccionados = array_values($datos['servicios']);

        for ($i = 0; $i < count($serviciosSeleccionados); $i++) {
            for (
                $j = $i + 1;
                $j < count($serviciosSeleccionados);
                $j++
            ) {
                $servicioA = $serviciosSeleccionados[$i];
                $servicioB = $serviciosSeleccionados[$j];

                /*
             * Si tienen fechas diferentes, no existe superposición.
             */
                if ($servicioA['fecha'] !== $servicioB['fecha']) {
                    continue;
                }

                $horarioA = $horariosSeleccionados->get(
                    $servicioA['horario_id']
                );

                $horarioB = $horariosSeleccionados->get(
                    $servicioB['horario_id']
                );

                $seSuperponen =
                    $horarioA->hora_inicio < $horarioB->hora_termino
                    &&
                    $horarioA->hora_termino > $horarioB->hora_inicio;

                if ($seSuperponen) {
                    throw ValidationException::withMessages([
                        'servicios' =>
                        'Los servicios de una misma fecha no pueden tener horarios superpuestos.',
                    ]);
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Reordenar los servicios
    |--------------------------------------------------------------------------
    | Convierte claves como 3 y 8 en posiciones normales 0 y 1.
    */
        $datos['servicios'] = array_values($datos['servicios']);

        session([
            'reserva.datos' => $datos,
        ]);

        return redirect()->route('reservas.confirmacion');
    }

    public function confirmacion()
    {
        if (
            ! session()->has('reserva.cliente') ||
            ! session()->has('reserva.datos')
        ) {
            return redirect()->route('reservas.cliente');
        }

        $cliente = session('reserva.cliente');
        $reserva = session('reserva.datos');

        $tipoCliente = TipoCliente::query()
            ->find($cliente['tipo_cliente_id']);

        $regionNombre = DB::table('regiones')
            ->where('id', $cliente['region_id'])
            ->value('nombre');

        $comunaNombre = DB::table('comunas')
            ->where('id', $cliente['comuna_id'])
            ->value('nombre');

        $servicioIds = collect($reserva['servicios'])
            ->pluck('servicio_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $servicios = ServicioExperiencia::query()
            ->whereIn('id', $servicioIds)
            ->where('activo', true)
            ->get()
            ->keyBy('id');

        $horarioIds = collect($reserva['servicios'])
            ->pluck('horario_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $horarios = HorarioDisponible::query()
            ->whereIn('id', $horarioIds)
            ->get()
            ->keyBy('id');

        $cantidadPersonas =
            (int) $reserva['cantidad_asistentes'];

        $detallesServicios = collect(
            $reserva['servicios']
        )->map(function ($seleccion) use (
            $servicios,
            $horarios,
            $cantidadPersonas
        ) {
            $servicioId = $seleccion['servicio_id'];
            $horarioId = $seleccion['horario_id'];
            $fecha = $seleccion['fecha'];

            $servicio = $servicios->get($servicioId);

            if (! $servicio) {
                return null;
            }

            $horario = $horarios->get($horarioId);

            if (! $horario) {
                return null;
            }

            return [
                'id' => $servicio->id,
                'nombre' => $servicio->nombre,
                'tipo_cobro' => $servicio->tipo_cobro,
                'precio_unitario' => (float) $servicio->precio,

                'subtotal' =>
                $this->calcularSubtotalServicio(
                    $servicio,
                    $cantidadPersonas
                ),

                'fecha' => $fecha,

                'horario_id' => $horario->id,

                'hora_inicio' =>
                substr($horario->hora_inicio, 0, 5),

                'hora_termino' =>
                substr($horario->hora_termino, 0, 5),
            ];
        })->filter()->values();

        if (
            $detallesServicios->count()
            !== count($reserva['servicios'])
        ) {
            return redirect()
                ->route('reservas.datos')
                ->with(
                    'error',
                    'Uno o más servicios seleccionados ya no están disponibles.'
                );
        }

        $subtotal = $detallesServicios->sum(
            'subtotal'
        );

        return view('reservas.paso3-confirmacion', [
            'paso' => 3,
            'cliente' => $cliente,
            'reserva' => $reserva,
            'tipoCliente' => $tipoCliente,
            'regionNombre' => $regionNombre,
            'comunaNombre' => $comunaNombre,
            'detallesServicios' => $detallesServicios,
            'subtotal' => $subtotal,
            'descuento' => 0,
            'total' => $subtotal,
        ]);
    }

    public function finalizar(): RedirectResponse
    {
        $datosCliente = session('reserva.cliente');
        $datosReserva = session('reserva.datos');

        if (! $datosCliente || ! $datosReserva) {
            return redirect()
                ->route('reservas.cliente')
                ->with('error', 'La información de la reserva está incompleta.');
        }

        $reserva = DB::transaction(function () use (
            $datosCliente,
            $datosReserva
        ) {
            $servicios = ServicioExperiencia::query()
                ->whereIn('id', $datosReserva['servicios'])
                ->where('activo', true)
                ->get();

            if (
                $servicios->isEmpty()
                || $servicios->count() > 2
                || $servicios->count()
                !== count($datosReserva['servicios'])
            ) {
                throw ValidationException::withMessages([
                    'servicios' =>
                    'Uno o más servicios ya no están disponibles.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Comprobar que los horarios sigan disponibles
            |--------------------------------------------------------------------------
            */

            foreach ($servicios as $servicio) {
                $horarioId =
                    $datosReserva['horarios'][$servicio->id];

                $horarioOcupado = DB::table('reserva_servicio')
                    ->join(
                        'reservas',
                        'reservas.id',
                        '=',
                        'reserva_servicio.reserva_id'
                    )
                    ->where(
                        'reservas.fecha',
                        $datosReserva['fecha']
                    )
                    ->where(
                        'reserva_servicio.servicio_experiencia_id',
                        $servicio->id
                    )
                    ->where(
                        'reserva_servicio.horario_disponible_id',
                        $horarioId
                    )
                    ->whereNotIn('reservas.estado', [
                        'CANCELADA',
                        'RECHAZADA',
                    ])
                    ->exists();

                if ($horarioOcupado) {
                    throw ValidationException::withMessages([
                        'horarios' =>
                        "El horario de {$servicio->nombre} ya no está disponible.",
                    ]);
                }
            }

            $cantidadPersonas =
                (int) $datosReserva['cantidad_asistentes'];

            $subtotalGeneral = $servicios->sum(
                fn(ServicioExperiencia $servicio) =>
                $this->calcularSubtotalServicio(
                    $servicio,
                    $cantidadPersonas
                )
            );

            $reserva = Reserva::query()->create([
                'tipo_cliente_id' =>
                $datosCliente['tipo_cliente_id'],

                'nombres' =>
                $datosCliente['nombres'] ?? null,

                'apellidos' =>
                $datosCliente['apellidos'] ?? null,

                'rut_persona' =>
                $datosCliente['rut_persona'] ?? null,

                'nombre_entidad' =>
                $datosCliente['nombre_entidad'] ?? null,

                'rut_entidad' =>
                $datosCliente['rut_entidad'] ?? null,

                'nombre_encargado' =>
                $datosCliente['nombre_encargado'] ?? null,

                'rut_encargado' =>
                $datosCliente['rut_encargado'] ?? null,

                'email' =>
                $datosCliente['email'],

                'telefono' =>
                $datosCliente['telefono'],

                'region_id' =>
                $datosCliente['region_id'] ?? null,

                'comuna_id' =>
                $datosCliente['comuna_id'] ?? null,

                'fecha' =>
                $datosReserva['fecha'],

                'cantidad_asistentes' =>
                $cantidadPersonas,

                'cantidad_alumnos' =>
                $datosReserva['cantidad_alumnos'] ?? null,

                'cantidad_profesores' =>
                $datosReserva['cantidad_profesores'] ?? null,

                'nivel_educacional' =>
                $datosReserva['nivel_educacional'] ?? null,

                'curso' =>
                $datosReserva['curso'] ?? null,

                'subtotal' =>
                $subtotalGeneral,

                'descuento' => 0,

                'total' =>
                $subtotalGeneral,

                'estado' =>
                'PENDIENTE',
            ]);

            foreach ($servicios as $servicio) {
                $precioUnitario = (float) $servicio->precio;

                $subtotalServicio =
                    $this->calcularSubtotalServicio(
                        $servicio,
                        $cantidadPersonas
                    );

                $horarioId =
                    $datosReserva['horarios'][$servicio->id];

                $reserva->servicios()->attach(
                    $servicio->id,
                    [
                        'horario_disponible_id' =>
                        $horarioId,

                        'precio_unitario' =>
                        $precioUnitario,

                        'cantidad_personas' =>
                        $cantidadPersonas,

                        'subtotal' =>
                        $subtotalServicio,
                    ]
                );
            }

            return $reserva;
        });

        session()->forget('reserva');

        return redirect()
            ->route('reservas.cliente')
            ->with(
                'success',
                "La reserva N.º {$reserva->id} fue registrada correctamente."
            );
    }

    public function comunasPorRegion(Region $region)
    {
        $comunas = $region->comunas()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ]);

        return response()->json($comunas);
    }

    public function reserva()
    {
        if (! session()->has('reserva.cliente')) {
            return redirect()->route('reservas.cliente');
        }

        $categoriasServicio = CategoriaServicio::query()
            ->where('activo', true)
            ->with([
                'servicios' => function ($query) {
                    $query
                        ->where('activo', true)
                        ->orderBy('nombre');
                },
            ])
            ->whereHas('servicios', function ($query) {
                $query->where('activo', true);
            })
            ->orderBy('nombre')
            ->get();

        return view('reservas.paso2-reserva', [
            'paso' => 2,
            'datosCliente' => session('reserva.cliente'),
            'datosReserva' => session('reserva.datos', []),
            'categoriasServicio' => $categoriasServicio,
        ]);
    }

    public function consultarHorarios(
        Request $request
    ): JsonResponse {
        $datos = $request->validate([
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'servicio_id' => [
                'required',
                Rule::exists(
                    'servicios_experiencias',
                    'id'
                )->where('activo', true),
            ],
        ]);

        $servicio = ServicioExperiencia::query()
            ->findOrFail($datos['servicio_id']);

        $horariosOcupados = DB::table('reserva_servicio')
            ->join(
                'reservas',
                'reservas.id',
                '=',
                'reserva_servicio.reserva_id'
            )
            ->where(
                'reservas.fecha',
                $datos['fecha']
            )
            ->where(
                'reserva_servicio.servicio_experiencia_id',
                $servicio->id
            )
            ->whereNotIn('reservas.estado', [
                'CANCELADA',
                'RECHAZADA',
            ])
            ->pluck(
                'reserva_servicio.horario_disponible_id'
            );

        $horarios = HorarioDisponible::query()
            ->where(
                'servicio_experiencia_id',
                $servicio->id
            )
            ->whereDate(
                'fecha',
                $datos['fecha']
            )
            ->where('activo', true)
            ->orderBy('hora_inicio')
            ->get()
            ->map(function (
                HorarioDisponible $horario
            ) use ($horariosOcupados) {
                /*
             *
             * Más adelante aquí se calculará la disponibilidad
             * considerando:
             * - servicio;
             * - fecha;
             * - horario;
             * - capacidad máxima del servicio;
             * - personas ya reservadas.
             */

                return [
                    'id' => $horario->id,

                    'hora_inicio' => substr(
                        $horario->hora_inicio,
                        0,
                        5
                    ),

                    'hora_termino' => substr(
                        $horario->hora_termino,
                        0,
                        5
                    ),

                    'disponible' => ! $horariosOcupados->contains($horario->id),
                ];
            });

        return response()->json([
            'servicio' => [
                'id' => $servicio->id,
                'nombre' => $servicio->nombre,
            ],

            'horarios' => $horarios,
        ]);
    }

    private function calcularSubtotalServicio(
        ServicioExperiencia $servicio,
        int $cantidadPersonas
    ): float {
        $precio = (float) $servicio->precio;

        return match ($servicio->tipo_cobro) {
            'POR_PERSONA' =>
            $precio * $cantidadPersonas,

            'POR_GRUPO',
            'FIJO' =>
            $precio,

            default =>
            $precio,
        };
    }

    public function consultarServiciosDisponibles(): JsonResponse
    {
        $servicios = ServicioExperiencia::query()
            ->with('categoria')
            ->where('activo', true)
            ->whereIn(
                'id',
                HorarioDisponible::query()
                    ->where('activo', true)
                    ->whereDate('fecha', '>=', today())
                    ->whereNotNull('servicio_experiencia_id')
                    ->select('servicio_experiencia_id')
            )
            ->orderBy('nombre')
            ->get([
                'id',
                'categoria_servicio_id',
                'nombre',
                'precio',
                'tipo_cobro',
            ])
            ->map(function (
                ServicioExperiencia $servicio
            ) {
                return [
                    'id' => $servicio->id,
                    'nombre' => $servicio->nombre,
                    'precio' => $servicio->precio,
                    'tipo_cobro' => $servicio->tipo_cobro,
                    'categoria' => $servicio->categoria
                        ? [
                            'id' => $servicio->categoria->id,
                            'nombre' => $servicio->categoria->nombre,
                        ]
                        : null,
                ];
            });

        return response()->json([
            'servicios' => $servicios,
        ]);
    }
}
