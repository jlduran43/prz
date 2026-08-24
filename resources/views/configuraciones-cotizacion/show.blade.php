@extends('adminlte::page')

@section('title', 'Detalle de configuración')

@section('content_header')

    <h1>
        Detalle de configuración
    </h1>

@stop


@section('content')

    <div class="card">

        {{-- Encabezado --}}
        <div class="card-header bg-info">

            <h3 class="card-title">
                {{ $configuracion->titulo }}
            </h3>

        </div>


        <div class="card-body">

            {{-- Título --}}
            <div class="row mb-3">

                <div class="col-md-3">
                    <strong>
                        Título
                    </strong>
                </div>

                <div class="col-md-9">
                    {{ $configuracion->titulo }}
                </div>

            </div>


            {{-- Banco --}}
            <div class="row mb-3">

                <div class="col-md-3">
                    <strong>
                        Banco
                    </strong>
                </div>

                <div class="col-md-9">
                    {{ $configuracion->banco ?? 'Sin banco' }}
                </div>

            </div>


            {{-- Tipo de cuenta --}}
            @if (isset($configuracion->tipo_cuenta))
                <div class="row mb-3">

                    <div class="col-md-3">
                        <strong>
                            Tipo de cuenta
                        </strong>
                    </div>

                    <div class="col-md-9">
                        {{ $configuracion->tipo_cuenta ?? '-' }}
                    </div>

                </div>
            @endif


            {{-- Número de cuenta --}}
            <div class="row mb-3">

                <div class="col-md-3">
                    <strong>
                        Número de cuenta
                    </strong>
                </div>

                <div class="col-md-9">
                    {{ $configuracion->numero_cuenta ?? '-' }}
                </div>

            </div>


            {{-- Titular --}}
            @if (isset($configuracion->titular_cuenta))
                <div class="row mb-3">

                    <div class="col-md-3">
                        <strong>
                            Titular de cuenta
                        </strong>
                    </div>

                    <div class="col-md-9">
                        {{ $configuracion->titular_cuenta ?? '-' }}
                    </div>

                </div>
            @endif


            {{-- RUT --}}
            @if (isset($configuracion->rut_titular))
                <div class="row mb-3">

                    <div class="col-md-3">
                        <strong>
                            RUT titular
                        </strong>
                    </div>

                    <div class="col-md-9">
                        {{ $configuracion->rut_titular ?? '-' }}
                    </div>

                </div>
            @endif


            {{-- Validez --}}
            <div class="row mb-3">

                <div class="col-md-3">
                    <strong>
                        Validez de cotización
                    </strong>
                </div>

                <div class="col-md-9">
                    {{ $configuracion->dias_validez }} días
                </div>

            </div>


            {{-- Estado --}}
            <div class="row mb-3">

                <div class="col-md-3">
                    <strong>
                        Estado
                    </strong>
                </div>

                <div class="col-md-9">

                    @if ($configuracion->activo)
                        <span class="badge badge-success">
                            Vigente
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            Inactiva
                        </span>
                    @endif

                </div>

            </div>


            {{-- Condiciones --}}
            @if (isset($configuracion->condiciones))
                <div class="row mb-3">

                    <div class="col-md-3">
                        <strong>
                            Condiciones
                        </strong>
                    </div>

                    <div class="col-md-9">
                        {!! nl2br(e($configuracion->condiciones ?? 'Sin condiciones')) !!}
                    </div>

                </div>
            @endif


            {{-- Botón volver --}}
            <div class="mt-4">

                <a href="{{ route('configuraciones-cotizacion.index') }}" class="btn btn-secondary">

                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver

                </a>

            </div>

        </div>

    </div>

@stop