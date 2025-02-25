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
    



}
