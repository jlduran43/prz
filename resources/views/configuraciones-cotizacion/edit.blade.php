@extends('adminlte::page')

@section('title', 'Editar condiciones de cotización')

@section('content_header')

    <div class="
            d-flex
            justify-content-between
            align-items-center">

        <h1>
            Editar condiciones de cotización
        </h1>
    </div>

@stop

@section('content')

    <div class="card card-warning">

        <div class="card-header">

            <h3 class="card-title">
                Condiciones de cotización
            </h3>

        </div>

        <form method="POST"
            action="{{ route('configuraciones-cotizacion.update', $configuracion) }}">

            @csrf
            @method('PUT')

            <div class="card-body">

                @include('configuraciones-cotizacion._form')

            </div>

            <div class="card-footer">

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i>
                    Actualizar
                </button>

                <a href="{{ route('configuraciones-cotizacion.index') }}"
                    class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>

            </div>

        </form>

    </div>

@stop
