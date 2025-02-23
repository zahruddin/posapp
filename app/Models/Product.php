<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel (opsional, jika berbeda dari nama default)
    protected $table = 'products';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'id_outlet',
        'nama_produk',
        'harga_produk',
        'stok_produk',
    ];

    // Tipe data yang harus dikonversi
    protected $casts = [
        'harga_produk' => 'decimal:2', // Harga dikonversi ke format decimal
    ];

    // Relasi ke model Outlet (Satu outlet bisa memiliki banyak produk)
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
}
