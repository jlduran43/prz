@extends('adminlte::page')

@section('title', 'Nueva configuración')

@section('content_header')

    <div class="
            d-flex
            justify-content-between
            align-items-center
        ">
        <h1>
            Nueva configuración de cotización
        </h1>
    </div>

@stop


@section('content')

    <div class="card card-primary">

        <div class="card-header">

            <h3 class="card-title">
                Condiciones de cotización
            </h3>

        </div>


        <form method="POST" action="{{ route('configuraciones-cotizacion.store') }}">

            @csrf

            <div class="card-body">

                @include('configuraciones-cotizacion._form')

            </div>


            <div class="card-footer">

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>
                    Guardar
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
