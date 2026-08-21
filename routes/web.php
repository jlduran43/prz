<?php

use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\ComunaController;
use App\Http\Controllers\HorarioDisponibleController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReservaWizardController;
use App\Http\Controllers\ServicioExperienciaController;
use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\TipoClienteController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\ConfiguracionCotizacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        LoginController::class,
        'showLoginForm'
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login'
    ])->name('login.attempt');
});

Route::post('/logout', [
    LoginController::class,
    'logout'
])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    // TODAS LAS RUTAS DEL PANEL ADMINISTRATIVO

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    Route::resource(
        'convenios',
        ConvenioController::class
    );

    Route::controller(TipoClienteController::class)
        ->prefix('tipos-cliente')
        ->name('tipos-cliente.')
        ->group(function () {
            Route::patch(
                '{tipoCliente}/cambiar-estado',
                'cambiarEstado'
            )->name('cambiar-estado');
        });

    Route::resource(
        'tipos-cliente',
        TipoClienteController::class
    )
        ->parameters([
            'tipos-cliente' => 'tipoCliente',
        ])
        ->except('destroy');

    Route::controller(RegionController::class)
        ->prefix('regiones')
        ->name('regiones.')
        ->group(function () {
            Route::patch(
                '{region}/cambiar-estado',
                'cambiarEstado'
            )->name('cambiar-estado');
        });

    Route::resource(
        'regiones',
        RegionController::class
    )
        ->parameters([
            'regiones' => 'region',
        ])
        ->except('destroy');
    Route::controller(ComunaController::class)
        ->prefix('comunas')
        ->name('comunas.')
        ->group(function () {
            Route::patch(
                '{comuna}/cambiar-estado',
                'cambiarEstado'
            )->name('cambiar-estado');
        });

    Route::resource(
        'comunas',
        ComunaController::class
    )
        ->parameters([
            'comunas' => 'comuna',
        ])
        ->except('destroy');

    Route::controller(CategoriaServicioController::class)
        ->prefix('categorias-servicio')
        ->name('categorias-servicio.')
        ->group(function () {
            Route::patch(
                '{categoria}/activar',
                'activar'
            )->name('activar');
        });

    Route::resource(
        'categorias-servicio',
        CategoriaServicioController::class
    )
        ->parameters([
            'categorias-servicio' => 'categoria',
        ]);
    Route::controller(HorarioDisponibleController::class)
        ->prefix('horarios-disponibles')
        ->name('horarios-disponibles.')
        ->group(function () {

            Route::get(
                'generar',
                'generar'
            )->name('generar');

            Route::post(
                'recurrentes',
                'guardarRecurrentes'
            )->name('recurrentes.guardar');

            Route::patch(
                '{horario}/activar',
                'activar'
            )->name('activar');
        });

    Route::resource(
        'horarios-disponibles',
        HorarioDisponibleController::class
    )
        ->parameters([
            'horarios-disponibles' => 'horario',
        ]);

    Route::controller(ServicioExperienciaController::class)
        ->prefix('servicios-experiencias')
        ->name('servicios-experiencias.')
        ->group(function () {
            Route::patch(
                '{servicio}/activar',
                'activar'
            )->name('activar');
        });

    Route::resource(
        'servicios-experiencias',
        ServicioExperienciaController::class
    )
        ->parameters([
            'servicios-experiencias' => 'servicio',
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

    Route::get(
        '/cotizaciones/{cotizacion}',
        [CotizacionController::class, 'show']
    )->name('cotizaciones.show');
    /*
|--------------------------------------------------------------------------
| Configuración de cotizaciones
|--------------------------------------------------------------------------
*/

    Route::resource(
        'configuraciones-cotizacion',
        ConfiguracionCotizacionController::class
    )
        ->except([
            'show',
            'destroy',
        ])
        ->parameters([
            'configuraciones-cotizacion' => 'configuracion',
        ]);

    Route::patch(
        'configuraciones-cotizacion/{configuracion}/activar',
        [
            ConfiguracionCotizacionController::class,
            'activar',
        ]
    )->name(
        'configuraciones-cotizacion.activar'
    );

    Route::get(
        '/admin/cotizaciones/{cotizacion}',
        [CotizacionController::class, 'show']
    )->name('admin.cotizaciones.show');

    Route::patch(
        '/admin/cotizaciones/{cotizacion}/anular',
        [CotizacionController::class, 'anularAdmin']
    )->name('admin.cotizaciones.anular');

    Route::get(
        '/admin/cotizaciones/{cotizacion}/pdf',
        [CotizacionController::class, 'descargarPdf']
    )->name('admin.cotizaciones.pdf');
});


/*
|--------------------------------------------------------------------------
| Wizard de reservas
|--------------------------------------------------------------------------
*/

Route::controller(ReservaWizardController::class)
    ->prefix('reservas')
    ->name('reservas.')
    ->group(function () {

        /*
         * Paso 0: tipo de operación
         * Cotizar o reservar y pagar
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
         * Paso 1: datos del cliente
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
         * Paso 2: datos de la reserva
         */
        Route::get(
            'crear/datos-reserva',
            'reserva'
        )->name('datos');

        Route::post(
            '/validar-convenio',
            'validarConvenio',
        )->name('validar-convenio');

        Route::post(
            'crear/datos-reserva',
            'guardarReserva'
        )->name('datos.guardar');


        /*
         * Paso 3: confirmación
         */
        Route::get(
            'crear/confirmacion',
            'confirmacion'
        )->name('confirmacion');


        /*
         * Paso 4: guardar definitivamente
         */
        Route::post(
            'crear/finalizar',
            'finalizar'
        )->name('finalizar');


        /*
         * Consultas AJAX
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
| COTIZACIONES
|--------------------------------------------------------------------------
*/
Route::post(
    '/cotizaciones/generar',
    [ReservaWizardController::class, 'generarCotizacion']
)->name('cotizaciones.generar');

Route::get(
    '/cotizaciones/{cotizacion}/resultado/{token}',
    [CotizacionController::class, 'showPublico']
)->name('cotizaciones.resultado');

Route::patch(
    '/cotizaciones/{cotizacion}/anular/{token}',
    [CotizacionController::class, 'anularPublico']
)->name('cotizaciones.anular');

Route::get(
    '/cotizaciones/{cotizacion}/pdf/{token}',
    [CotizacionController::class, 'descargarPdfPublico']
)->name('cotizaciones.pdf.publico');

Route::get(
    '/cotizaciones/{cotizacion}/convertir/{token}',
    [CotizacionController::class, 'convertirEnReserva']
)->name('cotizaciones.convertir');

Route::get(
    '/reservas/nueva',
    [ReservaWizardController::class, 'nuevaOperacion']
)->name('reservas.nueva');

Route::get(
    '/cotizacion/{cotizacion}/convertir-reserva',
    [CotizacionController::class, 'convertirDesdeCorreo']
)
    ->middleware('signed')
    ->name('cotizaciones.publica.convertir');
    
Route::post(
    '/admin/cotizaciones/{cotizacion}/reenviar-correo',
    [CotizacionController::class, 'reenviarCorreo']
)->name('cotizaciones.reenviar-correo');

Route::get(
    '/reservas/{reserva}/resultado',
    [ReservaWizardController::class, 'resultado']
)->name('reservas.resultado');