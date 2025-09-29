<?php

namespace App\Http\Controllers;

use App\Models\KlasifikasiMitra;
use App\Models\DataMitra;
use Illuminate\Http\Request;

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
            'mesin_pembersih_gabah' => 'nullable|in:1,2,3',
            'lantai_jemur' => 'nullable|in:1,2,3',
            'mesin_pengering' => 'nullable|in:1,2,3',
            'alat_pengering_lainnya' => 'nullable|in:1,2,3',
            'mesin_pembersih_awal' => 'nullable|in:1,2,3',
            'mesin_pemecah_kulit' => 'nullable|in:1,2,3',
            'mesin_pembersih_sekam' => 'nullable|in:1,2,3',
            'mesin_pemisah_gabah_pecah_kulit' => 'nullable|in:1,2,3',
            'mesin_pemisah_batu' => 'nullable|in:1,2,3',
            'mesin_penyosoh' => 'nullable|in:1,2,3',
            'mesin_pengkabut' => 'nullable|in:1,2,3',
            'mesin_pemesah_menir' => 'nullable|in:1,2,3',
            'mesin_pemisah_katul' => 'nullable|in:1,2,3',
            'mesin_pemisah_berdasarkan_ukuran' => 'nullable|in:1,2,3',
            'mesin_pemisah_berdasarkan_warna' => 'nullable|in:1,2,3',
            'tangki_penyimpanan' => 'nullable|in:1,2,3',
            'mesin_pengemas' => 'nullable|in:1,2,3',
            'mesin_jahit' => 'nullable|in:1,2,3',
            'gudang_konvensional' => 'nullable|in:1,2,3',
            'silo_gkg_hopper' => 'nullable|in:1,2,3',
            'truk' => 'nullable|in:1,2,3',
            'mini_lab' => 'nullable|in:1,2,3',
            'moisture_tester' => 'nullable|in:1,2,3',
            'pembanding_derajat_sosoh' => 'nullable|in:1,2,3',
            'bagian_quality_control' => 'nullable|in:1,2,3',
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
            'mesin_pembersih_gabah' => 'nullable|in:1,2,3',
            'lantai_jemur' => 'nullable|in:1,2,3',
            'mesin_pengering' => 'nullable|in:1,2,3',
            'alat_pengering_lainnya' => 'nullable|in:1,2,3',
            'mesin_pembersih_awal' => 'nullable|in:1,2,3',
            'mesin_pemecah_kulit' => 'nullable|in:1,2,3',
            'mesin_pembersih_sekam' => 'nullable|in:1,2,3',
            'mesin_pemisah_gabah_pecah_kulit' => 'nullable|in:1,2,3',
            'mesin_pemisah_batu' => 'nullable|in:1,2,3',
            'mesin_penyosoh' => 'nullable|in:1,2,3',
            'mesin_pengkabut' => 'nullable|in:1,2,3',
            'mesin_pemesah_menir' => 'nullable|in:1,2,3',
            'mesin_pemisah_katul' => 'nullable|in:1,2,3',
            'mesin_pemisah_berdasarkan_ukuran' => 'nullable|in:1,2,3',
            'mesin_pemisah_berdasarkan_warna' => 'nullable|in:1,2,3',
            'tangki_penyimpanan' => 'nullable|in:1,2,3',
            'mesin_pengemas' => 'nullable|in:1,2,3',
            'mesin_jahit' => 'nullable|in:1,2,3',
            'gudang_konvensional' => 'nullable|in:1,2,3',
            'silo_gkg_hopper' => 'nullable|in:1,2,3',
            'truk' => 'nullable|in:1,2,3',
            'mini_lab' => 'nullable|in:1,2,3',
            'moisture_tester' => 'nullable|in:1,2,3',
            'pembanding_derajat_sosoh' => 'nullable|in:1,2,3',
            'bagian_quality_control' => 'nullable|in:1,2,3',
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
}
