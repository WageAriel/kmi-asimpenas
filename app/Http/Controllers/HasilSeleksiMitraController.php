<?php

namespace App\Http\Controllers;

use App\Models\HasilSeleksiMitra;
use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
use App\Exports\HasilSeleksiMitraExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class HasilSeleksiMitraController extends Controller
{
    public function index()
    {
        $hasilSeleksiMitras = HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])->get();
        return response()->json($hasilSeleksiMitras);
    }

    public function myHasilSeleksi()
    {
        $userId = auth()->id();
        $mitra = DataMitra::where('user_id', $userId)->first();

        if (!$mitra) {
            return response()->json(['message' => 'Data mitra tidak ditemukan'], 404);
        }

        $hasilSeleksi = HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])
            ->where('id_mitra', $mitra->id_mitra)
            ->latest()
            ->get();

        return response()->json($hasilSeleksi);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_seleksi_mitra' => 'required|exists:data_seleksi_mitra,id_seleksi_mitra',
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
            'kesimpulan_dokumen' => 'required|in:Lolos,Tidak Lolos',
            'kesimpulan_sarana_pengeringan' => 'required|in:Lolos,Tidak Lolos',
            'kesimpulan_sarana_penggilingan' => 'required|in:Lolos,Tidak Lolos',
            'kesimpulan_akhir' => 'required|in:Lolos,Tidak Lolos',
        ]);

        DB::beginTransaction();
        
        try {
            // Ambil data seleksi mitra
            $seleksiMitra = DataSeleksiMitra::findOrFail($validated['id_seleksi_mitra']);
            
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
            $dokumenAdaTidakValid = []; // dokumen yang ADA tetapi dinilai 'Tidak Lolos' oleh admin
            $dokumenTidakAda = [];
            $dokumenLolos = 0;
            $dokumenTidakLolos = 0;

            foreach ($dokumenMapping as $field => $label) {
                // Cek apakah dokumen ada di data_seleksi_mitra
                if ($seleksiMitra->$field === 'Ada') {
                    // Ambil nilai validasi dari input admin (jika ada)
                    $adminValue = $validated[$field] ?? null;

                    if ($adminValue === 'Lolos') {
                        $dokumenAdaValid[] = $label;
                        $dokumenLolos++;
                    } elseif ($adminValue === 'Tidak Lolos') {
                        // Dokumen ADA tetapi admin menilai TIDAK LOLOS -> masuk ke dokumen_ada_tidak_valid
                        $dokumenAdaTidakValid[] = $label;
                        $dokumenTidakLolos++;
                    } else {
                        // Jika admin belum memberikan validasi untuk dokumen yang ada, kita lewati
                    }
                } else {
                    // Jika dokumen tidak ada
                    $dokumenTidakAda[] = $label;
                }
            }

            // Ambil kesimpulan dokumen dari input admin
            $kesimpulanDokumen = $validated['kesimpulan_dokumen'];

            // Proses sarana pengeringan
            $pengeringanMapping = [
                'lantai_jemur' => 'Lantai Jemur',
                'sarana_lainnya' => 'Sarana Lainnya',
            ];

            $pengeringanAda = [];
            $pengeringanTidakAda = [];
            $pengeringanLolos = 0;
            $pengeringanTidakLolos = 0;

            foreach ($pengeringanMapping as $field => $label) {
                if ($seleksiMitra->$field === 'Ada') {
                    if (isset($validated[$field])) {
                        if ($validated[$field] === 'Lolos') {
                            $pengeringanAda[] = $label;
                            $pengeringanLolos++;
                        } else {
                            $pengeringanTidakAda[] = $label;
                            $pengeringanTidakLolos++;
                        }
                    }
                } else {
                    // Jika sarana tidak ada
                    $pengeringanTidakAda[] = $label;
                }
            }

            // Ambil kesimpulan pengeringan dari input admin
            $kesimpulanPengeringan = $validated['kesimpulan_sarana_pengeringan'];

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
            $penggilinganTidakLolos = 0;

            foreach ($penggilinganMapping as $field => $label) {
                // Untuk mesin_pemisah_gabah_dengan_beras, cek field mesin_pemisah_gabah
                $seleksiField = ($field === 'mesin_pemisah_gabah_dengan_beras') ? 'mesin_pemisah_gabah' : $field;
                
                if ($seleksiMitra->$seleksiField === 'Ada') {
                    if (isset($validated[$field])) {
                        if ($validated[$field] === 'Lolos') {
                            $penggilinganAda[] = $label;
                            $penggilinganLolos++;
                        } else {
                            $penggilinganTidakAda[] = $label;
                            $penggilinganTidakLolos++;
                        }
                    }
                } else {
                    // Jika sarana tidak ada
                    $penggilinganTidakAda[] = $label;
                }
            }

            // Ambil kesimpulan penggilingan dari input admin
            $kesimpulanPenggilingan = $validated['kesimpulan_sarana_penggilingan'];

            // Ambil kesimpulan akhir dari input admin
            $kesimpulanAkhir = $validated['kesimpulan_akhir'];

            // Simpan hasil seleksi
            $hasilSeleksi = HasilSeleksiMitra::create([
                'id_seleksi_mitra' => $validated['id_seleksi_mitra'],
                'id_mitra' => $seleksiMitra->id_mitra,
                'surat_permohonan' => $validated['surat_permohonan'] ?? null,
                'akta_notaris' => $validated['akta_notaris'] ?? null,
                'nib' => $validated['nib'] ?? null,
                'ktp' => $validated['ktp'] ?? null,
                'no_rekening' => $validated['no_rekening'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'surat_kuasa' => $validated['surat_kuasa'] ?? null,
                'lantai_jemur' => $validated['lantai_jemur'] ?? null,
                'sarana_lainnya' => $validated['sarana_lainnya'] ?? null,
                'mesin_memecah_kulit' => $validated['mesin_memecah_kulit'] ?? null,
                'mesin_pemisah_gabah_dengan_beras' => $validated['mesin_pemisah_gabah_dengan_beras'] ?? null,
                'mesin_penyosoh' => $validated['mesin_penyosoh'] ?? null,
                'alat_pemisah_beras' => $validated['alat_pemisah_beras'] ?? null,
                'kesimpulan_dokumen' => $kesimpulanDokumen,
                'dokumen_ada_valid' => $dokumenAdaValid,
                'dokumen_ada_tidak_valid' => $dokumenAdaTidakValid,
                'dokumen_tidak_ada' => $dokumenTidakAda,
                'kesimpulan_sarana_pengeringan' => $kesimpulanPengeringan,
                'sarana_pengeringan_ada' => $pengeringanAda,
                'sarana_pengeringan_tidak_ada' => $pengeringanTidakAda,
                'kesimpulan_sarana_penggilingan' => $kesimpulanPenggilingan,
                'sarana_penggilingan_ada' => $penggilinganAda,
                'sarana_penggilingan_tidak_ada' => $penggilinganTidakAda,
                'kesimpulan_akhir' => $kesimpulanAkhir,
            ]);

            DB::commit();

            Log::info('Hasil Seleksi Created:', $hasilSeleksi->toArray());
            
            return response()->json([
                'message' => 'Hasil seleksi berhasil disimpan',
                'data' => $hasilSeleksi->load(['mitra', 'seleksiMitra'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating hasil seleksi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menyimpan hasil seleksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $hasilSeleksiMitra = HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])->findOrFail($id);
        return response()->json($hasilSeleksiMitra);
    }

    public function getBySeleksiMitra($id_seleksi_mitra)
    {
        $hasilSeleksi = HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])
            ->where('id_seleksi_mitra', $id_seleksi_mitra)
            ->first();
        
        if (!$hasilSeleksi) {
            return response()->json(['message' => 'Hasil seleksi tidak ditemukan'], 404);
        }

        return response()->json($hasilSeleksi);
    }

    public function update(Request $request, $id)
    {
        $hasilSeleksiMitra = HasilSeleksiMitra::findOrFail($id);

        $validated = $request->validate([
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
            'kesimpulan_dokumen' => 'required|in:Lolos,Tidak Lolos',
            'kesimpulan_sarana_pengeringan' => 'required|in:Lolos,Tidak Lolos',
            'kesimpulan_sarana_penggilingan' => 'required|in:Lolos,Tidak Lolos',
            'kesimpulan_akhir' => 'required|in:Lolos,Tidak Lolos',
        ]);

        DB::beginTransaction();
        
        try {
            $seleksiMitra = $hasilSeleksiMitra->seleksiMitra;
            
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
            $dokumenAdaTidakValid = []; // dokumen yang ADA tetapi dinilai 'Tidak Lolos' oleh admin
            $dokumenTidakAda = [];
            $dokumenLolos = 0;
            $dokumenTidakLolos = 0;

            foreach ($dokumenMapping as $field => $label) {
                if ($seleksiMitra->$field === 'Ada') {
                    // Ambil nilai validasi dari input admin (jika ada)
                    $adminValue = $validated[$field] ?? null;

                    if ($adminValue === 'Lolos') {
                        $dokumenAdaValid[] = $label;
                        $dokumenLolos++;
                    } elseif ($adminValue === 'Tidak Lolos') {
                        $dokumenAdaTidakValid[] = $label;
                        $dokumenTidakLolos++;
                    } else {
                        // admin belum memberikan validasi untuk dokumen yang ada
                    }
                } else {
                    // Jika dokumen tidak ada
                    $dokumenTidakAda[] = $label;
                }
            }

            // Ambil kesimpulan dokumen dari input admin
            $kesimpulanDokumen = $validated['kesimpulan_dokumen'];

            // Proses sarana pengeringan
            $pengeringanMapping = [
                'lantai_jemur' => 'Lantai Jemur',
                'sarana_lainnya' => 'Sarana Lainnya',
            ];

            $pengeringanAda = [];
            $pengeringanTidakAda = [];
            $pengeringanLolos = 0;
            $pengeringanTidakLolos = 0;

            foreach ($pengeringanMapping as $field => $label) {
                if ($seleksiMitra->$field === 'Ada') {
                    if (isset($validated[$field])) {
                        if ($validated[$field] === 'Lolos') {
                            $pengeringanAda[] = $label;
                            $pengeringanLolos++;
                        } else {
                            $pengeringanTidakAda[] = $label;
                            $pengeringanTidakLolos++;
                        }
                    }
                } else {
                    // Jika sarana tidak ada
                    $pengeringanTidakAda[] = $label;
                }
            }

            // Ambil kesimpulan pengeringan dari input admin
            $kesimpulanPengeringan = $validated['kesimpulan_sarana_pengeringan'];

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
            $penggilinganTidakLolos = 0;

            foreach ($penggilinganMapping as $field => $label) {
                $seleksiField = ($field === 'mesin_pemisah_gabah_dengan_beras') ? 'mesin_pemisah_gabah' : $field;
                
                if ($seleksiMitra->$seleksiField === 'Ada') {
                    if (isset($validated[$field])) {
                        if ($validated[$field] === 'Lolos') {
                            $penggilinganAda[] = $label;
                            $penggilinganLolos++;
                        } else {
                            $penggilinganTidakAda[] = $label;
                            $penggilinganTidakLolos++;
                        }
                    }
                } else {
                    // Jika sarana tidak ada
                    $penggilinganTidakAda[] = $label;
                }
            }

            // Ambil kesimpulan penggilingan dari input admin
            $kesimpulanPenggilingan = $validated['kesimpulan_sarana_penggilingan'];

            // Ambil kesimpulan akhir dari input admin
            $kesimpulanAkhir = $validated['kesimpulan_akhir'];

            // Update hasil seleksi
            $hasilSeleksiMitra->update([
                'surat_permohonan' => $validated['surat_permohonan'] ?? null,
                'akta_notaris' => $validated['akta_notaris'] ?? null,
                'nib' => $validated['nib'] ?? null,
                'ktp' => $validated['ktp'] ?? null,
                'no_rekening' => $validated['no_rekening'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
                'surat_kuasa' => $validated['surat_kuasa'] ?? null,
                'lantai_jemur' => $validated['lantai_jemur'] ?? null,
                'sarana_lainnya' => $validated['sarana_lainnya'] ?? null,
                'mesin_memecah_kulit' => $validated['mesin_memecah_kulit'] ?? null,
                'mesin_pemisah_gabah_dengan_beras' => $validated['mesin_pemisah_gabah_dengan_beras'] ?? null,
                'mesin_penyosoh' => $validated['mesin_penyosoh'] ?? null,
                'alat_pemisah_beras' => $validated['alat_pemisah_beras'] ?? null,
                'kesimpulan_dokumen' => $kesimpulanDokumen,
                'dokumen_ada_valid' => $dokumenAdaValid,
                'dokumen_ada_tidak_valid' => $dokumenAdaTidakValid,
                'dokumen_tidak_ada' => $dokumenTidakAda,
                'kesimpulan_sarana_pengeringan' => $kesimpulanPengeringan,
                'sarana_pengeringan_ada' => $pengeringanAda,
                'sarana_pengeringan_tidak_ada' => $pengeringanTidakAda,
                'kesimpulan_sarana_penggilingan' => $kesimpulanPenggilingan,
                'sarana_penggilingan_ada' => $penggilinganAda,
                'sarana_penggilingan_tidak_ada' => $penggilinganTidakAda,
                'kesimpulan_akhir' => $kesimpulanAkhir,
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Hasil seleksi berhasil diupdate',
                'data' => $hasilSeleksiMitra->load(['mitra', 'seleksiMitra'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal mengupdate hasil seleksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $hasilSeleksiMitra = HasilSeleksiMitra::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Reset status seleksi mitra ke pending
            if ($hasilSeleksiMitra->seleksiMitra) {
                $hasilSeleksiMitra->seleksiMitra->update(['status_seleksi' => 'pending']);
            }
            
            $hasilSeleksiMitra->delete();
            
            DB::commit();
            
            return response()->json(['message' => 'Data hasil seleksi mitra berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data hasil seleksi mitra to Excel
     */
    public function export()
    {
        return Excel::download(new HasilSeleksiMitraExport(), 'hasil-seleksi-mitra-' . date('Y-m-d') . '.xlsx');
    }
}
