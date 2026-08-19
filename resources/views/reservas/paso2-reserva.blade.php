@extends('adminlte::page')

@section('title', 'Nueva reserva')

@section('content_header')
    @php
        $tipoOperacion = $tipoOperacion ?? session('reserva.tipo_operacion');

        $esCotizacion = $tipoOperacion === 'COTIZACION';

        $esReserva = $tipoOperacion === 'RESERVA';

        $datosCliente = $datosCliente ?? session('reserva.cliente', []);
        $datosReserva = $datosReserva ?? session('reserva.datos', []);

        $codigoTipoCliente = $datosCliente['codigo_tipo_cliente'] ?? ($datosCliente['tipo_cliente_codigo'] ?? null);

        $tipoEstructura = $datosCliente['tipo_estructura'] ?? null;

        $esEstablecimientoEducacional = $codigoTipoCliente === 'ESTABLECIMIENTO_EDUCACIONAL';
    @endphp

    <div>
        <h1 class="mb-1">
            @if ($esCotizacion)
                Nueva cotización
            @else
                Nueva reserva
            @endif
        </h1>
        <p class="text-muted mb-0">
            @if ($esCotizacion)
                <div class="alert alert-info">

                    <i class="fas fa-file-invoice-dollar mr-1"></i>

                    Selecciona un máximo de dos servicios.

                    El sistema calculará el valor estimado según
                    la cantidad de personas indicada.

                    <strong>
                        En una cotización no se seleccionan horarios.
                    </strong>

                </div>
            @else
                <div class="alert alert-info">

                    <i class="fas fa-info-circle mr-1"></i>

                    Primero selecciona la fecha de la visita.

                    Luego podrás escoger un máximo de dos
                    servicios y sus horarios disponibles.

                </div>
            @endif
        </p>
    </div>
@stop

@section('content')
    <style>
        .servicio-card {
            margin-bottom: 14px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background: #ffffff;
            overflow: visible;
            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                opacity .2s ease;
            position: relative;
        }

        .servicio-card:hover {
            border-color: #9ec5fe;
        }

        .servicio-card.seleccionado {
            border: 2px solid #007bff;
            box-shadow: 0 4px 14px rgba(0, 123, 255, .14);
        }

        .servicio-card.deshabilitado {
            opacity: .5;
        }

        .servicio-cabecera {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 96px;
            padding: 16px 18px;
            margin: 0;
            cursor: pointer;
        }

        .servicio-checkbox {
            flex: 0 0 auto;
            width: 20px;
            height: 20px;
            margin: 0;
        }

        .servicio-info {
            flex: 1;
            min-width: 0;
        }

        .servicio-nombre {
            display: block;
            color: #343a40;
            font-size: 16px;
            font-weight: 700;
        }

        .servicio-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .servicio-precio {
            flex: 0 0 150px;
            text-align: right;
        }

        .servicio-configuracion {
            display: none !important;
            padding: 18px;
            border-top: 1px solid #dee2e6;
            background: #f8f9fa;
        }

        .servicio-card.seleccionado .servicio-configuracion {
            display: block !important;
        }

        .configuracion-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .configuracion-titulo {
            margin: 0;
            color: #343a40;
            font-size: 15px;
            font-weight: 700;
        }

        .fecha-fila {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 16px;
        }

        .fecha-fila .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .horario-option {
            position: relative;
            width: 100%;
            min-height: 86px;
            padding: 14px;
            border: 2px solid #ced4da;
            border-radius: 9px;
            background: #ffffff;
            text-align: left;
            transition: .2s ease;
        }

        .horario-option:hover:not(:disabled) {
            border-color: #007bff;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 123, 255, .12);
        }

        .horario-option.selected {
            border-color: #007bff;
            background: #007bff;
            color: #ffffff;
        }

        .horario-option.no-disponible {
            cursor: not-allowed;
            opacity: .5;
            background: #f1f3f5;
        }

        .horario-franja {
            display: block;
            font-size: 16px;
            font-weight: 700;
        }

        .horario-cupos {
            display: block;
            margin-top: 6px;
            font-size: 13px;
        }

        .lista-servicios {
            max-height: none;
            overflow-y: visible;
            padding-right: 4px;
        }

        .lista-servicios::-webkit-scrollbar {
            width: 8px;
        }

        .lista-servicios::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background: #ced4da;
        }

        @media (max-width: 767.98px) {
            .servicio-cabecera {
                align-items: flex-start;
            }

            .servicio-precio {
                flex-basis: auto;
                text-align: left;
            }

            .fecha-fila {
                flex-direction: column;
                align-items: stretch;
            }

            .configuracion-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        .servicio-imagen-contenedor {
            position: relative;
            flex: 0 0 90px;
            width: 90px;
            height: 65px;
            z-index: 1;
        }

        .servicio-imagen-contenedor:hover {
            z-index: 9999;
        }

        .servicio-imagen {
            width: 90px;
            height: 65px;
            object-fit: cover;
            border-radius: 6px;

            position: relative;
            display: block;

            cursor: zoom-in;

            transition:
                transform .25s ease,
                box-shadow .25s ease;

            transform-origin: center center;
        }

        /*
                                                * Al pasar el mouse se amplía.
                                                * Aumenta dentro del flujo para evitar
                                                * que el contenedor con scroll la corte.
                                            */

        .servicio-imagen:hover {
            transform: scale(3);

            box-shadow:
                0 8px 24px rgba(0, 0, 0, .30);
        }

        .servicio-sin-imagen {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 65px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #f8f9fa;
            color: #adb5bd;
        }
    </style>
    @include('reservas.partials._wizard', ['paso' => 2])

    @if (session('conversion_cotizacion_id'))
        <div class="alert alert-success">

            <i class="fas fa-calendar-check mr-1"></i>

            <strong>
                Reserva desde cotización:
            </strong>

            se conservaron los servicios y la cantidad
            de asistentes de tu cotización.

            Selecciona una fecha para comprobar
            nuevamente la disponibilidad de horarios.

        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reservas.datos.guardar') }}" method="POST" id="formReserva">
        @csrf

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-star mr-1"></i>
                    @if ($esCotizacion)
                        Servicios a cotizar
                    @else
                        Servicios, fecha y horarios
                    @endif
                </h3>
            </div>

            <div class="card-body">
                @if ($esReserva)
                    <div class="form-group mb-4">

                        <label for="fecha_reserva">

                            Fecha de la visita

                            <span class="text-danger">*</span>

                        </label>


                        <input type="date" id="fecha_reserva" name="fecha_reserva" class="form-control"
                            min="{{ now()->format('Y-m-d') }}"
                            value="{{ old('fecha_reserva', session('reserva.datos.fecha_reserva')) }}" required>


                        <small class="form-text text-muted">

                            Selecciona una fecha para consultar
                            los servicios y horarios disponibles.

                        </small>

                    </div>
                @endif

                @if ($esCotizacion)
                    <div class="alert alert-info">

                        <i class="fas fa-file-invoice-dollar mr-1"></i>

                        Selecciona un máximo de dos servicios.

                        El sistema calculará el valor estimado según
                        la cantidad de personas indicada.

                        <strong>
                            En una cotización no se seleccionan horarios.
                        </strong>

                    </div>
                @else
                    <div class="alert alert-info">

                        <i class="fas fa-info-circle mr-1"></i>

                        Primero selecciona la fecha de la visita.

                        Luego podrás escoger un máximo de dos
                        servicios y sus horarios disponibles.

                    </div>
                @endif

                <div id="mensaje-servicios" class="alert alert-light border text-center">
                    @if ($esCotizacion)
                        <i class="fas fa-spinner fa-spin mr-1"></i>

                        Cargando servicios disponibles para cotizar...
                    @else
                        <i class="fas fa-calendar-alt mr-1"></i>

                        Selecciona una fecha para consultar
                        los servicios disponibles.
                    @endif

                </div>

                <div id="contenedor-servicios" class="lista-servicios"></div>

                <div class="selection-summary mt-3">
                    <div>
                        <strong>Servicios seleccionados</strong>
                        <small class="d-block text-muted">
                            @if ($esCotizacion)
                                Puedes seleccionar hasta dos servicios
                                para calcular el valor estimado.
                            @else
                                Cada servicio seleccionado debe tener un horario.
                            @endif

                        </small>
                    </div>

                    <strong class="text-primary h4 mb-0">
                        <span id="totalServiciosSeleccionados">0</span>/2
                    </strong>
                </div>

                <div id="serviciosError" class="text-danger mt-2 d-none">
                    Solo puedes seleccionar un máximo de dos servicios.
                </div>
            </div>
        </div>

        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Detalle de asistentes y precio
                </h3>
            </div>



            <div class="card-body">
                @if ($esEstablecimientoEducacional)
                    <div class="alert alert-info">
                        Como el cliente es un establecimiento educacional,
                        debes indicar la composición del grupo.
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cantidad_alumnos">
                                    Cantidad de alumnos
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="number" name="cantidad_alumnos" id="cantidad_alumnos"
                                    class="form-control @error('cantidad_alumnos') is-invalid @enderror"
                                    value="{{ old('cantidad_alumnos', $datosReserva['cantidad_alumnos'] ?? 1) }}"
                                    min="1" required>

                                @error('cantidad_alumnos')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cantidad_profesores">
                                    Cantidad de profesores
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="number" name="cantidad_profesores" id="cantidad_profesores"
                                    class="form-control @error('cantidad_profesores') is-invalid @enderror"
                                    value="{{ old('cantidad_profesores', $datosReserva['cantidad_profesores'] ?? 0) }}"
                                    min="0" required>

                                @error('cantidad_profesores')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nivel_educacional">
                                    Nivel educacional
                                    <span class="text-danger">*</span>
                                </label>

                                @php
                                    $niveles = [
                                        'PARVULARIA' => 'Educación parvularia',
                                        'BASICA' => 'Educación básica',
                                        'MEDIA' => 'Educación media',
                                        'ESPECIAL' => 'Educación especial',
                                        'SUPERIOR' => 'Educación superior',
                                        'ADULTOS' => 'Educación de adultos',
                                        'OTRO' => 'Otro',
                                    ];

                                    $nivelSeleccionado = old(
                                        'nivel_educacional',
                                        $datosReserva['nivel_educacional'] ?? '',
                                    );
                                @endphp

                                <select name="nivel_educacional" id="nivel_educacional"
                                    class="form-control @error('nivel_educacional') is-invalid @enderror" required>
                                    <option value="">Seleccione un nivel</option>

                                    @foreach ($niveles as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected($nivelSeleccionado === $codigo)>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('nivel_educacional')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="curso">
                                    Curso
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="curso" id="curso"
                                    class="form-control @error('curso') is-invalid @enderror"
                                    value="{{ old('curso', $datosReserva['curso'] ?? '') }}"
                                    placeholder="Ej.: 4.º básico A" maxlength="100" required>

                                @error('curso')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="objetivo_visita">
                                    Objetivo de la visita
                                </label>

                                <textarea name="objetivo_visita" id="objetivo_visita" rows="3" maxlength="500"
                                    class="form-control
                                    @error('objetivo_visita') is-invalid @enderror"
                                    placeholder="Ej.: Complementar los contenidos de Ciencias Naturales mediante una visita educativa.">{{ old('objetivo_visita', $datosReserva['objetivo_visita'] ?? '') }}</textarea>

                                <small class="form-text text-muted">
                                    Indica brevemente el propósito educativo de la visita.
                                </small>

                                @error('objetivo_visita')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            @if ($codigoTipoCliente !== 'PERSONA')
                                <div class="card card-outline card-warning mt-4 mb-4">

                                    <div class="card-header">

                                        <h3 class="card-title">
                                            <i class="fas fa-percent mr-2"></i>
                                            Convenio o descuento
                                        </h3>

                                    </div>

                                    <div class="card-body">

                                        <label for="codigo_convenio">

                                            Código de convenio

                                            <small class="text-muted">
                                                (opcional)
                                            </small>

                                        </label>

                                        <div class="input-group">

                                            <input type="text" id="codigo_convenio" name="codigo_convenio"
                                                class="form-control" maxlength="50" autocomplete="off"
                                                value="{{ old('codigo_convenio', $convenioAplicado['codigo'] ?? '') }}"
                                                placeholder="Ej.: MUNICIPAL2026">

                                            <div class="input-group-append">

                                                <button type="button" id="btnAplicarConvenio" class="btn btn-warning">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Aplicar
                                                </button>

                                            </div>

                                        </div>

                                        <small class="form-text text-muted">
                                            El código será validado junto al RUT de la entidad registrada.
                                        </small>

                                        <div id="resultadoConvenio" class="mt-3"></div>

                                    </div>

                                </div>
                            @endif

                        </div>
                        <input type="hidden" name="cantidad_asistentes" id="cantidad_asistentes"
                            value="{{ old('cantidad_asistentes', $datosReserva['cantidad_asistentes'] ?? 1) }}">
                    @else
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cantidad_asistentes">
                                        Cantidad de personas
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="number" name="cantidad_asistentes" id="cantidad_asistentes"
                                        class="form-control @error('cantidad_asistentes') is-invalid @enderror"
                                        value="{{ old('cantidad_asistentes', $datosReserva['cantidad_asistentes'] ?? 1) }}"
                                        min="1" required>

                                    @error('cantidad_asistentes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                @endif

                <div class="row mt-2">
                    <div class="col-md-6 mb-3">
                        <div class="selection-summary">
                            <div>
                                <strong>Total de asistentes</strong>
                                <small class="d-block text-muted">
                                    @if ($esCotizacion)
                                        Cantidad utilizada para calcular el valor estimado.
                                    @else
                                        Cantidad utilizada para validar los cupos.
                                    @endif
                                </small>
                            </div>
                            <strong id="totalAsistentes" class="text-info h4 mb-0">1</strong>
                        </div>
                    </div>

                    {{-- Subtotal --}}
                    <div class="col-md-3 mb-3">

                        <div class="selection-summary">

                            <div>
                                <strong>
                                    Subtotal
                                </strong>
                            </div>

                            <strong id="subtotalGeneral" class="text-dark h5 mb-0">
                                $0
                            </strong>

                        </div>

                    </div>

                    @if ($esEstablecimientoEducacional)
                        {{-- Descuento --}}
                        <div class="col-md-3 mb-3">

                            <div class="selection-summary">

                                <div>
                                    <strong>
                                        Descuento
                                    </strong>
                                </div>

                                <strong id="descuentoConvenio" class="text-success h5 mb-0">
                                    $0
                                </strong>

                            </div>

                        </div>
                    @endif

                    {{-- Total --}}
                    <div class="col-md-3 mb-3">

                        <div class="selection-summary">

                            <div>

                                <strong>

                                    @if ($esCotizacion)
                                        Total estimado
                                    @else
                                        Total
                                    @endif

                                </strong>

                            </div>


                            <strong id="precioTotal" class="text-primary h4 mb-0">
                                $0
                            </strong>

                        </div>

                    </div>
                </div>
                <div id="detalleServicios"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('reservas.cliente') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Continuar
                        <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maximoServicios = 2;
            const esCotizacion = @json($esCotizacion);
            const tipoOperacion = @json($tipoOperacion);

            const codigoTipoCliente = @json($codigoTipoCliente);
            const tipoEstructura = @json($tipoEstructura);
            const esPersona = codigoTipoCliente === 'PERSONA';
            const esEstablecimientoEducacional = codigoTipoCliente === 'ESTABLECIMIENTO_EDUCACIONAL';
            const esOrganizacion = tipoEstructura === 'ORGANIZACION';

            const datosAnteriores = @json(old('servicios', $datosReserva['servicios'] ?? []));

            const urlServicios = @json(route('reservas.servicios-disponibles'));
            const urlHorarios = @json(route('reservas.consultar-horarios'));

            const urlValidarConvenio = @json(route('reservas.validar-convenio'));

            const csrfToken = @json(csrf_token());

            const formulario = document.getElementById('formReserva');
            const fechaReservaInput = document.getElementById('fecha_reserva');
            const contenedor = document.getElementById('contenedor-servicios');
            const mensaje = document.getElementById('mensaje-servicios');
            const totalSeleccionados = document.getElementById('totalServiciosSeleccionados');
            const serviciosError = document.getElementById('serviciosError');
            const precioTotal = document.getElementById('precioTotal');
            const detalleServicios = document.getElementById('detalleServicios');
            const cantidadInput = document.getElementById('cantidad_asistentes');
            const alumnosInput = document.getElementById('cantidad_alumnos');
            const profesoresInput = document.getElementById('cantidad_profesores');
            const totalAsistentes = document.getElementById('totalAsistentes');
            const codigoConvenioInput = document.getElementById('codigo_convenio');
            const btnAplicarConvenio = document.getElementById('btnAplicarConvenio');
            const resultadoConvenio = document.getElementById('resultadoConvenio');
            const subtotalGeneralElemento = document.getElementById('subtotalGeneral');
            const descuentoConvenioElemento = document.getElementById('descuentoConvenio');

            let convenioActual = esEstablecimientoEducacional ? null : @json($convenioAplicado ?? null);

            function escaparHtml(valor) {
                const div = document.createElement('div');
                div.textContent = String(valor ?? '');
                return div.innerHTML;
            }

            function formatearPrecio(valor) {
                return new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP',
                    maximumFractionDigits: 0,
                }).format(Number(valor) || 0);
            }

            function cantidadPersonas() {
                if (alumnosInput && profesoresInput) {
                    const alumnos = Math.max(Number(alumnosInput.value) || 0, 0);
                    const profesores = Math.max(Number(profesoresInput.value) || 0, 0);
                    const total = alumnos + profesores;
                    cantidadInput.value = total;
                    totalAsistentes.textContent = total;
                    return total;
                }

                const total = Math.max(Number(cantidadInput?.value) || 0, 0);
                totalAsistentes.textContent = total;
                return total;
            }

            function obtenerCardsSeleccionadas() {
                return Array.from(
                    contenedor.querySelectorAll('.servicio-checkbox:checked')
                ).map(checkbox => checkbox.closest('.servicio-card'));
            }

            function calcularSubtotal(precio, tipoCobro, personas) {
                return tipoCobro === 'POR_PERSONA' ?
                    precio * personas :
                    precio;
            }

            function actualizarPrecio() {

                const personas =
                    cantidadPersonas();

                const entradasLiberadas =
                    obtenerEntradasLiberadas(
                        personas
                    );

                const personasCobradas =
                    Math.max(
                        personas - entradasLiberadas,
                        0
                    );

                const cards =
                    obtenerCardsSeleccionadas();

                let subtotalGeneral = 0;

                let html = '';


                cards.forEach(card => {

                    const precio =
                        Number(
                            card.dataset.precio
                        ) || 0;

                    const tipoCobro =
                        card.dataset.tipoCobro ||
                        'FIJO';


                    const subtotal =
                        calcularSubtotal(
                            precio,
                            tipoCobro,
                            personasCobradas
                        );


                    subtotalGeneral += subtotal;

                    let descripcion;

                    if (tipoCobro === 'POR_PERSONA') {

                        descripcion =
                            `${formatearPrecio(precio)} × ${personasCobradas} personas`;

                        if (entradasLiberadas === 1) {

                            descripcion +=
                                ' · 1 entrada liberada';

                        } else if (entradasLiberadas === 2) {

                            descripcion +=
                                ' · 2 entradas liberadas';
                        }

                    } else if (tipoCobro === 'POR_GRUPO') {

                        descripcion =
                            `${formatearPrecio(precio)} por grupo`;

                    } else {

                        descripcion =
                            'Precio fijo';
                    }

                    html += `
            <div class="detalle-precio-fila">

                <div>

                    <strong>
                        ${escaparHtml(
                            card.dataset.nombre
                        )}
                    </strong>

                    <small
                        class="d-block text-muted"
                    >
                        ${descripcion}
                    </small>

                </div>

                <strong>
                    ${formatearPrecio(subtotal)}
                </strong>

            </div>
        `;
                });


                let porcentaje = 0;

                if (!esPersona && convenioActual) {

                    porcentaje =
                        Number(
                            convenioActual
                            .porcentaje_descuento
                        ) || 0;
                }


                const descuento =
                    Math.round(
                        subtotalGeneral *
                        (porcentaje / 100)
                    );


                const total =
                    Math.max(
                        subtotalGeneral - descuento,
                        0
                    );


                detalleServicios.innerHTML =
                    html || `
            <div class="text-muted">
                Selecciona un servicio para
                ver el detalle.
            </div>
        `;


                if (subtotalGeneralElemento) {

                    subtotalGeneralElemento.textContent =
                        formatearPrecio(
                            subtotalGeneral
                        );
                }


                if (descuentoConvenioElemento) {

                    descuentoConvenioElemento.textContent =
                        descuento > 0 ?
                        `-${formatearPrecio(descuento)}` :
                        formatearPrecio(0);
                }


                precioTotal.textContent =
                    formatearPrecio(total);
            }

            if (
                btnAplicarConvenio &&
                codigoConvenioInput
            ) {

                btnAplicarConvenio.addEventListener(
                    'click',
                    async function() {

                        const codigo =
                            codigoConvenioInput
                            .value
                            .trim();


                        if (!codigo) {

                            convenioActual = null;

                            resultadoConvenio.innerHTML = `
                    <div class="alert alert-warning mb-0">
                        Ingresa un código de convenio.
                    </div>
                `;

                            actualizarPrecio();

                            return;
                        }


                        btnAplicarConvenio.disabled =
                            true;


                        btnAplicarConvenio.innerHTML = `
                <span
                    class="
                        spinner-border
                        spinner-border-sm
                        mr-1
                    "
                ></span>

                Validando...
            `;


                        try {

                            const respuesta =
                                await fetch(
                                    urlValidarConvenio, {
                                        method: 'POST',

                                        headers: {
                                            'Content-Type': 'application/json',

                                            Accept: 'application/json',

                                            'X-CSRF-TOKEN': csrfToken,

                                            'X-Requested-With': 'XMLHttpRequest',
                                        },

                                        body: JSON.stringify({
                                            codigo_convenio: codigo,
                                        }),
                                    }
                                );


                            const datos =
                                await respuesta.json();


                            if (
                                !respuesta.ok ||
                                !datos.ok
                            ) {

                                throw new Error(
                                    datos.mensaje ||
                                    'No fue posible validar el convenio.'
                                );
                            }


                            convenioActual =
                                datos.convenio;


                            codigoConvenioInput.value =
                                convenioActual.codigo;


                            resultadoConvenio.innerHTML = `
                    <div
                        class="
                            alert
                            alert-success
                            mb-0
                        "
                    >

                        <i
                            class="
                                fas
                                fa-check-circle
                                mr-1
                            "
                        ></i>

                        <strong>
                            ${escaparHtml(
                                convenioActual.nombre
                            )}
                        </strong>

                        <div class="mt-1">

                            Descuento aplicado:

                            <strong>
                                ${Number(
                                    convenioActual
                                        .porcentaje_descuento
                                )}%
                            </strong>

                        </div>

                    </div>
                `;


                            actualizarPrecio();

                        } catch (error) {

                            convenioActual = null;


                            resultadoConvenio.innerHTML = `
                    <div
                        class="
                            alert
                            alert-danger
                            mb-0
                        "
                    >

                        <i
                            class="
                                fas
                                fa-times-circle
                                mr-1
                            "
                        ></i>

                        ${escaparHtml(
                            error.message
                        )}

                    </div>
                `;


                            actualizarPrecio();

                        } finally {

                            btnAplicarConvenio.disabled =
                                false;

                            btnAplicarConvenio.innerHTML = `
                    <i
                        class="
                            fas
                            fa-check
                            mr-1
                        "
                    ></i>

                    Aplicar
                `;
                        }
                    }
                );
            }

            codigoConvenioInput?.addEventListener(
                'input',
                function() {

                    if (
                        convenioActual &&
                        this.value.trim().toUpperCase() !==
                        convenioActual.codigo
                    ) {

                        convenioActual = null;

                        resultadoConvenio.innerHTML = '';

                        actualizarPrecio();
                    }
                }
            );

            function actualizarEstadoServicios() {
                const cardsSeleccionadas =
                    obtenerCardsSeleccionadas();

                const maximoAlcanzado =
                    cardsSeleccionadas.length >= maximoServicios;

                totalSeleccionados.textContent =
                    cardsSeleccionadas.length;

                contenedor
                    .querySelectorAll('.servicio-card')
                    .forEach(function(card) {
                        const checkbox =
                            card.querySelector('.servicio-checkbox');

                        const configuracion =
                            card.querySelector(
                                '.servicio-configuracion'
                            );

                        const seleccionado =
                            checkbox.checked;

                        card.classList.toggle(
                            'seleccionado',
                            seleccionado
                        );

                        /*
                         * Mostrar exclusivamente la configuración
                         * de servicios seleccionados.
                         */
                        if (configuracion) {
                            configuracion.style.display =
                                seleccionado ?
                                'block' :
                                'none';
                        }

                        checkbox.disabled = !seleccionado && maximoAlcanzado;

                        card.classList.toggle(
                            'deshabilitado',
                            checkbox.disabled
                        );

                        card
                            .querySelectorAll('.campo-servicio')
                            .forEach(function(campo) {
                                campo.disabled = !seleccionado;
                            });
                    });

                actualizarPrecio();
            }

            function crearTarjeta(servicio) {
                const categoria = servicio.categoria?.nombre || 'Sin categoría';
                const precio = Number(servicio.precio) || 0;
                const tipoCobro = servicio.tipo_cobro || 'FIJO';

                const imagenHtml = servicio.imagen ?
                    `
            <span class="servicio-imagen-contenedor">
                <img
                    src="/storage/${escaparHtml(servicio.imagen)}"
                    alt="${escaparHtml(servicio.nombre)}"
                    class="servicio-imagen"
                    title="Pasa el mouse para ampliar"
                >
            </span>
        ` :
                    `
            <span
                class="servicio-sin-imagen"
                title="Sin imagen"
            >
                <i class="fas fa-image fa-lg"></i>
            </span>
        `;

                const card = document.createElement('div');
                card.className = 'servicio-card';
                card.dataset.servicioId = servicio.id;
                card.dataset.nombre = servicio.nombre;
                card.dataset.precio = precio;
                card.dataset.tipoCobro = tipoCobro;

                card.innerHTML = `
    <label class="servicio-cabecera">
        <input
            type="checkbox"
            class="servicio-checkbox"
            value="${servicio.id}"
        >

        ${imagenHtml}

        <span class="servicio-info">

            <span class="servicio-nombre">
                ${escaparHtml(servicio.nombre)}
            </span>

            <span class="servicio-meta">

                <span class="badge badge-info">
                    ${escaparHtml(categoria)}
                </span>

                ${
                    servicio.duracion_minutos
                        ? `
                                                                                        <small class="text-muted">
                                                                                            <i class="far fa-clock mr-1"></i>
                                                                                            ${servicio.duracion_minutos}
                                                                                            minutos
                                                                                        </small>
                                                                                    `
                        : ''
                }

                ${
                    servicio.capacidad_maxima
                        ? `
                                                                                        <small class="text-muted">
                                                                                            <i class="fas fa-users mr-1"></i>
                                                                                            Máximo:
                                                                                            ${servicio.capacidad_maxima}
                                                                                            personas
                                                                                        </small>
                                                                                    `
                        : ''
                }

            </span>

        </span>

        <span class="servicio-precio">

            <strong class="text-success d-block h5 mb-0">
                ${formatearPrecio(precio)}
            </strong>

            <small class="text-muted">
                ${
                    tipoCobro === 'POR_PERSONA'
                        ? 'por persona'
                        : tipoCobro === 'POR_GRUPO'
                            ? 'por grupo'
                            : 'precio fijo'
                }
            </small>

        </span>
    </label>

    ${
    esCotizacion
        ? `
                                                                                                                                                                                                                    <div class="servicio-configuracion">

                                                                                                                                                                                                                        <div class="configuracion-header">

                                                                                                                                                                                                                            <h6 class="configuracion-titulo">

                                                                                                                                                                                                                                <i
                                                                                                                                                                                                                                    class="fas fa-file-invoice-dollar
                                                                                                                                                                                                                                           text-info mr-1">
                                                                                                                                                                                                                                </i>

                                                                                                                                                                                                                                Servicio incluido en la cotización

                                                                                                                                                                                                                            </h6>


                                                                                                                                                                                                                            <button
                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                class="
                                                                                                                                                                                                                                    btn
                                                                                                                                                                                                                                    btn-sm
                                                                                                                                                                                                                                    btn-outline-danger
                                                                                                                                                                                                                                    quitar-servicio
                                                                                                                                                                                                                                "
                                                                                                                                                                                                                            >

                                                                                                                                                                                                                                <i class="fas fa-times mr-1"></i>

                                                                                                                                                                                                                                Quitar servicio

                                                                                                                                                                                                                            </button>

                                                                                                                                                                                                                        </div>


                                                                                                                                                                                                                        <input
                                                                                                                                                                                                                            type="hidden"
                                                                                                                                                                                                                            name="servicios[${servicio.id}][servicio_id]"
                                                                                                                                                                                                                            value="${servicio.id}"
                                                                                                                                                                                                                            class="campo-servicio"
                                                                                                                                                                                                                            disabled
                                                                                                                                                                                                                        >

                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                `
        : `
                                                                                                                                                                                                                    <div class="servicio-configuracion">

                                                                                                                                                                                                                        <input
                                                                                                                                                                                                                            type="hidden"
                                                                                                                                                                                                                            name="servicios[${servicio.id}][servicio_id]"
                                                                                                                                                                                                                            value="${servicio.id}"
                                                                                                                                                                                                                            class="campo-servicio"
                                                                                                                                                                                                                            disabled
                                                                                                                                                                                                                        >


                                                                                                                                                                                                                        <div class="configuracion-header">

                                                                                                                                                                                                                            <h6 class="configuracion-titulo">

                                                                                                                                                                                                                                <i
                                                                                                                                                                                                                                    class="fas fa-calendar-alt
                                                                                                                                                                                                                                           text-primary mr-1">
                                                                                                                                                                                                                                </i>

                                                                                                                                                                                                                                Configuración de la visita

                                                                                                                                                                                                                            </h6>


                                                                                                                                                                                                                            <button
                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                class="
                                                                                                                                                                                                                                    btn
                                                                                                                                                                                                                                    btn-sm
                                                                                                                                                                                                                                    btn-outline-danger
                                                                                                                                                                                                                                    quitar-servicio
                                                                                                                                                                                                                                "
                                                                                                                                                                                                                            >

                                                                                                                                                                                                                                <i class="fas fa-times mr-1"></i>

                                                                                                                                                                                                                                Quitar servicio

                                                                                                                                                                                                                            </button>

                                                                                                                                                                                                                        </div>


                                                                                                                                                                                                                        <input
                                                                                                                                                                                                                            type="hidden"
                                                                                                                                                                                                                            name="servicios[${servicio.id}][fecha]"
                                                                                                                                                                                                                            class="fecha-servicio campo-servicio"
                                                                                                                                                                                                                            value="${fechaReservaInput?.value || ''}"
                                                                                                                                                                                                                            disabled
                                                                                                                                                                                                                        >


                                                                                                                                                                                                                        <div
                                                                                                                                                                                                                            class="
                                                                                                                                                                                                                                mensaje-horarios
                                                                                                                                                                                                                                alert
                                                                                                                                                                                                                                alert-info
                                                                                                                                                                                                                                mb-3
                                                                                                                                                                                                                            "
                                                                                                                                                                                                                        >

                                                                                                                                                                                                                            <i class="fas fa-calendar-alt mr-1"></i>

                                                                                                                                                                                                                            Selecciona una fecha para
                                                                                                                                                                                                                            consultar los horarios.

                                                                                                                                                                                                                        </div>


                                                                                                                                                                                                                        <div
                                                                                                                                                                                                                            class="row contenedor-horarios">
                                                                                                                                                                                                                        </div>


                                                                                                                                                                                                                        <input
                                                                                                                                                                                                                            type="hidden"
                                                                                                                                                                                                                            name="servicios[${servicio.id}][horario_id]"
                                                                                                                                                                                                                            class="horario-id campo-servicio"
                                                                                                                                                                                                                            disabled
                                                                                                                                                                                                                        >

                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                `
}`;

                const checkbox = card.querySelector('.servicio-checkbox');
                const fechaInput = card.querySelector('.fecha-servicio');
                const quitar = card.querySelector('.quitar-servicio');

                checkbox.addEventListener('change', function() {
                    if (obtenerCardsSeleccionadas().length > maximoServicios) {
                        checkbox.checked = false;
                        serviciosError.classList.remove('d-none');
                    } else {
                        serviciosError.classList.add('d-none');
                    }

                    if (!checkbox.checked) {
                        limpiarConfiguracion(card);
                    }

                    if (checkbox.checked && !esCotizacion) {
                        const fecha = fechaReservaInput.value;

                        card.querySelector('.fecha-servicio').value = fecha;

                        cargarHorarios(card, fecha);

                        setTimeout(function() {
                            card.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest',
                            });
                        }, 100);
                    }

                    actualizarEstadoServicios();
                });

                quitar.addEventListener('click', function() {
                    checkbox.checked = false;
                    limpiarConfiguracion(card);
                    actualizarEstadoServicios();
                });

                return card;
            }

            function limpiarConfiguracion(card) {
                const fechaInput =
                    card.querySelector('.fecha-servicio');

                const horarioInput =
                    card.querySelector('.horario-id');

                const contenedorHorarios =
                    card.querySelector('.contenedor-horarios');

                const mensajeHorarios =
                    card.querySelector('.mensaje-horarios');


                /*
                 * Estos elementos solamente existen
                 * cuando estamos realizando una reserva.
                 */

                if (fechaInput) {
                    fechaInput.value = '';
                }

                if (horarioInput) {
                    horarioInput.value = '';
                }

                if (contenedorHorarios) {
                    contenedorHorarios.innerHTML = '';
                }

                if (mensajeHorarios) {

                    mensajeHorarios.className =
                        'mensaje-horarios alert alert-info mb-3';

                    mensajeHorarios.innerHTML = `
            <i class="fas fa-calendar-alt mr-1"></i>
            Selecciona una fecha para consultar
            los horarios.
        `;
                }


                const configuracion =
                    card.querySelector('.servicio-configuracion');

                if (configuracion) {
                    configuracion.style.display = 'none';
                }
            }

            async function cargarHorarios(card, fecha, horarioASeleccionar = '') {
                const servicioId = card.dataset.servicioId;
                const mensajeHorarios = card.querySelector('.mensaje-horarios');
                const contenedorHorarios = card.querySelector('.contenedor-horarios');
                const horarioInput = card.querySelector('.horario-id');

                contenedorHorarios.innerHTML = '';
                horarioInput.value = '';

                if (!fecha) {
                    return;
                }

                mensajeHorarios.className = 'mensaje-horarios alert alert-info mb-3';
                mensajeHorarios.innerHTML = `
                    <i class="fas fa-spinner fa-spin mr-1"></i>
                    Consultando horarios disponibles...
                `;

                try {
                    const url = new URL(urlHorarios, window.location.origin);
                    url.searchParams.set('servicio_id', servicioId);
                    url.searchParams.set('fecha', fecha);
                    url.searchParams.set('cantidad_personas', cantidadPersonas());

                    const respuesta = await fetch(url, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const datos = await respuesta.json();

                    if (!respuesta.ok) {
                        throw new Error(datos.message || 'No fue posible consultar los horarios.');
                    }

                    const todosLosHorarios = datos.horarios || [];

                    const ahora = new Date();

                    const fechaHoy = [
                        ahora.getFullYear(),
                        String(ahora.getMonth() + 1).padStart(2, '0'),
                        String(ahora.getDate()).padStart(2, '0'),
                    ].join('-');

                    const minutosActuales =
                        (ahora.getHours() * 60) + ahora.getMinutes();

                    const horarios = todosLosHorarios.filter(function(horario) {
                        /*
                         * Para fechas futuras se muestran todos los horarios
                         * recibidos desde Laravel.
                         */
                        if (fecha !== fechaHoy) {
                            return true;
                        }

                        /*
                         * Para hoy solamente se muestran horarios que todavía
                         * no hayan comenzado.
                         */
                        const minutosInicio =
                            convertirHoraMinutos(horario.hora_inicio);

                        return minutosInicio > minutosActuales;
                    });

                    if (!horarios.length) {
                        mensajeHorarios.className = 'mensaje-horarios alert alert-warning mb-3';
                        mensajeHorarios.innerHTML = `
                            <i class="fas fa-calendar-times mr-1"></i>
                            No existen horarios futuros disponibles para la fecha seleccionada.
                        `;
                        return;
                    }

                    mensajeHorarios.classList.add('d-none');

                    horarios.forEach(horario => {
                        const columna = document.createElement('div');
                        columna.className = 'col-xl-3 col-lg-4 col-md-6 col-12 mb-3';

                        const boton = document.createElement('button');
                        boton.type = 'button';
                        boton.className = 'horario-option';
                        boton.dataset.horarioId = horario.id;
                        boton.dataset.fecha = fecha;
                        boton.dataset.horaInicio = horario.hora_inicio;
                        boton.dataset.horaTermino = horario.hora_termino;
                        boton.dataset.disponibleOriginal = horario.disponible ? '1' : '0';

                        boton.innerHTML = `
                            <span class="horario-franja">
                                ${escaparHtml(horario.hora_inicio)} -
                                ${escaparHtml(horario.hora_termino)}
                            </span>
                        `;

                        if (!horario.disponible) {
                            boton.disabled = true;
                            boton.classList.add('no-disponible');
                            boton.title = 'No quedan cupos suficientes para la cantidad de asistentes.';
                        } else {
                            boton.addEventListener('click', function() {
                                seleccionarHorario(card, boton);
                            });
                        }

                        columna.appendChild(boton);
                        contenedorHorarios.appendChild(columna);

                        if (String(horario.id) === String(horarioASeleccionar) && horario.disponible) {
                            seleccionarHorario(card, boton);
                        }
                    });

                    actualizarDisponibilidadCruzada();
                } catch (error) {
                    console.error(error);
                    mensajeHorarios.className = 'mensaje-horarios alert alert-danger mb-3';
                    mensajeHorarios.textContent = error.message;
                }
            }

            function convertirHoraMinutos(hora) {
                const [h, m] = String(hora).substring(0, 5).split(':').map(Number);
                return (h * 60) + m;
            }

            function seSuperponen(inicioA, terminoA, inicioB, terminoB) {
                return convertirHoraMinutos(inicioA) < convertirHoraMinutos(terminoB) &&
                    convertirHoraMinutos(terminoA) > convertirHoraMinutos(inicioB);
            }

            function horariosSeleccionados(cardExcluida = null) {
                return obtenerCardsSeleccionadas()
                    .filter(card => card !== cardExcluida)
                    .map(card => card.querySelector('.horario-option.selected'))
                    .filter(Boolean);
            }

            function actualizarDisponibilidadCruzada() {
                obtenerCardsSeleccionadas().forEach(card => {
                    const otros = horariosSeleccionados(card);

                    card.querySelectorAll('.horario-option').forEach(boton => {
                        if (boton.dataset.disponibleOriginal === '0') {
                            return;
                        }

                        const esSeleccionado = boton.classList.contains('selected');
                        const conflicto = otros.some(otro =>
                            otro.dataset.fecha === boton.dataset.fecha &&
                            seSuperponen(
                                boton.dataset.horaInicio,
                                boton.dataset.horaTermino,
                                otro.dataset.horaInicio,
                                otro.dataset.horaTermino
                            )
                        );

                        boton.disabled = conflicto && !esSeleccionado;
                        boton.classList.toggle('no-disponible', conflicto && !esSeleccionado);
                    });
                });
            }

            function seleccionarHorario(card, boton) {
                const input = card.querySelector('.horario-id');
                const yaSeleccionado = boton.classList.contains('selected');

                card.querySelectorAll('.horario-option').forEach(item => {
                    item.classList.remove('selected');
                });

                if (yaSeleccionado) {
                    input.value = '';
                } else {
                    boton.classList.add('selected');
                    input.value = boton.dataset.horarioId;
                }

                actualizarDisponibilidadCruzada();
            }

            async function cargarServicios(fecha) {
                mensaje.className = 'alert alert-info';

                mensaje.innerHTML = `
        <i class="fas fa-spinner fa-spin mr-1"></i>
        Cargando servicios disponibles...
    `;

                mensaje.classList.remove('d-none');

                try {
                    const url = new URL(
                        urlServicios,
                        window.location.origin
                    );

                    url.searchParams.set(
                        'tipo_operacion',
                        tipoOperacion
                    );

                    if (!esCotizacion) {

                        url.searchParams.set(
                            'fecha',
                            fecha
                        );

                        url.searchParams.set(
                            'cantidad_personas',
                            cantidadPersonas()
                        );

                    }

                    const respuesta = await fetch(url, {
                        method: 'GET',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    /*
                     * Primero leer como texto para poder ver el error
                     * real que Laravel está devolviendo.
                     */
                    const contenido = await respuesta.text();

                    let datos;

                    try {
                        datos = JSON.parse(contenido);
                    } catch (error) {
                        console.error(
                            'Laravel devolvió una respuesta que no es JSON:',
                            contenido
                        );

                        throw new Error(
                            'Laravel devolvió HTML en lugar de JSON. ' +
                            'Revisa la pestaña Network y el archivo laravel.log.'
                        );
                    }

                    if (!respuesta.ok) {
                        throw new Error(
                            datos.message ||
                            'No fue posible cargar los servicios.'
                        );
                    }

                    contenedor.innerHTML = '';

                    const servicios = datos.servicios || [];

                    if (!servicios.length) {
                        mensaje.className = 'alert alert-warning';

                        mensaje.innerHTML = `
                <i class="fas fa-calendar-times mr-1"></i>
                No existen servicios con horarios disponibles.
            `;

                        return;
                    }

                    servicios.forEach(function(servicio) {
                        contenedor.appendChild(
                            crearTarjeta(servicio)
                        );
                    });

                    mensaje.classList.add('d-none');

                    await restaurarDatosAnteriores();

                    actualizarEstadoServicios();
                } catch (error) {
                    console.error(error);

                    mensaje.className = 'alert alert-danger';

                    mensaje.innerHTML = `
            <i class="fas fa-exclamation-triangle mr-1"></i>
            ${escaparHtml(error.message)}
        `;
                }
            }

            async function restaurarDatosAnteriores() {

                const entradas =
                    Object.values(datosAnteriores || {});


                for (const datos of entradas) {

                    const servicioId =
                        String(datos.servicio_id || '');

                    const card =
                        contenedor.querySelector(
                            `.servicio-card[data-servicio-id="${servicioId}"]`
                        );


                    if (!card) {
                        continue;
                    }


                    const checkbox =
                        card.querySelector('.servicio-checkbox');


                    checkbox.checked = true;

                    actualizarEstadoServicios();


                    /*
                     * En cotización solamente restauramos
                     * el servicio seleccionado.
                     *
                     * No existen fecha ni horario.
                     */

                    if (esCotizacion) {
                        continue;
                    }

                    const fecha =
                        card.querySelector('.fecha-servicio');

                    if (!fecha) {
                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Fecha del servicio
                    |--------------------------------------------------------------------------
                    |
                    | Si venimos de una reserva anterior, usamos datos.fecha.
                    |
                    | Si venimos de una cotización convertida, la cotización no tiene fecha,
                    | por lo que usamos la fecha general seleccionada en fecha_reserva.
                    |
                    */

                    const fechaSeleccionada =
                        datos.fecha ||
                        fechaReservaInput?.value ||
                        '';

                    fecha.value =
                        fechaSeleccionada;


                    /*
                    |--------------------------------------------------------------------------
                    | Consultar horarios
                    |--------------------------------------------------------------------------
                    */

                    if (fechaSeleccionada) {

                        await cargarHorarios(
                            card,
                            fechaSeleccionada,
                            datos.horario_id || ''
                        );

                    }
                }
            }

            function recargarHorariosSeleccionados() {
                obtenerCardsSeleccionadas().forEach(card => {
                    const fecha = card.querySelector('.fecha-servicio').value;
                    const horarioActual = card.querySelector('.horario-id').value;

                    if (fecha) {
                        cargarHorarios(card, fecha, horarioActual);
                    }
                });
            }

            [cantidadInput, alumnosInput, profesoresInput]
            .filter(Boolean)
                .forEach(input => {
                    input.addEventListener('input', function() {
                        actualizarPrecio();

                        if (!esCotizacion) {
                            clearTimeout(window.__recargaHorarios);
                            window.__recargaHorarios = setTimeout(recargarHorariosSeleccionados, 350);
                        }
                    });
                });

            formulario.addEventListener('submit', function(event) {
                const cards = obtenerCardsSeleccionadas();

                if (!cards.length) {
                    event.preventDefault();
                    console.log(typeof Swal);
                    Swal.fire({
                        icon: 'info',
                        title: 'Selecciona un servicio',
                        html: `
                        Antes de continuar debes seleccionar
                        <strong>al menos un servicio</strong>.
                            `,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#17a2b8',
                        allowOutsideClick: false,
                        allowEscapeKey: true
                    });
                    return;
                }

                if (!esCotizacion) {

                    const incompletos = cards.some(card => {

                        const fecha =
                            card.querySelector('.fecha-servicio');

                        const horario =
                            card.querySelector('.horario-id');

                        return (
                            !fecha?.value ||
                            !horario?.value
                        );
                    });


                    if (incompletos) {

                        event.preventDefault();

                        Swal.fire({
                            icon: 'warning',
                            title: 'Faltan datos',
                            html: `
                <p class="mb-0">
                    Antes de continuar debes seleccionar un
                    <strong>
                        horario para cada servicio.
                    </strong>
                </p>
            `,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#17a2b8'
                        });

                        return;
                    }
                }
            });

            cantidadPersonas();

            if (fechaReservaInput) {

                fechaReservaInput.addEventListener(
                    'change',
                    async function() {

                        const fecha = this.value;

                        contenedor.innerHTML = '';

                        totalSeleccionados.textContent = '0';

                        serviciosError.classList.add('d-none');

                        detalleServicios.innerHTML = `
                <div class="text-muted">
                    Selecciona un servicio para ver el detalle.
                </div>
            `;

                        precioTotal.textContent = formatearPrecio(0);


                        if (!fecha) {

                            mensaje.className =
                                'alert alert-light border text-center';

                            mensaje.innerHTML = `
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Selecciona una fecha para consultar
                    los servicios disponibles.
                `;

                            return;
                        }


                        await cargarServicios(fecha);
                    }
                );

            }

            if (esCotizacion) {

                cargarServicios('');

            } else if (fechaReservaInput && fechaReservaInput.value) {

                cargarServicios(
                    fechaReservaInput.value
                );

            }

            function obtenerEntradasLiberadas(personas) {

                /*
                 * Persona natural:
                 * nunca tiene entradas liberadas.
                 */
                if (esPersona) {
                    return 0;
                }


                /*
                 * Establecimiento educacional
                 * con 26 o más asistentes:
                 * 2 entradas liberadas.
                 */
                if (
                    esEstablecimientoEducacional &&
                    personas >= 26
                ) {
                    return 2;
                }


                /*
                 * Cualquier cliente que NO sea
                 * persona natural, desde 11 personas:
                 * 1 entrada liberada.
                 */
                if (personas >= 11) {
                    return 1;
                }


                return 0;
            }

            function cantidadPersonasCobradas(personas) {

                const entradasLiberadas =
                    obtenerEntradasLiberadas(
                        personas
                    );

                return Math.max(
                    personas - entradasLiberadas,
                    0
                );
            }
        });
    </script>
@stop
