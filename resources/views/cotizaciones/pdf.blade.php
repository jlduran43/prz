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
        $cliente = $cotizacion->cliente;

        $codigoTipoCliente = $cliente->tipoCliente->codigo ?? null;

        $esPersona = $codigoTipoCliente === 'PERSONA';

        $esEstablecimiento = $codigoTipoCliente === 'ESTABLECIMIENTO_EDUCACIONAL';

        $esTourOperador = $codigoTipoCliente === 'TOUR_OPERADOR_AGENCIA_VIAJES';
    @endphp


    <div class="seccion">

        <div class="seccion-titulo">
            Datos del cliente
        </div>

        <table class="tabla-datos">

            {{-- TIPO DE CLIENTE --}}
            <tr>
                <td class="etiqueta">
                    Tipo de cliente:
                </td>

                <td class="valor" colspan="3">
                    <span class="tipo-cliente">
                        {{ $cliente->tipoCliente->nombre ?? 'Sin información' }}
                    </span>
                </td>
            </tr>


            {{-- =====================================
             PERSONA NATURAL
        ====================================== --}}

            @if ($esPersona)
                <tr>
                    <td class="etiqueta">
                        Nombre:
                    </td>

                    <td class="valor">
                        {{ $cliente->nombres }}
                        {{ $cliente->apellidos }}
                    </td>

                    <td class="etiqueta">
                        RUT:
                    </td>

                    <td class="valor">
                        {{ $cliente->rut_persona ?? '-' }}
                    </td>
                </tr>


                {{-- =====================================
             ESTABLECIMIENTO EDUCACIONAL
        ====================================== --}}
            @elseif($esEstablecimiento)
                <tr>
                    <td class="etiqueta">
                        Establecimiento:
                    </td>

                    <td class="valor">
                        {{ $cliente->nombre_entidad ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT:
                    </td>

                    <td class="valor">
                        {{ $cliente->rut_entidad ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta">
                        Encargado:
                    </td>

                    <td class="valor">
                        {{ $cliente->encargado ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT encargado:
                    </td>

                    <td class="valor">
                        {{ $cliente->rut_encargado ?? '-' }}
                    </td>
                </tr>


                {{-- =====================================
             TOUR OPERADOR / AGENCIA
        ====================================== --}}
            @elseif($esTourOperador)
                <tr>
                    <td class="etiqueta">
                        Empresa:
                    </td>

                    <td class="valor">
                        {{ $tipoCliente->nombre_empresa ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT:
                    </td>

                    <td class="valor">
                        {{ $cliente->rut_empresa ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta">
                        Encargado:
                    </td>

                    <td class="valor">
                        {{ $cliente->encargado ?? '-' }}
                    </td>

                    <td class="etiqueta">
                        RUT encargado:
                    </td>

                    <td class="valor">
                        {{ $cliente->rut_encargado ?? '-' }}
                    </td>
                </tr>
            @endif


            {{-- =====================================
             DATOS DE CONTACTO
        ====================================== --}}

            <tr>
                <td class="etiqueta">
                    Correo:
                </td>

                <td class="valor">
                    {{ $cliente->email ?? '-' }}
                </td>

                <td class="etiqueta">
                    Teléfono:
                </td>

                <td class="valor">
                    {{ $cliente->telefono ?? '-' }}
                </td>
            </tr>


            {{-- REGIÓN Y COMUNA --}}
            <tr>
                <td class="etiqueta">
                    Región:
                </td>

                <td class="valor">
                    {{ $cliente->region->nombre ?? '-' }}
                </td>

                <td class="etiqueta">
                    Comuna:
                </td>

                <td class="valor">
                    {{ $cliente->comuna->nombre ?? '-' }}
                </td>
            </tr>

        </table>

    </div>

</body>

</html>
