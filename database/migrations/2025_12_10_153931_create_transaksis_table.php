<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tanggal_transaksi')->useCurrent();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('total', 12, 2);
            $table->string('month_name');
            $table->integer('year');
            $table->integer('week_number');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tanggal_transaksi');
            $table->index('produk_id');
            $table->index(['year', 'month_name']);
            $table->index(['produk_id', 'tanggal_transaksi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
