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
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                        </div>

                        <input type="text" name="codigo" id="codigo"
                            class="form-control @error('codigo') is-invalid @enderror"
                            value="{{ old('codigo', $convenio->codigo ?? '') }}" maxlength="50"
                            placeholder="Ej.: MUNICIPAL2026" required autocomplete="off">
                    </div>

                    @error('codigo')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror

                    <small class="form-text text-muted">
                        Código que deberá ingresar el cliente al cotizar o reservar.
                    </small>
                </div>
            </div>

            {{-- Nombre --}}
            <div class="col-md-5">
                <div class="form-group">
                    <label for="nombre">
                        Nombre del convenio
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-file-signature"></i>
                            </span>
                        </div>

                        <input type="text" name="nombre" id="nombre"
                            class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $convenio->nombre ?? '') }}" maxlength="150"
                            placeholder="Ej.: Convenio Municipalidad de Natales" required>
                    </div>

                    @error('nombre')
                        <span class="invalid-feedback d-block">
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
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <input type="number" name="porcentaje_descuento" id="porcentaje_descuento"
                            class="form-control @error('porcentaje_descuento') is-invalid @enderror"
                            value="{{ old('porcentaje_descuento', $convenio->porcentaje_descuento ?? '') }}"
                            min="0" max="100" step="0.01" placeholder="15" required>

                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
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
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </div>

                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                            class="form-control @error('fecha_inicio') is-invalid @enderror"
                            value="{{ old('fecha_inicio', isset($convenio) && $convenio->fecha_inicio ? \Illuminate\Support\Carbon::parse($convenio->fecha_inicio)->format('Y-m-d') : '') }}"
                            required>
                    </div>

                    @error('fecha_inicio')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecha_termino">
                        Fecha de término
                        <small class="text-muted">(opcional)</small>
                    </label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                        </div>

                        <input type="date" name="fecha_termino" id="fecha_termino"
                            class="form-control @error('fecha_termino') is-invalid @enderror"
                            value="{{ old('fecha_termino', isset($convenio) && $convenio->fecha_termino ? \Illuminate\Support\Carbon::parse($convenio->fecha_termino)->format('Y-m-d') : '') }}">
                    </div>

                    @error('fecha_termino')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror

                    <small class="form-text text-muted">
                        Déjalo vacío si el convenio no tiene fecha de término definida.
                    </small>
                </div>
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="form-group">
            <label for="observaciones">
                Observaciones
                <small class="text-muted">(opcional)</small>
            </label>

            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text align-items-start pt-2">
                        <i class="fas fa-comment-alt"></i>
                    </span>
                </div>

                <textarea name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                    rows="3" maxlength="1000" placeholder="Información adicional sobre el convenio...">{{ old('observaciones', $convenio->observaciones ?? '') }}</textarea>
            </div>

            @error('observaciones')
                <span class="invalid-feedback d-block">
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
                $entidadesGuardadas = [];

                if (isset($convenio) && $convenio->relationLoaded('entidades')) {
                    $entidadesGuardadas = $convenio->entidades
                        ->map(function ($entidad) {
                            return [
                                'nombre_entidad' => $entidad->nombre_entidad,
                                'rut_entidad' => $entidad->rut_entidad,
                            ];
                        })
                        ->values()
                        ->toArray();
                }

                if (empty($entidadesGuardadas)) {
                    $entidadesGuardadas = [
                        [
                            'nombre_entidad' => '',
                            'rut_entidad' => '',
                        ],
                    ];
                }

                $entidadesAnteriores = old('entidades', $entidadesGuardadas);
            @endphp

            @foreach ($entidadesAnteriores as $indice => $entidad)
                <div class="entidad-item border rounded p-3 mb-3 bg-light">
                    <div class="row">
                        {{-- Nombre entidad --}}
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label>
                                    Nombre entidad
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-building"></i>
                                        </span>
                                    </div>

                                    <input type="text" name="entidades[{{ $indice }}][nombre_entidad]"
                                        class="form-control" value="{{ $entidad['nombre_entidad'] ?? '' }}"
                                        maxlength="150" placeholder="Ej.: Colegio Patagonia" required>
                                </div>
                            </div>
                        </div>

                        {{-- RUT --}}
                        <div class="col-md-5">
                            <div class="form-group mb-md-0">
                                <label>
                                    RUT entidad
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group rut-input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                    </div>

                                    <input type="text" name="entidades[{{ $indice }}][rut_entidad]"
                                        class="form-control rut-chileno" value="{{ $entidad['rut_entidad'] ?? '' }}"
                                        maxlength="12" placeholder="76.123.456-7" autocomplete="off" required>

                                    <div class="input-group-append">
                                        <span class="input-group-text rut-estado">
                                            <i class="fas fa-minus text-muted"></i>
                                        </span>
                                    </div>
                                </div>

                                <small class="form-text text-muted rut-mensaje">
                                    Ingresa el RUT con o sin puntos.
                                </small>
                            </div>
                        </div>

                        {{-- Eliminar --}}
                        <div class="col-md-1 mb-3 columna-eliminar-entidad">
                            <button type="button" class="btn btn-outline-danger btnEliminarEntidad btn-block"
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
    <div class="card-footer d-flex">
        <a href="{{ route('convenios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Volver
        </a>

        <button type="submit" class="btn btn-primary ml-auto">
            <i class="fas fa-save mr-1"></i>
            {{ isset($convenio) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</div>