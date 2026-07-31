<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="codigo">
                Código
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="codigo"
                id="codigo"
                class="form-control text-uppercase
                    @error('codigo') is-invalid @enderror"
                value="{{ old(
                    'codigo',
                    $tipoCliente->codigo ?? ''
                ) }}"
                placeholder="GRUPO_ADULTO_MAYOR"
                maxlength="40"
                required
            >

            @error('codigo')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="nombre">
                Nombre
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="nombre"
                id="nombre"
                class="form-control
                    @error('nombre') is-invalid @enderror"
                value="{{ old(
                    'nombre',
                    $tipoCliente->nombre ?? ''
                ) }}"
                placeholder="Grupo de adultos mayores"
                maxlength="100"
                required
            >

            @error('nombre')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="tipo_estructura">
                Tipo de estructura
                <span class="text-danger">*</span>
            </label>

            <select
                name="tipo_estructura"
                id="tipo_estructura"
                class="form-control
                    @error('tipo_estructura') is-invalid @enderror"
                required
            >
                <option value="">
                    Seleccione una opción
                </option>

                <option
                    value="PERSONA"
                    @selected(
                        old(
                            'tipo_estructura',
                            $tipoCliente->tipo_estructura ?? ''
                        ) === 'PERSONA'
                    )
                >
                    Persona
                </option>

                <option
                    value="ORGANIZACION"
                    @selected(
                        old(
                            'tipo_estructura',
                            $tipoCliente->tipo_estructura ?? ''
                        ) === 'ORGANIZACION'
                    )
                >
                    Organización
                </option>
            </select>

            @error('tipo_estructura')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input
            type="hidden"
            name="activo"
            value="0"
        >

        <input
            type="checkbox"
            name="activo"
            id="activo"
            value="1"
            class="custom-control-input"
            @checked(
                old(
                    'activo',
                    $tipoCliente->activo ?? true
                )
            )
        >

        <label
            for="activo"
            class="custom-control-label"
        >
            Tipo de cliente activo
        </label>
    </div>
</div>
