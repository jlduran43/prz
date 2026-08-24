@extends('adminlte::page')

@section('title', 'Convenios y descuentos')

@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

        <div>
            <h1 class="mb-1">
                Convenios y descuentos
            </h1>

            <p class="text-muted mb-0">
                Administra los convenios, descuentos
                y entidades autorizadas.
            </p>
        </div>

        <a href="{{ route('convenios.create') }}" class="btn btn-primary mt-3 mt-md-0">
            <i class="fas fa-plus mr-1"></i>
            Nuevo convenio
        </a>

    </div>

@stop


@section('content')

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success">

            <i class="fas fa-check-circle mr-1"></i>

            {{ session('success') }}

        </div>
    @endif


    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-percent mr-2"></i>

                Convenios registrados

            </h3>

        </div>
        {{-- Buscador --}}
        <div class="card-header">
            <form action="{{ route('convenios.index') }}" method="GET">
                <div class="buscador-fila">

                    <div class="buscador-input">
                        <input type="text" name="buscar" class="form-control" value="{{ $buscar ?? '' }}"
                            placeholder="Buscar por código o nombre...">
                    </div>

                    <button type="submit" class="btn btn-primary btn-busqueda">
                        <i class="fas fa-search mr-1"></i>
                        <span class="texto-boton">
                            Buscar
                        </span>
                    </button>

                    <a href="{{ route('convenios.index') }}" class="btn btn-secondary btn-limpiar">
                        <i class="fas fa-eraser mr-1"></i>
                        <span class="texto-boton">
                            Limpiar
                        </span>
                    </a>

                </div>
            </form>
        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class=" table
                            table-hover
                            table-striped
                            mb-0">

                    <thead>

                        <tr>

                            <th>
                                Código
                            </th>

                            <th>
                                Nombre
                            </th>

                            <th class="text-center">
                                Descuento
                            </th>

                            <th>
                                Vigencia
                            </th>

                            <th class="text-center">
                                Entidades
                            </th>

                            <th class="text-center">
                                Estado
                            </th>

                            <th class="text-center" style="width: 150px;">
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($convenios as $convenio)
                            <tr class="align-middle">

                                {{-- Código --}}
                                <td class="align-middle">

                                    <span
                                        class="
                                            badge
                                            badge-info
                                            p-2
                                        ">
                                        {{ $convenio->codigo }}
                                    </span>

                                </td>


                                {{-- Nombre --}}
                                <td class="align-middle">

                                    <strong>
                                        {{ $convenio->nombre }}
                                    </strong>

                                </td>


                                {{-- Descuento --}}
                                <td class="text-center align-middle">

                                    <span
                                        class="
                                            badge
                                            badge-success
                                            p-2
                                        ">

                                        <i
                                            class="
                                                fas
                                                fa-percent
                                                mr-1
                                            "></i>

                                        {{ number_format($convenio->porcentaje_descuento, 0, ',', '.') }}

                                    </span>

                                </td>


                                {{-- Vigencia --}}
                                <td class="align-middle">

                                    <div>

                                        <i
                                            class="
                                                far
                                                fa-calendar-alt
                                                text-muted
                                                mr-1
                                            "></i>

                                        {{ optional($convenio->fecha_inicio)->format('d/m/Y') }}

                                    </div>


                                    <small class="text-muted">

                                        hasta

                                        @if ($convenio->fecha_termino)
                                            {{ $convenio->fecha_termino->format('d/m/Y') }}
                                        @else
                                            Sin fecha de término
                                        @endif

                                    </small>

                                </td>


                                {{-- Cantidad de entidades --}}
                                <td class="text-center align-middle">

                                    <span
                                        class=" badge
                                                badge-secondary p-2">

                                        <i
                                            class="
                                            fas
                                            fa-building
                                            mr-1">
                                        </i>

                                        {{ $convenio->entidades_count }}

                                    </span>

                                </td>

                                {{-- Estado --}}
                                <td class="text-center align-middle">

                                    @if ($convenio->activo)
                                        <span
                                            class="
                                                badge
                                                badge-success
                                                p-2
                                            ">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="
                                                badge
                                                badge-secondary
                                                p-2
                                            ">
                                            Inactivo
                                        </span>
                                    @endif

                                </td>


                                {{-- Acciones --}}
                                <td class="align-middle text-center text-nowrap">
                                    <div class="acciones-botones">
                                        <a href="{{ route('convenios.show', $convenio) }}" class="btn btn-info btn-sm"
                                            title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('convenios.edit', $convenio) }}" class="btn btn-sm btn-warning"
                                            title="Editar">

                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if ($convenio->activo)
                                            {{-- Desactivar --}}
                                            <button type="button" class="btn btn-secondary btn-sm" title="Desactivar"
                                                data-toggle="modal" data-target="#modalDesactivarConvenio"
                                                data-id="{{ $convenio->id }}" data-nombre="{{ $convenio->nombre }}">

                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            {{-- Reactivar --}}
                                            <button type="button" class="btn btn-success btn-sm" title="Reactivar"
                                                data-toggle="modal" data-target="#modalActivarConvenio"
                                                data-id="{{ $convenio->id }}" data-nombre="{{ $convenio->nombre }}">

                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="
                                        text-center
                                        text-muted
                                        py-5">
                                    <i
                                        class="
                                            fas
                                            fa-percent
                                            fa-2x
                                            mb-3">
                                    </i>

                                    <div>
                                        No existen convenios
                                        registrados.
                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if ($convenios->hasPages())
            <div class="card-footer">

                {{ $convenios->links() }}

            </div>
        @endif

    </div>
    {{-- Modal desactivar convenio --}}
    <div class="modal fade" id="modalDesactivarConvenio" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Desactivar convenio
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">

                        <span aria-hidden="true">
                            &times;
                        </span>

                    </button>

                </div>

                <div class="modal-body">

                    <p class="mb-0">
                        ¿Está seguro de que desea desactivar el convenio
                        <strong id="nombreConvenioDesactivar"></strong>?
                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">

                        Cancelar

                    </button>

                    <form id="formDesactivarConvenio" method="POST">

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
    {{-- Modal activar convenio --}}
    <div class="modal fade" id="modalActivarConvenio" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Reactivar convenio
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">

                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <p class="mb-0">
                        ¿Está seguro de que desea reactivar el convenio
                        <strong id="nombreConvenioActivar"></strong>?
                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <form id="formActivarConvenio" method="POST">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-success">
                            Sí, reactivar
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </div>

@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/buscador.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones_botones.css') }}">
@stop
@section('js')
    <script src="{{ asset('js/convenios/index.js') }}"></script>
@stop
