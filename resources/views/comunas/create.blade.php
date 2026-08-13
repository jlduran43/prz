@extends('adminlte::page')

@section('title', 'Nueva comuna')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Nueva comuna</h1>
    </div>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                Datos de la comuna
            </h3>
        </div>
        <form   action="{{ route('comunas.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @include('comunas._form')
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar
                </button>
                <a href="{{ route('comunas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>
            </div>
        </form>
    </div>
@stop
