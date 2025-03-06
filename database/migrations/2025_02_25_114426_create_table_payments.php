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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('id_sale')->constrained('sales')->onDelete('cascade'); // Relasi ke transaksi
            $table->enum('metode_bayar', ['cash', 'qris', 'debit', 'kredit'])->default('cash'); // Metode pembayaran
            $table->decimal('jumlah_bayar', 12, 2); // Jumlah yang dibayarkan
            $table->decimal('kembalian', 12, 2)->default(0); // Kembalian jika ada
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending'); // Status pembayaran
            $table->timestamps(); // created_at & updated_at
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_payments');
    }
};
