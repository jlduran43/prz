<?php

use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\ComunaController;
use App\Http\Controllers\HorarioDisponibleController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReservaWizardController;
use App\Http\Controllers\ServicioExperienciaController;
use App\Http\Controllers\TipoClienteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::view('/dashboard', 'dashboard')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Tipos de cliente
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Regiones
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Comunas
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Categorías de servicio
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Horarios disponibles
|--------------------------------------------------------------------------
*/

Route::controller(HorarioDisponibleController::class)
    ->prefix('horarios-disponibles')
    ->name('horarios-disponibles.')
    ->group(function () {
        /*
         * Deben declararse antes del resource para que "generar"
         * no sea interpretado como el identificador de un horario.
         */
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

/*
|--------------------------------------------------------------------------
| Servicios y experiencias
|--------------------------------------------------------------------------
*/

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
| Wizard de reservas
|--------------------------------------------------------------------------
*/

Route::controller(ReservaWizardController::class)
    ->prefix('reservas')
    ->name('reservas.')
    ->group(function () {
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
            'crear/datos-reserva',
            'guardarReserva'
        )->name('datos.guardar');

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
    });
