@extends('adminlte::page')

@section('title', 'Verificar ticket')

@section('content_header')

    <div class="text-center">

        <h1>
            Verificación de ticket
        </h1>

    </div>

@stop


@section('content')

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-7 col-md-9">

                <div class="card shadow">

                    <div class="card-body text-center">

                        {{-- FOLIO --}}

                        <h2 class="mb-3">

                            RES-{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}

                        </h2>


                        {{-- ===================================================== --}}
                        {{-- TICKET NO PAGADO --}}
                        {{-- ===================================================== --}}

                        @if ($estadoTicket === 'NO_PAGADO')

                            <div class="alert alert-danger">

                                <h2>

                                    <i class="fas fa-times-circle"></i>

                                    Ticket no válido

                                </h2>

                                <p class="mb-0">

                                    Estado actual:

                                    <strong>
                                        {{ $reserva->estado }}
                                    </strong>

                                </p>

                            </div>


                            {{-- ===================================================== --}}
                            {{-- TICKET YA UTILIZADO --}}
                            {{-- ===================================================== --}}
                        @elseif ($estadoTicket === 'UTILIZADO')
                            <div class="alert alert-danger">

                                <h2>

                                    <i class="fas fa-times-circle"></i>

                                    Ticket ya utilizado

                                </h2>

                                <p class="mt-3 mb-1">

                                    Este ticket ya fue utilizado anteriormente.

                                </p>


                                @if ($reserva->validada_at)
                                    <h4 class="mt-3">

                                        {{ $reserva->validada_at->format('d/m/Y H:i:s') }}

                                    </h4>
                                @endif


                                @if ($reserva->validadaPor)
                                    <p class="mt-3 mb-0">

                                        Validado por:

                                        <strong>

                                            {{ $reserva->validadaPor->name }}

                                        </strong>

                                    </p>
                                @endif

                            </div>


                            {{-- ===================================================== --}}
                            {{-- PRIMER ESCANEO --}}
                            {{-- ===================================================== --}}
                        @elseif ($estadoTicket === 'VALIDADO')
                            <div class="alert alert-success">

                                <h2>

                                    <i class="fas fa-check-circle"></i>

                                    Verificación válida

                                </h2>

                                <p class="mb-0">

                                    Ingreso registrado correctamente.

                                </p>


                                @if ($reserva->validada_at)
                                    <h4 class="mt-3">

                                        {{ $reserva->validada_at->format('d/m/Y H:i:s') }}

                                    </h4>
                                @endif

                            </div>

                        @endif


                        {{-- ===================================================== --}}
                        {{-- TOTAL PAGADO --}}
                        {{-- ===================================================== --}}

                        @if (isset($reserva->total))
                            <p class="mt-4">

                                <strong>
                                    Total pagado:
                                </strong>

                                ${{ number_format($reserva->total, 0, ',', '.') }}

                            </p>
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

@stop