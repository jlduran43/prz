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

            <div id="calendarioHorarios"></div>

        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('horarios-disponibles.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="estado" class="form-control">
                            <option value="">
                                Todos los estados
                            </option>

                            <option value="1" @selected($estado === '1')>
                                Activos
                            </option>

                            <option value="0" @selected($estado === '0')>
                                Inactivos
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <div class="d-flex">

                            <button type="submit" class="btn btn-primary mr-2 btn-filtro-responsive" title="Buscar">

                                <i class="fas fa-search"></i>

                                <span class="d-none d-md-inline ml-1">
                                    Buscar
                                </span>

                            </button>

                            <a href="{{ route('horarios-disponibles.index') }}"
                                class="btn btn-secondary btn-filtro-responsive" title="Limpiar">

                                <i class="fas fa-eraser"></i>

                                <span class="d-none d-md-inline ml-1">
                                    Limpiar
                                </span>

                            </a>

                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Hora de inicio</th>
                        <th>Hora de término</th>
                        <th>Servicios asociados</th>
                        <th>Estado Google Calendar</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($horarios as $horario)
                        <tr>
                            <td class="align-middle">
                                {{ $horario->id }}
                            </td>

                            <td class="align-middle">
                                {{ $horario->fecha->format('d/m/Y') }}
                            </td>

                            <td class="align-middle">
                                {{ substr($horario->hora_inicio, 0, 5) }}
                            </td>

                            <td class="align-middle">
                                {{ substr($horario->hora_termino, 0, 5) }}
                            </td>

                            <td style="min-width: 340px;">
                                <div class="d-flex flex-column">
                                    @forelse ($horario->servicios as $servicio)
                                        <span class="mb-1">
                                            <i class="fas fa-circle text-info mr-2" style="font-size: 7px;"></i>
                                            {{ $servicio->nombre }}
                                        </span>
                                    @empty
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle mr-1"></i>
                                            Sin servicios asociados
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            <td>
                                @if ($horario->google_sync_error)
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Error
                                    </span>
                                @elseif($horario->google_event_id)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check mr-1"></i>
                                        Sincronizado
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pendiente
                                    </span>
                                @endif
                            </td>

                            <td class="align-middle">
                                @if ($horario->activo)
                                    <span class="badge badge-success">
                                        Activo
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <div class="acciones-botones">

                                    <a href="{{ route('horarios-disponibles.show', $horario) }}"
                                        class="btn btn-info btn-sm btn-accion" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('horarios-disponibles.edit', $horario) }}"
                                        class="btn btn-warning btn-sm btn-accion" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if ($horario->activo)
                                        <button type="button" class="btn btn-secondary btn-sm btn-accion"
                                            title="Desactivar" data-toggle="modal" data-target="#modalDesactivar"
                                            data-id="{{ $horario->id }}">

                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm btn-accion" title="Reactivar"
                                            data-toggle="modal" data-target="#modalActivar" data-id="{{ $horario->id }}">

                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No hay horarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($horarios->hasPages())
            <div class="card-footer">
                {{ $horarios->links() }}
            </div>
        @endif
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
    <script src="{{ asset('js/horarios_atencion/index.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/locales-all.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const calendarEl = document.getElementById(
                'calendarioHorarios'
            );

            if (!calendarEl) {
                return;
            }

            const estadoActual = @json($estado ?? '');

            const calendar = new FullCalendar.Calendar(
                calendarEl, {
                    locale: 'es',

                    initialView: 'dayGridMonth',

                    initialDate: @json($fecha !== '' ? $fecha : now()->format('Y-m-d')),

                    firstDay: 1,

                    height: 'auto',

                    navLinks: true,

                    nowIndicator: true,

                    dayMaxEvents: true,

                    displayEventTime: false,

                    dayCellDidMount: function(info) {

                        const fechaSeleccionada =
                            @json($fecha ?? '');

                        if (!fechaSeleccionada) {
                            return;
                        }

                        const year = info.date.getFullYear();

                        const month = String(
                            info.date.getMonth() + 1
                        ).padStart(2, '0');

                        const day = String(
                            info.date.getDate()
                        ).padStart(2, '0');

                        const fecha =
                            `${year}-${month}-${day}`;

                        if (fecha === fechaSeleccionada) {
                            info.el.classList.add(
                                'dia-seleccionado'
                            );
                        }

                    },

                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },

                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día'
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Obtener horarios desde Laravel
                    |--------------------------------------------------------------------------
                    */

                    events: @json(route('horarios-disponibles.calendario.eventos')),

                    /*
                    |--------------------------------------------------------------------------
                    | Clic en una fecha
                    |--------------------------------------------------------------------------
                    */

                    dateClick: function(info) {

                        const url = new URL(
                            @json(route('horarios-disponibles.index')),
                            window.location.origin
                        );

                        url.searchParams.set(
                            'fecha',
                            info.dateStr
                        );

                        if (estadoActual !== '') {

                            url.searchParams.set(
                                'estado',
                                estadoActual
                            );
                        }

                        window.location.href =
                            url.toString();
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Clic en un horario
                    |--------------------------------------------------------------------------
                    */

                    eventClick: function(info) {

                        info.jsEvent.preventDefault();

                        const evento = info.event;

                        const propiedades =
                            evento.extendedProps;

                        /*
                        |--------------------------------------------------------------------------
                        | Horario
                        |--------------------------------------------------------------------------
                        */

                        $('#calendarHorario')
                            .text(evento.title);

                        /*
                        |--------------------------------------------------------------------------
                        | Estado
                        |--------------------------------------------------------------------------
                        */

                        const estado =
                            propiedades.activo ?
                            'Activo' :
                            'Inactivo';

                        $('#calendarEstado')
                            .text(estado);

                        /*
                        |--------------------------------------------------------------------------
                        | Estado Google Calendar
                        |--------------------------------------------------------------------------
                        */

                        const google =
                            propiedades.google ?? 'PENDIENTE';

                        $('#calendarGoogle')
                            .text(google);

                        /*
                        |--------------------------------------------------------------------------
                        | Servicios
                        |--------------------------------------------------------------------------
                        */

                        const lista =
                            $('#calendarServicios');

                        lista.empty();

                        const servicios =
                            propiedades.servicios ?? [];

                        if (servicios.length === 0) {

                            lista.append(
                                '<li>Sin servicios asociados</li>'
                            );

                        } else {

                            servicios.forEach(
                                function(servicio) {

                                    $('<li>')
                                        .text(servicio)
                                        .appendTo(lista);
                                }
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Mostrar modal
                        |--------------------------------------------------------------------------
                        */

                        $('#modalHorarioCalendario')
                            .modal('show');
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Apariencia activo / inactivo
                    |--------------------------------------------------------------------------
                    */

                    eventDidMount: function(info) {

                        if (!info.event.extendedProps.activo) {

                            info.el.style.opacity = '0.55';

                            info.el.style.textDecoration =
                                'line-through';
                        }
                    }
                }
            );

            calendar.render();

        });
    </script>
@stop
