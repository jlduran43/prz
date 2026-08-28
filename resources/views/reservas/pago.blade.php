@extends('adminlte::page')

@section('title', 'Pago de reserva')

@section('content_header')
    <div>
        <h1 class="mb-1">
            Pago de reserva
        </h1>

        <p class="text-muted mb-0">
            Selecciona tu medio de pago para completar la reserva.
        </p>
    </div>
@stop

@section('content')

    <div class="container-fluid">

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>

                {{ session('error') }}

                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- TIEMPO DISPONIBLE --}}
        {{-- ========================================================= --}}
        <div class="alert alert-warning">
            <i class="fas fa-clock mr-2"></i>

            Tienes
            <strong>15 minutos</strong>
            para completar el pago.
        </div>


        {{-- ========================================================= --}}
        {{-- RESUMEN DEL PAGO --}}
        {{-- ========================================================= --}}
        <div class="card card-outline card-success">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-credit-card mr-2"></i>
                    Resumen de pago
                </h3>
            </div>

            <div class="card-body">

                <div class="row mb-3">

                    <div class="col-md-4 font-weight-bold">
                        Reserva
                    </div>

                    <div class="col-md-8">
                        RES-{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}
                    </div>

                </div>


                <div class="row mb-3">

                    <div class="col-md-4 font-weight-bold">
                        Total
                    </div>

                    <div class="col-md-8">
                        <span class="h3 text-success">
                            ${{ number_format($reserva->total, 0, ',', '.') }}
                        </span>
                    </div>

                </div>


                <div class="row">

                    <div class="col-md-4 font-weight-bold">
                        Tiempo restante
                    </div>

                    <div class="col-md-8">

                        <span id="contador-pago" class="badge badge-warning px-3 py-2"
                            data-expira="{{ optional($reserva->pago_expira_at)->toIso8601String() }}">
                            15:00
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MEDIOS DE PAGO --}}
        {{-- ========================================================= --}}
        <div class="card card-outline card-primary">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-wallet mr-2"></i>
                    Selecciona tu medio de pago
                </h3>

            </div>


            <div class="card-body">

                <p class="text-muted mb-4">
                    Elige el medio de pago que más te acomode.
                </p>


                <div class="row">

                    {{-- ================================================= --}}
                    {{-- WEBPAY --}}
                    {{-- ================================================= --}}
                    <div class="col-lg-3 col-md-6 mb-3">

                        <label class="medio-pago-card" for="medio_webpay">

                            <div class="d-flex align-items-start">

                                <input type="radio" name="medio_pago" id="medio_webpay" value="WEBPAY"
                                    class="medio-pago-radio mt-1" form="form-pago">

                                <div class="ml-3">

                                    <div class="font-weight-bold mb-2">
                                        Webpay / Tarjeta
                                    </div>

                                    <div class="medio-icono text-primary">
                                        <i class="fas fa-credit-card"></i>
                                    </div>

                                    <small class="text-muted d-block mt-3">
                                        Paga con tarjeta de débito
                                        o crédito de forma segura.
                                    </small>

                                </div>

                            </div>

                        </label>

                    </div>


                    {{-- ================================================= --}}
                    {{-- TRANSFERENCIA / KHIPU --}}
                    {{-- ================================================= --}}
                    <div class="col-lg-3 col-md-6 mb-3">

                        <label class="medio-pago-card" for="medio_khipu">

                            <div class="d-flex align-items-start">

                                <input type="radio" name="medio_pago" id="medio_khipu" value="KHIPU"
                                    class="medio-pago-radio mt-1" form="form-pago">

                                <div class="ml-3">

                                    <div class="font-weight-bold mb-2">
                                        Transferencia bancaria
                                    </div>

                                    <div class="medio-icono text-info">
                                        <i class="fas fa-university"></i>
                                    </div>

                                    <small class="text-muted d-block mt-3">
                                        Paga mediante transferencia bancaria
                                        de forma segura con Khipu.
                                    </small>

                                </div>

                            </div>

                        </label>

                    </div>
                </div>


                {{-- ===================================================== --}}
                {{-- INFORMACIÓN --}}
                {{-- ===================================================== --}}
                <div class="alert alert-info mb-0 mt-2">

                    <i class="fas fa-info-circle mr-2"></i>

                    Una vez aprobado el pago,
                    tu reserva será confirmada
                    y recibirás la confirmación
                    correspondiente.

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- CONTACTO --}}
        {{-- ========================================================= --}}
        <div class="card card-outline card-secondary">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-envelope mr-2"></i>
                    Datos para el comprobante
                </h3>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                Correo electrónico
                            </label>

                            <input type="email" class="form-control" value="{{ $reserva->email }}" readonly>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                Teléfono de contacto
                            </label>

                            <input type="text" class="form-control" value="{{ $reserva->telefono }}" readonly>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- BOTONES --}}
        {{-- ========================================================= --}}
        <div
            class="
                d-flex
                flex-column
                flex-sm-row
                justify-content-between
                mb-4
            ">

            <a href="{{ route('reservas.confirmacion') }}" class="btn btn-default mb-2 mb-sm-0">

                <i class="fas fa-arrow-left mr-1"></i>
                Volver

            </a>


            <form action="{{ route('reservas.pago.procesar', $reserva) }}" method="POST" id="form-pago">

                @csrf

                <button type="submit" class="btn btn-success btn-lg" id="btn-continuar-pago" disabled>

                    <i class="fas fa-credit-card mr-1"></i>
                    Continuar al pago

                </button>

            </form>

        </div>

    </div>

@stop


@section('css')

    <style>
        .medio-pago-card {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            height: 100%;
            margin: 0;
            min-height: 210px;
            padding: 20px;
            transition:
                border-color .2s ease,
                background-color .2s ease,
                box-shadow .2s ease;
        }


        .medio-pago-card:hover {
            border-color: #28a745;
        }


        .medio-pago-card.seleccionado {
            background-color: #f1fbf4;
            border-color: #28a745;
            box-shadow:
                0 0 0 1px rgba(40, 167, 69, .10);
        }


        .medio-pago-radio {
            cursor: pointer;
            height: 18px;
            width: 18px;
        }


        .medio-icono {
            font-size: 35px;
        }


        #contador-pago {
            font-size: 16px;
            min-width: 65px;
        }


        @media (max-width: 575.98px) {

            #btn-continuar-pago {
                width: 100%;
            }

        }
    </style>

@stop


@section('js')

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                /*
                 * =====================================================
                 * MEDIOS DE PAGO
                 * =====================================================
                 */

                const radios =
                    document.querySelectorAll(
                        '.medio-pago-radio'
                    );

                const tarjetas =
                    document.querySelectorAll(
                        '.medio-pago-card'
                    );

                const botonContinuar =
                    document.getElementById(
                        'btn-continuar-pago'
                    );


                radios.forEach(function(radio) {

                    radio.addEventListener(
                        'change',
                        function() {

                            tarjetas.forEach(
                                function(tarjeta) {

                                    tarjeta.classList.remove(
                                        'seleccionado'
                                    );

                                }
                            );


                            const tarjetaSeleccionada =
                                radio.closest(
                                    '.medio-pago-card'
                                );


                            if (tarjetaSeleccionada) {

                                tarjetaSeleccionada
                                    .classList
                                    .add(
                                        'seleccionado'
                                    );

                            }


                            botonContinuar.disabled = false;

                        }
                    );

                });


                /*
                 * =====================================================
                 * CONTADOR DE 15 MINUTOS
                 * =====================================================
                 */

                const contador =
                    document.getElementById(
                        'contador-pago'
                    );


                if (!contador) {
                    return;
                }


                const expira =
                    contador.dataset.expira;


                if (!expira) {
                    return;
                }


                const fechaExpiracion =
                    new Date(expira);


                function actualizarContador() {

                    const ahora =
                        new Date();


                    const diferencia =
                        fechaExpiracion.getTime() -
                        ahora.getTime();


                    if (diferencia <= 0) {

                        contador.textContent =
                            '00:00';

                        contador.classList.remove(
                            'badge-warning'
                        );

                        contador.classList.add(
                            'badge-danger'
                        );

                        botonContinuar.disabled = true;

                        return;
                    }


                    const segundosTotales =
                        Math.floor(
                            diferencia / 1000
                        );


                    const minutos =
                        Math.floor(
                            segundosTotales / 60
                        );


                    const segundos =
                        segundosTotales % 60;


                    contador.textContent =
                        String(minutos)
                        .padStart(2, '0') +
                        ':' +
                        String(segundos)
                        .padStart(2, '0');


                    setTimeout(
                        actualizarContador,
                        1000
                    );

                }


                actualizarContador();

                /*
                 * =====================================================
                 * BOTÓN CONTINUAR
                 * =====================================================
                 */

                const formularioPago =
                    document.getElementById('form-pago');

                formularioPago.addEventListener(
                    'submit',
                    function(event) {

                        const medioSeleccionado =
                            document.querySelector(
                                'input[name="medio_pago"]:checked'
                            );

                        if (!medioSeleccionado) {

                            event.preventDefault();

                            alert(
                                'Debes seleccionar un medio de pago.'
                            );

                            return;
                        }

                        botonContinuar.disabled = true;

                        if (medioSeleccionado.value === 'WEBPAY') {

                            botonContinuar.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm mr-2"
                    role="status"
                    aria-hidden="true">
                </span>

                Conectando con Webpay...
            `;

                        }

                        if (medioSeleccionado.value === 'KHIPU') {

                            botonContinuar.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm mr-2"
                    role="status"
                    aria-hidden="true">
                </span>

                Conectando con Khipu...
            `;

                        }

                    }
                );

            }
        );
    </script>

@stop
