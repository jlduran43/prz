@extends('adminlte::page')

@section('title', 'Nueva reserva - Cliente')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1">Nueva reserva</h1>

            <p class="text-muted mb-0">
                Completa los datos del cliente que solicita la reserva.
            </p>
        </div>
    </div>
@stop

@section('content')

    @if (session('reserva.tipo_operacion') === 'COTIZACION')
        <div class="alert alert-info py-2">

            <i class="fas fa-file-invoice-dollar mr-1"></i>

            <strong>Modalidad:</strong>
            Solicitud de cotización

        </div>
    @elseif (session('reserva.tipo_operacion') === 'PAGO')
        <div class="alert alert-success py-2">

            <i class="fas fa-credit-card mr-1"></i>

            <strong>Modalidad:</strong>
            Reserva con pago

        </div>
    @endif
    @if (session('conversion_cotizacion_id'))
        <div class="alert alert-success py-2">

            <i class="fas fa-calendar-check mr-1"></i>

            <strong>
                Reserva desde cotización:
            </strong>

            Los datos del cliente fueron cargados
            automáticamente desde la cotización.

        </div>
    @endif

    <form method="POST" action="{{ route('reservas.cliente.guardar') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Revisa los campos marcados antes de continuar.
            </div>
        @endif

        {{-- Tipo de cliente --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    Tipo de cliente
                </h3>
            </div>

            <div class="card-body">
                @php
                    $tipos = $tiposCliente->sortBy(function ($tipo) {
                        return $tipo->codigo === 'PERSONA' ? 0 : 1;
                    });
                @endphp

                <div class="form-group">
                    <label for="tipo_cliente_id">
                        Tipo de cliente
                        <span class="text-danger">*</span>
                    </label>

                    <select id="tipo_cliente_id" name="tipo_cliente_id"
                        class="form-control @error('tipo_cliente_id') is-invalid @enderror" required>
                        <option value="">
                            Seleccione un tipo de cliente
                        </option>

                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}" data-codigo="{{ $tipo->codigo }}"
                                data-estructura="{{ $tipo->tipo_estructura }}" @selected(old('tipo_cliente_id', $datosCliente['tipo_cliente_id'] ?? '') == $tipo->id)>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <small id="descripcion-tipo" class="form-text text-muted mt-2">
                        Selecciona el tipo de cliente que realizará la reserva.
                    </small>

                    @error('tipo_cliente_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Datos de persona --}}
        <div id="campos-persona" class="card card-info" hidden>
            <div class="card-header">
                <h3 class="card-title">
                    Datos de la persona
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nombres">
                                Nombres
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <input id="nombres" name="nombres" type="text"
                                    class="form-control @error('nombres') is-invalid @enderror"
                                    value="{{ old('nombres', $datosCliente['nombres'] ?? '') }}"
                                    placeholder="Ej.: María José">
                            </div>

                            @error('nombres')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="apellidos">
                                Apellidos
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <input id="apellidos" name="apellidos" type="text"
                                    class="form-control @error('apellidos') is-invalid @enderror"
                                    value="{{ old('apellidos', $datosCliente['apellidos'] ?? '') }}"
                                    placeholder="Ej.: González Pérez">
                            </div>

                            @error('apellidos')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rut_persona">
                                RUT de la persona
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group rut-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                </div>

                                <input id="rut_persona" name="rut_persona" type="text"
                                    class="form-control rut-chileno @error('rut_persona') is-invalid @enderror"
                                    value="{{ old('rut_persona', $datosCliente['rut_persona'] ?? '') }}"
                                    placeholder="12.345.678-5" maxlength="12" autocomplete="off">

                                <div class="input-group-append">
                                    <span class="input-group-text rut-estado">
                                        <i class="fas fa-minus text-muted"></i>
                                    </span>
                                </div>

                                @error('rut_persona')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <small class="form-text text-muted rut-mensaje">
                                Ingresa el RUT con o sin puntos.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Datos de la organización --}}
        <div id="campos-entidad" class="card card-info" hidden>
            <div class="card-header">
                <h3 id="titulo-datos-entidad" class="card-title">
                    Datos de la organización
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label id="label-nombre-entidad" for="nombre_entidad">
                                Nombre de la organización
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                </div>

                                <input id="nombre_entidad" name="nombre_entidad" type="text"
                                    class="form-control @error('nombre_entidad') is-invalid @enderror"
                                    value="{{ old('nombre_entidad', $datosCliente['nombre_entidad'] ?? '') }}"
                                    placeholder="Ingrese el nombre">
                            </div>

                            @error('nombre_entidad')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div id="contenedor-rut-entidad" class="col-md-6">
                        <div class="form-group">
                            <label id="label-rut-entidad" for="rut_entidad">
                                RUT de la organización
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group rut-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                </div>

                                <input id="rut_entidad" name="rut_entidad" type="text"
                                    class="form-control rut-chileno @error('rut_entidad') is-invalid @enderror"
                                    value="{{ old('rut_entidad', $datosCliente['rut_entidad'] ?? '') }}"
                                    placeholder="76.543.210-K" maxlength="12" autocomplete="off">

                                <div class="input-group-append">
                                    <span class="input-group-text rut-estado">
                                        <i class="fas fa-minus text-muted"></i>
                                    </span>
                                </div>

                                @error('rut_entidad')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <small class="form-text text-muted rut-mensaje">
                                Ingresa el RUT con o sin puntos.
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_encargado">
                                Nombre del encargado
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <input id="nombre_encargado" name="nombre_encargado" type="text"
                                    class="form-control @error('nombre_encargado') is-invalid @enderror"
                                    value="{{ old('nombre_encargado', $datosCliente['nombre_encargado'] ?? '') }}"
                                    placeholder="Nombre completo">
                            </div>

                            @error('nombre_encargado')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rut_encargado">
                                RUT del encargado
                                <small class="text-muted">(opcional)</small>
                            </label>

                            <div class="input-group rut-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user-shield"></i>
                                    </span>
                                </div>

                                <input id="rut_encargado" name="rut_encargado" type="text"
                                    class="form-control rut-chileno @error('rut_encargado') is-invalid @enderror"
                                    value="{{ old('rut_encargado', $datosCliente['rut_encargado'] ?? '') }}"
                                    placeholder="12.345.678-5" maxlength="12" autocomplete="off">

                                <div class="input-group-append">
                                    <span class="input-group-text rut-estado">
                                        <i class="fas fa-minus text-muted"></i>
                                    </span>
                                </div>

                                @error('rut_encargado')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <small class="form-text text-muted rut-mensaje">
                                Ingresa el RUT con o sin puntos.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contacto y ubicación --}}
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    Contacto y ubicación
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">
                                Teléfono
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                </div>

                                <input id="telefono" name="telefono" type="tel"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono', $datosCliente['telefono'] ?? '') }}"
                                    placeholder="+56 9 1234 5678" required>
                            </div>

                            @error('telefono')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">
                                Correo electrónico
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>

                                <input id="email" name="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $datosCliente['email'] ?? '') }}"
                                    placeholder="correo@ejemplo.cl" required>
                            </div>

                            @error('email')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="region_id">
                                Región
                                <span class="text-danger">*</span>
                            </label>

                            <select id="region_id" name="region_id"
                                class="form-control @error('region_id') is-invalid @enderror" required>
                                <option value="">
                                    Seleccione una región
                                </option>

                                @foreach ($regiones as $region)
                                    <option value="{{ $region->id }}" @selected(old('region_id', $datosCliente['region_id'] ?? '') == $region->id)>
                                        {{ $region->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @error('region_id')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="comuna_id">
                                Comuna
                                <span class="text-danger">*</span>
                            </label>

                            <select id="comuna_id" name="comuna_id"
                                class="form-control @error('comuna_id') is-invalid @enderror"
                                data-selected="{{ old('comuna_id', $datosCliente['comuna_id'] ?? '') }}" required>
                                <option value="">
                                    Seleccione una región primero
                                </option>
                            </select>

                            @error('comuna_id')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">

                <a href="{{ route('reservas.operacion') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>

                <button type="submit" class="btn btn-primary ml-auto">
                    Siguiente
                    <i class="fas fa-arrow-right ml-1"></i>
                </button>

            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/reservas/wizard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rut-chileno.css') }}?v={{ time() }}">
@stop
@section('js')
    <script src="{{ asset('js/paso1.js') }}"></script>
@stop
