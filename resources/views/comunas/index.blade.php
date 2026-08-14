@extends('adminlte::page')

@section('title', 'Comunas')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

        <h1 class="mb-2 mb-md-0">
            Comunas
        </h1>

        <a href="{{ route('comunas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nueva comuna
        </a>

    </div>
@stop

@section('content')
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
                action="{{ route('comunas.index') }}"
                method="GET">

                <div class="row align-items-center">
                    {{-- Campo de búsqueda --}}
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
                                placeholder="Buscar por código, comuna o región..."
                                value="{{ $busqueda }}">
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
                                href="{{ route('comunas.index') }}"
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
                        <th style="width: 15%;">
                            Código
                        </th>
                        <th style="width: 25%;">
                            Comuna
                        </th>
                        <th style="width: 30%;">
                            Región
                        </th>
                        <th style="width: 15%;">
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
                    @forelse ($comunas as $comuna)
                        <tr>
                            {{-- Código --}}
                            <td class="align-middle font-weight-bold">
                                {{ $comuna->codigo }}
                            </td>
                            {{-- Comuna --}}
                            <td class="align-middle">
                                {{ $comuna->nombre }}
                            </td>
                            {{-- Región --}}
                            <td class="align-middle">
                                {{ $comuna->region->nombre }}
                            </td>
                            {{-- Estado --}}
                            <td class="align-middle">
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
                            {{-- Acciones --}}
                            <td class="align-middle text-center text-nowrap">
                                <a
                                    href="{{ route('comunas.show', $comuna) }}"
                                    class="btn btn-info btn-sm"
                                    title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a
                                    href="{{ route('comunas.edit', $comuna) }}"
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
                                            $comuna->activo
                                                ? 'btn-secondary'
                                                : 'btn-success'
                                        }}"
                                    title="{{
                                        $comuna->activo
                                            ? 'Desactivar'
                                            : 'Activar'}}"
                                    data-toggle="modal"
                                    data-target="#modalCambiarEstadoComuna"
                                    data-id="{{ $comuna->id }}"
                                    data-nombre="{{ $comuna->nombre }}"
                                    data-activo="{{ $comuna->activo ? 1 : 0 }}">
                                    <i
                                        class="
                                            fas
                                            {{
                                                $comuna->activo
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
                                colspan="5"
                                class="text-center py-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                No existen comunas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Modal cambio de estado --}}
            <div
                class="modal fade"
                id="modalCambiarEstadoComuna"
                tabindex="-1"
                role="dialog"
                aria-labelledby="modalCambiarEstadoComunaLabel"
                aria-hidden="true">
                <div
                    class="modal-dialog modal-dialog-centered"
                    role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="modalCambiarEstadoComunaLabel">
                                Cambiar estado
                            </h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Cerrar">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                        </div>
                        <form
                            id="formCambiarEstadoComuna"
                            method="POST">

                            @csrf
                            @method('PATCH')

                            <div class="modal-body">
                                <p
                                    id="mensajeCambiarEstadoComuna"
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
                                    id="botonConfirmarEstadoComuna"
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
        @if ($comunas->hasPages())
            <div class="card-footer clearfix">
                {{ $comunas->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
@stop

@section('js')
    <script src="{{ asset('js/comunas/index.js') }}"></script>
@stop