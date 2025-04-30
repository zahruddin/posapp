<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ExpenseController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
    
        // Ambil data pengeluaran berdasarkan outlet user yang login
        $expenses = Expense::with('category') // ini ditambahkan
                    ->where('outlet_id', $user->id_outlet)
                    ->orderBy('datetime', 'desc')
                    ->paginate(10);

    
        // Ambil kategori pengeluaran milik outlet user yang login
        $kategori_pengeluaran = ExpenseCategory::where('outlet_id', $user->id_outlet)->get();
    
        return view('kasir.pengeluaran', compact('expenses', 'kategori_pengeluaran'));
    }
    
    public function store(Request $request)
    {
        // Ambil outlet
        $outlet_id = Auth::user()->id_outlet;

        // Validasi dasar tanpa validasi exists dulu
        $request->validate([
            'biaya' => 'required|numeric',
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


    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        // Pastikan user hanya bisa edit data miliknya dan outletnya
        if ($expense->user_id !== Auth::id() && $expense->outlet_id !== Auth::user()->outlet_id) {
            abort(403);
        }

        $request->validate([
            'biaya' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'datetime' => 'required|date',
        ]);

        $expense->update([
            'biaya' => $request->biaya,
            'keterangan' => $request->keterangan,
            'datetime' => $request->datetime,
        ]);

        return back()->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->user_id !== Auth::id() && $expense->outlet_id !== Auth::user()->outlet_id) {
            abort(403);
        }

        $expense->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
