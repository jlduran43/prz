<?php

namespace App\Services;

use App\Models\Reserva;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class KhipuService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.khipu.base_url'),
            '/'
        );

        $this->apiKey = config('services.khipu.api_key');
    }


    public function crearPago(Reserva $reserva): array
    {
        // ...

        $response = Http::acceptJson()
            ->asJson()
            ->withHeaders([
                'x-api-key' => $this->apiKey,
            ])
            ->post(
                $this->baseUrl . '/v3/payments',
                [
                    // datos del pago...
                ]
            );

        if ($response->failed()) {

            throw new RuntimeException(
                'Error Khipu | HTTP: ' .
                    $response->status() .
                    ' | Body: ' .
                    $response->body()
            );
        }

        $data = $response->json();

        // ...
    }


    public function consultarPago(string $paymentId): array
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'x-api-key' => $this->apiKey,
            ])
            ->get(
                $this->baseUrl . '/v3/payments/' . $paymentId
            );

        if ($response->failed()) {

            throw new RuntimeException(
                'Error Khipu | HTTP: ' .
                    $response->status() .
                    ' | Body: ' .
                    $response->body()
            );
        }

        return $response->json();
    }
}
