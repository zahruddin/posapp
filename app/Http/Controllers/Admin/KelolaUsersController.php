<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;




class KelolaUsersController extends Controller
{
    //
    public function boot()
    {
        Paginator::useBootstrap();
    }

    function showUsers() {
        // $users = User::all(); 
        $users = User::with('outlet')->paginate(10); // Ambil 5 data per halaman
        $outlets = Outlet::all(); 
        return view('admin.kelolausers', compact('users','outlets'));
    }

    function showUsersOutlet($id) {
        // $users = User::all(); 
        $users = User::with('outlet')->where('id_outlet', $id)->paginate(10); // Ambil 5 data per halaman
        $outlets = Outlet::findOrFail($id);
        return view('admin.kelolausers', compact('users','outlets'));
    }

    public function tambahUser(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'role'      => 'required|in:admin,kasir',
            'id_outlet' => $request->role === 'kasir' ? 'required|exists:outlets,id' : 'nullable'
        ]);

        // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan pesan error
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', implode('<br>', $validator->errors()->all())) // Menggabungkan semua pesan error
                ->withInput(); // Mengembalikan input yang sudah diisi
        }

        try {
            // Data yang akan disimpan
            $userData = [
                'name'      => $request->name,
                'username'  => $request->username,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => $request->role,
            ];

            // Jika role adalah kasir, tambahkan id_outlet
            if ($request->role === 'kasir') {
                $userData['id_outlet'] = $request->id_outlet;
            }

            // Simpan data user baru
            User::create($userData);

            return redirect()->back()->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function hapusUser($id)
    {
        try {
            $user = User::findOrFail($id);

            // Pastikan admin tidak menghapus dirinya sendiri
            if (auth()->id() == $user->id) {
                return response()->json(['error' => 'Anda tidak dapat menghapus akun Anda sendiri!'], 403);
            }

            // Hapus user
            $user->delete();
            session()->flash('success', 'User berhasil dihapus!');
            return response()->json(['success' => 'User berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
