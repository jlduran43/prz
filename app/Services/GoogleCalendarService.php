<?php

namespace App\Services;

use App\Models\HorarioDisponible;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected Calendar $calendar;

    protected string $calendarId;

    protected string $timezone;

    public function __construct()
    {
        $client = new Client();

        $client->setAuthConfig(
            config('services.google_calendar.credentials')
        );

        $client->setScopes([
            Calendar::CALENDAR_EVENTS,
        ]);

        $this->calendar = new Calendar($client);

        $this->calendarId = config(
            'services.google_calendar.calendar_id'
        );

        $this->timezone = config(
            'services.google_calendar.timezone',
            'America/Santiago'
        );
    }

    public function crearEvento(HorarioDisponible $horario): string
    {

        $horario->load('servicios');

        $nombresServicios = $horario->servicios
            ->pluck('nombre')
            ->filter()
            ->values();

        $titulo = 'Horario disponible PRZ';

        $descripcionServicios = $nombresServicios->isNotEmpty()
            ? $nombresServicios
            ->map(fn($nombre) => '- ' . $nombre)
            ->implode("\n")
            : '- Sin servicios asociados';

        $inicio = Carbon::parse(
            $horario->fecha->format('Y-m-d')
                . ' '
                . $horario->hora_inicio,
            $this->timezone
        );

        $termino = Carbon::parse(
            $horario->fecha->format('Y-m-d')
                . ' '
                . $horario->hora_termino,
            $this->timezone
        );

        $evento = new Event([
            'summary' => $titulo,

            'description' =>
            "Servicios:\n"
                . $descripcionServicios
                . "\n\n"
                . "Horario generado desde el sistema de reservas PRZ.\n"
                . "ID horario: "
                . $horario->id,

            'start' => [
                'dateTime' => $inicio->toRfc3339String(),
                'timeZone' => $this->timezone,
            ],

            'end' => [
                'dateTime' => $termino->toRfc3339String(),
                'timeZone' => $this->timezone,
            ],
        ]);

        $eventoCreado = $this->calendar
            ->events
            ->insert(
                $this->calendarId,
                $evento
            );

        Log::info(
            'Horario sincronizado con Google Calendar',
            [
                'horario_id' => $horario->id,
                'google_event_id' => $eventoCreado->getId(),
            ]
        );

        return $eventoCreado->getId();
    }

    public function actualizarEvento(HorarioDisponible $horario): void
    {
        if (!$horario->google_event_id) {
            return;
        }

        $horario->load('servicios');

        $nombresServicios = $horario->servicios
            ->pluck('nombre')
            ->filter()
            ->values();

        $titulo = 'Horario disponible PRZ';

        $descripcionServicios = $nombresServicios->isNotEmpty()
            ? $nombresServicios
            ->map(fn($nombre) => '- ' . $nombre)
            ->implode("\n")
            : '- Sin servicios asociados';

        $inicio = Carbon::parse(
            $horario->fecha->format('Y-m-d')
                . ' '
                . $horario->hora_inicio,
            $this->timezone
        );

        $termino = Carbon::parse(
            $horario->fecha->format('Y-m-d')
                . ' '
                . $horario->hora_termino,
            $this->timezone
        );

        $evento = $this->calendar
            ->events
            ->get(
                $this->calendarId,
                $horario->google_event_id
            );

        $evento->setSummary(
            $horario->activo
                ? $titulo
                : '[INACTIVO] ' . $titulo
        );

        $evento->setDescription(
            "Servicios:\n"
                . $descripcionServicios
                . "\n\n"
                . "Horario generado desde el sistema de reservas PRZ.\n"
                . "ID horario: "
                . $horario->id
        );

        $evento->setStart(
            new \Google\Service\Calendar\EventDateTime([
                'dateTime' => $inicio->toRfc3339String(),
                'timeZone' => $this->timezone,
            ])
        );

        $evento->setEnd(
            new \Google\Service\Calendar\EventDateTime([
                'dateTime' => $termino->toRfc3339String(),
                'timeZone' => $this->timezone,
            ])
        );

        $this->calendar
            ->events
            ->update(
                $this->calendarId,
                $horario->google_event_id,
                $evento
            );
    }

    public function actualizarEstadoEvento(HorarioDisponible $horario): void 
    {

        if (!$horario->google_event_id) {
            return;
        }

        $this->actualizarEvento($horario);
    }
}
