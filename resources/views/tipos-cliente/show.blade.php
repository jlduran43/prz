@extends('adminlte::page')

@section('title', 'Detalle del tipo de cliente')

@section('content_header')
    <h1>Detalle del tipo de cliente</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">
                    {{ $tipoCliente->id }}
                </dd>

                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">
                    <code>{{ $tipoCliente->codigo }}</code>
                </dd>

                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">
                    {{ $tipoCliente->nombre }}
                </dd>

                <dt class="col-sm-3">
                    Tipo de estructura
                </dt>

                <dd class="col-sm-9">
                    @if ($tipoCliente->tipo_estructura === 'PERSONA')
                        <span class="badge badge-info">
                            Persona
                        </span>
                    @else
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
            <a href="{{ route('tipos-cliente.edit', $tipoCliente) }}"
                class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>
                Editar
            </a>

            <a href="{{ route('tipos-cliente.index') }}" class="btn btn-secondary">
                Volver
            </a>
        </div>
    </div>
@stop
