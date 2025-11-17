<?php

namespace App\Http\Controllers;

use App\Models\KlasifikasiMitra;
use App\Models\DataMitra;
use App\Imports\KlasifikasiMitraImport;
use App\Exports\KlasifikasiMitraExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class KlasifikasiMitraController extends Controller
{
    public function index()
    {
        $klasifikasis = KlasifikasiMitra::all();
        return response()->json($klasifikasis);
    }

    public function myKlasifikasi(){
        $userId = auth()->id();
        $mitra = DataMitra::where('user_id', $userId)->first();

        if (!$mitra) {
            return response()->json(['message' => 'Data mitra tidak ditemukan'], 404);
        }

        $klasifikasi = KlasifikasiMitra::with('mitra')
            ->where('id_mitra', $mitra->id_mitra)
            ->get();
        return response()->json($klasifikasi);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mitra' => 'required|exists:data_mitra,id_mitra',
            'mesin_pembersih_gabah' => ['nullable', Rule::in(['1. Tidak Ada', '2. Ada | ≤ 20', '3. Ada | > 20'])],
            'lantai_jemur' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 10', '3. Ada | > 10'])],
            'mesin_pengering' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | ≤ 20', '3. Ada | > 20'])],
            'alat_pengering_lainnya' => ['nullable', Rule::in(['1. Ada | ≤ 1', '2. Tidak Disyaratkan', '3. Tidak Disyaratkan'])],
            'mesin_pembersih_awal' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemecah_kulit' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pembersih_sekam' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_gabah_pecah_kulit' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_batu' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_penyosoh' => ['nullable', Rule::in(['1. Ada | ≤ 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2'])],
            'mesin_pengkabut' => ['nullable', Rule::in(['1. Ada | ≤ 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2'])],
            'mesin_pemesah_menir' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_katul' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_berdasarkan_ukuran' => ['nullable', Rule::in(['1. Ada | ≤ 1', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_berdasarkan_warna' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'tangki_penyimpanan' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | ≤ 10', '3. Ada | > 10'])],
            'mesin_pengemas' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis'])],
            'mesin_jahit' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis'])],
            'gudang_konvensional' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | < 3000', '3. Ada | > 3000'])],
            'silo_gkg_hopper' => ['nullable', Rule::in(['1. Tidak ada', '2. Tidak Ada', '3. Ada | > 2000'])],
            'truk' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 5', '3. Ada | > 5'])],
            'mini_lab' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Tidak Khusus', '3. Ada | Ruang Khusus'])],
            'moisture_tester' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Berfungsi', '3. Ada | Lengkap | Berfungsi'])],
            'pembanding_derajat_sosoh' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Tidak Sesuai', '3. Ada | Sesuai Standar'])],
            'bagian_quality_control' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Merangkap', '3. Ada | Tidak Merangkap'])],
            'hasil_klasifikasi' => ['nullable', Rule::in(['A', 'B', 'C'])]
        ]);

        $klasifikasi = KlasifikasiMitra::create($validated);

        return response()->json($klasifikasi, 201);
    }

    public function show($id)
    {
        $klasifikasi = KlasifikasiMitra::findOrFail($id);
        return response()->json($klasifikasi);
    }

    public function update(Request $request, $id)
    {
        $klasifikasi = KlasifikasiMitra::findOrFail($id);

        $validated = $request->validate([
            'id_mitra' => 'sometimes|required|exists:data_mitra,id_mitra',
            'mesin_pembersih_gabah' => ['nullable', Rule::in(['1. Tidak Ada', '2. Ada | ≤ 20', '3. Ada | > 20'])],
            'lantai_jemur' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 10', '3. Ada | > 10'])],
            'mesin_pengering' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | ≤ 20', '3. Ada | > 20'])],
            'alat_pengering_lainnya' => ['nullable', Rule::in(['1. Ada | ≤ 1', '2. Tidak Disyaratkan', '3. Tidak Disyaratkan'])],
            'mesin_pembersih_awal' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemecah_kulit' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pembersih_sekam' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_gabah_pecah_kulit' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_batu' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_penyosoh' => ['nullable', Rule::in(['1. Ada | ≤ 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2'])],
            'mesin_pengkabut' => ['nullable', Rule::in(['1. Ada | ≤ 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2'])],
            'mesin_pemesah_menir' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_katul' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_berdasarkan_ukuran' => ['nullable', Rule::in(['1. Ada | ≤ 1', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'mesin_pemisah_berdasarkan_warna' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])],
            'tangki_penyimpanan' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | ≤ 10', '3. Ada | > 10'])],
            'mesin_pengemas' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis'])],
            'mesin_jahit' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis'])],
            'gudang_konvensional' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | < 3000', '3. Ada | > 3000'])],
            'silo_gkg_hopper' => ['nullable', Rule::in(['1. Tidak ada', '2. Tidak Ada', '3. Ada | > 2000'])],
            'truk' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | 1 s/d 5', '3. Ada | > 5'])],
            'mini_lab' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Tidak Khusus', '3. Ada | Ruang Khusus'])],
            'moisture_tester' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Berfungsi', '3. Ada | Lengkap | Berfungsi'])],
            'pembanding_derajat_sosoh' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Tidak Sesuai', '3. Ada | Sesuai Standar'])],
            'bagian_quality_control' => ['nullable', Rule::in(['1. Tidak ada', '2. Ada | Merangkap', '3. Ada | Tidak Merangkap'])],
            'hasil_klasifikasi' => ['nullable', Rule::in(['A', 'B', 'C'])]
        ]);

        $klasifikasi->update($validated);

        return response()->json($klasifikasi);
    }

    public function destroy($id)
    {
        $klasifikasi = KlasifikasiMitra::findOrFail($id);
        $klasifikasi->delete();

        return response()->json(['message' => 'Klasifikasi mitra berhasil dihapus']);
    }

    /**
     * Import data klasifikasi mitra from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // max 5MB
        ]);

        try {
            $import = new KlasifikasiMitraImport();
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

            return response()->json([
                'message' => 'Data klasifikasi mitra berhasil diimport'
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
     * Export data klasifikasi mitra to Excel
     */
    public function export()
    {
        return Excel::download(new KlasifikasiMitraExport(false), 'data-klasifikasi-mitra-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Download template Excel untuk import
     */
    public function downloadTemplate()
    {
        return Excel::download(new KlasifikasiMitraExport(true), 'template-data-klasifikasi-mitra.xlsx');
    }
}
