@extends('adminlte::page')

@section('title', 'Detalle del horario de atención')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalle del horario de atención</h1>

        <div>
            <a href="{{ route('horarios-disponibles.edit', $horario) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>
                Editar
            </a>

            <a href="{{ route('horarios-disponibles.index') }}" class="btn btn-secondary">
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
                Información del horario
            </h3>
        </div>

        <div class="card-body">

            <dl class="row">
                <dt class="col-sm-3">Fecha</dt>
                <dd class="col-sm-9">
                    {{ $horario->fecha ? $horario->fecha->format('d-m-Y') : 'Sin fecha' }}
                </dd>
                <dt class="col-sm-3">
                    Hora de atención
                </dt>

                <dd class="col-sm-9">
                    <span class="badge badge-info p-2" style="font-size: 1rem;">
                        <i class="far fa-clock mr-1"></i>
                        {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                        a
                        {{ \Carbon\Carbon::parse($horario->hora_termino)->format('H:i') }}
                    </span>
                </dd>

                <dt class="col-sm-3">
                    Estado
                </dt>

                <dd class="col-sm-9">
                    @if ($horario->activo)
                        <span class="badge badge-success">
                            Activo
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            Inactivo
                        </span>
                    @endif
                </dd>

                <dt class="col-sm-3">
                    Fecha de creación
                </dt>

                <dd class="col-sm-9">
                    {{ $horario->created_at?->format('d-m-Y H:i') }}
                </dd>

                <dt class="col-sm-3">
                    Última actualización
                </dt>

                <dd class="col-sm-9">
                    {{ $horario->updated_at?->format('d-m-Y H:i') }}
                </dd>

            </dl>

        </div>

    </div>

@stop
