<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\SalesDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function showDashboard(Request $request)
    {
        // Ambil tanggal dari request, jika tidak ada gunakan hari ini
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Total transaksi pada tanggal yang dipilih
        $totalTransaksi = Sale::whereDate('created_at', $tanggal)->count();

        // Total item terjual pada tanggal yang dipilih
        $totalItemTerjual = SalesDetail::whereDate('created_at', $tanggal)->sum('jumlah');

        // Total pendapatan pada tanggal yang dipilih
        $totalPendapatan = Sale::whereDate('created_at', $tanggal)->sum('total_harga');


        return view('kasir.dashboard', compact('totalTransaksi', 'totalItemTerjual', 'totalPendapatan', 'tanggal'));
    }
}
