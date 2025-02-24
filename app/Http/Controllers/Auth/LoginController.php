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

        $remember = $request->has('remember'); // Ambil status remember me

        // Coba login dengan username atau email dalam satu percobaan saja
        if (Auth::attempt(['username' => $request->username_or_email, 'password' => $request->password], $remember) ||
            Auth::attempt(['email' => $request->username_or_email, 'password' => $request->password], $remember)) {

            // Ambil data user yang sedang login
            $user = Auth::user();

            // Redirect sesuai role
            return $user->role == 'admin' ? redirect()->route('admin.dashboard') 
                : redirect()->route('kasir.dashboard');
        }

        // Jika login gagal
        return back()->withErrors(['message' => 'Email/Username atau Password salah!'])->withInput();
    }


    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout(); // Logout pengguna

        $request->session()->invalidate(); // Hapus sesi
        $request->session()->regenerateToken(); // Regenerasi token CSRF

        return redirect('/login');
    }

}
