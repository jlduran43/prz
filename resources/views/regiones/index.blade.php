@extends('adminlte::page')

@section('title', 'Regiones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Regiones</h1>

        <a
            href="{{ route('regiones.create') }}"
            class="btn btn-primary"
        >
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

            <button
                type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Cerrar"
            >
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}

            <button
                type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Cerrar"
            >
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form
                action="{{ route('regiones.index') }}"
                method="GET"
            >
                <div class="row">
                    <div class="col-md-8">
                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            value="{{ $busqueda }}"
                            placeholder="Buscar por código o nombre..."
                        >
                    </div>

                    <div class="col-md-4">
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>

                        <a
                            href="{{ route('regiones.index') }}"
                            class="btn btn-secondary"
                        >
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
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Comunas</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($regiones as $region)
                        <tr>
                            <td>{{ $region->id }}</td>

                            <td>
                                <strong>{{ $region->codigo }}</strong>
                            </td>

                            <td>{{ $region->nombre }}</td>

                            <td>
                                <span class="badge badge-info">
                                    {{ $region->comunas_count }}
                                </span>
                            </td>

                            <td>
                                @if ($region->activo)
                                    <span class="badge badge-success">
                                        Activa
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Inactiva
                                    </span>
                                @endif
                            </td>

                            <td class="text-center text-nowrap">
                                <a
                                    href="{{ route('regiones.show', $region) }}"
                                    class="btn btn-info btn-sm"
                                    title="Ver"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a
                                    href="{{ route('regiones.edit', $region) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Editar"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form
                                    action="{{ route('regiones.destroy', $region) }}"
                                    method="POST"
                                    class="d-inline formulario-eliminar"
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
                            <td colspan="6" class="text-center py-4">
                                No se encontraron regiones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($regiones->hasPages())
            <div class="card-footer clearfix">
                {{ $regiones->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        document.querySelectorAll('.formulario-eliminar').forEach((formulario) => {
            formulario.addEventListener('submit', function (event) {
                const confirmado = confirm(
                    '¿Está seguro de eliminar esta región?'
                );

                if (!confirmado) {
                    event.preventDefault();
                }
            });
        });
    </script>
@stop
