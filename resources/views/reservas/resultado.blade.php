@extends('adminlte::page')

@section('title', 'Solicitud de reserva registrada')

@section('content_header')

    <div>
        <h1 class="mb-1">
            Solicitud de reserva registrada
        </h1>

        <p class="text-muted mb-0">
            Tu solicitud fue registrada correctamente.
        </p>
    </div>

@stop


@section('content')

    {{-- MENSAJE DE ÉXITO --}}
    @if (session('success'))
        <div class="alert alert-success">

            <i class="fas fa-check-circle mr-2"></i>

            {{ session('success') }}

        </div>
    @endif


    {{-- CABECERA --}}
    <div class="card card-outline card-success">

        <div class="card-body text-center py-4">

            <div class="
                    d-inline-flex
                    align-items-center
                    justify-content-center
                    bg-success
                    rounded-circle
                    mb-3
                "
                style="
                    width: 70px;
                    height: 70px;
                    font-size: 30px;
                ">
                <i class="fas fa-check text-white"></i>
            </div>

            <h3 class="font-weight-bold mb-2">

                Solicitud N.º
                {{ $reserva->id }}

            </h3>

            <p class="text-muted mb-3">

                Registrada el

                {{ optional($reserva->created_at)->format('d/m/Y \a \l\a\s H:i') }}

            </p>

            <span class="badge badge-warning px-3 py-2">

                {{ $reserva->estado }}

            </span>

        </div>

    </div>


    <div class="row">

        {{-- DATOS CLIENTE --}}
        <div class="col-lg-6">

            <div class="card card-outline card-primary h-100">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-user mr-2"></i>

                        Datos del cliente

                    </h3>

                </div>

                <div class="card-body">

                    @if ($reserva->tipoCliente)
                        <div class="row mb-2">

                            <div class="col-sm-5 font-weight-bold">
                                Tipo de cliente
                            </div>

                            <div class="col-sm-7">
                                {{ $reserva->tipoCliente->nombre }}
                            </div>

                        </div>
                    @endif


                    @if ($reserva->nombres || $reserva->apellidos)
                        <div class="row mb-2">

                            <div class="col-sm-5 font-weight-bold">
                                Nombre
                            </div>

                            <div class="col-sm-7">

                                {{ trim(($reserva->nombres ?? '') . ' ' . ($reserva->apellidos ?? '')) }}

                            </div>

                        </div>
                    @endif


                    @if ($reserva->nombre_entidad)
                        <div class="row mb-2">

                            <div class="col-sm-5 font-weight-bold">
                                Entidad
                            </div>

                            <div class="col-sm-7">
                                {{ $reserva->nombre_entidad }}
                            </div>

                        </div>
                    @endif


                    <div class="row mb-2">

                        <div class="col-sm-5 font-weight-bold">
                            Correo electrónico
                        </div>

                        <div class="col-sm-7">
                            {{ $reserva->email }}
                        </div>

                    </div>


                    <div class="row mb-2">

                        <div class="col-sm-5 font-weight-bold">
                            Teléfono
                        </div>

                        <div class="col-sm-7">
                            {{ $reserva->telefono }}
                        </div>

                    </div>


                    @if ($reserva->region)
                        <div class="row mb-2">

                            <div class="col-sm-5 font-weight-bold">
                                Región
                            </div>

                            <div class="col-sm-7">
                                {{ $reserva->region->nombre }}
                            </div>

                        </div>
                    @endif


                    @if ($reserva->comuna)
                        <div class="row">

                            <div class="col-sm-5 font-weight-bold">
                                Comuna
                            </div>

                            <div class="col-sm-7">
                                {{ $reserva->comuna->nombre }}
                            </div>

                        </div>
                    @endif

                </div>

            </div>

        </div>


        {{-- DATOS RESERVA --}}
        <div class="col-lg-6">

            <div class="card card-outline card-info h-100">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-calendar-check mr-2"></i>

                        Datos de la reserva

                    </h3>

                </div>

                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-sm-5 font-weight-bold">
                            Fecha
                        </div>

                        <div class="col-sm-7">

                            {{ optional($reserva->fecha)->format('d/m/Y') }}

                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-5 font-weight-bold">
                            Cantidad de asistentes
                        </div>

                        <div class="col-sm-7">

                            {{ $reserva->cantidad_asistentes }}

                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-5 font-weight-bold">
                            Estado
                        </div>

                        <div class="col-sm-7">

                            <span class="badge badge-warning">
                                {{ $reserva->estado }}
                            </span>

                        </div>

                    </div>


                    @if ($reserva->cotizacion_id)
                        <div class="row">

                            <div class="col-sm-5 font-weight-bold">
                                Cotización asociada
                            </div>

                            <div class="col-sm-7">

                                COT-{{ str_pad($reserva->cotizacion_id, 6, '0', STR_PAD_LEFT) }}

                            </div>

                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- SERVICIOS --}}
    <div class="card card-outline card-success mt-4">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-concierge-bell mr-2"></i>

                Servicios reservados

            </h3>

        </div>


        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="thead-light">

                    <tr>

                        <th>Servicio</th>

                        <th>Fecha</th>

                        <th>Horario</th>

                        <th class="text-right">
                            Precio
                        </th>

                        <th class="text-right">
                            Subtotal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($reserva->servicios as $servicio)
                        @php

                            $horarioId = $servicio->pivot->horario_disponible_id;

                            $horario = $horarios->get($horarioId);

                        @endphp


                        <tr>

                            <td class="font-weight-bold">

                                {{ $servicio->nombre }}

                            </td>


                            <td>

                                {{ \Carbon\Carbon::parse($servicio->pivot->fecha)->format('d/m/Y') }}

                            </td>


                            <td>

                                @if ($horario)
                                    <span class="badge badge-info">

                                        <i class="far fa-clock mr-1"></i>

                                        {{ substr($horario->hora_inicio, 0, 5) }}

                                        -

                                        {{ substr($horario->hora_termino, 0, 5) }}

                                    </span>
                                @else
                                    <span class="text-muted">
                                        -
                                    </span>
                                @endif

                            </td>


                            <td class="text-right">

                                ${{ number_format($servicio->pivot->precio, 0, ',', '.') }}

                            </td>


                            <td class="text-right font-weight-bold">

                                ${{ number_format($servicio->pivot->subtotal, 0, ',', '.') }}

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>


    {{-- RESUMEN --}}
    <div class="row justify-content-end">

        <div class="col-lg-5">

            <div class="card card-outline card-warning">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-receipt mr-2"></i>

                        Resumen

                    </h3>

                </div>


                <div class="card-body">

                    <div
                        class="
                            d-flex
                            justify-content-between
                            mb-3
                        ">

                        <span>
                            Subtotal
                        </span>

                        <strong>

                            ${{ number_format($reserva->subtotal, 0, ',', '.') }}

                        </strong>

                    </div>


                    @if ($reserva->descuento > 0)
                        <div
                            class="
                                d-flex
                                justify-content-between
                                mb-3
                                text-success
                            ">

                            <span>
                                Descuento
                            </span>

                            <strong>

                                -${{ number_format($reserva->descuento, 0, ',', '.') }}

                            </strong>

                        </div>
                    @endif


                    <hr>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        ">

                        <strong class="h5 mb-0">
                            Total
                        </strong>

                        <strong
                            class="
                                text-primary
                                h3
                                mb-0
                            ">

                            ${{ number_format($reserva->total, 0, ',', '.') }}

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- INFORMACIÓN --}}
    <div class="alert alert-info">

        <i class="fas fa-info-circle mr-2"></i>

        <strong>
            Tu solicitud de reserva fue registrada correctamente.
        </strong>

        Los antecedentes quedaron registrados y serán gestionados
        de acuerdo con el proceso de reservas del Parque.

    </div>


    {{-- ÚNICO BOTÓN --}}
    <div class="text-center mb-4">

        <a href="{{ route('reservas.operacion') }}" class="btn btn-secondary">

            <i class="fas fa-home mr-1"></i>

            Volver al inicio

        </a>

    </div>

@stop