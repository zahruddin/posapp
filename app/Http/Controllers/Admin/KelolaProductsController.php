<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;

class KelolaProductsController extends Controller
{
    //
    public function boot()
    {
        Paginator::useBootstrap();
    }
    public function showProducts($id) 
    {
        // Ambil outlet berdasarkan ID
        $outlet = Outlet::findOrFail($id);

        // Ambil produk hanya dari outlet yang dipilih
        $products = Product::where('id_outlet', $id)->paginate(10);
        // $products = Product::where('id_outlet', $id)->orderBy('id', 'asc')->paginate(10);


        // Kirim data ke view
        return view('admin.kelolaproducts', compact('products', 'outlet'));
    }
    public function hapusProduct($id_outlet, $id)
    {
        try {
            // Cari produk berdasarkan id_outlet dan id
            $product = Product::where('id_outlet', $id_outlet)->where('id', $id)->firstOrFail();
    
            // Hapus produk
            $product->delete();
            session()->flash('success', 'Product berhasil dihapus!');
            return response()->json(['success' => 'Produk berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    public function tambahProduk(Request $request, $id_outlet)
    {
        // Validasi input
        $request->validate([
            'nama_produk'  => 'required|string|max:255',
            'harga_produk' => 'required|numeric|min:0',
            'stok_produk'  => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'       => 'required|in:aktif,nonaktif',
        ]);

        // Proses upload gambar ke public/assets/gambar_produk/
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
        
            // Simpan file ke `storage/app/public/gambar_produk/`
            $gambarPath = $file->storeAs('public/gambar_produk', $namaFile);
        
            // Ubah path agar bisa diakses melalui `public/storage/gambar_produk/`
            $gambarPath = str_replace('public/', 'storage/', $gambarPath);
        }        

        // Simpan data produk ke database
        Product::create([
            'id_outlet'    => $id_outlet,
            'nama_produk'  => $request->nama_produk,
            'harga_produk' => $request->harga_produk,
            'stok_produk'  => $request->stok_produk,
            'deskripsi'    => $request->deskripsi,
            'gambar'       => $gambarPath,
            'status'       => $request->status,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }

}
