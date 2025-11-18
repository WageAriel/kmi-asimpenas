<?php
use App\Http\Controllers\DataMitraController;
use App\Http\Controllers\DataSeleksiMitraController;
use App\Http\Controllers\HasilSeleksiMitraController;
use App\Http\Controllers\KlasifikasiMitraController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/activities/my', [ActivityController::class, 'myActivities']);
    Route::post('/activities/refresh', [ActivityController::class, 'refreshActivities']);
    
    // Dashboard stats and data
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData']);
});

Route::middleware('auth:sanctum')->get('/data-mitra/my', [DataMitraController::class, 'myMitra']);
Route::middleware('auth:sanctum')->get('/data-mitra/birthdays', [DataMitraController::class, 'getBirthdays']);
Route::middleware('auth:sanctum')->get('/data-seleksi-mitra/my', [DataSeleksiMitraController::class, 'mySeleksi']);
Route::middleware('auth:sanctum')->get('/klasifikasi-mitra/my', [KlasifikasiMitraController::class, 'myKlasifikasi']);
Route::middleware('auth:sanctum')->get('/hasil-seleksi-mitra/my', [HasilSeleksiMitraController::class, 'myHasilSeleksi']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/data-mitra', [DataMitraController::class, 'store'])->name('data-mitra.store');
    Route::get('/data-mitra/{id}', [DataMitraController::class, 'show'])->name('data-mitra.show');
    Route::put('/data-mitra/{id}', [DataMitraController::class, 'update'])->name('data-mitra.update');
    Route::delete('/data-mitra/{id}', [DataMitraController::class, 'destroy'])->name('data-mitra.destroy');
    
    // Route lain yang juga perlu auth bisa dimasukkan di sini
    Route::get('/data-mitra', [DataMitraController::class, 'index'])->name('data-mitra.index');
    
    Route::get('/data-seleksi-mitra', [DataSeleksiMitraController::class, 'indexByMitra'])->name('data-seleksi-mitra.index');
    Route::post('/data-seleksi-mitra', [DataSeleksiMitraController::class, 'store'])->name('data-seleksi-mitra.store');
    Route::get('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'show'])->name('data-seleksi-mitra.show');
    Route::put('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'update'])->name('data-seleksi-mitra.update');
    Route::delete('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'destroy'])->name('data-seleksi-mitra.destroy');
    
    // Import/Export routes for Data Seleksi Mitra
    Route::post('/data-seleksi-mitra/import', [DataSeleksiMitraController::class, 'import'])->name('data-seleksi-mitra.import');
    Route::get('/data-seleksi-mitra/export/data', [DataSeleksiMitraController::class, 'export'])->name('data-seleksi-mitra.export');
    Route::get('/data-seleksi-mitra/export/template', [DataSeleksiMitraController::class, 'downloadTemplate'])->name('data-seleksi-mitra.template');
    
    // Hasil Seleksi Mitra
    Route::get('/hasil-seleksi-mitra', [HasilSeleksiMitraController::class, 'index']);
    Route::post('/hasil-seleksi-mitra', [HasilSeleksiMitraController::class, 'store']);
    Route::get('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'show']);
    Route::get('/hasil-seleksi-mitra/by-seleksi/{id_seleksi_mitra}', [HasilSeleksiMitraController::class, 'getBySeleksiMitra']);
    Route::put('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'update']);
    Route::delete('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'destroy']);
    
    // Export route for Hasil Seleksi Mitra
    Route::get('/hasil-seleksi-mitra/export/data', [HasilSeleksiMitraController::class, 'export'])->name('hasil-seleksi-mitra.export');
    
    Route::get('/klasifikasi-mitra', [KlasifikasiMitraController::class, 'index'])->name('klasifikasi-mitra.index');
    Route::post('/klasifikasi-mitra', [KlasifikasiMitraController::class, 'store'])->name('klasifikasi-mitra.store');
    Route::get('/klasifikasi-mitra/{id}', [KlasifikasiMitraController::class, 'show'])->name('klasifikasi-mitra.show');
    Route::put('/klasifikasi-mitra/{id}', [KlasifikasiMitraController::class, 'update'])->name('klasifikasi-mitra.update');
    Route::delete('/klasifikasi-mitra/{id}', [KlasifikasiMitraController::class, 'destroy'])->name('klasifikasi-mitra.destroy');
    
    // Import/Export routes for Klasifikasi Mitra
    Route::post('/klasifikasi-mitra/import', [KlasifikasiMitraController::class, 'import'])->name('klasifikasi-mitra.import');
    Route::get('/klasifikasi-mitra/export/data', [KlasifikasiMitraController::class, 'export'])->name('klasifikasi-mitra.export');
    Route::get('/klasifikasi-mitra/export/template', [KlasifikasiMitraController::class, 'downloadTemplate'])->name('klasifikasi-mitra.template');

    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    
    // User Management (Super Admin only)
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

