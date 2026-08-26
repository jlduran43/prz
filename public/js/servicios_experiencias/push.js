document.addEventListener('DOMContentLoaded', function () {

    const inputImagen = document.getElementById('imagen');
    const vistaPrevia = document.getElementById('vistaPreviaImagen');
    const contenedor = document.getElementById('contenedorVistaPrevia');

    if (!inputImagen) {
        return;
    }

    inputImagen.addEventListener('change', function () {

        const archivo = this.files[0];

        const label = this
            .closest('.custom-file')
            .querySelector('.custom-file-label');

        if (!archivo) {
            label.textContent = 'Seleccionar imagen...';

            vistaPrevia.src = '';
            contenedor.style.display = 'none';

            return;
        }

        // Mostrar nombre del archivo seleccionado
        label.textContent = archivo.name;

        // Mostrar vista previa
        const lector = new FileReader();

        lector.onload = function (e) {
            vistaPrevia.src = e.target.result;
            contenedor.style.display = 'block';
        };

        lector.readAsDataURL(archivo);
    });

});