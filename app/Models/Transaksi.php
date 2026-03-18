<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use App\Observers\TransaksiObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


#[ObservedBy([TransaksiObserver::class])]
class Transaksi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tanggal_transaksi',
        'produk_id',
        'quantity',
        'total',
        'month_name',
        'year',
        'week_number',
    ];


    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class)->withTrashed();
    }

    public function setTanggalTransaksiAttribute($value)
    {
        if (!$value) {
            $this->attributes['tanggal_transaksi'] = null;
            return;
        }

        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                $this->attributes['tanggal_transaksi'] =
                    Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                return;
            }
            $this->attributes['tanggal_transaksi'] =
                Carbon::parse($value)->format('Y-m-d');

        } catch (\Exception $e) {
            $this->attributes['tanggal_transaksi'] = null;
        }
    }

}
