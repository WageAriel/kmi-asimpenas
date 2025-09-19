<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MitraController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/test', function () {
    return Inertia::render('Test');
})->name('test');

Route::get('/test-input', function () {
    return Inertia::render('TestInputMitra');
})->name('test-input');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes Dashboard Mitra tanpa middleware (untuk testing)
Route::prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Mitra/Dashboard', [
            'statistik' => [
                'pengajuan_total' => 3,
                'pengajuan_approved' => 1,
                'pengajuan_pending' => 2,
                'po_aktif' => 5
            ]
        ]);
    })->name('dashboard');

    Route::get('/pengajuan-seleksi', function () {
        return Inertia::render('Mitra/PengajuanSeleksi');
    })->name('pengajuan-seleksi');

    Route::get('/klasifikasi-mitra', function () {
        return Inertia::render('Mitra/KlasifikasiMitra', [
            'title' => 'Klasifikasi Mitra'
        ]);
    })->name('klasifikasi-mitra');

    Route::post('/klasifikasi-mitra', function () {
        // Untuk testing, hanya return success response
        return redirect()->route('mitra.klasifikasi-mitra')
            ->with('success', 'Data klasifikasi mitra berhasil disimpan!');
    })->name('klasifikasi-mitra.store');

    Route::get('/hasil-seleksi', function () {
        return Inertia::render('Mitra/HasilSeleksi', [
            'hasilSeleksi' => [
                'status' => 'Dalam Review',
                'tanggal_pengajuan' => now()->format('d/m/Y'),
                'detail' => [
                    'skor' => 85,
                    'keterangan' => 'Memenuhi syarat seleksi awal'
                ]
            ]
        ]);
    })->name('hasil-seleksi');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';