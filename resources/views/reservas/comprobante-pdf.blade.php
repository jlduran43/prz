<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 landscape;
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        .ticket {
            width: 96%;
            margin: 0 auto;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            overflow: hidden;
        }

        .layout {
            border-collapse: collapse;
            table-layout: fixed;
        }

        .main {
            width: 71%;
            vertical-align: top;
            padding: 11px 14px;
            border-right: 1px dashed #cbd5e1;
        }

        .stub {
            width: 26%;
            vertical-align: top;
            padding: 11px 14px;
            text-align: center;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .logo-cell {
            width: 60%;
            vertical-align: middle;
        }

        .folio-cell {
            width: 40%;
            vertical-align: middle;
            text-align: center;
        }

        .logo {
            height: 90px;
            width: auto;
        }

        .folio-label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }

        .folio {
            color: #137c3a;
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }

        .fecha-emision {
            margin-top: 4px;
            font-size: 11px;
        }

        .section {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 9px 11px;
            margin-bottom: 7px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 7px;
            color: #111827;
        }

        .two-cols {
            width: 100%;
            border-collapse: collapse;
        }

        .two-cols td {
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .label {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .value {
            margin-bottom: 4px;
        }

        .highlight {
            color: #137c3a;
            font-weight: bold;
        }

        .services {
            width: 100%;
            border-collapse: collapse;
        }

        .services th {
            text-align: left;
            font-size: 10px;
            padding: 5px 5px;
            border-bottom: 1px solid #d1d5db;
        }

        .services td {
            padding: 5px 5px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .services .right {
            text-align: right;
        }

        .services .center {
            text-align: center;
        }

        .total-row td {
            font-weight: bold;
            border-bottom: none;
            font-size: 13px;
        }

        .total-value {
            color: #137c3a;
            font-size: 16px;
        }

        .info-box {
            border: 1px solid #b7dfc4;
            background: #f8fffa;
        }

        .info-title {
            color: #137c3a;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            width: 50%;
            vertical-align: top;
        }

        .info-table ul {
            padding-left: 17px;
            margin: 0;
        }

        .info-table li {
            margin-bottom: 2px;
        }

        .stub-title {
            background: #137c3a;
            color: white;
            font-weight: bold;
            padding: 7px 10px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .stub-label {
            color: #6b7280;
            text-transform: uppercase;
            font-size: 11px;
        }

        .stub-folio {
            font-size: 22px;
            color: #137c3a;
            font-weight: bold;
            margin: 4px 0 8px;
        }

        .qr-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 7px;
            margin: 0 auto 6px;
            width: 166px;
        }

        .qr {
            width: 150px;
            height: 150px;
        }

        .scan-text {
            font-size: 10px;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .stub-divider {
            border-top: 1px solid #e5e7eb;
            margin: 8px 0;
        }

        .stub-info-label {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .stub-info-value {
            color: #137c3a;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 7px;
        }

        .footer-message {
            color: #137c3a;
            font-weight: bold;
            margin-top: 12px;
            font-size: 11px;
        }

        .contacto-cell {
            padding-left: 25px;
            vertical-align: top;
        }

        .contacto-table {
            border-collapse: collapse;
            width: 100%;
        }

        .contacto-table td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 10px;
        }

        .contacto-table .contacto-icono {
            width: 42px;
            font-weight: bold;
            color: #137c3a;
            padding-right: 7px;
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

    <div class="ticket">

        <table class="layout">
            <tr>

                {{-- PARTE PRINCIPAL --}}
                <td class="main">

                    <table class="header-table">
                        <tr>

                            <td class="logo-cell">

                                @if ($logoBase64)
                                    <img src="{{ $logoBase64 }}" class="logo" Parque Pedro del Río Zañartu">
                                @else
                                    <strong style="font-size: 22px; color:#137c3a;">
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
                    <div class="section">

                        <div class="section-title">
                            Datos de la reserva
                        </div>

                        <table class="two-cols">
                            <tr>

                                <td>

                                    <div class="label">
                                        Tipo de cliente:
                                    </div>

                                    <div class="value">
                                        {{ $reserva->tipoCliente->nombre ?? '-' }}
                                    </div>

                                    <div class="label">
                                        Entidad:
                                    </div>

                                    <div class="value">
                                        {{ $reserva->entidad ?? ($reserva->nombre_entidad ?? '-') }}
                                    </div>

                                    <div class="label">
                                        Encargado:
                                    </div>

                                    <div class="value">
                                        @if ($reserva->nombre_encargado)
                                            {{ $reserva->nombre_encargado }}
                                        @elseif($reserva->nombres)
                                            {{ trim($reserva->nombres . ' ' . $reserva->apellidos) }}
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="label">
                                        Correo:
                                    </div>

                                    <div class="value">
                                        {{ $reserva->email ?: '-' }}
                                    </div>

                                    <div class="label">
                                        Teléfono:
                                    </div>

                                    <div class="value">
                                        {{ $reserva->telefono ?? '-' }}
                                    </div>

                                </td>


                                <td>

                                    <div class="label">
                                        Fecha de visita:
                                    </div>

                                    <div class="value highlight">
                                        {{ $fechaVisita ? $fechaVisita->translatedFormat('d \d\e F \d\e Y') : '-' }}
                                    </div>

                                    <div class="label">
                                        Horario:
                                    </div>

                                    <div class="value">
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

                                    <div class="label">
                                        Cantidad de asistentes:
                                    </div>

                                    <div class="value">
                                        {{ $reserva->cantidad_personas ?? ($reserva->asistentes ?? '-') }}
                                        personas
                                    </div>
                                </td>
                            </tr>
                        </table>

                    </div>


                    {{-- SERVICIOS --}}
                    <div class="section">

                        <div class="section-title">
                            Servicios reservados
                        </div>

                        <table class="services">

                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Tipo de cobro</th>
                                    <th class="center">Cantidad</th>
                                    <th class="right">Valor unitario</th>
                                    <th class="right">Subtotal</th>
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

                                            @if (!empty($servicio->descripcion))
                                                <br>
                                                <span style="font-size:9px;">
                                                    {{ $servicio->descripcion }}
                                                </span>
                                            @endif
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


                                <tr class="total-row">

                                    <td colspan="4" class="right">
                                        TOTAL PAGADO
                                    </td>

                                    <td class="right total-value">
                                        ${{ number_format((float) $reserva->total, 0, ',', '.') }}
                                    </td>

                                </tr>

                            </tbody>
                        </table>

                    </div>


                    {{-- INFORMACIÓN --}}
                    <div class="section info-box">

                        <table class="info-table">
                            <tr>

                                <td>

                                    <div class="info-title">
                                        Información importante
                                    </div>

                                    <ul>
                                        <li>
                                            Presenta este ticket impreso o en tu celular al llegar al parque.
                                        </li>

                                        <li>
                                            Llega con al menos 15 minutos de anticipación.
                                        </li>

                                        <li>
                                            En caso de dudas o cambios, contáctanos con anticipación.
                                        </li>
                                    </ul>

                                </td>

                                <td class="contacto-cell">

                                    <table class="contacto-table">

                                        <tr>
                                            <td class="contacto-icono">
                                                Tel:
                                            </td>
                                            <td>
                                                41 123 4567
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="contacto-icono">
                                                Email:
                                            </td>
                                            <td>
                                                reservas@prz.cl
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="contacto-icono">
                                                Web:
                                            </td>
                                            <td>
                                                www.prz.cl
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="contacto-icono">
                                                Lugar:
                                            </td>
                                            <td>
                                                Hualpén, Región del Biobío
                                            </td>
                                        </tr>

                                    </table>

                                </td>

                            </tr>
                        </table>

                    </div>

                </td>


                {{-- TALÓN DERECHO --}}
                <td class="stub">

                    <div class="stub-title">
                        TICKET DE RESERVA
                    </div>

                    <div class="stub-label">
                        Folio
                    </div>

                    <div class="stub-folio">
                        {{ $folio }}
                    </div>


                    @if ($qrBase64)
                        <div class="qr-box">
                            <img src="{{ $qrBase64 }}" class="qr" alt="QR">
                        </div>
                    @endif


                    <div class="scan-text">
                        Escanea este código en<br>
                        el acceso al parque
                    </div>


                    <div class="stub-divider"></div>


                    <div class="stub-info-label">
                        Fecha de visita
                    </div>

                    <div class="stub-info-value">
                        {{ $fechaVisita ? $fechaVisita->format('d/m/Y') : '-' }}
                    </div>


                    <div class="stub-info-label">
                        Horario
                    </div>

                    <div class="stub-info-value">
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


                    <div class="stub-info-label">
                        Asistentes
                    </div>

                    <div class="stub-info-value">
                        @if ($reserva->cantidad_asistentes)
                            {{ $reserva->cantidad_asistentes }} personas
                        @else
                            -
                        @endif
                    </div>


                    <div class="footer-message">
                        CUIDEMOS<br>
                        NUESTRO PARQUE
                    </div>

                    <div style="margin-top: 8px;">
                        Gracias por tu visita
                    </div>

                </td>

            </tr>
        </table>

    </div>

</body>

</html>
