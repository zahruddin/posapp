<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    use HasFactory;

    protected $table = 'sales_details';
    protected $fillable = [
        'id_sale',
        'id_produk',
        'nama_produk',
        'harga_produk',
        'jumlah',
        'subtotal',
        'diskon',
        'total',
    ];

    // Relasi ke transaksi utama (sales)
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'id_sale');
    }

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk');
    }
}
