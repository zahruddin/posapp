<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $fillable = [
        'id_sale',
        'metode_bayar',
        'jumlah_bayar',
        'kembalian',
        'status',
    ];

    // Relasi ke transaksi utama (sales)
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'id_sale');
    }
}
