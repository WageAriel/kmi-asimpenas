<?php

namespace App\Http\Controllers;

use App\Models\DataSeleksiMitra;
use Illuminate\Http\Request;

class DataSeleksiMitraController extends Controller
{
    public function index()
    {
        $seleksimitras = DataSeleksiMitra::all();
        return response()->json($seleksimitras);
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
            'mb_ktp' => 'nullable|date',
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
            'mb_ktp' => 'nullable|date',
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

        $seleksimitra->update($validated);
        return response()->json($seleksimitra);
    }

    public function destroy($id)
    {
        $seleksimitra = DataSeleksiMitra::findOrFail($id);
        $seleksimitra->delete();
        return response()->json(['message' => 'Data seleksi mitra berhasil dihapus']);
    }

}
