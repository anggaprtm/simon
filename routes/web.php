<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\BahanController; 


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    // ... route profile ...
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('gudang', GudangController::class);
    Route::resource('bahan', BahanController::class);

    // Grup route untuk Superadmin
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('program-studi', ProgramStudiController::class);
    });
});

require __DIR__.'/auth.php';
