<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); 
            $table->string('action'); // Contoh: create, update, delete
            $table->string('table_name'); // Tabel apa yang diubah, misal: products
            $table->unsignedBigInteger('record_id')->nullable(); // ID record yang diubah
            $table->text('description')->nullable(); // Catatan apa yang diubah
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
