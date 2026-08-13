@extends('adminlte::page')

@section('title', 'Detalle del servicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalle del servicio o experiencia</h1>

        <div>
            <a href="{{ route('servicios-experiencias.edit', $servicio) }}"
                class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>
                Editar
            </a>

            <a href="{{ route('servicios-experiencias.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                {{ $servicio->nombre }}
            </h3>
        </div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">
                    {{ $servicio->codigo }}
                </dd>

                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">
                    {{ $servicio->nombre }}
                </dd>

                <dt class="col-sm-3">Categoría</dt>
                <dd class="col-sm-9">
                    {{ $servicio->categoria?->nombre ?? 'Sin categoría' }}

                    @if ($servicio->categoria && !$servicio->categoria->activo)
                        <span class="badge badge-warning ml-1">
                            Categoría inactiva
                        </span>
                    @endif
                </dd>

                <dt class="col-sm-3">Descripción</dt>
                <dd class="col-sm-9">
                    {{ $servicio->descripcion ?: 'Sin descripción' }}
                </dd>

                <dt class="col-sm-3">Duración</dt>
                <dd class="col-sm-9">
                    {{ $servicio->duracion_minutos ? $servicio->duracion_minutos . ' minutos' : 'No definida' }}
                </dd>

                <dt class="col-sm-3">Imagen</dt>
                <dd class="col-sm-9">

                    @if ($servicio->imagen)
                        <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="{{ $servicio->nombre }}"
                            class="img-thumbnail"
                            style="
                width: 300px;
                height: 190px;
                object-fit: cover;
            ">
                    @else
                        <span class="text-muted">
                            <i class="fas fa-image mr-1"></i>
                            Sin imagen registrada
                        </span>
                    @endif

                </dd>

                <dt class="col-sm-3">Capacidad mínima</dt>
                <dd class="col-sm-9">
                    {{ $servicio->capacidad_minima ?? 'No definida' }}
                </dd>

                <dt class="col-sm-3">Capacidad máxima</dt>
                <dd class="col-sm-9">
                    {{ $servicio->capacidad_maxima ?? 'No definida' }}
                </dd>

                <dt class="col-sm-3">Precio</dt>
                <dd class="col-sm-9">
                    ${{ number_format((float) $servicio->precio, 0, ',', '.') }}
                </dd>

                <dt class="col-sm-3">Requiere reserva</dt>
                <dd class="col-sm-9">
                    @if ($servicio->requiere_reserva)
                        <span class="badge badge-success">
                            Sí
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            No
                        </span>
                    @endif
                </dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    @if ($servicio->activo)
                        <span class="badge badge-success">
                            Activo
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            Inactivo
                        </span>
                    @endif
                </dd>

            </dl>
        </div>
    </div>
@stop
