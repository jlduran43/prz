@extends('adminlte::page')

@section('title', 'Regiones')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <h1 class="mb-2 mb-md-0">Regiones</h1>

        <a href="{{ route('regiones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva región
        </a>
    </div>
@stop

@section('content')
    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>

            {{ session('success') }}

            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    {{-- Mensaje de error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i>

            {{ session('error') }}

            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        {{-- Buscador --}}
        <div class="card-header">

            <form action="{{ route('regiones.index') }}" method="GET">

                <div class="buscador-fila">
                    {{-- Buscador --}}
                    <div class="buscador-input">

                        <input type="text" name="buscar" class="form-control" value="{{ $buscar ?? '' }}"
                            placeholder="Buscar por código o nombre...">

                    </div>
                    {{-- Buscar --}}
                    <button type="submit" class="btn btn-primary btn-busqueda">
                        <i class="fas fa-search mr-1"></i>

                        <span class="texto-boton">Buscar</span>
                    </button>
                    <a href="{{ route('regiones.index') }}" class="btn btn-secondary btn-limpiar">
                        <i class="fas fa-eraser mr-1"></i>

                        <span class="texto-boton">Limpiar</span>
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabla --}}
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-nowrap mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th class="d-none d-md-table-cell">Comunas</th>
                        <th class="d-none d-sm-table-cell">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($regiones as $region)
                        <tr>
                            {{-- Código --}}
                            <td class="align-middle font-weight-bold">
                                {{ $region->codigo }}
                            </td>
                            {{-- Nombre --}}
                            <td class="align-middle">
                                {{ $region->nombre }}
                            </td>
                            {{-- Comunas --}}
                            <td class="align-middle d-none d-md-table-cell">
                                <span class="badge badge-info">
                                    {{ $region->comunas_count }}
                                </span>
                            </td>
                            {{-- Estado --}}
                            <td class="align-middle d-none d-sm-table-cell">
                                @if ($region->activo)
                                    <span class="badge badge-success">
                                        Activa
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactiva
                                    </span>
                                @endif
                            </td>
                            {{-- Acciones --}}
                            <td class="align-middle text-center">
                                <div class="acciones-botones">
                                    {{-- botones --}}
                                    <a href="{{ route('regiones.show', $region) }}" class="btn btn-info btn-sm" btn-accion
                                        title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('regiones.edit', $region) }}"
                                        class="btn btn-warning btn-sm btn-accion" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="
                                        btn
                                        btn-sm btn-accion
                                        {{ $region->activo ? 'btn-secondary' : 'btn-success' }}"
                                        title="{{ $region->activo ? 'Desactivar' : 'Activar' }}" data-toggle="modal"
                                        data-target="#modalCambiarEstadoRegion" data-id="{{ $region->id }}"
                                        data-nombre="{{ $region->nombre }}" data-activo="{{ $region->activo ? 1 : 0 }}">

                                        <i
                                            class="
                                            fas
                                            {{ $region->activo ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No se encontraron regiones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Modal cambio de estado --}}
            <div class="modal fade" id="modalCambiarEstadoRegion" tabindex="-1" role="dialog"
                aria-labelledby="modalCambiarEstadoRegionLabel" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCambiarEstadoRegionLabel">
                                Confirmar cambio de estado
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                        </div>
                        <form id="formCambiarEstadoRegion" method="POST" data-url-base="{{ url('regiones') }}">

                            @csrf
                            @method('PATCH')

                            <div class="modal-body">
                                <p id="mensajeCambiarEstadoRegion" class="mb-0"></p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="submit" id="botonConfirmarEstadoRegion" class="btn">
                                    Confirmar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paginación --}}
        @if ($regiones->hasPages())
            <div class="card-footer clearfix">
                {{ $regiones->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/buscador.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/regiones/index.js') }}"></script>
@stop
