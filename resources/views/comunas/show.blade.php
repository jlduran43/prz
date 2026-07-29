@extends('adminlte::page')

@section('title', 'Detalle de Comuna')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalle de Comuna</h1>

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

<div class="card card-info">

    <div class="card-header">
        <h3 class="card-title">
            Información de la comuna
        </h3>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <div class="form-group">
                    <label>Código</label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $comuna->codigo }}"
                        readonly
                    >
                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group">
                    <label>Región</label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $comuna->region->nombre }}"
                        readonly
                    >
                </div>

            </div>

        </div>

        <div class="form-group">

            <label>Nombre</label>

            <input
                type="text"
                class="form-control"
                value="{{ $comuna->nombre }}"
                readonly
            >

        </div>

        <div class="form-group">

            <label>Estado</label>

            <div>

                @if($comuna->activo)
                    <span class="badge badge-success">
                        Activo
                    </span>
                @else
                    <span class="badge badge-danger">
                        Inactivo
                    </span>
                @endif

            </div>

        </div>

        <div class="row">

            <div class="col-md-6">

                <div class="form-group">
                    <label>Fecha de creación</label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $comuna->created_at->format('d/m/Y H:i') }}"
                        readonly
                    >
                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group">
                    <label>Última actualización</label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $comuna->updated_at->format('d/m/Y H:i') }}"
                        readonly
                    >
                </div>

            </div>

        </div>

    </div>

    <div class="card-footer">

        <a
            href="{{ route('comunas.index') }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>

        <a
            href="{{ route('comunas.edit', $comuna) }}"
            class="btn btn-warning"
        >
            <i class="fas fa-edit"></i>
            Editar
        </a>

    </div>

</div>

@stop
