@extends('adminlte::page')

@section('title', 'Crear categoría de servicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Crear categoría de servicio</h1>
    </div>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                Datos de la categoría
            </h3>
        </div>

        <form action="{{ route('categorias-servicio.store') }}" method="POST">
            @csrf

            <div class="card-body">
                @include('categorias-servicio._form')
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar
                </button>
                <a href="{{ route('categorias-servicio.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>
        </form>
    </div>
@stop
