<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

        return Inertia::render('Admin/PurchaseOrder/Index', [
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
        
        return Inertia::render('Admin/PurchaseOrder/Create', [
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
            'kualitas_items' => 'required|array|min:1',
            'kualitas_items.*.harga' => 'required|numeric|min:0',
            'kualitas_items.*.kuantum' => 'required|numeric|min:0',
            'kualitas_items.*.komplek_pergudangan' => 'required|string',
            'kualitas_items.*.komplek_pergudangan_custom' => 'nullable|string|max:255',
            'kualitas_items.*.kualitas' => 'required|string',
            'kualitas_items.*.kualitas_custom' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = auth()->user()->name ?? 'Admin';
        $validated['no_surat'] = PurchaseOrder::generateNoSurat($validated['nama_perusahaan']);

        // Create the main Purchase Order (no need for user_id since admin creates it)
        $purchaseOrder = PurchaseOrder::create([
            'user_id' => auth()->id(), // Admin's ID
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'jenis_komoditas' => $validated['jenis_komoditas'],
            'jenis_komoditas_custom' => $validated['jenis_komoditas_custom'],
            'jenis_pengadaan' => $validated['jenis_pengadaan'],
            'no_surat' => $validated['no_surat'],
            'created_by' => $validated['created_by'],
        ]);

        // Create the quality items
        foreach ($validated['kualitas_items'] as $item) {
            $item['nilai'] = $item['harga'] * $item['kuantum'];
            $item['purchase_order_id'] = $purchaseOrder->id;
            \App\Models\PurchaseOrderItem::create($item);
        }

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        
        return Inertia::render('Admin/PurchaseOrder/Show', [
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
                'paraf' => $purchaseOrder->paraf,
                'kontrak_yll' => $purchaseOrder->kontrak_yll,
                'created_at' => $purchaseOrder->created_at->format('d/m/Y H:i'),
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

        return Inertia::render('Admin/PurchaseOrder/Edit', [
            'purchaseOrder' => [
                'id' => $purchaseOrder->id,
                'no_surat' => $purchaseOrder->no_surat,
                'nama_perusahaan' => $purchaseOrder->nama_perusahaan,
                'jenis_komoditas' => $purchaseOrder->jenis_komoditas,
                'jenis_komoditas_custom' => $purchaseOrder->jenis_komoditas_custom,
                'jenis_pengadaan' => $purchaseOrder->jenis_pengadaan,
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

        // Update main PO data
        $purchaseOrder->update([
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'jenis_komoditas' => $validated['jenis_komoditas'],
            'jenis_komoditas_custom' => $validated['jenis_komoditas_custom'],
            'jenis_pengadaan' => $validated['jenis_pengadaan'],
            'agenda_no' => $validated['agenda_no'],
            'tanggal_terima' => $validated['tanggal_terima'],
            'paraf' => $validated['paraf'],
            'kontrak_yll' => $validated['kontrak_yll'],
        ]);

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

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
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

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus!');
    }

    /**
     * Generate PDF surat permohonan
     */
    public function generateSuratPermohonan(PurchaseOrder $purchaseOrder)
    {
        // Load relasi items dan mitra untuk PDF
        $purchaseOrder->load(['items', 'mitra']);

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'mitra' => $purchaseOrder->mitra,
            'tanggal' => Carbon::now()->locale('id')->translatedFormat('d F Y'),
            'no_surat' => $purchaseOrder->no_surat ?? 'NO Test',
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

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'mitra' => $purchaseOrder->mitra,
            'tanggal' => Carbon::now()->locale('id')->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.form-penawaran', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Form_Penawaran_PO_' . $purchaseOrder->id . '.pdf');
    }

    /**
     * Generate Combined PDF (Surat Permohonan + Form Penawaran)
     */
    public function generateCombinedPdf(PurchaseOrder $purchaseOrder)
    {
        // Load relasi items untuk PDF
        $purchaseOrder->load('items');

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'tanggal' => Carbon::now()->locale('id')->translatedFormat('d F Y'),
            'no_surat' => $purchaseOrder->no_surat ?? 'NO Test',
        ];

        $pdf = Pdf::loadView('pdf.combined-purchase-order', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Purchase_Order_' . $purchaseOrder->id . '.pdf');
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
