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
        $purchaseOrders = PurchaseOrder::with('items')
            ->latest()
            ->paginate(10)
            ->through(fn ($po) => [
                'id' => $po->id,
                'nama_perusahaan' => $po->nama_perusahaan,
                'jenis_komoditas' => $po->jenis_komoditas_lengkap,
                'jenis_pengadaan' => $po->jenis_pengadaan,
                'total_harga' => number_format($po->total_harga, 0, ',', '.'),
                'total_kuantum' => number_format($po->total_kuantum, 0, ',', '.'),
                'total_nilai' => number_format($po->total_nilai, 0, ',', '.'),
                'items_count' => $po->items->count(),
                'created_at' => $po->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Mitra/PurchaseOrder/Index', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Mitra/PurchaseOrder/Create', [
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

        $validated['created_by'] = auth()->user()->name ?? 'System';

        // Create the main Purchase Order
        $purchaseOrder = PurchaseOrder::create([
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'jenis_komoditas' => $validated['jenis_komoditas'],
            'jenis_komoditas_custom' => $validated['jenis_komoditas_custom'],
            'jenis_pengadaan' => $validated['jenis_pengadaan'],
            'created_by' => $validated['created_by'],
        ]);

        // Create the quality items
        foreach ($validated['kualitas_items'] as $item) {
            $item['nilai'] = $item['harga'] * $item['kuantum'];
            $item['purchase_order_id'] = $purchaseOrder->id;
            \App\Models\PurchaseOrderItem::create($item);
        }

        return redirect()->route('mitra.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        
        return Inertia::render('Mitra/PurchaseOrder/Show', [
            'purchaseOrder' => [
                'id' => $purchaseOrder->id,
                'nama_perusahaan' => $purchaseOrder->nama_perusahaan,
                'jenis_komoditas' => $purchaseOrder->jenis_komoditas_lengkap,
                'jenis_pengadaan' => $purchaseOrder->jenis_pengadaan,
                'total_harga' => $purchaseOrder->total_harga,
                'total_kuantum' => $purchaseOrder->total_kuantum,
                'total_nilai' => $purchaseOrder->total_nilai,
                'agenda_no' => $purchaseOrder->agenda_no,
                'tanggal_terima' => $purchaseOrder->tanggal_terima?->format('Y-m-d'),
                'paraf' => $purchaseOrder->paraf,
                'kontrak_yll' => $purchaseOrder->kontrak_yll,
                'created_at' => $purchaseOrder->created_at->format('d/m/Y H:i'),
                'created_by' => $purchaseOrder->created_by,
                'items' => $purchaseOrder->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'harga' => $item->harga,
                        'kuantum' => $item->kuantum,
                        'nilai' => $item->nilai,
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
        return Inertia::render('Mitra/PurchaseOrder/Edit', [
            'purchaseOrder' => $purchaseOrder,
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
            'harga' => 'required|numeric|min:0',
            'kuantum' => 'required|numeric|min:0',
            'komplek_pergudangan' => 'required|string',
            'komplek_pergudangan_custom' => 'nullable|string|max:255',
            'kualitas' => 'required|string',
            'kualitas_custom' => 'nullable|string|max:255',
            'agenda_no' => 'nullable|string|max:255',
            'tanggal_terima' => 'nullable|date',
            'paraf' => 'nullable|string',
            'kontrak_yll' => 'nullable|in:REALISASI S/D,DISETUJUI/TIDAK',
        ]);

        // Hitung ulang nilai
        $validated['nilai'] = $validated['harga'] * $validated['kuantum'];

        $purchaseOrder->update($validated);

        return redirect()->route('mitra.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('mitra.purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus!');
    }

    /**
     * Generate PDF surat permohonan
     */
    public function generateSuratPermohonan(PurchaseOrder $purchaseOrder)
    {
        $data = [
            'purchaseOrder' => $purchaseOrder,
            'tanggal' => Carbon::now()->locale('id')->translatedFormat('d F Y'),
            'no_surat' => 'NO Test',
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
        $data = [
            'purchaseOrder' => $purchaseOrder,
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
        $data = [
            'purchaseOrder' => $purchaseOrder,
            'tanggal' => Carbon::now()->locale('id')->translatedFormat('d F Y'),
            'no_surat' => 'NO Test',
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
