<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use App\Models\ConfiguracionReserva;
use App\Models\HorarioDisponible;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Reserva;
use App\Models\ServicioExperiencia;
use App\Models\TipoCliente;
use App\Rules\RutChileno;
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
                    new RutChileno(),
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
                    new RutChileno(),
                ],

                'nombre_encargado' => [
                    'required',
                    'string',
                    'max:150',
                    new RutChileno(),
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
                    Rule::exists(
                        'servicios_experiencias',
                        'id'
                    )->where('activo', true),
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

                'objetivo_visita' => [
                    'nullable',
                    'string',
                    'max:500',
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

                'servicios.*.servicio_id.integer' =>
                'El servicio seleccionado no es válido.',

                'servicios.*.servicio_id.distinct' =>
                'No puedes seleccionar dos veces el mismo servicio.',

                'servicios.*.servicio_id.exists' =>
                'Uno de los servicios seleccionados no existe o está inactivo.',

                'servicios.*.fecha.required' =>
                'Debes seleccionar una fecha para cada servicio.',

                'servicios.*.fecha.date' =>
                'La fecha seleccionada no es válida.',

                'servicios.*.fecha.after_or_equal' =>
                'La fecha del servicio no puede ser anterior a hoy.',

                'servicios.*.horario_id.required' =>
                'Debes seleccionar un horario para cada servicio.',

                'servicios.*.horario_id.integer' =>
                'El horario seleccionado no es válido.',

                'servicios.*.horario_id.exists' =>
                'Uno de los horarios seleccionados no existe.',

                'cantidad_asistentes.required' =>
                'Debes indicar la cantidad de asistentes.',

                'cantidad_asistentes.integer' =>
                'La cantidad de asistentes debe ser un número entero.',

                'cantidad_asistentes.min' =>
                'La reserva debe tener al menos un asistente.',

                'cantidad_alumnos.required' =>
                'Debes indicar la cantidad de alumnos.',

                'cantidad_alumnos.integer' =>
                'La cantidad de alumnos debe ser un número entero.',

                'cantidad_alumnos.min' =>
                'Debe existir al menos un alumno.',

                'cantidad_profesores.required' =>
                'Debes indicar la cantidad de profesores.',

                'cantidad_profesores.integer' =>
                'La cantidad de profesores debe ser un número entero.',

                'cantidad_profesores.min' =>
                'La cantidad de profesores no puede ser negativa.',

                'nivel_educacional.required' =>
                'Debes seleccionar el nivel educacional.',

                'nivel_educacional.in' =>
                'El nivel educacional seleccionado no es válido.',

                'curso.required' =>
                'Debes indicar el curso.',

                'curso.string' =>
                'El curso debe ser un texto.',

                'curso.max' =>
                'El curso no puede superar los 100 caracteres.',

                'objetivo_visita.string' =>
                'El objetivo de la visita debe ser un texto.',

                'objetivo_visita.max' =>
                'El objetivo de la visita no puede superar los 500 caracteres.',
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Validar la composición del grupo educacional
    |--------------------------------------------------------------------------
    */
        if ($esEstablecimiento) {
            $totalEducacional =
                (int) $datos['cantidad_alumnos']
                + (int) $datos['cantidad_profesores'];

            if (
                $totalEducacional
                !== (int) $datos['cantidad_asistentes']
            ) {
                throw ValidationException::withMessages([
                    'cantidad_asistentes' =>
                    'El total de asistentes debe coincidir con la suma de alumnos y profesores.',
                ]);
            }
        }

        $cantidadAsistentes =
            (int) $datos['cantidad_asistentes'];

        /*
    |--------------------------------------------------------------------------
    | Obtener capacidad simultánea general
    |--------------------------------------------------------------------------
    */
        $capacidadSimultanea = ConfiguracionReserva::query()
            ->value('capacidad_maxima_simultanea');

        if ($capacidadSimultanea === null) {
            throw ValidationException::withMessages([
                'servicios' =>
                'No se ha configurado la capacidad máxima simultánea del parque.',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Obtener servicios y horarios seleccionados
    |--------------------------------------------------------------------------
    */
        $servicioIds = collect($datos['servicios'])
            ->pluck('servicio_id')
            ->unique()
            ->values()
            ->all();

        $horarioIds = collect($datos['servicios'])
            ->pluck('horario_id')
            ->unique()
            ->values()
            ->all();

        $serviciosDisponibles =
            ServicioExperiencia::query()
            ->whereIn('id', $servicioIds)
            ->where('activo', true)
            ->get([
                'id',
                'nombre',
            ])
            ->keyBy('id');

        $horariosSeleccionados =
            HorarioDisponible::query()
            ->with([
                'servicios:id',
            ])
            ->whereIn('id', $horarioIds)
            ->where('activo', true)
            ->get([
                'id',
                'fecha',
                'hora_inicio',
                'hora_termino',
                'activo',
            ])
            ->keyBy('id');

        if (
            $serviciosDisponibles->count()
            !== count($servicioIds)
        ) {
            throw ValidationException::withMessages([
                'servicios' =>
                'Uno o más servicios ya no están disponibles.',
            ]);
        }

        if (
            $horariosSeleccionados->count()
            !== count($horarioIds)
        ) {
            throw ValidationException::withMessages([
                'servicios' =>
                'Uno o más horarios ya no están disponibles.',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Validar cada servicio y horario
    |--------------------------------------------------------------------------
    */
        foreach ($datos['servicios'] as $indice => $seleccion) {
            $servicioId =
                (int) $seleccion['servicio_id'];

            $horarioId =
                (int) $seleccion['horario_id'];

            $fechaSeleccionada =
                $seleccion['fecha'];

            $servicio =
                $serviciosDisponibles->get($servicioId);

            $horario =
                $horariosSeleccionados->get($horarioId);

            /*
         * Comprobar que la fecha del horario sea la seleccionada.
         */
            if (
                $horario->fecha->format('Y-m-d')
                !== $fechaSeleccionada
            ) {
                throw ValidationException::withMessages([
                    "servicios.{$indice}.horario_id" =>
                    "El horario seleccionado no corresponde a la fecha {$fechaSeleccionada}.",
                ]);
            }

            /*
         * Comprobar que el servicio esté habilitado
         * para la franja mediante horario_servicio.
         */
            $servicioAsociado = $horario
                ->servicios
                ->contains('id', $servicioId);

            if (! $servicioAsociado) {
                throw ValidationException::withMessages([
                    "servicios.{$indice}.horario_id" =>
                    "El servicio {$servicio->nombre} no está disponible en el horario seleccionado.",
                ]);
            }

            /*
         * Personas presentes en cualquier horario
         * que se superponga con esta franja.
         */
            $personasSuperpuestas =
                $this->obtenerPersonasSuperpuestas(
                    $horario
                );

            $cuposGeneralesDisponibles = max(
                (int) $capacidadSimultanea
                    - $personasSuperpuestas,
                0
            );

            if (
                $cantidadAsistentes
                > $cuposGeneralesDisponibles
            ) {
                throw ValidationException::withMessages([
                    'cantidad_asistentes' =>
                    "Durante la franja {$horario->hora_inicio} a {$horario->hora_termino} solamente quedan {$cuposGeneralesDisponibles} cupos disponibles considerando la capacidad simultánea del parque.",
                ]);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Comprobar superposición entre los servicios de esta reserva
    |--------------------------------------------------------------------------
    | Aunque el parque permita actividades simultáneas, el mismo grupo
    | no puede asistir a dos servicios al mismo tiempo.
    */
        $serviciosSeleccionados =
            array_values($datos['servicios']);

        for (
            $i = 0;
            $i < count($serviciosSeleccionados);
            $i++
        ) {
            for (
                $j = $i + 1;
                $j < count($serviciosSeleccionados);
                $j++
            ) {
                $seleccionA =
                    $serviciosSeleccionados[$i];

                $seleccionB =
                    $serviciosSeleccionados[$j];

                if (
                    $seleccionA['fecha']
                    !== $seleccionB['fecha']
                ) {
                    continue;
                }

                $horarioA =
                    $horariosSeleccionados->get(
                        $seleccionA['horario_id']
                    );

                $horarioB =
                    $horariosSeleccionados->get(
                        $seleccionB['horario_id']
                    );

                $seSuperponen =
                    $horarioA->hora_inicio
                    < $horarioB->hora_termino
                    &&
                    $horarioA->hora_termino
                    > $horarioB->hora_inicio;

                if ($seSuperponen) {
                    throw ValidationException::withMessages([
                        'servicios' =>
                        'Los servicios seleccionados para el mismo grupo no pueden tener horarios superpuestos.',
                    ]);
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Reordenar y guardar temporalmente en sesión
    |--------------------------------------------------------------------------
    */
        $datos['servicios'] =
            array_values($datos['servicios']);

        session([
            'reserva.datos' => $datos,
        ]);

        return redirect()
            ->route('reservas.confirmacion');
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
                ->with(
                    'error',
                    'La información de la reserva está incompleta.'
                );
        }

        try {
            $reserva = DB::transaction(function () use (
                $datosCliente,
                $datosReserva
            ) {
                /*
            |--------------------------------------------------------------------------
            | Normalizar servicios guardados en sesión
            |--------------------------------------------------------------------------
            */
                $selecciones = array_values(
                    $datosReserva['servicios'] ?? []
                );

                if (
                    count($selecciones) < 1
                    || count($selecciones) > 2
                ) {
                    throw ValidationException::withMessages([
                        'servicios' =>
                        'Debes seleccionar entre uno y dos servicios.',
                    ]);
                }

                $cantidadPersonas = (int) (
                    $datosReserva['cantidad_asistentes'] ?? 0
                );

                if ($cantidadPersonas < 1) {
                    throw ValidationException::withMessages([
                        'cantidad_asistentes' =>
                        'La cantidad de asistentes no es válida.',
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Obtener capacidad simultánea general
            |--------------------------------------------------------------------------
            */
                $capacidadSimultanea =
                    ConfiguracionReserva::query()
                    ->value(
                        'capacidad_maxima_simultanea'
                    );

                if ($capacidadSimultanea === null) {
                    throw ValidationException::withMessages([
                        'servicios' =>
                        'No se ha configurado la capacidad máxima simultánea del parque.',
                    ]);
                }

                $capacidadSimultanea =
                    (int) $capacidadSimultanea;

                /*
            |--------------------------------------------------------------------------
            | Obtener identificadores
            |--------------------------------------------------------------------------
            */
                $servicioIds = collect($selecciones)
                    ->pluck('servicio_id')
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                $horarioIds = collect($selecciones)
                    ->pluck('horario_id')
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                $fechas = collect($selecciones)
                    ->pluck('fecha')
                    ->unique()
                    ->values()
                    ->all();

                /*
            |--------------------------------------------------------------------------
            | Bloquear horarios de las fechas involucradas
            |--------------------------------------------------------------------------
            | Todas las finalizaciones deben usar este mismo bloqueo.
            | Así evitamos que dos reservas simultáneas lean los mismos cupos.
            */
                HorarioDisponible::query()
                    ->whereIn('fecha', $fechas)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get(['id']);

                /*
            |--------------------------------------------------------------------------
            | Obtener servicios activos
            |--------------------------------------------------------------------------
            */
                $servicios = ServicioExperiencia::query()
                    ->whereIn('id', $servicioIds)
                    ->where('activo', true)
                    ->get([
                        'id',
                        'nombre',
                        'precio',
                        'tipo_cobro',
                        'activo',
                    ])
                    ->keyBy('id');

                if (
                    $servicios->count()
                    !== count($servicioIds)
                ) {
                    throw ValidationException::withMessages([
                        'servicios' =>
                        'Uno o más servicios ya no están disponibles.',
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Obtener horarios activos y servicios asociados
            |--------------------------------------------------------------------------
            */
                $horarios = HorarioDisponible::query()
                    ->with([
                        'servicios:id,nombre',
                    ])
                    ->whereIn('id', $horarioIds)
                    ->where('activo', true)
                    ->get([
                        'id',
                        'fecha',
                        'hora_inicio',
                        'hora_termino',
                        'activo',
                    ])
                    ->keyBy('id');

                if (
                    $horarios->count()
                    !== count($horarioIds)
                ) {
                    throw ValidationException::withMessages([
                        'servicios' =>
                        'Uno o más horarios ya no están disponibles.',
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Validar superposición dentro de la misma reserva
            |--------------------------------------------------------------------------
            | El mismo grupo no puede asistir a dos servicios al mismo tiempo.
            */
                for ($i = 0; $i < count($selecciones); $i++) {
                    for (
                        $j = $i + 1;
                        $j < count($selecciones);
                        $j++
                    ) {
                        $seleccionA = $selecciones[$i];
                        $seleccionB = $selecciones[$j];

                        if (
                            $seleccionA['fecha']
                            !== $seleccionB['fecha']
                        ) {
                            continue;
                        }

                        $horarioA = $horarios->get(
                            (int) $seleccionA['horario_id']
                        );

                        $horarioB = $horarios->get(
                            (int) $seleccionB['horario_id']
                        );

                        $seSuperponen =
                            $horarioA->hora_inicio
                            < $horarioB->hora_termino
                            &&
                            $horarioA->hora_termino
                            > $horarioB->hora_inicio;

                        if ($seSuperponen) {
                            throw ValidationException::withMessages([
                                'servicios' =>
                                'Los servicios seleccionados para el mismo grupo no pueden tener horarios superpuestos.',
                            ]);
                        }
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Validación definitiva de cada selección
            |--------------------------------------------------------------------------
            */
                foreach ($selecciones as $indice => $seleccion) {
                    $servicioId =
                        (int) $seleccion['servicio_id'];

                    $horarioId =
                        (int) $seleccion['horario_id'];

                    $fechaSeleccionada =
                        $seleccion['fecha'];

                    /** @var ServicioExperiencia|null $servicio */
                    $servicio = $servicios->get(
                        $servicioId
                    );

                    /** @var HorarioDisponible|null $horario */
                    $horario = $horarios->get(
                        $horarioId
                    );

                    if (! $servicio || ! $horario) {
                        throw ValidationException::withMessages([
                            "servicios.{$indice}" =>
                            'El servicio o el horario seleccionado ya no está disponible.',
                        ]);
                    }

                    /*
                 * La fecha enviada debe ser la fecha real del horario.
                 */
                    if (
                        $horario->fecha->format('Y-m-d')
                        !== $fechaSeleccionada
                    ) {
                        throw ValidationException::withMessages([
                            "servicios.{$indice}.horario_id" =>
                            'El horario seleccionado no corresponde a la fecha indicada.',
                        ]);
                    }

                    /*
                 * El servicio debe estar asociado a la franja.
                 */
                    $servicioAsociado = $horario
                        ->servicios
                        ->contains(
                            'id',
                            $servicioId
                        );

                    if (! $servicioAsociado) {
                        throw ValidationException::withMessages([
                            "servicios.{$indice}.horario_id" =>
                            "El servicio {$servicio->nombre} no está habilitado en el horario seleccionado.",
                        ]);
                    }

                    /*
                 * Personas existentes en todos los horarios
                 * que se superponen con esta franja.
                 */
                    $personasSuperpuestas = $this->obtenerPersonasSuperpuestas($horario);

                    $cuposGeneralesDisponibles = max($capacidadSimultanea - $personasSuperpuestas, 0);

                    if ($cantidadPersonas > $cuposGeneralesDisponibles) {
                        $horaInicio = substr($horario->hora_inicio, 0, 5);

                        $horaTermino = substr($horario->hora_termino, 0, 5);

                        throw ValidationException::withMessages([
                            'cantidad_asistentes' =>
                            "Entre las {$horaInicio} y las {$horaTermino} solamente quedan {$cuposGeneralesDisponibles} cupos, considerando la capacidad simultánea del parque.",
                        ]);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Calcular subtotales
            |--------------------------------------------------------------------------
            */
                $subtotalGeneral = collect($selecciones)
                    ->sum(function ($seleccion) use (
                        $servicios,
                        $cantidadPersonas
                    ) {
                        $servicio = $servicios->get(
                            (int) $seleccion['servicio_id']
                        );

                        return $this->calcularSubtotalServicio(
                            $servicio,
                            $cantidadPersonas
                        );
                    });

                /*
            |--------------------------------------------------------------------------
            | Fecha principal de la reserva
            |--------------------------------------------------------------------------
            | Si reservas.fecha sigue siendo obligatoria, guardamos la fecha
            | del primer servicio. La fecha específica queda también en el pivot.
            */
                $fechaPrincipal =
                    $selecciones[0]['fecha'];

                /*
            |--------------------------------------------------------------------------
            | Crear reserva
            |--------------------------------------------------------------------------
            */
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
                    $fechaPrincipal,

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

                    'objetivo_visita' =>
                    $datosReserva['objetivo_visita'] ?? null,

                    'subtotal' =>
                    $subtotalGeneral,

                    'descuento' => 0,

                    'total' =>
                    $subtotalGeneral,

                    'estado' =>
                    'PENDIENTE',
                ]);

                /*
            |--------------------------------------------------------------------------
            | Guardar servicios de la reserva
            |--------------------------------------------------------------------------
            */
                foreach ($selecciones as $seleccion) {
                    $servicio = $servicios->get(
                        (int) $seleccion['servicio_id']
                    );

                    $precioUnitario =
                        (float) $servicio->precio;

                    $subtotalServicio =
                        $this->calcularSubtotalServicio(
                            $servicio,
                            $cantidadPersonas
                        );

                    $reserva->servicios()->attach(
                        $servicio->id,
                        [
                            'horario_disponible_id' =>
                            (int) $seleccion['horario_id'],

                            'fecha' =>
                            $seleccion['fecha'],

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
            }, 3);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('reservas.datos')
                ->withErrors(
                    $exception->errors()
                )
                ->withInput();
        }

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
    ) {
        $datos = $request->validate(
            [
                'fecha' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                ],

                'servicio_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'servicios_experiencias',
                        'id'
                    )->where('activo', true),
                ],
            ],
            [
                'fecha.required' =>
                'Debes seleccionar una fecha.',

                'fecha.date' =>
                'La fecha seleccionada no es válida.',

                'fecha.after_or_equal' =>
                'La fecha no puede ser anterior a hoy.',

                'servicio_id.required' =>
                'Debes seleccionar un servicio.',

                'servicio_id.integer' =>
                'El servicio seleccionado no es válido.',

                'servicio_id.exists' =>
                'El servicio seleccionado no existe o está inactivo.',
            ]
        );

        $servicio = ServicioExperiencia::query()
            ->where('activo', true)
            ->findOrFail($datos['servicio_id']);

        $capacidadGeneral = ConfiguracionReserva::query()
            ->value('capacidad_maxima_simultanea');

        if ($capacidadGeneral === null) {
            throw ValidationException::withMessages([
                'servicio_id' =>
                'No se ha configurado la capacidad máxima simultánea del parque.',
            ]);
        }

        $capacidadGeneral = (int) $capacidadGeneral;

        $horarios = HorarioDisponible::query()
            ->whereDate(
                'fecha',
                $datos['fecha']
            )
            ->where('activo', true)
            ->whereHas(
                'servicios',
                function ($query) use ($servicio) {
                    $query->where(
                        'servicios_experiencias.id',
                        $servicio->id
                    );
                }
            )
            ->orderBy('hora_inicio')
            ->get()
            ->map(function ($horario) use (
                $capacidadGeneral
            ) {
                $personasSuperpuestas =
                    $this->obtenerPersonasSuperpuestas(
                        $horario
                    );

                $cuposGenerales = max(
                    $capacidadGeneral
                        - $personasSuperpuestas,
                    0
                );

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

                    'cupos_disponibles' =>
                    $cuposGenerales,

                    'disponible' =>
                    $cuposGenerales > 0,
                ];
            })
            ->values();

        return response()->json([
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

    public function consultarServiciosDisponibles(Request $request)
    {
        $datos = $request->validate([
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ]);

        $fecha = $datos['fecha'];

        $servicios = ServicioExperiencia::query()
            ->select([
                'id',
                'categoria_servicio_id',
                'nombre',
                'duracion_minutos',
                'capacidad_maxima',
                'precio',
                'tipo_cobro',
            ])
            ->with([
                'categoria:id,nombre',
            ])
            ->where('activo', true)
            ->whereHas('horariosDisponibles', function ($query) use ($fecha) {
                $query
                    ->whereDate('horarios_disponibles.fecha', $fecha)
                    ->where('horarios_disponibles.activo', true);
            })
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'ok' => true,
            'servicios' => $servicios,
        ]);
    }

    private function obtenerPersonasSuperpuestas(
        HorarioDisponible $horario
    ): int {
        return (int) DB::table('reserva_servicios')
            ->join(
                'reservas',
                'reservas.id',
                '=',
                'reserva_servicios.reserva_id'
            )
            ->join(
                'horarios_disponibles',
                'horarios_disponibles.id',
                '=',
                'reserva_servicios.horario_disponible_id'
            )
            ->whereDate(
                'horarios_disponibles.fecha',
                $horario->fecha->format('Y-m-d')
            )
            ->where(
                'horarios_disponibles.hora_inicio',
                '<',
                $horario->hora_termino
            )
            ->where(
                'horarios_disponibles.hora_termino',
                '>',
                $horario->hora_inicio
            )
            ->whereNotIn('reservas.estado', [
                'CANCELADA',
                'RECHAZADA',
            ])
            ->sum('reserva_servicios.cantidad_personas');
    }
}
