<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Kasir;
// use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::middleware('web')->group(function () {

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Gunakan controller logout milikmu
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Backup
    Route::get('/backup-database', [Admin\BackupController::class, 'backup'])->name('backup.database');
    // home dashboard
    Route::get('/admin', function() { return redirect()->route('admin.dashboard'); })->name('redirect.admin.dashboard'); 
    Route::get('/admin/dashboard', [Admin\DashboardController::class, 'showDashboard'])->name('admin.dashboard'); 
    // Outlet
    Route::get('/admin/kelolaoutlet', [Admin\KelolaOutletController::class, 'showKelolaOutlet'])->name('admin.kelolaOutlet'); 
    Route::get('/admin/kelolaoutlet/id/{id}', [Admin\DashboardController::class, 'showDashboardOutlet'])->name('admin.dashboardOutlet');
    Route::post('/admin/kelolaoutlet/tambahoutlet', [Admin\KelolaOutletController::class, 'tambahOutlet'])->name('admin.tambahOutlet');
    Route::post('/admin/kelolaoutlet/hapusoutlet/{id}', [Admin\KelolaOutletController::class, 'hapusOutlet'])->name('admin.deleteOutlet');
    Route::put('/admin/kelolaoutlet/editoutlet/{id}', [Admin\KelolaOutletController::class, 'updateOutlet'])->name('admin.updateOutlet');
    
    // Outlet Produk
    Route::get('/admin/kelolaoutlet/id/{id}/products', [Admin\KelolaProductsController::class, 'showProducts'])->name('admin.productsOutlet');
    Route::post('/admin/kelolaoutlet/id/{id_outlet}/tambahproduct', [Admin\KelolaProductsController::class, 'tambahProduk'])->name('admin.tambahProduct'); 
    Route::post('/admin/kelolaUsers/editproduct', [Admin\KelolaUsersController::class, 'tambahUser'])->name('admin.editProduct'); 
    Route::post('/admin/kelolaoutlet/id/{id_outlet}/products/{id_product}', [Admin\KelolaProductsController::class, 'hapusProduct'])->name('admin.deleteProduct');
    Route::put('/admin/kelolaoutlet/products/update/{id_product}', [Admin\KelolaProductsController::class, 'updateProduct'])->name('admin.updateProduct');
    //sales outlet
    Route::get('/admin/kelolaoutlet/id/{id}/penjualan', [Admin\PenjualanController::class, 'dataPenjualan'])->name('admin.datasales');
    
    // outlet Users
    Route::get('/admin/kelolaoutlet/id/{id}/kasir', [Admin\KelolaUsersController::class, 'showUsersOutlet'])->name('admin.kasirOutlet');
    // pengeluaran expense
    Route::get('/admin/kelolaoutlet/id/{id}/expense', [Admin\ExpenseController::class, 'index'])->name('admin.expense');
    Route::post('/admin/kelolaoutlet/id/{id_outlet}/expense', [Admin\ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::post('/admin/expenses/id/{id_outlet}/{id_expense}', [Admin\ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
    
    // Laporan penyeduhan 
    Route::get('/admin/kelolaoutlet/id/{id}/laporanpenyeduhan', [Admin\SeduhController::class, 'index'])->name('admin.seduh');
    Route::post('/admin/kelolaoutlet/id/{id}/laporanpenyeduhan', [Admin\SeduhController::class, 'store'])->name('admin.seduh.store');
    Route::delete('/admin/kelolaoutlet/id/{id}/laporanpenyeduhan/{id_seduh}', [Admin\SeduhController::class, 'destroy'])->name('admin.seduh.destroy');

    Route::get('/admin/kelolaUsers', [Admin\KelolaUsersController::class, 'showUsers'])->name('admin.kelolaUsers'); 
    Route::get('/admin/kelolaUsers/edit', [Admin\KelolaUsersController::class, 'showUsers'])->name('admin.editUser'); 
    Route::post('/admin/kelolaUsers/hapususer/{id}', [Admin\KelolaUsersController::class, 'hapusUser'])->name('admin.deleteUser');
    Route::post('/admin/kelolaUsers/tambahuser', [Admin\KelolaUsersController::class, 'tambahUser'])->name('admin.tambahUser'); 
    Route::put('/admin/kelolauser/update/{id}', [Admin\KelolaUsersController::class, 'updateUser'])->name('admin.updateUser'); 
    
    // Profile
    Route::get('/admin/profile', [Admin\ProfileController::class, 'showProfile'])->name('admin.profile'); 
    Route::put('/admin/profile', [Admin\ProfileController::class, 'updateProfile'])->name('admin.profile.update'); 
    // log


    Route::get('/admin/log', [Admin\logActivityController::class, 'index'])->name('admin.log'); 
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir', function() { return redirect()->route('kasir.sales'); })->name('redirect.kasir.sales'); 
    Route::get('/kasir/dashboard', [Kasir\DashboardController::class, 'showDashboard'])->name('kasir.dashboard');
    Route::get('/kasir/sales', [Kasir\SalesController::class, 'showHalamanKasir'])->name('kasir.sales');
    Route::post('/kasir/sales', [Kasir\SalesController::class, 'tambahPenjualan'])->name('kasir.sales.addsales');
    Route::get('/kasir/datasales', [Kasir\PenjualanController::class, 'dataPenjualan'])->name('kasir.datasales');
    Route::get('/kasir/kelolaproduk', [Kasir\KelolaProductsController::class, 'showKelolaProduk'])->name('kasir.kelolaproduk');
    // Route::post('/kasir/kelolaproduk/tambahproduk', [Kasir\KelolaProductsController::class, 'tambahProduct'])->name('kasir.tambahProduct');
    // Route::post('/kasir/kelolaproduk/hapusproduk/{id}', [Kasir\KelolaProductsController::class, 'hapusProduct'])->name('kasir.hapusProduct');
    Route::put('/kasir/kelolaproduk/editproduk/{id}', [Kasir\KelolaProductsController::class, 'updateProduk'])->name('kasir.updateProduk');
    Route::get('/api/products', [Kasir\SalesController::class, 'getUpdatedProducts'])->name('api.dataproduk');
    
    Route::get('/kasir/profile', [Kasir\ProfileController::class, 'index'])->name('kasir.profile'); 
    Route::put('/kasir/profile', [Kasir\ProfileController::class, 'update'])->name('kasir.profile.update'); 

    Route::get('/kasir/expenses', [Kasir\ExpenseController::class, 'index'])->name('kasir.pengeluaran');
    Route::post('/kasir/expenses', [Kasir\ExpenseController::class, 'store'])->name('kasir.expenses.store');
    Route::delete('/kasir/expenses/{id}', [Kasir\ExpenseController::class, 'destroy'])->name('kasir.expenses.destroy');


    Route::get('/kasir/laporanseduh', [Kasir\SeduhController::class, 'index'])->name('kasir.seduh');
    Route::post('/kasir/laporanseduh', [Kasir\SeduhController::class, 'store'])->name('kasir.seduh.store');
    Route::delete('/kasir/laporanseduh/{id}', [Kasir\SeduhController::class, 'destroy'])->name('kasir.seduh.destroy');
    // Route::put('/kasir/expenses/{id}', [Kasir\ExpenseController::class, 'update'])->name('kasir.expenses.update');

});
