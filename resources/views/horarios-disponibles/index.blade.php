@extends('adminlte::page')

@section('title', 'Horarios de atención')

@section('content_header')
    <div class="d-flex align-items-center">

        <a href="{{ route('horarios-disponibles.generar') }}" class="btn btn-success mr-2">
            <i class="fas fa-calendar-plus mr-1"></i>
            Generar horarios
        </a>

        <a href="{{ route('horarios-disponibles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nuevo horario de atención
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
            <form action="{{ route('horarios-disponibles.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                    </div>

                    <div class="col-md-3 mb-2">
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

                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>
                            Buscar
                        </button>

                        <a href="{{ route('horarios-disponibles.index') }}" class="btn btn-secondary">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Hora de inicio</th>
                        <th>Hora de término</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($horarios as $horario)
                        <tr>
                            <td class="align-middle">
                                {{ $horario->id }}
                            </td>

                            <td class="align-middle">
                                {{ $horario->fecha->format('d/m/Y') }}
                            </td>

                            <td class="align-middle">
                                {{ substr($horario->hora_inicio, 0, 5) }}
                            </td>

                            <td class="align-middle">
                                {{ substr($horario->hora_termino, 0, 5) }}
                            </td>

                            <td class="align-middle">
                                @if ($horario->activo)
                                    <span class="badge badge-success">
                                        Activo
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="align-middle text-nowrap">
                                <a href="{{ route('horarios-disponibles.show', $horario) }}" class="btn btn-info btn-sm"
                                    title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('horarios-disponibles.edit', $horario) }}" class="btn btn-warning btn-sm"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('horarios-disponibles.destroy', $horario) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No hay horarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($horarios->hasPages())
            <div class="card-footer">
                {{ $horarios->links() }}
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
                        Desactivar horario
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        ¿Está seguro de que desea desactivar este horario disponible?
                        <strong id="nombreHorarioDesactivar"></strong>?
                    </p>

                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>

                        El horario dejará de estar disponible para
                        nuevas reservas.
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

    {{-- Modal para activar --}}
    <div class="modal fade" id="modalActivar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle mr-1"></i>
                        Reactivar horario
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        ¿Está seguro de que desea reactivar este horario disponible?
                        <strong id="nombreHorarioActivar"></strong>?
                    </p>

                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle mr-2"></i>

                        El horario volverá a estar disponible para
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
        $('#modalDesactivar').on(
            'show.bs.modal',
            function(event) {
                const boton = $(event.relatedTarget);
                const id = boton.data('id');

                const modal = $(this);

                modal
                    .find('#formDesactivar')
                    .attr(
                        'action',
                        '{{ url('horarios-disponibles') }}/' +
                        id
                    );
            }
        );

        $('#modalActivar').on(
            'show.bs.modal',
            function(event) {
                const boton = $(event.relatedTarget);
                const id = boton.data('id');
                const nombre = boton.data('nombre');

                const modal = $(this);

                modal
                    .find('#nombreHorarioActivar')
                    .text(nombre);

                modal
                    .find('#formActivar')
                    .attr(
                        'action',
                        '{{ url('horarios-disponibles') }}/' +
                        id +
                        '/activar'
                    );
            }
        );
    </script>
@stop
