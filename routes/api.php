<?php
use App\Http\Controllers\DataMitraController;
use App\Http\Controllers\DataSeleksiMitraController;
use App\Http\Controllers\HasilSeleksiMitraController;

Route::get('/data-mitra', [DataMitraController::class, 'index'])->name('data-mitra.index');
Route::post('/data-mitra', [DataMitraController::class, 'store'])->name('data-mitra.store');
Route::get('/data-mitra/{id}', [DataMitraController::class, 'show'])->name('data-mitra.show');
Route::put('/data-mitra/{id}', [DataMitraController::class, 'update'])->name('data-mitra.update');
Route::delete('/data-mitra/{id}', [DataMitraController::class, 'destroy'])->name('data-mitra.destroy');

Route::get('/data-seleksi-mitra', [DataSeleksiMitraController::class, 'index'])->name('data-seleksi-mitra.index');
Route::post('/data-seleksi-mitra', [DataSeleksiMitraController::class, 'store'])->name('data-seleksi-mitra.store');
Route::get('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'show'])->name('data-seleksi-mitra.show');
Route::put('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'update'])->name('data-seleksi-mitra.update');
Route::delete('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'destroy'])->name('data-seleksi-mitra.destroy');

Route::get('/hasil-seleksi-mitra', [HasilSeleksiMitraController::class, 'index'])->name('hasil-seleksi-mitra.index');
Route::post('/hasil-seleksi-mitra', [HasilSeleksiMitraController::class, 'store'])->name('hasil-seleksi-mitra.store');
Route::get('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'show'])->name('hasil-seleksi-mitra.show');
Route::put('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'update'])->name('hasil-seleksi-mitra.update');
Route::delete('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'destroy'])->name('hasil-seleksi-mitra.destroy');
