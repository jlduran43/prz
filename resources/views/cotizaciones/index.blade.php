@extends('adminlte::page')

@section('title', 'Cotizaciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">

        <h1 class="m-0">
            Cotizaciones
        </h1>

        <a href="{{ route('reservas.nueva') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nueva cotización
        </a>

    </div>
@stop


@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle mr-1"></i>

            {{ session('success') }}

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>

        </div>
    @endif


    <div class="card">

        div class="card-header">

        <form action="{{ route('cotizaciones.index') }}" method="GET">

            <div class="buscador-fila">

                <div class="buscador-input">

                    <input type="text" name="buscar" class="form-control" value="{{ $buscar ?? '' }}"
                        placeholder="Buscar por folio, cliente o correo...">

                </div>


                <button type="submit" class="btn btn-primary btn-busqueda">

                    <i class="fas fa-search mr-1"></i>

                    <span class="texto-boton">
                        Buscar
                    </span>

                </button>


                <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary btn-limpiar">

                    <i class="fas fa-eraser mr-1"></i>

                    <span class="texto-boton">
                        Limpiar
                    </span>

                </a>

            </div>

        </form>

    </div>


    <div class="card-body table-responsive p-0">

        <table class="table table-hover">

            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Asistentes</th>
                    <th class="text-right">
                        Total
                    </th>
                    <th>Estado</th>
                    <th class="text-right">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody>

                @forelse ($cotizaciones as $cotizacion)
                    @php
                        $nombreCliente =
                            $cotizacion->nombre_entidad ?:
                            trim(($cotizacion->nombres ?? '') . ' ' . ($cotizacion->apellidos ?? ''));
                    @endphp

                    <tr>

                        <td>
                            <strong>
                                {{ $cotizacion->folio }}
                            </strong>
                        </td>

                        <td>
                            {{ $nombreCliente ?: '-' }}

                            <div class="small text-muted">
                                {{ $cotizacion->email }}
                            </div>
                        </td>

                        <td>
                            {{ $cotizacion->fecha_emision->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            {{ $cotizacion->cantidad_asistentes }}
                        </td>

                        <td class="text-right">
                            <strong>
                                ${{ number_format($cotizacion->total, 0, ',', '.') }}
                            </strong>
                        </td>

                        <td>

                            @php
                                $estado = strtoupper($cotizacion->estado ?? '');

                                $badgeEstado = match ($estado) {
                                    'EMITIDA' => 'badge-info',
                                    'ENVIADA' => 'badge-primary',
                                    'ACEPTADA' => 'badge-success',
                                    'ANULADA' => 'badge-danger',
                                    'VENCIDA' => 'badge-secondary',
                                    default => 'badge-light',
                                };

                                $textoEstado = match ($estado) {
                                    'EMITIDA' => 'Emitida',
                                    'ENVIADA' => 'Enviada',
                                    'ACEPTADA' => 'Aceptada',
                                    'ANULADA' => 'Anulada',
                                    'VENCIDA' => 'Vencida',
                                    default => ucfirst(strtolower($estado)),
                                };
                            @endphp

                            <span class="badge {{ $badgeEstado }}">
                                {{ $textoEstado }}
                            </span>

                        </td>

                        <td class="text-right">

                            <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" class="btn btn-info btn-sm"
                                title="Ver cotización">
                                <i class="fas fa-eye"></i>
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No hay cotizaciones registradas.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>


    @if ($cotizaciones->hasPages())
        <div class="card-footer">
            {{ $cotizaciones->links() }}
        </div>
    @endif

    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/buscador.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
@stop
