<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanSeduhTable extends Migration
{
    public function up()
    {
        Schema::create('laporan_seduh', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('id_outlet'); // Relasi ke tabel outlets
            $table->unsignedBigInteger('id_user'); // Relasi ke tabel users
            $table->decimal('seduh', 8, 2); // Kolom decimal (contoh: max 999999.99)
            $table->string('keterangan')->nullable(); // Keterangan opsional
            $table->timestamps(); // created_at dan updated_at

            // Foreign key constraint
            $table->foreign('id_outlet')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan_seduh');
    }
}
