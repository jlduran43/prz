@extends('adminlte::page')

@section('title', 'Nueva reserva')

@section('content_header')
    <div>
        <h1 class="mb-1">Nueva reserva</h1>

        <p class="text-muted mb-0">
            Selecciona cómo deseas continuar.
        </p>
    </div>
@stop

@section('content')
    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-route mr-1"></i>

                ¿Qué deseas hacer?

            </h3>

        </div>


        <div class="card-body">

            <p class="text-muted mb-4">
                Selecciona una opción para comenzar.
            </p>


            <div class="row">

                {{-- ===================================================== --}}
                {{-- COTIZACIÓN --}}
                {{-- ===================================================== --}}

                <div class="col-lg-6 mb-4">

                    <div class="opcion-operacion">

                        <div class="opcion-body">

                            <div class="opcion-icono">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>


                            <h4 class="opcion-titulo">
                                Solicitar cotización
                            </h4>


                            <p class="opcion-descripcion">

                                Obtén un valor estimado para tu visita
                                según los servicios seleccionados y la
                                cantidad de personas.

                            </p>


                            <div class="opcion-aviso">

                                <i class="fas fa-info-circle
                                           text-info">
                                </i>

                                La cotización es referencial y
                                <strong>
                                    no reserva cupos ni horarios.
                                </strong>

                            </div>


                            <form action="{{ route('reservas.operacion.guardar') }}" method="POST">

                                @csrf

                                <input type="hidden" name="tipo_operacion" value="COTIZACION">


                                <button type="submit" class="opcion-enlace cotizar">

                                    Cotizar

                                    <i class="fas fa-arrow-right"></i>

                                </button>

                            </form>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- RESERVA / PAGO --}}
                {{-- ===================================================== --}}

                <div class="col-lg-6 mb-4">

                    <div class="opcion-operacion reserva">

                        <div class="opcion-body">

                            <div class="opcion-icono">
                                <i class="fas fa-calendar-check"></i>
                            </div>


                            <h4 class="opcion-titulo">
                                Realizar reserva
                            </h4>


                            <p class="opcion-descripcion">

                                Selecciona fecha, servicios y horarios
                                disponibles para realizar tu reserva.

                            </p>


                            <div class="opcion-aviso">

                                <i class="fas fa-info-circle
                                           text-primary">
                                </i>

                                La disponibilidad de cupos será validada
                                durante el proceso de reserva.

                            </div>


                            <form action="{{ route('reservas.operacion.guardar') }}" method="POST">

                                @csrf

                                <input type="hidden" name="tipo_operacion" value="RESERVA">


                                <button type="submit" class="opcion-enlace reservar">

                                    Reservar

                                    <i class="fas fa-arrow-right"></i>

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>


            <div class="alert alert-primary mb-0">

                <div class="d-flex">

                    <div class="mr-3">

                        <i class="fas fa-info-circle
                                   fa-2x">
                        </i>

                    </div>


                    <div>

                        <strong>
                            Importante
                        </strong>

                        <div>

                            Una cotización entrega un valor estimado
                            de los servicios seleccionados.

                            Los cupos y horarios solo se validan
                            al realizar una reserva.

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/reservas/wizard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reservas/paso0.css') }}">
@stop
