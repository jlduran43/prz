<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use App\Models\ConfiguracionReserva;
use App\Models\Convenio;
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
use Carbon\Carbon;

class ReservaWizardController extends Controller
{

    public function operacion()
    {
        session()->forget('reserva');

        return view('reservas.paso0-operacion', [
            'paso' => 0,
        ]);
    }

    public function guardarOperacion(Request $request)
    {
        $datos = $request->validate([
            'tipo_operacion' => [
                'required',
                'in:COTIZACION,RESERVA',
            ],
        ]);

        session([
            'reserva.tipo_operacion'
            => $datos['tipo_operacion'],
        ]);

        return redirect()
            ->route('reservas.cliente');
    }

    public function cliente()
    {

        if (! session()->has('reserva.tipo_operacion')) {
            return redirect()
                ->route('reservas.operacion');
        }

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
        /*
    |--------------------------------------------------------------------------
    | 1. Datos comunes
    |--------------------------------------------------------------------------
    */

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


        /*
    |--------------------------------------------------------------------------
    | 2. Obtener tipo de cliente
    |--------------------------------------------------------------------------
    */

        $tipoCliente = TipoCliente::findOrFail(
            $datos['tipo_cliente_id']
        );

        $codigoTipoCliente = $tipoCliente->codigo;

        /*
    |--------------------------------------------------------------------------
    | 3. Persona natural
    |--------------------------------------------------------------------------
    */

        if ($codigoTipoCliente === 'PERSONA') {

            $datosPersona = $request->validate([
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

            $datos = array_merge(
                $datos,
                $datosPersona
            );
        }


        /*
    |--------------------------------------------------------------------------
    | 4. Establecimiento educacional
    |--------------------------------------------------------------------------
    */
        $tiposOrganizacion = [
            'ESTABLECIMIENTO_EDUCACIONAL',
            'TOUR_OPERADOR_AGENCIA_VIAJES',
            'GRUPO_ADULTOS_MAYORES',
        ];

        if (in_array($codigoTipoCliente, $tiposOrganizacion, true)) {

            $datosEntidad = $request->validate([
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
                ],

                'rut_encargado' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
            ]);

            $datos = array_merge(
                $datos,
                $datosEntidad
            );
        }


        /*
    |--------------------------------------------------------------------------
    | 5. Tour operador / Agencia de viajes
    |--------------------------------------------------------------------------
    */

        if (
            $codigoTipoCliente ===
            'TOUR_OPERADOR_AGENCIA_VIAJES'
        ) {

            $datosEntidad = $request->validate([
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
                ],

                'rut_encargado' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
            ]);

            $datos = array_merge(
                $datos,
                $datosEntidad
            );
        }


        /*
    |--------------------------------------------------------------------------
    | 6. Guardar código del tipo
    |--------------------------------------------------------------------------
    */

        $datos['codigo_tipo_cliente'] =
            $codigoTipoCliente;


        /*
    |--------------------------------------------------------------------------
    | 7. Validar que comuna pertenezca a región
    |--------------------------------------------------------------------------
    */

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


        /*
    |--------------------------------------------------------------------------
    | 8. Guardar en sesión
    |--------------------------------------------------------------------------
    */

        session([
            'reserva.cliente' => $datos,
        ]);


        /*
    |--------------------------------------------------------------------------
    | 9. Paso siguiente
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('reservas.datos');
    }

    public function guardarReserva(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | 1. Obtener modalidad y cliente desde sesión
    |--------------------------------------------------------------------------
    */

        $tipoOperacion = session('reserva.tipo_operacion');

        if (! in_array($tipoOperacion, ['COTIZACION', 'RESERVA'], true)) {
            return redirect()
                ->route('reservas.operacion')
                ->with('error', 'Primero debes seleccionar qué deseas realizar.');
        }

        $datosCliente = session('reserva.cliente', []);

        if (empty($datosCliente)) {
            return redirect()
                ->route('reservas.cliente')
                ->with('error', 'Primero debes completar los datos del cliente.');
        }

        $codigoTipoCliente =
            $datosCliente['codigo_tipo_cliente']
            ?? ($datosCliente['tipo_cliente_codigo'] ?? null);

        $esEstablecimiento =
            $codigoTipoCliente === 'ESTABLECIMIENTO_EDUCACIONAL';

        $esCotizacion =
            $tipoOperacion === 'COTIZACION';

        $esReserva =
            $tipoOperacion === 'RESERVA';


        /*
    |--------------------------------------------------------------------------
    | 2. Reglas comunes
    |--------------------------------------------------------------------------
    */

        $reglas = [

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
                )->where(function ($query) {
                    $query->where('activo', 1);
                }),
            ],

            'cantidad_asistentes' => [
                'required',
                'integer',
                'min:1',
            ],

            'objetivo_visita' => [
                'nullable',
                'string',
                'max:500',
            ],

            'codigo_convenio' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];


        /*
    |--------------------------------------------------------------------------
    | 3. Datos específicos para establecimiento educacional
    |--------------------------------------------------------------------------
    */

        if ($esEstablecimiento) {

            $reglas['cantidad_alumnos'] = [
                'required',
                'integer',
                'min:1',
            ];

            $reglas['cantidad_profesores'] = [
                'required',
                'integer',
                'min:0',
            ];

            $reglas['nivel_educacional'] = [
                'required',
                'string',
                Rule::in([
                    'PARVULARIA',
                    'BASICA',
                    'MEDIA',
                    'ESPECIAL',
                    'SUPERIOR',
                    'ADULTOS',
                    'OTRO',
                ]),
            ];

            $reglas['curso'] = [
                'required',
                'string',
                'max:100',
            ];
        }


        /*
    |--------------------------------------------------------------------------
    | 4. Solo una RESERVA necesita fecha y horario
    |--------------------------------------------------------------------------
    */

        if ($esReserva) {

            $reglas['fecha_reserva'] = [
                'required',
                'date',
                'after_or_equal:today',
            ];

            $reglas['servicios.*.fecha'] = [
                'required',
                'date',
                'after_or_equal:today',
            ];

            $reglas['servicios.*.horario_id'] = [
                'required',
                'integer',
                'exists:horarios_disponibles,id',
            ];
        }


        /*
    |--------------------------------------------------------------------------
    | 5. Mensajes personalizados
    |--------------------------------------------------------------------------
    */

        $mensajes = [

            'servicios.required' =>
            'Debes seleccionar al menos un servicio.',

            'servicios.min' =>
            'Debes seleccionar al menos un servicio.',

            'servicios.max' =>
            'Solo puedes seleccionar un máximo de dos servicios.',

            'servicios.*.servicio_id.required' =>
            'Debes seleccionar un servicio.',

            'servicios.*.servicio_id.distinct' =>
            'No puedes seleccionar el mismo servicio más de una vez.',

            'cantidad_asistentes.required' =>
            'Debes indicar la cantidad de asistentes.',

            'cantidad_asistentes.min' =>
            'La cantidad de asistentes debe ser al menos 1.',

            'fecha_reserva.required' =>
            'Debes seleccionar la fecha de la visita.',

            'fecha_reserva.after_or_equal' =>
            'La fecha de la visita no puede ser anterior a hoy.',

            'servicios.*.fecha.required' =>
            'Debes indicar la fecha del servicio.',

            'servicios.*.horario_id.required' =>
            'Debes seleccionar un horario para cada servicio.',

            'servicios.*.horario_id.exists' =>
            'Uno de los horarios seleccionados no es válido.',

            'cantidad_alumnos.required' =>
            'Debes indicar la cantidad de alumnos.',

            'cantidad_profesores.required' =>
            'Debes indicar la cantidad de profesores.',

            'nivel_educacional.required' =>
            'Debes seleccionar el nivel educacional.',

            'curso.required' =>
            'Debes indicar el curso.',
        ];


        /*
    |--------------------------------------------------------------------------
    | 6. Validar
    |--------------------------------------------------------------------------
    */

        $datos = $request->validate(
            $reglas,
            $mensajes
        );


        /*
    |--------------------------------------------------------------------------
    | 7. Recalcular asistentes para establecimientos
    |--------------------------------------------------------------------------
    |
    | No confiamos únicamente en el hidden cantidad_asistentes.
    | Lo calculamos nuevamente desde Laravel.
    |
    */

        if ($esEstablecimiento) {

            $cantidadAlumnos =
                (int) $datos['cantidad_alumnos'];

            $cantidadProfesores =
                (int) $datos['cantidad_profesores'];

            $datos['cantidad_asistentes'] =
                $cantidadAlumnos + $cantidadProfesores;
        }


        /*
    |--------------------------------------------------------------------------
    | 8. Limpiar servicios según modalidad
    |--------------------------------------------------------------------------
    */

        $serviciosLimpios = [];

        foreach ($datos['servicios'] as $servicio) {

            /*
         * Cotización:
         * solamente necesitamos saber qué servicio
         * fue seleccionado.
         */
            if ($esCotizacion) {

                $serviciosLimpios[] = [
                    'servicio_id' =>
                    (int) $servicio['servicio_id'],
                ];

                continue;
            }


            /*
         * Reserva:
         * necesitamos servicio + fecha + horario.
         */
            $serviciosLimpios[] = [

                'servicio_id' =>
                (int) $servicio['servicio_id'],

                'fecha' =>
                $servicio['fecha'],

                'horario_id' =>
                (int) $servicio['horario_id'],
            ];
        }

        $datos['servicios'] =
            $serviciosLimpios;


        /*
    |--------------------------------------------------------------------------
    | 9. En cotización no guardar fecha
    |--------------------------------------------------------------------------
    */

        if ($esCotizacion) {

            unset(
                $datos['fecha_reserva']
            );
        }


        /*
    |--------------------------------------------------------------------------
    | 10. Guardar Paso 2 en sesión
    |--------------------------------------------------------------------------
    */

        session([
            'reserva.datos' => $datos,
        ]);


        /*
    |--------------------------------------------------------------------------
    | 11. Continuar a confirmación
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('reservas.confirmacion');
    }

    public function confirmacion()
    {
        /*
    |--------------------------------------------------------------------------
    | 1. Validar sesión
    |--------------------------------------------------------------------------
    */

        $tipoOperacion = session('reserva.tipo_operacion');
        $datosCliente = session('reserva.cliente');
        $datosReserva = session('reserva.datos');

        if (! $tipoOperacion) {
            return redirect()
                ->route('reservas.operacion');
        }

        if (! $datosCliente) {
            return redirect()
                ->route('reservas.cliente');
        }

        if (! $datosReserva) {
            return redirect()
                ->route('reservas.datos');
        }

        $esCotizacion =
            $tipoOperacion === 'COTIZACION';

        $esReserva =
            $tipoOperacion === 'RESERVA';


        /*
|--------------------------------------------------------------------------
| Datos descriptivos del cliente
|--------------------------------------------------------------------------
*/

        if (!empty($datosCliente['tipo_cliente_id'])) {

            $tipoCliente = DB::table('tipos_cliente')
                ->where('id', $datosCliente['tipo_cliente_id'])
                ->first();
        }

        if (!empty($datosCliente['region_id'])) {

            $regionNombre = DB::table('regiones')
                ->where('id', $datosCliente['region_id'])
                ->value('nombre');
        }

        if (!empty($datosCliente['comuna_id'])) {

            $comunaNombre = DB::table('comunas')
                ->where('id', $datosCliente['comuna_id'])
                ->value('nombre');
        }

        if (! $tipoOperacion) {
            return redirect()
                ->route('reservas.operacion');
        }

        if (! $datosCliente) {
            return redirect()
                ->route('reservas.cliente');
        }

        if (! $datosReserva) {
            return redirect()
                ->route('reservas.datos');
        }

        $esCotizacion =
            $tipoOperacion === 'COTIZACION';

        $esReserva =
            $tipoOperacion === 'RESERVA';


        /*
    |--------------------------------------------------------------------------
    | 2. Obtener servicios seleccionados
    |--------------------------------------------------------------------------
    */

        $selecciones =
            $datosReserva['servicios'] ?? [];

        $idsServicios =
            collect($selecciones)
            ->pluck('servicio_id')
            ->filter()
            ->values();


        $servicios =
            ServicioExperiencia::query()
            ->with('categoria')
            ->whereIn('id', $idsServicios)
            ->get()
            ->keyBy('id');


        /*
    |--------------------------------------------------------------------------
    | 3. COTIZACIÓN
    |--------------------------------------------------------------------------
    |
    | No existen fecha ni horario.
    | Solamente preparamos servicios y precios.
    |--------------------------------------------------------------------------
    */

        if ($esCotizacion) {

            $cantidadPersonas =
                (int) ($datosReserva['cantidad_asistentes'] ?? 0);

            $detalleServicios =
                collect($selecciones)
                ->map(function ($seleccion) use (
                    $servicios,
                    $cantidadPersonas
                ) {

                    $servicioId =
                        $seleccion['servicio_id'];

                    $servicio =
                        $servicios->get($servicioId);

                    if (! $servicio) {
                        return null;
                    }


                    $precio =
                        (float) $servicio->precio;

                    $tipoCobro =
                        $servicio->tipo_cobro;


                    $subtotal =
                        $tipoCobro === 'POR_PERSONA'
                        ? $precio * $cantidadPersonas
                        : $precio;


                    return [

                        'servicio_id' =>
                        $servicio->id,

                        'servicio' =>
                        $servicio,

                        'precio_unitario' =>
                        $precio,

                        'tipo_cobro' =>
                        $tipoCobro,

                        'cantidad_personas' =>
                        $cantidadPersonas,

                        'subtotal' =>
                        $subtotal,

                        /*
                         * En cotización no existen:
                         *
                         * horario_id
                         * horario
                         * fecha
                         */
                    ];
                })
                ->filter()
                ->values();


            $subtotalGeneral = (float) $detalleServicios->sum('subtotal');


            $convenioAplicado = session('reserva.convenio');


            $resumen = $this->calcularResumen(
                $subtotalGeneral,
                $convenioAplicado
            );


            return view(
                'reservas.paso3-confirmacion',
                [
                    'paso' => 3,

                    'tipoOperacion' => $tipoOperacion,

                    'esCotizacion' => true,

                    'esReserva' => false,

                    'datosCliente' => $datosCliente,

                    'cliente' => $datosCliente,

                    'datosReserva' => $datosReserva,

                    'reserva' => $datosReserva,

                    'tipoCliente' => $tipoCliente,

                    'regionNombre' => $regionNombre,

                    'comunaNombre' => $comunaNombre,

                    'detallesServicios' => $detalleServicios,

                    'convenio' => $convenioAplicado,

                    'subtotalGeneral' => $resumen['subtotal'],

                    'porcentajeDescuento' => $resumen['porcentaje_descuento'],

                    'descuentoTotal' => $resumen['descuento'],

                    'total' =>
                    $resumen['total'],
                ]
            );
        }


        /*
    |--------------------------------------------------------------------------
    | 4. RESERVA
    |--------------------------------------------------------------------------
    |
    | Aquí sí necesitamos fecha y horario.
    |--------------------------------------------------------------------------
    */

        $idsHorarios =
            collect($selecciones)
            ->pluck('horario_id')
            ->filter()
            ->values();


        $horarios =
            HorarioDisponible::query()
            ->whereIn('id', $idsHorarios)
            ->get()
            ->keyBy('id');


        $cantidadPersonas =
            (int) ($datosReserva['cantidad_asistentes'] ?? 0);


        $detalleServicios =
            collect($selecciones)
            ->map(function ($seleccion) use (
                $servicios,
                $horarios,
                $cantidadPersonas
            ) {

                $servicioId =
                    $seleccion['servicio_id'];

                $horarioId =
                    $seleccion['horario_id'];

                $fecha =
                    $seleccion['fecha'];


                $servicio =
                    $servicios->get($servicioId);

                $horario =
                    $horarios->get($horarioId);


                if (! $servicio || ! $horario) {
                    return null;
                }


                $precio =
                    (float) $servicio->precio;

                $tipoCobro =
                    $servicio->tipo_cobro;


                $subtotal =
                    $tipoCobro === 'POR_PERSONA'
                    ? $precio * $cantidadPersonas
                    : $precio;


                return [

                    'servicio_id' =>
                    $servicio->id,

                    'servicio' =>
                    $servicio,

                    'horario_id' =>
                    $horario->id,

                    'horario' =>
                    $horario,

                    'fecha' =>
                    $fecha,

                    'precio_unitario' =>
                    $precio,

                    'tipo_cobro' =>
                    $tipoCobro,

                    'cantidad_personas' =>
                    $cantidadPersonas,

                    'subtotal' =>
                    $subtotal,
                ];
            })
            ->filter()
            ->values();


        $total =
            $detalleServicios->sum('subtotal');


        return view(
            'reservas.paso3-confirmacion',
            [
                'paso' => 3,

                'tipoOperacion' => $tipoOperacion,

                'esCotizacion' => false,

                'esReserva' => true,

                'datosCliente' => $datosCliente,

                'cliente' => $datosCliente,

                'datosReserva' => $datosReserva,

                'reserva' => $datosReserva,

                'tipoCliente' => $tipoCliente,

                'regionNombre' => $regionNombre,

                'comunaNombre' => $comunaNombre,

                'detallesServicios' => $detalleServicios,

                'total' => $total,
            ]
        );
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
                         * Comprobar nuevamente que el horario
                            * todavía no haya comenzado.
                    */
                    $inicioHorario = Carbon::parse(
                        $horario->fecha->format('Y-m-d')
                            . ' '
                            . $horario->hora_inicio
                    );

                    if ($inicioHorario->lessThanOrEqualTo(now())) {
                        throw ValidationException::withMessages([
                            "servicios.{$indice}.horario_id" =>
                            "El horario de {$horario->hora_inicio} ya comenzó. Selecciona otro horario.",
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

                $convenioSesion =
                    session('reserva.convenio');

                $convenio = null;

                if ($convenioSesion) {

                    /*
                        * Volvemos a validar el convenio antes
                        * de guardar definitivamente la reserva.
                    */
                    $convenio =
                        $this->obtenerConvenioValido(
                            $convenioSesion['codigo'],
                            $datosCliente
                        );
                }


                /*
                    |--------------------------------------------------------------------------
                    | Calcular subtotal, descuento y total
                    |--------------------------------------------------------------------------
                */

                $resumen =
                    $this->calcularResumen(
                        $subtotalGeneral,
                        $convenio
                            ? [
                                'porcentaje_descuento' =>
                                (float)
                                $convenio->porcentaje_descuento,
                            ]
                            : null
                    );




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
                    'tipo_cliente_id' => $datosCliente['tipo_cliente_id'],

                    'nombres' => $datosCliente['nombres'] ?? null,

                    'apellidos' => $datosCliente['apellidos'] ?? null,

                    'rut_persona' => $datosCliente['rut_persona'] ?? null,

                    'nombre_entidad' => $datosCliente['nombre_entidad'] ?? null,

                    'rut_entidad' => $datosCliente['rut_entidad'] ?? null,

                    'nombre_encargado' => $datosCliente['nombre_encargado'] ?? null,

                    'rut_encargado' => $datosCliente['rut_encargado'] ?? null,

                    'email' => $datosCliente['email'],

                    'telefono' => $datosCliente['telefono'],

                    'region_id' => $datosCliente['region_id'] ?? null,

                    'comuna_id' => $datosCliente['comuna_id'] ?? null,

                    'fecha' => $fechaPrincipal,

                    'cantidad_asistentes' => $cantidadPersonas,

                    'cantidad_alumnos' => $datosReserva['cantidad_alumnos'] ?? null,

                    'cantidad_profesores' => $datosReserva['cantidad_profesores'] ?? null,

                    'nivel_educacional' => $datosReserva['nivel_educacional'] ?? null,

                    'curso' => $datosReserva['curso'] ?? null,

                    'objetivo_visita' => $datosReserva['objetivo_visita'] ?? null,

                    'convenio_id' => $convenio?->id,

                    'codigo_convenio' => $convenio?->codigo,

                    'nombre_convenio' => $convenio?->nombre,

                    'porcentaje_descuento' => $resumen['porcentaje_descuento'],

                    'subtotal' => $resumen['subtotal'],

                    'descuento' => $resumen['descuento'],

                    'total' => $resumen['total'],

                    'estado' => 'PENDIENTE',
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

        if (! session()->has('reserva.tipo_operacion')) {
            return redirect()
                ->route('reservas.operacion');
        }

        $tipoOperacion = session('reserva.tipo_operacion');

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
            'tipoOperacion' => $tipoOperacion,
            'convenioAplicado' => session('reserva.convenio'),
        ]);
    }

    public function consultarHorarios(Request $request)
    {
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

        $fechaSeleccionada = Carbon::parse(
            $datos['fecha']
        )->startOfDay();

        $consultaHorarios = HorarioDisponible::query()
            ->whereDate(
                'fecha',
                $fechaSeleccionada->format('Y-m-d')
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
            );

        /*
     * Si la fecha seleccionada es hoy,
     * ocultar horarios que ya comenzaron.
     */
        if ($fechaSeleccionada->isToday()) {
            $consultaHorarios->where(
                'hora_inicio',
                '>',
                now()->format('H:i:s')
            );
        }

        $horarios = $consultaHorarios
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

        $tipoOperacion = $request->input('tipo_operacion');

        /*
    |--------------------------------------------------------------------------
    | COTIZACIÓN
    |--------------------------------------------------------------------------
    |
    | Para cotizar solamente necesitamos los servicios activos.
    | No revisamos fechas, horarios ni cupos.
    |
    */

        if ($tipoOperacion === 'COTIZACION') {

            $servicios = DB::table('servicios_experiencias')
                ->leftJoin(
                    'categorias_servicio',
                    'categorias_servicio.id',
                    '=',
                    'servicios_experiencias.categoria_servicio_id'
                )
                ->where(
                    'servicios_experiencias.activo',
                    true
                )
                ->orderBy(
                    'servicios_experiencias.nombre'
                )
                ->select([
                    'servicios_experiencias.id',
                    'servicios_experiencias.nombre',
                    'servicios_experiencias.descripcion',
                    'servicios_experiencias.duracion_minutos',
                    'servicios_experiencias.capacidad_minima',
                    'servicios_experiencias.capacidad_maxima',
                    'servicios_experiencias.precio',
                    'servicios_experiencias.tipo_cobro',

                    'categorias_servicio.id as categoria_id',
                    'categorias_servicio.nombre as categoria_nombre',
                ])
                ->get()
                ->map(function ($servicio) {

                    return [
                        'id' => $servicio->id,

                        'nombre' =>
                        $servicio->nombre,

                        'descripcion' =>
                        $servicio->descripcion,

                        'duracion_minutos' =>
                        $servicio->duracion_minutos,

                        'capacidad_minima' =>
                        $servicio->capacidad_minima,

                        'capacidad_maxima' =>
                        $servicio->capacidad_maxima,

                        'precio' =>
                        $servicio->precio,

                        'tipo_cobro' =>
                        $servicio->tipo_cobro,

                        /*
                     * Tu JavaScript espera:
                     *
                     * servicio.categoria.nombre
                     */
                        'categoria' => [
                            'id' =>
                            $servicio->categoria_id,

                            'nombre' =>
                            $servicio->categoria_nombre,
                        ],
                    ];
                })
                ->values();

            return response()->json([
                'servicios' => $servicios,
            ]);
        }


        /*
    |--------------------------------------------------------------------------
    | RESERVA
    |--------------------------------------------------------------------------
    */

        $datos = $request->validate([
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'cantidad_personas' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $fecha = $datos['fecha'];

        $datos = $request->validate([
            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ]);

        $fecha = Carbon::parse(
            $datos['fecha']
        )->startOfDay();

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
            ->whereHas(
                'horariosDisponibles',
                function ($query) use ($fecha) {
                    $query
                        ->whereDate(
                            'horarios_disponibles.fecha',
                            $fecha->format('Y-m-d')
                        )
                        ->where(
                            'horarios_disponibles.activo',
                            true
                        );

                    /*
                 * Para hoy, el servicio debe tener al menos
                 * un horario que todavía no haya comenzado.
                 */
                    if ($fecha->isToday()) {
                        $query->where(
                            'horarios_disponibles.hora_inicio',
                            '>',
                            now()->format('H:i:s')
                        );
                    }
                }
            )
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

    private function normalizarRutConvenio(?string $rut): string
    {

        return mb_strtoupper(
            preg_replace(
                '/[^0-9kK]/',
                '',
                (string) $rut
            )
        );
    }

    private function obtenerConvenioValido(string $codigo, array $datosCliente): Convenio
    {

        /*
    |--------------------------------------------------------------------------
    | El convenio solamente se puede utilizar
    | si existe una entidad con RUT.
    |--------------------------------------------------------------------------
    */

        $rutEntidad =
            $datosCliente['rut_entidad'] ?? null;

        if (! $rutEntidad) {

            throw ValidationException::withMessages([
                'codigo_convenio' =>
                'Este cliente no posee un RUT de entidad para validar el convenio.',
            ]);
        }


        $codigo =
            mb_strtoupper(
                trim($codigo)
            );

        $rutNormalizado =
            $this->normalizarRutConvenio(
                $rutEntidad
            );


        $convenio = Convenio::query()

            ->where('codigo', $codigo)

            ->where('activo', true)

            ->whereDate(
                'fecha_inicio',
                '<=',
                now()->toDateString()
            )

            ->where(function ($query) {

                $query
                    ->whereNull('fecha_termino')
                    ->orWhereDate(
                        'fecha_termino',
                        '>=',
                        now()->toDateString()
                    );
            })

            /*
         * Seguridad adicional:
         * el RUT debe estar autorizado.
         */
            ->whereHas(
                'entidades',
                function ($query) use (
                    $rutNormalizado
                ) {

                    $query
                        ->where(
                            'rut_normalizado',
                            $rutNormalizado
                        )
                        ->where(
                            'activo',
                            true
                        );
                }
            )

            ->first();


        if (! $convenio) {

            throw ValidationException::withMessages([
                'codigo_convenio' =>
                'El código no es válido, está vencido o no corresponde al RUT de esta entidad.',
            ]);
        }


        return $convenio;
    }

    public function validarConvenio(Request $request)
    {
        $datos = $request->validate([
            'codigo_convenio' => [
                'required',
                'string',
                'max:50',
            ],
        ]);


        $datosCliente =
            session('reserva.cliente', []);


        if (empty($datosCliente)) {

            return response()->json([
                'ok' => false,
                'mensaje' =>
                'No se encontraron los datos del cliente.',
            ], 422);
        }


        try {

            $convenio =
                $this->obtenerConvenioValido(
                    $datos['codigo_convenio'],
                    $datosCliente
                );


            session([
                'reserva.convenio' => [

                    'id' =>
                    $convenio->id,

                    'codigo' =>
                    $convenio->codigo,

                    'nombre' =>
                    $convenio->nombre,

                    'porcentaje_descuento' =>
                    (float)
                    $convenio->porcentaje_descuento,
                ],
            ]);


            return response()->json([
                'ok' => true,

                'convenio' => [
                    'id' =>
                    $convenio->id,

                    'codigo' =>
                    $convenio->codigo,

                    'nombre' =>
                    $convenio->nombre,

                    'porcentaje_descuento' =>
                    (float)
                    $convenio->porcentaje_descuento,
                ],
            ]);
        } catch (ValidationException $exception) {

            session()->forget(
                'reserva.convenio'
            );

            return response()->json([
                'ok' => false,

                'mensaje' =>
                collect(
                    $exception->errors()
                )->flatten()->first(),
            ], 422);
        }
    }

    private function calcularResumen(
        float $subtotal,
        ?array $convenio
    ): array {

        $porcentaje = 0;


        if ($convenio) {

            $porcentaje =
                (float) (
                    $convenio['porcentaje_descuento']
                    ?? 0
                );
        }


        $descuento =
            round(
                $subtotal
                    * ($porcentaje / 100)
            );


        $total =
            max(
                $subtotal - $descuento,
                0
            );


        return [
            'subtotal' =>
            $subtotal,

            'porcentaje_descuento' =>
            $porcentaje,

            'descuento' =>
            $descuento,

            'total' =>
            $total,
        ];
    }
}
