<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PdfGeneratorController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\PDF;

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

    //4. untuk mengakses tabel puschase order
    Route::get('/purchase-orders', function () {
        $purchaseOrders = App\Models\PurchaseOrder::with('items')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('Mitra/PurchaseOrder/Index', [
            'purchaseOrders' => $purchaseOrders
        ]);
    })->name('purchase-orders.index');

    // Form Create Purchase Order
    Route::get('/purchase-orders/create', function () {
        return Inertia::render('Mitra/PurchaseOrder/Create');
    })->name('purchase-orders.create');

    // Store Purchase Order
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])
        ->name('purchase-orders.store');

    // Show Purchase Order
    Route::get('/purchase-orders/{purchaseOrder}', function (App\Models\PurchaseOrder $purchaseOrder) {
        // Pastikan user hanya bisa melihat PO mereka sendiri
        if ($purchaseOrder->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this Purchase Order.');
        }
        
        return Inertia::render('Mitra/PurchaseOrder/Show', [
            'purchaseOrder' => $purchaseOrder->load('items')
        ]);
    })->name('purchase-orders.show');

    // Edit Purchase Order
    Route::get('/purchase-orders/{purchaseOrder}/edit', function (App\Models\PurchaseOrder $purchaseOrder) {
        // Pastikan user hanya bisa edit PO mereka sendiri
        if ($purchaseOrder->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this Purchase Order.');
        }
        
        return Inertia::render('Mitra/PurchaseOrder/Edit', [
            'purchaseOrder' => $purchaseOrder->load('items')
        ]);
    })->name('purchase-orders.edit');

    // Update Purchase Order
    Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
        ->name('purchase-orders.update');

    // Delete Purchase Order
    Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])
        ->name('purchase-orders.destroy');

    // Generate PDF Routes
    Route::get('/purchase-orders/{purchaseOrder}/surat-permohonan', 
        [PdfGeneratorController::class, 'downloadSuratPermohonanPdf'])
        ->name('purchase-orders.surat-permohonan');

    Route::get('/purchase-orders/{purchaseOrder}/form-penawaran', 
        [PdfGeneratorController::class, 'downloadFormPenawaranPdf'])
        ->name('purchase-orders.form-penawaran');

    Route::get('/purchase-orders/{purchaseOrder}/combined-pdf', 
        [PdfGeneratorController::class, 'downloadCombinedPdf'])
        ->name('purchase-orders.combined-pdf');

    // Helper Route for Dynamic Options
    Route::get('/kualitas-options', [PurchaseOrderController::class, 'getKualitasOptions'])
        ->name('purchase-orders.kualitas-options');

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

    // Purchase Order routes
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('/purchase-orders/{purchaseOrder}/surat-permohonan', [PurchaseOrderController::class, 'generateSuratPermohonan'])->name('purchase-orders.surat-permohonan');
    Route::get('/purchase-orders/{purchaseOrder}/form-penawaran', [PurchaseOrderController::class, 'generateFormPenawaran'])->name('purchase-orders.form-penawaran');
    Route::get('/purchase-orders/{purchaseOrder}/combined-pdf', [PurchaseOrderController::class, 'generateCombinedPdf'])->name('purchase-orders.combined-pdf');
    Route::get('/kualitas-options', [PurchaseOrderController::class, 'getKualitasOptions'])->name('purchase-orders.kualitas-options');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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