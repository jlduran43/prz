$(document).ready(function () {

    // ==========================================================
    // ELEMENTO CALENDARIO
    // ==========================================================

    const calendarEl = document.getElementById('calendarioHorarios');

    if (!calendarEl) {
        return;
    }


    // ==========================================================
    // CONFIGURACIÓN ENTREGADA DESDE LARAVEL
    // ==========================================================

    const eventosUrl = calendarEl.dataset.eventosUrl;
    const indexUrl = calendarEl.dataset.indexUrl;
    const baseUrl = calendarEl.dataset.baseUrl;
    const fechaSeleccionada = calendarEl.dataset.fecha || '';
    const estadoActual = calendarEl.dataset.estado || '';


    // ==========================================================
    // HELPER ESTADO ACTIVO
    // ==========================================================

    function estaActivo(valor) {

        return (
            valor == 1 ||
            valor === true ||
            valor === '1'
        );
    }


    // ==========================================================
    // CONFIGURACIÓN FULLCALENDAR
    // ==========================================================

    const calendarOptions = {

        locale: 'es',

        initialView: 'dayGridMonth',

        firstDay: 1,

        height: 'auto',

        navLinks: true,

        nowIndicator: true,

        dayMaxEvents: true,

        displayEventTime: false,


        // ======================================================
        // CABECERA
        // ======================================================

        headerToolbar: {

            left: 'prev,next today',

            center: 'title',

            right: 'dayGridMonth,timeGridWeek,timeGridDay'

        },


        buttonText: {

            today: 'Hoy',

            month: 'Mes',

            week: 'Semana',

            day: 'Día'

        },


        // ======================================================
        // OBTENER HORARIOS DESDE LARAVEL
        // ======================================================

        events: eventosUrl,


        // ======================================================
        // MARCAR FECHA SELECCIONADA
        // ======================================================

        dayCellDidMount: function (info) {

            if (!fechaSeleccionada) {
                return;
            }

            const year =
                info.date.getFullYear();

            const month =
                String(
                    info.date.getMonth() + 1
                ).padStart(2, '0');

            const day =
                String(
                    info.date.getDate()
                ).padStart(2, '0');

            const fecha =
                `${year}-${month}-${day}`;

            if (fecha === fechaSeleccionada) {

                info.el.classList.add(
                    'dia-seleccionado'
                );

            }

        },


        // ======================================================
        // CLIC EN UNA FECHA
        // ======================================================

        dateClick: function (info) {

            const url =
                new URL(
                    indexUrl,
                    window.location.origin
                );

            url.searchParams.set(
                'fecha',
                info.dateStr
            );


            if (estadoActual !== '') {

                url.searchParams.set(
                    'estado',
                    estadoActual
                );

            }


            window.location.href =
                url.toString();

        },


        // ======================================================
        // CLIC EN UN HORARIO
        // ======================================================

        eventClick: function (info) {

            info.jsEvent.preventDefault();

            const evento =
                info.event;

            const propiedades =
                evento.extendedProps;


            // ==================================================
            // HORARIO
            // ==================================================

            $('#calendarHorario')
                .text(
                    evento.title
                );


            // ==================================================
            // ESTADO
            // ==================================================

            const activo =
                estaActivo(
                    propiedades.activo
                );

            $('#calendarEstado')
                .text(
                    activo
                        ? 'Activo'
                        : 'Inactivo'
                );


            // ==================================================
            // GOOGLE CALENDAR
            // ==================================================

            const google =
                propiedades.google ??
                'PENDIENTE';

            $('#calendarGoogle')
                .text(
                    google
                );


            // ==================================================
            // SERVICIOS
            // ==================================================

            const lista =
                $('#calendarServicios');

            lista.empty();


            const servicios =
                propiedades.servicios ?? [];


            if (servicios.length === 0) {

                lista.append(
                    '<li>Sin servicios asociados</li>'
                );

            } else {

                servicios.forEach(
                    function (servicio) {

                        $('<li>')
                            .text(servicio)
                            .appendTo(lista);

                    }
                );

            }


            // ==================================================
            // BOTÓN EDITAR
            // ==================================================

            $('#btnEditarHorarioCalendario')
                .attr(
                    'href',
                    baseUrl +
                    '/' +
                    evento.id +
                    '/edit'
                );


            // ==================================================
            // BOTONES ACTIVAR / DESACTIVAR
            // ==================================================

            const btnDesactivar =
                $('#btnDesactivarHorarioCalendario');

            const btnActivar =
                $('#btnActivarHorarioCalendario');


            btnDesactivar
                .attr(
                    'data-id',
                    evento.id
                )
                .attr(
                    'data-nombre',
                    evento.title
                );


            btnActivar
                .attr(
                    'data-id',
                    evento.id
                )
                .attr(
                    'data-nombre',
                    evento.title
                );


            if (activo) {

                btnDesactivar.show();

                btnActivar.hide();

            } else {

                btnDesactivar.hide();

                btnActivar.show();

            }


            // ==================================================
            // MOSTRAR MODAL
            // ==================================================

            $('#modalHorarioCalendario')
                .modal('show');

        },


        // ======================================================
        // APARIENCIA ACTIVO / INACTIVO
        // ======================================================

        eventDidMount: function (info) {

            const activo =
                estaActivo(
                    info.event
                        .extendedProps
                        .activo
                );


            if (!activo) {

                info.el.style.opacity =
                    '0.55';

                info.el.style
                    .textDecoration =
                    'line-through';

            }

        }

    };


    // ==========================================================
    // FECHA INICIAL
    // ==========================================================

    if (fechaSeleccionada) {

        calendarOptions.initialDate =
            fechaSeleccionada;

    }


    // ==========================================================
    // CREAR CALENDARIO
    // ==========================================================

    const calendar =
        new FullCalendar.Calendar(
            calendarEl,
            calendarOptions
        );


    // ==========================================================
    // DESACTIVAR HORARIO
    // ==========================================================

    $('#btnDesactivarHorarioCalendario')
        .on('click', function () {

            const id =
                $(this)
                    .attr('data-id');

            const nombre =
                $(this)
                    .attr('data-nombre');


            if (!id) {

                console.error(
                    'No se encontró el ID del horario.'
                );

                return;

            }


            // Nombre horario

            $('#nombreHorarioDesactivar')
                .text(nombre);


            // Action formulario

            $('#formDesactivar')
                .attr(
                    'action',
                    baseUrl +
                    '/' +
                    id
                );


            // Cerrar detalle

            $('#modalHorarioCalendario')
                .modal('hide');


            // Abrir modal confirmación

            $('#modalHorarioCalendario')
                .one(
                    'hidden.bs.modal',
                    function () {

                        $('#modalDesactivar')
                            .modal('show');

                    }
                );

        });


    // ==========================================================
    // REACTIVAR HORARIO
    // ==========================================================

    $('#btnActivarHorarioCalendario')
        .on('click', function () {

            const id =
                $(this)
                    .attr('data-id');

            const nombre =
                $(this)
                    .attr('data-nombre');


            if (!id) {

                console.error(
                    'No se encontró el ID del horario.'
                );

                return;

            }


            // Nombre horario

            $('#nombreHorarioActivar')
                .text(nombre);


            // Action formulario

            $('#formActivar')
                .attr(
                    'action',
                    baseUrl +
                    '/' +
                    id +
                    '/activar'
                );


            // Cerrar detalle

            $('#modalHorarioCalendario')
                .modal('hide');


            // Abrir modal confirmación

            $('#modalHorarioCalendario')
                .one(
                    'hidden.bs.modal',
                    function () {

                        $('#modalActivar')
                            .modal('show');

                    }
                );

        });


    // ==========================================================
    // RENDERIZAR CALENDARIO
    // ==========================================================

    calendar.render();

});