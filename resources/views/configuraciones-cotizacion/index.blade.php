@extends('adminlte::page')

@section(
    'title',
    'Condiciones de cotización'
)

@section('content_header')

    <div
        class="
            d-flex
            flex-column
            flex-md-row
            justify-content-between
            align-items-md-center
        "
    >

        <h1>
            Condiciones de cotización
        </h1>

        <a
            href="{{
                route(
                    'configuraciones-cotizacion.create'
                )
            }}"
            class="btn btn-primary"
        >
            <i class="fas fa-plus mr-1"></i>
            Nueva configuración
        </a>

    </div>

@stop


@section('content')

    @if (session('success'))

        <div
            class="
                alert
                alert-success
                alert-dismissible
                fade
                show
            "
        >

            <i class="fas fa-check-circle mr-1"></i>

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

        <div class="card-body table-responsive p-0">

            <table
                class="
                    table
                    table-hover
                    text-nowrap
                "
            >

                <thead>
                    <tr>

                        <th>
                            Título
                        </th>

                        <th>
                            Banco
                        </th>

                        <th>
                            Cuenta
                        </th>

                        <th>
                            Validez
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Acciones
                        </th>

                    </tr>
                </thead>


                <tbody>

                    @forelse (
                        $configuraciones
                        as $configuracion
                    )

                        <tr>

                            <td>
                                {{
                                    $configuracion->titulo
                                }}
                            </td>

                            <td>
                                {{
                                    $configuracion->banco
                                    ?? '-'
                                }}
                            </td>

                            <td>
                                {{
                                    $configuracion->numero_cuenta
                                    ?? '-'
                                }}
                            </td>

                            <td>
                                {{
                                    $configuracion->dias_validez
                                }}
                                días
                            </td>

                            <td>

                                @if ($configuracion->activo)

                                    <span
                                        class="
                                            badge
                                            badge-success
                                        "
                                    >
                                        Vigente
                                    </span>

                                @else

                                    <span
                                        class="
                                            badge
                                            badge-secondary
                                        "
                                    >
                                        Inactiva
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{
                                        route(
                                            'configuraciones-cotizacion.edit',
                                            $configuracion
                                        )
                                    }}"
                                    class="
                                        btn
                                        btn-warning
                                        btn-sm
                                    "
                                    title="Editar"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>


                                @unless (
                                    $configuracion->activo
                                )

                                    <form
                                        action="{{
                                            route(
                                                'configuraciones-cotizacion.activar',
                                                $configuracion
                                            )
                                        }}"
                                        method="POST"
                                        class="d-inline"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="
                                                btn
                                                btn-success
                                                btn-sm
                                            "
                                            title="Activar"
                                        >
                                            <i
                                                class="
                                                    fas
                                                    fa-check
                                                "
                                            ></i>
                                        </button>

                                    </form>

                                @endunless

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="
                                    text-center
                                    text-muted
                                    py-4
                                "
                            >
                                No existen configuraciones registradas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if (
            $configuraciones->hasPages()
        )

            <div class="card-footer">

                {{
                    $configuraciones->links()
                }}

            </div>

        @endif

    </div>

@stop