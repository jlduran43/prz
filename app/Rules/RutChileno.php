<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RutChileno implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if (! is_string($value) || ! self::esValido($value)) {
            $fail('El :attribute no es un RUT chileno válido.');
        }
    }

    public static function esValido(?string $rut): bool
    {
        if ($rut === null || trim($rut) === '') {
            return false;
        }

        // Eliminar puntos, guion y espacios.
        $rut = strtoupper(
            preg_replace('/[^0-9K]/i', '', $rut)
        );

        if (strlen($rut) < 2) {
            return false;
        }

        $cuerpo = substr($rut, 0, -1);
        $dvIngresado = substr($rut, -1);

        if (! ctype_digit($cuerpo)) {
            return false;
        }

        // Evitar valores evidentemente falsos como 11.111.111-1.
        if (preg_match('/^(\d)\1+$/', $cuerpo)) {
            return false;
        }

        $suma = 0;
        $multiplicador = 2;

        for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
            $suma += ((int) $cuerpo[$i]) * $multiplicador;

            $multiplicador++;

            if ($multiplicador > 7) {
                $multiplicador = 2;
            }
        }

        $resto = 11 - ($suma % 11);

        $dvCalculado = match ($resto) {
            11 => '0',
            10 => 'K',
            default => (string) $resto,
        };

        return $dvIngresado === $dvCalculado;
    }
}
