<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        {{ $cotizacion->folio }}
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>

<body>

    <h1>Cotización</h1>

    <p>
        <strong>Folio:</strong>
        {{ $cotizacion->folio }}
    </p>

    <p>
        <strong>Fecha de emisión:</strong>
        {{ $cotizacion->created_at->format('d/m/Y') }}
    </p>

</body>

</html>
