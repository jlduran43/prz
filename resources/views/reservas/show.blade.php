@extends('adminlte::page')

@section('title', 'Detalle de reserva')

@section('content_header')
    <h1>Detalle de reserva</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">

            <p>
                <strong>ID:</strong>
                {{ $reserva->id }}
            </p>

            <a href="{{ route('reservas.index') }}" class="btn btn-secondary">
                Volver
            </a>

        </div>
    </div>

@stop
