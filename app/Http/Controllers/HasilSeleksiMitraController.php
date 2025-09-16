<?php

namespace App\Http\Controllers;

use App\Models\HasilSeleksiMitra;
use Illuminate\Http\Request;

class HasilSeleksiMitraController extends Controller
{
    public function index()
    {
        $hasilSeleksiMitras = HasilSeleksiMitra::all();
        return response()->json($hasilSeleksiMitras);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mitra' => 'required|exists:data_mitra,id_mitra',
            'kesimpulan_akhir' => 'nullable|in:Lolos, Tidak Lolos',
            'surat_permohonan' => 'nullable|in:Lolos, Tidak Lolos',
            'akta_notaris' => 'nullable|in:Lolos, Tidak Lolos',
            'nib' => 'nullable|in:Lolos, Tidak Lolos',
            'ktp' => 'nullable|in:Lolos, Tidak Lolos',
            'no_rekening' => 'nullable|in:Lolos, Tidak Lolos',
            'npwp' => 'nullable|in:Lolos, Tidak Lolos',
            'surat_kuasa' => 'nullable|in:Lolos, Tidak Lolos',
            'lantai_jemur' => 'nullable|in:Lolos, Tidak Lolos',
            'sarana_lainnya' => 'nullable|in:Lolos, Tidak Lolos',
            'mesin_memecah_kulit' => 'nullable|in:Lolos, Tidak Lolos',
            'mesin_pemisah_gabah_dengan_beras' => 'nullable|in:Lolos, Tidak Lolos',
            'mesin_penyosoh' => 'nullable|in:Lolos, Tidak Lolos',
            'alat_pemisah_beras' => 'nullable|in:Lolos, Tidak Lolos',
            'kesimpulan_dokumen' => 'nullable|in:Lolos, Tidak Lolos',
            'dokumen_ada_valid' => 'nullable|array',
            'dokumen_tidak_ada' => 'nullable|array',
            'kesimpulan_sarana_pengeringan' => 'nullable|in:Lolos, Tidak Lolos',
            'sarana_pengeringan_ada' => 'nullable|array',
            'sarana_pengeringan_tidak_ada' => 'nullable|array',
            'kesimpulan_sarana_penggilingan' => 'nullable|in:Lolos, Tidak Lolos',
            'sarana_penggilingan_ada' => 'nullable|array',
            'sarana_penggilingan_tidak_ada' => 'nullable|array',
        ]);

        \Log::debug('Data Validated:', $validated);

        $hasilSeleksiMitra = HasilSeleksiMitra::create($validated);

        \Log::debug('Data Created:', $hasilSeleksiMitra->toArray());
        return response()->json($hasilSeleksiMitra, 201);
    }

    public function show($id)
    {
        $hasilSeleksiMitra = HasilSeleksiMitra::findOrFail($id);
        return response()->json($hasilSeleksiMitra);
    }

    public function update(Request $request, $id)
    {
        $hasilSeleksiMitra = HasilSeleksiMitra::findOrFail($id);

        $validated = $request->validate([
            'id_mitra' => 'sometimes|exists:data_mitra,id_mitra',
            'kesimpulan_akhir' => 'nullable|in:Lolos,Tidak Lolos',
            'surat_permohonan' => 'nullable|in:Lolos,Tidak Lolos',
            'akta_notaris' => 'nullable|in:Lolos,Tidak Lolos',
            'nib' => 'nullable|in:Lolos,Tidak Lolos',
            'ktp' => 'nullable|in:Lolos,Tidak Lolos',
            'no_rekening' => 'nullable|in:Lolos,Tidak Lolos',
            'npwp' => 'nullable|in:Lolos,Tidak Lolos',
            'surat_kuasa' => 'nullable|in:Lolos,Tidak Lolos',
            'lantai_jemur' => 'nullable|in:Lolos,Tidak Lolos',
            'sarana_lainnya' => 'nullable|in:Lolos,Tidak Lolos',
            'mesin_memecah_kulit' => 'nullable|in:Lolos,Tidak Lolos',
            'mesin_pemisah_gabah_dengan_beras' => 'nullable|in:Lolos,Tidak Lolos',
            'mesin_penyosoh' => 'nullable|in:Lolos,Tidak Lolos',
            'alat_pemisah_beras' => 'nullable|in:Lolos,Tidak Lolos',
            'kesimpulan_dokumen' => 'nullable|in:Lolos,Tidak Lolos',
            'dokumen_ada_valid' => 'nullable|array',
            'dokumen_tidak_ada' => 'nullable|array',
            'kesimpulan_sarana_pengeringan' => 'nullable|in:Lolos,Tidak Lolos',
            'sarana_pengeringan_ada' => 'nullable|array',
            'sarana_pengeringan_tidak_ada' => 'nullable|array',
            'kesimpulan_sarana_penggilingan' => 'nullable|in:Lolos,Tidak Lolos',
            'sarana_penggilingan_ada' => 'nullable|array',
            'sarana_penggilingan_tidak_ada' => 'nullable|array',
        ]);
        $hasilSeleksiMitra->update($validated);
        return response()->json($hasilSeleksiMitra);
    }

    public function destroy($id)
    {
        $hasilSeleksiMitra = HasilSeleksiMitra::findOrFail($id);
        $hasilSeleksiMitra->delete();
        return response()->json(['message' => 'Data hasil seleksi mitra berhasil dihapus']);
    }
}
