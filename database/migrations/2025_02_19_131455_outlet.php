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
        //
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_outlet');
            $table->string('alamat_outlet');
            $table->timestamps();
            $table->softDeletes(); // Fitur soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('outlets');
    }
};
