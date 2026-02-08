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

use App\Http\Controllers\BackupDatabaseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AreaParkirController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KendaraanMemberController;
use App\Http\Controllers\KendaraanMembershipController;
use App\Http\Controllers\KendaraanTipeController;
use App\Http\Controllers\KonfigurasiTarifController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipTierController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\TarifDurasiController;
use App\Http\Controllers\TipeKendaraanController;
use App\Http\Controllers\TarifTipeKendaraanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UsersController;
use App\Models\Kendaraan;
use App\Models\MetodePembayaran;

Route::get('/', function () {return redirect('login');});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/ganti-password', [AuthController::class, 'gantiPaswordForm']);
Route::post('/ganti-password', [AuthController::class, 'updatePassword']);

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/', function () {return redirect('/admin/users');});
    Route::resource('users', UsersController::class);
    Route::resource('areaParkir', AreaParkirController::class);
    Route::resource('tipeKendaraan', KendaraanTipeController::class);
    Route::resource('tarifTipeKendaraan', TarifTipeKendaraanController::class);
    Route::resource('tarif-durasi', TarifDurasiController::class);
    Route::get('/konfigurasi-tarif', [KonfigurasiTarifController::class, 'index'])->name('konfigurasi-tarif.index');
    Route::post('/konfigurasi-tarif', [KonfigurasiTarifController::class, 'update'])->name('konfigurasi-tarif.update');
    Route::resource('kendaraan', KendaraanController::class);
    Route::resource('metodePembayaran', MetodePembayaranController::class);
    Route::resource('membership-tier', MembershipTierController::class);
    Route::resource('membership', MembershipController::class);
    Route::get('/database/index',[BackupDatabaseController::class,'index']);
    Route::post('/database/backup', [BackupDatabaseController::class, 'download'])->name('database.backup');
    Route::post('/database/restore', [BackupDatabaseController::class, 'restore'])->name('database.restore');
    Route::get('/membership-kendaraan', [KendaraanMemberController::class, 'index']);
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);
});


Route::prefix('petugas')->middleware(['auth','role:petugas'])->group(function () {

    Route::get('/', fn()=>redirect('/petugas/transaksi'));
    Route::get('/transaksi', [TransaksiController::class,'index'])->name('transaksi');
    Route::post('/transaksi/masuk', [TransaksiController::class,'masuk'])->name('transaksi.masuk');
    Route::post('/transaksi/keluar', [TransaksiController::class,'keluar'])->name('keluar.hitung');
    Route::post('/transaksi/keluar/bayar', [TransaksiController::class,'bayar'])->name('keluar.bayar');
    Route::get('/transaksi-aktif',[TransaksiController::class,'transaksiAktif'])->name('transaksi.transaksiAktif');
    Route::post('/transaksi/keluar/batal', function(){
        session()->forget('draft_keluar');
        session()->forget('struk_keluar');
        return back();
    })->name('keluar.batal');
});

Route::prefix('owner')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', function () {return redirect('/laporan/harian');});
});

Route::prefix('laporan')->middleware(['auth', 'role:admin,owner'])->group(function () {
    Route::get('/harian', [LaporanController::class, 'laporanHariIni']);
    Route::get('/periode', [LaporanController::class, 'laporanPeriode']);
    Route::post('/periode', [LaporanController::class, 'laporanPeriode']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('laporan/occupancy', [LaporanController::class, 'occupancy']);
    Route::get('laporan/riwayatTransaksi', [LaporanController::class, 'riwayatTransaksi']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'showTransaksi'])->name('transaksi.show');
    Route::get('/tracking-kendaraan', [KendaraanController::class, 'tracking'])->name('kendaraan.tracking');
    Route::get('/kendaraan/search', [KendaraanController::class, 'search'])->name('kendaraan.search');
});
