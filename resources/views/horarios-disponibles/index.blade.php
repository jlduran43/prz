@extends('adminlte::page')

@section('title', 'Horarios de atención')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

        <h1 class="mb-3 mb-md-0">
            Horarios de atención
        </h1>

        <div class="d-flex flex-column flex-sm-row">

            <a href="{{ route('horarios-disponibles.generar') }}" class="btn btn-success mb-2 mb-sm-0 mr-sm-2">
                <i class="fas fa-calendar-plus mr-1"></i>
                Generar horarios
            </a>

            <a href="{{ route('horarios-disponibles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i>
                Nuevo horario de atención
            </a>

        </div>

    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>

            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>

            {{ session('error') }}
        </div>
    @endif


    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="far fa-calendar-alt mr-2"></i>
                Calendario de horarios
            </h3>
        </div>

        <div class="card-body">

            <div id="calendarioHorarios" data-eventos-url="{{ route('horarios-disponibles.calendario.eventos') }}"
                data-index-url="{{ route('horarios-disponibles.index') }}" data-base-url="{{ url('horarios-disponibles') }}"
                data-fecha="{{ $fecha ?? '' }}" data-estado="{{ $estado ?? '' }}">
            </div>

        </div>
    </div>

    {{-- Modal para desactivar --}}
    <div class="modal fade" id="modalDesactivar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Desactivar horario
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        ¿Está seguro de que desea desactivar este horario disponible?
                        <strong id="nombreHorarioDesactivar"></strong>
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <form id="formDesactivar" method="POST">

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">

                            <i class="fas fa-ban mr-1"></i>
                            Sí, desactivar

                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para activar --}}
    <div class="modal fade" id="modalActivar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Reactivar horario
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        ¿Está seguro de que desea reactivar este horario disponible?
                        <strong id="nombreHorarioActivar"></strong>
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <form id="formActivar" method="POST">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-success">
                            Sí, reactivar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal detalle horario desde calendario --}}
    <div class="modal fade" id="modalHorarioCalendario" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="far fa-calendar-alt mr-2"></i>
                        Detalle del horario
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <p>
                        <strong>Horario:</strong>
                        <span id="calendarHorario"></span>
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        <span id="calendarEstado"></span>
                    </p>

                    <p>
                        <strong>Google Calendar:</strong>
                        <span id="calendarGoogle"></span>
                    </p>

                    <hr>

                    <strong>Servicios asociados:</strong>

                    <ul id="calendarServicios" class="mt-2 mb-0">
                    </ul>

                </div>

                <div class="modal-footer">
                    {{-- Editar --}}
                    <a href="#" id="btnEditarHorarioCalendario" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i>
                        Editar
                    </a>
                    {{-- Desactivar --}}
                    <button type="button" id="btnDesactivarHorarioCalendario" class="btn btn-danger"
                        style="display: none;">

                        <i class="fas fa-ban mr-1"></i>
                        Desactivar
                    </button>
                    {{-- Reactivar --}}
                    <button type="button" id="btnActivarHorarioCalendario" class="btn btn-success"
                        style="display: none;">

                        <i class="fas fa-check mr-1"></i>
                        Reactivar

                    </button>
                    {{-- Cerrar --}}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">

                        Cerrar

                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/buscador.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
    <style>
        #calendarioHorarios .dia-seleccionado {
            background-color: rgba(40, 167, 69, 0.15);
        }
    </style>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/locales-all.global.min.js"></script>
    <script src="{{ asset('js/horarios_atencion/index.js') }}"></script>
@stop
