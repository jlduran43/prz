<div class="row">

    {{-- Región --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="region_id">
                Región
                <span class="text-danger">*</span>
            </label>

            <select
                name="region_id"
                id="region_id"
                class="form-control @error('region_id') is-invalid @enderror"
            >
                <option value="">Seleccione una región</option>

                @foreach($regiones as $region)
                    <option
                        value="{{ $region->id }}"
                        @selected(old('region_id', $comuna->region_id ?? '') == $region->id)
                    >
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

    {{-- Código --}}
    <div class="col-md-3">
        <div class="form-group">
            <label for="codigo">
                Código
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="codigo"
                id="codigo"
                class="form-control @error('codigo') is-invalid @enderror"
                value="{{ old('codigo', $comuna->codigo ?? '') }}"
                maxlength="10"
            >

            @error('codigo')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    {{-- Estado --}}
    <div class="col-md-3">
        <div class="form-group">
            <label for="activo">
                Estado
                <span class="text-danger">*</span>
            </label>

            <select
                name="activo"
                id="activo"
                class="form-control @error('activo') is-invalid @enderror"
            >
                <option
                    value="1"
                    @selected(old('activo', $comuna->activo ?? true))
                >
                    Activo
                </option>

                <option
                    value="0"
                    @selected(old('activo', $comuna->activo ?? true) == false)
                >
                    Inactivo
                </option>
            </select>

            @error('activo')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    {{-- Nombre --}}
    <div class="col-md-12">
        <div class="form-group">
            <label for="nombre">
                Nombre
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="nombre"
                id="nombre"
                class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $comuna->nombre ?? '') }}"
                maxlength="120"
            >

            @error('nombre')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

</div>
