<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Cotización {{ $cotizacion->folio }}
    </title>
</head>

<body
    style="
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 30px;
    ">

    <div
        style="
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        ">

        <div
            style="
                background: #007bff;
                color: #ffffff;
                padding: 22px 30px;
            ">
            <h2 style="margin: 0;">
                Cotización {{ $cotizacion->folio }}
            </h2>
        </div>

        <div style="padding: 30px;">

            <p>
                Estimado/a cliente:
            </p>

            <p>
                Hemos generado correctamente la cotización
                correspondiente a su visita al
                <strong>
                    Parque Pedro del Río Zañartu
                </strong>.
            </p>

            <p>
                <strong>Folio:</strong>
                {{ $cotizacion->folio }}
            </p>

            <p>
                <strong>Fecha de emisión:</strong>

                {{ optional($cotizacion->created_at)->format('d/m/Y') }}
            </p>

            @if (!empty($cotizacion->total))
                <p>
                    <strong>Total cotizado:</strong>

                    ${{ number_format($cotizacion->total, 0, ',', '.') }}
                </p>
            @endif

            <div
                style="
                    background: #fff3cd;
                    border: 1px solid #ffeeba;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 25px 0;
                ">

                <strong>Importante:</strong>

                <p style="margin-bottom: 0;">

                    Esta cotización no constituye una
                    reserva de cupos ni horarios.

                    La disponibilidad será comprobada
                    nuevamente cuando realice la reserva.

                </p>

            </div>

            <div style="
                    text-align: center;
                    margin: 30px 0;
                ">

                <a href="{{ $urlConvertir }}"
                    style="
                        display: inline-block;
                        background: #28a745;
                        color: #ffffff;
                        padding: 14px 24px;
                        border-radius: 5px;
                        text-decoration: none;
                        font-weight: bold;
                    ">
                    Convertir cotización en reserva
                </a>

            </div>

            <p>
                También encontrará adjunta una copia
                de la cotización en formato PDF.
            </p>

            <hr
                style="
                    border: 0;
                    border-top: 1px solid #dddddd;
                    margin: 30px 0;
                ">

            <p style="
                    color: #777777;
                    font-size: 13px;
                ">

                Parque Pedro del Río Zañartu

            </p>

        </div>

    </div>

</body>

</html>