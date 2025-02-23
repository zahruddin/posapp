<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'outlets'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key (default di Laravel)

    protected $fillable = [
        'nama_outlet',
        'alamat_outlet',
    ];

    // Tambahkan relasi jika diperlukan
    public function products()
    {
        return $this->hasMany(Product::class, 'id_outlet');
    }
}
