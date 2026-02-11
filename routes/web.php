<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BackupDatabaseController,
    AuthController,
    AreaParkirController,
    KendaraanController,
    KendaraanMemberController,
    KendaraanMembershipController,
    KendaraanTipeController,
    KonfigurasiTarifController,
    LaporanController,
    LogAktivitasController,
    MembershipController,
    MembershipTierController,
    MetodePembayaranController,
    TarifDurasiController,
    TipeKendaraanController,
    TarifTipeKendaraanController,
    TransaksiController,
    UsersController,
    ViewOnlyController
};

Route::get('/', fn()=>redirect('login'));

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/ganti-password', [AuthController::class, 'gantiPaswordForm']);
Route::post('/ganti-password', [AuthController::class, 'updatePassword']);

// ================= ADMIN =================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/', fn()=>redirect('/admin/users'));

    Route::resource('users', UsersController::class)
        ->middleware('log.menu:Users');
    Route::resource('areaParkir', AreaParkirController::class)
        ->middleware('log.menu:Area Parkir');
    Route::resource('tipeKendaraan', KendaraanTipeController::class)
        ->middleware('log.menu:Tipe Kendaraan');
    Route::resource('tarifTipeKendaraan', TarifTipeKendaraanController::class)
        ->middleware('log.menu:Tarif Tipe Kendaraan');
    Route::resource('tarif-durasi', TarifDurasiController::class)
        ->middleware('log.menu:Tarif Durasi');
    Route::get('/konfigurasi-tarif', [KonfigurasiTarifController::class, 'index'])
        ->name('konfigurasi-tarif.index')
        ->middleware('log.menu:Konfigurasi Tarif');
    Route::post('/konfigurasi-tarif', [KonfigurasiTarifController::class, 'update'])
        ->name('konfigurasi-tarif.update');
    Route::resource('kendaraan', KendaraanController::class)
        ->middleware('log.menu:Kendaraan');
    Route::resource('metodePembayaran', MetodePembayaranController::class)
        ->middleware('log.menu:Metode Pembayaran');
    Route::resource('membership-tier', MembershipTierController::class)
        ->middleware('log.menu:Membership Tier');
    Route::resource('membership', MembershipController::class)
        ->middleware('log.menu:Membership');
    Route::get('/database/index',[BackupDatabaseController::class,'index'])
        ->middleware('log.menu:Backup Database');
    Route::post('/database/backup', [BackupDatabaseController::class, 'download'])->name('database.backup');
    Route::post('/database/restore', [BackupDatabaseController::class, 'restore'])->name('database.restore');
    Route::get('/membership-kendaraan', [KendaraanMemberController::class, 'index'])
        ->middleware('log.menu:Membership Kendaraan');
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])
        ->middleware('log.menu:Log Aktivitas');
});

// ================= PETUGAS =================
Route::prefix('petugas')->middleware(['auth','role:petugas'])->group(function () {

    Route::get('/', fn()=>redirect('/petugas/transaksi'));
    Route::get('/transaksi', [TransaksiController::class,'index'])
        ->name('transaksi')
        ->middleware('log.menu:Transaksi');
    Route::post('/transaksi/masuk', [TransaksiController::class,'masuk'])->name('transaksi.masuk');
    Route::post('/transaksi/keluar', [TransaksiController::class,'keluar'])->name('keluar.hitung');
    Route::post('/transaksi/keluar/bayar', [TransaksiController::class,'bayar'])->name('keluar.bayar');
    Route::get('/transaksi-aktif',[TransaksiController::class,'transaksiAktif'])
        ->name('transaksi.transaksiAktif')
        ->middleware('log.menu:Transaksi Aktif');
    Route::post('/transaksi/keluar/batal', function(){
        session()->forget('draft_keluar');
        session()->forget('struk_keluar');
        return back();
    })->name('keluar.batal');
    Route::get('membership', [MembershipController::class, 'indexPetugas'])
        ->name('petugas.membership.index')
        ->middleware('log.menu:Membership');
    Route::get('membership/create', [MembershipController::class, 'createPetugas'])
        ->name('petugas.membership.create')
        ->middleware('log.menu:Tambah Membership');
    Route::post('membership', [MembershipController::class, 'storePetugas'])
        ->name('petugas.membership.store');
    Route::get('membership/{membership}/edit', [MembershipController::class, 'editPetugas'])
        ->name('petugas.membership.edit')
        ->middleware('log.menu:Edit Membership');
    Route::put('membership/{membership}', [MembershipController::class, 'updatePetugas'])
        ->name('petugas.membership.update');
    Route::get('/membership-kendaraan', [KendaraanMemberController::class, 'petugasIndex'])
        ->name('membership_kendaraan.index')
        ->middleware('log.menu:Membership Kendaraan');
    Route::get('/membership-tier', [MembershipTierController::class, 'petugasIndex'])
        ->name('membership_tier.index')
        ->middleware('log.menu:Membership Tier');
});

// ================= OWNER =================
Route::prefix('owner')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', fn()=>redirect('/laporan/harian'));
});

// ================= LAPORAN =================
Route::prefix('laporan')->middleware(['auth', 'role:admin,owner'])->group(function () {
    Route::get('/harian', [LaporanController::class, 'laporanHariIni'])
        ->middleware('log.menu:Laporan Harian');
    Route::get('/periode', [LaporanController::class, 'laporanPeriode'])
        ->middleware('log.menu:Laporan Periode');
    Route::post('/periode', [LaporanController::class, 'laporanPeriode']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('laporan/occupancy', [LaporanController::class, 'occupancy'])
        ->middleware('log.menu:Laporan Occupancy');
    Route::get('laporan/riwayatTransaksi', [LaporanController::class, 'riwayatTransaksi'])
        ->middleware('log.menu:Riwayat Transaksi');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'showTransaksi'])
        ->name('transaksi.show')
        ->middleware('log.menu:Detail Transaksi');
    Route::get('/tracking-kendaraan', [KendaraanController::class, 'tracking'])
        ->name('kendaraan.tracking')
        ->middleware('log.menu:Tracking Kendaraan');
    Route::get('/kendaraan/search', [KendaraanController::class, 'search'])
        ->name('kendaraan.search')
        ->middleware('log.menu:Cari Kendaraan');
});

// ================= VIEW ONLY =================
Route::middleware(['auth'])->prefix('show-data')->group(function () {
    Route::get('tipekendaraan', [ViewOnlyController::class, 'tipeKendaraan'])
        ->name('show-data.tipeKendaraan')
        ->middleware('log.menu:Tipe Kendaraan (view-only)');
    Route::get('area-parkir', [ViewOnlyController::class, 'areaParkir'])
        ->name('show-data.areaParkir')
        ->middleware('log.menu:Area Parkir (view-only)');
    Route::get('kendaraan', [ViewOnlyController::class, 'kendaraan'])
        ->name('show-data.kendaraan')
        ->middleware('log.menu:Kendaraan (view-only)');
    Route::get('metode-pembayaran', [ViewOnlyController::class, 'metodePembayaran'])
        ->name('show-data.metodePembayaran')
        ->middleware('log.menu:Metode Pembayaran (view-only)');
    Route::get('tarif-tipe-kendaraan', [ViewOnlyController::class, 'tarifTipeKendaraan'])
        ->name('show-data.tarifTipeKendaraan')
        ->middleware('log.menu:Tarif Tipe Kendaraan (view-only)');
    Route::get('tarif-durasi', [ViewOnlyController::class, 'tarifDurasi'])
        ->name('show-data.tarifDurasi')
        ->middleware('log.menu:Tarif Durasi (view-only)');
    Route::get('konfigurasi-tarif', [ViewOnlyController::class, 'konfigurasiTarif'])
        ->name('show-data.konfigurasiTarif')
        ->middleware('log.menu:Konfigurasi Tarif (view-only)');
});

