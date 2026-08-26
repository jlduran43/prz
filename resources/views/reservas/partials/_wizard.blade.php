<style>
    .wizard-steps {
        display: flex;
        align-items: stretch;
        margin-bottom: 25px;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

    .wizard-step {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        position: relative;
        color: #6c757d;
        background: #f8f9fa;
        border-right: 1px solid #dee2e6;
    }

    .wizard-step:last-child {
        border-right: none;
    }

    .wizard-step .step-number {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 36px;
        height: 36px;

        flex: 0 0 36px;

        border-radius: 50%;
        background: #adb5bd;
        color: #fff;

        font-weight: 700;
        font-size: 15px;
    }

    .wizard-step strong {
        color: inherit;
    }

    .wizard-step small {
        color: #6c757d;
    }

    .wizard-step.active {
        background: #e7f1ff;
        color: #007bff;
    }

    .wizard-step.active .step-number {
        background: #007bff;
    }

    .wizard-step.completed {
        background: #eaf7ee;
        color: #28a745;
    }

    .wizard-step.completed .step-number {
        background: #28a745;
    }

    @media (max-width: 767.98px) {
        .wizard-steps {
            flex-direction: column;
        }

        .wizard-step {
            border-right: none;
            border-bottom: 1px solid #dee2e6;
        }

        .wizard-step:last-child {
            border-bottom: none;
        }
    }
</style>
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