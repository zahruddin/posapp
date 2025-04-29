<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;



class ProfileController extends Controller
{
    //
    public function showProfile(){
        $user = Auth::user(); // ambil data user yang sedang login
        // dd($user);
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request){
        try {
            $user = Auth::user();
    
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'username' => 'required|string|max:50|unique:users,username,' . $user->id,
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6|confirmed',
            ]);
    
            // Update nama dan email
            $user->name = $validated['name'];
            $user->username = $validated['username'];
            $user->email = $validated['email'];
    
            // Update password jika diisi
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
    
            $user->save();
    
            return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui profil: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui profil. Silakan coba lagi.');
        }
    }
}
