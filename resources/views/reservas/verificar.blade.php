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

            {{-- MENSAJE ÉXITO --}}

            @if(session('success'))

                <div class="alert alert-success text-center">

                    <h4 class="mb-0">

                        <i class="fas fa-check-circle mr-2"></i>

                        {{ session('success') }}

                    </h4>

                </div>

            @endif


            {{-- MENSAJE YA UTILIZADO --}}

            @if(session('warning'))

                <div class="alert alert-warning text-center">

                    <h4 class="mb-0">

                        <i class="fas fa-exclamation-triangle mr-2"></i>

                        {{ session('warning') }}

                    </h4>

                </div>

            @endif


            {{-- MENSAJE ERROR --}}

            @if(session('error'))

                <div class="alert alert-danger text-center">

                    <h4 class="mb-0">

                        <i class="fas fa-times-circle mr-2"></i>

                        {{ session('error') }}

                    </h4>

                </div>

            @endif


            <div class="card shadow">

                <div class="card-body text-center">

                    {{-- FOLIO --}}

                    <h2 class="mb-3">

                        RES-{{ str_pad(
                            $reserva->id,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ) }}

                    </h2>


                    {{-- ===================================================== --}}
                    {{-- RESERVA NO PAGADA --}}
                    {{-- ===================================================== --}}

                    @if($reserva->estado !== 'PAGADA')

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

                    @elseif($reserva->validada_at)

                        <div class="alert alert-warning">

                            <h2>

                                <i class="fas fa-exclamation-triangle"></i>

                                Ticket ya utilizado

                            </h2>

                            <p class="mt-3 mb-1">

                                Este ticket ya fue validado anteriormente.

                            </p>

                            <h4 class="mt-3">

                                {{ $reserva->validada_at
                                    ->format('d/m/Y H:i:s') }}

                            </h4>


                            @if($reserva->validadaPor)

                                <p class="mt-3 mb-0">

                                    Validado por:

                                    <strong>

                                        {{ $reserva->validadaPor->name }}

                                    </strong>

                                </p>

                            @endif

                        </div>


                    {{-- ===================================================== --}}
                    {{-- TICKET DISPONIBLE --}}
                    {{-- ===================================================== --}}

                    @else

                        <div class="alert alert-success">

                            <h2>

                                <i class="fas fa-check-circle"></i>

                                Ticket válido

                            </h2>

                            <p class="mb-0">

                                Reserva:

                                <strong>
                                    PAGADA
                                </strong>

                            </p>

                        </div>


                        {{-- DATOS DE LA RESERVA --}}

                        <div class="mt-4">

                            @if($reserva->fecha)

                                <p>

                                    <strong>
                                        Fecha de visita:
                                    </strong>

                                    {{ $reserva->fecha->format('d/m/Y') }}

                                </p>

                            @endif


                            @if(isset($reserva->total))

                                <p>

                                    <strong>
                                        Total pagado:
                                    </strong>

                                    ${{ number_format(
                                        $reserva->total,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </p>

                            @endif

                        </div>


                        {{-- ================================================= --}}
                        {{-- FUNCIONARIO AUTENTICADO --}}
                        {{-- ================================================= --}}

                        @auth

                            <hr>

                            <div class="mt-4">

                                <h4>
                                    Control de acceso
                                </h4>

                                <p class="text-muted">

                                    Confirma el ingreso solamente cuando
                                    el visitante se encuentre en la entrada.

                                </p>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'reservas.validar-ingreso',
                                        [
                                            'token' =>
                                                $reserva->token_verificacion
                                        ]
                                    ) }}"
                                    onsubmit="
                                        return confirm(
                                            '¿Confirmar el ingreso de esta reserva?'
                                        );
                                    "
                                >

                                    @csrf


                                    <button
                                        type="submit"
                                        class="btn btn-success btn-lg"
                                    >

                                        <i class="fas fa-check-circle mr-2"></i>

                                        VALIDAR INGRESO

                                    </button>

                                </form>

                            </div>

                        @else

                            {{-- ================================================= --}}
                            {{-- PERSONA NO AUTENTICADA --}}
                            {{-- ================================================= --}}

                            <div class="alert alert-info mt-4">

                                <i class="fas fa-info-circle mr-2"></i>

                                Este ticket está pagado y se encuentra
                                disponible para validación en la entrada.

                            </div>

                        @endauth

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@stop