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

Route::get('/input-data-mitra', function () {
    return Inertia::render('Mitra/InputDataMitra');
})->name('input-data-mitra');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes Dashboard Mitra tanpa middleware (untuk testing)
Route::prefix('mitra')->name('mitra.')->middleware(['auth', 'role:mitra'])->group(function () {
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

    //untuk mengakses tabel pengajuan seleksi
    Route::get('/pengajuan-seleksi', function () {
        return Inertia::render('Mitra/PengajuanSeleksi/Index');
    })->name('pengajuan-seleksi.index');

    Route::get('/input-data-mitra', function () {
        return Inertia::render('Mitra/InputDataMitra');
    })->name('input-data-mitra');

    //untuk mengakses form pengajuan seleksi
    Route::get('/pengajuan-seleksi/form', function () {
        return Inertia::render('Mitra/PengajuanSeleksi/Form');
    })->name('pengajuan-seleksi.form');

    //untuk mengakses tabel klasifikasi mitra
    Route::get('/klasifikasi-mitra', function () {
        return Inertia::render('Mitra/KlasifikasiMitra/Index', [
            'title' => 'Klasifikasi Mitra'
        ]);
    })->name('klasifikasi-mitra.index');

    //untuk mengakses form klasifikasi mitra
    Route::get('/klasifikasi-mitra/form', function () {
        return Inertia::render('Mitra/KlasifikasiMitra/Form', [
            'title' => 'Klasifikasi Mitra'
        ]);
    })->name('klasifikasi-mitra.form');

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