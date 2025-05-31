<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\BahanController; 
use App\Http\Controllers\TransaksiStokController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // ... route profile ...
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('gudang', GudangController::class);
    Route::resource('bahan', BahanController::class);

    Route::prefix('transaksi')->name('transaksi.')->group(function() {
        // Rute untuk menampilkan form
        Route::get('/{bahan}/masuk', [TransaksiStokController::class, 'createMasuk'])->name('createMasuk');
        Route::get('/{bahan}/keluar', [TransaksiStokController::class, 'createKeluar'])->name('createKeluar');
        
        // Rute untuk menyimpan data dari form
        Route::post('/{bahan}/masuk', [TransaksiStokController::class, 'storeMasuk'])->name('storeMasuk');
        Route::post('/{bahan}/keluar', [TransaksiStokController::class, 'storeKeluar'])->name('storeKeluar');

        // Rute untuk melihat riwayat
        Route::get('/{bahan}/riwayat', [TransaksiStokController::class, 'history'])->name('history');
    });
    // Grup route untuk Superadmin
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('program-studi', ProgramStudiController::class);
    });
});

require __DIR__.'/auth.php';
