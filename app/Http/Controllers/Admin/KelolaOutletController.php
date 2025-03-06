<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;

class KelolaOutletController extends Controller
{
    // tampil kelola outlet
    public function showKelolaOutlet()
    {
        // Ambil semua data outlet dari database
        $outlets = Outlet::all(); 
    
        // Kirim data ke view
        return view('admin.kelolaoutlet', compact('outlets'));
    }
    
    public function tambahOutlet(Request $request) 
    {
        $request->validate([
            'nama_outlet' => 'required|string|max:255',
            'alamat_outlet' => 'required|string|max:255',
        ]);
    
        try {
            Outlet::create([
                'nama_outlet' => $request->nama_outlet,
                'alamat_outlet' => $request->alamat_outlet,
            ]);
    
            return redirect()->back()->with('success', 'Outlet berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan outlet. Silakan coba lagi.');
        }
    }
    public function hapusOutlet($id)
    {
        try {
            $outlet = Outlet::findOrFail($id);

            // Hapus outlet
            $outlet->delete();
            session()->flash('success', 'Outlet berhasil dihapus!');
            return response()->json(['success' => 'Outlet berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
