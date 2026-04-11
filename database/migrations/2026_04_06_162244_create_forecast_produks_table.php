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
        Schema::create('forecast_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained()->cascadeOnDelete();
            $table->foreignId('forecast_run_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->float('forecast_qyt');
            $table->float('upper')->nullable();
            $table->float('lower')->nullable();
            $table->timestamps();

            $table->unique(['produk_id', 'forecast_run_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecast_produks');
    }
};
