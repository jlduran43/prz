@extends('adminlte::page')

@section('title', 'Crear servicio o experiencia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Crear servicio o experiencia</h1>

        <a href="{{ route('servicios-experiencias.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')
    @if ($categorias->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-1"></i>

            No existen categorías activas. Debe crear o reactivar una
            categoría antes de registrar un servicio.
        </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                Datos del servicio o experiencia
            </h3>
        </div>

        <form action="{{ route('servicios-experiencias.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-body">
                @include('servicios-experiencias._form')
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" @disabled($categorias->isEmpty())>
                    <i class="fas fa-save mr-1"></i>
                    Guardar
                </button>

                <a href="{{ route('servicios-experiencias.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
