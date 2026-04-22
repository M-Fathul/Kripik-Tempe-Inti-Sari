<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class ForecastService
{
    private function validateResponse($response): bool
    {
        if (!$response->successful()) {
            \Log::error('API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }
        return true;
    }
    
    public function forecast($data, $periods)
    {
        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.flask.key'),
            ])
            ->post(config('services.flask.url'), [
                'data' => $data,
                'periods' => $periods,
            ]);

            if (!$this->validateResponse($response)) {
                throw new \Exception('Forecast failed');
            }
            
            return $response->json();
    }

    public function transformData($transaksis)
    {
        return $transaksis->map(function ($item) {
            return [
                'ds' => $item->tanggal_transaksi,
                'y' => (int)$item->quantity,
            ];
        })->values()->toArray();
    }
}