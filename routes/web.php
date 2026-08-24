<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\ComunaController;
use App\Http\Controllers\ConfiguracionCotizacionController;
use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\HorarioDisponibleController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReservaWizardController;
use App\Http\Controllers\ServicioExperienciaController;
use App\Http\Controllers\TipoClienteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [LoginController::class, 'showLoginForm']
    )->name('login');

    Route::post(
        '/login',
        [LoginController::class, 'login']
    )->name('login.attempt');
});

Route::post(
    '/logout',
    [LoginController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| PANEL ADMINISTRATIVO
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

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
        ->group(function () {

            Route::get(
                'generar',
                [HorarioDisponibleController::class, 'generar']
            )->name('generar');

            Route::post(
                'recurrentes',
                [HorarioDisponibleController::class, 'guardarRecurrentes']
            )->name('recurrentes.guardar');

            Route::patch(
                '{horario}/activar',
                [HorarioDisponibleController::class, 'activar']
            )->name('activar');
        });

    Route::resource(
        'horarios-disponibles',
        HorarioDisponibleController::class
    )
        ->parameters([
            'horarios-disponibles' => 'horario',
        ]);


    /*
    |--------------------------------------------------------------------------
    | Servicios / experiencias
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

    Route::resource(
        'configuraciones-cotizacion',
        ConfiguracionCotizacionController::class
    )
        ->parameters([
            'configuraciones-cotizacion' => 'configuracion',
        ]);

    Route::patch(
        'configuraciones-cotizacion/{configuracion}/activar',
        [ConfiguracionCotizacionController::class, 'activar']
    )->name('configuraciones-cotizacion.activar');


    /*
    |--------------------------------------------------------------------------
    | Cotizaciones - Administración
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin/cotizaciones')
        ->name('admin.cotizaciones.')
        ->group(function () {

            Route::get(
                '{cotizacion}',
                [CotizacionController::class, 'show']
            )->name('show');

            Route::patch(
                '{cotizacion}/anular',
                [CotizacionController::class, 'anularAdmin']
            )->name('anular');

            Route::get(
                '{cotizacion}/pdf',
                [CotizacionController::class, 'descargarPdf']
            )->name('pdf');

            Route::post(
                '{cotizacion}/reenviar-correo',
                [CotizacionController::class, 'reenviarCorreo']
            )->name('reenviar-correo');
        });

    Route::get(
        '/cotizaciones',
        [CotizacionController::class, 'index']
    )->name('cotizaciones.index');
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

        Route::get(
            'crear',
            'operacion'
        )->name('operacion');

        Route::post(
            'crear/operacion',
            'guardarOperacion'
        )->name('operacion.guardar');


        /*
        |--------------------------------------------------------------------------
        | Paso 1 - Cliente
        |--------------------------------------------------------------------------
        */

        Route::get(
            'crear/cliente',
            'cliente'
        )->name('cliente');

        Route::post(
            'crear/cliente',
            'guardarCliente'
        )->name('cliente.guardar');


        /*
        |--------------------------------------------------------------------------
        | Paso 2 - Datos de reserva
        |--------------------------------------------------------------------------
        */

        Route::get(
            'crear/datos-reserva',
            'reserva'
        )->name('datos');

        Route::post(
            'crear/datos-reserva',
            'guardarReserva'
        )->name('datos.guardar');

        Route::post(
            'validar-convenio',
            'validarConvenio'
        )->name('validar-convenio');


        /*
        |--------------------------------------------------------------------------
        | Paso 3 - Confirmación
        |--------------------------------------------------------------------------
        */

        Route::get(
            'crear/confirmacion',
            'confirmacion'
        )->name('confirmacion');


        /*
        |--------------------------------------------------------------------------
        | Paso 4 - Finalizar / Pago
        |--------------------------------------------------------------------------
        */

        Route::post(
            'crear/finalizar',
            'finalizar'
        )->name('finalizar');

        Route::get(
            '{reserva}/pago',
            'pago'
        )->name('pago');

        Route::get(
            '{reserva}/resultado',
            'resultado'
        )->name('resultado');


        /*
        |--------------------------------------------------------------------------
        | Webpay
        |--------------------------------------------------------------------------
        */

        Route::post(
            '{reserva}/pago/webpay',
            'iniciarPagoWebpay'
        )->name('pago.webpay');

        Route::get(
            '{reserva}/pago/webpay/retorno',
            'retornoWebpay'
        )->name('pago.webpay.retorno');


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
    });


/*
|--------------------------------------------------------------------------
| CONVERSIÓN DE COTIZACIÓN
|--------------------------------------------------------------------------
*/

Route::get(
    '/cotizacion/{cotizacion}/convertir-reserva',
    [CotizacionController::class, 'convertirDesdeCorreo']
)
    ->middleware('signed')
    ->name('cotizaciones.publica.convertir');

Route::get(
    '/reservas/nueva',
    [ReservaWizardController::class, 'nuevaOperacion']
)->name('reservas.nueva');
