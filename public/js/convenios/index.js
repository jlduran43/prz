$(document).ready(function () {

    // ==========================================
    // DESACTIVAR CONVENIO
    // ==========================================
    $('#modalDesactivarConvenio').on('show.bs.modal', function (event) {

        const boton = $(event.relatedTarget);

        const id = boton.data('id');
        const nombre = boton.data('nombre');

        const modal = $(this);

        modal
            .find('#nombreConvenioDesactivar')
            .text(nombre);

        modal
            .find('#formDesactivarConvenio')
            .attr(
                'action',
                '/convenios/' + id
            );
    });


    // ==========================================
    // ACTIVAR / REACTIVAR CONVENIO
    // ==========================================
    $('#modalActivarConvenio').on('show.bs.modal', function (event) {

        const boton = $(event.relatedTarget);

        const id = boton.data('id');
        const nombre = boton.data('nombre');

        const modal = $(this);

        modal
            .find('#nombreConvenioActivar')
            .text(nombre);

        modal
            .find('#formActivarConvenio')
            .attr(
                'action',
                '/convenios/' + id + '/activar'
            );
    });

});