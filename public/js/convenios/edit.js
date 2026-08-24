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


        function prepararRut(
            input
        ) {

            input.addEventListener(
                'input',
                function () {

                    this.value =
                        formatearRut(
                            this.value
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
                        '.rut-entidad'
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