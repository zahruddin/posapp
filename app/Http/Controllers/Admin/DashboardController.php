<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\Expense;
use App\Models\LaporanSeduh;
use Carbon\Carbon;



class DashboardController extends Controller
{
    //
    public function showDashboard(Request $request)
    {
        // Ambil semua outlet untuk dropdown
        $outlet = Outlet::all();

        // Ambil input tanggal mulai dan akhir, default ke hari ini
        $startDateInput = $request->input('start_date', Carbon::today()->toDateString());
        $endDateInput = $request->input('end_date', Carbon::today()->toDateString());

        // Parsing tanggal menjadi Carbon instance
        $startDate = Carbon::parse($startDateInput)->startOfDay();
        $endDate = Carbon::parse($endDateInput)->endOfDay();

        // Ambil ID outlet yang dipilih
        $outletId = $request->input('outlet_id');

        // Ambil nama outlet yang dipilih (jika ada)
        $namaOutletTerpilih = 'Semua Outlet';
        if ($outletId) {
            $selectedOutlet = Outlet::find($outletId);
            if ($selectedOutlet) {
                $namaOutletTerpilih = $selectedOutlet->nama_outlet;
            }
        }

        // Filter query berdasarkan outlet jika outlet dipilih
        $saleQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $expenseQuery = Expense::whereBetween('created_at', [$startDate, $endDate]);
        $seduhQuery = LaporanSeduh::whereBetween('created_at', [$startDate, $endDate]);
        $salesDetailQuery = SalesDetail::whereHas('sale', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        });

        if ($outletId) {
            $saleQuery->where('id_outlet', $outletId);
            $expenseQuery->where('outlet_id', $outletId);
            $seduhQuery->where('id_outlet', $outletId);
            $salesDetailQuery->whereHas('sale', function ($query) use ($outletId) {
                $query->where('id_outlet', $outletId);
            });
        }

        $totalTransaksi = $saleQuery->count();
        $totalPendapatan = $saleQuery->sum('total_harga');
        $totalItemTerjual = $salesDetailQuery->sum('jumlah');
        $totalPengeluaran = $expenseQuery->sum('biaya');
        $totalSeduh = $seduhQuery->sum('seduh');

        $totalPendapatanCash = $saleQuery->where('metode_bayar', 'cash')->where('status_bayar', 'lunas')->sum('total_harga');
        $totalPendapatanQris = $saleQuery->where('metode_bayar', 'qris')->where('status_bayar', 'lunas')->sum('total_harga');

        // Grafik penjualan harian
        $penjualanHarian = Sale::selectRaw('DATE(created_at) as tanggal, SUM(total_harga) as total')
            ->when($outletId, function ($query) use ($outletId) {
                $query->where('id_outlet', $outletId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->get();

        $tanggal = $penjualanHarian->pluck('tanggal')->toArray();
        $totalPerHari = $penjualanHarian->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            'totalTransaksi',
            'totalItemTerjual',
            'totalPendapatan',
            'totalSeduh',
            'startDate',
            'endDate',
            'outlet',
            'totalPendapatanCash',
            'totalPendapatanQris',
            'totalPengeluaran',
            'tanggal',
            'totalPerHari',
            'namaOutletTerpilih'
        ));
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
            // Total pendapatan oleh user yang login pada tanggal yang dipilih
        $totalPendapatanCash = Sale::where('id_outlet', $outlet->id)->where('metode_bayar', 'cash')->where('status_bayar', 'lunas')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_harga');
        $totalPendapatanQris = Sale::where('id_outlet', $outlet->id)->where('metode_bayar', 'qris')->where('status_bayar', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');
        $totalPengeluaran = Expense::whereBetween('created_at', [$startDate, $endDate])
        ->where('outlet_id', $outlet->id)
        ->sum('biaya');
        $totalSeduh = LaporanSeduh::whereBetween('created_at', [$startDate, $endDate])
        ->where('id_outlet', $outlet->id)
        ->sum('seduh');
        
        
    
        return view('admin.dashboardoutlet', compact(
            'totalTransaksi', 
            'totalItemTerjual', 
            'totalPendapatan', 
            'totalSeduh',
            'startDate', 
            'endDate', 
            'outlet',
            'totalPendapatanCash',
            'totalPendapatanQris',
            'totalPengeluaran'
        ));
    }
    
    
    


}
