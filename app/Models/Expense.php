<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    protected static function booted()
    {
        static::deleted(function ($expense) {
            if ($expense->expense_category_id) {
                $categoryId = $expense->expense_category_id;
                $count = Expense::where('expense_category_id', $categoryId)->count();

                if ($count === 0) {
                    \App\Models\ExpenseCategory::where('id', $categoryId)->delete();
                }
            }
        });
    }

    protected $fillable = [
        'outlet_id', 
        'user_id', 
        'expense_category_id', 
        'biaya', 
        'keterangan', 
        'datetime'
    ];
    
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

