document.addEventListener('DOMContentLoaded', function () {
    const tipoCliente =
        document.getElementById('tipo_cliente_id');

    const camposPersona =
        document.getElementById('campos-persona');

    const camposEntidad =
        document.getElementById('campos-entidad');

    const tituloDatosEntidad =
        document.getElementById('titulo-datos-entidad');

    const labelNombreEntidad =
        document.getElementById('label-nombre-entidad');

    const labelRutEntidad =
        document.getElementById('label-rut-entidad');

    const nombreEntidad =
        document.getElementById('nombre_entidad');

    const regionSelect =
        document.getElementById('region_id');

    const comunaSelect =
        document.getElementById('comuna_id');

    const rutEncargado =
        document.getElementById('rut_encargado');

    const camposPersonaRequeridos = [
        document.getElementById('nombres'),
        document.getElementById('apellidos'),
        document.getElementById('rut_persona'),
    ];

    const camposEntidadRequeridos = [
        document.getElementById('nombre_entidad'),
        document.getElementById('rut_entidad'),
        document.getElementById('nombre_encargado'),
    ];

    const camposRut =
        document.querySelectorAll('.rut-chileno');

    function limpiarRut(valor) {
        return String(valor ?? '')
            .replace(/[^0-9kK]/g, '')
            .toUpperCase();
    }

    function formatearRut(valor) {
        const rutLimpio = limpiarRut(valor);

        if (rutLimpio.length <= 1) {
            return rutLimpio;
        }

        const cuerpo = rutLimpio.slice(0, -1);
        const dv = rutLimpio.slice(-1);

        const cuerpoFormateado = cuerpo.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            '.'
        );

        return `${cuerpoFormateado}-${dv}`;
    }

    function validarRut(valor) {
        const rutLimpio = limpiarRut(valor);

        if (rutLimpio.length < 2) {
            return false;
        }

        const cuerpo = rutLimpio.slice(0, -1);
        const dvIngresado = rutLimpio.slice(-1);

        if (!/^\d+$/.test(cuerpo)) {
            return false;
        }

        if (/^(\d)\1+$/.test(cuerpo)) {
            return false;
        }

        let suma = 0;
        let multiplicador = 2;

        for (let i = cuerpo.length - 1; i >= 0; i--) {
            suma += Number(cuerpo[i]) * multiplicador;

            multiplicador++;

            if (multiplicador > 7) {
                multiplicador = 2;
            }
        }

        const resto = 11 - (suma % 11);

        let dvCalculado;

        if (resto === 11) {
            dvCalculado = '0';
        } else if (resto === 10) {
            dvCalculado = 'K';
        } else {
            dvCalculado = String(resto);
        }

        return dvIngresado === dvCalculado;
    }

    function actualizarEstadoRut(input) {
        const grupo =
            input.closest('.rut-input-group');

        const estado =
            grupo?.querySelector('.rut-estado');

        const mensaje =
            grupo
                ?.closest('.form-group')
                ?.querySelector('.rut-mensaje');

        if (!grupo || !estado) {
            return;
        }

        grupo.classList.remove(
            'rut-valido',
            'rut-invalido'
        );

        if (mensaje) {
            mensaje.classList.remove(
                'valido',
                'invalido'
            );
        }

        const valorLimpio =
            limpiarRut(input.value);

        if (!valorLimpio) {
            estado.innerHTML =
                '<i class="fas fa-minus text-muted"></i>';

            if (mensaje) {
                mensaje.textContent =
                    'Ingresa el RUT con o sin puntos.';
            }

            return;
        }

        if (validarRut(input.value)) {
            grupo.classList.add('rut-valido');

            estado.innerHTML =
                '<i class="fas fa-check text-success"></i>';

            if (mensaje) {
                mensaje.textContent = 'RUT válido.';
                mensaje.classList.add('valido');
            }

            return;
        }

        grupo.classList.add('rut-invalido');

        estado.innerHTML =
            '<i class="fas fa-times text-danger"></i>';

        if (mensaje) {
            mensaje.textContent =
                'El RUT ingresado no es válido.';

            mensaje.classList.add('invalido');
        }
    }

    function limpiarEstadoRut(input) {
        if (!input) {
            return;
        }

        input.value = '';

        const grupo =
            input.closest('.rut-input-group');

        const estado =
            grupo?.querySelector('.rut-estado');

        const mensaje =
            grupo
                ?.closest('.form-group')
                ?.querySelector('.rut-mensaje');

        grupo?.classList.remove(
            'rut-valido',
            'rut-invalido'
        );

        if (estado) {
            estado.innerHTML =
                '<i class="fas fa-minus text-muted"></i>';
        }

        if (mensaje) {
            mensaje.textContent =
                'Ingresa el RUT con o sin puntos.';

            mensaje.classList.remove(
                'valido',
                'invalido'
            );
        }
    }

    camposRut.forEach(function (input) {
        input.addEventListener('input', function () {
            input.value =
                formatearRut(input.value);

            actualizarEstadoRut(input);
        });

        input.addEventListener('blur', function () {
            input.value =
                formatearRut(input.value);

            actualizarEstadoRut(input);
        });

        if (input.value) {
            input.value =
                formatearRut(input.value);

            actualizarEstadoRut(input);
        }
    });

    function actualizarCamposCliente() {
        const opcionSeleccionada =
            tipoCliente.options[
            tipoCliente.selectedIndex
            ];

        const codigo =
            opcionSeleccionada?.dataset.codigo ?? '';

        const estructura =
            opcionSeleccionada?.dataset.estructura ?? '';

        const esPersona =
            estructura === 'PERSONA';

        const esEstablecimiento =
            estructura === 'ESTABLECIMIENTO';

        const esOrganizacion =
            estructura === 'ORGANIZACION';

        const usaDatosEntidad =
            esEstablecimiento || esOrganizacion;

        camposPersona.hidden = !esPersona;
        camposEntidad.hidden = !usaDatosEntidad;

        camposPersonaRequeridos.forEach(
            function (campo) {
                campo.required = esPersona;
                campo.disabled = !esPersona;
            }
        );

        camposEntidadRequeridos.forEach(
            function (campo) {
                campo.required = usaDatosEntidad;
                campo.disabled = !usaDatosEntidad;
            }
        );

        rutEncargado.required = false;
        rutEncargado.disabled = !usaDatosEntidad;

        if (!esPersona) {
            limpiarEstadoRut(
                document.getElementById(
                    'rut_persona'
                )
            );
        }

        if (!usaDatosEntidad) {
            limpiarEstadoRut(
                document.getElementById(
                    'rut_entidad'
                )
            );

            limpiarEstadoRut(rutEncargado);
        }

        if (esEstablecimiento) {
            tituloDatosEntidad.textContent =
                'Datos del establecimiento educacional';

            labelNombreEntidad.innerHTML =
                'Nombre del establecimiento educacional ' +
                '<span class="text-danger">*</span>';

            labelRutEntidad.innerHTML =
                'RUT del establecimiento educacional ' +
                '<span class="text-danger">*</span>';

            nombreEntidad.placeholder =
                'Ej.: Escuela República de Chile';

            return;
        }

        if (codigo === 'TOUR_OPERADOR_AGENCIA_VIAJES') {
            tituloDatosEntidad.textContent =
                'Datos del tour operador o agencia de viajes';

            labelNombreEntidad.innerHTML =
                'Nombre del tour operador o agencia de viajes ' +
                '<span class="text-danger">*</span>';

            labelRutEntidad.innerHTML =
                'RUT del tour operador o agencia de viajes ' +
                '<span class="text-danger">*</span>';

            nombreEntidad.placeholder =
                'Ej.: Turismo Patagonia SpA';

            return;
        }

        if (codigo === 'GRUPO_ADULTOS_MAYORES') {
            tituloDatosEntidad.textContent =
                'Datos del grupo de adultos mayores';

            labelNombreEntidad.innerHTML =
                'Nombre de la organización o agrupación ' +
                '<span class="text-danger">*</span>';

            labelRutEntidad.innerHTML =
                'RUT de la organización o agrupación ' +
                '<span class="text-danger">*</span>';

            nombreEntidad.placeholder =
                'Ej.: Club Adulto Mayor Los Alerces';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ORGANIZACIÓN GENÉRICA
        |--------------------------------------------------------------------------
        */

        if (esOrganizacion) {
            tituloDatosEntidad.textContent =
                'Datos de la organización';

            labelNombreEntidad.innerHTML =
                'Nombre de la organización ' +
                '<span class="text-danger">*</span>';

            labelRutEntidad.innerHTML =
                'RUT de la organización ' +
                '<span class="text-danger">*</span>';

            nombreEntidad.placeholder =
                'Ingrese el nombre';
        }
    }

    async function cargarComunas() {
        const regionId = regionSelect.value;

        const comunaSeleccionada =
            comunaSelect.dataset.selected;

        if (!regionId) {
            comunaSelect.innerHTML =
                '<option value="">' +
                'Seleccione una región primero' +
                '</option>';

            comunaSelect.disabled = true;
            return;
        }

        comunaSelect.disabled = true;

        comunaSelect.innerHTML =
            '<option value="">' +
            'Cargando comunas...' +
            '</option>';

        try {
            const url =
                `/reservas/comunas-por-region/${regionId}`;

            const respuesta = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!respuesta.ok) {
                throw new Error(
                    'No fue posible cargar las comunas.'
                );
            }

            const comunas =
                await respuesta.json();

            comunaSelect.innerHTML =
                '<option value="">' +
                'Seleccione una comuna' +
                '</option>';

            comunas.forEach(function (comuna) {
                const option =
                    document.createElement('option');

                option.value = comuna.id;
                option.textContent = comuna.nombre;

                option.selected =
                    String(comuna.id) ===
                    String(comunaSeleccionada);

                comunaSelect.appendChild(option);
            });

            comunaSelect.disabled = false;
        } catch (error) {
            console.error(error);

            comunaSelect.innerHTML =
                '<option value="">' +
                'Error al cargar comunas' +
                '</option>';

            comunaSelect.disabled = true;
        }
    }

    tipoCliente.addEventListener(
        'change',
        actualizarCamposCliente
    );

    regionSelect.addEventListener(
        'change',
        function () {
            comunaSelect.dataset.selected = '';
            cargarComunas();
        }
    );

    actualizarCamposCliente();

    if (regionSelect.value) {
        cargarComunas();
    } else {
        comunaSelect.disabled = true;
    }
});