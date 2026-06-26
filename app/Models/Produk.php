<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'image',
        'nama_produk',
        'harga_produk',
        'stok',
        'total_terjual',
        'kategori_id',
        'status',
        'pemasok',
    ];
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}
