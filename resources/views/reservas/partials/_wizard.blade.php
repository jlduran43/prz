<div class="wizard-steps">

    <div
        class="wizard-step
        {{ $paso === 1 ? 'active' : '' }}
        {{ $paso > 1 ? 'completed' : '' }}"
    >
        <span class="step-number">
            {{ $paso > 1 ? '✓' : '1' }}
        </span>

        <span>
            <strong>Cliente</strong><br>
            <small>Datos del solicitante</small>
        </span>
    </div>

    <div
        class="wizard-step
        {{ $paso === 2 ? 'active' : '' }}
        {{ $paso > 2 ? 'completed' : '' }}"
    >
        <span class="step-number">
            {{ $paso > 2 ? '✓' : '2' }}
        </span>

        <span>
            <strong>Reserva</strong><br>
            <small>Recinto, fecha y horario</small>
        </span>
    </div>

    <div
        class="wizard-step
        {{ $paso === 3 ? 'active' : '' }}
        {{ $paso > 3 ? 'completed' : '' }}"
    >
        <span class="step-number">
            {{ $paso > 3 ? '✓' : '3' }}
        </span>

        <span>
            <strong>Confirmación</strong><br>
            <small>Revisión final</small>
        </span>
    </div>

</div>

@section('css')
<style>
    .wizard-steps {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }

    .wizard-step {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: #f4f6f9;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        color: #6c757d;
    }

    .wizard-step.active {
        background: #007bff;
        border-color: #007bff;
        color: #ffffff;
    }

    .wizard-step.completed {
        background: #28a745;
        border-color: #28a745;
        color: #ffffff;
    }

    .step-number {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 50%;
        background: #ffffff;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .wizard-step.active .step-number {
        color: #007bff;
    }

    .wizard-step.completed .step-number {
        color: #28a745;
    }

    .wizard-step small {
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .wizard-steps {
            flex-direction: column;
        }
    }
</style>
@stop
