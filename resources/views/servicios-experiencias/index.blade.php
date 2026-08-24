@extends('adminlte::page')

@section('title', 'Servicios y experiencias')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <h1 class="mb-3 mb-md-0">Lista de Servicios</h1>

        <a href="{{ route('servicios-experiencias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nuevo servicio
        </a>
    </div>
@stop

@section('content')
    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i>

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
            <form action="{{ route('servicios-experiencias.index') }}" method="GET">
                <div class="row align-items-end">

                    {{-- Buscador --}}
                    <div class="col-12 col-lg-5 mb-2">
                        <label class="small text-muted mb-1">
                            Buscar servicio
                        </label>

                        <input type="text" name="buscar" class="form-control" value="{{ $buscar ?? '' }}"
                            placeholder="Código o nombre...">
                    </div>

                    {{-- Categoría --}}
                    <div class="col-12 col-sm-6 col-lg-3 mb-2">
                        <label class="small text-muted mb-1">
                            Categoría
                        </label>

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

                    {{-- Estado --}}
                    <div class="col-12 col-sm-6 col-lg-2 mb-2">
                        <label class="small text-muted mb-1">
                            Estado
                        </label>

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

                    {{-- Botones --}}
                    <div class="col-12 col-lg-2 mb-2">
                        <div class="d-flex">

                            <button type="submit" class="btn btn-primary flex-fill mr-2">
                                <i class="fas fa-search mr-1"></i>
                                Buscar
                            </button>

                            <a href="{{ route('servicios-experiencias.index') }}" class="btn btn-outline-secondary"
                                title="Limpiar filtros">
                                <i class="fas fa-eraser"></i>
                            </a>

                        </div>
                    </div>

                </div>
            </form>
        </div>
        {{-- Tabla --}}
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Tipo de cobro</th>
                        <th class="text-center">Duración</th>
                        <th width="100">Imagen</th>
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

                            <td>
                                @if ($servicio->imagen)
                                    <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="{{ $servicio->nombre }}"
                                        class="img-thumbnail"
                                        style="width: 70px;
                                               height: 50px;
                                               object-fit: cover;">
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-image"></i>
                                    </span>
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

                            {{-- Acciones --}}
                            <td class="align-middle text-center text-nowrap">
                                <div class="acciones-botones">
                                    <a href="{{ route('servicios-experiencias.show', $servicio) }}"
                                        class="btn btn-info btn-sm" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('servicios-experiencias.edit', $servicio) }}"
                                        class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if ($servicio->activo)
                                        <button type="button" 
                                                class="btn btn-secondary btn-sm" title="Desactivar"
                                            data-toggle="modal" data-target="#modalDesactivar"
                                            data-id="{{ $servicio->id }}" data-nombre="{{ $servicio->nombre }}">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm" title="Reactivar"
                                            data-toggle="modal" data-target="#modalActivar" data-id="{{ $servicio->id }}"
                                            data-nombre="{{ $servicio->nombre }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
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
                <div class="modal-header">
                    <h5 class="modal-title">
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
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <form id="formDesactivar" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
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

@section('css')
    <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
@stop

@section('js')
    <script>
        = "{{ asset('js/servicios_experiencias/index.js') }}"
    </script>
@stop
