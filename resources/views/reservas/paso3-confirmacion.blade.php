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

                            <dt class="col-sm-6">
                                Cantidad de asistentes
                            </dt>

                            <dd class="col-sm-6">
                                {{ $reserva['cantidad_asistentes'] ?? 0 }}
                            </dd>


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
        <div class="card card-outline card-success mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-concierge-bell mr-2"></i>
                    Servicios seleccionados
                </h3>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>
                                    Servicio
                                </th>

                                <th>
                                    Horario
                                </th>

                                <th>
                                    Tipo de cobro
                                </th>

                                <th class="text-right">
                                    Precio
                                </th>

                                <th class="text-right">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($detallesServicios as $detalle)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">
                                            {{ $detalle['servicio']->nombre }}
                                        </div>
                                    </td>

                                    <td>

                                        @if ($esReserva)
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
                                        @else
                                            <span class="text-muted">
                                                No aplica
                                            </span>
                                        @endif

                                    </td>

                                    <td>
                                        @switch($detalle['tipo_cobro'])
                                            @case('POR_PERSONA')
                                                Por persona
                                            @break

                                            @case('POR_GRUPO')
                                                Por grupo
                                            @break

                                            @case('FIJO')
                                                Precio fijo
                                            @break

                                            @default
                                                {{ $detalle['tipo_cobro'] }}
                                        @endswitch
                                    </td>

                                    <td class="text-right">
                                        $
                                        {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}
                                    </td>

                                    <td class="text-right font-weight-bold">
                                        $
                                        {{ number_format($detalle['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Resumen total --}}
        @php
            /*
             * Por ahora todavía no tenemos implementado
             * el módulo de convenios y descuentos.
             */
            $descuentoTotal = 0;

            /*
             * $total ya viene calculado desde el Controller
             * como la suma de los subtotales de los servicios.
             */
            $subtotalGeneral = $total;

            $totalFinal = $subtotalGeneral - $descuentoTotal;
        @endphp

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

                        <div class="d-flex justify-content-between
                           align-items-center mb-2">

                            <span>
                                Subtotal
                            </span>

                            <strong>
                                ${{ number_format($subtotalGeneral, 0, ',', '.') }}
                            </strong>

                        </div>


                        @if ($descuentoTotal > 0)
                            <div
                                class="d-flex justify-content-between
                               align-items-center mb-3">

                                <span>
                                    Descuento
                                </span>

                                <strong class="text-success">
                                    -${{ number_format($descuentoTotal, 0, ',', '.') }}
                                </strong>

                            </div>
                        @endif


                        <hr>


                        <div
                            class="d-flex justify-content-between
                           align-items-center total-reserva">

                            <span>
                                @if ($esCotizacion)
                                    Total estimado
                                @else
                                    Total
                                @endif
                            </span>

                            <strong>
                                ${{ number_format($totalFinal, 0, ',', '.') }}
                            </strong>

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
