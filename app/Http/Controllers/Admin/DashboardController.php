<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\Sale;
use App\Models\SalesDetail;
use Carbon\Carbon;



class DashboardController extends Controller
{
    //
    public function showDashboard()
    {
        
        return view('admin.dashboard');
    }
    public function showDashboardOutlet(Request $request, $id)
    {
        // Ambil data outlet berdasarkan ID
        $outlet = Outlet::findOrFail($id);
    
        // Ambil tanggal mulai dan tanggal akhir dari input, default ke hari ini
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
    
        // Konversi ke format Carbon
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
    
        // Total transaksi dalam rentang waktu yang dipilih
        $totalTransaksi = Sale::where('id_outlet', $outlet->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    
        // Total item terjual dalam rentang waktu yang dipilih
        $totalItemTerjual = SalesDetail::whereHas('sale', function ($query) use ($outlet, $startDate, $endDate) {
            $query->where('id_outlet', $outlet->id)
                  ->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('jumlah');
    
        // Total pendapatan dalam rentang waktu yang dipilih
        $totalPendapatan = Sale::where('id_outlet', $outlet->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');
    
        return view('admin.dashboardoutlet', compact('totalTransaksi', 'totalItemTerjual', 'totalPendapatan', 'startDate', 'endDate', 'outlet'));
    }
    
    
    


}
