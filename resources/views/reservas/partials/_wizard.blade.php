<div class="wizard-steps">

    <div class="wizard-step
        {{ $paso === 1 ? 'active' : '' }}
        {{ $paso > 1 ? 'completed' : '' }}">
        <span class="step-number">
            {{ $paso > 1 ? '✓' : '1' }}
        </span>

        <span>
            <strong>Cliente</strong><br>
            <small>Datos del solicitante</small>
        </span>
    </div>

    <div class="wizard-step
        {{ $paso === 2 ? 'active' : '' }}
        {{ $paso > 2 ? 'completed' : '' }}">
        <span class="step-number">
            {{ $paso > 2 ? '✓' : '2' }}
        </span>

        <span>
            <strong>Reserva</strong><br>
            <small>Recinto, fecha y horario</small>
        </span>
    </div>

    <div class="wizard-step
        {{ $paso === 3 ? 'active' : '' }}
        {{ $paso > 3 ? 'completed' : '' }}">
        <span class="step-number">
            {{ $paso > 3 ? '✓' : '3' }}
        </span>

        <span>
            <strong>Confirmación</strong><br>
            <small>Revisión final</small>
        </span>
    </div>

</div>