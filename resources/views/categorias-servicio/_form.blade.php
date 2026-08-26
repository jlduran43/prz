<div class="row">
    <div class="col-md-5">
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
                    class="form-control @error('codigo') is-invalid @enderror"
                    value="{{ old('codigo', $categoria->codigo ?? '') }}" maxlength="50"
                    placeholder="Ejemplo: EXPERIENCIA_EDUCATIVA" required>
            </div>

            @error('codigo')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror

            <small class="form-text text-muted">
                Utilice mayúsculas y guion bajo.
            </small>
        </div>
    </div>

    <div class="col-md-7">
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
                    class="form-control @error('nombre') is-invalid @enderror"
                    value="{{ old('nombre', $categoria->nombre ?? '') }}" maxlength="100"
                    placeholder="Ejemplo: Experiencias educativas" required>

                @error('nombre')
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="descripcion">
        Descripción
    </label>

    <textarea name="descripcion" id="descripcion" rows="4"
        class="form-control @error('descripcion') is-invalid @enderror"
        placeholder="Ingrese una descripción de la categoría">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>

    @error('descripcion')
        <span class="invalid-feedback">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="form-group">
    <label>Estado</label>

    <div class="custom-control custom-switch">
        <input type="checkbox" name="activo" id="activo" value="1" class="custom-control-input"
            @checked(old('activo', $categoria->activo ?? true))>

        <label class="custom-control-label" for="activo">
            Categoría activa
        </label>
    </div>
</div>
