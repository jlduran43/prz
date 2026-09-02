@extends('adminlte::page')

@section('title', 'Generar horarios')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1 class="mb-0">
                Generación automática de horarios
            </h1>

            <small class="text-muted">
                Crea múltiples franjas para una temporada.
            </small>
        </div>

    </div>

@stop


@section('content')

    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Revisa los siguientes datos:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach

            </ul>

        </div>

    @endif


    <form action="{{ route('horarios-disponibles.recurrentes.guardar') }}" method="POST" id="formGenerarHorarios">

        @csrf


        <div class="card card-success">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-calendar-plus mr-1"></i>

                    Generación automática de horarios

                </h3>

            </div>


            <div class="card-body">


                {{-- TEMPORADA --}}

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="fecha_fin">

                                Inicio temporada

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                                value="{{ old('fecha_desde') }}" required>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="fecha_hasta">

                                Hasta

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                                value="{{ old('fecha_hasta') }}" required>

                        </div>

                    </div>

                </div>


                {{-- DÍAS --}}

                <div class="form-group">

                    <label>

                        Días disponibles

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <div class="dias-container">

                        @php

                            $dias = [
                                1 => 'Lunes',
                                2 => 'Martes',
                                3 => 'Miércoles',
                                4 => 'Jueves',
                                5 => 'Viernes',
                                6 => 'Sábado',
                                7 => 'Domingo',
                            ];

                            $diasOld = array_map('intval', old('dias', []));

                        @endphp


                        @foreach ($dias as $numero => $nombre)
                            <label class="dia-check">

                                <input type="checkbox" name="dias_semana[]" value="{{ $numero }}"
                                    @checked(in_array($numero, $diasOld, true))>

                                <span>
                                    {{ $nombre }}
                                </span>

                            </label>
                        @endforeach

                    </div>

                </div>


                <hr>


                {{-- FRANJAS --}}

                <div
                    class="d-flex
                           justify-content-between
                           align-items-center
                           mb-3">

                    <div>

                        <h5 class="mb-1">

                            <i class="fas fa-clock text-success mr-1"></i>

                            Franjas horarias

                        </h5>

                        <small class="text-muted">

                            Puedes agregar varias franjas.
                            Cada una puede tener su propia
                            capacidad y servicios.

                        </small>

                    </div>


                    <button type="button" class="btn btn-success" id="btnAgregarFranja">

                        <i class="fas fa-plus mr-1"></i>

                        Agregar franja

                    </button>

                </div>


                <div id="contenedorFranjas"></div>


                <div id="mensajeSinFranjas" class="alert alert-light border text-center">

                    <i
                        class="fas fa-calendar-day
                               fa-2x
                               text-muted
                               mb-2"></i>

                    <div>
                        Agrega una franja horaria
                        para comenzar.
                    </div>

                </div>

            </div>


            <div class="card-footer">

                <button type="submit" class="btn btn-success">

                    <i class="fas fa-calendar-plus mr-1"></i>

                    Generar horarios

                </button>


                <a href="{{ route('horarios-disponibles.index') }}" class="btn btn-secondary">

                    <i class="fas fa-arrow-left mr-1"></i>

                    Volver

                </a>

            </div>

        </div>

    </form>


    {{-- TEMPLATE FRANJA --}}

    <template id="templateFranja">

        <div class="card card-outline
                   card-success
                   franja-card">

            <div class="card-header
                       d-flex
                       align-items-center">

                <h3 class="card-title">

                    <i class="fas fa-clock mr-1"></i>

                    <span class="titulo-franja">
                        Franja
                    </span>

                </h3>


                <button type="button"
                    class="btn
                           btn-sm
                           btn-outline-danger
                           ml-auto
                           btnEliminarFranja">

                    <i class="fas fa-trash mr-1"></i>

                    Eliminar

                </button>

            </div>


            <div class="card-body">


                <div class="row">


                    {{-- INICIO --}}

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>

                                Hora de inicio

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input type="time"
                                class="
                                    form-control
                                    input-hora-inicio
                                "
                                required>

                        </div>

                    </div>


                    {{-- TÉRMINO --}}

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>

                                Hora de término

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input type="time"
                                class="
                                    form-control
                                    input-hora-termino
                                "
                                required>

                        </div>

                    </div>


                    {{-- CAPACIDAD --}}

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>

                                Capacidad máxima

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input type="number" min="1"
                                class="
                                    form-control
                                    input-capacidad
                                "
                                placeholder="Ej: 45" required>

                        </div>

                    </div>

                </div>


                {{-- SERVICIOS --}}

                <div class="form-group mb-0">

                    <div
                        class="d-flex
                               justify-content-between
                               align-items-center
                               mb-2">

                        <label class="mb-0">

                            Servicios disponibles
                            en esta franja

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <div>

                            <button type="button"
                                class="
                                    btn
                                    btn-sm
                                    btn-outline-success
                                    btnSeleccionarTodos
                                ">

                                Seleccionar todos

                            </button>


                            <button type="button"
                                class="
                                    btn
                                    btn-sm
                                    btn-outline-secondary
                                    btnLimpiarServicios
                                ">

                                Limpiar

                            </button>

                        </div>

                    </div>


                    {{-- BUSCADOR --}}

                    <div class="input-group mb-3">

                        <div class="input-group-prepend">

                            <span class="input-group-text">

                                <i class="fas fa-search"></i>

                            </span>

                        </div>

                        <input type="text"
                            class="
                                form-control
                                buscador-servicios
                            "
                            placeholder="Buscar servicio...">

                    </div>


                    {{-- CHECKBOXES --}}

                    <div class="lista-servicios-franja">

                        @foreach ($servicios as $servicio)
                            <label class="servicio-check"
                                data-nombre="{{ Str::lower($servicio->nombre) }}">

                                <input type="checkbox" value="{{ $servicio->id }}"
                                    class="
                                        checkbox-servicio
                                    ">

                                <span>

                                    {{ $servicio->nombre }}

                                </span>

                            </label>
                        @endforeach

                    </div>


                    <small class="form-text text-muted">

                        Selecciona uno o más servicios
                        disponibles durante esta franja.

                    </small>

                </div>

            </div>

        </div>

    </template>

@stop


@section('css')

    <style>
        .dias-container {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(150px, 1fr));
            gap: 12px;
            padding: 18px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #fff;
        }


        .dia-check {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            cursor: pointer;
            font-weight: 600;
        }


        .dia-check input {
            width: 18px;
            height: 18px;
        }


        .franja-card {
            margin-bottom: 20px;
        }


        .lista-servicios-franja {
            max-height: 260px;
            overflow-y: auto;

            display: grid;
            grid-template-columns:
                repeat(2, minmax(250px, 1fr));

            gap: 8px;

            padding: 15px;

            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #fafafa;
        }


        .servicio-check {
            display: flex;
            align-items: flex-start;

            gap: 9px;

            margin: 0;
            padding: 8px 10px;

            border-radius: 5px;

            cursor: pointer;
        }


        .servicio-check:hover {
            background: #eef8f1;
        }


        .servicio-check input {
            margin-top: 3px;

            width: 17px;
            height: 17px;

            flex-shrink: 0;
        }


        @media (max-width: 768px) {

            .dias-container {
                grid-template-columns:
                    repeat(2, 1fr);
            }


            .lista-servicios-franja {
                grid-template-columns: 1fr;
            }

        }
    </style>

@stop


@section('js')

    <script src="{{ asset('js/horarios_atencion/generar.js') }}"></script>

@stop