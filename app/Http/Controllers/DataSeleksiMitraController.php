<?php

namespace App\Http\Controllers;

use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
use App\Services\ActivityAggregatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // Jika tidak ada mitra
    if (!$mitra) {
        return response()->json(['message' => 'Data mitra tidak ditemukan'], 404);
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
            'surat_permohonan' => 'nullable|in:ada,tidak ada',
            'mb_surat_permohonan' => 'nullable|date',
            'akta_notaris' => 'nullable|in:ada,tidak ada',
            'mb_akta_notaris' => 'nullable|date',
            'nib' => 'nullable|in:ada,tidak ada',
            'mb_nib' => 'nullable|date',
            'ktp' => 'nullable|in:ada,tidak ada',
            'no_rekening' => 'nullable|in:ada,tidak ada',
            'mb_no_rekening' => 'nullable|date',
            'npwp' => 'nullable|in:ada,tidak ada',
            'mb_npwp' => 'nullable|date',
            'surat_kuasa' => 'nullable|in:ada,tidak ada',
            'mb_surat_kuasa' => 'nullable|date',
            'lantai_jemur' => 'nullable|in:ada,tidak ada',
            'sarana_lainnya' => 'nullable|in:ada,tidak ada',
            'mesin_memecah_kulit' => 'nullable|in:ada,tidak ada',
            'mesin_pemisah_gabah' => 'nullable|in:ada,tidak ada',
            'mesin_penyosoh' => 'nullable|in:ada,tidak ada',
            'alat_pemisah_beras' => 'nullable|in:ada,tidak ada',
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
            'surat_permohonan' => 'nullable|in:ada,tidak ada',
            'mb_surat_permohonan' => 'nullable|date',
            'akta_notaris' => 'nullable|in:ada,tidak ada',
            'mb_akta_notaris' => 'nullable|date',
            'nib' => 'nullable|in:ada,tidak ada',
            'mb_nib' => 'nullable|date',
            'ktp' => 'nullable|in:ada,tidak ada',
            'no_rekening' => 'nullable|in:ada,tidak ada',
            'mb_no_rekening' => 'nullable|date',
            'npwp' => 'nullable|in:ada,tidak ada',
            'mb_npwp' => 'nullable|date',
            'surat_kuasa' => 'nullable|in:ada,tidak ada',
            'mb_surat_kuasa' => 'nullable|date',
            'lantai_jemur' => 'nullable|in:ada,tidak ada',
            'sarana_lainnya' => 'nullable|in:ada,tidak ada',
            'mesin_memecah_kulit' => 'nullable|in:ada,tidak ada',
            'mesin_pemisah_gabah' => 'nullable|in:ada,tidak ada',
            'mesin_penyosoh' => 'nullable|in:ada,tidak ada',
            'alat_pemisah_beras' => 'nullable|in:ada,tidak ada',
            'status_seleksi' => 'nullable|in:pending,lolos,tidak lolos',
        ]);

        $seleksimitra->update($validated);
        
        // Jika status diubah oleh admin, buat atau update hasil seleksi
        if (isset($validated['status_seleksi']) && in_array($validated['status_seleksi'], ['lolos', 'tidak lolos'])) {
            $this->createOrUpdateHasilSeleksi($seleksimitra, $validated['status_seleksi']);
        }
        
        // Clear cache for dashboard updates
        if (Auth::check()) {
            ActivityAggregatorService::clearUserCache(Auth::id());
        }
        ActivityAggregatorService::clearAllActivitiesCache();
        
        return response()->json($seleksimitra);
    }
    
    /**
     * Create or update hasil seleksi based on admin decision
     */
    private function createOrUpdateHasilSeleksi($seleksimitra, $status)
    {
        $hasilSeleksi = \App\Models\HasilSeleksiMitra::where('id_seleksi_mitra', $seleksimitra->id_seleksi_mitra)->first();
        
        $kesimpulanAkhir = ($status === 'lolos') ? 'Lolos' : 'Tidak Lolos';
        
        // Proses dokumen
        $dokumenMapping = [
            'surat_permohonan' => 'Surat Permohonan',
            'akta_notaris' => 'Akta Notaris',
            'nib' => 'NIB',
            'ktp' => 'KTP',
            'no_rekening' => 'No Rekening',
            'npwp' => 'NPWP',
            'surat_kuasa' => 'Surat Kuasa',
        ];

        $dokumenAdaValid = [];
        $dokumenTidakAda = [];
        $dokumenLolos = 0;
        $dokumenTotal = 0;

        foreach ($dokumenMapping as $field => $label) {
            if ($seleksimitra->$field === 'ada') {
                $dokumenTotal++;
                if ($status === 'lolos') {
                    $dokumenAdaValid[] = $label;
                    $dokumenLolos++;
                } else {
                    $dokumenAdaValid[] = $label;
                }
            } elseif ($seleksimitra->$field === 'tidak ada') {
                $dokumenTidakAda[] = $label;
                $dokumenTotal++;
            }
        }

        $kesimpulanDokumen = ($dokumenTotal > 0 && $dokumenLolos === $dokumenTotal) ? 'Lolos' : 'Tidak Lolos';

        // Proses sarana pengeringan
        $pengeringanMapping = [
            'lantai_jemur' => 'Lantai Jemur',
            'sarana_lainnya' => 'Sarana Lainnya',
        ];

        $pengeringanAda = [];
        $pengeringanTidakAda = [];
        $pengeringanLolos = 0;
        $pengeringanTotal = 0;

        foreach ($pengeringanMapping as $field => $label) {
            if ($seleksimitra->$field === 'ada') {
                $pengeringanTotal++;
                if ($status === 'lolos') {
                    $pengeringanAda[] = $label;
                    $pengeringanLolos++;
                } else {
                    $pengeringanAda[] = $label;
                }
            } elseif ($seleksimitra->$field === 'tidak ada') {
                $pengeringanTidakAda[] = $label;
                $pengeringanTotal++;
            }
        }

        $kesimpulanPengeringan = ($pengeringanTotal > 0 && $pengeringanLolos === $pengeringanTotal) ? 'Lolos' : 'Tidak Lolos';

        // Proses sarana penggilingan
        $penggilinganMapping = [
            'mesin_memecah_kulit' => 'Mesin Memecah Kulit',
            'mesin_pemisah_gabah_dengan_beras' => 'Mesin Pemisah Gabah dengan Beras',
            'mesin_penyosoh' => 'Mesin Penyosoh',
            'alat_pemisah_beras' => 'Alat Pemisah Beras',
        ];

        $penggilinganAda = [];
        $penggilinganTidakAda = [];
        $penggilinganLolos = 0;
        $penggilinganTotal = 0;

        foreach ($penggilinganMapping as $field => $label) {
            // Untuk mesin_pemisah_gabah_dengan_beras, cek field mesin_pemisah_gabah
            $seleksiField = ($field === 'mesin_pemisah_gabah_dengan_beras') ? 'mesin_pemisah_gabah' : $field;
            
            if ($seleksimitra->$seleksiField === 'ada') {
                $penggilinganTotal++;
                if ($status === 'lolos') {
                    $penggilinganAda[] = $label;
                    $penggilinganLolos++;
                } else {
                    $penggilinganAda[] = $label;
                }
            } elseif ($seleksimitra->$seleksiField === 'tidak ada') {
                $penggilinganTidakAda[] = $label;
                $penggilinganTotal++;
            }
        }

        $kesimpulanPenggilingan = ($penggilinganTotal > 0 && $penggilinganLolos === $penggilinganTotal) ? 'Lolos' : 'Tidak Lolos';
        
        $data = [
            'id_seleksi_mitra' => $seleksimitra->id_seleksi_mitra,
            'id_mitra' => $seleksimitra->id_mitra,
            'kesimpulan_akhir' => $kesimpulanAkhir,
            // Set all individual fields based on availability and status
            'surat_permohonan' => $seleksimitra->surat_permohonan === 'ada' ? $kesimpulanAkhir : ($seleksimitra->surat_permohonan === 'tidak ada' ? 'Tidak Lolos' : null),
            'akta_notaris' => $seleksimitra->akta_notaris === 'ada' ? $kesimpulanAkhir : ($seleksimitra->akta_notaris === 'tidak ada' ? 'Tidak Lolos' : null),
            'nib' => $seleksimitra->nib === 'ada' ? $kesimpulanAkhir : ($seleksimitra->nib === 'tidak ada' ? 'Tidak Lolos' : null),
            'ktp' => $seleksimitra->ktp === 'ada' ? $kesimpulanAkhir : ($seleksimitra->ktp === 'tidak ada' ? 'Tidak Lolos' : null),
            'no_rekening' => $seleksimitra->no_rekening === 'ada' ? $kesimpulanAkhir : ($seleksimitra->no_rekening === 'tidak ada' ? 'Tidak Lolos' : null),
            'npwp' => $seleksimitra->npwp === 'ada' ? $kesimpulanAkhir : ($seleksimitra->npwp === 'tidak ada' ? 'Tidak Lolos' : null),
            'surat_kuasa' => $seleksimitra->surat_kuasa === 'ada' ? $kesimpulanAkhir : ($seleksimitra->surat_kuasa === 'tidak ada' ? 'Tidak Lolos' : null),
            'lantai_jemur' => $seleksimitra->lantai_jemur === 'ada' ? $kesimpulanAkhir : ($seleksimitra->lantai_jemur === 'tidak ada' ? 'Tidak Lolos' : null),
            'sarana_lainnya' => $seleksimitra->sarana_lainnya === 'ada' ? $kesimpulanAkhir : ($seleksimitra->sarana_lainnya === 'tidak ada' ? 'Tidak Lolos' : null),
            'mesin_memecah_kulit' => $seleksimitra->mesin_memecah_kulit === 'ada' ? $kesimpulanAkhir : ($seleksimitra->mesin_memecah_kulit === 'tidak ada' ? 'Tidak Lolos' : null),
            'mesin_pemisah_gabah_dengan_beras' => $seleksimitra->mesin_pemisah_gabah === 'ada' ? $kesimpulanAkhir : ($seleksimitra->mesin_pemisah_gabah === 'tidak ada' ? 'Tidak Lolos' : null),
            'mesin_penyosoh' => $seleksimitra->mesin_penyosoh === 'ada' ? $kesimpulanAkhir : ($seleksimitra->mesin_penyosoh === 'tidak ada' ? 'Tidak Lolos' : null),
            'alat_pemisah_beras' => $seleksimitra->alat_pemisah_beras === 'ada' ? $kesimpulanAkhir : ($seleksimitra->alat_pemisah_beras === 'tidak ada' ? 'Tidak Lolos' : null),
            'kesimpulan_dokumen' => $kesimpulanDokumen,
            'kesimpulan_sarana_pengeringan' => $kesimpulanPengeringan,
            'kesimpulan_sarana_penggilingan' => $kesimpulanPenggilingan,
            'dokumen_ada_valid' => $dokumenAdaValid,
            'dokumen_tidak_ada' => $dokumenTidakAda,
            'sarana_pengeringan_ada' => $pengeringanAda,
            'sarana_pengeringan_tidak_ada' => $pengeringanTidakAda,
            'sarana_penggilingan_ada' => $penggilinganAda,
            'sarana_penggilingan_tidak_ada' => $penggilinganTidakAda,
        ];
        
        if ($hasilSeleksi) {
            $hasilSeleksi->update($data);
        } else {
            \App\Models\HasilSeleksiMitra::create($data);
        }
    }

    public function destroy($id)
    {
        $seleksimitra = DataSeleksiMitra::findOrFail($id);
        $seleksimitra->delete();
        return response()->json(['message' => 'Data seleksi mitra berhasil dihapus']);
    }

}
