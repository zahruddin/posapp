<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['outlet_id', 'nama_kategori'];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}

