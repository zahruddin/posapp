<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_outlet')->nullable()->after('id'); 
            $table->foreign('id_outlet')->references('id')->on('outlets'); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_outlet']); // Hapus foreign key
            $table->dropColumn('id_outlet'); // Hapus kolom jika rollback
        });
    }
};
