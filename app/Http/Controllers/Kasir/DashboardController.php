<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\Expense;
use App\Models\SalesDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function showDashboard(Request $request)
    {
        // Ambil ID user yang sedang login
        $idUser = auth()->id(); // Mendapatkan ID user yang sedang login
        
        // Ambil tanggal mulai dan tanggal akhir dari input, default ke hari ini
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
    
        // Konversi ke format Carbon
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        // Ambil tanggal dari request, jika tidak ada gunakan hari ini
        // $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Total transaksi oleh user yang login pada tanggal yang dipilih
        $totalTransaksi = Sale::where('id_user', $idUser)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Total item terjual oleh user yang login pada tanggal yang dipilih
        $totalItemTerjual = SalesDetail::whereHas('sale', function ($query) use ($idUser, $startDate, $endDate) {
            $query->where('id_user', $idUser)
                ->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('jumlah');

        // Total pendapatan oleh user yang login pada tanggal yang dipilih
        $totalPendapatan = Sale::where('id_user', $idUser)
            ->where('status_bayar', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');
        // Total pendapatan oleh user yang login pada tanggal yang dipilih
        $totalPendapatanCash = Sale::where('id_user', $idUser)->where('metode_bayar', 'cash')->where('status_bayar', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');
        $totalPendapatanQris = Sale::where('id_user', $idUser)->where('metode_bayar', 'qris')->where('status_bayar', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');
        // Total pengeluaran oleh user yang login pada tanggal yang dipilih
        $totalPengeluaran = Expense::where('user_id', $idUser)
        ->whereBetween('datetime', [$startDate, $endDate])
        ->sum('biaya');

        return view('kasir.dashboard', compact(
            'totalTransaksi', 
            'totalItemTerjual', 
            'totalPendapatan', 
            'totalPendapatanCash',
            'totalPendapatanQris',
            'totalPengeluaran', 
            'startDate', 
            'endDate'
        ));        
    }

    
}
