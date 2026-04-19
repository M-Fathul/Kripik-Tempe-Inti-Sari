<?php

namespace App\Jobs;

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StatusUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $produkID;

    /**
     * Create a new job instance.
     */
    public function __construct($produkID)
    {
        $this->produkID = $produkID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $produks = Produk::where('status', 'aktif')->where('stok', 0)->get();

        foreach ($produks as $produk) {
            $transaksis = Transaksi::where('produk_id', $produk->id)
                ->max('tanggal_transaksi')
                ->get();
            if ($transaksis->isEmpty()) {
                continue;
            }
            
            // $produk->update(['status' => 'habis']);
        }
    }
}
