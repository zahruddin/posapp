<?php

namespace App\Http\Controllers\admin;

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

class PenjualanController extends Controller
{
    //
    public function dataPenjualan(Request $request, $id)
    {
        $outlet = Outlet::findOrFail($id);

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

        $sales = Sale::where('id_outlet', $id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with(['outlet', 'user', 'details.product']) 
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    // dd($sales);
        return view('admin.datasales', compact('sales', 'tanggal' ,'outlet', 'startDate', 'endDate', 'outlet'));
    }
}
