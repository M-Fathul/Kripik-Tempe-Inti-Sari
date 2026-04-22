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
            $table->string('month_name');
            $table->integer('week_number');
            $table->integer('year');
            $table->float('forecast_qyt');
            $table->float('aktual_qyt')->nullable();
            $table->float('upper')->nullable();
            $table->float('lower')->nullable();
            $table->timestamps();

            $table->unique(['produk_id', 'forecast_run_id', 'tanggal']);
            $table->index('produk_id');
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
