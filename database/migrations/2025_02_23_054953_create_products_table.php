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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_outlet')->constrained('outlets')->onDelete('cascade'); // Relasi ke tabel outlets
            $table->string('nama_produk');
            $table->decimal('harga_produk', 10, 2); // Menggunakan tipe data decimal untuk harga
            $table->unsignedInteger('stok_produk'); // Pastikan stok tidak negatif
            $table->timestamps();
            $table->softDeletes(); // Fitur soft delete
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
