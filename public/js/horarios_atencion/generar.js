$(document).ready(function () {

    const contenedor =
        document.getElementById(
            'contenedorFranjas'
        );

    const template =
        document.getElementById(
            'templateFranja'
        );

    const btnAgregar =
        document.getElementById(
            'btnAgregarFranja'
        );

    const mensajeSinFranjas =
        document.getElementById(
            'mensajeSinFranjas'
        );


    let contadorFranjas = 0;


    /*
    |--------------------------------------------------------------------------
    | Agregar franja
    |--------------------------------------------------------------------------
    */

    function agregarFranja(
        valores = null
    ) {

        const indice = contadorFranjas++;

        const fragmento =
            template.content.cloneNode(true);

        const tarjeta =
            fragmento.querySelector(
                '.franja-card'
            );


        tarjeta.dataset.indice = indice;


        /*
        |--------------------------------------------------------------------------
        | Nombres de inputs
        |--------------------------------------------------------------------------
        */

        const inicio =
            tarjeta.querySelector(
                '.input-hora-inicio'
            );

        const termino =
            tarjeta.querySelector(
                '.input-hora-termino'
            );

        const capacidad =
            tarjeta.querySelector(
                '.input-capacidad'
            );


        inicio.name =
            `franjas[${indice}][hora_inicio]`;

        termino.name =
            `franjas[${indice}][hora_termino]`;

        capacidad.name =
            `franjas[${indice}][capacidad_maxima]`;


        /*
        |--------------------------------------------------------------------------
        | Servicios
        |--------------------------------------------------------------------------
        */

        tarjeta
            .querySelectorAll(
                '.checkbox-servicio'
            )
            .forEach(function (checkbox) {

                checkbox.name =
                    `franjas[${indice}][servicios][]`;
            });


        /*
        |--------------------------------------------------------------------------
        | Valores opcionales
        |--------------------------------------------------------------------------
        */

        if (valores) {

            inicio.value =
                valores.hora_inicio || '';

            termino.value =
                valores.hora_termino || '';

            capacidad.value =
                valores.capacidad_maxima || '';
        }


        contenedor.appendChild(
            fragmento
        );


        actualizarNumeracion();

        actualizarMensaje();
    }


    /*
    |--------------------------------------------------------------------------
    | Numeración visual
    |--------------------------------------------------------------------------
    */

    function actualizarNumeracion() {

        document
            .querySelectorAll(
                '.franja-card'
            )
            .forEach(
                function (
                    tarjeta,
                    posicion
                ) {

                    const titulo =
                        tarjeta.querySelector(
                            '.titulo-franja'
                        );

                    titulo.textContent =
                        'Franja '
                        + (posicion + 1);
                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Mensaje sin franjas
    |--------------------------------------------------------------------------
    */

    function actualizarMensaje() {

        const cantidad =
            document.querySelectorAll(
                '.franja-card'
            ).length;

        if (cantidad === 0) {

            mensajeSinFranjas.classList
                .remove('d-none');

        } else {

            mensajeSinFranjas.classList
                .add('d-none');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Botón agregar
    |--------------------------------------------------------------------------
    */

    btnAgregar.addEventListener(
        'click',
        function () {

            agregarFranja();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Eventos dinámicos
    |--------------------------------------------------------------------------
    */

    contenedor.addEventListener(
        'click',
        function (event) {

            const tarjeta =
                event.target.closest(
                    '.franja-card'
                );


            if (!tarjeta) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Eliminar
            |--------------------------------------------------------------------------
            */

            const btnEliminar =
                event.target.closest(
                    '.btnEliminarFranja'
                );

            if (btnEliminar) {

                tarjeta.remove();

                actualizarNumeracion();

                actualizarMensaje();

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Seleccionar todos
            |--------------------------------------------------------------------------
            */

            const seleccionarTodos =
                event.target.closest(
                    '.btnSeleccionarTodos'
                );

            if (seleccionarTodos) {

                tarjeta
                    .querySelectorAll(
                        '.servicio-check'
                    )
                    .forEach(function (item) {

                        if (
                            item.style.display
                            !== 'none'
                        ) {

                            const checkbox =
                                item.querySelector(
                                    '.checkbox-servicio'
                                );

                            checkbox.checked = true;
                        }
                    });

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Limpiar
            |--------------------------------------------------------------------------
            */

            const limpiar =
                event.target.closest(
                    '.btnLimpiarServicios'
                );

            if (limpiar) {

                tarjeta
                    .querySelectorAll(
                        '.checkbox-servicio'
                    )
                    .forEach(function (checkbox) {

                        checkbox.checked = false;
                    });
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Buscador de servicios
    |--------------------------------------------------------------------------
    */

    contenedor.addEventListener(
        'input',
        function (event) {

            if (
                !event.target.classList
                    .contains(
                        'buscador-servicios'
                    )
            ) {
                return;
            }


            const buscador =
                event.target;

            const tarjeta =
                buscador.closest(
                    '.franja-card'
                );

            const texto =
                buscador.value
                    .toLowerCase()
                    .trim();


            tarjeta
                .querySelectorAll(
                    '.servicio-check'
                )
                .forEach(function (servicio) {

                    const nombre =
                        servicio.dataset.nombre
                        || '';

                    servicio.style.display =
                        nombre.includes(texto)
                            ? ''
                            : 'none';
                });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Validación antes de enviar
    |--------------------------------------------------------------------------
    */

    $('#formGenerarHorarios')
        .on(
            'submit',
            function (event) {

                const dias =
                    document.querySelectorAll(
                        'input[name="dias_semana[]"]:checked'
                    );


                if (dias.length === 0) {

                    event.preventDefault();

                    alert(
                        'Selecciona al menos un día.'
                    );

                    return;
                }


                const franjas =
                    document.querySelectorAll(
                        '.franja-card'
                    );


                if (franjas.length === 0) {

                    event.preventDefault();

                    alert(
                        'Agrega al menos una franja horaria.'
                    );

                    return;
                }


                let valido = true;


                franjas.forEach(
                    function (tarjeta) {

                        const inicio =
                            tarjeta.querySelector(
                                '.input-hora-inicio'
                            ).value;

                        const termino =
                            tarjeta.querySelector(
                                '.input-hora-termino'
                            ).value;

                        const capacidad =
                            tarjeta.querySelector(
                                '.input-capacidad'
                            ).value;

                        const servicios =
                            tarjeta.querySelectorAll(
                                '.checkbox-servicio:checked'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Validar horas
                        |--------------------------------------------------------------------------
                        */

                        if (
                            inicio
                            &&
                            termino
                            &&
                            termino <= inicio
                        ) {

                            valido = false;

                            alert(
                                'La hora de término '
                                + 'debe ser posterior '
                                + 'a la hora de inicio.'
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Validar capacidad
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !capacidad
                            ||
                            parseInt(
                                capacidad,
                                10
                            ) <= 0
                        ) {

                            valido = false;

                            alert(
                                'Cada franja debe tener '
                                + 'una capacidad mayor a cero.'
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Validar servicios
                        |--------------------------------------------------------------------------
                        */

                        if (
                            servicios.length === 0
                        ) {

                            valido = false;

                            alert(
                                'Cada franja debe tener '
                                + 'al menos un servicio.'
                            );

                            return;
                        }

                    }
                );


                if (!valido) {

                    event.preventDefault();

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Validar franjas superpuestas
                |--------------------------------------------------------------------------
                */

                const rangos = [];


                franjas.forEach(
                    function (tarjeta) {

                        const inicio =
                            tarjeta.querySelector(
                                '.input-hora-inicio'
                            ).value;

                        const termino =
                            tarjeta.querySelector(
                                '.input-hora-termino'
                            ).value;


                        rangos.push({
                            inicio: inicio,
                            termino: termino
                        });

                    }
                );


                for (
                    let i = 0;
                    i < rangos.length;
                    i++
                ) {

                    for (
                        let j = i + 1;
                        j < rangos.length;
                        j++
                    ) {

                        const a = rangos[i];
                        const b = rangos[j];


                        const seSuperponen =
                            a.inicio < b.termino
                            &&
                            b.inicio < a.termino;


                        if (seSuperponen) {

                            event.preventDefault();

                            alert(
                                'Las franjas '
                                + a.inicio
                                + ' - '
                                + a.termino
                                + ' y '
                                + b.inicio
                                + ' - '
                                + b.termino
                                + ' se superponen.'
                            );

                            return;
                        }
                    }
                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Primera franja automática
    |--------------------------------------------------------------------------
    */

    agregarFranja();

});
