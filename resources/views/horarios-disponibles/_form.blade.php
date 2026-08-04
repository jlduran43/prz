<div class="row">

    {{-- Servicios --}}
    <div class="col-lg-5">
        <div class="card border">
            <div class="card-body">
                <h5 class="font-weight-bold mb-3">
                    <i class="fas fa-list-check text-primary mr-1"></i>
                    Servicios disponibles
                </h5>

                <div class="form-group mb-2">
                    <label for="servicios">
                        Selecciona uno o más servicios
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="servicios[]"
                        id="servicios"
                        class="form-control servicios-select
                            @error('servicios') is-invalid @enderror"
                        multiple
                        required
                    >
                        @foreach ($servicios as $servicio)
                            <option
                                value="{{ $servicio->id }}"
                                @selected(
                                    collect(
                                        old(
                                            'servicios',
                                            isset($horario)
                                                ? $horario
                                                    ->servicios
                                                    ->pluck('id')
                                                    ->all()
                                                : []
                                        )
                                    )->contains($servicio->id)
                                )
                            >
                                {{ $servicio->nombre }}
                                — máximo
                                {{ $servicio->capacidad_maxima }}
                                personas
                            </option>
                        @endforeach
                    </select>

                    @error('servicios')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="alert alert-light border mb-0 mt-3">
                    <i class="fas fa-info-circle text-info mr-1"></i>

                    <small>
                        Mantén presionada la tecla
                        <strong>Ctrl</strong>
                        para seleccionar varios servicios.
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Datos de la franja --}}
    <div class="col-lg-7">
        <div class="card border">
            <div class="card-body">
                <h5 class="font-weight-bold mb-3">
                    <i class="fas fa-clock text-primary mr-1"></i>
                    Datos de la franja horaria
                </h5>

                <div class="row">

                    {{-- Fecha --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="fecha">
                                Fecha
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="fecha"
                                id="fecha"
                                class="form-control
                                    @error('fecha') is-invalid @enderror"
                                value="{{ old(
                                    'fecha',
                                    isset($horario) && $horario->fecha
                                        ? $horario->fecha->format('Y-m-d')
                                        : ''
                                ) }}"
                                required
                            >

                            @error('fecha')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Hora inicio --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hora_inicio">
                                Hora de inicio
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="time"
                                name="hora_inicio"
                                id="hora_inicio"
                                class="form-control
                                    @error('hora_inicio') is-invalid @enderror"
                                value="{{ old(
                                    'hora_inicio',
                                    isset($horario)
                                        ? substr(
                                            $horario->hora_inicio,
                                            0,
                                            5
                                        )
                                        : ''
                                ) }}"
                                required
                            >

                            @error('hora_inicio')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Hora término --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hora_termino">
                                Hora de término
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="time"
                                name="hora_termino"
                                id="hora_termino"
                                class="form-control
                                    @error('hora_termino') is-invalid @enderror"
                                value="{{ old(
                                    'hora_termino',
                                    isset($horario)
                                        ? substr(
                                            $horario->hora_termino,
                                            0,
                                            5
                                        )
                                        : ''
                                ) }}"
                                required
                            >

                            @error('hora_termino')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Estado --}}
<div class="card border mt-3">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <span class="d-block font-weight-bold">
                    Estado del horario
                </span>

                <small class="text-muted">
                    Los horarios inactivos no aparecerán
                    disponibles para nuevas reservas.
                </small>
            </div>

            <div class="custom-control custom-switch">
                <input
                    type="checkbox"
                    name="activo"
                    id="activo"
                    value="1"
                    class="custom-control-input"
                    @checked(
                        old(
                            'activo',
                            isset($horario)
                                ? $horario->activo
                                : true
                        )
                    )
                >

                <label
                    class="custom-control-label font-weight-bold"
                    for="activo"
                >
                    Horario activo
                </label>
            </div>

        </div>
    </div>
</div>

@section('css')
    <style>
        .servicios-select {
            min-height: 185px;
            padding: 8px;
        }

        .servicios-select option {
            padding: 8px 10px;
            border-radius: 4px;
        }

        .servicios-select option:checked {
            background: #007bff linear-gradient(
                0deg,
                #007bff 0%,
                #007bff 100%
            );
            color: #ffffff;
        }

        .card.border {
            height: 100%;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px;
            box-shadow: none;
        }

        .card.border .card-body {
            padding: 20px;
        }

        .input-group-text {
            min-width: 90px;
            justify-content: center;
        }

        @media (max-width: 991.98px) {
            .col-lg-5 {
                margin-bottom: 16px;
            }
        }

        @media (max-width: 575.98px) {
            .d-flex.align-items-center.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 14px;
            }
        }
    </style>
@stop
