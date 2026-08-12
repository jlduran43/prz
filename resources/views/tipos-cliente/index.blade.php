@extends('adminlte::page')

@section('title', 'Tipos de cliente')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Tipos de cliente</h1>

        <a href="{{ route('tipos-cliente.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo tipo
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
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('tipos-cliente.index') }}">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text" name="buscar" class="form-control" value="{{ $buscar }}"
                                placeholder="Buscar por código o nombre...">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                            <a href="{{ route('tipos-cliente.index') }}" class="btn btn-secondary ml-1">
                                <i class="fas fa-eraser mr-1"></i>
                                Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo de estructura</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tiposCliente as $tipoCliente)
                        <tr>
                            <td><strong>{{ $tipoCliente->codigo }}</strong></td>
                            <td>{{ $tipoCliente->nombre }}</td>
                            <td>
                                @if ($tipoCliente->tipo_estructura === 'PERSONA')
                                    <span class="badge badge-info">
                                        Persona
                                    </span>
                                @else
                                    <span class="badge badge-primary">
                                        Organización
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($tipoCliente->activo)
                                    <span class="badge badge-success">
                                        Activo
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('tipos-cliente.show', $tipoCliente) }}" class="btn btn-info btn-sm"
                                    title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('tipos-cliente.edit', $tipoCliente) }}" class="btn btn-warning btn-sm"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form method="POST" action="{{ route('tipos-cliente.cambiar-estado', $tipoCliente) }}"
                                    class="d-inline">

                                    @csrf
                                    @method('PATCH')

                                    <button type="button"
                                        class="btn btn-sm
                                            {{ $tipoCliente->activo ? 'btn-secondary' : 'btn-success' }}"
                                        title="{{ $tipoCliente->activo ? 'Desactivar' : 'Activar' }}" data-toggle="modal"
                                        data-target="#modalCambiarEstado" data-id="{{ $tipoCliente->id }}"
                                        data-nombre="{{ $tipoCliente->nombre }}"
                                        data-activo="{{ $tipoCliente->activo ? 1 : 0 }}">

                                        <i class="fas {{ $tipoCliente->activo ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No hay tipos de cliente registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div    class="modal fade"
                    id="modalCambiarEstado"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="modalCambiarEstadoLabel"
                    aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCambiarEstadoLabel">
                                Confirmar cambio de estado
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                        </div>

                        <form id="formCambiarEstado" method="POST" data-url-base="{{ url('tipos-cliente') }}">

                            @csrf
                            @method('PATCH')

                            <div class="modal-body">
                                <p id="mensajeCambiarEstado" class="mb-0"></p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="submit" id="botonConfirmarEstado" class="btn">
                                    Confirmar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if ($tiposCliente->hasPages())
            <div class="card-footer clearfix">
                {{ $tiposCliente->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
@stop

@section('js')
    <script src="{{ asset('js/tipos_clientes/index.js') }}"></script>
@stop
