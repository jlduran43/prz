@extends('adminlte::page')

@section('title', 'Editar horario disponible')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar horario disponible</h1>

        <a
            href="{{ route('horarios-disponibles.index') }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left mr-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">
                Datos del horario
            </h3>
        </div>

        <form
            action="{{ route(
                'horarios-disponibles.update',
                $horario
            ) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="card-body">
                @include('horarios-disponibles._form')
            </div>

            <div class="card-footer">
                <button
                    type="submit"
                    class="btn btn-warning"
                >
                    <i class="fas fa-save mr-1"></i>
                    Actualizar
                </button>

                <a
                    href="{{ route('horarios-disponibles.index') }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
