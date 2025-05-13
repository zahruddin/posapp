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
    public function user()
    {
        return $this->hasMany(User::class, 'id_outlet');
    }
    public function seduh()
    {
        return $this->hasMany(LaporanSeduh::class, 'id_outlet');
    }
    public function expenseCategory()
    {
        return $this->hasMany(ExpenseCategory::class, 'outlet_id');
    }
    public function sale()
    {
        return $this->hasMany(Sale::class, 'id_outlet');
    }
    public function expense()
    {
        return $this->hasMany(Expense::class, 'outlet_id');
    }
}
