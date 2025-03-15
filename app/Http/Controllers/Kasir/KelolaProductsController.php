<?php

namespace App\Http\Controllers\kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\User;

class KelolaProductsController extends Controller
{
    //
    function showKelolaProduk()
    {
        
            // Ambil user yang sedang login
            $user = Auth::user();

            // Pastikan user memiliki outlet yang terkait
            if (!$user || !$user->id_outlet) {
                return redirect()->back()->with('error', 'Outlet tidak ditemukan untuk user ini.');
            }

            // Ambil outlet berdasarkan ID outlet yang dimiliki user
            $outlet = Outlet::findOrFail($user->id_outlet);
            // dd($outlet);
            // Ambil produk hanya dari outlet tersebut
            $products = Product::where('id_outlet', $outlet->id)->paginate(10);
            // dd($products->toArray());

            // Kirim data ke view
            return view('kasir.kelolaproducts', compact('products', 'outlet'));
    }
    public function tambahProduct(Request $request)
    {
        $user = Auth::user();

        if (!$user->id_outlet) {
            return redirect()->back()->with('error', 'Anda tidak memiliki outlet terkait.');
        }

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga_produk' => 'required|numeric|min:0',
            'stok_produk' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Simpan gambar ke public/assets/gambar_produk/
        $gambarPath = null;
        // $gambarPath = 'dist/assets/img/prod-1.jpg'; 
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
        
            // Simpan file ke `storage/app/public/gambar_produk/`
            $gambarPath = $file->storeAs('public/gambar_produk', $namaFile);
        
            // Ubah path agar bisa diakses melalui `public/storage/gambar_produk/`
            $gambarPath = str_replace('public/', 'storage/', $gambarPath);
        }
        

        // Simpan produk ke database
        Product::create([
            'id_outlet' => $user->id_outlet,
            'nama_produk' => $request->nama_produk,
            'harga_produk' => $request->harga_produk,
            'stok_produk' => $request->stok_produk,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath, // Simpan path relatif
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
    }
    public function hapusProduct($id)
    {
        try {
            // Cari produk berdasarkan id_outlet dan id
            $user = Auth::user();
            $id_outlet = $user->id_outlet;
            $product = Product::where('id_outlet', $id_outlet)->where('id', $id)->firstOrFail();
            // Hapus produk
            $product->delete();
            session()->flash('success', 'Product berhasil dihapus!');
            return response()->json(['success' => 'Produk berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    public function updateProduk(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga_produk' => 'required|numeric|min:0',
            'stok_produk' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $produk = Product::findOrFail($id);

        // Simpan gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar && file_exists(public_path($produk->gambar))) {
                unlink(public_path($produk->gambar));
            }

            // Simpan gambar baru ke `storage/app/public/gambar_produk/`
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/gambar_produk', $namaFile);

            // Path gambar untuk disimpan di database (agar bisa diakses via `storage/`)
            $gambarPath = "storage/gambar_produk/" . $namaFile;

            // Perbarui gambar di database
            $produk->gambar = $gambarPath;
        }

        // Update data produk tanpa mengganti gambar jika tidak ada gambar baru
        $produk->update([
            'nama_produk' => $request->nama_produk,
            'harga_produk' => $request->harga_produk,
            'stok_produk' => $request->stok_produk,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }


}
