@extends('adminlte::page')

@section('title', 'Editar servicio o experiencia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar servicio o experiencia</h1>

        <a
            href="{{ route('servicios-experiencias.index') }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left mr-1"></i>
            Volver
        </a>
    </div>
@stop

@section('content')
    @unless ($servicio->categoria?->activo)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-1"></i>

            La categoría actualmente asociada se encuentra inactiva.
            Puede conservarla, pero el servicio no podrá reactivarse
            hasta que la categoría vuelva a estar activa.
        </div>
    @endunless

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">
                Datos del servicio o experiencia
            </h3>
        </div>

        <form
            action="{{ route(
                'servicios-experiencias.update',
                $servicio
            ) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="card-body">
                @include('servicios-experiencias._form')
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
                    href="{{ route('servicios-experiencias.index') }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
