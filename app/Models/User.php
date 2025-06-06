<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_outlet',
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Fungsi untuk menemukan user berdasarkan username atau email
     * untuk keperluan login (misalnya menggunakan Passport).
     *
     * @param string $username
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)
                    ->orWhere('username', $username)
                    ->first();
    }

    // Opsional, jika ingin menambahkan validasi atau atribut lain untuk keperluan login
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
    public function seduh()
    {
        return $this->hasMany(LaporanSeduh::class, 'id_user');
    }
    public function sale()
    {
        return $this->hasMany(Sale::class, 'id_user');
    }
    public function activity()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
    public function expense()
    {
        return $this->hasMany(Expense::class, 'user_id');
    }

}
