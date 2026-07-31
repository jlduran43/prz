@extends('adminlte::page')

@section('title', 'Nueva reserva')

@section('content_header')
    @php
        /*
        |--------------------------------------------------------------------------
        | Tipo de cliente del paso anterior
        |--------------------------------------------------------------------------
        | El controlador puede enviar $esEstablecimiento directamente.
        | También se admite el código almacenado en la sesión.
        */
        $datosCliente = $datosCliente ?? session('reserva.cliente', []);
        $datosReserva = $datosReserva ?? session('reserva.datos', []);

        $codigoTipoCliente = $datosCliente['codigo_tipo_cliente'] ?? ($datosCliente['tipo_cliente_codigo'] ?? null);

        $esEstablecimiento = $esEstablecimiento ?? $codigoTipoCliente === 'ESTABLECIMIENTO_EDUCACIONAL';
    @endphp

    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1">Nueva reserva</h1>

            <p class="text-muted mb-0">
                Ingresa los servicios, asistentes, fecha y horario de la visita.
            </p>
        </div>
    </div>
@stop

@section('content')

    {{-- Indicador de pasos --}}
    @include('reservas.partials._wizard', ['paso' => 2])

    {{-- Mensajes --}}
    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="font-weight-bold mb-2">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Revisa los siguientes datos:
            </div>

            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reservas.datos.guardar') }}" method="POST" id="formReserva">
        @csrf

        {{-- Servicios --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-star mr-1"></i>
                    Servicios disponibles
                </h3>
            </div>

            <div class="card-body">
                <p class="text-muted mb-3">
                    Selecciona hasta dos servicios. Después podrás indicar
                    una fecha y un horario para cada uno.
                </p>

                <div id="mensaje-servicios" class="alert alert-info mb-3">
                    <i class="fas fa-spinner fa-spin mr-1"></i>
                    Cargando servicios disponibles...
                </div>

                <div id="contenedor-servicios" class="row" hidden></div>

                <div class="service-selection-summary mt-3">
                    <div>
                        <span class="d-block font-weight-bold">
                            Servicios seleccionados
                        </span>

                        <small class="text-muted">
                            Puedes seleccionar un máximo de 2.
                        </small>
                    </div>

                    <strong class="text-primary h4 mb-0">
                        <span id="totalServiciosSeleccionados">0</span>/2
                    </strong>
                </div>

                <div id="serviciosError" class="text-danger mt-2 d-none">
                    Solamente puedes seleccionar un máximo de 2 servicios.
                </div>

                @error('servicios')
                    <div class="text-danger mt-2">
                        {{ $message }}
                    </div>
                @enderror

                @error('servicios.*')
                    <div class="text-danger mt-2">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-1"></i>
                    Horarios de los servicios
                </h3>
            </div>

            <div class="card-body">

                <div class="alert alert-info mb-3">
                    Selecciona un horario para cada servicio.
                </div>

                <div id="horariosPorServicio"></div>

            </div>
        </div>

        {{-- Asistentes y precio --}}
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Detalle de asistentes y precio
                </h3>
            </div>

            <div class="card-body">

                @if ($esEstablecimiento)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>

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
                                    class="form-control
                                        @error('cantidad_alumnos') is-invalid @enderror"
                                    value="{{ old('cantidad_alumnos', $datosReserva['cantidad_alumnos'] ?? 1) }}"
                                    min="1" required>

                                @error('cantidad_alumnos')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
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
                                    class="form-control
                                        @error('cantidad_profesores') is-invalid @enderror"
                                    value="{{ old('cantidad_profesores', $datosReserva['cantidad_profesores'] ?? 0) }}"
                                    min="0" required>

                                @error('cantidad_profesores')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
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
                                    $nivelesEducacionales = [
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
                                    class="form-control
                                        @error('nivel_educacional') is-invalid @enderror"
                                    required>
                                    <option value="">
                                        Seleccione un nivel
                                    </option>

                                    @foreach ($nivelesEducacionales as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected($nivelSeleccionado === $codigo)>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('nivel_educacional')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
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
                                    class="form-control
                                        @error('curso') is-invalid @enderror"
                                    value="{{ old('curso', $datosReserva['curso'] ?? '') }}"
                                    placeholder="Ej.: 4.º básico A" maxlength="100" required>

                                @error('curso')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 mb-3">
                            <div class="visitor-summary">
                                <div>
                                    <span class="d-block font-weight-bold">
                                        Total de asistentes
                                    </span>

                                    <small class="text-muted">
                                        Alumnos más profesores.
                                    </small>
                                </div>

                                <strong id="totalAsistentesEducacional" class="text-info">
                                    1
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="price-summary">
                                <span class="font-weight-bold">
                                    Total estimado
                                </span>

                                <strong id="precioTotal" class="text-primary">
                                    $0
                                </strong>
                            </div>
                        </div>
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
                                    class="form-control
                                        @error('cantidad_asistentes') is-invalid @enderror"
                                    value="{{ old('cantidad_asistentes', $datosReserva['cantidad_asistentes'] ?? 1) }}"
                                    min="1" required>

                                @error('cantidad_asistentes')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="price-summary mt-md-4">
                                <span class="font-weight-bold">
                                    Total estimado
                                </span>

                                <strong id="precioTotal" class="text-primary">
                                    $0
                                </strong>
                            </div>
                        </div>

                    </div>
                @endif

                <div id="detalleServicios" class="mt-3"></div>
            </div>
        </div>

        {{-- Botones de navegación --}}
        <div class="card">
            <div class="card-footer">
                <div class="row align-items-center">

                    <div class="col-6 text-left">
                        <a href="{{ route('reservas.cliente') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Volver
                        </a>
                    </div>

                    <div class="col-6 text-right">
                        <button type="submit" class="btn btn-primary">
                            Continuar
                            <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </form>
@stop

@section('css')
    <style>
        .service-category-card {
            padding: 18px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #ffffff;
        }

        .service-category-header {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid #dee2e6;
        }

        .service-options {
            display: grid;
            gap: 10px;
        }

        .service-option {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 64px;
            padding: 12px 14px;
            margin: 0;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #ffffff;
            cursor: pointer;
            transition: .2s ease;
        }

        .service-option:hover {
            border-color: #007bff;
            background: #f8fbff;
        }

        .service-option.selected {
            border-color: #007bff;
            background: #eef6ff;
        }

        .service-option.disabled {
            cursor: not-allowed;
            opacity: .55;
        }

        .service-checkbox {
            width: 18px;
            height: 18px;
            margin: 0;
        }

        .service-option-content {
            display: flex;
            flex: 1;
            flex-direction: column;
            min-width: 0;
        }

        .service-name {
            color: #343a40;
            font-size: 14px;
            font-weight: 700;
        }

        .service-description {
            margin-top: 3px;
            color: #6c757d;
            font-size: 12px;
        }

        .service-check-icon {
            display: none;
            place-items: center;
            flex: 0 0 28px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #007bff;
            color: #ffffff;
        }

        .service-option.selected .service-check-icon {
            display: grid;
        }

        .service-selection-summary,
        .visitor-summary,
        .price-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 76px;
            padding: 14px 16px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .visitor-summary strong,
        .price-summary strong {
            font-size: 28px;
            line-height: 1;
        }

        .price-detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 14px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .price-detail-row:last-child {
            border-bottom: 0;
        }

        .price-service-name {
            display: block;
            color: #343a40;
            font-size: 14px;
            font-weight: 700;
        }

        .price-service-description {
            margin-top: 4px;
            color: #6c757d;
            font-size: 13px;
        }

        .price-service-subtotal {
            flex-shrink: 0;
            color: #343a40;
            font-size: 15px;
        }

        .empty-price-detail {
            padding: 14px 16px;
            border: 1px dashed #ced4da;
            border-radius: 8px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 14px;
        }

        @media (max-width: 575.98px) {

            .service-category-header,
            .price-detail-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }



        button.horario-option:hover {
            border-color: #007bff;
            background-color: #f4f9ff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, .12);
        }

        button.horario-option.selected,
        button.horario-option.selected:hover,
        button.horario-option.selected:focus,
        button.horario-option.selected:active {
            border: 2px solid #007bff !important;
            background-color: #eaf4ff !important;
            color: #212529 !important;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, .18) !important;
        }

        button.horario-option.selected .horario-icono {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        .horario-option.no-disponible {
            cursor: not-allowed;
            opacity: .55;
            background: #f8f9fa;
        }

        .horario-option-icon {
            margin-right: 6px;
            color: #007bff;
        }

        .horario-option.selected::after {
            position: absolute;
            top: 10px;
            right: 12px;
            color: #007bff;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            content: "\f058";
        }

        button.horario-option.selected .horario-seleccionado {
            display: block;
            color: #007bff;
        }

        .horario-option.no-disponible:hover {
            border-color: #d9dee3;
            box-shadow: none;
            transform: none;
        }

        button.horario-option:focus {
            outline: none;
        }

        button.horario-option {
            position: relative;
            width: 100%;
            min-height: 92px;
            padding: 14px 16px;
            border-width: 2px;
            border-radius: 10px;
            background-color: #ffffff;
            text-align: left;
            white-space: normal;
            transition:
                background-color .2s ease,
                border-color .2s ease,
                box-shadow .2s ease,
                transform .2s ease;
        }

        button.horario-option:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, .16);
        }

        button.horario-option:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, .20);
        }

        button.horario-option.selected,
        button.horario-option.btn-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #ffffff !important;
        }

        .horario-icono {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: rgba(0, 123, 255, .12);
            color: #007bff;
            font-size: 18px;
        }

        button.horario-option.selected .horario-icono {
            background-color: rgba(255, 255, 255, .20);
            color: #ffffff;
        }

        .horario-franja {
            display: block;
            font-size: 17px;
            font-weight: 600;
            line-height: 1.3;
        }

        button.horario-option.selected .horario-franja {
            color: #ffffff;
        }



        button.horario-option.selected .horario-check {
            display: block;
        }

        button.horario-option.no-disponible {
            cursor: not-allowed;
            opacity: .55;
            background-color: #f8f9fa;
            border-color: #ced4da;
            color: #6c757d;
        }

        .horario-check {
            display: none;
            margin-left: 20px;
            padding-left: 16px;
            border-left: 1px solid rgba(0, 123, 255, .20);
            color: #007bff;
            font-size: 22px;
        }

        .horario-servicio-card {
            padding: 18px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .horario-servicio-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid #dee2e6;
        }

        @media (max-width: 575.98px) {
            .horario-servicio-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maximoServicios = 2;

            const urlServiciosDisponibles =
                @json(route('reservas.servicios-disponibles'));

            const urlConsultarHorarios =
                @json(route('reservas.consultar-horarios'));

            const cantidadInput =
                document.getElementById('cantidad_asistentes');

            const cantidadAlumnosInput =
                document.getElementById('cantidad_alumnos');

            const cantidadProfesoresInput =
                document.getElementById('cantidad_profesores');

            const totalAsistentesEducacional =
                document.getElementById('totalAsistentesEducacional');

            const totalElement =
                document.getElementById('totalServiciosSeleccionados');

            const precioTotalElement =
                document.getElementById('precioTotal');

            const detalleServiciosElement =
                document.getElementById('detalleServicios');

            const errorElement =
                document.getElementById('serviciosError');

            const contenedorServicios =
                document.getElementById('contenedor-servicios');

            const mensajeServicios =
                document.getElementById('mensaje-servicios');

            const horariosPorServicio =
                document.getElementById('horariosPorServicio');

            function obtenerCheckboxes() {
                return Array.from(
                    document.querySelectorAll('.service-checkbox')
                );
            }

            function obtenerSeleccionados() {
                return obtenerCheckboxes().filter(function(checkbox) {
                    return checkbox.checked;
                });
            }

            contenedorServicios.addEventListener(
                'change',
                function(event) {
                    const checkbox =
                        event.target.closest('.service-checkbox');

                    if (!checkbox) {
                        return;
                    }

                    const seleccionados =
                        obtenerSeleccionados();

                    if (seleccionados.length > maximoServicios) {
                        checkbox.checked = false;

                        errorElement.classList.remove('d-none');
                    } else {
                        errorElement.classList.add('d-none');
                    }

                    actualizarInterfaz();
                }
            );

            function formatearPrecio(valor) {
                return new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP',
                    maximumFractionDigits: 0,
                }).format(valor);
            }

            function actualizarCantidadEducacional() {
                if (
                    !cantidadAlumnosInput ||
                    !cantidadProfesoresInput ||
                    !cantidadInput
                ) {
                    return;
                }

                const alumnos = Math.max(
                    Number(cantidadAlumnosInput.value) || 0,
                    0
                );

                const profesores = Math.max(
                    Number(cantidadProfesoresInput.value) || 0,
                    0
                );

                const total = alumnos + profesores;

                cantidadInput.value = total;

                if (totalAsistentesEducacional) {
                    totalAsistentesEducacional.textContent = total;
                }
            }

            function actualizarContadores() {
                const seleccionados = obtenerSeleccionados();

                totalElement.textContent = seleccionados.length;

                document
                    .querySelectorAll('.category-count')
                    .forEach(function(contador) {
                        const categoriaId = contador.dataset.category;

                        const cantidadCategoria = seleccionados.filter(
                            function(checkbox) {
                                return (
                                    checkbox.dataset.category ===
                                    categoriaId
                                );
                            }
                        ).length;

                        contador.textContent = cantidadCategoria;
                    });
            }

            function actualizarEstadoVisual() {
                const seleccionados = obtenerSeleccionados();
                const seAlcanzoMaximo =
                    seleccionados.length >= maximoServicios;

                obtenerCheckboxes().forEach(function(checkbox) {
                    const contenedor =
                        checkbox.closest('.service-option');

                    checkbox.disabled =
                        seAlcanzoMaximo && !checkbox.checked;

                    contenedor.classList.toggle(
                        'selected',
                        checkbox.checked
                    );

                    contenedor.classList.toggle(
                        'disabled',
                        checkbox.disabled
                    );
                });
            }

            function calcularSubtotal(
                precio,
                tipoCobro,
                cantidadPersonas
            ) {
                if (tipoCobro === 'POR_PERSONA') {
                    return precio * cantidadPersonas;
                }

                return precio;
            }

            function obtenerDescripcionCobro(
                precio,
                tipoCobro,
                cantidadPersonas
            ) {
                if (tipoCobro === 'POR_PERSONA') {
                    return (
                        formatearPrecio(precio) +
                        ' por persona × ' +
                        cantidadPersonas
                    );
                }

                if (tipoCobro === 'POR_GRUPO') {
                    return formatearPrecio(precio) + ' por grupo';
                }

                return 'Precio fijo';
            }

            function calcularPrecio() {
                const seleccionados = obtenerSeleccionados();

                const cantidadPersonas = Math.max(
                    Number(cantidadInput?.value) || 0,
                    0
                );

                let total = 0;
                let detalleHtml = '';

                if (seleccionados.length === 0) {
                    detalleServiciosElement.innerHTML = `
                        <div class="empty-price-detail">
                            Selecciona al menos un servicio para ver
                            el detalle del precio.
                        </div>
                    `;

                    precioTotalElement.textContent =
                        formatearPrecio(0);

                    return;
                }

                seleccionados.forEach(function(checkbox) {
                    const nombre =
                        checkbox.dataset.nombre || 'Servicio';

                    const precio =
                        Number(checkbox.dataset.precio) || 0;

                    const tipoCobro =
                        checkbox.dataset.tipoCobro || 'FIJO';

                    const subtotal = calcularSubtotal(
                        precio,
                        tipoCobro,
                        cantidadPersonas
                    );

                    total += subtotal;

                    const descripcion = obtenerDescripcionCobro(
                        precio,
                        tipoCobro,
                        cantidadPersonas
                    );

                    detalleHtml += `
                        <div class="price-detail-row">
                            <div>
                                <strong class="price-service-name">
                                    ${nombre}
                                </strong>

                                <div class="price-service-description">
                                    ${descripcion}
                                </div>
                            </div>

                            <strong class="price-service-subtotal">
                                ${formatearPrecio(subtotal)}
                            </strong>
                        </div>
                    `;
                });

                detalleServiciosElement.innerHTML = detalleHtml;

                precioTotalElement.textContent =
                    formatearPrecio(total);
            }

            function actualizarInterfaz() {
                actualizarContadores();
                actualizarEstadoVisual();
                calcularPrecio();
                actualizarHorariosPorServicio();
            }

            if (
                cantidadInput &&
                cantidadInput.type !== 'hidden'
            ) {
                cantidadInput.addEventListener(
                    'input',
                    calcularPrecio
                );
            }

            if (
                cantidadAlumnosInput &&
                cantidadProfesoresInput
            ) {
                cantidadAlumnosInput.addEventListener(
                    'input',
                    function() {
                        actualizarCantidadEducacional();
                        calcularPrecio();
                    }
                );

                cantidadProfesoresInput.addEventListener(
                    'input',
                    function() {
                        actualizarCantidadEducacional();
                        calcularPrecio();
                    }
                );
            }

            actualizarCantidadEducacional();
            calcularPrecio();
            cargarServiciosDisponibles();

            function escaparHtml(valor) {
                const elemento = document.createElement('div');

                elemento.textContent = String(valor ?? '');

                return elemento.innerHTML;
            }

            if (!mensajeServicios) {
                console.error(
                    'No se encontró el elemento #mensaje-servicios'
                );

                return;
            }

            function mostrarMensajeSeleccionarFecha() {
                mensajeServicios.className =
                    'alert alert-info mb-3';

                mensajeServicios.innerHTML = `
        <i class="fas fa-calendar-alt mr-1"></i>
        Selecciona una fecha para consultar
        los servicios disponibles.
    `;

                mensajeServicios.hidden = false;
            }

            function mostrarCargandoServicios() {
                mensajeServicios.className =
                    'alert alert-info mb-3';

                mensajeServicios.innerHTML = `
        <i class="fas fa-spinner fa-spin mr-1"></i>
        Consultando servicios disponibles...
    `;

                mensajeServicios.hidden = false;
                contenedorServicios.hidden = true;
            }

            function mostrarErrorServicios(mensaje) {
                mensajeServicios.className =
                    'alert alert-danger mb-3';

                mensajeServicios.innerHTML = `
        <i class="fas fa-exclamation-triangle mr-1"></i>
        ${escaparHtml(mensaje)}
    `;

                mensajeServicios.hidden = false;
                contenedorServicios.hidden = true;
            }

            function crearTarjetaServicio(servicio) {
                const precio = Number(servicio.precio) || 0;

                const categoria =
                    servicio.categoria?.nombre ||
                    'Sin categoría';

                return `
        <div class="col-lg-6 mb-3">
            <label
                class="service-option h-100"
                for="servicio_${servicio.id}"
            >
                <input
                    type="checkbox"
                    id="servicio_${servicio.id}"
                    value="${servicio.id}"
                    class="service-checkbox"
                    data-nombre="${escaparHtml(servicio.nombre)}"
                    data-precio="${precio}"
                    data-tipo-cobro="${escaparHtml(
                        servicio.tipo_cobro
                    )}"
                >

                <span class="service-option-content">
                    <span class="service-name">
                        ${escaparHtml(servicio.nombre)}
                    </span>

                    <span class="mt-2">
                        <span class="badge badge-info">
                            ${escaparHtml(categoria)}
                        </span>
                    </span>

                    <strong class="text-primary mt-2">
                        ${formatearPrecio(precio)}
                    </strong>
                </span>

                <span class="service-check-icon">
                    <i class="fas fa-check"></i>
                </span>
            </label>
        </div>
    `;
            }

            function renderizarServiciosDisponibles(servicios) {
                contenedorServicios.innerHTML = '';

                if (!servicios.length) {
                    mensajeServicios.className =
                        'alert alert-warning mb-3';

                    mensajeServicios.innerHTML = `
            <i class="fas fa-calendar-times mr-1"></i>
            No existen servicios disponibles para
            la fecha seleccionada.
        `;

                    mensajeServicios.hidden = false;
                    contenedorServicios.hidden = true;

                    return;
                }

                servicios.forEach(function(servicio) {
                    contenedorServicios.insertAdjacentHTML(
                        'beforeend',
                        crearTarjetaServicio(servicio)
                    );
                });

                mensajeServicios.hidden = true;
                contenedorServicios.hidden = false;

                actualizarInterfaz();
            }

            function limpiarSeleccionReserva() {
                contenedorServicios.innerHTML = '';
                contenedorServicios.hidden = true;

                horariosPorServicio.innerHTML = '';

                if (totalElement) {
                    totalElement.textContent = '0';
                }

                if (errorElement) {
                    errorElement.classList.add('d-none');
                }

                calcularPrecio();
            }

            async function cargarServiciosDisponibles() {
                mostrarCargandoServicios();

                try {
                    const url = new URL(
                        urlServiciosDisponibles,
                        window.location.origin
                    );

                    const respuesta = await fetch(url, {
                        method: 'GET',

                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const contenido = await respuesta.text();

                    let datos;

                    try {
                        datos = JSON.parse(contenido);
                    } catch (error) {
                        console.error(
                            'Respuesta recibida desde Laravel:',
                            contenido
                        );

                        throw new Error(
                            'Laravel no devolvió JSON. ' +
                            'Revisa la consola del navegador.'
                        );
                    }

                    if (!respuesta.ok) {
                        throw new Error(
                            datos.message ||
                            'No fue posible consultar los servicios.'
                        );
                    }

                    renderizarServiciosDisponibles(
                        datos.servicios || []
                    );
                } catch (error) {
                    console.error(error);

                    mostrarErrorServicios(
                        error.message
                    );
                }
            }

            function obtenerHorarioAnterior(servicioId) {
                const horariosGuardados = @json(old('horarios', $datosReserva['horarios'] ?? []));

                return String(
                    horariosGuardados[servicioId] || ''
                );
            }

            function crearBloqueServicio(checkbox) {
                const servicioId = checkbox.value;

                const nombre =
                    checkbox.dataset.nombre || 'Servicio';

                const bloque =
                    document.createElement('div');

                bloque.className =
                    'horario-servicio-card mb-3';

                bloque.dataset.servicioId =
                    servicioId;

                bloque.innerHTML = `
        <div class="horario-servicio-header">
            <div>
                <span class="text-muted small">
                    Servicio seleccionado
                </span>

                <h5 class="mb-0">
                    ${escaparHtml(nombre)}
                </h5>
            </div>

            <span class="badge badge-primary">
                Seleccione una fecha
            </span>
        </div>

        <input
            type="hidden"
            name="servicios[${servicioId}][servicio_id]"
            value="${servicioId}"
        >

        <div class="form-group">
            <label for="fecha_servicio_${servicioId}">
                Fecha del servicio
                <span class="text-danger">*</span>
            </label>

            <input
                type="date"
                id="fecha_servicio_${servicioId}"
                name="servicios[${servicioId}][fecha]"
                class="form-control fecha-servicio"
                min="{{ now()->format('Y-m-d') }}"
                required
            >
        </div>

        <div
            class="mensaje-horario alert alert-info mb-3"
        >
            <i class="fas fa-calendar-alt mr-1"></i>
            Selecciona una fecha para consultar
            los horarios de este servicio.
        </div>

        <div
            class="row horarios-servicio-opciones"
        ></div>

        <input
            type="hidden"
            name="servicios[${servicioId}][horario_id]"
            class="horario-servicio-input"
            value=""
        >
    `;

                horariosPorServicio.appendChild(bloque);

                const fechaServicio =
                    bloque.querySelector('.fecha-servicio');

                fechaServicio.addEventListener(
                    'change',
                    async function() {
                        const fechaSeleccionada =
                            this.value;

                        await cargarHorariosServicio(
                            checkbox,
                            bloque,
                            fechaSeleccionada
                        );
                    }
                );

                return bloque;
            }

            async function cargarHorariosServicio(
                checkbox,
                bloque,
                fecha
            ) {
                const servicioId = checkbox.value;

                const mensaje =
                    bloque.querySelector('.mensaje-horario');

                const contenedor =
                    bloque.querySelector(
                        '.horarios-servicio-opciones'
                    );

                const input =
                    bloque.querySelector(
                        '.horario-servicio-input'
                    );

                contenedor.innerHTML = '';
                input.value = '';

                if (!fecha) {
                    mensaje.className =
                        'mensaje-horario alert alert-info mb-3';

                    mensaje.innerHTML = `
            <i class="fas fa-calendar-alt mr-1"></i>
            Selecciona una fecha para consultar horarios.
        `;

                    mensaje.classList.remove('d-none');

                    return;
                }

                mensaje.className =
                    'mensaje-horario alert alert-info mb-3';

                mensaje.innerHTML = `
        <i class="fas fa-spinner fa-spin mr-1"></i>
        Consultando horarios disponibles...
    `;

                mensaje.classList.remove('d-none');

                try {
                    const url = new URL(
                        urlConsultarHorarios,
                        window.location.origin
                    );

                    url.searchParams.set('fecha', fecha);

                    url.searchParams.set(
                        'servicio_id',
                        servicioId
                    );

                    const respuesta = await fetch(url, {
                        method: 'GET',

                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const datos = await respuesta.json();

                    if (!respuesta.ok) {
                        throw new Error(
                            datos.message ||
                            'No fue posible consultar los horarios.'
                        );
                    }

                    const horarios = datos.horarios || [];

                    if (horarios.length === 0) {
                        mensaje.className =
                            'mensaje-horario alert alert-warning mb-3';

                        mensaje.innerHTML = `
                <i class="fas fa-calendar-times mr-1"></i>
                No existen horarios disponibles
                para este servicio en la fecha seleccionada.
            `;

                        return;
                    }

                    mensaje.classList.add('d-none');

                    horarios.forEach(function(horario) {
                        const columna =
                            document.createElement('div');

                        columna.className =
                            'col-xl-3 col-lg-4 col-md-6 col-12 mb-3';

                        const boton =
                            document.createElement('button');

                        boton.type = 'button';

                        boton.className =
                            'btn btn-outline-primary horario-option';

                        boton.dataset.horarioId = horario.id;
                        boton.dataset.horaInicio =
                            horario.hora_inicio;
                        boton.dataset.horaTermino =
                            horario.hora_termino;
                        boton.dataset.fecha = fecha;

                        boton.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="horario-icono">
                        <i class="fas fa-business-time"></i>
                    </div>

                    <div class="ml-3">
                        <span class="horario-franja">
                            ${horario.hora_inicio}
                            a
                            ${horario.hora_termino}
                        </span>
                    </div>
                </div>
            `;

                        boton.dataset.disponibleOriginal =
                            horario.disponible ? '1' : '0';

                        if (!horario.disponible) {
                            boton.disabled = true;

                            boton.classList.add(
                                'no-disponible'
                            );

                            boton.title =
                                'Este horario no se encuentra disponible.';
                        } else {
                            boton.addEventListener(
                                'click',
                                function() {
                                    seleccionarHorarioServicio(
                                        bloque,
                                        horario.id,
                                        boton
                                    );
                                }
                            );
                        }

                        columna.appendChild(boton);
                        contenedor.appendChild(columna);
                    });

                    actualizarDisponibilidadCruzada();
                } catch (error) {
                    console.error(error);

                    mensaje.className =
                        'mensaje-horario alert alert-danger mb-3';

                    mensaje.innerHTML = `
            <i class="fas fa-exclamation-triangle mr-1"></i>
            ${escaparHtml(error.message)}
        `;
                }
            }

            function convertirHoraAMinutos(hora) {
                if (!hora) {
                    return 0;
                }

                const partes = hora.substring(0, 5).split(':');

                const horas = Number(partes[0]) || 0;
                const minutos = Number(partes[1]) || 0;

                return (horas * 60) + minutos;
            }

            function horariosSeSuperponen(
                inicioA,
                terminoA,
                inicioB,
                terminoB
            ) {
                const inicioAMinutos =
                    convertirHoraAMinutos(inicioA);

                const terminoAMinutos =
                    convertirHoraAMinutos(terminoA);

                const inicioBMinutos =
                    convertirHoraAMinutos(inicioB);

                const terminoBMinutos =
                    convertirHoraAMinutos(terminoB);

                /*
                 * Existe superposición cuando un horario comienza
                 * antes de que termine el otro y termina después
                 * de que comience el otro.
                 */
                return (
                    inicioAMinutos < terminoBMinutos &&
                    terminoAMinutos > inicioBMinutos
                );
            }

            function obtenerHorariosSeleccionados(
                bloqueExcluido = null
            ) {
                return Array.from(
                        horariosPorServicio.querySelectorAll(
                            '.horario-servicio-card'
                        )
                    )
                    .filter(function(bloque) {
                        return bloque !== bloqueExcluido;
                    })
                    .map(function(bloque) {
                        const boton = bloque.querySelector(
                            '.horario-option.selected'
                        );

                        if (!boton) {
                            return null;
                        }

                        return {
                            bloque: bloque,
                            servicioId: bloque.dataset.servicioId,
                            horarioId: boton.dataset.horarioId,
                            fecha: boton.dataset.fecha,
                            horaInicio: boton.dataset.horaInicio,
                            horaTermino: boton.dataset.horaTermino,
                        };
                    })
                    .filter(function(horario) {
                        return horario !== null;
                    });
            }

            function existeConflictoHorario(
                bloqueActual,
                botonSeleccionado
            ) {
                const seleccionados =
                    obtenerHorariosSeleccionados(bloqueActual);

                return seleccionados.some(function(horario) {
                    if (
                        horario.fecha !==
                        botonSeleccionado.dataset.fecha
                    ) {
                        return false;
                    }

                    return horariosSeSuperponen(
                        botonSeleccionado.dataset.horaInicio,
                        botonSeleccionado.dataset.horaTermino,
                        horario.horaInicio,
                        horario.horaTermino
                    );
                });
            }

            function actualizarDisponibilidadCruzada() {
                const bloques = horariosPorServicio.querySelectorAll(
                    '.horario-servicio-card'
                );

                bloques.forEach(function(bloqueActual) {
                    const otrosHorarios =
                        obtenerHorariosSeleccionados(bloqueActual);

                    bloqueActual
                        .querySelectorAll('.horario-option')
                        .forEach(function(boton) {
                            /*
                             * No modificamos botones que originalmente
                             * vienen como no disponibles desde el servidor.
                             */
                            if (boton.dataset.disponibleOriginal === '0') {
                                return;
                            }

                            const esSeleccionado =
                                boton.classList.contains('selected');

                            const tieneConflicto =
                                otrosHorarios.some(function(horario) {
                                    if (
                                        horario.fecha !==
                                        boton.dataset.fecha
                                    ) {
                                        return false;
                                    }

                                    return horariosSeSuperponen(
                                        boton.dataset.horaInicio,
                                        boton.dataset.horaTermino,
                                        horario.horaInicio,
                                        horario.horaTermino
                                    );
                                });

                            boton.disabled =
                                tieneConflicto && !esSeleccionado;

                            boton.classList.toggle(
                                'no-disponible',
                                tieneConflicto && !esSeleccionado
                            );

                            boton.title =
                                tieneConflicto && !esSeleccionado ?
                                'Este horario se superpone con otro servicio seleccionado.' :
                                '';
                        });
                });
            }

            function seleccionarHorarioServicio(
                bloque,
                horarioId,
                botonSeleccionado
            ) {
                const yaEstaSeleccionado =
                    botonSeleccionado.classList.contains('selected');

                const input = bloque.querySelector(
                    '.horario-servicio-input'
                );

                const badge = bloque.querySelector(
                    '.horario-servicio-header .badge'
                );

                /*
                 * Si el usuario vuelve a presionar el mismo horario,
                 * se desmarca.
                 */
                if (yaEstaSeleccionado) {
                    botonSeleccionado.classList.remove(
                        'selected',
                        'btn-primary'
                    );

                    botonSeleccionado.classList.add(
                        'btn-outline-primary'
                    );

                    input.value = '';

                    if (badge) {
                        badge.className = 'badge badge-primary';
                        badge.textContent = 'Seleccione un horario';
                    }

                    actualizarDisponibilidadCruzada();

                    return;
                }

                /*
                 * Verificar que no se superponga con otro servicio.
                 */
                if (
                    existeConflictoHorario(
                        bloque,
                        botonSeleccionado
                    )
                ) {
                    alert(
                        'Este horario se superpone con el horario seleccionado para otro servicio.'
                    );

                    return;
                }

                /*
                 * Quitar la selección anterior del mismo servicio.
                 */
                const botones = bloque.querySelectorAll(
                    '.horario-option'
                );

                botones.forEach(function(boton) {
                    boton.classList.remove(
                        'selected',
                        'btn-primary'
                    );

                    boton.classList.add(
                        'btn-outline-primary'
                    );
                });

                /*
                 * Seleccionar el nuevo horario.
                 */
                botonSeleccionado.classList.remove(
                    'btn-outline-primary'
                );

                botonSeleccionado.classList.add(
                    'selected',
                    'btn-primary'
                );

                input.value = horarioId;

                if (badge) {
                    badge.className = 'badge badge-success';

                    badge.textContent =
                        botonSeleccionado.dataset.horaInicio +
                        ' a ' +
                        botonSeleccionado.dataset.horaTermino;
                }

                actualizarDisponibilidadCruzada();
            }

            function actualizarHorariosPorServicio() {
                const seleccionados = obtenerSeleccionados();

                /*
                 * Elimina bloques de servicios que ya no estén marcados.
                 */
                horariosPorServicio
                    .querySelectorAll('.horario-servicio-card')
                    .forEach(function(bloque) {
                        const servicioId =
                            bloque.dataset.servicioId;

                        const sigueSeleccionado =
                            seleccionados.some(function(checkbox) {
                                return checkbox.value === servicioId;
                            });

                        if (!sigueSeleccionado) {
                            bloque.remove();
                        }
                    });

                /*
                 * Agrega bloques para servicios nuevos.
                 */
                seleccionados.forEach(function(checkbox) {
                    const servicioId = checkbox.value;

                    const bloque = horariosPorServicio.querySelector(
                        `[data-servicio-id="${servicioId}"]`
                    );

                    if (!bloque) {
                        crearBloqueServicio(
                            checkbox
                        );
                    }
                });
            }
        });
    </script>
@stop
