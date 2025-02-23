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
    Route::get('/admin/kelolaOutlet', [Admin\KelolaOutletController::class, 'showKelolaOutlet'])->name('admin.kelolaOutlet'); 
    Route::get('/admin/kelolaOutlet/{id}/dashboard', [Admin\DashboardController::class, 'showDashboard'])->name('admin.dashboardOutlet');
    Route::post('/admin/kelolaOutlet/tambahoutlet', [Admin\KelolaOutletController::class, 'tambahOutlet'])->name('admin.tambahOutlet');
    // Outlet Produk
    Route::get('/admin/kelolaOutlet/produk', [Admin\KelolaOutletController::class, 'showProduk'])->name('admin.outlet.produk');
    Route::post('/admin/kelolaOutlet/tambahproduk', [Admin\KelolaOutletController::class, 'tambahProduk'])->name('admin.outlet.tambahProduk');
    // outlet Users
    Route::get('/admin/kelolaUsers', [Admin\KelolaUsersController::class, 'showUsers'])->name('admin.kelolaUsers'); 
    Route::get('/admin/kelolaUsers/edit', [Admin\KelolaUsersController::class, 'showUsers'])->name('admin.editUser'); 
    Route::post('/admin/kelolaUsers/hapususer/{id}', [Admin\KelolaUsersController::class, 'hapusUser'])->name('admin.deleteUser');
    Route::post('/admin/kelolaUsers/tambahuser', [Admin\KelolaUsersController::class, 'tambahUser'])->name('admin.tambahUser'); 
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir', function() { return redirect()->route('kasir.dashboard'); })->name('redirect.kasir.dashboard'); 
    Route::get('/kasir/dashboard', [Kasir\DashboardController::class, 'showDashboard'])->name('kasir.dashboard');
});
