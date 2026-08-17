<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Cotización {{ $cotizacion->folio }}</title>

    <style>
        @page {
            margin: 30px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* ================================
           ENCABEZADO
        ================================= */

        .header {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 22px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-container {
            width: 40%;
        }

        .logo {
            max-width: 180px;
            max-height: 90px;
        }

        .datos-parque {
            width: 60%;
            text-align: right;
            font-size: 10px;
            line-height: 1.5;
            color: #555;
        }

        .nombre-parque {
            font-size: 15px;
            font-weight: bold;
            color: #222;
            margin-bottom: 4px;
        }

        /* ================================
           TÍTULO COTIZACIÓN
        ================================= */

        .titulo-cotizacion {
            width: 100%;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .titulo-cotizacion table {
            width: 100%;
            border-collapse: collapse;
        }

        .titulo {
            font-size: 23px;
            font-weight: bold;
            color: #222;
        }

        .datos-documento {
            text-align: right;
            font-size: 11px;
            line-height: 1.7;
        }

        .folio {
            font-weight: bold;
            font-size: 13px;
        }

        /* ================================
   DATOS DEL CLIENTE
================================ */

        .seccion {
            margin-bottom: 22px;
        }

        .seccion-titulo {
            font-size: 14px;
            font-weight: bold;
            color: #222;
            border-bottom: 1px solid #999;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla-datos td {
            padding: 5px 7px;
            vertical-align: top;
            font-size: 11px;
        }

        .tabla-datos .etiqueta {
            font-weight: bold;
            width: 18%;
            color: #444;
        }

        .tabla-datos .valor {
            width: 32%;
        }

        .tipo-cliente {
            display: inline-block;
            padding: 4px 8px;
            background-color: #eeeeee;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        /* ================================
   INFORMACIÓN Y CONDICIONES
================================ */

        .condiciones {
            margin-top: 18px;
        }

        .bloque-condicion {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .bloque-condicion-titulo {
            font-size: 11px;
            font-weight: bold;
            color: #222;
            margin-bottom: 5px;
        }

        .bloque-condicion-texto {
            font-size: 10px;
            line-height: 1.6;
            color: #444;
            text-align: justify;
        }

        .nota-importante {
            border: 1px solid #999;
            background-color: #f5f5f5;
            padding: 10px;
            margin-top: 12px;
            page-break-inside: avoid;
        }

        .nota-importante-titulo {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .salto-pagina {
            page-break-before: always;
        }
    </style>
</head>

<body>

    {{-- =====================================
         ENCABEZADO DEL PARQUE
    ====================================== --}}

    <div class="header">

        <table class="header-table">
            <tr>

                {{-- LOGO --}}
                <td class="logo-container">

                    <img src="{{ public_path('images/logo-parque.png') }}" class="logo"
                        alt="Parque Museo Pedro del Río Zañartu">

                </td>

                {{-- DATOS INSTITUCIONALES --}}
                <td class="datos-parque">

                    <div class="nombre-parque">
                        Parque Museo Pedro del Río Zañartu
                    </div>

                    Hualpén, Región del Biobío<br>

                    Chile<br>

                    contacto@prz.cl<br>

                    www.prz.cl

                </td>

            </tr>
        </table>

    </div>


    {{-- =====================================
         INFORMACIÓN DE LA COTIZACIÓN
    ====================================== --}}

    <div class="titulo-cotizacion">

        <table>
            <tr>

                <td>
                    <div class="titulo">
                        COTIZACIÓN
                    </div>
                </td>

                <td class="datos-documento">

                    <div class="folio">
                        {{ $cotizacion->folio }}
                    </div>

                    Fecha de emisión:
                    {{ $cotizacion->created_at->format('d/m/Y') }}

                </td>

            </tr>
        </table>

    </div>

    {{-- =====================================
     DATOS DEL CLIENTE
====================================== --}}

    @php
        $tipoEstructura = $cotizacion->tipoCliente->tipo_estructura ?? null;

        $codigoTipoCliente = $cotizacion->tipoCliente->codigo ?? null;

        $esPersona = $tipoEstructura === 'PERSONA';

        $esEstablecimiento = $tipoEstructura === 'ESTABLECIMIENTO';

        $esOrganizacion = $tipoEstructura === 'ORGANIZACION';
    @endphp


    <div class="seccion">

        <div class="seccion-titulo">
            Datos del cliente
        </div>

        <table class="tabla-datos">
            {{-- PERSONA NATURAL --}}
            {{-- PERSONA NATURAL --}}
            @if ($esPersona)
                <tr>
                    <td class="etiqueta">
                        Nombre:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->nombres }}
                        {{ $cotizacion->apellidos }}
                    </td>

                    <td class="etiqueta">
                        RUT:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->rut_persona ?? '-' }}
                    </td>
                </tr>


                {{-- ESTABLECIMIENTO --}}
            @elseif ($esEstablecimiento)
                <tr>
                    <td class="etiqueta">
                        Establecimiento:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->nombre_entidad ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->rut_entidad ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta">
                        Encargado:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->nombre_encargado ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT encargado:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->rut_encargado ?? '-' }}
                    </td>
                </tr>


                {{-- ORGANIZACIÓN --}}
            @elseif ($esOrganizacion)
                <tr>
                    <td class="etiqueta">
                        Organización:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->nombre_entidad ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->rut_entidad ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta">
                        Encargado:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->nombre_encargado ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT encargado:
                    </td>

                    <td class="valor">
                        {{ $cotizacion->rut_encargado ?? '-' }}
                    </td>
                </tr>
            @endif

            {{-- DATOS DE CONTACTO --}}
            <tr>
                <td class="etiqueta">
                    Correo:
                </td>

                <td class="valor">
                    {{ $cotizacion->email ?? '-' }}
                </td>

                <td class="etiqueta">
                    Teléfono:
                </td>

                <td class="valor">
                    {{ $cotizacion->telefono ?? '-' }}
                </td>
            </tr>


            {{-- REGIÓN Y COMUNA --}}
            <tr>
                <td class="etiqueta">
                    Región:
                </td>

                <td class="valor">
                    {{ $cotizacion->region->nombre ?? '-' }}
                </td>

                <td class="etiqueta">
                    Comuna:
                </td>

                <td class="valor">
                    {{ $cotizacion->comuna->nombre ?? '-' }}
                </td>
            </tr>

        </table>

    </div>

    {{-- =====================================
     SERVICIOS COTIZADOS
====================================== --}}

    <div class="seccion">

        <div class="seccion-titulo">
            Servicios cotizados
        </div>

        <table
            style="
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        ">

            <thead>
                <tr style="background-color: #f2f2f2;">

                    <th
                        style="
                        text-align: left;
                        padding: 8px;
                        border: 1px solid #ccc;
                    ">
                        Servicio
                    </th>

                    <th
                        style="
                        text-align: center;
                        padding: 8px;
                        border: 1px solid #ccc;
                    ">
                        Tipo de cobro
                    </th>

                    <th
                        style="
                        text-align: right;
                        padding: 8px;
                        border: 1px solid #ccc;
                    ">
                        Precio
                    </th>

                    <th
                        style="
                        text-align: center;
                        padding: 8px;
                        border: 1px solid #ccc;
                    ">
                        Pagadas
                    </th>

                    <th
                        style="
                        text-align: center;
                        padding: 8px;
                        border: 1px solid #ccc;
                    ">
                        Liberadas
                    </th>

                    <th
                        style="
                        text-align: right;
                        padding: 8px;
                        border: 1px solid #ccc;
                    ">
                        Subtotal
                    </th>

                </tr>
            </thead>

            <tbody>

                @foreach ($cotizacion->servicios as $detalle)
                    <tr>

                        <td
                            style="
                            padding: 8px;
                            border: 1px solid #ccc;
                        ">
                            {{ $detalle->nombre_servicio }}
                        </td>

                        <td
                            style="
                            text-align: center;
                            padding: 8px;
                            border: 1px solid #ccc;
                        ">
                            {{ $detalle->tipo_cobro === 'POR_PERSONA' ? 'Por persona' : 'Por grupo' }}
                        </td>

                        <td
                            style="
                            text-align: right;
                            padding: 8px;
                            border: 1px solid #ccc;
                        ">
                            ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                        </td>

                        <td
                            style="
                            text-align: center;
                            padding: 8px;
                            border: 1px solid #ccc;
                        ">
                            @if ($detalle->tipo_cobro === 'POR_PERSONA')
                                {{ $detalle->personas_pagadas }}
                            @else
                                -
                            @endif
                        </td>

                        <td
                            style="
                            text-align: center;
                            padding: 8px;
                            border: 1px solid #ccc;
                        ">
                            {{ $detalle->entradas_liberadas ?? 0 }}
                        </td>

                        <td
                            style="
                            text-align: right;
                            padding: 8px;
                            border: 1px solid #ccc;
                            font-weight: bold;
                        ">
                            ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    {{-- =====================================
     RESUMEN DE VALORES
====================================== --}}

    <div class="seccion">

        <div class="seccion-titulo">
            Resumen de cotización
        </div>

        <table
            style="
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        ">

            <tr>
                <td style="padding: 6px;">
                    Cantidad de asistentes:
                </td>

                <td
                    style="
                    padding: 6px;
                    text-align: right;
                    font-weight: bold;
                ">
                    {{ $cotizacion->cantidad_asistentes }}
                </td>
            </tr>

            <tr>
                <td style="padding: 6px;">
                    Subtotal:
                </td>

                <td
                    style="
                    padding: 6px;
                    text-align: right;
                    font-weight: bold;
                ">
                    ${{ number_format($cotizacion->subtotal, 0, ',', '.') }}
                </td>
            </tr>

            @if ($cotizacion->descuento > 0)

                <tr>
                    <td style="padding: 6px;">
                        Descuento:
                    </td>

                    <td
                        style="
                        padding: 6px;
                        text-align: right;
                        font-weight: bold;
                    ">
                        -${{ number_format($cotizacion->descuento, 0, ',', '.') }}
                    </td>
                </tr>

                @if ($cotizacion->nombre_convenio)

                    <tr>
                        <td style="padding: 6px;">
                            Convenio:
                        </td>

                        <td
                            style="
                            padding: 6px;
                            text-align: right;
                        ">
                            {{ $cotizacion->nombre_convenio }}

                            @if ($cotizacion->porcentaje_descuento)
                                (
                                {{ number_format($cotizacion->porcentaje_descuento, 0) }}%
                                )
                            @endif
                        </td>
                    </tr>

                @endif

            @endif

            <tr>
                <td
                    style="
                    padding: 10px 6px;
                    border-top: 2px solid #333;
                    font-size: 14px;
                    font-weight: bold;
                ">
                    Total estimado:
                </td>

                <td
                    style="
                    padding: 10px 6px;
                    border-top: 2px solid #333;
                    text-align: right;
                    font-size: 16px;
                    font-weight: bold;
                ">
                    ${{ number_format($cotizacion->total, 0, ',', '.') }}
                </td>
            </tr>

        </table>

    </div>

    {{-- =====================================
     INFORMACIÓN Y CONDICIONES
====================================== --}}

    <div class="seccion condiciones salto-pagina">

        <div class="seccion-titulo">
            Información y condiciones de la visita
        </div>


        {{-- POLÍTICA DE DEVOLUCIONES --}}
        @if (!empty($configuracion->politica_devoluciones))
            <div class="bloque-condicion">

                <div class="bloque-condicion-titulo">
                    Sobre devoluciones
                </div>

                <div class="bloque-condicion-texto">
                    {!! nl2br(e($configuracion->politica_devoluciones)) !!}
                </div>

            </div>
        @endif

        {{-- CONDICIONES DE PAGO --}}
        @if (!empty($configuracion->condiciones_pago))

            <div class="bloque-condicion">

                <div class="bloque-condicion-titulo">
                    Condiciones de pago
                </div>

                <div class="bloque-condicion-texto">

                    {!! nl2br(e($configuracion->condiciones_pago)) !!}

                    <br><br>

                    @if (!empty($configuracion->titular_cuenta))
                        <strong>Titular de Cuenta:</strong>
                        {{ $configuracion->titular_cuenta }}<br>
                    @endif

                    @if (!empty($configuracion->rut_titular))
                        <strong>RUT:</strong>
                        {{ $configuracion->rut_titular }}<br>
                    @endif

                    @if (!empty($configuracion->banco))
                        <strong>Banco:</strong>
                        {{ $configuracion->banco }}<br>
                    @endif

                    @if (!empty($configuracion->tipo_cuenta))
                        <strong>Tipo de Cuenta:</strong>
                        {{ $configuracion->tipo_cuenta }}<br>
                    @endif

                    @if (!empty($configuracion->numero_cuenta))
                        <strong>Nº Cuenta:</strong>
                        {{ $configuracion->numero_cuenta }}<br>
                    @endif

                    @if (!empty($configuracion->correo_comprobantes))
                        <br>
                        <strong>Comprobante de transferencia:</strong>
                        enviar a {{ $configuracion->correo_comprobantes }}
                    @endif

                </div>

            </div>

        @endif

        {{-- CONFIRMACIÓN DE VIAJE --}}
        <div class="bloque-condicion">

            <div class="bloque-condicion-titulo">
                Confirmación de viaje
            </div>

            <div class="bloque-condicion-texto">

                La confirmación de viaje puede realizarse vía correo a

                <strong>
                    {{ $configuracion->correo_reservas }}
                </strong>

                o llamando al teléfono

                <strong>
                    {{ $configuracion->telefono_reservas }}
                </strong>

                en el horario de

                <strong>
                    {{ $configuracion->horario_contacto }}
                </strong>

            </div>

        </div>

        {{-- VIGENCIA DE LA COTIZACIÓN --}}
        <div class="bloque-condicion">

            <div class="bloque-condicion-titulo">
                Vigencia de la cotización
            </div>

            <div class="bloque-condicion-texto">

                La cotización tiene una validez de

                <strong>
                    {{ $configuracion->dias_validez }} días corridos
                </strong>

                contados desde la fecha de emisión.

            </div>

        </div>

        {{-- CONDICIONES DEL MUSEO --}}
        @if (!empty($configuracion->condiciones_museo))
            <div class="bloque-condicion">

                <div class="bloque-condicion-titulo">
                    Visita Museo
                </div>

                <div class="bloque-condicion-texto">
                    {!! nl2br(e($configuracion->condiciones_museo)) !!}
                </div>

            </div>
        @endif


        {{-- RECOMENDACIONES / NORMAS DEL MUSEO --}}
        @if (!empty($configuracion->recomendaciones_museo))
            <div class="bloque-condicion">

                <div class="bloque-condicion-titulo">
                    Normas para la visita al Museo
                </div>

                <div class="bloque-condicion-texto">
                    {!! nl2br(e($configuracion->recomendaciones_museo)) !!}
                </div>

            </div>
        @endif


        {{-- RECOMENDACIONES DEL PARQUE --}}
        @if (!empty($configuracion->recomendaciones_parque))
            <div class="bloque-condicion">

                <div class="bloque-condicion-titulo">
                    Visita Parque
                </div>

                <div class="bloque-condicion-texto">
                    {!! nl2br(e($configuracion->recomendaciones_parque)) !!}
                </div>

            </div>
        @endif


        {{-- NOTA IMPORTANTE --}}
        @if (!empty($configuracion->nota_importante))
            <div class="nota-importante">

                <div class="nota-importante-titulo">
                    NOTA IMPORTANTE
                </div>

                <div class="bloque-condicion-texto">
                    {!! nl2br(e($configuracion->nota_importante)) !!}
                </div>

            </div>
        @endif

    </div>


</body>

</html>
