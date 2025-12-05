<?php
use App\Http\Controllers\DataMitraController;
use App\Http\Controllers\DataSeleksiMitraController;
use App\Http\Controllers\HasilSeleksiMitraController;
use App\Http\Controllers\KlasifikasiMitraController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HppController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========================================
// PUBLIC API ROUTES (untuk Landing Page)
// ========================================
// HPP Routes - Public access untuk landing page
Route::get('/hpp', [HppController::class, 'getAllDocuments']);
Route::get('/hpp/{jenis}', [HppController::class, 'getByJenis']);

// ========================================
// ROUTES UNTUK SEMUA AUTHENTICATED USERS
// ========================================
Route::middleware('auth:sanctum')->group(function () {
    // Activities - Semua user bisa akses activity mereka
    Route::get('/activities/my', [ActivityController::class, 'myActivities']);
    Route::post('/activities/refresh', [ActivityController::class, 'refreshActivities']);
    
    // Dashboard stats and data - Semua user
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData']);
});

// ========================================
// ROUTES KHUSUS MITRA
// ========================================
// Route untuk mitra akses data mereka sendiri
Route::middleware(['auth:sanctum', 'role:mitra'])->group(function () {
    // Specific routes harus di atas dynamic routes
    Route::get('/data-mitra/birthdays', [DataMitraController::class, 'getBirthdays']);
    Route::get('/data-mitra/my', [DataMitraController::class, 'myMitra']);
    Route::get('/data-seleksi-mitra/my', [DataSeleksiMitraController::class, 'mySeleksi']);
    Route::get('/klasifikasi-mitra/my', [KlasifikasiMitraController::class, 'myKlasifikasi']);
    Route::get('/hasil-seleksi-mitra/my', [HasilSeleksiMitraController::class, 'myHasilSeleksi']);
});

// ========================================
// ROUTES UNTUK MITRA, ADMIN, DAN SUPER ADMIN
// ========================================
Route::middleware(['auth:sanctum', 'role:mitra,admin,super admin'])->group(function () {
    // Data Mitra - CRUD (Semua role bisa POST, GET, PUT)
    // Dynamic routes {id} harus di bawah specific routes
    Route::post('/data-mitra', [DataMitraController::class, 'store'])->name('data-mitra.store');
    Route::get('/data-mitra/{id}', [DataMitraController::class, 'show'])->name('data-mitra.show');
    Route::put('/data-mitra/{id}', [DataMitraController::class, 'update'])->name('data-mitra.update');
    
    // Data Seleksi Mitra - CRUD (Semua role bisa POST, GET, PUT)
    Route::get('/data-seleksi-mitra', [DataSeleksiMitraController::class, 'indexByMitra'])->name('data-seleksi-mitra.index');
    Route::post('/data-seleksi-mitra', [DataSeleksiMitraController::class, 'store'])->name('data-seleksi-mitra.store');
    Route::get('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'show'])->name('data-seleksi-mitra.show');
    Route::put('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'update'])->name('data-seleksi-mitra.update');
    
    // Klasifikasi Mitra - CRUD (Semua role bisa POST, GET, PUT)
    Route::post('/klasifikasi-mitra', [KlasifikasiMitraController::class, 'store'])->name('klasifikasi-mitra.store');
    Route::get('/klasifikasi-mitra/{id}', [KlasifikasiMitraController::class, 'show'])->name('klasifikasi-mitra.show');
    Route::put('/klasifikasi-mitra/{id}', [KlasifikasiMitraController::class, 'update'])->name('klasifikasi-mitra.update');
    
    // Hasil Seleksi Mitra - Read & Detail
    Route::get('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'show']);
    Route::get('/hasil-seleksi-mitra/by-seleksi/{id_seleksi_mitra}', [HasilSeleksiMitraController::class, 'getBySeleksiMitra']);
});

// ========================================
// ROUTES UNTUK ADMIN DAN SUPER ADMIN ONLY
// ========================================
Route::middleware(['auth:sanctum', 'role:admin,super admin'])->group(function () {
    // Data Mitra - Admin & Super Admin bisa lihat semua & birthdays
    // Specific routes HARUS di atas dynamic routes
    Route::get('/data-mitra', [DataMitraController::class, 'index'])->name('data-mitra.index');
    
    // Import/Export routes for Data Seleksi Mitra
    Route::post('/data-seleksi-mitra/import', [DataSeleksiMitraController::class, 'import'])->name('data-seleksi-mitra.import');
    Route::get('/data-seleksi-mitra/export/data', [DataSeleksiMitraController::class, 'export'])->name('data-seleksi-mitra.export');
    Route::get('/data-seleksi-mitra/export/template', [DataSeleksiMitraController::class, 'downloadTemplate'])->name('data-seleksi-mitra.template');
    
    // Bulk delete for Data Seleksi Mitra
    Route::post('/data-seleksi-mitra/bulk-delete', [DataSeleksiMitraController::class, 'bulkDelete'])->name('data-seleksi-mitra.bulk-delete');

    // Hasil Seleksi Mitra - Admin & Super Admin full CRUD
    Route::get('/hasil-seleksi-mitra', [HasilSeleksiMitraController::class, 'index']);
    Route::post('/hasil-seleksi-mitra', [HasilSeleksiMitraController::class, 'store']);
    Route::put('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'update']);
    Route::get('/hasil-seleksi-mitra/export/data', [HasilSeleksiMitraController::class, 'export'])->name('hasil-seleksi-mitra.export');
    
    // Klasifikasi Mitra - Admin & Super Admin bisa lihat semua & import/export
    Route::get('/klasifikasi-mitra', [KlasifikasiMitraController::class, 'index'])->name('klasifikasi-mitra.index');
    Route::post('/klasifikasi-mitra/import', [KlasifikasiMitraController::class, 'import'])->name('klasifikasi-mitra.import');
    Route::get('/klasifikasi-mitra/export/data', [KlasifikasiMitraController::class, 'export'])->name('klasifikasi-mitra.export');
    Route::get('/klasifikasi-mitra/export/template', [KlasifikasiMitraController::class, 'downloadTemplate'])->name('klasifikasi-mitra.template');

    // Karyawan - Admin & Super Admin bisa akses
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
});

// ========================================
// ROUTES UNTUK SUPER ADMIN ONLY
// ========================================
Route::middleware(['auth:sanctum', 'role:super admin'])->group(function () {
    // DELETE operations - Hanya Super Admin
    Route::delete('/data-mitra/{id}', [DataMitraController::class, 'destroy'])->name('data-mitra.destroy');
    Route::delete('/data-seleksi-mitra/{id}', [DataSeleksiMitraController::class, 'destroy'])->name('data-seleksi-mitra.destroy');
    Route::delete('/hasil-seleksi-mitra/{id}', [HasilSeleksiMitraController::class, 'destroy']);
    Route::delete('/klasifikasi-mitra/{id}', [KlasifikasiMitraController::class, 'destroy'])->name('klasifikasi-mitra.destroy');
    
    // User Management - Hanya Super Admin
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

