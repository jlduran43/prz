@extends('adminlte::page')

@section('title', 'Servicios y experiencias')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Servicios y experiencias</h1>

        <a href="{{ route('servicios-experiencias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nuevo servicio
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
            <form action="{{ route('servicios-experiencias.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="buscar" class="form-control" value="{{ $buscar }}"
                            placeholder="Buscar por código o nombre">
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="categoria_id" class="form-control">
                            <option value="">
                                Todas las categorías
                            </option>

                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected($categoriaId == $categoria->id)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <select name="estado" class="form-control">
                            <option value="">
                                Todos los estados
                            </option>

                            <option value="1" @selected($estado === '1')>
                                Activos
                            </option>

                            <option value="0" @selected($estado === '0')>
                                Inactivos
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>
                            Buscar
                        </button>

                        <a href="{{ route('servicios-experiencias.index') }}"
                            class="btn btn-secondary">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Tipo de cobro</th>
                        <th class="text-center">Duración</th>
                        <th class="text-center">Capacidad</th>
                        <th class="text-right">Precio</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center" style="width: 180px;">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($servicios as $servicio)
                        <tr>
                            <td>
                                <strong>
                                    {{ $servicio->codigo }}
                                </strong>
                            </td>

                            <td>{{ $servicio->nombre }}</td>

                            <td>
                                {{ $servicio->categoria?->nombre ?? 'Sin categoría' }}

                                @if ($servicio->categoria && !$servicio->categoria->activo)
                                    <span class="badge badge-warning">
                                        Categoría inactiva
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($servicio->tipo_cobro === 'POR_PERSONA')
                                    <span class="badge badge-info">
                                        Por persona
                                    </span>
                                @else
                                    <span class="badge badge-primary">
                                        Por grupo
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if ($servicio->duracion_minutos)
                                    {{ $servicio->duracion_minutos }} min
                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-center">
                                @if ($servicio->capacidad_minima || $servicio->capacidad_maxima)
                                    {{ $servicio->capacidad_minima ?? '—' }}
                                    a
                                    {{ $servicio->capacidad_maxima ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-right">
                                ${{ number_format((float) $servicio->precio, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                @if ($servicio->activo)
                                    <span class="badge badge-success">
                                        Activo
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('servicios-experiencias.show', $servicio) }}"
                                    class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('servicios-experiencias.edit', $servicio) }}"
                                    class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if ($servicio->activo)
                                    <button type="button" class="btn btn-danger btn-sm" title="Desactivar"
                                        data-toggle="modal" data-target="#modalDesactivar" data-id="{{ $servicio->id }}"
                                        data-nombre="{{ $servicio->nombre }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success btn-sm" title="Reactivar"
                                        data-toggle="modal" data-target="#modalActivar" data-id="{{ $servicio->id }}"
                                        data-nombre="{{ $servicio->nombre }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No se encontraron servicios o experiencias.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($servicios->hasPages())
            <div class="card-footer">
                {{ $servicios->links() }}
            </div>
        @endif
    </div>

    {{-- Modal para desactivar --}}
    <div class="modal fade" id="modalDesactivar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-ban mr-1"></i>
                        Desactivar servicio
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        ¿Está seguro de que desea desactivar el servicio
                        <strong id="nombreServicioDesactivar"></strong>?
                    </p>

                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>

                        El servicio no será eliminado de la base de datos.
                        Dejará de estar disponible para nuevas reservas.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <form id="formDesactivar" method="POST">
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

    {{-- Modal para reactivar --}}
    <div class="modal fade" id="modalActivar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle mr-1"></i>
                        Reactivar servicio
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        ¿Está seguro de que desea reactivar el servicio
                        <strong id="nombreServicioActivar"></strong>?
                    </p>

                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle mr-2"></i>

                        El servicio volverá a estar disponible para
                        nuevas reservas.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <form id="formActivar" method="POST">
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
@stop

@section('js')
    <script>
        $('#modalDesactivar').on('show.bs.modal', function(event) {
            const boton = $(event.relatedTarget);
            const id = boton.data('id');
            const nombre = boton.data('nombre');

            const modal = $(this);

            modal
                .find('#nombreServicioDesactivar')
                .text(nombre);

            modal
                .find('#formDesactivar')
                .attr(
                    'action',
                    '{{ url('servicios-experiencias') }}/' + id
                );
        });

        $('#modalActivar').on('show.bs.modal', function(event) {
            const boton = $(event.relatedTarget);
            const id = boton.data('id');
            const nombre = boton.data('nombre');

            const modal = $(this);

            modal
                .find('#nombreServicioActivar')
                .text(nombre);

            modal
                .find('#formActivar')
                .attr(
                    'action',
                    '{{ url('servicios-experiencias') }}/' +
                    id +
                    '/activar'
                );
        });
    </script>
@stop
