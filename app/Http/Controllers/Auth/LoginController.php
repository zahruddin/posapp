<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        // Cek jika pengguna sudah login
        if (auth()->check()) {
            // Jika sudah login, arahkan ke halaman yang sesuai berdasarkan role
            $role = auth()->user()->role;
            // Misalnya jika role adalah admin, arahkan ke dashboard admin
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            // Jika role adalah kasir, arahkan ke dashboard kasir
            if ($role === 'kasir') {
                return redirect()->route('kasir.dashboard');
            }
            // Jika role tidak sesuai, bisa arahkan ke halaman utama atau halaman lain
            return redirect('/');
        }
        // Jika belum login, tampilkan halaman login
        return view('auth.login');
    }


    // Proses login
    public function login(Request $request)
    {
        // Validasi input pengguna
        $request->validate([
            'username_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Mencari user berdasarkan username atau email
        $credentials = $request->only('username_or_email', 'password');

        // Menemukan user berdasarkan username atau email
        $user = User::where('username', $credentials['username_or_email'])
                    ->orWhere('email', $credentials['username_or_email'])
                    ->first();

        // Jika user ditemukan dan password cocok
        if ($user && Auth::attempt([
            'username' => $credentials['username_or_email'], 
            'password' => $credentials['password']
        ]) || $user && Auth::attempt([
            'email' => $credentials['username_or_email'], 
            'password' => $credentials['password']
        ])) {
            // Setelah login berhasil, arahkan berdasarkan role pengguna
            if ($user->role == 'admin') {
                // Arahkan ke dashboard admin
                return redirect()->route('admin.dashboard');
            } elseif ($user->role == 'kasir') {
                // Arahkan ke dashboard kasir
                return redirect()->route('kasir.dashboard');
            }
        }

        // Jika login gagal
        return back()->withErrors(['message' => 'Login failed']);
    }

    // Proses logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login'); // Arahkan kembali ke halaman login
    }
}
