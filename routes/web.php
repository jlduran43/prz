<?php

use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\ComunaController;
use App\Http\Controllers\HorarioDisponibleController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReservaWizardController;
use App\Http\Controllers\ServicioExperienciaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipoClienteController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::patch(
    'tipos-cliente/{tipoCliente}/cambiar-estado',
    [TipoClienteController::class, 'cambiarEstado']
)->name('tipos-cliente.cambiar-estado');

Route::resource(
    'tipos-cliente',
    TipoClienteController::class
)->parameters([
    'tipos-cliente' => 'tipoCliente',
])->except([
    'destroy',
]);

Route::patch(
    'regiones/{region}/cambiar-estado',
    [RegionController::class, 'cambiarEstado']
)->name('regiones.cambiar-estado');

Route::resource('regiones', RegionController::class)
    ->parameters([
        'regiones' => 'region',
    ])
    ->except([
        'destroy',
    ]);

Route::patch(
    'comunas/{comuna}/cambiar-estado',
    [ComunaController::class, 'cambiarEstado']
)->name('comunas.cambiar-estado');

Route::resource('comunas', ComunaController::class)
    ->parameters([
        'comunas' => 'comuna',
    ])
    ->except([
        'destroy',
    ]);

Route::patch(
    'categorias-servicio/{categoria}/activar',
    [CategoriaServicioController::class, 'activar']
)->name('categorias-servicio.activar');

Route::resource(
    'categorias-servicio',
    CategoriaServicioController::class
)->parameters([
    'categorias-servicio' => 'categoria',
]);

Route::patch(
    'horarios-disponibles/{horario}/activar',
    [HorarioDisponibleController::class, 'activar']
)->name('horarios-disponibles.activar');

Route::resource(
    'horarios-disponibles',
    HorarioDisponibleController::class
)->parameters([
    'horarios-disponibles' => 'horario',
]);

Route::patch(
    '/servicios-experiencias/{servicio}/activar',
    [ServicioExperienciaController::class, 'activar']
)->name('servicios-experiencias.activar');

Route::resource(
    'servicios-experiencias',
    ServicioExperienciaController::class
)->parameters([
    'servicios-experiencias' => 'servicio',
]);

Route::prefix('reservas')
    ->name('reservas.')
    ->group(function () {
        Route::get(
            '/crear/cliente',
            [ReservaWizardController::class, 'cliente']
        )->name('cliente');

        Route::post(
            '/crear/cliente',
            [ReservaWizardController::class, 'guardarCliente']
        )->name('cliente.guardar');

        Route::get(
            '/crear/datos-reserva',
            [ReservaWizardController::class, 'reserva']
        )->name('datos');

        Route::post(
            '/crear/datos-reserva',
            [ReservaWizardController::class, 'guardarReserva']
        )->name('datos.guardar');

        /*
        |--------------------------------------------------------------------------
        | Consultar servicios disponibles por fecha
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/servicios-disponibles',
            [
                ReservaWizardController::class,
                'consultarServiciosDisponibles',
            ]
        )->name('servicios-disponibles');

        /*
        |--------------------------------------------------------------------------
        | Consultar horarios de un servicio
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/consultar-horarios',
            [
                ReservaWizardController::class,
                'consultarHorarios',
            ]
        )->name('consultar-horarios');

        Route::get(
            '/crear/confirmacion',
            [ReservaWizardController::class, 'confirmacion']
        )->name('confirmacion');

        Route::post(
            '/crear/finalizar',
            [ReservaWizardController::class, 'finalizar']
        )->name('finalizar');

        Route::get(
            '/comunas-por-region/{region}',
            [
                ReservaWizardController::class,
                'comunasPorRegion',
            ]
        )->name('comunas-por-region');
    });
