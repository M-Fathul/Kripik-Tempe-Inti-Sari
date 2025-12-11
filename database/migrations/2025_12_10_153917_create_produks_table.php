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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('nama_produk');
            $table->decimal('harga_produk', 12, 2);
            $table->integer('stok')->default(0);
            $table->integer('total_terjual')->default(0);
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->enum('status', ['aktif', 'tidak_lanjut'])->default('aktif');
            $table->enum('pemasok', ['orisinil', 'eksternal'])->default('orisinil');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('total_terjual');
            $table->index('harga_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
