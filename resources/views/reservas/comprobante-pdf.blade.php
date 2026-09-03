<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        table {
            border-collapse: collapse;
        }

        /*
        |--------------------------------------------------------------------------
        | TICKET COMPLETO
        |--------------------------------------------------------------------------
        */

        .ticket {
            width: 100%;
            border: 1px solid #cccccc;
        }

        .ticket-layout {
            width: 100%;
            table-layout: fixed;
        }

        .ticket-layout>tbody>tr>td {
            vertical-align: top;
        }

        /*
        |--------------------------------------------------------------------------
        | COLUMNA IZQUIERDA
        |--------------------------------------------------------------------------
        */

        .contenido {
            width: 74%;
            padding: 5mm;
            border-right: 1px dashed #bbbbbb;
        }

        /*
        |--------------------------------------------------------------------------
        | COLUMNA DERECHA
        |--------------------------------------------------------------------------
        */

        .ticket-qr {
            width: 26%;
            padding: 5mm 4mm;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | CABECERA
        |--------------------------------------------------------------------------
        */

        .cabecera {
            width: 100%;
            margin-bottom: 3mm;
        }

        .cabecera td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 60%;
        }

        .folio-cell {
            width: 40%;
            text-align: right;
        }

        .logo {
            max-height: 22mm;
            max-width: 75mm;
        }

        .folio-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
        }

        .folio {
            font-size: 18px;
            font-weight: bold;
            color: #137c3a;
        }

        .fecha-emision {
            margin-top: 1mm;
            font-size: 8px;
            color: #555;
        }

        /*
        |--------------------------------------------------------------------------
        | TÍTULOS
        |--------------------------------------------------------------------------
        */

        .titulo {
            font-size: 11px;
            font-weight: bold;
            color: #137c3a;
            margin-bottom: 2mm;
        }

        /*
        |--------------------------------------------------------------------------
        | DATOS RESERVA
        |--------------------------------------------------------------------------
        */

        .datos {
            width: 100%;
            margin-bottom: 3mm;
            border: 1px solid #dddddd;
        }

        .datos td {
            width: 50%;
            vertical-align: top;
            padding: 2.5mm;
        }

        .datos td+td {
            border-left: 1px solid #eeeeee;
        }

        .campo {
            margin-bottom: 1.5mm;
        }

        .campo-label {
            font-weight: bold;
            color: #555;
        }

        .campo-valor {
            margin-top: 0.5mm;
        }

        .verde {
            color: #137c3a;
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | SERVICIOS
        |--------------------------------------------------------------------------
        */

        .servicios {
            width: 100%;
            margin-bottom: 3mm;
        }

        .servicios th {
            background: #f3f5f4;
            font-size: 8px;
            padding: 1.8mm;
            border: 1px solid #dddddd;
            text-align: left;
        }

        .servicios td {
            padding: 1.8mm;
            border: 1px solid #dddddd;
            font-size: 8px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .total-label {
            font-weight: bold;
            text-align: right;
        }

        .total {
            font-size: 11px;
            color: #137c3a;
            font-weight: bold;
            text-align: right;
        }

        /*
        |--------------------------------------------------------------------------
        | INFORMACIÓN INFERIOR
        |--------------------------------------------------------------------------
        */

        .informacion {
            width: 100%;
            border: 1px solid #b7dfc4;
            background: #f8fffa;
        }

        .informacion td {
            vertical-align: top;
            padding: 2mm;
            font-size: 7.5px;
        }

        .informacion ul {
            margin: 1mm 0 0 4mm;
            padding: 0;
        }

        .informacion li {
            margin-bottom: 0.8mm;
        }

        /*
        |--------------------------------------------------------------------------
        | TALÓN QR
        |--------------------------------------------------------------------------
        */

        .ticket-title {
            background: #137c3a;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 2mm;
            margin-bottom: 3mm;
        }

        .qr-folio-label {
            font-size: 8px;
            color: #777;
        }

        .qr-folio {
            color: #137c3a;
            font-weight: bold;
            font-size: 16px;
            margin: 1mm 0 3mm;
        }

        .qr {
            width: 38mm;
            height: 38mm;
        }

        .scan {
            font-size: 8px;
            margin: 2mm 0 4mm;
        }

        .separador {
            border-top: 1px solid #dddddd;
            margin: 3mm 0;
        }

        .detalle-label {
            color: #777;
            font-size: 8px;
            margin-top: 2mm;
        }

        .detalle-valor {
            color: #137c3a;
            font-weight: bold;
            font-size: 11px;
            margin-top: 1mm;
        }

        .mensaje {
            margin-top: 5mm;
            color: #137c3a;
            font-weight: bold;
            font-size: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | EVITAR CORTES
        |--------------------------------------------------------------------------
        */

        .ticket,
        .datos,
        .servicios,
        .informacion {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    @php
        $folio = 'RES-' . str_pad($reserva->id, 6, '0', STR_PAD_LEFT);

        $fechaVisita = $reserva->fecha ? \Carbon\Carbon::parse($reserva->fecha) : null;

        $qrBase64 = null;

        if (!empty($qrPath) && file_exists($qrPath)) {
            $qrBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($qrPath));
        }

        $logoPath = public_path('images/logo-prz.png');

        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp


    <table class="ticket">
        <tr>
            <td>

                <table class="ticket-layout">
                    <tr>

                        {{-- ==============================
                        CONTENIDO IZQUIERDO
                    ============================== --}}
                        <td class="contenido">

                            {{-- CABECERA --}}
                            <table class="cabecera">
                                <tr>

                                    <td class="logo-cell">

                                        @if ($logoBase64)
                                            <img src="{{ $logoBase64 }}" class="logo" alt="Parque PRZ">
                                        @else
                                            <strong
                                                style="
                                                color:#137c3a;
                                                font-size:18px;
                                            ">
                                                PARQUE PEDRO DEL RÍO ZAÑARTU
                                            </strong>
                                        @endif

                                    </td>


                                    <td class="folio-cell">

                                        <div class="folio-label">
                                            Folio reserva
                                        </div>

                                        <div class="folio">
                                            {{ $folio }}
                                        </div>

                                        <div class="fecha-emision">

                                            {{ optional($reserva->pagada_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}

                                        </div>

                                    </td>

                                </tr>
                            </table>


                            {{-- DATOS RESERVA --}}
                            <div class="titulo">
                                Datos de la reserva
                            </div>

                            <table class="datos">
                                <tr>

                                    <td>

                                        <div class="campo">
                                            <div class="campo-label">
                                                Tipo de cliente
                                            </div>

                                            <div class="campo-valor">
                                                {{ $reserva->tipoCliente->nombre ?? '-' }}
                                            </div>
                                        </div>


                                        <div class="campo">
                                            <div class="campo-label">
                                                Entidad
                                            </div>

                                            <div class="campo-valor">

                                                {{ $reserva->entidad ?? ($reserva->nombre_entidad ?? '-') }}

                                            </div>
                                        </div>


                                        <div class="campo">
                                            <div class="campo-label">
                                                Encargado
                                            </div>

                                            <div class="campo-valor">

                                                @if ($reserva->nombre_encargado)
                                                    {{ $reserva->nombre_encargado }}
                                                @elseif($reserva->nombres)
                                                    {{ trim($reserva->nombres . ' ' . $reserva->apellidos) }}
                                                @else
                                                    -
                                                @endif

                                            </div>
                                        </div>


                                        <div class="campo">
                                            <div class="campo-label">
                                                Correo
                                            </div>

                                            <div class="campo-valor">
                                                {{ $reserva->email ?: '-' }}
                                            </div>
                                        </div>


                                        <div class="campo">
                                            <div class="campo-label">
                                                Teléfono
                                            </div>

                                            <div class="campo-valor">
                                                {{ $reserva->telefono ?? '-' }}
                                            </div>
                                        </div>

                                    </td>


                                    <td>

                                        <div class="campo">

                                            <div class="campo-label">
                                                Fecha de visita
                                            </div>

                                            <div
                                                class="
                                                campo-valor
                                                verde
                                            ">

                                                {{ $fechaVisita ? $fechaVisita->translatedFormat('d \d\e F \d\e Y') : '-' }}

                                            </div>

                                        </div>


                                        <div class="campo">

                                            <div class="campo-label">
                                                Horario
                                            </div>

                                            <div class="campo-valor">

                                                @if ($reserva->hora_inicio)

                                                    {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}

                                                    @if ($reserva->hora_termino)
                                                        -

                                                        {{ \Carbon\Carbon::parse($reserva->hora_termino)->format('H:i') }}
                                                    @endif

                                                    hrs.
                                                @else
                                                    -

                                                @endif

                                            </div>

                                        </div>


                                        <div class="campo">

                                            <div class="campo-label">
                                                Cantidad de asistentes
                                            </div>

                                            <div class="campo-valor">

                                                {{ $reserva->cantidad_asistentes ?? '-' }}

                                                personas

                                            </div>

                                        </div>

                                    </td>

                                </tr>
                            </table>


                            {{-- SERVICIOS --}}
                            <div class="titulo">
                                Servicios reservados
                            </div>

                            <table class="servicios">

                                <thead>
                                    <tr>

                                        <th style="width:34%;">
                                            Servicio
                                        </th>

                                        <th style="width:18%;">
                                            Tipo
                                        </th>

                                        <th style="width:12%;" class="center">
                                            Cant.
                                        </th>

                                        <th style="width:18%;" class="right">
                                            Valor
                                        </th>

                                        <th style="width:18%;" class="right">
                                            Subtotal
                                        </th>

                                    </tr>
                                </thead>


                                <tbody>

                                    @foreach ($reserva->servicios as $servicio)
                                        @php

                                            $pivot = $servicio->pivot;

                                            $precio = (float) ($pivot->precio ?? 0);

                                            if ($servicio->tipo_cobro === 'POR_PERSONA') {
                                                $cantidad = (int) ($reserva->cantidad_asistentes ?? 0);
                                            } else {
                                                $cantidad = 1;
                                            }

                                            $subtotal = (float) ($pivot->subtotal ?? $cantidad * $precio);
                                        @endphp


                                        <tr>

                                            <td>
                                                <strong>
                                                    {{ $servicio->nombre }}
                                                </strong>
                                            </td>


                                            <td>

                                                {{ str_replace('_', ' ', $servicio->tipo_cobro ?? '-') }}

                                            </td>


                                            <td class="center">
                                                {{ $cantidad }}
                                            </td>


                                            <td class="right">

                                                ${{ number_format($precio, 0, ',', '.') }}

                                            </td>


                                            <td class="right">

                                                ${{ number_format($subtotal, 0, ',', '.') }}

                                            </td>

                                        </tr>
                                    @endforeach


                                    <tr>

                                        <td colspan="4" class="total-label">
                                            TOTAL PAGADO
                                        </td>

                                        <td class="total">

                                            ${{ number_format((float) $reserva->total, 0, ',', '.') }}

                                        </td>

                                    </tr>

                                </tbody>
                            </table>


                            {{-- INFORMACIÓN --}}
                            <table class="informacion">

                                <tr>

                                    <td style="width:58%;">

                                        <strong
                                            style="
                                            color:#137c3a;
                                        ">
                                            Información importante
                                        </strong>

                                        <ul>

                                            <li>
                                                Presenta este ticket
                                                impreso o en tu celular.
                                            </li>

                                            <li>
                                                Llega 15 minutos antes.
                                            </li>

                                            <li>
                                                El ticket será validado
                                                en el acceso al parque.
                                            </li>

                                        </ul>

                                    </td>


                                    <td style="width:42%;">

                                        <strong>
                                            Contacto
                                        </strong>

                                        <br>

                                        Tel: 41 123 4567

                                        <br>

                                        Email: reservas@prz.cl

                                        <br>

                                        Web: www.prz.cl

                                        <br>

                                        Hualpén,
                                        Región del Biobío

                                    </td>

                                </tr>

                            </table>

                        </td>


                        {{-- ==============================
                        TALÓN DERECHO / QR
                    ============================== --}}

                        <td class="ticket-qr">

                            <div class="ticket-title">
                                TICKET DE RESERVA
                            </div>


                            <div class="qr-folio-label">
                                Folio
                            </div>

                            <div class="qr-folio">
                                {{ $folio }}
                            </div>


                            @if ($qrBase64)
                                <img src="{{ $qrBase64 }}" class="qr" alt="QR">
                            @endif


                            <div class="scan">

                                Escanea este código
                                en el acceso al parque

                            </div>


                            <div class="separador"></div>


                            <div class="detalle-label">
                                Fecha de visita
                            </div>

                            <div class="detalle-valor">

                                {{ $fechaVisita ? $fechaVisita->format('d/m/Y') : '-' }}

                            </div>


                            <div class="detalle-label">
                                Horario
                            </div>

                            <div class="detalle-valor">

                                @if ($reserva->hora_inicio)

                                    {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}

                                    @if ($reserva->hora_termino)
                                        -

                                        {{ \Carbon\Carbon::parse($reserva->hora_termino)->format('H:i') }}
                                    @endif
                                @else
                                    -

                                @endif

                            </div>


                            <div class="detalle-label">
                                Asistentes
                            </div>

                            <div class="detalle-valor">

                                {{ $reserva->cantidad_asistentes ?? '-' }}

                                personas

                            </div>


                            <div class="mensaje">

                                CUIDEMOS
                                <br>
                                NUESTRO PARQUE

                                <br><br>

                                <span
                                    style="
                                    color:#555;
                                    font-weight:normal;
                                ">
                                    Gracias por tu visita
                                </span>

                            </div>

                        </td>

                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
