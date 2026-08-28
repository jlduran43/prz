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


        {{-- ============================================= --}}
        {{-- BUSCADOR --}}
        {{-- ============================================= --}}
        <div class="card-header">

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
                                            <a href="{{ route('reservas.pago', $reserva) }}" class="btn btn-success btn-sm"
                                                title="Ir al pago">

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

    @stop


    @section('css')
        <link rel="stylesheet" href="{{ asset('css/buscador.css') }}">
        <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
    @stop
