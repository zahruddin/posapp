<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesDetail;
// use App\Models\Payment;
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
                            // ->orderBy('harga_produk', 'asc') // Urutkan harga dari yang terendah
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

    // public function tambahPenjualan(Request $request)
    // {
    //     $cartItems = collect($request->input('cart'));
    //     $paymentMethod = $request->input('paymentMethod', 'cash');

    //     if ($cartItems->isEmpty()) {
    //         return response()->json(['message' => 'Keranjang kosong!'], 400);
    //     }

    //     // Ambil semua ID produk dari keranjang, lalu ambil datanya sekaligus
    //     $productIds = $cartItems->pluck('id')->all();
    //     $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

    //     $totalHarga = 0;
    //     $totalDiskon = 0;
    //     $salesDetails = [];

    //     foreach ($cartItems as $item) {
    //         $product = $products[$item['id']] ?? null;

    //         if (!$product) {
    //             return response()->json(['message' => "Produk dengan ID {$item['id']} tidak ditemukan!"], 404);
    //         }

    //         if ($product->stok_produk < $item['qty']) {
    //             return response()->json(['message' => "Stok untuk {$product->nama_produk} tidak mencukupi!"], 400);
    //         }

    //         $qty = $item['qty'];
    //         $price = $item['price'];
    //         $discount = $item['discount'] ?? 0;
    //         $subtotal = $price * $qty;
    //         $total = $subtotal - $discount;

    //         $totalHarga += $subtotal;
    //         $totalDiskon += $discount;

    //         $salesDetails[] = [
    //             'id_produk'    => $product->id,
    //             'nama_produk'  => $product->nama_produk,
    //             'harga_produk' => $price,
    //             'jumlah'       => $qty,
    //             'subtotal'     => $subtotal,
    //             'diskon'       => $discount,
    //             'total'        => $total
    //         ];
    //     }

    //     $jumlahBayar = $totalHarga - $totalDiskon;

    //     try {
    //         DB::beginTransaction();

    //         $sale = Sale::create([
    //             'id_outlet'    => Auth::user()->id_outlet,
    //             'id_user'      => Auth::id(),
    //             'total_harga'  => $totalHarga,
    //             'total_diskon' => $totalDiskon,
    //             'total_bayar'  => $jumlahBayar,
    //             'metode_bayar' => $paymentMethod,
    //             'status_bayar' => 'lunas'
    //         ]);

    //         foreach ($salesDetails as $detail) {
    //             SalesDetail::create(array_merge($detail, ['id_sale' => $sale->id]));

    //             // Update stok produk langsung dari koleksi yang sudah diambil sebelumnya
    //             $products[$detail['id_produk']]->decrement('stok_produk', $detail['jumlah']);
    //         }

    //         DB::commit();

    //         session()->flash('success', 'Penjualan berhasil disimpan!');
    //         return response()->json([
    //             'message' => 'Penjualan berhasil disimpan!',
    //             'total'   => $jumlahBayar
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Terjadi kesalahan saat menyimpan penjualan!',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    
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
