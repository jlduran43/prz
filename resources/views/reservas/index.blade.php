@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

        <div>

            <h1 class="mb-1">
                Reservas
            </h1>

            <p class="text-muted mb-0">
                Administra y consulta las reservas registradas en el sistema.
            </p>

        </div>

    </div>

@stop


@section('content')

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success">

            <i class="fas fa-check-circle mr-1"></i>

            {{ session('success') }}

        </div>
    @endif


    {{-- Mensaje de error --}}
    @if (session('error'))
        <div class="alert alert-danger">

            <i class="fas fa-exclamation-circle mr-1"></i>

            {{ session('error') }}

        </div>
    @endif


    <div class="card">

        {{-- ============================================= --}}
        {{-- TÍTULO --}}
        {{-- ============================================= --}}
        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-calendar-check mr-2"></i>

                Reservas registradas

            </h3>

        </div>

        <div class="mb-3 d-flex justify-content-end">

            <div class="btn-group" role="group">

                <button type="button" class="btn btn-success active" id="btnVistaTabla">
                    <i class="fas fa-list mr-1"></i>
                    Tabla
                </button>


                <button type="button" class="btn btn-outline-success" id="btnVistaCalendario">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Calendario
                </button>

            </div>

        </div>


        {{-- ============================================= --}}
        {{-- BUSCADOR --}}
        {{-- ============================================= --}}
        <div class="card-header" id="buscadorReservas">

            <form action="{{ route('reservas.index') }}" method="GET">

                <div class="buscador-fila">

                    {{-- Texto --}}
                    <div class="buscador-input">

                        <input type="text" name="buscar" class="form-control" value="{{ $buscar ?? '' }}"
                            placeholder="Buscar por folio, cliente, RUT o correo...">

                    </div>


                    {{-- Estado --}}
                    <div class="buscador-select">

                        <select name="estado" class="form-control">

                            <option value="">
                                Todos los estados
                            </option>

                            <option value="PENDIENTE_PAGO" @selected(($estado ?? '') === 'PENDIENTE_PAGO')>
                                Pendiente de pago
                            </option>

                            <option value="PAGADA" @selected(($estado ?? '') === 'PAGADA')>
                                Pagada
                            </option>

                            <option value="CONFIRMADA" @selected(($estado ?? '') === 'CONFIRMADA')>
                                Confirmada
                            </option>

                            <option value="CANCELADA" @selected(($estado ?? '') === 'CANCELADA')>
                                Cancelada
                            </option>

                        </select>

                    </div>


                    {{-- Buscar --}}
                    <button type="submit" class="btn btn-primary btn-busqueda">

                        <i class="fas fa-search mr-1"></i>

                        <span class="texto-boton">
                            Buscar
                        </span>

                    </button>


                    {{-- Limpiar --}}
                    <a href="{{ route('reservas.index') }}" class="btn btn-secondary btn-limpiar">

                        <i class="fas fa-eraser mr-1"></i>

                        <span class="texto-boton">
                            Limpiar
                        </span>

                    </a>

                </div>

            </form>

        </div>


        {{-- ============================================= --}}
        {{-- TABLA --}}
        {{-- ============================================= --}}
        <div id="vistaTabla">
            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-striped mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Reserva
                                </th>

                                <th>
                                    Cliente
                                </th>

                                <th>
                                    Fecha visita
                                </th>

                                <th class="text-center">
                                    Asistentes
                                </th>

                                <th class="text-right">
                                    Total
                                </th>

                                <th class="text-center">
                                    Medio de pago
                                </th>

                                <th class="text-center">
                                    Estado
                                </th>

                                <th class="text-center" style="width: 130px;">
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($reservas as $reserva)

                                <tr>

                                    {{-- Folio --}}
                                    <td class="align-middle">

                                        <span class="badge badge-info p-2">

                                            RES-{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}

                                        </span>

                                    </td>


                                    {{-- Cliente --}}
                                    <td class="align-middle">

                                        @if ($reserva->nombres)
                                            <strong>
                                                {{ $reserva->nombres }}
                                                {{ $reserva->apellidos }}
                                            </strong>

                                            @if ($reserva->rut_persona)
                                                <div>
                                                    <small class="text-muted">
                                                        {{ $reserva->rut_persona }}
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <strong>
                                                {{ $reserva->nombre_entidad ?? 'Sin información' }}
                                            </strong>

                                            @if ($reserva->rut_entidad)
                                                <div>
                                                    <small class="text-muted">
                                                        {{ $reserva->rut_entidad }}
                                                    </small>
                                                </div>
                                            @endif
                                        @endif


                                        @if ($reserva->email)
                                            <div>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ $reserva->email }}
                                                </small>
                                            </div>
                                        @endif

                                    </td>


                                    {{-- Fecha --}}
                                    <td class="align-middle">

                                        @if ($reserva->fecha)
                                            <i class="far fa-calendar-alt text-muted mr-1"></i>

                                            {{ \Illuminate\Support\Carbon::parse($reserva->fecha)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">
                                                Sin fecha
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Asistentes --}}
                                    <td class="text-center align-middle">

                                        <span class="badge badge-secondary p-2">

                                            <i class="fas fa-users mr-1"></i>

                                            {{ $reserva->cantidad_asistentes ?? 0 }}

                                        </span>

                                    </td>


                                    {{-- Total --}}
                                    <td class="text-right align-middle">

                                        <strong class="text-success">

                                            ${{ number_format($reserva->total ?? 0, 0, ',', '.') }}

                                        </strong>

                                    </td>


                                    {{-- Medio de pago --}}
                                    <td class="text-center align-middle">

                                        @if ($reserva->medio_pago === 'WEBPAY')
                                            <span class="badge badge-primary p-2">

                                                <i class="fas fa-credit-card mr-1"></i>

                                                Webpay

                                            </span>
                                        @elseif ($reserva->medio_pago === 'KHIPU')
                                            <span class="badge badge-info p-2">

                                                <i class="fas fa-university mr-1"></i>

                                                Transferencia

                                            </span>
                                        @else
                                            <span class="badge badge-light p-2">

                                                <i class="fas fa-minus mr-1"></i>

                                                Sin definir

                                            </span>
                                        @endif

                                    </td>


                                    {{-- Estado --}}
                                    <td class="text-center align-middle">

                                        @switch($reserva->estado)
                                            @case('PENDIENTE_PAGO')
                                                <span class="badge badge-warning p-2">

                                                    <i class="fas fa-clock mr-1"></i>

                                                    Pendiente de pago

                                                </span>
                                            @break

                                            @case('PAGADA')
                                                <span class="badge badge-success p-2">

                                                    <i class="fas fa-dollar-sign mr-1"></i>

                                                    Pagada

                                                </span>
                                            @break

                                            @case('CONFIRMADA')
                                                <span class="badge badge-success p-2">

                                                    <i class="fas fa-check-circle mr-1"></i>

                                                    Confirmada

                                                </span>
                                            @break

                                            @case('CANCELADA')
                                                <span class="badge badge-danger p-2">

                                                    <i class="fas fa-ban mr-1"></i>

                                                    Cancelada

                                                </span>
                                            @break

                                            @default
                                                <span class="badge badge-secondary p-2">

                                                    {{ $reserva->estado }}

                                                </span>
                                        @endswitch

                                    </td>


                                    {{-- Acciones --}}
                                    <td class="text-center align-middle text-nowrap">

                                        <div class="acciones-botones">

                                            {{-- Ver --}}
                                            <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-info btn-sm"
                                                title="Ver reserva">

                                                <i class="fas fa-eye"></i>

                                            </a>


                                            {{-- Pago pendiente --}}
                                            @if ($reserva->estado === 'PENDIENTE_PAGO')
                                                <a href="{{ route('reservas.pago', $reserva) }}"
                                                    class="btn btn-success btn-sm" title="Ir al pago">

                                                    <i class="fas fa-credit-card"></i>

                                                </a>
                                            @endif

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                    <tr>

                                        <td colspan="8" class="text-center text-muted py-5">

                                            <i class="fas fa-calendar-times fa-2x mb-3"></i>

                                            <div>
                                                No existen reservas registradas.
                                            </div>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- ============================================= --}}
                {{-- PAGINACIÓN --}}
                {{-- ============================================= --}}
                @if ($reservas->hasPages())
                    <div class="card-footer">

                        {{ $reservas->withQueryString()->links() }}

                    </div>
                @endif

            </div>
        </div>
        <div id="vistaCalendario" class="d-none">

            <div class="card card-success">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-calendar-alt mr-1"></i>

                        Calendario de reservas

                    </h3>

                </div>


                <div class="card-body">


                    <div class="row mb-3">


                        <div class="col-md-4">

                            <label for="estadoCalendario">
                                Estado
                            </label>

                            <select id="estadoCalendario" class="form-control">

                                <option value="">
                                    Todos los estados
                                </option>

                                <option value="PENDIENTE_PAGO">
                                    Pendiente de pago
                                </option>

                                <option value="PAGADA">
                                    Pagada
                                </option>

                                <option value="CONFIRMADA">
                                    Confirmada
                                </option>

                                <option value="VENCIDA_PAGO">
                                    Pago vencido
                                </option>

                                <option value="CANCELADA">
                                    Cancelada
                                </option>

                                <option value="RECHAZADA">
                                    Rechazada
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- LEYENDA --}}

                    <div class="leyenda-reservas mb-3">

                        <span>
                            <i class="fas fa-circle text-warning"></i>
                            Pendiente pago
                        </span>

                        <span>
                            <i class="fas fa-circle text-success"></i>
                            Pagada
                        </span>

                        <span>
                            <i class="fas fa-circle" style="color:#198754"></i>
                            Confirmada
                        </span>

                        <span>
                            <i class="fas fa-circle text-secondary"></i>
                            Pago vencido
                        </span>
                    </div>

                    <div id="calendarioReservas" data-eventos-url="{{ route('reservas.calendario.eventos') }}"
                        data-show-url="{{ url('reservas') }}">
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="modalReservaCalendario" tabindex="-1" role="dialog" aria-hidden="true">

            <div class="modal-dialog modal-lg" role="document">

                <div class="modal-content">


                    <div class="modal-header">

                        <h5 class="modal-title">

                            <i class="fas fa-calendar-check mr-1"></i>

                            Detalle de reserva

                        </h5>


                        <button type="button" class="close" data-dismiss="modal">
                            <span>
                                &times;
                            </span>
                        </button>

                    </div>


                    <div class="modal-body">


                        <div class="row">


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Reserva
                                </small>

                                <div class="font-weight-bold" id="calReservaFolio">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Estado
                                </small>

                                <div id="calReservaEstado">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Cliente
                                </small>

                                <div class="font-weight-bold" id="calReservaCliente">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    RUT
                                </small>

                                <div id="calReservaRut">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Correo
                                </small>

                                <div id="calReservaEmail">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Asistentes
                                </small>

                                <div id="calReservaAsistentes">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Fecha
                                </small>

                                <div id="calReservaFecha">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Horario
                                </small>

                                <div id="calReservaHorario">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Medio de pago
                                </small>

                                <div id="calReservaMedioPago">
                                    -
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <small class="text-muted">
                                    Total
                                </small>

                                <div class="font-weight-bold text-success" id="calReservaTotal">
                                    -
                                </div>

                            </div>


                            <div class="col-12">

                                <small class="text-muted">
                                    Servicios
                                </small>

                                <div id="calReservaServicios" class="mt-1">
                                    -
                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <a href="#" id="btnVerReservaCalendario" class="btn btn-info">

                            <i class="fas fa-eye mr-1"></i>

                            Ver reserva

                        </a>


                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cerrar
                        </button>

                    </div>

                </div>

            </div>

        </div>
    @stop


    @section('css')
        <style>
            /*
                                                            |--------------------------------------------------------------------------
                                                            | FullCalendar Reservas
                                                            |--------------------------------------------------------------------------
                                                            */

            #calendarioReservas {
                min-height: 700px;
            }


            #calendarioReservas .fc-toolbar-title {
                font-size: 1.35rem;
                font-weight: 700;
            }


            #calendarioReservas .fc-button-primary {
                background-color: #198754;
                border-color: #198754;
            }


            #calendarioReservas .fc-button-primary:hover {
                background-color: #146c43;
                border-color: #146c43;
            }


            #calendarioReservas .fc-button-primary:not(:disabled).fc-button-active {

                background-color: #146c43;
                border-color: #146c43;
            }


            #calendarioReservas .fc-daygrid-day-number {
                color: #495057;
            }


            #calendarioReservas .fc-event {
                cursor: pointer;

                font-size: 0.85rem;

                padding: 2px 4px;

                border-radius: 4px;
            }


            #calendarioReservas .fc-event-title {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }


            .leyenda-reservas {
                display: flex;
                flex-wrap: wrap;
                gap: 18px;

                padding: 10px 15px;

                background: #f8f9fa;

                border: 1px solid #dee2e6;

                border-radius: 5px;
            }


            .leyenda-reservas span {
                font-size: 0.9rem;
            }


            .leyenda-reservas i {
                margin-right: 4px;
            }


            @media (max-width: 768px) {

                #calendarioReservas {
                    min-height: 550px;
                }


                #calendarioReservas .fc-toolbar {
                    flex-direction: column;
                    gap: 10px;
                }

            }
        </style>
        <style>
            /*
                                                            |--------------------------------------------------------------------------
                                                            | FullCalendar Reservas
                                                            |--------------------------------------------------------------------------
                                                            */

            #calendarioReservas {
                min-height: 700px;
            }


            #calendarioReservas .fc-toolbar-title {
                font-size: 1.35rem;
                font-weight: 700;
            }


            #calendarioReservas .fc-button-primary {
                background-color: #198754;
                border-color: #198754;
            }


            #calendarioReservas .fc-button-primary:hover {
                background-color: #146c43;
                border-color: #146c43;
            }


            #calendarioReservas .fc-button-primary:not(:disabled).fc-button-active {

                background-color: #146c43;
                border-color: #146c43;
            }


            #calendarioReservas .fc-daygrid-day-number {
                color: #495057;
            }


            #calendarioReservas .fc-event {
                cursor: pointer;

                font-size: 0.85rem;

                padding: 2px 4px;

                border-radius: 4px;
            }


            #calendarioReservas .fc-event-title {
                font-weight: 600;
            }


            .leyenda-reservas {
                display: flex;
                flex-wrap: wrap;
                gap: 18px;

                padding: 10px 15px;

                background: #f8f9fa;

                border: 1px solid #dee2e6;

                border-radius: 5px;
            }


            .leyenda-reservas span {
                font-size: 0.9rem;
            }


            .leyenda-reservas i {
                margin-right: 4px;
            }


            @media (max-width: 768px) {

                #calendarioReservas {
                    min-height: 550px;
                }


                #calendarioReservas .fc-toolbar {
                    flex-direction: column;
                    gap: 10px;
                }

            }

            /* Separación entre botones de navegación y vistas */
            .fc .fc-button-group {
                gap: 6px;
            }

            /* Un poco más de espacio entre los bloques del toolbar */
            .fc .fc-toolbar-chunk {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            /* Evitar que los botones queden pegados visualmente */
            .fc .fc-button {
                border-radius: 4px !important;
            }

            #calendarioReservas .fc-daygrid-event {
                background: transparent !important;
                border: none !important;
                padding: 1px 2px;
                margin: 2px 0;
            }

            #calendarioReservas .fc-daygrid-event:hover {
                opacity: 0.9;
            }

            .evento-reserva-mes {
                display: block;
                width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-weight: 600;
            }

            #calendarioReservas .evento-reserva-mes {
                display: flex;
                align-items: center;
                gap: 5px;

                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;

                font-weight: 600;
                color: #343a40;
            }

            #calendarioReservas .evento-reserva-punto {
                display: inline-block;

                width: 10px;
                height: 10px;

                min-width: 10px;

                border-radius: 50%;
            }
        </style>
        <link rel="stylesheet" href="{{ asset('css/buscador.css') }}">
        <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
    @stop

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/locales-all.global.min.js"></script>
        <script src="{{ asset('js/reservas/index.js') }}"></script>
    @stop
