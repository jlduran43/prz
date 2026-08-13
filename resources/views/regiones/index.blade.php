@extends('adminlte::page')

@section('title', 'Regiones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Regiones</h1>

        <a href="{{ route('regiones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva región
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}

            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}

            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form action="{{ route('regiones.index') }}" method="GET">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input  type="text"
                                    name="buscar"
                                    class="form-control"
                                    value="{{ $busqueda }}"
                                    placeholder="Buscar por código o nombre...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>

                        <a href="{{ route('regiones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-eraser mr-1"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-nowrap">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Comunas</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($regiones as $region)
                        <tr>
                            <td>
                                <strong>{{ $region->codigo }}</strong>
                            </td>
                            <td>{{ $region->nombre }}</td>
                            <td>
                                <span class="badge badge-info">{{ $region->comunas_count }}</span>
                            </td>
                            <td>
                                @if ($region->activo)
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-secondary">Inactiva</span>
                                @endif
                            </td>

                            <td class="text-center text-nowrap">
                                <a href="{{ route('regiones.show', $region) }}" class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('regiones.edit', $region) }}" class="btn btn-warning btn-sm"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button type="button"
                                    class="btn btn-sm
                                        {{ $region->activo ? 'btn-secondary' : 'btn-success' }}"
                                    title="{{ $region->activo ? 'Desactivar' : 'Activar' }}" data-toggle="modal"
                                    data-target="#modalCambiarEstadoRegion" data-id="{{ $region->id }}"
                                    data-nombre="{{ $region->nombre }}" data-activo="{{ $region->activo ? 1 : 0 }}">
                                    <i class="fas {{ $region->activo ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No se encontraron regiones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div    class="modal fade"
                    id="modalCambiarEstadoRegion"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="modalCambiarEstadoRegionLabel"
                    aria-hidden="true">

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

        @if ($regiones->hasPages())
            <div class="card-footer clearfix">
                {{ $regiones->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
@stop

@section('js')
    <script src="{{ asset('js/regiones/index.js') }}"></script>
@stop
