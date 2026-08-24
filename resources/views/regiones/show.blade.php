@extends('adminlte::page')

@section('title', 'Detalle de Región')

@section('content_header')
    <h1>Detalle de Región</h1>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                Información de la Región {{ $region->nombre }}
            </h3>
        </div>

        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">
                    Código:
                </dt>

                <dd class="col-sm-9">
                    {{ $region->codigo }}
                </dd>

                <dt class="col-sm-3">
                    Nombre:
                </dt>

                <dd class="col-sm-9">
                    {{ $region->nombre }}
                </dd>

                <dt class="col-sm-3">
                    Estado:
                </dt>

                <dd class="col-sm-9">
                    @if ($region->activo)
                        <span class="badge badge-success">
                            Activo
                        </span>
                    @else
                        <span class="badge badge-danger">
                            Inactivo
                        </span>
                    @endif
                </dd>
            </dl>
        </div>

        <div class="card-footer">
            <a
                href="{{ route('regiones.index') }}"
                class="btn btn-secondary"
            >
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </a>
        </div>
    </div>
@stop
