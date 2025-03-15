<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;


class SalesController extends Controller
{
    //
    public function boot()
    {
        Paginator::useBootstrap();
    }
    public function showHalamanKasir() 
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Pastikan user memiliki outlet yang terkait
        if (!$user->id_outlet) {
            return redirect()->back()->with('error', 'Anda tidak memiliki outlet terkait.');
        }

        // Ambil hanya produk aktif berdasarkan outlet dari user yang login, urutkan berdasarkan harga terendah
        $products = Product::where('id_outlet', $user->id_outlet)
                            ->where('status', 'aktif') // Sesuaikan dengan nilai status yang digunakan
                            ->orderBy('harga_produk', 'asc') // Urutkan harga dari yang terendah
                            ->get();

        // Kirim data ke view
        return view('kasir.halamankasir', compact('products'));
    }

    
    public function tambahPenjualan(Request $request)
    {
        $cartItems = $request->input('cart');
        $paymentMethod = $request->input('paymentMethod', 'cash');
    
        if (!$cartItems || count($cartItems) == 0) {
            return response()->json(['message' => 'Keranjang kosong!'], 400);
        }
    
        // Hitung total harga dan total diskon
        $totalHarga = 0;
        $totalDiskon = 0;
    
        foreach ($cartItems as $item) {
            $subtotal = $item['price'] * $item['qty'];
            $diskon = $item['discount'] ?? 0;
    
            $totalHarga += $subtotal;
            $totalDiskon += $diskon;
        }
    
        $jumlahBayar = $totalHarga - $totalDiskon;
        $kembalian = 0; 
    
        try {
            DB::beginTransaction();
    
            // Simpan transaksi utama ke tabel sales
            $sale = Sale::create([
                'id_outlet'    => Auth::user()->id_outlet,
                'id_user'      => Auth::id(),
                'total_harga'  => $totalHarga,
                'total_diskon' => $totalDiskon,
                'total_bayar'  => $jumlahBayar,
                'metode_bayar' => $paymentMethod,
                'status_bayar' => 'lunas'
            ]);
    
            // Simpan detail penjualan dan kurangi stok produk
            foreach ($cartItems as $item) {
                SalesDetail::create([
                    'id_sale'      => $sale->id,
                    'id_produk'    => $item['id'],
                    'nama_produk'  => $item['name'],
                    'harga_produk' => $item['price'],
                    'jumlah'       => $item['qty'],
                    'subtotal'     => $item['price'] * $item['qty'],
                    'diskon'       => $item['discount'] ?? 0,
                    'total'        => ($item['price'] * $item['qty']) - ($item['discount'] ?? 0)
                ]);
    
                // **Kurangi stok produk**
                $product = Product::find($item['id']);

                if (!$product) {
                    throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan!");
                }

                // Debug stok sebelum pengecekan
                \Log::info("Produk: {$product->nama_produk}, Stok: {$product->stok_produk}, Qty: {$item['qty']}");

                if ($product->stok_produk >= $item['qty']) {
                    $product->stok_produk -= $item['qty'];
                    $product->save();
                } else {
                    throw new \Exception("Stok untuk {$product->nama_produk} tidak mencukupi!");
                }


            }
    
            // Simpan pembayaran ke tabel payments
            Payment::create([
                'id_sale'      => $sale->id,
                'metode_bayar' => $paymentMethod,
                'jumlah_bayar' => $jumlahBayar,
                'kembalian'    => $kembalian,
                'status'       => 'berhasil'
            ]);
    
            DB::commit();
            session()->flash('success', 'Penjualan berhasil disimpan!');
            return response()->json([
                'message' => 'Penjualan berhasil disimpan!',
                'total'   => $jumlahBayar
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan penjualan!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }    
    public function getUpdatedProducts()
    {
        try {
            $user = Auth::user();
    
            // Pastikan user memiliki outlet
            if (!$user->id_outlet) {
                return response()->json([
                    'message' => 'Anda tidak memiliki outlet terkait.'
                ], 403);
            }
    
            // Ambil produk dengan filter status dan urutan harga
            $products = Product::where('id_outlet', $user->id_outlet)
                ->where('status', 'aktif') // Tambahkan filter status
                ->orderBy('harga_produk', 'asc') // Tambahkan urutan harga
                ->get();
    
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data produk!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
