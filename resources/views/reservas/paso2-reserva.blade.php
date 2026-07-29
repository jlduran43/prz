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
        $datosReserva = $datosReserva ?? session('reserva.reserva', []);

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
                    Servicios y experiencias
                </h3>
            </div>

            <div class="card-body">
                <p class="text-muted mb-4">
                    Puedes seleccionar hasta 2 servicios en total.
                </p>

                <div id="serviciosContainer" class="row">
                    @foreach ($categoriasServicio as $categoria)
                        <div class="col-lg-6 mb-4">
                            <div class="service-category-card h-100">

                                <div class="service-category-header">
                                    <div>
                                        <h5 class="mb-1">
                                            {{ $categoria->nombre }}
                                        </h5>

                                        <p class="text-muted small mb-0">
                                            Selecciona uno o más servicios
                                            de esta categoría.
                                        </p>
                                    </div>

                                    <span class="badge badge-primary">
                                        <span class="category-count" data-category="{{ $categoria->id }}">
                                            0
                                        </span>
                                        seleccionados
                                    </span>
                                </div>

                                <div class="service-options">
                                    @forelse ($categoria->servicios as $servicio)
                                        <label class="service-option" for="servicio_{{ $servicio->id }}">
                                            <input type="checkbox" name="servicios[]" id="servicio_{{ $servicio->id }}"
                                                value="{{ $servicio->id }}" class="service-checkbox"
                                                data-category="{{ $categoria->id }}" data-nombre="{{ $servicio->nombre }}"
                                                data-precio="{{ $servicio->precio }}"
                                                data-tipo-cobro="{{ $servicio->tipo_cobro }}" @checked(in_array($servicio->id, old('servicios', $datosReserva['servicios'] ?? [])))>

                                            <span class="service-option-content">
                                                <span class="service-name">
                                                    {{ $servicio->nombre }}
                                                </span>
                                            </span>

                                            <span class="service-check-icon">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </label>
                                    @empty
                                        <div class="text-muted">
                                            No hay servicios disponibles.
                                        </div>
                                    @endforelse
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="service-selection-summary">
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

        {{-- Fecha y horario --}}
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Fecha y horario
                </h3>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">
                                Fecha
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date" name="fecha" id="fecha"
                                class="form-control
                                    @error('fecha') is-invalid @enderror"
                                value="{{ old('fecha', $datosReserva['fecha'] ?? '') }}"
                                min="{{ now()->format('Y-m-d') }}" required>

                            @error('fecha')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora_inicio">
                                Hora de ingreso
                                <span class="text-danger">*</span>
                            </label>

                            <input type="time" name="hora_inicio" id="hora_inicio"
                                class="form-control
                                    @error('hora_inicio') is-invalid @enderror"
                                value="{{ old('hora_inicio', $datosReserva['hora_inicio'] ?? '') }}" required>

                            @error('hora_inicio')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora_termino">
                                Hora de salida
                                <span class="text-danger">*</span>
                            </label>

                            <input type="time" name="hora_termino" id="hora_termino"
                                class="form-control
                                    @error('hora_termino') is-invalid @enderror"
                                value="{{ old('hora_termino', $datosReserva['hora_termino'] ?? '') }}" required>

                            @error('hora_termino')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

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
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maximoServicios = 2;

            const checkboxes = Array.from(
                document.querySelectorAll('.service-checkbox')
            );

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

            function obtenerSeleccionados() {
                return checkboxes.filter(function(checkbox) {
                    return checkbox.checked;
                });
            }

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

                checkboxes.forEach(function(checkbox) {
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

            function calcularSubtotal(precio, cantidadPersonas) {
                return precio * cantidadPersonas;
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
                        cantidadPersonas
                    );

                    total += subtotal;

                    const descripcion = formatearPrecio(precio) +
                        ' por persona × ' +
                        cantidadPersonas;

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
            }

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const seleccionados = obtenerSeleccionados();

                    if (seleccionados.length > maximoServicios) {
                        checkbox.checked = false;
                        errorElement.classList.remove('d-none');
                    } else {
                        errorElement.classList.add('d-none');
                    }

                    actualizarInterfaz();
                });
            });

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
            actualizarInterfaz();
        });
    </script>
@stop
