document.addEventListener(
    'DOMContentLoaded',
    function () {

        const contenedor =
            document.getElementById(
                'contenedorEntidades'
            );

        const btnAgregar =
            document.getElementById(
                'btnAgregarEntidad'
            );


        /*
         * Índice siguiente.
         */
        let indiceEntidad =
            contenedor
                .querySelectorAll(
                    '.entidad-item'
                )
                .length;


        /*
         * Normalizar código
         */
        const codigoInput =
            document.getElementById(
                'codigo'
            );

        codigoInput.addEventListener(
            'input',
            function () {

                this.value =
                    this.value
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9_-]/g,
                            ''
                        );
            }
        );


        /*
         * Formatear RUT
         */
        function limpiarRut(valor) {

            return String(
                valor ?? ''
            )
                .replace(
                    /[^0-9kK]/g,
                    ''
                )
                .toUpperCase();
        }


        function formatearRut(valor) {

            const limpio =
                limpiarRut(valor);

            if (
                limpio.length <= 1
            ) {
                return limpio;
            }


            const cuerpo =
                limpio.slice(
                    0,
                    -1
                );

            const dv =
                limpio.slice(-1);


            const cuerpoFormateado =
                cuerpo.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    '.'
                );


            return (
                cuerpoFormateado +
                '-' +
                dv
            );
        }

        function validarRut(rut) {

            const limpio = limpiarRut(rut);

            if (limpio.length < 2) {
                return false;
            }

            const cuerpo = limpio.slice(0, -1);
            const dvIngresado = limpio.slice(-1);

            if (!/^\d+$/.test(cuerpo)) {
                return false;
            }

            let suma = 0;
            let multiplicador = 2;

            for (let i = cuerpo.length - 1; i >= 0; i--) {

                suma +=
                    parseInt(cuerpo.charAt(i), 10) *
                    multiplicador;

                multiplicador++;

                if (multiplicador === 8) {
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


        function prepararRut(input) {

            input.addEventListener(
                'input',
                function () {

                    this.value =
                        formatearRut(this.value);

                    // Mientras escribe quitamos
                    // el estado anterior.
                    this.classList.remove(
                        'is-valid',
                        'is-invalid'
                    );

                    const feedback =
                        this.parentElement.querySelector(
                            '.rut-feedback'
                        );

                    if (feedback) {
                        feedback.remove();
                    }
                }
            );


            input.addEventListener(
                'blur',
                function () {

                    const rut = this.value.trim();

                    const feedbackAnterior =
                        this.parentElement.querySelector(
                            '.rut-feedback'
                        );

                    if (feedbackAnterior) {
                        feedbackAnterior.remove();
                    }


                    if (rut === '') {
                        return;
                    }


                    const feedback =
                        document.createElement('div');

                    feedback.classList.add(
                        'rut-feedback'
                    );


                    if (validarRut(rut)) {

                        this.classList.remove(
                            'is-invalid'
                        );

                        this.classList.add(
                            'is-valid'
                        );

                        feedback.classList.add(
                            'valid-feedback'
                        );

                        feedback.textContent =
                            'RUT válido';

                    } else {

                        this.classList.remove(
                            'is-valid'
                        );

                        this.classList.add(
                            'is-invalid'
                        );

                        feedback.classList.add(
                            'invalid-feedback'
                        );

                        feedback.textContent =
                            'El RUT ingresado no es válido.';
                    }


                    this.parentElement.appendChild(
                        feedback
                    );
                }
            );
        }


        contenedor
            .querySelectorAll(
                '.rut-entidad'
            )
            .forEach(
                prepararRut
            );


        /*
         * Agregar nueva entidad
         */
        btnAgregar.addEventListener(
            'click',
            function () {

                const div =
                    document.createElement(
                        'div'
                    );


                div.className =
                    'entidad-item border rounded p-3 mb-3 bg-light';


                div.innerHTML = `

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group mb-md-0">

                                        <label>

                                            Nombre entidad

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>

                                        <input
                                            type="text"
                                            name="entidades[${indiceEntidad}][nombre_entidad]"
                                            class="form-control"
                                            maxlength="150"
                                            placeholder="Ej.: Colegio Patagonia"
                                            required
                                        >

                                    </div>

                                </div>


                                <div class="col-md-5">

                                    <div class="form-group mb-md-0">

                                        <label>

                                            RUT entidad

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>

                                        <input
                                            type="text"
                                            name="entidades[${indiceEntidad}][rut_entidad]"
                                            class="form-control rut-entidad"
                                            maxlength="12"
                                            placeholder="76.123.456-7"
                                            required
                                        >

                                    </div>

                                </div>


                                <div
                                    class="
                                        col-md-1
                                        d-flex
                                        align-items-end
                                    "
                                >

                                    <button
                                        type="button"
                                        class="
                                            btn
                                            btn-outline-danger
                                            btnEliminarEntidad
                                            btn-block
                                        "
                                        title="Eliminar entidad"
                                    >

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </div>

                            </div>
                        `;


                contenedor.appendChild(
                    div
                );


                prepararRut(
                    div.querySelector(
                        '.rut-chileno'
                    )
                );


                indiceEntidad++;
            }
        );


        /*
         * Eliminar entidad
         */
        contenedor.addEventListener(
            'click',
            function (event) {

                const boton =
                    event.target.closest(
                        '.btnEliminarEntidad'
                    );


                if (!boton) {
                    return;
                }


                const total =
                    contenedor
                        .querySelectorAll(
                            '.entidad-item'
                        )
                        .length;


                if (total <= 1) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Entidad requerida',
                        text: 'El convenio debe tener al menos una entidad autorizada.',
                    });

                    return;
                }


                boton
                    .closest(
                        '.entidad-item'
                    )
                    .remove();
            }
        );

    }
);

function limpiarRut(valor) {

    return String(valor ?? '')
        .replace(/[^0-9kK]/g, '')
        .toUpperCase();
}


function formatearRut(valor) {

    const rutLimpio =
        limpiarRut(valor);

    if (rutLimpio.length <= 1) {
        return rutLimpio;
    }

    const cuerpo =
        rutLimpio.slice(0, -1);

    const dv =
        rutLimpio.slice(-1);

    const cuerpoFormateado =
        cuerpo.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            '.'
        );

    return `${cuerpoFormateado}-${dv}`;
}


function validarRut(valor) {

    const rutLimpio =
        limpiarRut(valor);

    if (rutLimpio.length < 2) {
        return false;
    }

    const cuerpo =
        rutLimpio.slice(0, -1);

    const dvIngresado =
        rutLimpio.slice(-1);

    if (!/^\d+$/.test(cuerpo)) {
        return false;
    }

    /*
     * Mismo criterio usado actualmente
     * en el wizard de reservas.
     */
    if (/^(\d)\1+$/.test(cuerpo)) {
        return false;
    }

    let suma = 0;
    let multiplicador = 2;

    for (
        let i = cuerpo.length - 1; i >= 0; i--
    ) {

        suma +=
            Number(cuerpo[i]) *
            multiplicador;

        multiplicador++;

        if (multiplicador > 7) {
            multiplicador = 2;
        }
    }

    const resto =
        11 - (suma % 11);

    let dvCalculado;

    if (resto === 11) {

        dvCalculado = '0';

    } else if (resto === 10) {

        dvCalculado = 'K';

    } else {

        dvCalculado =
            String(resto);
    }

    return (
        dvIngresado ===
        dvCalculado
    );
}


function actualizarEstadoRut(input) {

    const grupo =
        input.closest(
            '.rut-input-group'
        );

    const estado =
        grupo?.querySelector(
            '.rut-estado'
        );

    const mensaje =
        grupo
            ?.closest(
                '.form-group'
            )
            ?.querySelector(
                '.rut-mensaje'
            );

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

        grupo.classList.add(
            'rut-valido'
        );

        estado.innerHTML =
            '<i class="fas fa-check text-success"></i>';

        if (mensaje) {

            mensaje.textContent =
                'RUT válido.';

            mensaje.classList.add(
                'valido'
            );
        }

        return;
    }

    grupo.classList.add(
        'rut-invalido'
    );

    estado.innerHTML =
        '<i class="fas fa-times text-danger"></i>';

    if (mensaje) {

        mensaje.textContent =
            'El RUT ingresado no es válido.';

        mensaje.classList.add(
            'invalido'
        );
    }
}

function prepararRut(input) {

    input.addEventListener(
        'input',
        function () {

            input.value =
                formatearRut(
                    input.value
                );

            actualizarEstadoRut(
                input
            );
        }
    );

    input.addEventListener(
        'blur',
        function () {

            input.value =
                formatearRut(
                    input.value
                );

            actualizarEstadoRut(
                input
            );
        }
    );

    if (input.value) {

        input.value =
            formatearRut(
                input.value
            );

        actualizarEstadoRut(
            input
        );
    }
}

document
    .querySelectorAll(
        '.rut-chileno'
    )
    .forEach(
        prepararRut
    );