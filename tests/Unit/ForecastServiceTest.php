<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ForecastService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class ForecastServiceTest extends TestCase
{
    private ForecastService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForecastService();
    }


    public function test_throws_exception_when_server_is_down(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Connection refused');
        });

        $this->expectException(ConnectionException::class);

        $this->service->forecast([['ds' => '2024-01-01', 'y' => 10]], 30);
    }

    public function test_throws_exception_on_timeout(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Connection timed out after 30 seconds');
        });

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessageMatches('/timed out/i');

        $this->service->forecast([['ds' => '2024-01-01', 'y' => 10]], 30);
    }

    public function test_throws_exception_on_5xx_response(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push(['error' => 'Internal Server Error'], 500)
                ->push(['error' => 'Internal Server Error'], 500)
                ->push(['error' => 'Internal Server Error'], 500),
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('API failed', \Mockery::on(fn($ctx) => $ctx['status'] === 500));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Forecast API error/');

        $this->service->forecast([['ds' => '2024-01-01', 'y' => 10]], 30);
    }

    public function test_throws_exception_on_4xx_response(): void
    {
        Http::fake([
            '*' => Http::response(['error' => 'periods must be integer'], 400),
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('API failed', \Mockery::on(function ($context) {
                return $context['status'] === 400;
            }));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Forecast API error/');

        $this->service->forecast([['ds' => '2024-01-01', 'y' => 10]], 30);
    }


    public function test_returns_parsed_json_on_success(): void
    {
        $fakeResponse = [
            'mape' => 12.5,
            'insight' => 'Tren naik',
            'train_start' => '2023-01-01',
            'train_end' => '2023-12-31',
            'validation' => [],
            'forecast' => [
                [
                    'ds' => '2024-02-01',
                    'month' => 'February',
                    'week' => 5,
                    'year' => 2024,
                    'yhat' => 150,
                    'yhat_upper' => 180,
                    'yhat_lower' => 120
                ],
            ],
        ];

        Http::fake(['*' => Http::response($fakeResponse, 200)]);

        $result = $this->service->forecast([['ds' => '2024-01-01', 'y' => 10]], 30);

        $this->assertEquals(12.5, $result['mape']);
        $this->assertArrayHasKey('forecast', $result);
    }

    public function test_transform_data_formats_correctly(): void
    {
        $transaksis = collect([
            (object) ['tanggal_transaksi' => '2024-01-01', 'quantity' => '25'],
            (object) ['tanggal_transaksi' => '2024-01-02', 'quantity' => '30.7'],
        ]);

        $result = $this->service->transformData($transaksis);

        $this->assertEquals([
            ['ds' => '2024-01-01', 'y' => 25],
            ['ds' => '2024-01-02', 'y' => 30],
        ], $result);
    }

    
}