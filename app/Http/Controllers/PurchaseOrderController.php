<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Jurosh\PDFMerge\PDFMerger;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $month = request('month');
        $year = request('year');
        
        $query = PurchaseOrder::with('items');
        
        if ($month && $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        }
        
        $purchaseOrders = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(fn ($po) => [
                'id' => $po->id,
                'no_surat' => $po->no_surat,
                'nama_perusahaan' => $po->nama_perusahaan,
                'jenis_komoditas' => $po->jenis_komoditas_lengkap,
                'jenis_pengadaan' => $po->jenis_pengadaan,
                'total_harga' => number_format($po->total_harga, 0, ',', '.'),
                'total_kuantum' => number_format($po->total_kuantum, 0, ',', '.'),
                'total_nilai' => number_format($po->total_nilai, 0, ',', '.'),
                'items_count' => $po->items->count(),
                'created_at' => $po->created_at->format('d/m/Y H:i'),
                'created_by' => $po->created_by,
            ]);

        $mitras = \App\Models\DataMitra::orderBy('nama_perusahaan')->get();

        // Detect user role and render appropriate view
        $userRole = auth()->user()->role;
        $viewPath = $userRole === 'super admin' 
            ? 'SuperAdmin/PurchaseOrder/Index' 
            : 'Admin/PurchaseOrder/Index';

        return Inertia::render($viewPath, [
            'purchaseOrders' => $purchaseOrders,
            'mitras' => $mitras,
            'filters' => [
                'month' => $month,
                'year' => $year
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mitras = \App\Models\DataMitra::orderBy('nama_perusahaan')->get();
        
        // Detect user role and render appropriate view
        $userRole = auth()->user()->role;
        $viewPath = $userRole === 'super admin' 
            ? 'SuperAdmin/PurchaseOrder/Create' 
            : 'Admin/PurchaseOrder/Create';

        return Inertia::render($viewPath, [
            'mitras' => $mitras,
            'jenisKomoditasOptions' => PurchaseOrder::getJenisKomoditasOptions(),
            'komplekPergudanganOptions' => PurchaseOrder::getKomplekPergudanganOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'jenis_komoditas' => 'required|string',
            'jenis_komoditas_custom' => 'nullable|string|max:255',
            'jenis_pengadaan' => 'required|in:PSO,Komersial',
            'tanggal_pembuatan' => 'required|date',
            'kualitas_items' => 'required|array|min:1',
            'kualitas_items.*.harga' => 'required|numeric|min:0',
            'kualitas_items.*.kuantum' => 'required|numeric|min:0',
            'kualitas_items.*.komplek_pergudangan' => 'required|string',
            'kualitas_items.*.komplek_pergudangan_custom' => 'nullable|string|max:255',
            'kualitas_items.*.kualitas' => 'required|string',
            'kualitas_items.*.kualitas_custom' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = auth()->user()->name ?? 'Admin';

        // Create the main Purchase Order (no need for user_id since admin creates it)
        $purchaseOrder = PurchaseOrder::create([
            'user_id' => auth()->id(), // Admin's ID
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'jenis_komoditas' => $validated['jenis_komoditas'],
            'jenis_komoditas_custom' => $validated['jenis_komoditas_custom'],
            'jenis_pengadaan' => $validated['jenis_pengadaan'],
            'tanggal_pembuatan' => $validated['tanggal_pembuatan'],
            'created_by' => $validated['created_by'],
        ]);
        
        // Generate no_surat setelah PO dibuat (supaya ada ID)
        $purchaseOrder->no_surat = $purchaseOrder->generateNoSurat();
        $purchaseOrder->save();

        // Create the quality items
        foreach ($validated['kualitas_items'] as $item) {
            $item['nilai'] = $item['harga'] * $item['kuantum'];
            $item['purchase_order_id'] = $purchaseOrder->id;
            \App\Models\PurchaseOrderItem::create($item);
        }

        // Detect user role for redirect
        $userRole = auth()->user()->role;
        $routeName = $userRole === 'super admin' 
            ? 'super-admin.purchase-orders.show' 
            : 'admin.purchase-orders.show';

        return redirect()->route($routeName, $purchaseOrder)
            ->with('success', 'Purchase Order berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        
        // Detect user role and render appropriate view
        $userRole = auth()->user()->role;
        $viewPath = $userRole === 'super admin' 
            ? 'SuperAdmin/PurchaseOrder/Show' 
            : 'Admin/PurchaseOrder/Show';

        return Inertia::render($viewPath, [
            'purchaseOrder' => [
                'id' => $purchaseOrder->id,
                'no_surat' => $purchaseOrder->no_surat,
                'nama_perusahaan' => $purchaseOrder->nama_perusahaan,
                'jenis_komoditas' => $purchaseOrder->jenis_komoditas_lengkap,
                'jenis_pengadaan' => $purchaseOrder->jenis_pengadaan,
                'total_harga' => (int) $purchaseOrder->total_harga,
                'total_kuantum' => (int) $purchaseOrder->total_kuantum,
                'total_nilai' => (int) $purchaseOrder->total_nilai,
                'agenda_no' => $purchaseOrder->agenda_no,
                'tanggal_terima' => $purchaseOrder->tanggal_terima?->format('Y-m-d'),
                'tanggal_pembuatan' => $purchaseOrder->tanggal_pembuatan?->format('Y-m-d'),
                'paraf' => $purchaseOrder->paraf,
                'kontrak_yll' => $purchaseOrder->kontrak_yll,
                'created_at' => $purchaseOrder->created_at->format('d/m/Y'),
                'created_by' => $purchaseOrder->created_by,
                'items' => $purchaseOrder->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'harga' => (int) $item->harga,
                        'kuantum' => (float) $item->kuantum,
                        'nilai' => (int) $item->nilai,
                        'komplek_pergudangan' => $item->komplek_pergudangan_lengkap,
                        'kualitas' => $item->kualitas_lengkap,
                    ];
                })
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        $mitras = \App\Models\DataMitra::orderBy('nama_perusahaan')->get();

        $userRole = auth()->user()->role;
        $viewPath = $userRole === 'super admin' 
            ? 'SuperAdmin/PurchaseOrder/Edit' 
            : 'Admin/PurchaseOrder/Edit';

        return Inertia::render($viewPath, [
            'purchaseOrder' => [
                'id' => $purchaseOrder->id,
                'no_surat' => $purchaseOrder->no_surat,
                'nama_perusahaan' => $purchaseOrder->nama_perusahaan,
                'jenis_komoditas' => $purchaseOrder->jenis_komoditas,
                'jenis_komoditas_custom' => $purchaseOrder->jenis_komoditas_custom,
                'jenis_pengadaan' => $purchaseOrder->jenis_pengadaan,
                'tanggal_pembuatan' => $purchaseOrder->tanggal_pembuatan?->format('Y-m-d'),
                'agenda_no' => $purchaseOrder->agenda_no,
                'tanggal_terima' => $purchaseOrder->tanggal_terima?->format('Y-m-d'),
                'paraf' => $purchaseOrder->paraf,
                'kontrak_yll' => $purchaseOrder->kontrak_yll,
                'items' => $purchaseOrder->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'harga' => (int) $item->harga,
                        'kuantum' => (float) $item->kuantum,
                        'nilai' => (int) $item->nilai,
                        'komplek_pergudangan' => $item->komplek_pergudangan,
                        'komplek_pergudangan_custom' => $item->komplek_pergudangan_custom,
                        'kualitas' => $item->kualitas,
                        'kualitas_custom' => $item->kualitas_custom,
                    ];
                })
            ],
            'mitras' => $mitras,
            'jenisKomoditasOptions' => PurchaseOrder::getJenisKomoditasOptions(),
            'komplekPergudanganOptions' => PurchaseOrder::getKomplekPergudanganOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'jenis_komoditas' => 'required|string',
            'jenis_komoditas_custom' => 'nullable|string|max:255',
            'jenis_pengadaan' => 'required|in:PSO,Komersial',
            'tanggal_pembuatan' => 'required|date',
            'kualitas_items' => 'required|array|min:1',
            'kualitas_items.*.id' => 'nullable|integer|exists:purchase_order_items,id',
            'kualitas_items.*.harga' => 'required|numeric|min:0',
            'kualitas_items.*.kuantum' => 'required|numeric|min:0',
            'kualitas_items.*.komplek_pergudangan' => 'required|string',
            'kualitas_items.*.komplek_pergudangan_custom' => 'nullable|string|max:255',
            'kualitas_items.*.kualitas' => 'required|string',
            'kualitas_items.*.kualitas_custom' => 'nullable|string|max:255',
            'agenda_no' => 'nullable|string|max:255',
            'tanggal_terima' => 'nullable|date',
            'paraf' => 'nullable|string',
            'kontrak_yll' => 'nullable|in:REALISASI S/D,DISETUJUI/TIDAK',
        ]);

        // Cek apakah perlu regenerate nomor surat
        $shouldRegenerateNoSurat = false;
        
        // 1. Cek apakah tanggal_pembuatan atau nama_perusahaan berubah
        if ($purchaseOrder->tanggal_pembuatan != $validated['tanggal_pembuatan'] ||
            $purchaseOrder->nama_perusahaan != $validated['nama_perusahaan']) {
            $shouldRegenerateNoSurat = true;
        }
        
        // 2. Cek apakah No VMS atau Kode Mitra di data mitra berubah
        if (!$shouldRegenerateNoSurat && $purchaseOrder->no_surat) {
            // Ambil data mitra terbaru
            $mitra = \App\Models\DataMitra::where('nama_perusahaan', $purchaseOrder->nama_perusahaan)->first();
            if ($mitra) {
                // Extract No VMS dan Kode Mitra dari no_surat yang lama
                $parts = explode('/', $purchaseOrder->no_surat);
                if (count($parts) >= 3) {
                    $oldNoVms = $parts[0];
                    $oldKodeMitra = $parts[2];
                    $newNoVms = !empty($mitra->no_vms) ? $mitra->no_vms : '000000';
                    $newKodeMitra = !empty($mitra->kode_mitra) ? strtoupper($mitra->kode_mitra) : 'XXX';
                    
                    // Jika berbeda, regenerate
                    if ($oldNoVms != $newNoVms || $oldKodeMitra != $newKodeMitra) {
                        $shouldRegenerateNoSurat = true;
                    }
                }
            }
        }

        // Update main PO data
        $purchaseOrder->update([
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'jenis_komoditas' => $validated['jenis_komoditas'],
            'jenis_komoditas_custom' => $validated['jenis_komoditas_custom'],
            'jenis_pengadaan' => $validated['jenis_pengadaan'],
            'tanggal_pembuatan' => $validated['tanggal_pembuatan'],
            'agenda_no' => $validated['agenda_no'],
            'tanggal_terima' => $validated['tanggal_terima'],
            'paraf' => $validated['paraf'],
            'kontrak_yll' => $validated['kontrak_yll'],
        ]);

        // Regenerate no_surat jika diperlukan
        if ($shouldRegenerateNoSurat) {
            $purchaseOrder->no_surat = $purchaseOrder->generateNoSurat();
            $purchaseOrder->save();
        }

        // Get existing item IDs
        $existingItemIds = $purchaseOrder->items->pluck('id')->toArray();
        $submittedItemIds = [];

        // Update or create items
        foreach ($validated['kualitas_items'] as $itemData) {
            $itemData['nilai'] = $itemData['harga'] * $itemData['kuantum'];
            
            if (isset($itemData['id']) && $itemData['id']) {
                // Update existing item
                $item = $purchaseOrder->items()->find($itemData['id']);
                if ($item) {
                    $item->update($itemData);
                    $submittedItemIds[] = $itemData['id'];
                }
            } else {
                // Create new item
                $itemData['purchase_order_id'] = $purchaseOrder->id;
                unset($itemData['id']); // Remove null id
                $newItem = \App\Models\PurchaseOrderItem::create($itemData);
                $submittedItemIds[] = $newItem->id;
            }
        }

        // Delete items that were removed
        $itemsToDelete = array_diff($existingItemIds, $submittedItemIds);
        if (!empty($itemsToDelete)) {
            \App\Models\PurchaseOrderItem::whereIn('id', $itemsToDelete)->delete();
        }

        $userRole = auth()->user()->role;
        $routeName = $userRole === 'super admin' 
            ? 'super-admin.purchase-orders.show' 
            : 'admin.purchase-orders.show';

        return redirect()->route($routeName, $purchaseOrder)
            ->with('success', 'Purchase Order berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Delete all related items first
        $purchaseOrder->items()->delete();
        
        // Then delete the purchase order
        $purchaseOrder->delete();

        $userRole = auth()->user()->role;
        $routeName = $userRole === 'super admin' 
            ? 'super-admin.purchase-orders.index' 
            : 'admin.purchase-orders.index';

        return redirect()->route($routeName)
            ->with('success', 'Purchase Order berhasil dihapus!');
    }

    /**
     * Generate PDF surat permohonan
     */
    public function generateSuratPermohonan(PurchaseOrder $purchaseOrder)
    {
        // Load relasi items dan mitra untuk PDF
        $purchaseOrder->load(['items', 'mitra']);

        // Gunakan tanggal_pembuatan dari PO, fallback ke Carbon::now() jika null
        $tanggal = $purchaseOrder->tanggal_pembuatan 
            ? $purchaseOrder->tanggal_pembuatan->locale('id')->translatedFormat('d F Y')
            : Carbon::now()->locale('id')->translatedFormat('d F Y');

        // Tentukan nama penandatangan berdasarkan surat kuasa
        $namaPenandatangan = $purchaseOrder->mitra->nama_cp ?? $purchaseOrder->created_by ?? '';
        if ($purchaseOrder->mitra && 
            $purchaseOrder->mitra->surat_kuasa === 'Ada' && 
            !empty($purchaseOrder->mitra->nama_yang_dikuasakan)) {
            $namaPenandatangan = $purchaseOrder->mitra->nama_yang_dikuasakan;
        }

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'mitra' => $purchaseOrder->mitra,
            'tanggal' => $tanggal,
            'no_surat' => $purchaseOrder->no_surat ?? 'NO Test',
            'nama_penandatangan' => $namaPenandatangan,
        ];

        $pdf = Pdf::loadView('pdf.surat-permohonan', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Surat_Permohonan_PO_' . $purchaseOrder->id . '.pdf');
    }

    /**
     * Generate PDF form penawaran
     */
    public function generateFormPenawaran(PurchaseOrder $purchaseOrder)
    {
        // Load relasi items dan mitra untuk PDF
        $purchaseOrder->load(['items', 'mitra']);

        // Gunakan tanggal_pembuatan dari PO, fallback ke Carbon::now() jika null
        $tanggal = $purchaseOrder->tanggal_pembuatan 
            ? $purchaseOrder->tanggal_pembuatan->locale('id')->translatedFormat('d F Y')
            : Carbon::now()->locale('id')->translatedFormat('d F Y');

        // Tentukan nama penandatangan berdasarkan surat kuasa
        $namaPenandatangan = $purchaseOrder->mitra->nama_cp ?? $purchaseOrder->created_by ?? '';
        if ($purchaseOrder->mitra && 
            $purchaseOrder->mitra->surat_kuasa === 'Ada' && 
            !empty($purchaseOrder->mitra->nama_yang_dikuasakan)) {
            $namaPenandatangan = $purchaseOrder->mitra->nama_yang_dikuasakan;
        }

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'mitra' => $purchaseOrder->mitra,
            'tanggal' => $tanggal,
            'nama_penandatangan' => $namaPenandatangan,
        ];

        $pdf = Pdf::loadView('pdf.form-penawaran', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Form_Penawaran_PO_' . $purchaseOrder->id . '.pdf');
    }

    /**
     * Generate Combined PDF (Surat Permohonan + Form Penawaran) using PDF Merger
     */
    public function generateCombinedPdf(PurchaseOrder $purchaseOrder)
    {
        // Load relasi items dan mitra untuk PDF
        $purchaseOrder->load('items', 'mitra');

        // Ambil data mitra
        $mitra = \App\Models\DataMitra::where('nama_perusahaan', $purchaseOrder->nama_perusahaan)->first();

        // Gunakan tanggal_pembuatan dari PO, fallback ke Carbon::now() jika null
        $tanggal = $purchaseOrder->tanggal_pembuatan 
            ? $purchaseOrder->tanggal_pembuatan->locale('id')->translatedFormat('d F Y')
            : Carbon::now()->locale('id')->translatedFormat('d F Y');

        // Tentukan nama penandatangan berdasarkan surat kuasa
        $namaPenandatangan = $mitra->nama_cp ?? $purchaseOrder->created_by ?? '';
        if ($mitra && 
            $mitra->surat_kuasa === 'Ada' && 
            !empty($mitra->nama_yang_dikuasakan)) {
            $namaPenandatangan = $mitra->nama_yang_dikuasakan;
        }

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'mitra' => $mitra,
            'tanggal' => $tanggal,
            'nama_penandatangan' => $namaPenandatangan,
        ];

        // Generate PDF 1: Surat Permohonan
        $pdf1 = Pdf::loadView('pdf.surat-permohonan', $data);
        $pdf1->setPaper('A4', 'portrait');
        $tempPath1 = storage_path('app/temp/surat-permohonan-' . $purchaseOrder->id . '.pdf');
        
        // Pastikan direktori temp ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $pdf1->save($tempPath1);

        // Generate PDF 2: Form Penawaran
        $pdf2 = Pdf::loadView('pdf.form-penawaran', $data);
        $pdf2->setPaper('A4', 'portrait');
        $tempPath2 = storage_path('app/temp/form-penawaran-' . $purchaseOrder->id . '.pdf');
        $pdf2->save($tempPath2);

        // Merge kedua PDF
        $merger = new PDFMerger();
        $merger->addPDF($tempPath1, 'all');
        $merger->addPDF($tempPath2, 'all');
        
        $outputPath = storage_path('app/temp/combined-po-' . $purchaseOrder->id . '.pdf');
        $merger->merge('file', $outputPath);

        // Hapus file temporary
        @unlink($tempPath1);
        @unlink($tempPath2);

        // Buat nama file yang aman (tanpa karakter / dan \)
        $safeFilename = 'Purchase_Order_' . str_replace(['/', '\\'], '_', $purchaseOrder->no_surat) . '.pdf';

        // Return merged PDF
        return response()->download($outputPath, $safeFilename)->deleteFileAfterSend(true);
    }

    /**
     * Get kualitas options based on jenis komoditas (AJAX)
     */
    public function getKualitasOptions(Request $request)
    {
        $jenisKomoditas = $request->input('jenis_komoditas');
        $options = PurchaseOrder::getKualitasOptions($jenisKomoditas);
        
        return response()->json($options);
    }
}
