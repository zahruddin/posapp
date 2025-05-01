<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Outlet;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ExpenseController extends Controller
{
    //
    public function index(Request $request, $id) {
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

        $expenses = Expense::with('category')
                    ->where('outlet_id', $id)
                    ->whereBetween('datetime', [$startDate, $endDate]) // Tambahkan filter tanggal
                    ->orderBy('datetime', 'desc')
                    ->paginate(10);
        $kategori_pengeluaran = ExpenseCategory::where('outlet_id',$id)->get();

                    // dd($sales);
        return view('admin.expense', compact('expenses', 'kategori_pengeluaran','tanggal' ,'outlet', 'startDate', 'endDate', 'outlet'));
    }
    public function store(Request $request,  $id_outlet)
    {
        // Ambil outlet
        $outlet_id =  $id_outlet;

        // Validasi dasar tanpa validasi exists dulu
        $request->validate([
            'biaya' => 'required|numeric|max:10000000',
            'keterangan' => 'nullable|string',
            'expense_category_id' => 'nullable|string', // sementara string dulu karena bisa "tambah-baru"
            'kategori_baru' => 'nullable|string|max:255',
        ]);

        // Cek apakah memilih tambah kategori baru
        if ($request->expense_category_id === 'tambah-baru') {

            if (!$request->kategori_baru) {
                return back()->withErrors(['kategori_baru' => 'Nama kategori baru harus diisi.'])->withInput();
            }

            // Cek apakah kategori baru sudah ada di outlet
            $kategoriSudahAda = ExpenseCategory::where('outlet_id', $outlet_id)
                ->where('nama_kategori', $request->kategori_baru)
                ->exists();

            if ($kategoriSudahAda) {
                return back()->withErrors(['kategori_baru' => 'Kategori sudah ada.'])->withInput();
            }

            // Simpan kategori baru
            $kategoriBaru = ExpenseCategory::create([
                'nama_kategori' => $request->kategori_baru,
                'outlet_id'     => $outlet_id,
            ]);

            $category_id = $kategoriBaru->id;

        } else {
            // Validasi manual id kategori (harus numeric dan ada di database)
            if (!is_numeric($request->expense_category_id)) {
                return back()->withErrors(['expense_category_id' => 'Kategori tidak valid.'])->withInput();
            }

            $kategoriValid = ExpenseCategory::where('id', $request->expense_category_id)
                ->where('outlet_id', $outlet_id)
                ->exists();

            if (!$kategoriValid) {
                return back()->withErrors(['expense_category_id' => 'Kategori tidak ditemukan.'])->withInput();
            }

            $category_id = $request->expense_category_id;
        }

        // Simpan pengeluaran
        Expense::create([
            'outlet_id'           => $outlet_id,
            'user_id'             => Auth::id(),
            'expense_category_id' => $category_id,
            'biaya'               => $request->biaya,
            'keterangan'          => $request->keterangan,
            'datetime'            => now(),
        ]);

        return back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }
    public function destroy($id_outlet, $id_expense)
    {
        try {
            // Cari produk berdasarkan id_outlet dan id
            $expense = Expense::where('outlet_id', $id_outlet)->where('id', $id_expense)->firstOrFail();
    
            // Hapus produk
            $expense->delete();
            session()->flash('success', 'Product berhasil dihapus!');
            return response()->json(['success' => 'Pengeluaran berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
