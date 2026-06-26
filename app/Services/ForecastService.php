<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForecastService
{
    private const RETRY_TIMES = 3;

    private const RETRY_DELAY = 5;

    private const TIMEOUT = 120;

    private function buildHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.config('services.flask.key'),
            'Content-Type' => 'application/json',
        ];
    }

    private function shouldRetry(\Throwable $exception): bool
    {
        if ($exception instanceof RequestException) {
            return $exception->response->status() >= 500;
        }

        return true;
    }

    private function sendRequest(array $payload): Response
    {
        return Http::timeout(self::TIMEOUT)
            ->retry(self::RETRY_TIMES, self::RETRY_DELAY, fn ($e) => $this->shouldRetry($e))
            ->withHeaders($this->buildHeaders())
            ->post(config('services.flask.url'), $payload);
    }

    private function handleFailedResponse(RequestException $e): never
    {
        Log::error('API failed', [
            'status' => $e->response->status(),
            'body' => $e->response->body(),
        ]);

        throw new \Exception(
            "Forecast API error — status: {$e->response->status()}, body: {$e->response->body()}"
        );
    }

    public function forecast(array $data, int $periods): array
    {
        try {
            $response = $this->sendRequest([
                'data' => $data,
                'periods' => $periods,
            ]);
        } catch (RequestException $e) {
            $this->handleFailedResponse($e);
        }

        return $response->json();
    }
}
