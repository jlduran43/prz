@extends('adminlte::page')

@section('title', 'Detalle de Región')

@section('content_header')
    <h1>Detalle de Región</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        Información de la Región
    </div>

    <div class="card-body">

        <strong>Código:</strong> {{ $region->codigo }} <br>

        <strong>Nombre:</strong> {{ $region->nombre }} <br>

        <strong>Estado:</strong>

        @if($region->activo)
            <span class="badge bg-success">Activo</span>
        @else
            <span class="badge bg-danger">Inactivo</span>
        @endif

    </div>

    <div class="card-footer">
        <a href="{{ route('regiones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

@stop
