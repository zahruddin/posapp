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
    
}
