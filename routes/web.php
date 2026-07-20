<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('b/{kode}', [BarangController::class, 'publicDetail'])->name('barang.public');
Route::get('scan/{kode}', [ScanController::class, 'scan'])->name('scan.scan');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('scan', [ScanController::class, 'index'])->name('scan.index');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::middleware('admin')->group(function () {
        Route::post('barang/import-csv', [BarangController::class, 'importCsv'])->name('barang.import');
        Route::get('laporan/export-barang', [LaporanController::class, 'exportBarang'])->name('laporan.export.barang');
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
