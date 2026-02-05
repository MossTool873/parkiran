<?php

use Illuminate\Support\Facades\Route;

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


use App\Http\Controllers\AuthController;
use App\Http\Controllers\AreaParkirController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KendaraanTipeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipTierController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\TipeKendaraanController;
use App\Http\Controllers\TarifTipeKendaraanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UsersController;
use App\Models\Kendaraan;
use App\Models\MetodePembayaran;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/ganti-password', [AuthController::class, 'gantiPaswordForm']);
Route::post('/ganti-password', [AuthController::class, 'updatePassword']);

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', function () {
        return redirect('/admin/users');
    });
    Route::resource('users', UsersController::class);
    Route::resource('areaParkir', AreaParkirController::class);
    Route::resource('tipeKendaraan', KendaraanTipeController::class);
    Route::resource('tarifTipeKendaraan', TarifTipeKendaraanController::class);
    Route::resource('kendaraan', KendaraanController::class);
    Route::resource('metodePembayaran', MetodePembayaranController::class);
    Route::resource('membership-tier', MembershipTierController::class);
    Route::resource('membership', MembershipController::class);
});

Route::prefix('petugas')->middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/', function () {
        return redirect('/petugas/transaksi');
    });
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('/transaksi/masuk', [TransaksiController::class, 'masuk']);
    Route::post('/transaksi/keluar', [TransaksiController::class, 'keluar']);
    Route::get('/riwayatTransaksi', [TransaksiController::class, 'riwayat']);
    Route::get('/transaksi-aktif', [TransaksiController::class, 'transaksiAktif'])->name('transaksi.transaksiAktif');
});

Route::prefix('laporan')->middleware(['auth', 'role:admin,owner'])->group(function () {
    Route::get('/harian', [LaporanController::class, 'laporanHariIni']);
    Route::get('/periode', [LaporanController::class, 'laporanPeriode']);
    Route::post('/periode', [LaporanController::class, 'laporanPeriode']);
    Route::get('/occupancy', [LaporanController::class, 'occupancy']);
});

Route::get('/kendaraan/search', [KendaraanController::class, 'search'])->name('kendaraan.search');
Route::get('/kendaraan/search', function (\Illuminate\Http\Request $request) {
    return Kendaraan::where('plat_nomor', 'like', "%{$request->q}%")->with('tipeKendaraan')->limit(10)->get();
})->name('kendaraan.search');

