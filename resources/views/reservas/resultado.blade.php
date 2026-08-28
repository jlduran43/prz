@extends('adminlte::page')

@section('title', 'Resultado de reserva')

@section('content')

    <div class="container-fluid py-4">

        @if ($reserva->estado === 'PAGADA')

            <div class="card"
                style="
                border: 1px solid #b7dfc4;
                border-radius: 12px;
                box-shadow: none;
            ">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-md-7">

                            <div class="d-flex align-items-start">

                                <div
                                    style="
                                    color:#137c3a;
                                    font-size:42px;
                                    margin-right:18px;
                                ">
                                    <i class="fas fa-check-circle"></i>
                                </div>

                                <div>

                                    <h3
                                        style="
                                        color:#137c3a;
                                        font-weight:700;
                                        margin-bottom:8px;
                                    ">
                                        ¡Reserva confirmada!
                                    </h3>

                                    <p class="mb-1">
                                        Tu reserva ha sido realizada y el pago fue
                                        aprobado correctamente.
                                    </p>

                                    @if ($reserva->ticket_enviado_at)
                                        <p class="mb-0" style="color:#6c757d;">
                                            Se ha enviado un comprobante a
                                            <strong>
                                                {{ $reserva->email }}
                                            </strong>.
                                        </p>
                                    @elseif($reserva->ticket_email_error)
                                        <p class="mb-0" style="color:#b45309;">
                                            El pago fue aprobado, pero no fue posible
                                            enviar el comprobante por correo.
                                        </p>
                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="col-md-5 text-md-right mt-3 mt-md-0">

                            <a href="{{ route('reservas.comprobante', $reserva) }}"
                                class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-download mr-1"></i>
                                Descargar Ticket (PDF)
                            </a>

                            <a href="{{ route('reservas.comprobante.ver', $reserva) }}"
                                target="_blank" class="btn btn-success">
                                <i class="fas fa-print mr-1"></i>
                                Imprimir Ticket
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <p
                style="
                font-size:17px;
                margin-top:20px;
                margin-bottom:15px;
            ">
                Presenta este ticket el día de tu visita en el acceso al parque.
            </p>


            <div class="card">

                <div class="card-body">

                    <h4>
                        RES-{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}
                    </h4>

                    <p>
                        <strong>Fecha:</strong>
                        {{ optional($reserva->fecha)->format('d/m/Y') }}
                    </p>

                    <p>
                        <strong>Asistentes:</strong>
                        {{ $reserva->cantidad_asistentes }}
                    </p>

                    <p>
                        <strong>Total pagado:</strong>

                        <span
                            style="
                            color:#137c3a;
                            font-weight:bold;
                        ">
                            ${{ number_format((float) $reserva->total, 0, ',', '.') }}
                        </span>
                    </p>

                </div>

            </div>
        @elseif($reserva->estado === 'PAGO_FALLIDO')
            <div class="alert alert-danger">

                <h4>
                    <i class="fas fa-times-circle"></i>
                    Pago no aprobado
                </h4>

                <p class="mb-0">
                    El pago con Webpay no fue aprobado.
                    Puedes intentarlo nuevamente.
                </p>

            </div>
        @else
            <div class="alert alert-warning">

                La reserva se encuentra en estado:

                <strong>
                    {{ str_replace('_', ' ', $reserva->estado) }}
                </strong>

            </div>

        @endif

    </div>

@endsection