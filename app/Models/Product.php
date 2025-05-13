<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Traits\LogActivityAuto; 

class Product extends Model
{
    use HasFactory, SoftDeletes;
    // use LogActivityAuto; 

    // Nama tabel (opsional, jika berbeda dari nama default)
    protected $table = 'products';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'id_outlet',
        'nama_produk',
        'gambar',
        'harga_produk',
        'stok_produk',
        'deskripsi',
        'status',
    ];

    // Tipe data yang harus dikonversi
    protected $casts = [
        'harga_produk' => 'decimal:2', // Harga dikonversi ke format decimal
        'stok_produk' => 'integer', // Stok dikonversi ke integer
        'status' => 'string', // Status tetap sebagai string (enum)
    ];

    // Relasi ke model Outlet (Satu outlet bisa memiliki banyak produk)
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
    public function saleDetail()
    {
        return $this->hasMany(SalesDetail::class, 'id_produk');
    }

}
