@extends('adminlte::page')

@section('title', 'Condiciones de cotización')

@section('content_header')

    <div
        class="
            d-flex
            flex-column
            flex-md-row
            justify-content-between
            align-items-md-center
        ">

        <h1>
            Condiciones de cotización
        </h1>

        <a href="{{ route('configuraciones-cotizacion.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nueva configuración
        </a>

    </div>

@stop


@section('content')

    @if (session('success'))
        <div
            class="
                alert
                alert-success
                alert-dismissible
                fade
                show
            ">

            <i class="fas fa-check-circle mr-1"></i>

            {{ session('success') }}

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>

        </div>
    @endif


    <div class="card">

        {{-- Buscador --}}
        <div class="card-header">
            <form action="{{ route('configuraciones-cotizacion.index') }}" method="GET">

                <div class="d-flex align-items-center">

                    {{-- Input --}}
                    <div class="flex-grow-1">
                        <input type="text" name="buscar" class="form-control" value="{{ $buscar ?? '' }}"
                            placeholder="Buscar por título o banco...">
                    </div>

                    {{-- Buscar --}}
                    <button type="submit" class="btn btn-primary ml-2 btn-icono-busqueda" title="Buscar">
                        <i class="fas fa-search"></i>
                        <span class="d-none d-md-inline ml-1">Buscar</span>
                    </button>

                    {{-- Limpiar --}}
                    <a href="{{ route('configuraciones-cotizacion.index') }}"
                        class="btn btn-secondary ml-2 btn-icono-busqueda" title="Limpiar">
                        <i class="fas fa-eraser"></i>
                        <span class="d-none d-md-inline ml-1">Limpiar</span>
                    </a>

                </div>

            </form>
        </div>

        {{-- Tabla --}}
        <div class="card-body table-responsive p-0">

            <table class="table table-hover table-striped mb-0">

                <thead>
                    <tr>
                        <th style="width: 30%;">
                            Título
                        </th>

                        <th style="width: 17%;">
                            Banco
                        </th>

                        <th style="width: 17%;">
                            Cuenta
                        </th>

                        <th style="width: 12%;">
                            Validez
                        </th>

                        <th style="width: 10%;" class="text-center">
                            Estado
                        </th>

                        <th style="width: 14%;" class="text-center">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($configuraciones as $configuracion)
                        <tr>

                            <td class="align-middle font-weight-bold">
                                {{ $configuracion->titulo }}
                            </td>

                            <td class="align-middle">
                                {{ $configuracion->banco ?? '-' }}
                            </td>

                            <td class="align-middle">
                                {{ $configuracion->numero_cuenta ?? '-' }}
                            </td>

                            <td class="align-middle">
                                {{ $configuracion->dias_validez }} días
                            </td>

                            <td class="align-middle text-center">

                                @if ($configuracion->activo)
                                    <span class="badge badge-success">
                                        Vigente
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactiva
                                    </span>
                                @endif

                            </td>

                            <td class="align-middle text-center text-nowrap">

                                <div class="acciones-botones">
                                    <a href="{{ route('configuraciones-cotizacion.show', $configuracion) }}"
                                        class="btn btn-info btn-sm" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('configuraciones-cotizacion.edit', $configuracion) }}"
                                        class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Desactivar / Activar --}}
                                    @if ($configuracion->activo)
                                        <button type="button" class="btn btn-secondary btn-sm" title="Desactivar"
                                            data-toggle="modal" data-target="#modalDesactivarConfiguracion"
                                            data-id="{{ $configuracion->id }}" data-titulo="{{ $configuracion->titulo }}">

                                            <i class="fas fa-ban"></i>

                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm" title="Activar"
                                            data-toggle="modal" data-target="#modalActivarConfiguracion"
                                            data-id="{{ $configuracion->id }}" data-titulo="{{ $configuracion->titulo }}">

                                            <i class="fas fa-check"></i>

                                        </button>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center py-4">

                                <i class="fas fa-info-circle mr-1"></i>
                                No existen configuraciones registradas.

                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
            {{-- Modal desactivar configuración --}}
            <div class="modal fade" id="modalDesactivarConfiguracion" tabindex="-1" role="dialog" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered" role="document">

                    <div class="modal-content">

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Desactivar configuración
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">

                                <span aria-hidden="true">&times;</span>

                            </button>

                        </div>

                        <form id="formDesactivarConfiguracion" method="POST">

                            @csrf
                            @method('DELETE')

                            <div class="modal-body">

                                <p class="mb-0">
                                    ¿Está seguro de que desea desactivar la configuración
                                    <strong id="tituloConfiguracionDesactivar"></strong>?
                                </p>

                            </div>

                            <div class="modal-footer">

                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>

                                <button type="submit" class="btn btn-danger">
                                    Sí, desactivar
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
            {{-- Modal activar configuración --}}
            <div class="modal fade" id="modalActivarConfiguracion" tabindex="-1" role="dialog" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered" role="document">

                    <div class="modal-content">

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Activar configuración
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">

                                <span aria-hidden="true">&times;</span>

                            </button>

                        </div>

                        <form id="formActivarConfiguracion" method="POST">

                            @csrf
                            @method('PATCH')

                            <div class="modal-body">

                                <p class="mb-0">
                                    ¿Está seguro de que desea activar la configuración
                                    <strong id="tituloConfiguracionActivar"></strong>?
                                </p>

                            </div>

                            <div class="modal-footer">

                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>

                                <button type="submit" class="btn btn-success">
                                    Sí, activar
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>


        {{-- Paginación --}}
        @if ($configuraciones->hasPages())
            <div class="card-footer clearfix">

                {{ $configuraciones->links('vendor.pagination.bootstrap-5') }}

            </div>
        @endif

    </div>


    @if ($configuraciones->hasPages())
        <div class="card-footer">

            {{ $configuraciones->links() }}

        </div>
    @endif

    </div>

@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/buscador.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
@stop
@section('js')
    <script src="{{ asset('js/configuraciones_cotizacion/index.js') }}"></script>
@stop