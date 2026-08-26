<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="categoria_servicio_id">
                Categoría
                <span class="text-danger">*</span>
            </label>

            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-folder-open"></i>
                    </span>
                </div>

                <select name="categoria_servicio_id" id="categoria_servicio_id"
                    class="form-control
                    @error('categoria_servicio_id') is-invalid @enderror"
                    required>
                    <option value="">
                        Seleccione una categoría
                    </option>

                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected(old('categoria_servicio_id', $servicio->categoria_servicio_id ?? '') == $categoria->id)>
                            {{ $categoria->nombre }}

                            @unless ($categoria->activo)
                                — Inactiva
                            @endunless
                        </option>
                    @endforeach
                </select>

                @error('categoria_servicio_id')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="codigo">
                Código
                <span class="text-danger">*</span>
            </label>

            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-tag"></i>
                    </span>
                </div>

                <input type="text" name="codigo" id="codigo"
                    class="form-control
                    @error('codigo') is-invalid @enderror"
                    value="{{ old('codigo', $servicio->codigo ?? '') }}" maxlength="50"
                    placeholder="Ejemplo: VISITA_GUIADA" required>

                @error('codigo')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="nombre">
        Nombre
        <span class="text-danger">*</span>
    </label>

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-font"></i>
            </span>
        </div>


        <input type="text" name="nombre" id="nombre"
            class="form-control
            @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $servicio->nombre ?? '') }}" maxlength="150"
            placeholder="Ejemplo: Visita educativa guiada" required>

        @error('nombre')
            <span class="invalid-feedback">
                {{ $message }}
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="descripcion">
            Descripción
        </label>

        <textarea name="descripcion" id="descripcion" rows="4" maxlength="2000"
            class="form-control
            @error('descripcion') is-invalid @enderror"
            placeholder="Descripción del servicio o experiencia">{{ old('descripcion', $servicio->descripcion ?? '') }}</textarea>

        @error('descripcion')
            <span class="invalid-feedback">
                {{ $message }}
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="imagen">
            <i class="fas fa-image mr-1 text-primary"></i>
            Imagen del servicio o experiencia
        </label>

        @if (isset($servicio) && $servicio->imagen)
            <div class="mb-3">
                <p class="mb-2">
                    <strong>Imagen actual</strong>
                </p>

                <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="{{ $servicio->nombre }}"
                    class="img-thumbnail"
                    style="
                    width: 220px;
                    height: 140px;
                    object-fit: cover;
                ">
            </div>
        @endif

        <div class="custom-file">
            <input type="file" name="imagen" id="imagen"
                class="custom-file-input @error('imagen') is-invalid @enderror"
                accept="image/jpeg,image/png,image/webp">

            <label class="custom-file-label" for="imagen" data-browse="Seleccionar">
                {{ isset($servicio) && $servicio->imagen ? 'Seleccionar nueva imagen...' : 'Seleccionar imagen...' }}
            </label>

            @error('imagen')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <small class="form-text text-muted">
            Formatos permitidos: JPG, JPEG, PNG o WEBP.
            Tamaño máximo: 4 MB.

            @if (isset($servicio) && $servicio->imagen)
                Si selecciona una nueva imagen, reemplazará la actual.
            @endif
        </small>

        <div id="contenedorVistaPrevia" class="mt-3" style="display: none;">
            <p class="mb-2">
                <strong>{{ isset($servicio) ? 'Nueva imagen' : 'Vista previa' }}</strong>
            </p>

            <img id="vistaPreviaImagen" src="" alt="Vista previa" class="img-thumbnail"
                style="
                width: 220px;
                height: 140px;
                object-fit: cover;
            ">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="duracion_minutos">
                    Duración
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                    <input type="number" name="duracion_minutos" id="duracion_minutos"
                        class="form-control
                        @error('duracion_minutos') is-invalid @enderror"
                        value="{{ old('duracion_minutos', $servicio->duracion_minutos ?? '') }}" min="1">

                    <div class="input-group-append">
                        <span class="input-group-text">
                            minutos
                        </span>
                    </div>

                    @error('duracion_minutos')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="capacidad_minima">
                    Capacidad mínima
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-user-minus"></i>
                        </span>
                    </div>

                    <input type="number" name="capacidad_minima" id="capacidad_minima"
                        class="form-control
                        @error('capacidad_minima') is-invalid @enderror"
                        value="{{ old('capacidad_minima', $servicio->capacidad_minima ?? '') }}" min="1">

                    @error('capacidad_minima')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="capacidad_maxima">
                    Capacidad máxima
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>

                    <input type="number" name="capacidad_maxima" id="capacidad_maxima"
                        class="form-control
                        @error('capacidad_maxima') is-invalid @enderror"
                        value="{{ old('capacidad_maxima', $servicio->capacidad_maxima ?? '') }}" min="1">

                    @error('capacidad_maxima')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="precio">
                    Precio
                    <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                    </div>

                    <input type="number" name="precio" id="precio"
                        class="form-control
        @error('precio') is-invalid @enderror"
                        value="{{ old('precio', isset($servicio) ? $servicio->precio : '') }}" min="1"
                        step="1" required>

                    @error('precio')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <small class="form-text text-muted">
                    Ingrese el precio en pesos chilenos.
                </small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="tipo_cobro">
                    Tipo de cobro
                    <span class="text-danger">*</span>
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-money-bill-wave"></i>
                        </span>
                    </div>

                    <select name="tipo_cobro" id="tipo_cobro"
                        class="form-control
                        @error('tipo_cobro') is-invalid @enderror"
                        required>
                        <option value="">
                            Seleccione una opción
                        </option>

                        <option value="POR_PERSONA" @selected(old('tipo_cobro', $servicio->tipo_cobro ?? 'POR_PERSONA') === 'POR_PERSONA')>
                            Por persona
                        </option>

                        <option value="POR_GRUPO" @selected(old('tipo_cobro', $servicio->tipo_cobro ?? '') === 'POR_GRUPO')>
                            Por grupo
                        </option>
                    </select>

                    @error('tipo_cobro')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <small class="form-text text-muted">
                    Por persona multiplica el precio por la cantidad de asistentes.
                    Por grupo cobra el valor una sola vez.
                </small>

            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="d-block">
                    <i class="fas fa-calendar-check mr-1"></i>
                    Requiere reserva
                </label>

                <div class="custom-control custom-switch mt-2">
                    <input type="hidden" name="requiere_reserva" value="0">

                    <input type="checkbox" name="requiere_reserva" id="requiere_reserva" value="1"
                        class="custom-control-input" @checked(old('requiere_reserva', $servicio->requiere_reserva ?? true))>

                    <label for="requiere_reserva" class="custom-control-label">
                        Sí, requiere una reserva previa
                    </label>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('js/servicios_experiencias/push.js') }}"></script>
    @endpush
