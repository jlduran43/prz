<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Verificación de reserva
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px 15px;
            color: #333;
        }

        .contenedor {
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .08);
        }

        .estado {
            text-align: center;
            margin-bottom: 25px;
        }

        .estado-ok {
            color: #198754;
        }

        .estado-error {
            color: #dc3545;
        }

        .folio {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }

        .fila {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .fila:last-child {
            border-bottom: none;
        }

        .titulo {
            font-weight: bold;
            color: #555;
        }

        .valor {
            margin-top: 5px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
        }

        .badge-pagada,
        .badge-confirmada {
            background: #d1e7dd;
            color: #0f5132;
        }

        .badge-pendiente {
            background: #fff3cd;
            color: #664d03;
        }

        .badge-cancelada {
            background: #f8d7da;
            color: #842029;
        }
    </style>
</head>

<body>

    <div class="contenedor">

        <div class="card">

            @php
                $valida = in_array($reserva->estado, ['PAGADA', 'CONFIRMADA']);
            @endphp

            <div class="estado">

                @if ($valida)
                    <h1 class="estado-ok">
                        ✓ Reserva válida
                    </h1>
                @else
                    <h1 class="estado-error">
                        Reserva no válida
                    </h1>
                @endif

                <div class="folio">
                    RES-{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}
                </div>

            </div>

            <div class="fila">
                <div class="titulo">
                    Estado
                </div>

                <div class="valor">

                    @php
                        $claseEstado = match ($reserva->estado) {
                            'PAGADA' => 'badge-pagada',
                            'CONFIRMADA' => 'badge-confirmada',
                            'PENDIENTE_PAGO' => 'badge-pendiente',
                            default => 'badge-cancelada',
                        };
                    @endphp

                    <span class="badge {{ $claseEstado }}">
                        {{ str_replace('_', ' ', $reserva->estado) }}
                    </span>

                </div>
            </div>

            <div class="fila">
                <div class="titulo">
                    Cliente
                </div>

                <div class="valor">
                    {{ $reserva->nombres ?? '-' }}
                </div>
            </div>

            <div class="fila">
                <div class="titulo">
                    Fecha de la visita
                </div>

                <div class="valor">
                    {{ $reserva->fecha ?? '-' }}
                </div>
            </div>

            <div class="fila">
                <div class="titulo">
                    Cantidad de asistentes
                </div>

                <div class="valor">
                    {{ $reserva->cantidad_personas ?? ($reserva->asistentes ?? '-') }}
                </div>
            </div>

            @if ($reserva->servicios && $reserva->servicios->count())

                <div class="fila">
                    <div class="titulo">
                        Servicios
                    </div>

                    <div class="valor">

                        @foreach ($reserva->servicios as $servicio)
                            <div>
                                {{ $servicio->nombre ?? ($servicio->titulo ?? 'Servicio') }}
                            </div>
                        @endforeach

                    </div>
                </div>

            @endif

        </div>

    </div>

</body>

</html>
