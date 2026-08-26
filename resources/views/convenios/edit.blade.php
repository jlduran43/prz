@extends('adminlte::page')

@section('title', 'Editar convenio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1">Editar convenio</h1>
            <p class="text-muted mb-0">
                Modifica los datos del convenio,
                su descuento y las entidades autorizadas.
            </p>
        </div>
    </div>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('convenios.update', $convenio) }}" method="POST" id="formConvenio">
        @csrf
        @method('PUT')
        @include('convenios._form')
    </form>
@stop

@section('js')
    <script src="{{ asset('js/convenios/create.js') }}"></script>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/convenios/create.css') }}">
@stop
