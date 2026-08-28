<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
            color: #333;
        }

        .contenedor {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
        }

        .titulo {
            color: #137c3a;
            font-size: 24px;
            font-weight: bold;
        }

        .folio {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }

        .detalle {
            background: #f8faf9;
            border: 1px solid #d7eadc;
            border-radius: 8px;
            padding: 18px;
            margin-top: 20px;
        }

        .detalle p {
            margin: 7px 0;
        }

        .importante {
            margin-top: 25px;
            padding: 15px;
            background: #fffbea;
            border-left: 4px solid #d6a700;
        }
    </style>
</head>

<body>

    @php
        $folio = 'RES-' . str_pad($reserva->id, 6, '0', STR_PAD_LEFT);
    @endphp

    <div class="contenedor">

        <div class="titulo">
            ¡Reserva confirmada!
        </div>

        <p>
            Tu reserva ha sido realizada y el pago fue aprobado correctamente.
        </p>

        <div class="folio">
            {{ $folio }}
        </div>

        <div class="detalle">

            <p>
                <strong>Fecha de visita:</strong>
                {{ optional($reserva->fecha)->format('d/m/Y') }}
            </p>

            <p>
                <strong>Horario:</strong>

                @if ($reserva->hora_inicio)
                    {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}

                    @if ($reserva->hora_termino)
                        -
                        {{ \Carbon\Carbon::parse($reserva->hora_termino)->format('H:i') }}
                    @endif
                @else
                    -
                @endif
            </p>

            <p>
                <strong>Asistentes:</strong>
                {{ $reserva->cantidad_asistentes ?? '-' }}
            </p>

            <p>
                <strong>Total pagado:</strong>
                ${{ number_format((float) $reserva->total, 0, ',', '.') }}
            </p>

        </div>

        <div class="importante">
            Hemos adjuntado tu ticket de reserva en formato PDF.
            Preséntalo impreso o desde tu teléfono al ingresar al parque.
        </div>

        <p style="margin-top:25px;">
            Gracias por reservar con Parque Pedro del Río Zañartu.
        </p>

    </div>

</body>

</html>
