@extends('adminlte::page')

@section('title', 'Editar región')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar región</h1>
    </div>
@stop

@section('content')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">
                Editar {{ $region->nombre }}
            </h3>
        </div>

        <form action="{{ route('regiones.update', $region) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                @include('regiones._form', [
                    'region' => $region,
                ])
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i>
                    Actualizar
                </button>
                <a href="{{ route('regiones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>
        </form>
    </div>
@stop
