@extends('adminlte::page')

@section('title', 'Editar tipo de cliente')

@section('content_header')
    <h1>Editar tipo de cliente</h1>
@stop

@section('content')
    <div class="card card-outline card-warning">
        <form   method="POST"
                action="{{ route(
                    'tipos-cliente.update',
                    $tipoCliente
                ) }}">

            @csrf
            @method('PUT')

            <div class="card-body">
                @include('tipos-cliente._form')
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i>
                    Actualizar
                </button>

                <a href="{{ route('tipos-cliente.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>
            </div>
        </form>
    </div>
@stop
