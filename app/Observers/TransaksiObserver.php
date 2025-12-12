<?php

namespace App\Observers;

use App\Models\Transaksi;

class TransaksiObserver
{
    /**
     * Handle the Transaksi "created" event.
     */
    public function created(Transaksi $transaksi): void
    {
        $produk = $transaksi->produk;
        if ($produk) {
            $produk->decrement('stok', $transaksi->quantity);
            $produk->increment('total_terjual', $transaksi->quantity);
        }
    }

    /**
     * Handle the Transaksi "updated" event.
     */
    public function updated(Transaksi $transaksi): void
    {
        if ($transaksi->isDirty('quantity')) {
            $originalQuantity = $transaksi->getOriginal('quantity');
            $newQuantity = $transaksi->quantity;
            $difference = $newQuantity - $originalQuantity;

            $produk = $transaksi->produk;
            if ($produk) {
                if ($difference > 0) {
                    $produk->decrement('stok', $difference);
                    $produk->increment('total_terjual', $difference);
                } elseif ($difference < 0) {
                    $produk->increment('stok', abs($difference));
                    $produk->decrement('total_terjual', abs($difference));
                }
            }
        }
    }

    /**
     * Handle the Transaksi "deleted" event.
     */
    public function deleted(Transaksi $transaksi): void
    {
        if ($transaksi->isForceDeleting()) {
            return; 
        }
        
        $produk = $transaksi->produk;
        if ($produk) {
            $produk->increment('stok', $transaksi->quantity);
            $produk->decrement('total_terjual', $transaksi->quantity);
        }
    }

    /**
     * Handle the Transaksi "restored" event.
     */
    public function restored(Transaksi $transaksi): void
    {
        $produk = $transaksi->produk;
        if ($produk) {
            $produk->decrement('stok', $transaksi->quantity);
            $produk->increment('total_terjual', $transaksi->quantity);
        }
    }

    /**
     * Handle the Transaksi "force deleted" event.
     */
    public function forceDeleted(Transaksi $transaksi): void
    {
        //
    }
}
