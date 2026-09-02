$(document).ready(function () {

    const calendarEl =
        document.getElementById(
            'calendarioReservas'
        );


    if (!calendarEl) {
        return;
    }


    const eventosUrl =
        calendarEl.dataset.eventosUrl;

    const showUrl =
        calendarEl.dataset.showUrl;


    let calendario = null;


    /*
    |--------------------------------------------------------------------------
    | Formatear moneda
    |--------------------------------------------------------------------------
    */

    function formatearMoneda(valor) {

        return new Intl.NumberFormat(
            'es-CL',
            {
                style: 'currency',
                currency: 'CLP',
                maximumFractionDigits: 0
            }
        ).format(valor || 0);
    }


    /*
    |--------------------------------------------------------------------------
    | Formatear fecha
    |--------------------------------------------------------------------------
    */

    function formatearFecha(fecha) {

        if (!fecha) {
            return '-';
        }

        const partes =
            fecha.split('-');

        if (partes.length !== 3) {
            return fecha;
        }

        return (
            partes[2]
            + '/'
            + partes[1]
            + '/'
            + partes[0]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Estado
    |--------------------------------------------------------------------------
    */

    function badgeEstado(estado) {

        const configuracion = {

            PENDIENTE_PAGO: {
                clase: 'warning',
                texto: 'Pendiente de pago'
            },

            PAGADA: {
                clase: 'success',
                texto: 'Pagada'
            },

            CONFIRMADA: {
                clase: 'success',
                texto: 'Confirmada'
            },

            VENCIDA_PAGO: {
                clase: 'secondary',
                texto: 'Pago vencido'
            },

            CANCELADA: {
                clase: 'danger',
                texto: 'Cancelada'
            },

            RECHAZADA: {
                clase: 'danger',
                texto: 'Rechazada'
            }
        };


        const dato =
            configuracion[estado]
            || {
                clase: 'info',
                texto: estado || 'Sin estado'
            };


        return `
            <span class="badge badge-${dato.clase} p-2">
                ${dato.texto}
            </span>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | Crear calendario
    |--------------------------------------------------------------------------
    */

    calendario =
        new FullCalendar.Calendar(
            calendarEl,
            {

                locale: 'es',

                firstDay: 1,

                initialView:
                    'dayGridMonth',

                height: 'auto',

                navLinks: true,

                dayMaxEvents: true,

                nowIndicator: true,

                headerToolbar: {

                    left:
                        'prev,next today',

                    center:
                        'title',

                    right:
                        'dayGridMonth,timeGridWeek,timeGridDay'
                },


                buttonText: {

                    today:
                        'Hoy',

                    month:
                        'Mes',

                    week:
                        'Semana',

                    day:
                        'Día'
                },


                /*
                |--------------------------------------------------------------------------
                | Horario visible
                |--------------------------------------------------------------------------
                */

                slotMinTime:
                    '08:00:00',

                slotMaxTime:
                    '20:00:00',


                /*
                |--------------------------------------------------------------------------
                | Eventos
                |--------------------------------------------------------------------------
                */

                events: function (info, successCallback, failureCallback) {

                    const estado =
                        $('#estadoCalendario')
                            .val()
                        || '';


                    $.ajax({

                        url:
                            eventosUrl,

                        type:
                            'GET',

                        data: {

                            start:
                                info.startStr,

                            end:
                                info.endStr,

                            estado:
                                estado
                        },

                        success:
                            function (respuesta) {

                                successCallback(
                                    respuesta
                                );
                            },

                        error:
                            function (xhr) {

                                console.error(
                                    'Error cargando reservas',
                                    xhr
                                );

                                failureCallback(
                                    xhr
                                );
                            }
                    });
                },

                eventContent: function (arg) {

                    const datos =
                        arg.event.extendedProps;

                    const vista =
                        arg.view.type;

                    const contenedor =
                        document.createElement('div');


                    /*
                    |--------------------------------------------------------------------------
                    | Vista MES
                    |--------------------------------------------------------------------------
                    */

                    if (vista === 'dayGridMonth') {

                        const color =
                            arg.event.backgroundColor
                            || '#198754';

                        const folio =
                            datos.folio
                                ? datos.folio.replace(
                                    /^RES-0+/,
                                    'RES-'
                                )
                                : '';

                        contenedor.innerHTML = `
                            <span class="evento-reserva-mes">

                            <span
                                class="evento-reserva-punto"
                                style="background-color: ${color};">
                            </span>

                            <span>
                                ${arg.timeText} ${folio}
                            </span>
        </span>`;

                        return {
                            domNodes: [contenedor]
                        };
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Vista SEMANA / DÍA
                    |--------------------------------------------------------------------------
                    */

                    contenedor.innerHTML = `
        <div class="evento-reserva-detalle">

            <strong>
                ${datos.folio || ''}
            </strong>

            <div>
                ${datos.cliente || ''}
            </div>

            <div>
                <i class="fas fa-users"></i>
                ${datos.cantidad_asistentes || 0}
            </div>

        </div>
    `;

                    return {
                        domNodes: [contenedor]
                    };
                },


                /*
                |--------------------------------------------------------------------------
                | Click evento
                |--------------------------------------------------------------------------
                */

                eventClick:
                    function (info) {

                        const evento =
                            info.event;

                        const datos =
                            evento.extendedProps;


                        $('#calReservaFolio')
                            .text(
                                datos.folio
                                || '-'
                            );


                        $('#calReservaEstado')
                            .html(
                                badgeEstado(
                                    datos.estado
                                )
                            );


                        $('#calReservaCliente')
                            .text(
                                datos.cliente
                                || '-'
                            );


                        $('#calReservaRut')
                            .text(
                                datos.rut
                                || '-'
                            );


                        $('#calReservaEmail')
                            .text(
                                datos.email
                                || '-'
                            );


                        $('#calReservaAsistentes')
                            .text(
                                datos.cantidad_asistentes
                                || 0
                            );


                        $('#calReservaFecha')
                            .text(
                                formatearFecha(
                                    datos.fecha
                                )
                            );


                        $('#calReservaHorario')
                            .text(
                                (
                                    datos.hora_inicio
                                    || '--:--'
                                )
                                + ' - '
                                + (
                                    datos.hora_termino
                                    || '--:--'
                                )
                            );


                        $('#calReservaMedioPago')
                            .text(
                                datos.medio_pago
                                || '-'
                            );


                        $('#calReservaTotal')
                            .text(
                                formatearMoneda(
                                    datos.total
                                )
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Servicios
                        |--------------------------------------------------------------------------
                        */

                        const servicios =
                            datos.servicios
                            || [];


                        if (servicios.length) {

                            const html =
                                servicios
                                    .map(
                                        servicio => `
                                            <span
                                                class="
                                                    badge
                                                    badge-light
                                                    border
                                                    mr-1
                                                    mb-1
                                                    p-2
                                                "
                                            >
                                                ${servicio}
                                            </span>
                                        `
                                    )
                                    .join('');


                            $('#calReservaServicios')
                                .html(html);

                        } else {

                            $('#calReservaServicios')
                                .text('-');
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Ver reserva
                        |--------------------------------------------------------------------------
                        */

                        $('#btnVerReservaCalendario')
                            .attr(
                                'href',
                                showUrl
                                + '/'
                                + evento.id
                            );


                        $('#modalReservaCalendario')
                            .modal('show');
                    },


                /*
                |--------------------------------------------------------------------------
                | Mostrar hora dentro del evento
                |--------------------------------------------------------------------------
                */

                eventTimeFormat: {

                    hour:
                        '2-digit',

                    minute:
                        '2-digit',

                    hour12:
                        false
                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    $('#btnVistaTabla')
        .on(
            'click',
            function () {

                $('#vistaCalendario')
                    .addClass('d-none');

                $('#vistaTabla')
                    .removeClass('d-none');

                $('#buscadorReservas')
                    .removeClass('d-none');

                $('#btnVistaCalendario')
                    .removeClass(
                        'btn-success active'
                    )
                    .addClass(
                        'btn-outline-success'
                    );

                $('#btnVistaTabla')
                    .removeClass(
                        'btn-outline-success'
                    )
                    .addClass(
                        'btn-success active'
                    );
            }
        );


    /*
    |--------------------------------------------------------------------------
    | Calendario
    |--------------------------------------------------------------------------
    */

    $('#btnVistaCalendario')
        .on(
            'click',
            function () {

                $('#vistaTabla')
                    .addClass('d-none');

                $('#vistaCalendario')
                    .removeClass('d-none');

                $('#buscadorReservas')
                    .addClass('d-none');

                $('#btnVistaTabla')
                    .removeClass(
                        'btn-success active'
                    )
                    .addClass(
                        'btn-outline-success'
                    );


                $('#btnVistaCalendario')
                    .removeClass(
                        'btn-outline-success'
                    )
                    .addClass(
                        'btn-success active'
                    );


                /*
                 * FullCalendar necesita renderizar
                 * después de que el div sea visible.
                 */

                if (!calendario.el) {

                    calendario.render();

                } else {

                    calendario.updateSize();
                }

            }
        );



    /*
    |--------------------------------------------------------------------------
    | Filtro estado calendario
    |--------------------------------------------------------------------------
    */

    $('#estadoCalendario')
        .on(
            'change',
            function () {

                calendario.refetchEvents();
            }
        );


    /*
    |--------------------------------------------------------------------------
    | Render inicial
    |--------------------------------------------------------------------------
    */

    calendario.render();

});