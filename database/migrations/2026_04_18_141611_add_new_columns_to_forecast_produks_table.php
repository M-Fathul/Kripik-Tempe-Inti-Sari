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
        Schema::table('forecast_produks', function (Blueprint $table) {
            $table->string('month_name')->after('tanggal');
            $table->integer('week_number')->after('month_name');
            $table->integer('year')->after('week_number');

            $table->index('produk_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forecast_produks', function (Blueprint $table) {
            $table->dropColumn(['month_name', 'week_number', 'year']);
        });
    }
};
