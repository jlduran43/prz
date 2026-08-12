@extends('adminlte::page')

@section('title', 'Confirmar reserva')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0">
                Confirmar reserva
            </h1>

            <small class="text-muted">
                Revisa cuidadosamente la información antes de finalizar.
            </small>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">

        {{-- Mensajes de error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>

                {{ session('error') }}

                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="font-weight-bold mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    No fue posible confirmar la reserva.
                </div>

                <ul class="mb-0 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Indicador de pasos --}}
        <div class="card card-outline card-primary mb-4">
            <div class="card-body">
                <div class="row text-center">

                    <div class="col-4">
                        <div class="wizard-step completed">
                            <div class="wizard-circle">
                                <i class="fas fa-check"></i>
                            </div>

                            <div class="wizard-label">
                                Cliente
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="wizard-step completed">
                            <div class="wizard-circle">
                                <i class="fas fa-check"></i>
                            </div>

                            <div class="wizard-label">
                                Reserva
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="wizard-step active">
                            <div class="wizard-circle">
                                3
                            </div>

                            <div class="wizard-label">
                                Confirmación
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">

            {{-- Datos del cliente --}}
            <div class="col-lg-6">
                <div class="card card-outline card-primary h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user mr-2"></i>
                            Datos del cliente
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="row mb-0">

                            <dt class="col-sm-5">
                                Tipo de cliente
                            </dt>

                            <dd class="col-sm-7">
                                {{ $tipoCliente->nombre ?? 'No disponible' }}
                            </dd>

                            @if (($cliente['codigo_tipo_cliente'] ?? null) === 'PERSONA')
                                <dt class="col-sm-5">
                                    Nombres
                                </dt>

                                <dd class="col-sm-7">
                                    {{ $cliente['nombres'] ?? '-' }}
                                </dd>

                                <dt class="col-sm-5">
                                    Apellidos
                                </dt>

                                <dd class="col-sm-7">
                                    {{ $cliente['apellidos'] ?? '-' }}
                                </dd>

                                <dt class="col-sm-5">
                                    RUT
                                </dt>

                                <dd class="col-sm-7">
                                    {{ $cliente['rut_persona'] ?? '-' }}
                                </dd>
                            @else
                                <dt class="col-sm-5">
                                    Nombre entidad
                                </dt>

                                <dd class="col-sm-7">
                                    {{ $cliente['nombre_entidad'] ?? '-' }}
                                </dd>

                                <dt class="col-sm-5">
                                    RUT entidad
                                </dt>

                                <dd class="col-sm-7">
                                    {{ $cliente['rut_entidad'] ?? '-' }}
                                </dd>

                                <dt class="col-sm-5">
                                    Nombre encargado
                                </dt>

                                <dd class="col-sm-7">
                                    {{ $cliente['nombre_encargado'] ?? '-' }}
                                </dd>

                                <dt class="col-sm-5">
                                    RUT encargado
                                </dt>

                                <dd class="col-sm-7">
                                    {{ $cliente['rut_encargado'] ?? 'No informado' }}
                                </dd>
                            @endif

                            <dt class="col-sm-5">
                                Correo electrónico
                            </dt>

                            <dd class="col-sm-7">
                                {{ $cliente['email'] ?? '-' }}
                            </dd>

                            <dt class="col-sm-5">
                                Teléfono
                            </dt>

                            <dd class="col-sm-7">
                                {{ $cliente['telefono'] ?? '-' }}
                            </dd>

                            <dt class="col-sm-5">
                                Región
                            </dt>

                            <dd class="col-sm-7">
                                {{ $regionNombre ?? '-' }}
                            </dd>

                            <dt class="col-sm-5">
                                Comuna
                            </dt>

                            <dd class="col-sm-7">
                                {{ $comunaNombre ?? '-' }}
                            </dd>

                        </dl>
                    </div>
                </div>
            </div>

            {{-- Datos de la reserva / cotización --}}
            <div class="col-lg-6 mt-3 mt-lg-0">

                <div class="card card-outline card-info h-100">

                    <div class="card-header">

                        <h3 class="card-title">

                            @if ($esCotizacion)
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                Datos de la cotización
                            @else
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Datos de la reserva
                            @endif

                        </h3>

                    </div>


                    <div class="card-body">

                        <dl class="row mb-0">

                            {{-- ============================================ --}}
                            {{-- SERVICIOS --}}
                            {{-- ============================================ --}}

                            @foreach ($detallesServicios as $detalle)
                                <dt class="col-sm-5">
                                    Servicio
                                </dt>

                                <dd class="col-sm-7">
                                    <strong>
                                        {{ $detalle['servicio']->nombre }}
                                    </strong>
                                </dd>


                                {{-- Fecha y horario solamente en RESERVA --}}

                                @if ($esReserva)
                                    <dt class="col-sm-5">
                                        Fecha
                                    </dt>

                                    <dd class="col-sm-7">
                                        {{ \Carbon\Carbon::parse($detalle['fecha'])->format('d/m/Y') }}
                                    </dd>


                                    <dt class="col-sm-5">
                                        Horario
                                    </dt>

                                    <dd class="col-sm-7">

                                        @if (!empty($detalle['horario']))
                                            <span class="badge badge-info p-2">

                                                <i class="far fa-clock mr-1"></i>

                                                {{ substr($detalle['horario']->hora_inicio, 0, 5) }}

                                                -

                                                {{ substr($detalle['horario']->hora_termino, 0, 5) }}

                                            </span>
                                        @else
                                            <span class="text-danger">
                                                Horario no disponible
                                            </span>
                                        @endif

                                    </dd>
                                @endif


                                @if (!$loop->last)
                                    <div class="col-12">
                                        <hr class="my-3">
                                    </div>
                                @endif
                            @endforeach


                            <div class="col-12">
                                <hr class="my-3">
                            </div>


                            {{-- ============================================ --}}
                            {{-- ASISTENTES --}}
                            {{-- ============================================ --}}

                            <div class="col-12">
                                <div class="row mt-3">

                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <div class="text-muted small mb-1">
                                                <i class="fas fa-users mr-1"></i>
                                                Cantidad de asistentes
                                            </div>

                                            <div class="h5 mb-0 font-weight-bold">
                                                {{ $calculo['cantidad_asistentes'] }}
                                            </div>
                                        </div>
                                    </div>

                                    @if ($calculo['entradas_liberadas'] > 0)
                                        <div class="col-md-6 mt-3 mt-md-0">
                                            <div class="border rounded p-3 h-100">
                                                <div class="text-muted small mb-1">
                                                    <i class="fas fa-ticket-alt mr-1"></i>
                                                    Entradas liberadas
                                                </div>

                                                <div>
                                                    <span class="badge badge-success p-2">
                                                        {{ $calculo['entradas_liberadas'] }}
                                                        entrada{{ $calculo['entradas_liberadas'] > 1 ? 's' : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>

                            {{-- ============================================ --}}
                            {{-- ESTABLECIMIENTO EDUCACIONAL --}}
                            {{-- ============================================ --}}

                            @if (($cliente['codigo_tipo_cliente'] ?? null) === 'ESTABLECIMIENTO_EDUCACIONAL')

                                <dt class="col-sm-6">
                                    Cantidad de alumnos
                                </dt>

                                <dd class="col-sm-6">
                                    {{ $reserva['cantidad_alumnos'] ?? '-' }}
                                </dd>


                                <dt class="col-sm-6">
                                    Cantidad de profesores
                                </dt>

                                <dd class="col-sm-6">
                                    {{ $reserva['cantidad_profesores'] ?? '-' }}
                                </dd>


                                <dt class="col-sm-6">
                                    Nivel educacional
                                </dt>

                                <dd class="col-sm-6">

                                    {{ match ($reserva['nivel_educacional'] ?? null) {
                                        'PARVULARIA' => 'Educación parvularia',

                                        'BASICA' => 'Educación básica',

                                        'MEDIA' => 'Educación media',

                                        'ESPECIAL' => 'Educación especial',

                                        'SUPERIOR' => 'Educación superior',

                                        'ADULTOS' => 'Educación de adultos',

                                        'OTRO' => 'Otro',

                                        default => '-',
                                    } }}

                                </dd>


                                <dt class="col-sm-6">
                                    Curso
                                </dt>

                                <dd class="col-sm-6">
                                    {{ $reserva['curso'] ?? '-' }}
                                </dd>


                                @if (!empty($reserva['objetivo_visita']))
                                    <dt class="col-sm-6">
                                        Objetivo de la visita
                                    </dt>

                                    <dd class="col-sm-6">
                                        {{ $reserva['objetivo_visita'] }}
                                    </dd>
                                @endif

                            @endif

                        </dl>

                    </div>

                </div>

            </div>

        </div>

        {{-- Servicios seleccionados --}}
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-concierge-bell mr-2"></i>
                    Servicios seleccionados
                </h3>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Servicio</th>

                            @if ($tipoOperacion === 'RESERVA')
                                <th>Horario</th>
                            @endif

                            <th>Tipo de cobro</th>

                            <th class="text-right">
                                Precio
                            </th>

                            <th class="text-right">
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($calculo['servicios'] as $detalle)
                            <tr>

                                {{-- SERVICIO --}}
                                <td>
                                    <strong>
                                        {{ $detalle['servicio']->nombre }}
                                    </strong>

                                    @if ($detalle['servicio']->tipo_cobro === 'POR_PERSONA' && $detalle['entradas_liberadas'] > 0)
                                        <div class="text-muted small mt-1">

                                            ${{ number_format($detalle['precio'], 0, ',', '.') }}

                                            ×

                                            {{ $detalle['personas_pagadas'] }}
                                            personas

                                            ·

                                            {{ $detalle['entradas_liberadas'] }}

                                            entrada{{ $detalle['entradas_liberadas'] > 1 ? 's' : '' }}

                                            liberada{{ $detalle['entradas_liberadas'] > 1 ? 's' : '' }}

                                        </div>
                                    @endif
                                </td>


                                {{-- HORARIO --}}
                                @if ($esReserva)
                                    <td>
                                        @if (!empty($detalle['horario']))
                                            <span class="badge badge-info p-2">

                                                <i class="far fa-clock mr-1"></i>

                                                {{ substr($detalle['horario']->hora_inicio, 0, 5) }}

                                                -

                                                {{ substr($detalle['horario']->hora_termino, 0, 5) }}

                                            </span>
                                        @else
                                            <span class="text-muted">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                @endif


                                {{-- TIPO DE COBRO --}}
                                <td>
                                    @if ($detalle['servicio']->tipo_cobro === 'POR_PERSONA')
                                        Por persona
                                    @else
                                        Por grupo
                                    @endif
                                </td>


                                {{-- PRECIO --}}
                                <td class="text-right">
                                    ${{ number_format($detalle['precio'], 0, ',', '.') }}
                                </td>


                                {{-- SUBTOTAL --}}
                                <td class="text-right">
                                    <strong>
                                        ${{ number_format($detalle['subtotal'], 0, ',', '.') }}
                                    </strong>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Resumen total --}}
        {{-- Resumen total --}}
        <div class="row justify-content-end">
            <div class="col-lg-5">

                <div class="card card-outline card-warning">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-receipt mr-2"></i>

                            @if ($esCotizacion)
                                Resumen de cotización
                            @else
                                Resumen de pago
                            @endif
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between align-items-center pb-3">
                            <span class="text-muted">
                                Subtotal
                            </span>

                            <strong>
                                ${{ number_format($subtotalGeneral, 0, ',', '.') }}
                            </strong>
                        </div>


                        {{-- Entradas liberadas --}}
                        @if ($entradasLiberadas > 0)
                            <div class="d-flex justify-content-between align-items-center py-3 border-top">

                                <div>
                                    <i class="fas fa-ticket-alt text-success mr-1"></i>
                                    <span>
                                        Entradas liberadas
                                    </span>
                                </div>

                                <span class="badge badge-success p-2">
                                    {{ $entradasLiberadas }}
                                    entrada{{ $entradasLiberadas > 1 ? 's' : '' }}
                                    por servicio
                                </span>

                            </div>
                        @endif


                        {{-- Convenio --}}
                        @if ($convenio)
                            <div class="border-top pt-3">

                                <div class="alert alert-success mb-3">
                                    <div class="font-weight-bold">
                                        <i class="fas fa-percent mr-1"></i>
                                        Convenio aplicado
                                    </div>

                                    <div class="mt-2">
                                        {{ data_get($convenio, 'nombre') }}
                                    </div>

                                    <small>
                                        Código:
                                        <strong>
                                            {{ data_get($convenio, 'codigo') }}
                                        </strong>

                                        · Descuento:
                                        <strong>
                                            {{ number_format($porcentajeDescuento, 0) }}%
                                        </strong>
                                    </small>
                                </div>


                                <div class="d-flex justify-content-between align-items-center pb-3">

                                    <span>
                                        Descuento
                                    </span>

                                    <strong class="text-success">
                                        -${{ number_format($descuentoTotal, 0, ',', '.') }}
                                    </strong>

                                </div>

                            </div>
                        @endif


                        {{-- Total --}}
                        <div class="border-top pt-3">

                            <div class="d-flex justify-content-between align-items-center">

                                <span class="h5 mb-0">
                                    @if ($esCotizacion)
                                        Total estimado
                                    @else
                                        Total
                                    @endif
                                </span>

                                <strong class="h4 mb-0 text-primary">
                                    ${{ number_format($total, 0, ',', '.') }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- Botones --}}
        <div class="d-flex flex-column flex-sm-row
                   justify-content-between mb-4">
            <a href="{{ route('reservas.datos') }}" class="btn btn-default mb-2 mb-sm-0">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver a la reserva
            </a>

            <form action="{{ route('reservas.finalizar') }}" method="POST" id="form-confirmar-reserva">
                @csrf

                <button type="submit" class="btn btn-success btn-lg" id="btn-confirmar-reserva">
                    <i class="fas fa-check-circle mr-1"></i>
                    Confirmar reserva
                </button>
            </form>
        </div>

    </div>
@stop

@section('css')
    <style>
        .wizard-circle {
            align-items: center;
            background-color: #dee2e6;
            border-radius: 50%;
            color: #6c757d;
            display: inline-flex;
            font-size: 16px;
            font-weight: 700;
            height: 42px;
            justify-content: center;
            margin-bottom: 8px;
            width: 42px;
        }

        .wizard-step.completed .wizard-circle {
            background-color: #28a745;
            color: #fff;
        }

        .wizard-step.active .wizard-circle {
            background-color: #007bff;
            color: #fff;
        }

        .wizard-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
        }

        .wizard-step.active .wizard-label,
        .wizard-step.completed .wizard-label {
            color: #343a40;
        }

        .total-reserva {
            font-size: 1.35rem;
        }

        .card.h-100 {
            height: 100%;
        }

        dt {
            color: #495057;
        }

        dd {
            overflow-wrap: anywhere;
        }

        @media (max-width: 575.98px) {
            .wizard-label {
                font-size: 12px;
            }

            .wizard-circle {
                height: 36px;
                width: 36px;
            }

            #form-confirmar-reserva,
            #btn-confirmar-reserva {
                width: 100%;
            }
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {
                const formulario = document.getElementById(
                    'form-confirmar-reserva'
                );

                const boton = document.getElementById(
                    'btn-confirmar-reserva'
                );

                if (!formulario || !boton) {
                    return;
                }

                formulario.addEventListener(
                    'submit',
                    function() {
                        boton.disabled = true;

                        boton.innerHTML = `
                            <span
                                class="spinner-border spinner-border-sm mr-2"
                                role="status"
                                aria-hidden="true"
                            ></span>
                            Registrando reserva...
                        `;
                    }
                );
            }
        );
    </script>
@stop
