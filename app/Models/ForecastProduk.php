<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastProduk extends Model
{
    protected $fillable = [
        'produk_id',
        'forecast_run_id',
        'tanggal',
        'forecast_qyt',
        'aktual_qyt',
        'upper',
        'lower',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function run()
    {
        return $this->belongsTo(ForecastRun::class);
    }
}
