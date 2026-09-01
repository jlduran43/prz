<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\ComunaController;
use App\Http\Controllers\ConfiguracionCotizacionController;
use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\HorarioDisponibleController;
use App\Http\Controllers\KhipuController;
use App\Http\Controllers\KhipuWebhookController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReservaWizardController;
use App\Http\Controllers\ServicioExperienciaController;
use App\Http\Controllers\TipoClienteController;
use App\Mail\ReservaConfirmadaMail;
use App\Models\Reserva;
use App\Services\ReservaQrService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| PANEL ADMINISTRATIVO
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Convenios
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'convenios/{convenio}/activar',
        [ConvenioController::class, 'activar']
    )->name('convenios.activar');

    Route::resource(
        'convenios',
        ConvenioController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Tipos de cliente
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'tipos-cliente/{tipoCliente}/cambiar-estado',
        [TipoClienteController::class, 'cambiarEstado']
    )->name('tipos-cliente.cambiar-estado');

    Route::resource(
        'tipos-cliente',
        TipoClienteController::class
    )
        ->parameters([
            'tipos-cliente' => 'tipoCliente',
        ])
        ->except('destroy');


    /*
    |--------------------------------------------------------------------------
    | Regiones
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'regiones/{region}/cambiar-estado',
        [RegionController::class, 'cambiarEstado']
    )->name('regiones.cambiar-estado');

    Route::resource(
        'regiones',
        RegionController::class
    )
        ->parameters([
            'regiones' => 'region',
        ])
        ->except('destroy');


    /*
    |--------------------------------------------------------------------------
    | Comunas
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'comunas/{comuna}/cambiar-estado',
        [ComunaController::class, 'cambiarEstado']
    )->name('comunas.cambiar-estado');

    Route::resource(
        'comunas',
        ComunaController::class
    )
        ->parameters([
            'comunas' => 'comuna',
        ])
        ->except('destroy');


    /*
    |--------------------------------------------------------------------------
    | Categorías de servicio
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'categorias-servicio/{categoria}/activar',
        [CategoriaServicioController::class, 'activar']
    )->name('categorias-servicio.activar');

    Route::resource(
        'categorias-servicio',
        CategoriaServicioController::class
    )
        ->parameters([
            'categorias-servicio' => 'categoria',
        ]);


    /*
    |--------------------------------------------------------------------------
    | Horarios disponibles
    |--------------------------------------------------------------------------
    */

    Route::prefix('horarios-disponibles')
        ->name('horarios-disponibles.')
        ->controller(HorarioDisponibleController::class)
        ->group(function () {

            Route::get('generar', 'generar')
                ->name('generar');

            Route::post('recurrentes', 'guardarRecurrentes')
                ->name('recurrentes.guardar');

            Route::patch('{horario}/activar', 'activar')
                ->name('activar');
        });

    Route::get(
        'horarios-disponibles/calendario/eventos',
        [HorarioDisponibleController::class, 'eventosCalendario']
    )->name('horarios-disponibles.calendario.eventos');

    Route::resource(
        'horarios-disponibles',
        HorarioDisponibleController::class
    )
        ->parameters([
            'horarios-disponibles' => 'horario',
        ]);


    /*
    |--------------------------------------------------------------------------
    | Servicios / Experiencias
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'servicios-experiencias/{servicio}/activar',
        [ServicioExperienciaController::class, 'activar']
    )->name('servicios-experiencias.activar');

    Route::resource(
        'servicios-experiencias',
        ServicioExperienciaController::class
    )
        ->parameters([
            'servicios-experiencias' => 'servicio',
        ]);


    /*
    |--------------------------------------------------------------------------
    | Configuración de cotizaciones
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'configuraciones-cotizacion/{configuracion}/activar',
        [ConfiguracionCotizacionController::class, 'activar']
    )->name('configuraciones-cotizacion.activar');

    Route::resource(
        'configuraciones-cotizacion',
        ConfiguracionCotizacionController::class
    )
        ->parameters([
            'configuraciones-cotizacion' => 'configuracion',
        ]);


    /*
    |--------------------------------------------------------------------------
    | Cotizaciones - Administración
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cotizaciones',
        [CotizacionController::class, 'index']
    )->name('cotizaciones.index');

    Route::prefix('admin/cotizaciones')
        ->name('admin.cotizaciones.')
        ->controller(CotizacionController::class)
        ->group(function () {

            Route::get('{cotizacion}', 'show')
                ->name('show');

            Route::patch('{cotizacion}/anular', 'anularAdmin')
                ->name('anular');

            Route::get('{cotizacion}/pdf', 'descargarPdf')
                ->name('pdf');

            Route::post('{cotizacion}/reenviar-correo', 'reenviarCorreo')
                ->name('reenviar-correo');
        });


    /*
    |--------------------------------------------------------------------------
    | Reservas - Administración
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reservas',
        [ReservaWizardController::class, 'index']
    )->name('reservas.index');

    Route::get(
        '/reservas/{reserva}',
        [ReservaWizardController::class, 'show']
    )
        ->whereNumber('reserva')
        ->name('reservas.show');
});


/*
|--------------------------------------------------------------------------
| WIZARD DE RESERVAS
|--------------------------------------------------------------------------
*/

Route::prefix('reservas')
    ->name('reservas.')
    ->controller(ReservaWizardController::class)
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Paso 0 - Operación
        |--------------------------------------------------------------------------
        */

        Route::get('crear', 'operacion')
            ->name('operacion');

        Route::post('crear/operacion', 'guardarOperacion')
            ->name('operacion.guardar');


        /*
        |--------------------------------------------------------------------------
        | Paso 1 - Cliente
        |--------------------------------------------------------------------------
        */

        Route::get('crear/cliente', 'cliente')
            ->name('cliente');

        Route::post('crear/cliente', 'guardarCliente')
            ->name('cliente.guardar');


        /*
        |--------------------------------------------------------------------------
        | Paso 2 - Datos de reserva
        |--------------------------------------------------------------------------
        */

        Route::get('crear/datos-reserva', 'reserva')
            ->name('datos');

        Route::post('crear/datos-reserva', 'guardarReserva')
            ->name('datos.guardar');

        Route::post('validar-convenio', 'validarConvenio')
            ->name('validar-convenio');


        /*
        |--------------------------------------------------------------------------
        | Paso 3 - Confirmación
        |--------------------------------------------------------------------------
        */

        Route::get('crear/confirmacion', 'confirmacion')
            ->name('confirmacion');


        /*
        |--------------------------------------------------------------------------
        | Paso 4 - Finalizar / Pago
        |--------------------------------------------------------------------------
        */

        Route::post('crear/finalizar', 'finalizar')
            ->name('finalizar');

        Route::get('{reserva}/pago', 'pago')
            ->name('pago');

        Route::get('{reserva}/resultado', 'resultado')
            ->name('resultado');


        /*
        |--------------------------------------------------------------------------
        | Webpay - compatibilidad con flujo existente
        |--------------------------------------------------------------------------
        */

        Route::post('{reserva}/pago/webpay', 'iniciarPagoWebpay')
            ->name('pago.webpay');


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        Route::get(
            'servicios-disponibles',
            'consultarServiciosDisponibles'
        )->name('servicios-disponibles');

        Route::get(
            'consultar-horarios',
            'consultarHorarios'
        )->name('consultar-horarios');

        Route::get(
            'comunas-por-region/{region}',
            'comunasPorRegion'
        )->name('comunas-por-region');
    });


/*
|--------------------------------------------------------------------------
| NUEVA OPERACIÓN
|--------------------------------------------------------------------------
*/

Route::get(
    '/reservas/nueva',
    [ReservaWizardController::class, 'nuevaOperacion']
)->name('reservas.nueva');


/*
|--------------------------------------------------------------------------
| COTIZACIONES PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::post(
    '/cotizaciones/generar',
    [ReservaWizardController::class, 'generarCotizacion']
)->name('cotizaciones.generar');


Route::prefix('cotizaciones')
    ->name('cotizaciones.')
    ->controller(CotizacionController::class)
    ->group(function () {

        Route::get(
            '{cotizacion}/resultado/{token}',
            'showPublico'
        )->name('resultado');

        Route::patch(
            '{cotizacion}/anular/{token}',
            'anularPublico'
        )->name('anular');

        Route::get(
            '{cotizacion}/pdf/{token}',
            'descargarPdfPublico'
        )->name('pdf.publico');

        Route::get(
            '{cotizacion}/convertir/{token}',
            'convertirEnReserva'
        )->name('convertir');

        Route::post(
            '{cotizacion}/reenviar-correo',
            'reenviarCorreo'
        )->name('reenviar-correo');
    });


/*
|--------------------------------------------------------------------------
| CONVERSIÓN DESDE CORREO
|--------------------------------------------------------------------------
*/

Route::get(
    '/cotizacion/{cotizacion}/convertir-reserva',
    [CotizacionController::class, 'convertirDesdeCorreo']
)
    ->middleware('signed')
    ->name('cotizaciones.publica.convertir');


/*
|--------------------------------------------------------------------------
| PAGOS - KHIPU
|--------------------------------------------------------------------------
*/

Route::post(
    '/reservas/{reserva}/pago/khipu',
    [KhipuController::class, 'iniciar']
)->name('reservas.khipu.iniciar');

Route::get(
    '/reservas/{reserva}/pago/khipu/retorno',
    [KhipuController::class, 'retorno']
)->name('reservas.khipu.retorno');

Route::get(
    '/reservas/{reserva}/pago/khipu/cancelado',
    [KhipuController::class, 'cancelar']
)->name('reservas.khipu.cancelar');

Route::post(
    '/webhooks/khipu',
    [KhipuWebhookController::class, 'recibir']
)->name('webhooks.khipu');


/*
|--------------------------------------------------------------------------
| PAGOS - WEBPAY
|--------------------------------------------------------------------------
*/

Route::post(
    '/reservas/{reserva}/pago/procesar',
    [PagoController::class, 'procesar']
)->name('reservas.pago.procesar');

Route::get(
    '/reservas/{reserva}/pago/webpay/retorno',
    [PagoController::class, 'retornoWebpay']
)->name('reservas.pago.webpay.retorno');


/*
|--------------------------------------------------------------------------
| VERIFICACIÓN PÚBLICA DE RESERVAS / QR
|--------------------------------------------------------------------------
|
| Esta ruta DEBE permanecer fuera del middleware auth.
| El visitante debe poder escanear el QR sin iniciar sesión.
|
*/

Route::get(
    '/reservas/verificar/{token}',
    [ReservaWizardController::class, 'verificar']
)->name('reservas.verificar');

Route::post(
    '/reservas/verificar/{token}/validar',
    [ReservaWizardController::class, 'validarIngreso']
)
    ->middleware('auth')
    ->name('reservas.validar-ingreso');
/*
|--------------------------------------------------------------------------
| COMPROBANTES DE RESERVA
|--------------------------------------------------------------------------
*/

Route::get(
    '/reservas/{reserva}/comprobante',
    [ReservaWizardController::class, 'descargarComprobante']
)->name('reservas.comprobante');

Route::get(
    '/reservas/{reserva}/comprobante/ver',
    [ReservaWizardController::class, 'verComprobante']
)->name('reservas.comprobante.ver');


/*
|--------------------------------------------------------------------------
| RUTAS DE PRUEBA - SOLO DESARROLLO
|--------------------------------------------------------------------------
|
| Para evitar exponer herramientas de prueba en producción,
| estas rutas únicamente se registran cuando APP_ENV=local.
|
*/

if (app()->environment('local')) {

    Route::get('/prueba-qr/{id}', function (int $id, ReservaQrService $qrService) {

        $reserva = Reserva::findOrFail($id);

        $ruta = $qrService->generar($reserva);

        return [
            'qr' => $ruta,
            'token' => $reserva->fresh()->token_verificacion,
            'verificacion' => route(
                'reservas.verificar',
                [
                    'token' => $reserva->fresh()->token_verificacion,
                ]
            ),
        ];
    });

    Route::get('/prueba-correo-reserva/{id}', function (int $id) {

        $reserva = Reserva::findOrFail($id);

        Mail::to(
            $reserva->email
        )->send(
            new ReservaConfirmadaMail(
                $reserva
            )
        );

        return 'Correo enviado';
    });
}
