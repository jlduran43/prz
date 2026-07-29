@extends('adminlte::page')

@section('title', 'Comunas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Comunas</h1>

        <a
            href="{{ route('comunas.create') }}"
            class="btn btn-primary"
        >
            <i class="fas fa-plus"></i>
            Nueva comuna
        </a>
    </div>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card">

        <div class="card-header">

            <form
                action="{{ route('comunas.index') }}"
                method="GET"
            >
                <div class="row">

                    <div class="col-md-4">
                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            placeholder="Buscar por código, comuna o región..."
                            value="{{ $busqueda }}"
                        >
                    </div>

                    <div class="col-md-auto">
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>

                        <a
                            href="{{ route('comunas.index') }}"
                            class="btn btn-secondary"
                        >
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
                                {{ $comuna->codigo }}
                            </td>

                            <td>
                                {{ $comuna->nombre }}
                            </td>

                            <td>
                                {{ $comuna->region->nombre }}
                            </td>

                            <td>

                                @if($comuna->activo)

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

                                <a
                                    href="{{ route('comunas.show', $comuna) }}"
                                    class="btn btn-info btn-sm"
                                    title="Ver"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a
                                    href="{{ route('comunas.edit', $comuna) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Editar"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form
                                    action="{{ route('comunas.destroy', $comuna) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('¿Desea eliminar esta comuna?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Eliminar"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-4"
                            >
                                <i class="fas fa-info-circle"></i>

                                No existen comunas registradas.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if($comunas->hasPages())

            <div class="card-footer">

                {{ $comunas->links('vendor.pagination.bootstrap-5') }}

            </div>

        @endif

    </div>

@stop
