<div class="form-group">
    <label for="servicio_experiencia_id">
        Servicio
        <span class="text-danger">*</span>
    </label>

    <select name="servicio_experiencia_id" id="servicio_experiencia_id"
        class="form-control
            @error('servicio_experiencia_id')
                is-invalid
            @enderror"
        required>
        <option value="">
            Seleccione un servicio
        </option>

        @foreach ($servicios as $servicio)
            <option value="{{ $servicio->id }}" @selected(old('servicio_experiencia_id', $horario->servicio_experiencia_id ?? '') == $servicio->id)>
                {{ $servicio->nombre }}
            </option>
        @endforeach
    </select>

    @error('servicio_experiencia_id')
        <span class="invalid-feedback">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="fecha">
                Fecha
                <span class="text-danger">*</span>
            </label>

            <input type="date" name="fecha" id="fecha"
                class="form-control
            @error('fecha') is-invalid @enderror"
                value="{{ old('fecha', isset($horario) && $horario->fecha ? $horario->fecha->format('Y-m-d') : '') }}"
                required>

            @error('fecha')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="form-group">
            <label for="hora_inicio">
                Hora de inicio
                <span class="text-danger">*</span>
            </label>

            <input type="time" name="hora_inicio" id="hora_inicio"
                class="form-control
            @error('hora_inicio') is-invalid @enderror"
                value="{{ old('hora_inicio', isset($horario) ? substr($horario->hora_inicio, 0, 5) : '') }}"
                required>

            @error('hora_inicio')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="hora_termino">
                Hora de término
                <span class="text-danger">*</span>
            </label>

            <input type="time" name="hora_termino" id="hora_termino"
                class="form-control
                    @error('hora_termino') is-invalid @enderror"
                value="{{ old('hora_termino', isset($horario) ? substr($horario->hora_termino, 0, 5) : '') }}" required>

            @error('hora_termino')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" name="activo" id="activo" value="1" class="custom-control-input"
            @checked(old('activo', isset($horario) ? $horario->activo : true))>

        <label class="custom-control-label" for="activo">
            Horario activo
        </label>
    </div>
</div>
