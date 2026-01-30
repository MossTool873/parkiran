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
use App\Http\Controllers\TipeKendaraanController;
use App\Http\Controllers\TarifTipeKendaraanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UsersController;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', function () {return redirect('/admin/users');});
    Route::resource('users', UsersController::class);
    Route::resource('areaParkir', AreaParkirController::class);
    Route::resource('tipeKendaraan', KendaraanTipeController::class);
    Route::resource('tarifTipeKendaraan', TarifTipeKendaraanController::class);
    Route::resource('kendaraan', KendaraanController::class);
});

Route::prefix('petugas')->middleware(['auth', 'role:petugas,admin'])->group(function () {
    Route::get('/', function () {return redirect('/petugas/transaksi');});
    Route::get('/transaksi',[TransaksiController::class, 'index'])->name('transaksi');
    Route::post('/transaksi/masuk', [TransaksiController::class, 'masuk']);
    Route::post('/transaksi/keluar', [TransaksiController::class, 'keluar']);
    Route::get('/riwayatTransaksi', [TransaksiController::class, 'riwayat']);
    Route::get('/transaksi-aktif', [TransaksiController::class, 'aktif'])->name('transaksi.aktif');
});

Route::get('/kendaraan/search', [KendaraanController::class, 'search'])->name('kendaraan.search');


