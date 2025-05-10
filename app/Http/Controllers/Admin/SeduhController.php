<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanSeduh;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;



class SeduhController extends Controller
{
    //
    public function index(Request $request, $id) {
        $outlet = Outlet::findOrFail($id);
        // dd($id);
        // Ambil tanggal mulai dan tanggal akhir dari input, default ke hari ini
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Konversi ke format Carbon
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $outlet = Outlet::findOrFail($id);
        // Ambil tanggal dari request, jika tidak ada gunakan tanggal hari ini (format Y-m-d)
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $id = (int) $id; // Ubah ke integer sebelum query

        $laporanseduh = LaporanSeduh::where('id_outlet', $id)
                    ->whereBetween('created_at', [$startDate, $endDate])// Tambahkan filter tanggal
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

                    // dd($laporanseduh);
        return view('admin.seduh', compact('laporanseduh','tanggal' ,'outlet', 'startDate', 'endDate'));
    }


    public function destroy($id, $id_seduh)
    {
        $laporan = \App\Models\LaporanSeduh::where('id_outlet', $id)->findOrFail($id_seduh);
        $laporan->delete();

        return redirect()->back()->with('success', 'Laporan berhasil dihapus.');

    }

}
