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

    <style>
        .opcion-operacion {
            height: 100%;
            border: 1px solid #dee2e6;
            border-top: 3px solid #17a2b8;
            border-radius: 6px;
            background: #ffffff;
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;
        }

        .opcion-operacion:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .10);
            border-color: #17a2b8;
        }

        .opcion-operacion.reserva {
            border-top-color: #007bff;
        }

        .opcion-operacion.reserva:hover {
            border-color: #007bff;
        }

        .opcion-body {
            padding: 26px 24px;
        }

        .opcion-icono {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 52px;
            height: 52px;

            margin-bottom: 18px;

            border-radius: 50%;

            background: #17a2b8;
            color: white;

            font-size: 22px;
        }

        .reserva .opcion-icono {
            background: #007bff;
        }

        .opcion-titulo {
            margin-bottom: 8px;
            color: #212529;
            font-size: 24px;
            font-weight: 700;
        }

        .opcion-descripcion {
            min-height: 48px;
            margin-bottom: 20px;

            color: #6c757d;
            font-size: 15px;
        }

        .opcion-aviso {
            min-height: 58px;

            margin-bottom: 20px;
            padding: 14px 16px;

            border: 1px solid #dee2e6;
            border-radius: 4px;

            background: #f8f9fa;
            color: #343a40;
        }

        .opcion-aviso i {
            margin-right: 6px;
        }

        .opcion-enlace {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            border: none;
            padding: 0;

            background: transparent;

            font-weight: 700;
            font-size: 15px;
        }

        .opcion-enlace.cotizar {
            color: #17a2b8;
        }

        .opcion-enlace.reservar {
            color: #007bff;
        }

        .opcion-enlace:hover {
            text-decoration: none;
            opacity: .8;
        }

        @media (max-width: 767.98px) {
            .opcion-descripcion,
            .opcion-aviso {
                min-height: auto;
            }
        }
    </style>


    {{-- WIZARD --}}
    @include('reservas.partials._wizard', [
        'paso' => 0
    ])


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

                                <i
                                    class="fas fa-info-circle
                                           text-info">
                                </i>

                                La cotización es referencial y
                                <strong>
                                    no reserva cupos ni horarios.
                                </strong>

                            </div>


                            <form
                                action="{{ route('reservas.operacion.guardar') }}"
                                method="POST"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="tipo_operacion"
                                    value="COTIZACION"
                                >


                                <button
                                    type="submit"
                                    class="opcion-enlace cotizar"
                                >

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

                                <i
                                    class="fas fa-info-circle
                                           text-primary">
                                </i>

                                La disponibilidad de cupos será validada
                                durante el proceso de reserva.

                            </div>


                            <form
                                action="{{ route('reservas.operacion.guardar') }}"
                                method="POST"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="tipo_operacion"
                                    value="RESERVA"
                                >


                                <button
                                    type="submit"
                                    class="opcion-enlace reservar"
                                >

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

                        <i
                            class="fas fa-info-circle
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
