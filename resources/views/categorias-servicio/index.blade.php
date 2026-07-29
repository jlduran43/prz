@extends('adminlte::page')

@section('title', 'Categorías de servicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Categorías de servicio</h1>

        <a href="{{ route('categorias-servicio.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva categoría
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>

            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>

            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form action="{{ route('categorias-servicio.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" name="buscar" class="form-control" value="{{ $buscar }}"
                                placeholder="Buscar por código o nombre">

                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>

                    @if ($buscar)
                        <div class="col-md-2">
                            <a href="{{ route('categorias-servicio.index') }}" class="btn btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center">Servicios</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center" style="width: 180px;">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            <td>
                                <strong>
                                    {{ $categoria->codigo }}
                                </strong>
                            </td>

                            <td>{{ $categoria->nombre }}</td>

                            <td>
                                {{ \Illuminate\Support\Str::limit($categoria->descripcion, 80) ?: 'Sin descripción' }}
                            </td>

                            <td class="text-center">
                                <span class="badge badge-info">
                                    {{ $categoria->servicios()->count() }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if ($categoria->activo)
                                    <span class="badge badge-success">
                                        Activa
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactiva
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('categorias-servicio.show', $categoria) }}" class="btn btn-info btn-sm"
                                    title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('categorias-servicio.edit', $categoria) }}"
                                    class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if ($categoria->activo)
                                    <button type="button" class="btn btn-danger btn-sm" title="Desactivar"
                                        data-toggle="modal" data-target="#modalDesactivar" data-id="{{ $categoria->id }}"
                                        data-nombre="{{ $categoria->nombre }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success btn-sm" title="Reactivar"
                                        data-toggle="modal" data-target="#modalActivar" data-id="{{ $categoria->id }}"
                                        data-nombre="{{ $categoria->nombre }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No se encontraron categorías.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="modal fade" id="modalDesactivar" tabindex="-1" role="dialog"
                aria-labelledby="modalDesactivarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="modalDesactivarLabel">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Desactivar categoría
                            </h5>

                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <p>
                                ¿Está seguro de que desea desactivar la categoría
                                <strong id="nombreCategoriaDesactivar"></strong>?
                            </p>

                            <div class="alert alert-danger mb-0">
                                <h5>
                                    <i class="icon fas fa-ban"></i>
                                    Atención
                                </h5>

                                La categoría no será eliminada de la base de datos.
                                Solamente quedará <strong>desactivada</strong> y no podrá utilizarse en nuevos registros.
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>

                            <form id="formDesactivar" method="POST" action="">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-ban mr-1"></i>
                                    Sí, desactivar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalActivar" tabindex="-1" role="dialog" aria-labelledby="modalActivarLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title" id="modalActivarLabel">
                                <i class="fas fa-check-circle mr-1"></i>
                                Reactivar categoría
                            </h5>

                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <p>
                                ¿Está seguro de que desea reactivar la categoría
                                <strong id="nombreCategoriaActivar"></strong>?
                            </p>

                            <div class="alert alert-success mb-0">
                                <h5>
                                    <i class="icon fas fa-check-circle"></i>
                                    Confirmación
                                </h5>

                                La categoría volverá a estar disponible para crear nuevos
                                servicios y experiencias.
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>

                            <form id="formActivar" method="POST" action="">
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check mr-1"></i>
                                    Sí, reactivar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($categorias->hasPages())
            <div class="card-footer clearfix">
                {{ $categorias->links() }}
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        $('#modalDesactivar').on('show.bs.modal', function(event) {
            const boton = $(event.relatedTarget);
            const id = boton.data('id');
            const nombre = boton.data('nombre');

            const modal = $(this);

            modal
                .find('#nombreCategoriaDesactivar')
                .text(nombre);

            modal
                .find('#formDesactivar')
                .attr(
                    'action',
                    '{{ url('categorias-servicio') }}/' + id
                );
        });

        $('#modalActivar').on('show.bs.modal', function(event) {
            const boton = $(event.relatedTarget);
            const id = boton.data('id');
            const nombre = boton.data('nombre');

            const modal = $(this);

            modal
                .find('#nombreCategoriaActivar')
                .text(nombre);

            modal
                .find('#formActivar')
                .attr(
                    'action',
                    '{{ url('categorias-servicio') }}/' +
                    id +
                    '/activar'
                );
        });
    </script>
@stop
