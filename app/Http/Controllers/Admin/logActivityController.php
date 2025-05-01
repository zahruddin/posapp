<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Carbon\Carbon;


class logActivityController extends Controller
{
    //
    public function index(Request $request)
    {
        $logs = ActivityLog::query();

        // Jika tidak ada input dari user, default ke 1 bulan terakhir
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // Konversi ke Carbon format lengkap
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Filter berdasarkan rentang tanggal
        $logs->whereBetween('created_at', [$startDate, $endDate]);

        // Filter pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $logs->where(function ($query) use ($search) {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Ambil data dengan relasi user dan urutan terbaru
        $logs = $logs->with('user')->latest()->paginate(10);

        return view('admin.logActivity', compact('logs', 'startDate', 'endDate'));
    }


}
