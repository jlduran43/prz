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
    <link rel="stylesheet" href="{{ asset('css/rut-chileno.css') }}?v={{ time() }}">

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
    @include('reservas.partials._wizard', ['paso' => 1])

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

            const rutEncargado =
                document.getElementById('rut_encargado');

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

            const camposRut =
                document.querySelectorAll('.rut-chileno');

            function limpiarRut(valor) {
                return String(valor ?? '')
                    .replace(/[^0-9kK]/g, '')
                    .toUpperCase();
            }

            function formatearRut(valor) {
                const rutLimpio = limpiarRut(valor);

                if (rutLimpio.length <= 1) {
                    return rutLimpio;
                }

                const cuerpo = rutLimpio.slice(0, -1);
                const dv = rutLimpio.slice(-1);

                const cuerpoFormateado = cuerpo.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    '.'
                );

                return `${cuerpoFormateado}-${dv}`;
            }

            function validarRut(valor) {
                const rutLimpio = limpiarRut(valor);

                if (rutLimpio.length < 2) {
                    return false;
                }

                const cuerpo = rutLimpio.slice(0, -1);
                const dvIngresado = rutLimpio.slice(-1);

                if (!/^\d+$/.test(cuerpo)) {
                    return false;
                }

                if (/^(\d)\1+$/.test(cuerpo)) {
                    return false;
                }

                let suma = 0;
                let multiplicador = 2;

                for (let i = cuerpo.length - 1; i >= 0; i--) {
                    suma += Number(cuerpo[i]) * multiplicador;

                    multiplicador++;

                    if (multiplicador > 7) {
                        multiplicador = 2;
                    }
                }

                const resto = 11 - (suma % 11);

                let dvCalculado;

                if (resto === 11) {
                    dvCalculado = '0';
                } else if (resto === 10) {
                    dvCalculado = 'K';
                } else {
                    dvCalculado = String(resto);
                }

                return dvIngresado === dvCalculado;
            }

            function actualizarEstadoRut(input) {
                const grupo =
                    input.closest('.rut-input-group');

                const estado =
                    grupo?.querySelector('.rut-estado');

                const mensaje =
                    grupo
                    ?.closest('.form-group')
                    ?.querySelector('.rut-mensaje');

                if (!grupo || !estado) {
                    return;
                }

                grupo.classList.remove(
                    'rut-valido',
                    'rut-invalido'
                );

                if (mensaje) {
                    mensaje.classList.remove(
                        'valido',
                        'invalido'
                    );
                }

                const valorLimpio =
                    limpiarRut(input.value);

                if (!valorLimpio) {
                    estado.innerHTML =
                        '<i class="fas fa-minus text-muted"></i>';

                    if (mensaje) {
                        mensaje.textContent =
                            'Ingresa el RUT con o sin puntos.';
                    }

                    return;
                }

                if (validarRut(input.value)) {
                    grupo.classList.add('rut-valido');

                    estado.innerHTML =
                        '<i class="fas fa-check text-success"></i>';

                    if (mensaje) {
                        mensaje.textContent = 'RUT válido.';
                        mensaje.classList.add('valido');
                    }

                    return;
                }

                grupo.classList.add('rut-invalido');

                estado.innerHTML =
                    '<i class="fas fa-times text-danger"></i>';

                if (mensaje) {
                    mensaje.textContent =
                        'El RUT ingresado no es válido.';

                    mensaje.classList.add('invalido');
                }
            }

            function limpiarEstadoRut(input) {
                if (!input) {
                    return;
                }

                input.value = '';

                const grupo =
                    input.closest('.rut-input-group');

                const estado =
                    grupo?.querySelector('.rut-estado');

                const mensaje =
                    grupo
                    ?.closest('.form-group')
                    ?.querySelector('.rut-mensaje');

                grupo?.classList.remove(
                    'rut-valido',
                    'rut-invalido'
                );

                if (estado) {
                    estado.innerHTML =
                        '<i class="fas fa-minus text-muted"></i>';
                }

                if (mensaje) {
                    mensaje.textContent =
                        'Ingresa el RUT con o sin puntos.';

                    mensaje.classList.remove(
                        'valido',
                        'invalido'
                    );
                }
            }

            camposRut.forEach(function(input) {
                input.addEventListener('input', function() {
                    input.value =
                        formatearRut(input.value);

                    actualizarEstadoRut(input);
                });

                input.addEventListener('blur', function() {
                    input.value =
                        formatearRut(input.value);

                    actualizarEstadoRut(input);
                });

                if (input.value) {
                    input.value =
                        formatearRut(input.value);

                    actualizarEstadoRut(input);
                }
            });

            function actualizarCamposCliente() {
                const opcionSeleccionada =
                    tipoCliente.options[
                        tipoCliente.selectedIndex
                    ];

                const codigo =
                    opcionSeleccionada?.dataset.codigo ?? '';

                const estructura =
                    opcionSeleccionada?.dataset.estructura ?? '';

                const esPersona =
                    estructura === 'PERSONA';

                const esEstablecimiento =
                    estructura === 'ESTABLECIMIENTO';

                const esOrganizacion =
                    estructura === 'ORGANIZACION';

                const usaDatosEntidad =
                    esEstablecimiento || esOrganizacion;

                camposPersona.hidden = !esPersona;
                camposEntidad.hidden = !usaDatosEntidad;

                camposPersonaRequeridos.forEach(
                    function(campo) {
                        campo.required = esPersona;
                        campo.disabled = !esPersona;
                    }
                );

                camposEntidadRequeridos.forEach(
                    function(campo) {
                        campo.required = usaDatosEntidad;
                        campo.disabled = !usaDatosEntidad;
                    }
                );

                rutEncargado.required = false;
                rutEncargado.disabled = !usaDatosEntidad;

                if (!esPersona) {
                    limpiarEstadoRut(
                        document.getElementById(
                            'rut_persona'
                        )
                    );
                }

                if (!usaDatosEntidad) {
                    limpiarEstadoRut(
                        document.getElementById(
                            'rut_entidad'
                        )
                    );

                    limpiarEstadoRut(rutEncargado);
                }

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

                if (codigo === 'TOUR_OPERADOR_AGENCIA_VIAJES') {
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

                if (codigo === 'GRUPO_ADULTOS_MAYORES') {
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

                /*
            |--------------------------------------------------------------------------
            | ORGANIZACIÓN GENÉRICA
            |--------------------------------------------------------------------------
            */

                if (esOrganizacion) {
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
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!respuesta.ok) {
                        throw new Error(
                            'No fue posible cargar las comunas.'
                        );
                    }

                    const comunas =
                        await respuesta.json();

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
                    console.error(error);

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
