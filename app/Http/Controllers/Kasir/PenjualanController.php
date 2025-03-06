<?php

namespace App\Http\Controllers\Kasir;

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
    public function dataPenjualan(Request $request)
    {
        // Ambil tanggal dari request, jika tidak ada gunakan tanggal hari ini (format Y-m-d)
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
    
        $sales = Sale::where('id_user', Auth::id())
                    ->whereDate('created_at', Carbon::parse($tanggal)->format('Y-m-d')) // Konversi ke format yang benar
                    ->with(['outlet', 'user', 'details.product']) 
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
    
        return view('kasir.datasales', compact('sales', 'tanggal'));
    }
    
}
