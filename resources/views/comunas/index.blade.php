@extends('adminlte::page')

@section('title', 'Comunas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Comunas</h1>
        <a href="{{ route('comunas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva comuna
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
            <form action="{{ route('comunas.index') }}" method="GET">
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
                                    placeholder="Buscar por código, comuna o región..."
                                    value="{{ $busqueda }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>

                        <a href="{{ route('comunas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-eraser mr-1"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th width="120">Código</th>
                            <th>Comuna</th>
                            <th>Región</th>
                            <th width="120">Estado</th>
                            <th width="180" class="text-center">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comunas as $comuna)
                            <tr>
                                <td>
                                    <strong>{{ $comuna->codigo }}</strong>
                                </td>
                                <td>
                                    {{ $comuna->nombre }}
                                </td>
                                <td>
                                    {{ $comuna->region->nombre }}
                                </td>
                                <td>
                                    @if ($comuna->activo)
                                        <span class="badge badge-success">
                                            Activo
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('comunas.show', $comuna) }}" class="btn btn-info btn-sm"
                                        title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('comunas.edit', $comuna) }}" class="btn btn-warning btn-sm"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm
                                            {{ $comuna->activo ? 'btn-secondary' : 'btn-success' }}"
                                        title="{{ $comuna->activo ? 'Desactivar' : 'Activar' }}" data-toggle="modal"
                                        data-target="#modalCambiarEstadoComuna" data-id="{{ $comuna->id }}"
                                        data-nombre="{{ $comuna->nombre }}" data-activo="{{ $comuna->activo ? 1 : 0 }}">
                                        <i
                                            class="fas
                                            {{ $comuna->activo ? 'fa-ban' : 'fa-check' }}">
                                        </i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-info-circle"></i>
                                    No existen comunas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="modal fade" id="modalCambiarEstadoComuna" tabindex="-1" role="dialog"
                    aria-labelledby="modalCambiarEstadoComunaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCambiarEstadoComunaLabel">
                                    Confirmar cambio de estado
                                </h5>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">
                                        &times;
                                    </span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <p id="mensajeCambiarEstadoComuna" class="mb-0"></p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>

                                <form id="formCambiarEstadoComuna" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" id="botonConfirmarEstadoComuna" class="btn">
                                        Confirmar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        @if ($comunas->hasPages())
            <div class="card-footer">

                {{ $comunas->links('vendor.pagination.bootstrap-5') }}

            </div>
        @endif

    </div>

@stop
@section('js')
    <script>
        $('#modalCambiarEstadoComuna').on(
            'show.bs.modal',
            function (event) {
                const boton = $(event.relatedTarget);

                const id = boton.data('id');
                const nombre = boton.data('nombre');
                const activo = Number(
                    boton.data('activo')
                );

                const modal = $(this);

                const formulario = modal.find(
                    '#formCambiarEstadoComuna'
                );

                const mensaje = modal.find(
                    '#mensajeCambiarEstadoComuna'
                );

                const botonConfirmar = modal.find(
                    '#botonConfirmarEstadoComuna'
                );

                formulario.attr(
                    'action',
                    '{{ url('comunas') }}/'
                        + id
                        + '/cambiar-estado'
                );

                if (activo === 1) {
                    modal.find('.modal-title').text(
                        'Desactivar comuna'
                    );

                    mensaje.html(
                        '¿Deseas desactivar la comuna '
                        + '<strong>'
                        + nombre
                        + '</strong>?'
                    );

                    botonConfirmar
                        .removeClass('btn-success')
                        .addClass('btn-danger')
                        .text('Sí, desactivar');
                } else {
                    modal.find('.modal-title').text(
                        'Activar comuna'
                    );

                    mensaje.html(
                        '¿Deseas activar la comuna '
                        + '<strong>'
                        + nombre
                        + '</strong>?'
                    );

                    botonConfirmar
                        .removeClass('btn-danger')
                        .addClass('btn-success')
                        .text('Sí, activar');
                }
            }
        );
    </script>
@stop
