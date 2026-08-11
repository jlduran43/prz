@extends('adminlte::page')

@section('title', 'Convenios y descuentos')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1 class="mb-1">
                Convenios y descuentos
            </h1>

            <p class="text-muted mb-0">
                Administra los convenios, descuentos
                y entidades autorizadas.
            </p>
        </div>

        <a
            href="{{ route('convenios.create') }}"
            class="btn btn-primary"
        >
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


    <div class="card card-outline card-primary">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-percent mr-2"></i>

                Convenios registrados

            </h3>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="
                        table
                        table-hover
                        table-striped
                        mb-0
                    "
                >

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

                            <th
                                class="text-center"
                                style="width: 150px;"
                            >
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($convenios as $convenio)

                            <tr>

                                {{-- Código --}}
                                <td>

                                    <span
                                        class="
                                            badge
                                            badge-info
                                            p-2
                                        "
                                    >
                                        {{ $convenio->codigo }}
                                    </span>

                                </td>


                                {{-- Nombre --}}
                                <td>

                                    <strong>
                                        {{ $convenio->nombre }}
                                    </strong>

                                </td>


                                {{-- Descuento --}}
                                <td class="text-center">

                                    <span
                                        class="
                                            badge
                                            badge-success
                                            p-2
                                        "
                                    >

                                        <i
                                            class="
                                                fas
                                                fa-percent
                                                mr-1
                                            "
                                        ></i>

                                        {{ number_format(
                                            $convenio->porcentaje_descuento,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </span>

                                </td>


                                {{-- Vigencia --}}
                                <td>

                                    <div>

                                        <i
                                            class="
                                                far
                                                fa-calendar-alt
                                                text-muted
                                                mr-1
                                            "
                                        ></i>

                                        {{ optional(
                                            $convenio->fecha_inicio
                                        )->format('d/m/Y') }}

                                    </div>


                                    <small class="text-muted">

                                        hasta

                                        @if ($convenio->fecha_termino)

                                            {{ $convenio
                                                ->fecha_termino
                                                ->format('d/m/Y') }}

                                        @else

                                            Sin fecha de término

                                        @endif

                                    </small>

                                </td>


                                {{-- Cantidad de entidades --}}
                                <td class="text-center">

                                    <span
                                        class="
                                            badge
                                            badge-secondary
                                            p-2
                                        "
                                    >

                                        <i
                                            class="
                                                fas
                                                fa-building
                                                mr-1
                                            "
                                        ></i>

                                        {{ $convenio->entidades_count }}

                                    </span>

                                </td>


                                {{-- Estado --}}
                                <td class="text-center">

                                    @if ($convenio->activo)

                                        <span
                                            class="
                                                badge
                                                badge-success
                                                p-2
                                            "
                                        >
                                            Activo
                                        </span>

                                    @else

                                        <span
                                            class="
                                                badge
                                                badge-secondary
                                                p-2
                                            "
                                        >
                                            Inactivo
                                        </span>

                                    @endif

                                </td>


                                {{-- Acciones --}}
                                <td class="text-center">

                                    <a
                                        href="{{
                                            route(
                                                'convenios.edit',
                                                $convenio
                                            )
                                        }}"
                                        class="
                                            btn
                                            btn-sm
                                            btn-warning
                                        "
                                        title="Editar"
                                    >

                                        <i class="fas fa-edit"></i>

                                    </a>


                                    @if ($convenio->activo)

                                        <form
                                            action="{{
                                                route(
                                                    'convenios.destroy',
                                                    $convenio
                                                )
                                            }}"
                                            method="POST"
                                            class="d-inline"
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="
                                                    btn
                                                    btn-sm
                                                    btn-danger
                                                "
                                                title="Desactivar"
                                                onclick="
                                                    return confirm(
                                                        '¿Deseas desactivar este convenio?'
                                                    );
                                                "
                                            >

                                                <i
                                                    class="
                                                        fas
                                                        fa-ban
                                                    "
                                                ></i>

                                            </button>

                                        </form>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="
                                        text-center
                                        text-muted
                                        py-5
                                    "
                                >

                                    <i
                                        class="
                                            fas
                                            fa-percent
                                            fa-2x
                                            mb-3
                                        "
                                    ></i>

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

@stop
