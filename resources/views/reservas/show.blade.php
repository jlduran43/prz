@extends('adminlte::page')

@section('title', 'Detalle de reserva')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1 class="mb-0">
                Detalle de reserva
            </h1>

            <small class="text-muted">
                RES-{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}
            </small>
        </div>
    </div>
@stop


@section('content')

    @php

        $estado = strtoupper($reserva->estado ?? '');

        $estadoConfig = match ($estado) {
            'PENDIENTE_PAGO' => [
                'texto' => 'Pendiente de pago',
                'clase' => 'warning',
                'icono' => 'fas fa-clock',
            ],

            'PAGADA' => [
                'texto' => 'Pagada',
                'clase' => 'success',
                'icono' => 'fas fa-dollar-sign',
            ],

            'CONFIRMADA' => [
                'texto' => 'Confirmada',
                'clase' => 'success',
                'icono' => 'fas fa-check-circle',
            ],

            'VENCIDA_PAGO' => [
                'texto' => 'Pago vencido',
                'clase' => 'secondary',
                'icono' => 'fas fa-clock',
            ],

            'CANCELADA' => [
                'texto' => 'Cancelada',
                'clase' => 'danger',
                'icono' => 'fas fa-times-circle',
            ],

            'RECHAZADA' => [
                'texto' => 'Rechazada',
                'clase' => 'danger',
                'icono' => 'fas fa-times-circle',
            ],

            default => [
                'texto' => $reserva->estado ?? 'Sin estado',
                'clase' => 'secondary',
                'icono' => 'fas fa-info-circle',
            ],
        };

        $cliente = $reserva->nombre_entidad ?: trim(($reserva->nombres ?? '') . ' ' . ($reserva->apellidos ?? ''));

        $rut = $reserva->rut_entidad ?: $reserva->rut_persona;
    @endphp


    {{-- ========================================================= --}}
    {{-- RESUMEN --}}
    {{-- ========================================================= --}}

    <div class="card card-outline card-success">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-ticket-alt mr-2"></i>

                Reserva
                RES-{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}

            </h3>

            <div class="card-tools">

                <span class="badge badge-{{ $estadoConfig['clase'] }} p-2">
                    <i class="{{ $estadoConfig['icono'] }} mr-1"></i>

                    {{ $estadoConfig['texto'] }}
                </span>

            </div>

        </div>


        <div class="card-body">

            <div class="row">

                {{-- CLIENTE --}}

                <div class="col-md-6">

                    <h5 class="text-success mb-3">
                        <i class="fas fa-user mr-2"></i>
                        Datos del cliente
                    </h5>

                    <table class="table table-sm table-borderless">

                        <tbody>

                            <tr>
                                <th style="width: 160px;">
                                    Cliente
                                </th>

                                <td>
                                    {{ $cliente ?: '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    RUT
                                </th>

                                <td>
                                    {{ $rut ?: '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    Correo
                                </th>

                                <td>

                                    @if ($reserva->email)
                                        <a href="mailto:{{ $reserva->email }}">
                                            {{ $reserva->email }}
                                        </a>
                                    @else
                                        -
                                    @endif

                                </td>
                            </tr>

                            <tr>
                                <th>
                                    Teléfono
                                </th>

                                <td>
                                    {{ $reserva->telefono ?: '-' }}
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>


                {{-- VISITA --}}

                <div class="col-md-6">

                    <h5 class="text-success mb-3">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Datos de la visita
                    </h5>

                    <table class="table table-sm table-borderless">

                        <tbody>

                            <tr>
                                <th style="width: 160px;">
                                    Fecha
                                </th>

                                <td>

                                    @if ($reserva->fecha)
                                        <i class="far fa-calendar-alt text-muted mr-1"></i>

                                        {{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif

                                </td>
                            </tr>

                            <tr>
                                <th>
                                    Asistentes
                                </th>

                                <td>

                                    <span class="badge badge-secondary p-2">

                                        <i class="fas fa-users mr-1"></i>

                                        {{ $reserva->cantidad_asistentes ?? 0 }}

                                    </span>

                                </td>
                            </tr>


                            @if (!empty($reserva->cantidad_alumnos))
                                <tr>
                                    <th>
                                        Alumnos
                                    </th>

                                    <td>
                                        {{ $reserva->cantidad_alumnos }}
                                    </td>
                                </tr>
                            @endif


                            @if (!empty($reserva->cantidad_profesores))
                                <tr>
                                    <th>
                                        Profesores
                                    </th>

                                    <td>
                                        {{ $reserva->cantidad_profesores }}
                                    </td>
                                </tr>
                            @endif


                            @if (!empty($reserva->nivel_educacional))
                                <tr>
                                    <th>
                                        Nivel educacional
                                    </th>

                                    <td>
                                        {{ $reserva->nivel_educacional }}
                                    </td>
                                </tr>
                            @endif


                            @if (!empty($reserva->curso))
                                <tr>
                                    <th>
                                        Curso
                                    </th>

                                    <td>
                                        {{ $reserva->curso }}
                                    </td>
                                </tr>
                            @endif

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SERVICIOS --}}
    {{-- ========================================================= --}}

    <div class="card card-outline card-success">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-map-marked-alt mr-2"></i>

                Servicios reservados

            </h3>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead>

                        <tr>
                            <th>Servicio</th>
                            <th>Horario</th>
                            <th class="text-center">
                                Personas
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($reserva->servicios as $servicio)
                            @php

                                $horario = null;

                                if (!empty($servicio->pivot->horario_disponible_id)) {
                                    $horario = \App\Models\HorarioDisponible::find(
                                        $servicio->pivot->horario_disponible_id,
                                    );
                                }

                            @endphp

                            <tr>

                                <td>

                                    <strong>
                                        {{ $servicio->nombre }}
                                    </strong>

                                </td>


                                <td>

                                    @if ($horario)
                                        <i class="far fa-clock text-success mr-1"></i>

                                        {{ substr($horario->hora_inicio, 0, 5) }}

                                        -

                                        {{ substr($horario->hora_termino, 0, 5) }}
                                    @else
                                        -
                                    @endif

                                </td>


                                <td class="text-center">

                                    <span class="badge badge-secondary p-2">

                                        <i class="fas fa-users mr-1"></i>

                                        {{ $servicio->pivot->cantidad_personas ?? 0 }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3" class="text-center text-muted py-4">

                                    No existen servicios asociados
                                    a esta reserva.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PAGO --}}
    {{-- ========================================================= --}}

    <div class="card card-outline card-success">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-credit-card mr-2"></i>

                Información de pago

            </h3>

        </div>


        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">

                    <small class="text-muted d-block">
                        Subtotal
                    </small>

                    <strong>
                        ${{ number_format($reserva->subtotal ?? 0, 0, ',', '.') }}
                    </strong>

                </div>


                <div class="col-md-3 mb-3">

                    <small class="text-muted d-block">
                        Descuento
                    </small>

                    <strong>
                        ${{ number_format($reserva->descuento ?? 0, 0, ',', '.') }}
                    </strong>

                </div>


                <div class="col-md-3 mb-3">

                    <small class="text-muted d-block">
                        Total
                    </small>

                    <strong class="text-success h5">

                        ${{ number_format($reserva->total ?? 0, 0, ',', '.') }}

                    </strong>

                </div>


                <div class="col-md-3 mb-3">

                    <small class="text-muted d-block">
                        Medio de pago
                    </small>

                    @if (strtoupper($reserva->medio_pago ?? '') === 'WEBPAY')
                        <span class="badge badge-primary p-2">

                            <i class="fas fa-credit-card mr-1"></i>

                            Webpay

                        </span>
                    @else
                        <span class="badge badge-secondary p-2">

                            {{ $reserva->medio_pago ?: 'No informado' }}

                        </span>
                    @endif

                </div>

            </div>


            @if ($reserva->pago_expira_at)
                <hr>

                <div>

                    <small class="text-muted">
                        Vencimiento del pago:
                    </small>

                    <strong>

                        {{ \Carbon\Carbon::parse($reserva->pago_expira_at)->format('d/m/Y H:i') }}

                    </strong>

                </div>
            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- OBSERVACIONES --}}
    {{-- ========================================================= --}}

    @if (!empty($reserva->observaciones))
        <div class="card card-outline card-secondary">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-comment-alt mr-2"></i>

                    Observaciones

                </h3>

            </div>

            <div class="card-body">

                {{ $reserva->observaciones }}

            </div>

        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- ACCIONES --}}
    {{-- ========================================================= --}}

    <div class="mb-4">

        <a href="{{ route('reservas.index') }}" class="btn btn-secondary">

            <i class="fas fa-arrow-left mr-1"></i>

            Volver a reservas

        </a>

    </div>

@stop


@section('css')

    <style>
        .table-borderless th {
            color: #6c757d;
            font-weight: 600;
        }

        .card-outline.card-success {
            border-top: 3px solid #28a745;
        }

        .badge {
            font-size: 0.85rem;
        }
    </style>

@stop