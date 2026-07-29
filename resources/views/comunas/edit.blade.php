@extends('adminlte::page')

@section('title', 'Editar comuna')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar comuna</h1>

        <a
            href="{{ route('comunas.index') }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
@stop

@section('content')

    <div class="card card-warning">

        <div class="card-header">
            <h3 class="card-title">
                Editar datos de la comuna
            </h3>
        </div>

        <form
            action="{{ route('comunas.update', $comuna) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="card-body">

                @include('comunas._form')

            </div>

            <div class="card-footer">

                <button
                    type="submit"
                    class="btn btn-warning"
                >
                    <i class="fas fa-save"></i>
                    Actualizar
                </button>

                <a
                    href="{{ route('comunas.index') }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

@stop
