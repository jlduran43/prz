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

        {{-- <a
            href="{{ route('reservas.index') }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left"></i>
            Volver
        </a> --}}

    </div>
@stop

@section('content')

    @include('reservas.partials._wizard', ['paso' => 1])

    <form method="POST" action="{{ route('reservas.cliente.guardar') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
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
                        <option value="">Seleccione un tipo de cliente</option>

                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}" data-codigo="{{ $tipo->codigo }}"
                                data-estructura="{{ $tipo->tipo_estructura }}"
                                {{ old('tipo_cliente_id', $datosCliente['tipo_cliente_id'] ?? '') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach

                    </select>

                    <small id="descripcion-tipo" class="form-text text-muted mt-2">
                        Seleccione el tipo de cliente que realizará la reserva.
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
                <h3 class="card-title">Datos de la persona</h3>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nombres">
                                Nombres
                                <span class="text-danger">*</span>
                            </label>

                            <input id="nombres" name="nombres" type="text"
                                class="form-control @error('nombres') is-invalid @enderror"
                                value="{{ old('nombres', $datosCliente['nombres'] ?? '') }}" placeholder="Ej.: María José">

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

                            <input id="apellidos" name="apellidos" type="text"
                                class="form-control @error('apellidos') is-invalid @enderror"
                                value="{{ old('apellidos', $datosCliente['apellidos'] ?? '') }}"
                                placeholder="Ej.: González Pérez">

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

                            <input id="rut_persona" name="rut_persona" type="text"
                                class="form-control @error('rut_persona') is-invalid @enderror"
                                value="{{ old('rut_persona', $datosCliente['rut_persona'] ?? '') }}"
                                placeholder="12.345.678-9">

                            @error('rut_persona')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
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

                            <input id="nombre_entidad" name="nombre_entidad" type="text"
                                class="form-control
                            @error('nombre_entidad') is-invalid @enderror"
                                value="{{ old('nombre_entidad', $datosCliente['nombre_entidad'] ?? '') }}"
                                placeholder="Ingrese el nombre">

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

                            <input id="rut_entidad" name="rut_entidad" type="text"
                                class="form-control
                            @error('rut_entidad') is-invalid @enderror"
                                value="{{ old('rut_entidad', $datosCliente['rut_entidad'] ?? '') }}"
                                placeholder="76.543.210-K">

                            @error('rut_entidad')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_encargado">
                                Nombre del encargado
                                <span class="text-danger">*</span>
                            </label>

                            <input id="nombre_encargado" name="nombre_encargado" type="text"
                                class="form-control
                            @error('nombre_encargado') is-invalid @enderror"
                                value="{{ old('nombre_encargado', $datosCliente['nombre_encargado'] ?? '') }}"
                                placeholder="Nombre completo">

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
                                <span class="text-danger">*</span>
                            </label>

                            <input id="rut_encargado" name="rut_encargado" type="text"
                                class="form-control
                            @error('rut_encargado') is-invalid @enderror"
                                value="{{ old('rut_encargado', $datosCliente['rut_encargado'] ?? '') }}"
                                placeholder="12.345.678-9">

                            @error('rut_encargado')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Contacto y ubicación --}}
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Contacto y ubicación</h3>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">
                                Teléfono
                                <span class="text-danger">*</span>
                            </label>

                            <input id="telefono" name="telefono" type="tel"
                                class="form-control @error('telefono') is-invalid @enderror"
                                value="{{ old('telefono', $datosCliente['telefono'] ?? '') }}"
                                placeholder="+56 9 1234 5678">

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

                            <input id="email" name="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $datosCliente['email'] ?? '') }}" placeholder="correo@ejemplo.cl">

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
                                class="form-control @error('region_id') is-invalid @enderror">
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
                                data-selected="{{ old('comuna_id', $datosCliente['comuna_id'] ?? '') }}">
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

            <div class="card-footer text-right">
                <button class="btn btn-primary" type="submit">
                    Siguiente
                    <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>

    </form>

@stop

@section('css')
    <style>
        .client-type-option {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 16px 16px 16px 42px;
            background: #f8f9fa;
            min-height: 84px;
        }

        .client-type-option:hover {
            border-color: #007bff;
            background: #f1f7ff;
        }

        .client-type-option .custom-control-label {
            cursor: pointer;
            width: 100%;
        }

        .client-type-option .custom-control-label::before,
        .client-type-option .custom-control-label::after {
            top: 4px;
        }

        .client-card {

            cursor: pointer;

            margin: 0;

        }

        .client-card-body {

            border: 2px solid #dee2e6;

            border-radius: 8px;

            padding: 20px;

            display: flex;

            align-items: center;

            gap: 20px;

            transition: .25s;

            background: white;

            min-height: 120px;

        }

        .client-card:hover .client-card-body {

            border-color: #007bff;

            box-shadow: 0 0 12px rgba(0, 123, 255, .15);

        }

        .client-icon {

            width: 60px;

            text-align: center;

            color: #007bff;

        }

        .client-card.selected .client-card-body {

            border-color: #007bff;

            background: #eef6ff;

        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoCliente =
                document.getElementById('tipo_cliente_id');

            const camposPersona =
                document.getElementById('campos-persona');

            const camposEntidad =
                document.getElementById('campos-entidad');

            const tituloDatosEntidad =
                document.getElementById('titulo-datos-entidad');

            const labelNombreEntidad =
                document.getElementById('label-nombre-entidad');

            const labelRutEntidad =
                document.getElementById('label-rut-entidad');

            const nombreEntidad =
                document.getElementById('nombre_entidad');

            const regionSelect =
                document.getElementById('region_id');

            const comunaSelect =
                document.getElementById('comuna_id');

            const camposPersonaRequeridos = [
                document.getElementById('nombres'),
                document.getElementById('apellidos'),
                document.getElementById('rut_persona'),
            ];

            const camposEntidadRequeridos = [
                document.getElementById('nombre_entidad'),
                document.getElementById('rut_entidad'),
                document.getElementById('nombre_encargado'),
            ];

            function actualizarCamposCliente() {
                const rutEncargado =
                    document.getElementById('rut_encargado');

                rutEncargado.required = false;

                const opcionSeleccionada =
                    tipoCliente.options[
                        tipoCliente.selectedIndex
                    ];

                const codigo =
                    opcionSeleccionada?.dataset.codigo ?? '';

                const esPersona =
                    codigo === 'PERSONA';

                const esEstablecimiento =
                    codigo === 'ESTABLECIMIENTO_EDUCACIONAL';

                const esTourOperador =
                    codigo === 'TOUR_OPERADOR_AGENCIA_VIAJES';

                const esGrupoAdultosMayores =
                    codigo === 'GRUPO_ADULTOS_MAYORES';

                const esOrganizacion =
                    esEstablecimiento ||
                    esTourOperador ||
                    esGrupoAdultosMayores;

                camposPersona.hidden = !esPersona;
                camposEntidad.hidden = !esOrganizacion;

                camposPersonaRequeridos.forEach(function(campo) {
                    campo.required = esPersona;
                    campo.disabled = !esPersona;
                });

                camposEntidadRequeridos.forEach(function(campo) {
                    campo.required = esOrganizacion;
                    campo.disabled = !esOrganizacion;
                });

                rutEncargado.disabled = !esOrganizacion;

                if (esEstablecimiento) {
                    tituloDatosEntidad.textContent =
                        'Datos del establecimiento educacional';

                    labelNombreEntidad.innerHTML =
                        'Nombre del establecimiento educacional ' +
                        '<span class="text-danger">*</span>';

                    labelRutEntidad.innerHTML =
                        'RUT del establecimiento educacional ' +
                        '<span class="text-danger">*</span>';

                    nombreEntidad.placeholder =
                        'Ej.: Escuela República de Chile';

                    return;
                }

                if (esTourOperador) {
                    tituloDatosEntidad.textContent =
                        'Datos del tour operador o agencia de viajes';

                    labelNombreEntidad.innerHTML =
                        'Nombre del tour operador o agencia de viajes ' +
                        '<span class="text-danger">*</span>';

                    labelRutEntidad.innerHTML =
                        'RUT del tour operador o agencia de viajes ' +
                        '<span class="text-danger">*</span>';

                    nombreEntidad.placeholder =
                        'Ej.: Turismo Patagonia SpA';

                    return;
                }

                if (esGrupoAdultosMayores) {
                    tituloDatosEntidad.textContent =
                        'Datos del grupo de adultos mayores';

                    labelNombreEntidad.innerHTML =
                        'Nombre de la organización o agrupación ' +
                        '<span class="text-danger">*</span>';

                    labelRutEntidad.innerHTML =
                        'RUT de la organización o agrupación ' +
                        '<span class="text-danger">*</span>';

                    nombreEntidad.placeholder =
                        'Ej.: Club Adulto Mayor Los Alerces';

                    return;
                }

                tituloDatosEntidad.textContent =
                    'Datos de la organización';

                labelNombreEntidad.innerHTML =
                    'Nombre de la organización ' +
                    '<span class="text-danger">*</span>';

                labelRutEntidad.innerHTML =
                    'RUT de la organización ' +
                    '<span class="text-danger">*</span>';

                nombreEntidad.placeholder =
                    'Ingrese el nombre';
            }

            async function cargarComunas() {
                const regionId = regionSelect.value;

                const comunaSeleccionada =
                    comunaSelect.dataset.selected;

                if (!regionId) {
                    comunaSelect.innerHTML =
                        '<option value="">' +
                        'Seleccione una región primero' +
                        '</option>';

                    comunaSelect.disabled = true;
                    return;
                }

                comunaSelect.disabled = true;

                comunaSelect.innerHTML =
                    '<option value="">' +
                    'Cargando comunas...' +
                    '</option>';

                try {
                    const url =
                        `{{ url('/reservas/comunas-por-region') }}/${regionId}`;

                    const respuesta = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    if (!respuesta.ok) {
                        throw new Error(
                            'No fue posible cargar las comunas.'
                        );
                    }

                    const comunas = await respuesta.json();

                    comunaSelect.innerHTML =
                        '<option value="">' +
                        'Seleccione una comuna' +
                        '</option>';

                    comunas.forEach(function(comuna) {
                        const option =
                            document.createElement('option');

                        option.value = comuna.id;
                        option.textContent = comuna.nombre;

                        option.selected =
                            String(comuna.id) ===
                            String(comunaSeleccionada);

                        comunaSelect.appendChild(option);
                    });

                    comunaSelect.disabled = false;
                } catch (error) {
                    comunaSelect.innerHTML =
                        '<option value="">' +
                        'Error al cargar comunas' +
                        '</option>';

                    comunaSelect.disabled = true;
                }
            }

            tipoCliente.addEventListener(
                'change',
                actualizarCamposCliente
            );

            regionSelect.addEventListener(
                'change',
                function() {
                    comunaSelect.dataset.selected = '';
                    cargarComunas();
                }
            );

            actualizarCamposCliente();

            if (regionSelect.value) {
                cargarComunas();
            } else {
                comunaSelect.disabled = true;
            }
        });
    </script>
@stop
