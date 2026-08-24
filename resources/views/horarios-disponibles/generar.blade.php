@extends('adminlte::page')

@section('title', 'Generar horarios')

@section('content_header')
    <div>
        <h1 class="mb-1">Generar horarios</h1>

        <p class="text-muted mb-0">
            Crea automáticamente horarios recurrentes dentro de un rango de fechas.
        </p>
    </div>
@stop

@section('content')
    <form method="POST" action="{{ route('horarios-disponibles.recurrentes.guardar') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Revisa los campos marcados antes de continuar.
            </div>
        @endif

        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-plus mr-1"></i>
                    Generación automática de horarios
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_desde">
                                Inicio Temporada
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date" name="fecha_desde" id="fecha_desde"
                                class="form-control
                                    @error('fecha_desde') is-invalid @enderror"
                                value="{{ old('fecha_desde') }}" min="{{ now()->format('Y-m-d') }}" required>

                            @error('fecha_desde')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_hasta">
                                Hasta
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date" name="fecha_hasta" id="fecha_hasta"
                                class="form-control
                                    @error('fecha_hasta') is-invalid @enderror"
                                value="{{ old('fecha_hasta') }}" min="{{ now()->format('Y-m-d') }}" required>

                            @error('fecha_hasta')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        Días disponibles
                        <span class="text-danger">*</span>
                    </label>

                    <div class="border rounded p-3">
                        <div class="row">
                            @foreach ([
                                        1 => 'Lunes',
                                        2 => 'Martes',
                                        3 => 'Miércoles',
                                        4 => 'Jueves',
                                        5 => 'Viernes',
                                        6 => 'Sábado',
                                        0 => 'Domingo',
                            ] as $numero => $nombre)
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="dias_semana[]" value="{{ $numero }}"
                                            id="dia_{{ $numero }}" class="custom-control-input"
                                            @checked(in_array($numero, old('dias_semana', [])))>

                                        <label for="dia_{{ $numero }}" class="custom-control-label">
                                            {{ $nombre }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @error('dias_semana')
                        <small class="text-danger d-block mt-1">
                            {{ $message }}
                        </small>
                    @enderror

                    @error('dias_semana.*')
                        <small class="text-danger d-block mt-1">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora_inicio">
                                Hora de inicio
                                <span class="text-danger">*</span>
                            </label>

                            <input type="time" name="hora_inicio" id="hora_inicio"
                                class="form-control
                                    @error('hora_inicio') is-invalid @enderror"
                                value="{{ old('hora_inicio') }}" required>

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
                                Hora de término
                                <span class="text-danger">*</span>
                            </label>

                            <input type="time" name="hora_termino" id="hora_termino"
                                class="form-control
                                    @error('hora_termino') is-invalid @enderror"
                                value="{{ old('hora_termino') }}" required>

                            @error('hora_termino')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="capacidad_maxima">
                                Capacidad máxima total del parque
                                <span class="text-danger">*</span>
                            </label>

                            <input type="number" name="capacidad_maxima" id="capacidad_maxima"
                                class="form-control
                                    @error('capacidad_maxima') is-invalid @enderror"
                                value="{{ old('capacidad_maxima') }}" min="1" required>

                            @error('capacidad_maxima')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="servicios">
                        Servicios disponibles en esta franja
                        <span class="text-danger">*</span>
                    </label>

                    <select name="servicios[]" id="servicios"
                        class="form-control
                            @error('servicios') is-invalid @enderror" multiple
                        size="7" required>
                        @foreach ($servicios as $servicio)
                            <option value="{{ $servicio->id }}" @selected(in_array($servicio->id, old('servicios', [])))>
                                {{ $servicio->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <small class="form-text text-muted">
                        Mantén presionada la tecla Ctrl para seleccionar varios servicios.
                    </small>

                    @error('servicios')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-calendar-plus mr-1"></i>
                    Generar horarios
                </button>
                <a href="{{ route('horarios-disponibles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>
            </div>
        </div>
    </form>
@stop
