@extends('adminlte::page')

@section('title', 'Editar convenio')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>

            <h1 class="mb-1">
                Nuevo convenio
            </h1>

            <p class="text-muted mb-0">
                Modifica los datos del convenio, descuento y entidades autorizadas.
            </p>

        </div>

    </div>

@stop


@section('content')

    {{-- Errores generales --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Revisa los siguientes datos:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach

            </ul>

        </div>

    @endif


    <form action="{{ route('convenios.update', $convenio) }}" method="POST" id="formConvenio">

        @csrf
        @method('PUT')

        {{-- ============================================ --}}
        {{-- DATOS GENERALES --}}
        {{-- ============================================ --}}

        <div class="card card-primary">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-handshake mr-2"></i>

                    Datos del convenio

                </h3>

            </div>


            <div class="card-body">

                <div class="row">


                    {{-- Código --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label for="codigo">

                                Código del convenio

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <div class="input-group">

                                <div class="input-group-prepend">

                                    <span class="input-group-text">

                                        <i class="fas fa-key"></i>

                                    </span>

                                </div>


                                <input type="text" name="codigo" id="codigo"
                                    class="
                                        form-control
                                        @error('codigo')
                                            is-invalid
                                        @enderror
                                    "
                                    value="{{ old('codigo', $convenio->codigo) }}" maxlength="50"
                                    placeholder="Ej.: MUNICIPAL2026" required autocomplete="off">

                            </div>


                            @error('codigo')
                                <span class="invalid-feedback d-block">
                                    {{ $message }}
                                </span>
                            @enderror


                            <small class="form-text text-muted">

                                Código que deberá ingresar
                                el cliente al cotizar o reservar.

                            </small>

                        </div>

                    </div>


                    {{-- Nombre --}}
                    <div class="col-md-5">

                        <div class="form-group">

                            <label for="nombre">

                                Nombre del convenio

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text" name="nombre" id="nombre"
                                class="
                                    form-control
                                    @error('nombre')
                                        is-invalid
                                    @enderror
                                "
                                value="{{ old('nombre', $convenio->nombre) }}" maxlength="150"
                                placeholder="Ej.: Convenio Municipalidad de Natales" required>


                            @error('nombre')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>


                    {{-- Porcentaje --}}
                    <div class="col-md-3">

                        <div class="form-group">

                            <label for="porcentaje_descuento">

                                Descuento

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <div class="input-group">

                                <input type="number" name="porcentaje_descuento" id="porcentaje_descuento"
                                    class="
                                        form-control
                                        @error('porcentaje_descuento')
                                            is-invalid
                                        @enderror
                                    "
                                    value="{{ old('porcentaje_descuento', $convenio->porcentaje_descuento) }}"
                                    min="0" max="100" step="0.01" placeholder="15" required>


                                <div class="input-group-append">

                                    <span class="input-group-text">
                                        %
                                    </span>

                                </div>

                            </div>


                            @error('porcentaje_descuento')
                                <span class="invalid-feedback d-block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- Fechas --}}
                <div class="row">

                    <div class="col-md-4">

                        <div class="form-group">

                            <label for="fecha_inicio">

                                Fecha de inicio

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                class="
                                    form-control
                                    @error('fecha_inicio')
                                        is-invalid
                                    @enderror
                                "
                                value="{{ old(
                                    'fecha_inicio',
                                    $convenio->fecha_inicio ? \Carbon\Carbon::parse($convenio->fecha_inicio)->format('Y-m-d') : '',
                                ) }}">

                                @error('fecha_inicio')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                                </div>

                        </div>


                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="fecha_termino">

                                    Fecha de término

                                    <small class="text-muted">
                                        (opcional)
                                    </small>

                                </label>


                                <input type="date" name="fecha_termino" id="fecha_termino"
                                    class="
                                    form-control
                                    @error('fecha_termino')
                                        is-invalid
                                    @enderror
                                "
                                    value="{{ old(
                                        'fecha_termino',
                                        $convenio->fecha_termino ? \Carbon\Carbon::parse($convenio->fecha_termino)->format('Y-m-d') : '',
                                    ) }}">


                                @error('fecha_termino')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror


                                <small class="form-text text-muted">
                                    Déjalo vacío si el convenio
                                    no tiene fecha de término definida.
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- Observaciones --}}
                    <div class="form-group">

                        <label for="observaciones">

                            Observaciones

                            <small class="text-muted">
                                (opcional)
                            </small>

                        </label>


                        <textarea name="observaciones" id="observaciones"
                            class="
                            form-control
                            @error('observaciones')
                                is-invalid
                            @enderror
                        "
                            rows="3" maxlength="1000" placeholder="Información adicional sobre el convenio...">{{ old('observaciones', $convenio->observaciones) }}</textarea>


                        @error('observaciones')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>

            </div>



            {{-- ============================================ --}}
            {{-- ENTIDADES AUTORIZADAS --}}
            {{-- ============================================ --}}

            <div class="card card-info">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-building mr-2"></i>

                        Entidades autorizadas

                    </h3>

                </div>


                <div class="card-body">

                    <div class="alert alert-info">

                        <i class="fas fa-info-circle mr-1"></i>

                        El código del convenio solamente será válido
                        cuando el RUT ingresado en la reserva o cotización
                        corresponda a una de las entidades registradas aquí.

                    </div>


                    <div id="contenedorEntidades">

                        @php
                            $entidadesConvenio = $convenio->entidades
                                ->map(function ($entidad) {
                                    return [
                                        'nombre_entidad' => $entidad->nombre_entidad,

                                        'rut_entidad' => $entidad->rut_entidad,
                                    ];
                                })
                                ->toArray();

                            $entidadesAnteriores = old('entidades', $entidadesConvenio);
                        @endphp


                        @foreach ($entidadesAnteriores as $indice => $entidad)
                            <div
                                class="
                                entidad-item
                                border
                                rounded
                                p-3
                                mb-3
                                bg-light
                            ">

                                <div class="row">

                                    {{-- Nombre entidad --}}
                                    <div class="col-md-6">

                                        <div class="form-group mb-md-0">

                                            <label>

                                                Nombre entidad

                                                <span class="text-danger">
                                                    *
                                                </span>

                                            </label>


                                            <input type="text" name="entidades[{{ $indice }}][nombre_entidad]"
                                                class="form-control" value="{{ $entidad['nombre_entidad'] ?? '' }}"
                                                maxlength="150" placeholder="Ej.: Colegio Patagonia" required>

                                        </div>

                                    </div>


                                    {{-- RUT --}}
                                    <div class="col-md-5">

                                        <div class="form-group mb-md-0">

                                            <label>

                                                RUT entidad

                                                <span class="text-danger">
                                                    *
                                                </span>

                                            </label>


                                            <input type="text" name="entidades[{{ $indice }}][rut_entidad]"
                                                class="
                                                form-control
                                                rut-entidad
                                            "
                                                value="{{ $entidad['rut_entidad'] ?? '' }}" maxlength="12"
                                                placeholder="76.123.456-7" required>

                                        </div>

                                    </div>


                                    {{-- Eliminar --}}
                                    <div
                                        class="
                                        col-md-1
                                        d-flex
                                        align-items-end
                                    ">

                                        <button type="button"
                                            class="
                                            btn
                                            btn-outline-danger
                                            btnEliminarEntidad
                                            btn-block
                                        "
                                            title="Eliminar entidad">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>


                    <button type="button" id="btnAgregarEntidad" class="btn btn-outline-info">

                        <i class="fas fa-plus mr-1"></i>

                        Agregar entidad

                    </button>

                </div>

            </div>



            {{-- ============================================ --}}
            {{-- BOTONES --}}
            {{-- ============================================ --}}

            <div class="card">

                <div class="card-footer d-flex justify-content-between">

                    <a href="{{ route('convenios.index') }}" class="btn btn-default">

                        <i class="fas fa-arrow-left mr-1"></i>

                        Volver

                    </a>


                    <button type="submit" class="btn btn-primary ml-auto">

                        <i class="fas fa-save mr-1"></i>

                        Actualizar convenio

                    </button>

                </div>

            </div>

    </form>

@stop



@section('js')

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const contenedor =
                    document.getElementById(
                        'contenedorEntidades'
                    );

                const btnAgregar =
                    document.getElementById(
                        'btnAgregarEntidad'
                    );


                /*
                 * Índice siguiente.
                 */
                let indiceEntidad =
                    contenedor
                    .querySelectorAll(
                        '.entidad-item'
                    )
                    .length;


                /*
                 * Normalizar código
                 */
                const codigoInput =
                    document.getElementById(
                        'codigo'
                    );

                codigoInput.addEventListener(
                    'input',
                    function() {

                        this.value =
                            this.value
                            .toUpperCase()
                            .replace(
                                /[^A-Z0-9_-]/g,
                                ''
                            );
                    }
                );


                /*
                 * Formatear RUT
                 */
                function limpiarRut(valor) {

                    return String(
                            valor ?? ''
                        )
                        .replace(
                            /[^0-9kK]/g,
                            ''
                        )
                        .toUpperCase();
                }


                function formatearRut(valor) {

                    const limpio =
                        limpiarRut(valor);

                    if (
                        limpio.length <= 1
                    ) {
                        return limpio;
                    }


                    const cuerpo =
                        limpio.slice(
                            0,
                            -1
                        );

                    const dv =
                        limpio.slice(-1);


                    const cuerpoFormateado =
                        cuerpo.replace(
                            /\B(?=(\d{3})+(?!\d))/g,
                            '.'
                        );


                    return (
                        cuerpoFormateado +
                        '-' +
                        dv
                    );
                }


                function prepararRut(
                    input
                ) {

                    input.addEventListener(
                        'input',
                        function() {

                            this.value =
                                formatearRut(
                                    this.value
                                );
                        }
                    );
                }


                contenedor
                    .querySelectorAll(
                        '.rut-entidad'
                    )
                    .forEach(
                        prepararRut
                    );


                /*
                 * Agregar nueva entidad
                 */
                btnAgregar.addEventListener(
                    'click',
                    function() {

                        const div =
                            document.createElement(
                                'div'
                            );


                        div.className =
                            'entidad-item border rounded p-3 mb-3 bg-light';


                        div.innerHTML = `

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group mb-md-0">

                                        <label>

                                            Nombre entidad

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>

                                        <input
                                            type="text"
                                            name="entidades[${indiceEntidad}][nombre_entidad]"
                                            class="form-control"
                                            maxlength="150"
                                            placeholder="Ej.: Colegio Patagonia"
                                            required
                                        >

                                    </div>

                                </div>


                                <div class="col-md-5">

                                    <div class="form-group mb-md-0">

                                        <label>

                                            RUT entidad

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>

                                        <input
                                            type="text"
                                            name="entidades[${indiceEntidad}][rut_entidad]"
                                            class="form-control rut-entidad"
                                            maxlength="12"
                                            placeholder="76.123.456-7"
                                            required
                                        >

                                    </div>

                                </div>


                                <div
                                    class="
                                        col-md-1
                                        d-flex
                                        align-items-end
                                    "
                                >

                                    <button
                                        type="button"
                                        class="
                                            btn
                                            btn-outline-danger
                                            btnEliminarEntidad
                                            btn-block
                                        "
                                        title="Eliminar entidad"
                                    >

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </div>

                            </div>
                        `;


                        contenedor.appendChild(
                            div
                        );


                        prepararRut(
                            div.querySelector(
                                '.rut-entidad'
                            )
                        );


                        indiceEntidad++;
                    }
                );


                /*
                 * Eliminar entidad
                 */
                contenedor.addEventListener(
                    'click',
                    function(event) {

                        const boton =
                            event.target.closest(
                                '.btnEliminarEntidad'
                            );


                        if (!boton) {
                            return;
                        }


                        const total =
                            contenedor
                            .querySelectorAll(
                                '.entidad-item'
                            )
                            .length;


                        if (total <= 1) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Entidad requerida',
                                text: 'El convenio debe tener al menos una entidad autorizada.',
                            });

                            return;
                        }


                        boton
                            .closest(
                                '.entidad-item'
                            )
                            .remove();
                    }
                );

            }
        );
    </script>

@stop
