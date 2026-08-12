@extends('adminlte::page')

@section('title', 'Nuevo tipo de cliente')

@section('content_header')
    <h1>Nuevo tipo de cliente</h1>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <form method="POST" action="{{ route('tipos-cliente.store') }}">
            @csrf

            <div class="card-body">
                @include('tipos-cliente._form')
            </div>

            <div class="card-footer">
                <button
                    type="submit"
                    class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>
                    Guardar
                </button>

                <a href="{{ route('tipos-cliente.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>
            </div>
        </form>
    </div>
@stop
