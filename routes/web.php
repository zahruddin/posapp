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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); // Halaman login
Route::post('/login', [LoginController::class, 'login']); // Proses login

// logout
Route::post('/logout', function () {
    Auth::logout();  // Menjalankan proses logout
    return redirect()->route('login');  // Mengarahkan pengguna ke halaman login setelah logout
})->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    // home dashboard
    Route::get('/admin', function() { return redirect()->route('admin.dashboard'); })->name('redirect.admin.dashboard'); 
    Route::get('/admin/dashboard', [Admin\DashboardController::class, 'showDashboard'])->name('admin.dashboard'); 
    // Outlet
    Route::get('/admin/kelolaoutlet', [Admin\KelolaOutletController::class, 'showKelolaOutlet'])->name('admin.kelolaOutlet'); 
    Route::get('/admin/kelolaoutlet/id/{id}', [Admin\DashboardController::class, 'showDashboardOutlet'])->name('admin.dashboardOutlet');
    Route::post('/admin/kelolaoutlet/tambahoutlet', [Admin\KelolaOutletController::class, 'tambahOutlet'])->name('admin.tambahOutlet');
    Route::post('/admin/kelolaoutlet/hapusoutlet/{id}', [Admin\KelolaOutletController::class, 'hapusOutlet'])->name('admin.deleteOutlet');

    // Outlet Produk
    Route::get('/admin/kelolaoutlet/id/{id}/products', [Admin\KelolaProductsController::class, 'showProducts'])->name('admin.productsOutlet');
    Route::post('/admin/kelolaoutlet/id/{id_outlet}/tambahproduct', [Admin\KelolaProductsController::class, 'tambahProduk'])->name('admin.tambahProduct'); 
    Route::post('/admin/kelolaUsers/editproduct', [Admin\KelolaUsersController::class, 'tambahUser'])->name('admin.editProduct'); 
    Route::post('/admin/kelolaoutlet/id/{id_outlet}/products/{id_product}', [Admin\KelolaProductsController::class, 'hapusProduct'])->name('admin.deleteProduct');
    
    // outlet Users
    Route::get('/admin/kelolaoutlet/id/{id}/kasir', [Admin\KelolaUsersController::class, 'showUsersOutlet'])->name('admin.kasirOutlet');

    Route::get('/admin/kelolaUsers', [Admin\KelolaUsersController::class, 'showUsers'])->name('admin.kelolaUsers'); 
    Route::get('/admin/kelolaUsers/edit', [Admin\KelolaUsersController::class, 'showUsers'])->name('admin.editUser'); 
    Route::post('/admin/kelolaUsers/hapususer/{id}', [Admin\KelolaUsersController::class, 'hapusUser'])->name('admin.deleteUser');
    Route::post('/admin/kelolaUsers/tambahuser', [Admin\KelolaUsersController::class, 'tambahUser'])->name('admin.tambahUser'); 
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir', function() { return redirect()->route('kasir.sales'); })->name('redirect.kasir.sales'); 
    Route::get('/kasir/dashboard', [Kasir\DashboardController::class, 'showDashboard'])->name('kasir.dashboard');
    Route::get('/kasir/sales', [Kasir\SalesController::class, 'showHalamanKasir'])->name('kasir.sales');
    Route::post('/kasir/sales', [Kasir\SalesController::class, 'tambahPenjualan'])->name('kasir.sales.addsales');
    Route::get('/kasir/datasales', [Kasir\PenjualanController::class, 'dataPenjualan'])->name('kasir.datasales');
    Route::get('/kasir/kelolaproduk', [Kasir\KelolaProductsController::class, 'showKelolaProduk'])->name('kasir.kelolaproduk');
    Route::post('/kasir/kelolaproduk/tambahproduk', [Kasir\KelolaProductsController::class, 'tambahProduct'])->name('kasir.tambahProduct');
    Route::post('/kasir/kelolaproduk/hapusproduk/{id}', [Kasir\KelolaProductsController::class, 'hapusProduct'])->name('kasir.hapusProduct');
    Route::put('/kasir/kelolaproduk/editproduk/{id}', [Kasir\KelolaProductsController::class, 'updateProduk'])->name('kasir.updateProduk');
    Route::get('/api/products', [Kasir\SalesController::class, 'getUpdatedProducts'])->name('api.dataproduk');
});
