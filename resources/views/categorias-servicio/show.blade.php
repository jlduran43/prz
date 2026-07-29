@extends('adminlte::page')

@section('title', 'Detalle de categoría')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalle de categoría</h1>

        <div>
            <a
                href="{{ route(
                    'categorias-servicio.edit',
                    $categoria
                ) }}"
                class="btn btn-warning"
            >
                <i class="fas fa-edit"></i>
                Editar
            </a>

            <a
                href="{{ route('categorias-servicio.index') }}"
                class="btn btn-secondary"
            >
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                {{ $categoria->nombre }}
            </h3>
        </div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">
                    {{ $categoria->codigo }}
                </dd>

                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">
                    {{ $categoria->nombre }}
                </dd>

                <dt class="col-sm-3">Descripción</dt>
                <dd class="col-sm-9">
                    {{ $categoria->descripcion ?: 'Sin descripción' }}
                </dd>

                <dt class="col-sm-3">Cantidad de servicios</dt>
                <dd class="col-sm-9">
                    {{ $categoria->servicios()->count() }}
                </dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    @if ($categoria->activo)
                        <span class="badge badge-success">
                            Activa
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            Inactiva
                        </span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
@stop
