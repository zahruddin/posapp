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

            // Soft delete semua relasi terkait
            // Menghapus semua data terkait dengan outlet
            foreach ($outlet->sale as $sale) {
                $sale->details()->delete(); // Menghapus detail transaksi penjualan
                $sale->forceDelete();       // Force delete transaksi penjualan
            }

            // Hapus aktivitas dari semua user
            foreach ($outlet->user as $user) {
                $user->activity()->delete(); // Hapus aktivitas user
                $user->forceDelete();        // Force delete user
            }

            // Hapus produk dan gambar yang terkait
            foreach ($outlet->products as $product) {
                // Cek dan hapus file gambar jika ada
                if ($product->gambar && file_exists(public_path($product->gambar))) {
                    unlink(public_path($product->gambar)); // Menghapus file gambar
                }
                $product->forceDelete(); // Force delete produk
            }

            // Hapus data lainnya
            $outlet->seduh()->forceDelete();           // Force delete laporan seduh
            $outlet->expense()->forceDelete();         // Force delete pengeluaran
            $outlet->expenseCategory()->forceDelete(); // Force delete kategori pengeluaran

            // Hapus outlet itu sendiri
            $outlet->forceDelete(); // Force delete outlet

            session()->flash('success', 'Seluruh data outlet dan yang terkait berhasil dihapus!');
            return response()->json(['success' => 'Seluruh data outlet dan yang terkait berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updateOutlet(Request $request, $id)
    {
        $request->validate([
            'nama_outlet' => 'required|string|max:255',
            'alamat_outlet' => 'required|string|max:255',
        ]);

        $outlet = Outlet::findOrFail($id);

        // Update data outlet 
        $outlet->update([
            'nama_outlet' => $request->nama_outlet,
            'alamat_outlet' => $request->alamat_outlet,
        ]);

        return redirect()->back()->with('success', 'Outlet berhasil diperbarui.');
    }
}
