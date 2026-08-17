@extends('adminlte::page')

@section('title', 'Categorías de servicio')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <h1 class="mb-2 mb-md-0">
            Categorías de servicio
        </h1>
        <a
            href="{{ route('categorias-servicio.create') }}"
            class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nueva categoría
        </a>
    </div>
@stop

@section('content')
    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
            <button
                type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    {{-- Mensaje de error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ session('error') }}
            <button
                type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        {{-- Buscador --}}
        <div class="card-header">
            <form
                action="{{ route('categorias-servicio.index') }}"
                method="GET">
                <div class="row align-items-center">
                    {{-- Campo búsqueda --}}
                    <div class="col-12 col-lg-9 mb-2 mb-lg-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input
                                type="text"
                                name="buscar"
                                class="form-control"
                                value="{{ $buscar }}"
                                placeholder="Buscar por código o nombre...">
                        </div>
                    </div>
                    {{-- Botones --}}
                    <div class="col-12 col-lg-3">
                        <div
                            class="
                                d-flex
                                flex-column
                                flex-sm-row
                                justify-content-lg-end">

                            <button
                                type="submit"
                                class="
                                    btn
                                    btn-primary
                                    mb-2
                                    mb-sm-0
                                    mr-sm-2">
                                <i class="fas fa-search mr-1"></i>
                                Buscar
                            </button>
                            <a
                                href="{{ route('categorias-servicio.index') }}"
                                class="btn btn-secondary">
                                <i class="fas fa-eraser mr-1"></i>
                                Limpiar
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
                        <th style="width: 12%;">
                            Código
                        </th>
                        <th style="width: 22%;">
                            Nombre
                        </th>
                        <th style="width: 31%;">
                            Descripción
                        </th>
                        <th
                            style="width: 10%;"
                            class="text-center">
                            Servicios
                        </th>
                        <th
                            style="width: 10%;"
                            class="text-center">
                            Estado
                        </th>
                        <th
                            style="width: 15%;"
                            class="text-center">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            {{-- Código --}}
                            <td class="align-middle font-weight-bold">
                                {{ $categoria->codigo }}
                            </td>
                            {{-- Nombre --}}
                            <td class="align-middle">
                                {{ $categoria->nombre }}
                            </td>
                            {{-- Descripción --}}
                            <td class="align-middle">
                                {{
                                    \Illuminate\Support\Str::limit(
                                        $categoria->descripcion,
                                        80
                                    ) ?: 'Sin descripción'
                                }}
                            </td>
                            {{-- Servicios --}}
                            <td class="align-middle text-center">
                                <span class="badge badge-info">
                                    {{ $categoria->servicios()->count() }}
                                </span>
                            </td>
                            {{-- Estado --}}
                            <td class="align-middle text-center">
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
                            {{-- Acciones --}}
                            <td class="align-middle text-center text-nowrap">
                                <a
                                    href="{{ route('categorias-servicio.show', $categoria) }}"
                                    class="btn btn-info btn-sm"
                                    title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a
                                    href="{{ route('categorias-servicio.edit', $categoria) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button
                                    type="button"
                                    class="
                                        btn
                                        btn-sm
                                        {{
                                            $categoria->activo
                                                ? 'btn-secondary'
                                                : 'btn-success'
                                        }}"
                                    title="{{
                                        $categoria->activo
                                            ? 'Desactivar'
                                            : 'Activar'
                                    }}"
                                    data-toggle="modal"
                                    data-target="#modalCambiarEstadoCategoria"
                                    data-id="{{ $categoria->id }}"
                                    data-nombre="{{ $categoria->nombre }}"
                                    data-activo="{{ $categoria->activo ? 1 : 0 }}">
                                    <i
                                        class="
                                            fas
                                            {{
                                                $categoria->activo
                                                    ? 'fa-ban'
                                                    : 'fa-check'
                                            }}"
                                    ></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="6"
                                class="text-center py-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                No se encontraron categorías.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Modal cambio de estado --}}
            <div
                class="modal fade"
                id="modalCambiarEstadoCategoria"
                tabindex="-1"
                role="dialog"
                aria-labelledby="modalCambiarEstadoCategoriaLabel"
                aria-hidden="true">
                <div
                    class="modal-dialog modal-dialog-centered"
                    role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="modalCambiarEstadoCategoriaLabel">
                                Confirmar cambio de estado
                            </h5>

                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Cerrar"                            >
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                        </div>

                        <form
                            id="formCambiarEstadoCategoria"
                            method="POST"                        >

                            @csrf

                            <div class="modal-body">
                                <p
                                    id="mensajeCambiarEstadoCategoria"
                                    class="mb-0"
                                ></p>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-dismiss="modal">
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    id="botonConfirmarEstadoCategoria"
                                    class="btn">
                                    Confirmar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- Paginación --}}
        @if ($categorias->hasPages())
            <div class="card-footer clearfix">
                {{ $categorias->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
@stop

@section('js')

    <script>
        <script src="{{ asset('js/categorias-servicios/index.js') }}"></script>
    </script>

@stop