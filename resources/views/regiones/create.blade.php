@extends('adminlte::page')

@section('title', 'Nueva región')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Nueva región</h1>
    </div>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Datos de la región</h3>
        </div>

        <form action="{{ route('regiones.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @include('regiones._form')
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar
                </button>
                <a href="{{ route('regiones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>
        </form>
    </div>
@stop
