<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class ForecastService
{
    public function forecast($produk, $data, $periods = 30)
    {
        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.flask.key'),
            ])
            ->post(config('services.flask.url'), [
                'produk_id' => $produk->id,
                'data' => $data,
                'periods' => $periods,
            ]);

        if (!$response->successful()) {
            \Log::error('Flask API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Forecast service fail');
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