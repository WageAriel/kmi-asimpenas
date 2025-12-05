<?php

namespace App\Http\Controllers;

use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
use App\Services\ActivityAggregatorService;
use App\Imports\DataSeleksiMitraImport;
use App\Exports\DataSeleksiMitraExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DataSeleksiMitraController extends Controller
{
    public function index()
    {
        $seleksimitras = DataSeleksiMitra::all();
        return response()->json($seleksimitras);
    }
    public function indexByMitra()
{
    $seleksimitras = DataSeleksiMitra::with('mitra')->get();
    return response()->json($seleksimitras);
}



public function mySeleksi()
{
    // Ambil user yang sedang login
    $userId = auth()->id();

    // Ambil satu baris DataMitra milik user (asumsi satu user = satu mitra)
    $mitra = DataMitra::where('user_id', $userId)->first();

    // Jika Tidak Ada mitra
    if (!$mitra) {
        return response()->json(['message' => 'Data mitra Tidak ditemukan'], 404);
    }

    // Ambil semua seleksi milik mitra ini, beserta detail mitra
    $seleksi = DataSeleksiMitra::with('mitra')
        ->where('id_mitra', $mitra->id_mitra)
        ->get();

    return response()->json($seleksi);
}



    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mitra' => 'required|exists:data_mitra,id_mitra',
            'surat_permohonan' => 'nullable|in:Ada,Tidak Ada',
            'mb_surat_permohonan' => 'nullable|date',
            'akta_notaris' => 'nullable|in:Ada,Tidak Ada',
            'mb_akta_notaris' => 'nullable|date',
            'nib' => 'nullable|in:Ada,Tidak Ada',
            'mb_nib' => 'nullable|date',
            'ktp' => 'nullable|in:Ada,Tidak Ada',
            'no_rekening' => 'nullable|in:Ada,Tidak Ada',
            'mb_no_rekening' => 'nullable|date',
            'npwp' => 'nullable|in:Ada,Tidak Ada',
            'mb_npwp' => 'nullable|date',
            'surat_kuasa' => 'nullable|in:Ada,Tidak Ada',
            'mb_surat_kuasa' => 'nullable|date',
            'lantai_jemur' => 'nullable|in:Ada,Tidak Ada',
            'sarana_lainnya' => 'nullable|in:Ada,Tidak Ada',
            'mesin_memecah_kulit' => 'nullable|in:Ada,Tidak Ada',
            'mesin_pemisah_gabah' => 'nullable|in:Ada,Tidak Ada',
            'mesin_penyosoh' => 'nullable|in:Ada,Tidak Ada',
            'alat_pemisah_beras' => 'nullable|in:Ada,Tidak Ada',
        ]);

        $seleksimitra = DataSeleksiMitra::create($validated);
        
        // Clear cache for dashboard updates
        if (Auth::check()) {
            ActivityAggregatorService::clearUserCache(Auth::id());
        }
        ActivityAggregatorService::clearAllActivitiesCache();
        
        return response()->json($seleksimitra, 201);
    }
    public function show($id)
    {
        $seleksimitra = DataSeleksiMitra::findOrFail($id);
        return response()->json($seleksimitra);
    }

    public function update(Request $request, $id)
    {
        $seleksimitra = DataSeleksiMitra::findOrFail($id);

        $validated = $request->validate([
            'id_mitra' => 'required|exists:data_mitra,id_mitra',
            'surat_permohonan' => 'nullable|in:Ada,Tidak Ada',
            'mb_surat_permohonan' => 'nullable|date',
            'akta_notaris' => 'nullable|in:Ada,Tidak Ada',
            'mb_akta_notaris' => 'nullable|date',
            'nib' => 'nullable|in:Ada,Tidak Ada',
            'mb_nib' => 'nullable|date',
            'ktp' => 'nullable|in:Ada,Tidak Ada',
            'no_rekening' => 'nullable|in:Ada,Tidak Ada',
            'mb_no_rekening' => 'nullable|date',
            'npwp' => 'nullable|in:Ada,Tidak Ada',
            'mb_npwp' => 'nullable|date',
            'surat_kuasa' => 'nullable|in:Ada,Tidak Ada',
            'mb_surat_kuasa' => 'nullable|date',
            'lantai_jemur' => 'nullable|in:Ada,Tidak Ada',
            'sarana_lainnya' => 'nullable|in:Ada,Tidak Ada',
            'mesin_memecah_kulit' => 'nullable|in:Ada,Tidak Ada',
            'mesin_pemisah_gabah' => 'nullable|in:Ada,Tidak Ada',
            'mesin_penyosoh' => 'nullable|in:Ada,Tidak Ada',
            'alat_pemisah_beras' => 'nullable|in:Ada,Tidak Ada',
            'status_seleksi' => 'nullable|in:pending,lolos,Tidak lolos',
        ]);

        $seleksimitra->update($validated);
        
        // Clear cache for dashboard updates
        if (Auth::check()) {
            ActivityAggregatorService::clearUserCache(Auth::id());
        }
        ActivityAggregatorService::clearAllActivitiesCache();
        
        return response()->json($seleksimitra);
    }

    public function destroy($id)
    {
        $seleksimitra = DataSeleksiMitra::findOrFail($id);
        
        // Hapus hasil seleksi terkait jika ada (cascade delete)
        if ($seleksimitra->hasilSeleksi) {
            $seleksimitra->hasilSeleksi->delete();
        }
        
        $seleksimitra->delete();
        
        // Clear cache for dashboard updates
        if (Auth::check()) {
            ActivityAggregatorService::clearUserCache(Auth::id());
        }
        ActivityAggregatorService::clearAllActivitiesCache();
        
        return response()->json(['message' => 'Data seleksi mitra berhasil dihapus']);
    }

    /**
     * Import data seleksi mitra from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // max 5MB
        ]);

        try {
            $import = new DataSeleksiMitraImport();
            Excel::import($import, $request->file('file'));

            // Check if there are any failures
            $failures = $import->failures();
            
            if ($failures->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = [
                        'row' => $failure->row(),
                        'attribute' => $failure->attribute(),
                        'errors' => $failure->errors(),
                    ];
                }
                
                return response()->json([
                    'message' => 'Import selesai dengan beberapa error',
                    'failures' => $errorMessages
                ], 422);
            }

            // Clear cache for dashboard updates
            if (Auth::check()) {
                ActivityAggregatorService::clearUserCache(Auth::id());
            }
            ActivityAggregatorService::clearAllActivitiesCache();

            return response()->json([
                'message' => 'Data seleksi mitra berhasil diimport'
            ], 200);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            }
            
            return response()->json([
                'message' => 'Validasi gagal',
                'failures' => $errorMessages
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data seleksi mitra to Excel
     */
    public function export()
    {
        return Excel::download(new DataSeleksiMitraExport(false), 'data-seleksi-mitra-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Download template Excel untuk import
     */
    public function downloadTemplate()
    {
        // Path ke file template yang sudah ada
        $templatePath = storage_path('app/public/templates/template-data-seleksi-mitra.xlsx');
        
        // Cek apakah file template ada
        if (file_exists($templatePath)) {
            // Download file yang sudah ada
            return response()->download($templatePath, 'template-data-seleksi-mitra.xlsx');
        }
        
        // Fallback: jika file tidak ada, generate menggunakan Export
        return Excel::download(new DataSeleksiMitraExport(true), 'template-data-seleksi-mitra.xlsx');
    }

    /**
     * Bulk delete data seleksi mitra
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:data_seleksi_mitra,id_seleksi_mitra'
            ]);

            $deletedCount = DataSeleksiMitra::whereIn('id_seleksi_mitra', $request->ids)->delete();

            return response()->json([
                'message' => "Berhasil menghapus {$deletedCount} data seleksi mitra",
                'deleted_count' => $deletedCount
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

}
