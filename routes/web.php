<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PdfGeneratorController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SitemapController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\PDF;

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

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



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Route Dashboard Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // 2. Daftar User
    Route::get('/daftar-user', function () {
        $users = App\Models\User::orderBy('created_at', 'desc')->get();
        
        return Inertia::render('Admin/DaftarUser/Index', [
            'users' => $users
        ]);
    })->name('daftar-user.index');

    // 3. Daftar Mitra
    Route::get('/daftar-mitra', function () {
        $mitras = App\Models\DataMitra::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('Admin/DaftarMitra/Index', [
            'mitras' => $mitras
        ]);
    })->name('daftar-mitra.index');

    // Import Mitra
    Route::post('/daftar-mitra/import', [App\Http\Controllers\DataMitraController::class, 'import'])->name('daftar-mitra.import');
    Route::get('/daftar-mitra/template', [App\Http\Controllers\DataMitraController::class, 'downloadTemplate'])->name('daftar-mitra.template');
    Route::get('/daftar-mitra/export', [App\Http\Controllers\DataMitraController::class, 'export'])->name('daftar-mitra.export');
    Route::put('/daftar-mitra/{id}', [App\Http\Controllers\DataMitraController::class, 'updateByAdmin'])->name('admin.daftar-mitra.update');
    Route::delete('/daftar-mitra/{id}', [App\Http\Controllers\DataMitraController::class, 'destroy'])->name('admin.daftar-mitra.destroy');
    Route::post('/daftar-mitra/bulk-delete', [App\Http\Controllers\DataMitraController::class, 'bulkDelete'])->name('admin.daftar-mitra.bulk-delete');
    

    // 4. Daftar Seleksi Mitra
    Route::get('/seleksi-mitra', function () {
        $seleksiMitras = App\Models\DataSeleksiMitra::with('mitra')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('Admin/DaftarSeleksiMitra/Index', [
            'seleksiMitras' => $seleksiMitras
        ]);
    })->name('seleksi-mitra.index');

    // Generate PDF Surat Penetapan
    Route::get('/seleksi-mitra/{id}/surat-penetapan', [PdfGeneratorController::class, 'generateSuratPenetapan'])
        ->name('seleksi-mitra.surat-penetapan');

    // Download Pengajuan Seleksi Mitra
    Route::get('/seleksi-mitra/{id}/download-form', [PdfGeneratorController::class, 'downloadSeleksiMitraPdf'])
        ->name('seleksi-mitra.download-form');

    // 5. Daftar Klasifikasi Mitra
    Route::get('/klasifikasi-mitra', function () {
        $klasifikasiMitras = App\Models\KlasifikasiMitra::with('mitra')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('Admin/DaftarKlasifikasiMitra/Index', [
            'klasifikasiMitras' => $klasifikasiMitras
        ]);
    })->name('klasifikasi-mitra.index');

    //Generate PDF Surat Penetapan Klasifikasi
    Route::get('/klasifikasi-mitra/{id}/surat-penetapan', [PdfGeneratorController::class, 'generateSuratPenetapanKlasifikasi'])
    ->name('klasifikasi-mitra.surat-penetapan');

    //Generate PDF Berita Acara Klasifikasi
    Route::get('/klasifikasi-mitra/{id}/berita-acara', [PdfGeneratorController::class, 'generateBeritaAcaraKlasifikasi'])
    ->name('klasifikasi-mitra.berita-acara');

    // Download Klasifikasi Mitra
    Route::get('/klasifikasi-mitra/{id}/download-form', [PdfGeneratorController::class, 'downloadKlasifikasiMitraPdf'])
        ->name('klasifikasi-mitra.download-form');

    // 6. Daftar Hasil Seleksi Mitra
    Route::get('/hasil-seleksi-mitra', function () {
        $hasilSeleksiMitras = App\Models\HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('Admin/DaftarHasilSeleksiMitra/Index', [
            'hasilSeleksiMitras' => $hasilSeleksiMitras
        ]);
    })->name('hasil-seleksi-mitra.index');

    // Generate PDF Berita Acara Hasil Seleksi Mitra Pangan
    Route::get('/hasil-seleksi-mitra/{id}/berita-acara', [PdfGeneratorController::class, 'generateBeritaAcara'])
    ->name('hasil-seleksi-mitra.berita-acara');

    // 7. Purchase Orders
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])
        ->name('purchase-orders.index');

    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])
        ->name('purchase-orders.create');

    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])
        ->name('purchase-orders.store');

    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
        ->name('purchase-orders.show');

    Route::get('/purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])
        ->name('purchase-orders.edit');

    Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
        ->name('purchase-orders.update');

    Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])
        ->name('purchase-orders.destroy');

    // Generate PDF Routes
    Route::get('/purchase-orders/{purchaseOrder}/surat-permohonan', 
        [PurchaseOrderController::class, 'generateSuratPermohonan'])
        ->name('purchase-orders.surat-permohonan');

    Route::get('/purchase-orders/{purchaseOrder}/form-penawaran', 
        [PurchaseOrderController::class, 'generateFormPenawaran'])
        ->name('purchase-orders.form-penawaran');

    Route::get('/purchase-orders/{purchaseOrder}/combined-pdf', 
        [PurchaseOrderController::class, 'generateCombinedPdf'])
        ->name('purchase-orders.combined-pdf');

    // Helper Route for Dynamic Options
    Route::get('/kualitas-options', [PurchaseOrderController::class, 'getKualitasOptions'])
        ->name('purchase-orders.kualitas-options');

    // 8. HPP Management Routes (Admin Only)
    Route::get('/hpp', [App\Http\Controllers\HppController::class, 'index'])
        ->name('hpp.index');
    Route::post('/hpp', [App\Http\Controllers\HppController::class, 'store'])
        ->name('hpp.store');
    Route::get('/hpp/{id}', [App\Http\Controllers\HppController::class, 'show'])
        ->name('hpp.show');
    Route::put('/hpp/{id}', [App\Http\Controllers\HppController::class, 'update'])
        ->name('hpp.update');
    Route::delete('/hpp/{id}', [App\Http\Controllers\HppController::class, 'destroy'])
        ->name('hpp.destroy');
    Route::patch('/hpp/{id}/toggle-active', [App\Http\Controllers\HppController::class, 'toggleActive'])
        ->name('hpp.toggle-active');
});

// 1. Routes Dashboard Mitra tanpa middleware (untuk testing)
Route::prefix('mitra')->name('mitra.')->middleware(['auth', 'role:mitra'])->group(function () {
    Route::get('/dashboard', function () {
        $userId = auth()->id();
        
        // Hitung statistik Purchase Order untuk user yang sedang login
        $totalPO = \App\Models\PurchaseOrder::where('user_id', $userId)->count();
        $activePO = \App\Models\PurchaseOrder::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->count();
        $totalValueThisMonth = \App\Models\PurchaseOrder::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->with('items')
            ->get()
            ->sum(function($po) {
                return $po->total_nilai;
            });
        
        return Inertia::render('Mitra/Dashboard', [
            'statistik' => [
                'pengajuan_total' => 3,
                'pengajuan_approved' => 1,
                'pengajuan_pending' => 2,
                'po_aktif' => $activePO,
                'total_po' => $totalPO,
                'total_nilai_bulan_ini' => $totalValueThisMonth
            ]
        ]);
    })->name('dashboard');

    

    //2. untuk mengakses tabel pengajuan seleksi
    Route::get('/pengajuan-seleksi', function () {
        return Inertia::render('Mitra/PengajuanSeleksi/Index');
    })->name('pengajuan-seleksi.index');

    Route::get('/input-data-mitra', function () {
        return Inertia::render('Mitra/InputDataMitra');
    })->name('input-data-mitra');

    // Generate PDF Surat Permohonan MPP
    Route::get('/data-mitra/{id}/surat-permohonan', [PdfGeneratorController::class, 'generateSuratPermohonanPdf'])
        ->name('data-mitra.surat-permohonan');

    // Generate PDF Surat Pernyataan Non PKP
    Route::get('/data-mitra/{id}/surat-pernyataan-non-pkp', [PdfGeneratorController::class, 'generateSuratPernyataanNonPkp'])
        ->name('data-mitra.surat-pernyataan-non-pkp');

    // Generate PDF Pakta Integritas
    Route::get('/data-mitra/{id}/pakta-integritas', [PdfGeneratorController::class, 'generatePaktaIntegritas'])
        ->name('data-mitra.pakta-integritas');

    // Generate PDF Surat Kuasa
    Route::post('/data-mitra/{id}/surat-kuasa', [PdfGeneratorController::class, 'generateSuratKuasa'])
        ->name('data-mitra.surat-kuasa');

    //untuk mengakses form pengajuan seleksi
    Route::get('/pengajuan-seleksi/form', function () {
        return Inertia::render('Mitra/PengajuanSeleksi/Form');
    })->name('pengajuan-seleksi.form');

    //untuk  mengenerate tabel ke pdf
    Route::get('/pengajuan-seleksi/{id}/download', [PdfGeneratorController::class, 'downloadSeleksiMitraPdf'])
    ->name('pengajuan-seleksi.download');

    //3. untuk mengakses tabel klasifikasi mitra
    Route::get('/klasifikasi-mitra', function () {
        return Inertia::render('Mitra/KlasifikasiMitra/Index', [
            'title' => 'Klasifikasi Mitra'
        ]);
    })->name('klasifikasi-mitra.index');

    //untuk  mengenerate tabel klasifikasi mitra ke pdf
    Route::get('/klasifikasi/{id}/download', [PdfGeneratorController::class, 'downloadKlasifikasiMitraPdf'])
    ->name('klasifikasi.download');

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

    //4. untuk mengakses tabel puschase order - DEPRECATED (Moved to Admin)
    // Purchase Order now managed by Admin only

    //5. untuk mengakses tabel hasil seleksi
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

//Route Dashboard Super Admin
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'role:super admin'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'superAdminDashboard'])->name('dashboard');

    // 2. Daftar User
    Route::get('/daftar-user', function () {
        $users = App\Models\User::orderBy('created_at', 'desc')->get();
        
        return Inertia::render('SuperAdmin/Users/Index', [
            'users' => $users
        ]);
    })->name('daftar-user.index');

    // 3. Daftar Mitra
    Route::get('/daftar-mitra', function () {
        $mitras = App\Models\DataMitra::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('SuperAdmin/Mitra/Index', [
            'mitras' => $mitras
        ]);
    })->name('daftar-mitra.index');
    Route::post('/daftar-mitra/import', [App\Http\Controllers\DataMitraController::class, 'import'])->name('daftar-mitra.import');
    Route::get('/daftar-mitra/template', [App\Http\Controllers\DataMitraController::class, 'downloadTemplate'])->name('daftar-mitra.template');
    Route::get('/daftar-mitra/export', [App\Http\Controllers\DataMitraController::class, 'export'])->name('daftar-mitra.export');
    Route::put('/daftar-mitra/{id}', [App\Http\Controllers\DataMitraController::class, 'updateByAdmin'])->name('daftar-mitra.update');
    Route::delete('/daftar-mitra/{id}', [App\Http\Controllers\DataMitraController::class, 'destroy'])->name('daftar-mitra.destroy');
    Route::post('/daftar-mitra/bulk-delete', [App\Http\Controllers\DataMitraController::class, 'bulkDelete'])->name('daftar-mitra.bulk-delete');

    // 4. Daftar Seleksi Mitra
    Route::get('/seleksi-mitra', function () {
        $seleksiMitras = App\Models\DataSeleksiMitra::with('mitra')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('SuperAdmin/Seleksi/Index', [
            'seleksiMitras' => $seleksiMitras
        ]);
    })->name('seleksi-mitra.index');

    // Download form pengajuan seleksi mitra (Super Admin)
    Route::get('/seleksi-mitra/{id}/download-form', [PdfGeneratorController::class, 'downloadSeleksiMitraPdf'])
        ->name('seleksi-mitra.download-form');

    // Generate PDF Surat Penetapan Seleksi (Super Admin)
    Route::get('/seleksi-mitra/{id}/surat-penetapan', [PdfGeneratorController::class, 'generateSuratPenetapan'])
        ->name('seleksi-mitra.surat-penetapan');

    // 5. Daftar Klasifikasi Mitra
    Route::get('/klasifikasi-mitra', function () {
        $klasifikasiMitras = App\Models\KlasifikasiMitra::with('mitra')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('SuperAdmin/Klasifikasi/Index', [
            'klasifikasiMitras' => $klasifikasiMitras
        ]);
    })->name('klasifikasi-mitra.index');

    // Download Klasifikasi Mitra Form (Super Admin)
    Route::get('/klasifikasi-mitra/{id}/download-form', [PdfGeneratorController::class, 'downloadKlasifikasiMitraPdf'])
        ->name('klasifikasi-mitra.download-form');

    // Generate PDF Surat Penetapan Klasifikasi (Super Admin)
    Route::get('/klasifikasi-mitra/{id}/surat-penetapan', [PdfGeneratorController::class, 'generateSuratPenetapanKlasifikasi'])
        ->name('klasifikasi-mitra.surat-penetapan');

    // Generate PDF Berita Acara Klasifikasi (Super Admin)
    Route::get('/klasifikasi-mitra/{id}/berita-acara', [PdfGeneratorController::class, 'generateBeritaAcaraKlasifikasi'])
        ->name('klasifikasi-mitra.berita-acara');

    // 6. Daftar Hasil Seleksi Mitra
    Route::get('/hasil-seleksi-mitra', function () {
        $hasilSeleksiMitras = App\Models\HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('SuperAdmin/HasilSeleksi/Index', [
            'hasilSeleksiMitras' => $hasilSeleksiMitras
        ]);
    })->name('hasil-seleksi-mitra.index');

    // Generate PDF Berita Acara Hasil Seleksi Mitra (Super Admin)
    Route::get('/hasil-seleksi-mitra/{id}/berita-acara', [PdfGeneratorController::class, 'generateBeritaAcara'])
        ->name('hasil-seleksi-mitra.berita-acara');

    // 7. Purchase Orders (Super Admin)
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])
        ->name('purchase-orders.index');

    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])
        ->name('purchase-orders.create');

    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])
        ->name('purchase-orders.store');

    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
        ->name('purchase-orders.show');

    Route::get('/purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])
        ->name('purchase-orders.edit');

    Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
        ->name('purchase-orders.update');

    Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])
        ->name('purchase-orders.destroy');

    // Generate PDF Routes
    Route::get('/purchase-orders/{purchaseOrder}/surat-permohonan', 
        [PurchaseOrderController::class, 'generateSuratPermohonan'])
        ->name('purchase-orders.surat-permohonan');

    Route::get('/purchase-orders/{purchaseOrder}/form-penawaran', 
        [PurchaseOrderController::class, 'generateFormPenawaran'])
        ->name('purchase-orders.form-penawaran');

    Route::get('/purchase-orders/{purchaseOrder}/combined-pdf', 
        [PurchaseOrderController::class, 'generateCombinedPdf'])
        ->name('purchase-orders.combined-pdf');

    // Helper Route for Dynamic Options
    Route::get('/kualitas-options', [PurchaseOrderController::class, 'getKualitasOptions'])
        ->name('purchase-orders.kualitas-options');

    // 8. HPP Management Routes (Super Admin)
    Route::get('/hpp', [App\Http\Controllers\HppController::class, 'indexSuperAdmin'])
        ->name('hpp.index');
    Route::post('/hpp', [App\Http\Controllers\HppController::class, 'store'])
        ->name('hpp.store');
    Route::get('/hpp/{id}', [App\Http\Controllers\HppController::class, 'show'])
        ->name('hpp.show');
    Route::put('/hpp/{id}', [App\Http\Controllers\HppController::class, 'update'])
        ->name('hpp.update');
    Route::delete('/hpp/{id}', [App\Http\Controllers\HppController::class, 'destroy'])
        ->name('hpp.destroy');
    Route::patch('/hpp/{id}/toggle-active', [App\Http\Controllers\HppController::class, 'toggleActive'])
        ->name('hpp.toggle-active');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/input-data-mitra', function () {
        return Inertia::render('Mitra/InputDataMitra');
    })->name('input-data-mitra');
});

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/hasil-seleksi-mitra', function () {
        return App\Models\HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])
            ->orderBy('created_at', 'desc')
            ->get();
    })->name('hasil-seleksi-mitra');
    Route::get('/karyawan', function() {
        return App\Models\Karyawan::all();
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';