<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rule; // 




class KelolaUsersController extends Controller
{
    //
    // public function boot()
    // {
    //     Paginator::useBootstrap();
    // }

    function showUsers() {
        // $users = User::all(); 
        $users = User::with('outlet')->paginate(10); // Ambil 5 data per halaman
        $outlets = Outlet::all(); 
        return view('admin.kelolausers', compact('users','outlets'));
    }

    function showUsersOutlet($id) {
        // $users = User::all(); 
        $outlets = Outlet::all(); 
        $users = User::with('outlet')->where('id_outlet', $id)->paginate(10); // Ambil 5 data per halaman
        $outlet = Outlet::findOrFail($id);
        return view('admin.kelolausers', compact('users','outlet','outlets'));
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
    public function updateUser(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'edit_nama' => 'required|string|max:255',
            'edit_username' => [
                'required', 'string', 'max:255', 
                Rule::unique('users', 'username')->ignore($id)
            ],
            'edit_email' => [
                'required', 'email', 'max:255', 
                Rule::unique('users', 'email')->ignore($id)
            ],
            'edit_role' => 'required|in:admin,kasir',
            'edit_password' => 'nullable|string|min:6',
        ]);

        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Update data user
        $user->name = $request->edit_nama;
        $user->username = $request->edit_username;
        $user->email = $request->edit_email;
        $user->role = $request->edit_role;

        // Jika password diisi, update dengan hash baru
        if ($request->filled('edit_password')) {
            $user->password = Hash::make($request->edit_password);
        }

        // Jika role bukan admin, wajib mengisi id_outlet
        if ($request->edit_role == 'kasir') {
            $request->validate([
                'edit_id_outlet' => 'required|exists:outlets,id'
            ]);
            $user->id_outlet = $request->edit_id_outlet;
        } else {
            // Jika admin, outlet dikosongkan
            $user->id_outlet = null;
        }

        // Simpan perubahan
        $user->save();

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
    }
}
