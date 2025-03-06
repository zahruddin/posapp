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
        // Ambil ID user yang sedang login
        $idUser = auth()->id(); // Mendapatkan ID user yang sedang login

        // Ambil tanggal dari request, jika tidak ada gunakan hari ini
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Total transaksi oleh user yang login pada tanggal yang dipilih
        $totalTransaksi = Sale::where('id_user', $idUser)
            ->whereDate('created_at', $tanggal)
            ->count();

        // Total item terjual oleh user yang login pada tanggal yang dipilih
        $totalItemTerjual = SalesDetail::whereHas('sale', function ($query) use ($idUser, $tanggal) {
            $query->where('id_user', $idUser)
                ->whereDate('created_at', $tanggal);
        })->sum('jumlah');

        // Total pendapatan oleh user yang login pada tanggal yang dipilih
        $totalPendapatan = Sale::where('id_user', $idUser)
            ->whereDate('created_at', $tanggal)
            ->sum('total_harga');

        return view('kasir.dashboard', compact('totalTransaksi', 'totalItemTerjual', 'totalPendapatan', 'tanggal'));
    }

    
}
