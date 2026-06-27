<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastRun extends Model
{
    protected $fillable = [
        'produk_id',
        'akurasi',
        'insight',
        'periods',
    ];

    protected $casts = [
        'insight' => 'array',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function forecasts()
    {
        return $this->hasMany(ForecastProduk::class);
    }
}
