<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use filament\Notifications\Notification;

class ForecastService
{
    // ── Private: konstanta internal, tidak perlu diketahui luar ─────────────

    private const RETRY_TIMES = 3;
    private const RETRY_DELAY = 5;
    private const TIMEOUT     = 30;

    // ── Private: hanya dipakai oleh forecast(), bukan kontrak publik ─────────

    private function buildHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . config('services.flask.key'),
        ];
    }

    private function shouldRetry(\Throwable $exception): bool
    {
        if ($exception instanceof RequestException) {
            return $exception->response->status() >= 500;
        }
        return true; // retry untuk ConnectionException, timeout, dll
    }

    private function sendRequest(array $payload): Response
    {
        return Http::timeout(self::TIMEOUT)
            ->retry(self::RETRY_TIMES, self::RETRY_DELAY, fn($e) => $this->shouldRetry($e))
            ->withHeaders($this->buildHeaders())
            ->post(config('services.flask.url'), $payload);
    }

    private function handleFailedResponse(RequestException $e): never
    {
        Log::error('API failed', [
            'status' => $e->response->status(),
            'body'   => $e->response->body(),
        ]);

        throw new \Exception(
            "Forecast API error — status: {$e->response->status()}, body: {$e->response->body()}"
        );

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('Forecast Gagal Total')
                ->body("Layanan Forecast Produk ID {$this->produkID} gagal setelah 3 percobaan.")
                ->danger()
                ->sendToDatabase($admin);
        }
    }

    // ── Public: kontrak yang boleh dipanggil dari luar (Job, Controller) ─────

    public function forecast(array $data, int $periods): array
    {
        try {
            $response = $this->sendRequest([
                'data'    => $data,
                'periods' => $periods,
            ]);
        } catch (RequestException $e) {
            $this->handleFailedResponse($e);
        }

        return $response->json();
    }

    public function transformData(\Illuminate\Support\Collection $transaksis): array
    {
        return $transaksis->map(fn($item) => [
            'ds' => $item->tanggal_transaksi,
            'y'  => (int) $item->quantity,
        ])->values()->toArray();
    }
}