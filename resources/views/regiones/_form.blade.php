<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="codigo">
                Código
                <span class="text-danger">*</span>
            </label>

            <input  type="text"
                    id="codigo"
                    name="codigo"
                    class="form-control @error('codigo') is-invalid @enderror"
                    value="{{ old('codigo', $region->codigo ?? '') }}"
                    maxlength="10"
                    placeholder="Ejemplo: 13"
                    required>

            @error('codigo')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group">
            <label for="nombre">
                Nombre
                <span class="text-danger">*</span>
            </label>

            <input  type="text"
                    id="nombre"
                    name="nombre"
                    class="form-control @error('nombre') is-invalid @enderror"
                    value="{{ old('nombre', $region->nombre ?? '') }}"
                    maxlength="150"
                    placeholder="Ejemplo: Región Metropolitana de Santiago"
                    required>

            @error('nombre')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input  type="checkbox"
                class="custom-control-input"
                id="activo"
                name="activo"
                value="1"
                @checked(old('activo', $region->activo ?? true))>

        <label class="custom-control-label" for="activo">
            Región activa
        </label>
    </div>

    <small class="form-text text-muted">
        Las regiones inactivas no deberían aparecer en los formularios de nuevos clientes.
    </small>

    @error('activo')
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror
</div>
