<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('scan', [ScanController::class, 'index'])->name('scan.index');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::middleware('admin')->group(function () {
        Route::post('barang/import-csv', [BarangController::class, 'importCsv'])->name('barang.import');
        Route::get('laporan/export-barang', [LaporanController::class, 'exportBarang'])->name('laporan.export.barang');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('audit', [AuditController::class, 'index'])->name('audit.index');

        Route::post('barang/{barang}/mutasi', [BarangController::class, 'mutasi'])->name('barang.mutasi');
    });

    Route::get('barang/print-label', [BarangController::class, 'printLabel'])->name('barang.print-label');
    Route::resource('barang', BarangController::class)->except(['create', 'destroy']);
    Route::get('barang/{kode}/qr', [BarangController::class, 'downloadQR'])->name('barang.qr')->withoutMiddleware('admin');
    Route::delete('barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy')->middleware('admin');

    Route::resource('kategori', KategoriController::class)->except(['show', 'edit', 'destroy']);
    Route::get('kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy')->middleware('admin');

    Route::resource('lokasi', LokasiController::class)->except(['show', 'edit', 'destroy']);
    Route::get('lokasi/{id}', [LokasiController::class, 'show'])->name('lokasi.show');
    Route::get('lokasi/{id}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit');
    Route::delete('lokasi/{lokasi}', [LokasiController::class, 'destroy'])->name('lokasi.destroy')->middleware('admin');
});

Route::get('info', [BarangController::class, 'info']);
Route::get('b/{kode}', [BarangController::class, 'publicDetail'])->name('barang.public');
Route::get('scan/{kode}', [ScanController::class, 'scan'])->name('scan.scan');