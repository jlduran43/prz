@extends('adminlte::page')

@section('title', 'Cotización ' . $cotizacion->folio)


@section('content_header')

    <div>
        <h1 class="m-0">
            Cotización generada
        </h1>

        <small class="text-muted">
            La cotización fue registrada correctamente.
        </small>
    </div>

@stop


@section('content')
    @php
        $tipoEstructura = $cotizacion->tipoCliente->tipo_estructura ?? null;

        $esPersona = $tipoEstructura === 'PERSONA';

        $esEstablecimiento = $tipoEstructura === 'ESTABLECIMIENTO';

        $esOrganizacion = $tipoEstructura === 'ORGANIZACION';
    @endphp

    @if (session('success'))
        <div
            class="
                alert
                alert-success
                alert-dismissible
                fade
                show
            ">

            <i class="fas fa-check-circle mr-2"></i>

            {{ session('success') }}

            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span>&times;</span>
            </button>

        </div>
    @endif


    {{-- ============================================ --}}
    {{-- FOLIO --}}
    {{-- ============================================ --}}

    <div class="card card-outline card-success">

        <div class="card-body text-center py-4">

            <div class="mb-2">
                <span class="text-muted">
                    Número de cotización
                </span>
            </div>

            <h2 class="font-weight-bold text-success mb-2">
                {{ $cotizacion->folio }}
            </h2>

            <div class="text-muted">

                Emitida el

                {{ $cotizacion->fecha_emision->format('d/m/Y') }}

                a las

                {{ $cotizacion->fecha_emision->format('H:i') }}

            </div>

            <div class="mt-3">

                <span class="badge badge-info p-2">
                    {{ ucfirst(strtolower($cotizacion->estado)) }}
                </span>

            </div>

        </div>

    </div>


    <div class="row">

        {{-- ============================================ --}}
        {{-- CLIENTE --}}
        {{-- ============================================ --}}

        <div class="col-lg-6">

            <div class="card card-outline card-info h-100">

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
                            {{ $cotizacion->tipoCliente->nombre ?? '-' }}
                        </dd>


                        @if ($esPersona)
                            <dt class="col-sm-5">
                                Nombre
                            </dt>

                            <dd class="col-sm-7">
                                {{ trim($cotizacion->nombres . ' ' . $cotizacion->apellidos) }}
                            </dd>

                            <dt class="col-sm-5">
                                RUT
                            </dt>

                            <dd class="col-sm-7">
                                {{ $cotizacion->rut_persona }}
                            </dd>
                        @elseif ($esEstablecimiento || $esOrganizacion)
                            <dt class="col-sm-5">
                                Entidad
                            </dt>

                            <dd class="col-sm-7">
                                {{ $cotizacion->nombre_entidad ?? '-' }}
                            </dd>

                            <dt class="col-sm-5">
                                RUT entidad
                            </dt>

                            <dd class="col-sm-7">
                                {{ $cotizacion->rut_entidad ?? '-' }}
                            </dd>

                            <dt class="col-sm-5">
                                Encargado
                            </dt>

                            <dd class="col-sm-7">
                                {{ $cotizacion->nombre_encargado ?? '-' }}
                            </dd>
                        @endif


                        <dt class="col-sm-5">
                            Correo electrónico
                        </dt>

                        <dd class="col-sm-7">
                            {{ $cotizacion->email }}
                        </dd>


                        <dt class="col-sm-5">
                            Teléfono
                        </dt>

                        <dd class="col-sm-7">
                            {{ $cotizacion->telefono }}
                        </dd>


                        <dt class="col-sm-5">
                            Región
                        </dt>

                        <dd class="col-sm-7">
                            {{ $cotizacion->region->nombre ?? '-' }}
                        </dd>


                        <dt class="col-sm-5">
                            Comuna
                        </dt>

                        <dd class="col-sm-7">
                            {{ $cotizacion->comuna->nombre ?? '-' }}
                        </dd>

                    </dl>

                </div>

            </div>

        </div>


        {{-- ============================================ --}}
        {{-- RESUMEN --}}
        {{-- ============================================ --}}

        <div class="col-lg-6 mt-3 mt-lg-0">

            <div class="card card-outline card-warning h-100">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-receipt mr-2"></i>

                        Resumen de cotización

                    </h3>

                </div>


                <div class="card-body">

                    <div
                        class="
                            d-flex
                            justify-content-between
                            pb-3
                        ">

                        <span>
                            Cantidad de asistentes
                        </span>

                        <strong>
                            {{ $cotizacion->cantidad_asistentes }}
                        </strong>

                    </div>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            py-3
                            border-top
                        ">

                        <span>
                            Subtotal
                        </span>

                        <strong>
                            ${{ number_format($cotizacion->subtotal, 0, ',', '.') }}
                        </strong>

                    </div>


                    @if ($cotizacion->descuento > 0)
                        <div class="border-top pt-3">

                            <div
                                class="
                                    alert
                                    alert-success
                                    py-2
                                    mb-3
                                ">

                                <div class="font-weight-bold">

                                    <i class="fas fa-percent mr-1"></i>

                                    Convenio aplicado

                                </div>

                                <div class="mt-1">
                                    {{ $cotizacion->nombre_convenio }}
                                </div>

                                <small>

                                    Código:

                                    <strong>
                                        {{ $cotizacion->codigo_convenio }}
                                    </strong>

                                    ·

                                    {{ number_format($cotizacion->porcentaje_descuento, 0) }}%

                                </small>

                            </div>


                            <div
                                class="
                                    d-flex
                                    justify-content-between
                                    pb-3
                                ">

                                <span>
                                    Descuento
                                </span>

                                <strong class="text-success">

                                    -${{ number_format($cotizacion->descuento, 0, ',', '.') }}

                                </strong>

                            </div>

                        </div>
                    @endif


                    <div class="border-top pt-3">

                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-center
                            ">

                            <span class="h5 mb-0">
                                Total estimado
                            </span>

                            <strong
                                class="
                                    h3
                                    mb-0
                                    text-primary
                                ">

                                ${{ number_format($cotizacion->total, 0, ',', '.') }}

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================ --}}
    {{-- SERVICIOS --}}
    {{-- ============================================ --}}

    <div class="card card-outline card-success mt-3">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-concierge-bell mr-2"></i>

                Servicios cotizados

            </h3>

        </div>


        <div class="card-body table-responsive p-0">

            <table class="table table-hover">

                <thead class="thead-light">

                    <tr>

                        <th>
                            Servicio
                        </th>

                        <th>
                            Tipo de cobro
                        </th>

                        <th class="text-right">
                            Precio
                        </th>

                        <th class="text-center">
                            Pagadas
                        </th>

                        <th class="text-center">
                            Liberadas
                        </th>

                        <th class="text-right">
                            Subtotal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($cotizacion->servicios as $detalle)
                        <tr>

                            <td>

                                <strong>
                                    {{ $detalle->nombre_servicio }}
                                </strong>

                            </td>


                            <td>

                                {{ $detalle->tipo_cobro === 'POR_PERSONA' ? 'Por persona' : 'Por grupo' }}

                            </td>


                            <td class="text-right">

                                ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}

                            </td>


                            <td class="text-center">

                                @if ($detalle->tipo_cobro === 'POR_PERSONA')
                                    {{ $detalle->personas_pagadas }}
                                @else
                                    -
                                @endif

                            </td>


                            <td class="text-center">

                                @if ($detalle->entradas_liberadas > 0)
                                    <span
                                        class="
                                            badge
                                            badge-success
                                            p-2
                                        ">

                                        {{ $detalle->entradas_liberadas }}

                                    </span>
                                @else
                                    -
                                @endif

                            </td>


                            <td class="text-right">

                                <strong>

                                    ${{ number_format($detalle->subtotal, 0, ',', '.') }}

                                </strong>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>


    {{-- ============================================ --}}
    {{-- ACCIONES --}}
    {{-- ============================================ --}}

    <div
        class="
            d-flex
            flex-column
            flex-md-row
            justify-content-between
            mb-4
        ">

        <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary mb-2 mb-md-0">

            <i class="fas fa-arrow-left mr-1"></i>

            Volver a cotizaciones

        </a>


        <div>

            {{-- Próxima etapa --}}
            <a href="{{ route('cotizaciones.pdf', $cotizacion) }}" class="btn btn-danger mr-1"
                title="Descargar cotización en PDF">
                <i class="fas fa-file-pdf mr-1"></i>
                Descargar PDF
            </a>


            {{-- Próxima etapa --}}
            <button type="button" class="btn btn-info" disabled title="Se implementará en la siguiente etapa">

                <i class="fas fa-envelope mr-1"></i>

                Enviar por correo

            </button>

        </div>

    </div>

@stop
