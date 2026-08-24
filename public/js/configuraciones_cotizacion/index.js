$(document).ready(function () {

    // ==========================================
    // DESACTIVAR CONFIGURACIÓN
    // ==========================================
    $('#modalDesactivarConfiguracion').on('show.bs.modal', function (event) {

        const boton = $(event.relatedTarget);

        const id = boton.data('id');
        const titulo = boton.data('titulo');

        const modal = $(this);

        modal
            .find('#tituloConfiguracionDesactivar')
            .text(titulo);

        modal
            .find('#formDesactivarConfiguracion')
            .attr(
                'action',
                '/configuraciones-cotizacion/' + id
            );
    });


    // ==========================================
    // ACTIVAR CONFIGURACIÓN
    // ==========================================
    $('#modalActivarConfiguracion').on('show.bs.modal', function (event) {

        const boton = $(event.relatedTarget);

        const id = boton.data('id');
        const titulo = boton.data('titulo');

        const modal = $(this);

        modal
            .find('#tituloConfiguracionActivar')
            .text(titulo);

        modal
            .find('#formActivarConfiguracion')
            .attr(
                'action',
                '/configuraciones-cotizacion/' + id + '/activar'
            );
    });

});