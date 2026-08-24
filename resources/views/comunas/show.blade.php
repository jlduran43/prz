@extends('adminlte::page')

@section('title', 'Detalle de Comuna')

@section('content_header')
    <h1>Detalle de Comuna</h1>
@stop

@section('content')

<div class="card card-info">

    <div class="card-header">
        <h3 class="card-title">
            Información de la comuna {{ $comuna->nombre }}
        </h3>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Código:</strong>
            </div>
            <div class="col-md-9">
                {{ $comuna->codigo }}
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Nombre:</strong>
            </div>
            <div class="col-md-9">
                {{ $comuna->nombre }}
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Región:</strong>
            </div>
            <div class="col-md-9">
                {{ $comuna->region->nombre ?? 'Sin región' }}
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Fecha de creación</strong>
            </div>
            <div class="col-md-9">
                {{ $comuna->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Última actualización</strong>
            </div>
            <div class="col-md-9">
                {{ $comuna->updated_at->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <strong>Estado:</strong>
            </div>
            <div class="col-md-9">
                @if ($comuna->activo)
                    <span class="badge badge-success">
                        Activo
                    </span>
                @else
                    <span class="badge badge-secondary">
                        Inactivo
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="card-footer">
        <a
            href="{{ route('comunas.index') }}"
            class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

</div>

@stop
