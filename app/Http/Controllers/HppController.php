<?php

namespace App\Http\Controllers;

use App\Models\HppDocument;
use App\Models\HppKomoditas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HppController extends Controller
{
    /**
     * Display listing for admin dashboard
     */
    public function index()
    {
        $documents = HppDocument::with('komoditas')
            ->orderBy('tahun', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/HPP/Index', [
            'documents' => $documents
        ]);
    }

    /**
     * Get all HPP documents (API for landing page)
     */
    public function getAllDocuments()
    {
        $documents = HppDocument::with('komoditas')
            ->active()
            ->orderBy('tahun', 'desc')
            ->get();

        return response()->json($documents);
    }

    /**
     * Get HPP by jenis (API for landing page)
     */
    public function getByJenis($jenis)
    {
        $document = HppDocument::with('komoditas')
            ->active()
            ->byJenis($jenis)
            ->orderBy('tahun', 'desc')
            ->first();

        if (!$document) {
            return response()->json([
                'message' => 'Data HPP tidak ditemukan'
            ], 404);
        }

        return response()->json($document);
    }

    /**
     * Store new HPP document with komoditas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_hpp' => 'required|in:Gabah dan Beras,Jagung',
            'nomor_keputusan' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2100',
            'tentang' => 'required|string',
            'is_active' => 'boolean',
            'komoditas' => 'required|array|min:1',
            'komoditas.*.nama_komoditas' => 'required|string|max:255',
            'komoditas.*.tempat' => 'required|string|max:255',
            'komoditas.*.harga_per_kg' => 'required|numeric|min:0',
            'komoditas.*.ketentuan' => 'nullable|string',
            'komoditas.*.urutan' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Create document
            $document = HppDocument::create([
                'jenis_hpp' => $validated['jenis_hpp'],
                'nomor_keputusan' => $validated['nomor_keputusan'],
                'tahun' => $validated['tahun'],
                'tentang' => $validated['tentang'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Create komoditas
            foreach ($validated['komoditas'] as $index => $komoditasData) {
                $document->komoditas()->create([
                    'nama_komoditas' => $komoditasData['nama_komoditas'],
                    'tempat' => $komoditasData['tempat'],
                    'harga_per_kg' => $komoditasData['harga_per_kg'],
                    'ketentuan' => $komoditasData['ketentuan'] ?? null,
                    'urutan' => $komoditasData['urutan'] ?? $index,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data HPP berhasil ditambahkan',
                'data' => $document->load('komoditas')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating HPP document: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single HPP document
     */
    public function show($id)
    {
        $document = HppDocument::with('komoditas')->findOrFail($id);
        return response()->json($document);
    }

    /**
     * Update HPP document and komoditas
     */
    public function update(Request $request, $id)
    {
        $document = HppDocument::findOrFail($id);

        $validated = $request->validate([
            'jenis_hpp' => 'required|in:Gabah dan Beras,Jagung',
            'nomor_keputusan' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2100',
            'tentang' => 'required|string',
            'is_active' => 'boolean',
            'komoditas' => 'required|array|min:1',
            'komoditas.*.id' => 'nullable|integer|exists:hpp_komoditas,id',
            'komoditas.*.nama_komoditas' => 'required|string|max:255',
            'komoditas.*.tempat' => 'required|string|max:255',
            'komoditas.*.harga_per_kg' => 'required|numeric|min:0',
            'komoditas.*.ketentuan' => 'nullable|string',
            'komoditas.*.urutan' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Update document
            $document->update([
                'jenis_hpp' => $validated['jenis_hpp'],
                'nomor_keputusan' => $validated['nomor_keputusan'],
                'tahun' => $validated['tahun'],
                'tentang' => $validated['tentang'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Get existing komoditas IDs
            $existingIds = $document->komoditas->pluck('id')->toArray();
            $submittedIds = collect($validated['komoditas'])
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete komoditas that are not in the submitted data
            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                HppKomoditas::whereIn('id', $toDelete)->delete();
            }

            // Update or create komoditas
            foreach ($validated['komoditas'] as $index => $komoditasData) {
                if (isset($komoditasData['id']) && $komoditasData['id']) {
                    // Update existing
                    HppKomoditas::where('id', $komoditasData['id'])->update([
                        'nama_komoditas' => $komoditasData['nama_komoditas'],
                        'tempat' => $komoditasData['tempat'],
                        'harga_per_kg' => $komoditasData['harga_per_kg'],
                        'ketentuan' => $komoditasData['ketentuan'] ?? null,
                        'urutan' => $komoditasData['urutan'] ?? $index,
                    ]);
                } else {
                    // Create new
                    $document->komoditas()->create([
                        'nama_komoditas' => $komoditasData['nama_komoditas'],
                        'tempat' => $komoditasData['tempat'],
                        'harga_per_kg' => $komoditasData['harga_per_kg'],
                        'ketentuan' => $komoditasData['ketentuan'] ?? null,
                        'urutan' => $komoditasData['urutan'] ?? $index,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Data HPP berhasil diperbarui',
                'data' => $document->fresh()->load('komoditas')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating HPP document: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete HPP document
     */
    public function destroy($id)
    {
        try {
            $document = HppDocument::findOrFail($id);
            $document->delete(); // Will cascade delete komoditas

            return response()->json([
                'message' => 'Data HPP berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting HPP document: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $document = HppDocument::findOrFail($id);
            $document->is_active = !$document->is_active;
            $document->save();

            return response()->json([
                'message' => 'Status HPP berhasil diubah',
                'is_active' => $document->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling HPP status: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengubah status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
