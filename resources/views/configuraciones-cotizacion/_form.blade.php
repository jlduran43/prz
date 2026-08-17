<div class="row">

    <div class="col-md-8">

        <div class="form-group">
            <label>
                <i class="fas fa-heading mr-1 text-primary"></i>
                Título
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="titulo"
                class="form-control @error('titulo') is-invalid @enderror"
                value="{{ old(
                    'titulo',
                    $configuracion->titulo ?? 'Condiciones de la reserva'
                ) }}"
            >

            @error('titulo')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

    </div>


    <div class="col-md-4">

        <div class="form-group">

            <label>
                <i class="fas fa-calendar-alt mr-1 text-primary"></i>
                Validez de cotización
                <span class="text-danger">*</span>
            </label>

            <div class="input-group">

                <input
                    type="number"
                    name="dias_validez"
                    min="1"
                    max="365"
                    class="form-control
                        @error('dias_validez')
                            is-invalid
                        @enderror"
                    value="{{ old(
                        'dias_validez',
                        $configuracion->dias_validez ?? 30
                    ) }}"
                >

                <div class="input-group-append">
                    <span class="input-group-text">
                        días
                    </span>
                </div>

                @error('dias_validez')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

    </div>

</div>


<hr>

<h5 class="mb-3">
    <i class="fas fa-route mr-1 text-info"></i>
    Sobre el tour y visita
</h5>

<div class="form-group">

    <textarea
        name="descripcion_tour"
        rows="4"
        class="form-control"
        placeholder="Ingrese las condiciones generales del tour o visita..."
    >{{ old(
        'descripcion_tour',
        $configuracion->descripcion_tour ?? ''
    ) }}</textarea>

</div>


<hr>

<h5 class="mb-3">
    <i class="fas fa-credit-card mr-1 text-success"></i>
    Condiciones de pago
</h5>

<div class="form-group">

    <textarea
        name="condiciones_pago"
        rows="5"
        class="form-control"
        placeholder="Ingrese las formas de pago y condiciones..."
    >{{ old(
        'condiciones_pago',
        $configuracion->condiciones_pago ?? ''
    ) }}</textarea>

</div>


<div class="card card-outline card-success">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-university mr-1"></i>
            Datos para transferencia
        </h3>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <div class="form-group">
                    <label>Titular de la cuenta</label>

                    <input
                        type="text"
                        name="titular_cuenta"
                        class="form-control"
                        value="{{ old(
                            'titular_cuenta',
                            $configuracion->titular_cuenta ?? ''
                        ) }}"
                    >
                </div>

            </div>


            <div class="col-md-3">

                <div class="form-group">
                    <label>RUT titular</label>

                    <input
                        type="text"
                        name="rut_titular"
                        class="form-control"
                        value="{{ old(
                            'rut_titular',
                            $configuracion->rut_titular ?? ''
                        ) }}"
                    >
                </div>

            </div>


            <div class="col-md-3">

                <div class="form-group">
                    <label>Banco</label>

                    <input
                        type="text"
                        name="banco"
                        class="form-control"
                        value="{{ old(
                            'banco',
                            $configuracion->banco ?? ''
                        ) }}"
                    >
                </div>

            </div>

        </div>


        <div class="row">

            <div class="col-md-4">

                <div class="form-group">
                    <label>Tipo de cuenta</label>

                    <input
                        type="text"
                        name="tipo_cuenta"
                        class="form-control"
                        placeholder="Ej: Cuenta Corriente"
                        value="{{ old(
                            'tipo_cuenta',
                            $configuracion->tipo_cuenta ?? ''
                        ) }}"
                    >
                </div>

            </div>


            <div class="col-md-4">

                <div class="form-group">
                    <label>Número de cuenta</label>

                    <input
                        type="text"
                        name="numero_cuenta"
                        class="form-control"
                        value="{{ old(
                            'numero_cuenta',
                            $configuracion->numero_cuenta ?? ''
                        ) }}"
                    >
                </div>

            </div>


            <div class="col-md-4">

                <div class="form-group">
                    <label>
                        Correo para comprobantes
                    </label>

                    <input
                        type="email"
                        name="correo_comprobantes"
                        class="form-control"
                        value="{{ old(
                            'correo_comprobantes',
                            $configuracion->correo_comprobantes ?? ''
                        ) }}"
                    >
                </div>

            </div>

        </div>

    </div>

</div>


<hr>

<div class="form-group">

    <label>
        <i class="fas fa-undo-alt mr-1 text-warning"></i>
        Política de devoluciones
    </label>

    <textarea
        name="politica_devoluciones"
        rows="4"
        class="form-control"
    >{{ old(
        'politica_devoluciones',
        $configuracion->politica_devoluciones ?? ''
    ) }}</textarea>

</div>


<div class="form-group">

    <label>
        <i class="fas fa-landmark mr-1 text-info"></i>
        Condiciones de visita al Museo
    </label>

    <textarea
        name="condiciones_museo"
        rows="5"
        class="form-control"
    >{{ old(
        'condiciones_museo',
        $configuracion->condiciones_museo ?? ''
    ) }}</textarea>

</div>


<div class="form-group">

    <label>
        <i class="fas fa-list-ul mr-1 text-info"></i>
        Instrucciones y recomendaciones del Museo
    </label>

    <textarea
        name="recomendaciones_museo"
        rows="7"
        class="form-control"
        placeholder="Una recomendación por línea..."
    >{{ old(
        'recomendaciones_museo',
        $configuracion->recomendaciones_museo ?? ''
    ) }}</textarea>

</div>


<div class="form-group">

    <label>
        <i class="fas fa-tree mr-1 text-success"></i>
        Recomendaciones para visita al Parque
    </label>

    <textarea
        name="recomendaciones_parque"
        rows="7"
        class="form-control"
        placeholder="Una recomendación por línea..."
    >{{ old(
        'recomendaciones_parque',
        $configuracion->recomendaciones_parque ?? ''
    ) }}</textarea>

</div>


<div class="form-group">

    <label>
        <i class="fas fa-exclamation-triangle mr-1 text-warning"></i>
        Nota importante
    </label>

    <textarea
        name="nota_importante"
        rows="5"
        class="form-control"
    >{{ old(
        'nota_importante',
        $configuracion->nota_importante ?? ''
    ) }}</textarea>

</div>


<hr>

<h5>
    <i class="fas fa-address-book mr-1 text-primary"></i>
    Datos para confirmar la reserva
</h5>


<div class="row">

    <div class="col-md-4">

        <div class="form-group">
            <label>Correo de reservas</label>

            <input
                type="email"
                name="correo_reservas"
                class="form-control"
                value="{{ old(
                    'correo_reservas',
                    $configuracion->correo_reservas ?? ''
                ) }}"
            >
        </div>

    </div>


    <div class="col-md-4">

        <div class="form-group">
            <label>Teléfono de reservas</label>

            <input
                type="text"
                name="telefono_reservas"
                class="form-control"
                value="{{ old(
                    'telefono_reservas',
                    $configuracion->telefono_reservas ?? ''
                ) }}"
            >
        </div>

    </div>


    <div class="col-md-4">

        <div class="form-group">
            <label>Horario de contacto</label>

            <input
                type="text"
                name="horario_contacto"
                class="form-control"
                placeholder="Ej: 10:00 a 17:00 hrs."
                value="{{ old(
                    'horario_contacto',
                    $configuracion->horario_contacto ?? ''
                ) }}"
            >
        </div>

    </div>

</div>


<div class="custom-control custom-switch mt-3">

    <input
        type="hidden"
        name="activo"
        value="0"
    >

    <input
        type="checkbox"
        name="activo"
        value="1"
        class="custom-control-input"
        id="activo"
        {{
            old(
                'activo',
                $configuracion->activo ?? true
            )
                ? 'checked'
                : ''
        }}
    >

    <label
        class="custom-control-label"
        for="activo"
    >
        Utilizar esta configuración para las nuevas cotizaciones
    </label>

</div>