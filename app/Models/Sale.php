<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $fillable = [
        'id_outlet',
        'id_user',
        'total_harga',
        'total_diskon',
        'total_bayar',
        'metode_bayar',
        'status_bayar',
    ];

    // Relasi ke Outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    // Relasi ke User (kasir)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke detail penjualan
    public function details()
    {
        return $this->hasMany(SalesDetail::class, 'id_sale');
    }

    // Relasi ke pembayaran
    public function payment()
    {
        return $this->hasOne(Payment::class, 'id_sale');
    }
}
