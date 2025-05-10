<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanSeduh extends Model
{
    use HasFactory;

    // Menyebutkan nama tabel secara eksplisit
    protected $table = 'laporan_seduh';

    // Kolom yang boleh diisi (mass assignment)
    protected $fillable = [
        'id_outlet',
        'id_user',
        'seduh',
        'keterangan',
    ];

    // Relasi ke model Outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

}