<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanSeduh;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class SeduhController extends Controller
{
    //
    public function index(Request $request) {
        $user = Auth::user();

        // Ambil tanggal mulai dan akhir dari input; default ke hari ini
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Konversi ke format Carbon
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

         // Ambil data pengeluaran berdasarkan outlet user dan filter tanggal
         $laporanseduh = LaporanSeduh::where('id_user', $user->id)
         ->whereBetween('created_at', [$startDate, $endDate])// Tambahkan filter tanggal
         ->orderBy('created_at', 'desc')
         ->paginate(10);

        return view('kasir.seduh', compact('laporanseduh', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'seduh' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if ($value * 10 % 5 !== 0) {
                    $fail('Jumlah seduh harus kelipatan 0.5.');
                }
            }],
            'keterangan' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Simpan data ke database
        LaporanSeduh::create([
            'id_outlet' => $user->id_outlet, // asumsi kolom ini ada di tabel users
            'id_user' => $user->id,
            'seduh' => $request->input('seduh'),
            'keterangan' => $request->input('keterangan'),
        ]);

        return redirect()->back()->with('success', 'Laporan seduh berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $laporan = LaporanSeduh::findOrFail($id);

        // Optional: pastikan user hanya bisa menghapus data miliknya
        if ($laporan->id_user !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus data ini.');
        }

        $laporan->delete();

        return redirect()->route('kasir.seduh')->with('success', 'Laporan seduh berhasil dihapus.');
    }


}
