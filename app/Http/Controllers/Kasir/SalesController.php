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
    public function boot()
    {
        Paginator::useBootstrap();
    }

    public function showHalamanKasir() 
    {
        $user = auth()->user();

        if (!$user->id_outlet) {
            return redirect()->back()->with('error', 'Anda tidak memiliki outlet terkait.');
        }

        $products = Product::where('id_outlet', $user->id_outlet)
                         ->where('status', 'aktif')
                         ->get();

        return view('kasir.halamankasir', compact('products'));
    }
    
    public function tambahPenjualan(Request $request)
    {
        $cartItems = $request->input('cart');
        $paymentMethod = $request->input('paymentMethod', 'cash');
    
        if (empty($cartItems)) {
            return response()->json(['message' => 'Keranjang kosong!'], 400);
        }
    
        try {
            DB::beginTransaction();

            // Validate stock availability first
            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan!");
                }
                if ($product->stok_produk < $item['qty']) {
                    throw new \Exception("Stok untuk {$product->nama_produk} tidak mencukupi!");
                }
            }
    
            // Calculate totals
            $totalHarga = 0;
            $totalDiskon = 0;
            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $diskon = $item['discount'] ?? 0;
                $totalHarga += $subtotal;
                $totalDiskon += $diskon;
            }
    
            $jumlahBayar = $totalHarga - $totalDiskon;
    
            // Create sale record
            $sale = Sale::create([
                'id_outlet'    => Auth::user()->id_outlet,
                'id_user'      => Auth::id(),
                'total_harga'  => $totalHarga,
                'total_diskon' => $totalDiskon,
                'total_bayar'  => $jumlahBayar,
                'metode_bayar' => $paymentMethod,
                'status_bayar' => 'lunas'
            ]);
    
            // Create sale details and update stock in bulk
            $saleDetails = [];
            $stockUpdates = [];
            
            foreach ($cartItems as $item) {
                // Prepare sale detail
                $saleDetails[] = [
                    'id_sale'      => $sale->id,
                    'id_produk'    => $item['id'],
                    'nama_produk'  => $item['name'],
                    'harga_produk' => $item['price'],
                    'jumlah'       => $item['qty'],
                    'subtotal'     => $item['price'] * $item['qty'],
                    'diskon'       => $item['discount'] ?? 0,
                    'total'        => ($item['price'] * $item['qty']) - ($item['discount'] ?? 0)
                ];

                // Update stock using raw query to prevent additional model events
                DB::table('products')
                    ->where('id', $item['id'])
                    ->decrement('stok_produk', $item['qty']);
            }

            // Insert all sale details at once
            SalesDetail::insert($saleDetails);
    
            // Create payment record
            Payment::create([
                'id_sale'      => $sale->id,
                'metode_bayar' => $paymentMethod,
                'jumlah_bayar' => $jumlahBayar,
                'kembalian'    => 0,
                'status'       => 'berhasil'
            ]);
    
            DB::commit();

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
    
            if (!$user->id_outlet) {
                return response()->json([
                    'message' => 'Anda tidak memiliki outlet terkait.'
                ], 403);
            }
    
            $products = Product::where('id_outlet', $user->id_outlet)
                ->where('status', 'aktif')
                ->orderBy('harga_produk', 'asc')
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
