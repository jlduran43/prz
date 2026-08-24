@extends('adminlte::page')

@section('title', 'Detalle del convenio')

@section('content_header')
    <h1>Detalle del convenio</h1>
@stop

@section('content')

    {{-- Datos del convenio --}}
    <div class="card">

        {{-- Encabezado turquesa --}}
        <div class="card-header bg-info">
            <h3 class="card-title">
                {{ $convenio->nombre }}
            </h3>
        </div>

        <div class="card-body">

            {{-- Código --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Código</strong>
                </div>

                <div class="col-md-9">
                    {{ $convenio->codigo }}
                </div>
            </div>

            {{-- Nombre --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Nombre</strong>
                </div>

                <div class="col-md-9">
                    {{ $convenio->nombre }}
                </div>
            </div>

            {{-- Descuento --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Descuento</strong>
                </div>

                <div class="col-md-9">
                    {{ number_format($convenio->porcentaje_descuento, 0, ',', '.') }}%
                </div>
            </div>

            {{-- Fecha inicio --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Fecha de inicio</strong>
                </div>

                <div class="col-md-9">
                    {{ optional($convenio->fecha_inicio)->format('d/m/Y') }}
                </div>
            </div>

            {{-- Fecha término --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Fecha de término</strong>
                </div>

                <div class="col-md-9">
                    @if ($convenio->fecha_termino)
                        {{ $convenio->fecha_termino->format('d/m/Y') }}
                    @else
                        Sin fecha de término
                    @endif
                </div>
            </div>

            {{-- Estado --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Estado</strong>
                </div>

                <div class="col-md-9">
                    @if ($convenio->activo)
                        <span class="badge badge-success">
                            Activo
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            Inactivo
                        </span>
                    @endif
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Observaciones</strong>
                </div>

                <div class="col-md-9">
                    {{ $convenio->observaciones ?: 'Sin observaciones' }}
                </div>
            </div>


            {{-- Entidades autorizadas --}}
            <div class="row mb-3">

                <div class="col-md-3">
                    <strong>Entidades autorizadas</strong>
                </div>

                <div class="col-md-9">

                    @forelse ($convenio->entidades as $entidad)
                        <div class="mb-2">
                            {{ $entidad->nombre_entidad }}
                            <span class="text-muted">
                                — {{ $entidad->rut_entidad }}
                            </span>
                        </div>

                    @empty

                        <span class="text-muted">
                            Sin entidades autorizadas
                        </span>
                    @endforelse

                </div>

            </div>


            {{-- Volver --}}
            <div class="mt-4">
                <a href="{{ route('convenios.index') }}" class="btn btn-secondary">

                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver

                </a>
            </div>

        </div>

    </div>

@stop