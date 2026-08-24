@extends('adminlte::page')

@section('title', 'Crear horario de atención')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Crear horario de atención</h1>
    </div>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                Datos de la franja horaria
            </h3>
        </div>

        <form action="{{ route('horarios-disponibles.store') }}" method="POST">
            @csrf

            <div class="card-body">
                @include('horarios-disponibles._form')
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>
                    Guardar
                </button>
                <a href="{{ route('horarios-disponibles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>
            </div>
        </form>
    </div>
@stop
