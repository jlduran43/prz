@extends('adminlte::page')

@section('title', 'Detalle del tipo de cliente')

@section('content_header')
    <h1>Detalle del tipo de cliente</h1>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                Información tipo cliente {{ $tipoCliente->nombre }}
            </h3>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">
                    {{ $tipoCliente->id }}
                </dd>

                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9"><code>{{ $tipoCliente->codigo }}</code></dd>
                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">{{ $tipoCliente->nombre }}</dd>
                <dt class="col-sm-3">Tipo de estructura</dt>
                <dd class="col-sm-9">
                    @if ($tipoCliente->tipo_estructura === 'PERSONA')
                        <span class="badge badge-info">
                            Persona
                        </span>
                    @elseif ($tipoCliente->tipo_estructura === 'ESTABLECIMIENTO')
                        <span class="badge badge-warning">
                            Establecimiento
                        </span>
                    @elseif ($tipoCliente->tipo_estructura === 'ORGANIZACION')
                        <span class="badge badge-primary">
                            Organización
                        </span>
                    @endif
                </dd>
                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    @if ($tipoCliente->activo)
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
                    {{ $tipoCliente->created_at?->format('d/m/Y H:i') }}
                </dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('tipos-cliente.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </a>
        </div>
    </div>
@stop
