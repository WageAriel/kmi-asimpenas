<?php

namespace App\Http\Controllers;

use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
use Illuminate\Http\Request;

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
        
        return response()->json($seleksimitra);
    }
    
    /**
     * Create or update hasil seleksi based on admin decision
     */
    private function createOrUpdateHasilSeleksi($seleksimitra, $status)
    {
        $hasilSeleksi = \App\Models\HasilSeleksiMitra::where('id_seleksi_mitra', $seleksimitra->id_seleksi_mitra)->first();
        
        $kesimpulanAkhir = ($status === 'lolos') ? 'Lolos' : 'Tidak Lolos';
        
        $data = [
            'id_seleksi_mitra' => $seleksimitra->id_seleksi_mitra,
            'id_mitra' => $seleksimitra->id_mitra,
            'kesimpulan_akhir' => $kesimpulanAkhir,
            // Set all individual fields based on status
            'surat_permohonan' => $seleksimitra->surat_permohonan === 'ada' ? $kesimpulanAkhir : null,
            'akta_notaris' => $seleksimitra->akta_notaris === 'ada' ? $kesimpulanAkhir : null,
            'nib' => $seleksimitra->nib === 'ada' ? $kesimpulanAkhir : null,
            'ktp' => $seleksimitra->ktp === 'ada' ? $kesimpulanAkhir : null,
            'no_rekening' => $seleksimitra->no_rekening === 'ada' ? $kesimpulanAkhir : null,
            'npwp' => $seleksimitra->npwp === 'ada' ? $kesimpulanAkhir : null,
            'surat_kuasa' => $seleksimitra->surat_kuasa === 'ada' ? $kesimpulanAkhir : null,
            'lantai_jemur' => $seleksimitra->lantai_jemur === 'ada' ? $kesimpulanAkhir : null,
            'sarana_lainnya' => $seleksimitra->sarana_lainnya === 'ada' ? $kesimpulanAkhir : null,
            'mesin_memecah_kulit' => $seleksimitra->mesin_memecah_kulit === 'ada' ? $kesimpulanAkhir : null,
            'mesin_pemisah_gabah_dengan_beras' => $seleksimitra->mesin_pemisah_gabah === 'ada' ? $kesimpulanAkhir : null,
            'mesin_penyosoh' => $seleksimitra->mesin_penyosoh === 'ada' ? $kesimpulanAkhir : null,
            'alat_pemisah_beras' => $seleksimitra->alat_pemisah_beras === 'ada' ? $kesimpulanAkhir : null,
            'kesimpulan_dokumen' => $kesimpulanAkhir,
            'kesimpulan_sarana_pengeringan' => $kesimpulanAkhir,
            'kesimpulan_sarana_penggilingan' => $kesimpulanAkhir,
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
