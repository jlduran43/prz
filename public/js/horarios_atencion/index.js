$(document).ready(function () {

    // ==========================================
    // DESACTIVAR HORARIO
    // ==========================================
    $('#modalDesactivar').on('show.bs.modal', function (event) {

        const boton = $(event.relatedTarget);
        const id = boton.data('id');

        const modal = $(this);

        modal
            .find('#formDesactivar')
            .attr(
                'action',
                '/horarios-disponibles/' + id
            );
    });


    // ==========================================
    // ACTIVAR / REACTIVAR HORARIO
    // ==========================================
    $('#modalActivar').on('show.bs.modal', function (event) {

        const boton = $(event.relatedTarget);
        const id = boton.data('id');

        const modal = $(this);

        modal
            .find('#formActivar')
            .attr(
                'action',
                '/horarios-disponibles/' + id + '/activar'
            );
    });

});