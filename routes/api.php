<?php
use App\Http\Controllers\DataMitraController;

Route::get('/data-mitra', [DataMitraController::class, 'index'])->name('data-mitra.index');
Route::post('/data-mitra', [DataMitraController::class, 'store'])->name('data-mitra.store');
Route::get('/data-mitra/{id}', [DataMitraController::class, 'show'])->name('data-mitra.show');
Route::put('/data-mitra/{id}', [DataMitraController::class, 'update'])->name('data-mitra.update');
Route::delete('/data-mitra/{id}', [DataMitraController::class, 'destroy'])->name('data-mitra.destroy');
