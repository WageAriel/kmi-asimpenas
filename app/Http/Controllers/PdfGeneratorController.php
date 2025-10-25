<?php
namespace App\Http\Controllers;

use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
use App\Models\KlasifikasiMitra;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfGeneratorController extends Controller
{
    public function downloadSeleksiMitraPdf($id)
    {
        try {
            // Log untuk debugging
            Log::info("Generating PDF for submission ID: " . $id);
            
            // Get submission data
            $seleksi = DataSeleksiMitra::with('mitra')->findOrFail($id);
            $mitra = $seleksi->mitra;
            
            // Get year from seleksi
            $tahun = $seleksi->tahun ?? $seleksi->year ?? date('Y');
            
            // Calculate submission number for this mitra (seleksi ke berapa)
            $seleksiKe = DataSeleksiMitra::where('id_mitra', $mitra->id_mitra)
                ->where('id_seleksi_mitra', '<=', $id)
                ->orderBy('created_at', 'asc')
                ->count();
            
            // Format nomor urut seleksi (3 digit)
            $nomorUrutSeleksi = sprintf("%03d", $id);
            
            // Fixed values as requested
            $nomorEntitasBulog = '11030';
            $unitPelaksana = 'KANTOR CABANG SURAKARTA';
            
            // Format nomor lengkap: No urut seleksi/No Entitas Bulog/A/SELEKSI/seleksi ke berapa/tahun
            $nomorLengkap = sprintf("%s/%s/A/SELEKSI/%d/%s", 
                $nomorUrutSeleksi,
                $nomorEntitasBulog,
                $seleksiKe,
                $tahun
            );
            
            // Format data for PDF
            $data = [
                'seleksi' => $seleksi,
                'mitra' => $mitra,
                'tahun' => $tahun,
                'nomor' => $nomorLengkap,
                'tanggal_seleksi' => date('d-m-Y'),
                'nomor_urut_seleksi' => $nomorUrutSeleksi,
                'nomor_entitas_bulog' => $nomorEntitasBulog,
                'unit_pelaksana' => $unitPelaksana,
                'seleksi_ke' => $seleksiKe,
            ];

            // Load the PDF view
            $pdf = Pdf::loadView('pdf.seleksi-mitra-form', $data);
            
            // Set paper to portrait A4
            $pdf->setPaper('a4', 'portrait');
            
            // Download the PDF
            return $pdf->download('FORM-A_Seleksi_Mitra_' . $mitra->nama_perusahaan . '.pdf');
        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function downloadKlasifikasiMitraPdf($id)
    {
        try {
            Log::info("Generating Klasifikasi PDF for ID: " . $id);
            
            $klasifikasi = KlasifikasiMitra::with('mitra')->findOrFail($id);
            $mitra = $klasifikasi->mitra;
            
            $tahun = $klasifikasi->tahun ?? date('Y');
            
            $klasifikasiKe = KlasifikasiMitra::where('id_mitra', $mitra->id_mitra)
                ->where('id_klasifikasi_mitra', '<=', $id)
                ->orderBy('created_at', 'asc')
                ->count();
            
            $nomorUrutKlasifikasi = sprintf("%03d", $id);
            $nomorEntitasBulog = '11030';
            $unitPelaksana = 'KANTOR CABANG SURAKARTA';
            
            // Format nomor lengkap: No urut klasifikasi/No Entitas Bulog/B/KLASIFIKASI/klasifikasi ke berapa/tahun
            $nomorLengkap = sprintf("%s/%s/B/KLASIFIKASI/%d/%s", 
                $nomorUrutKlasifikasi,
                $nomorEntitasBulog,
                $klasifikasiKe,
                $tahun
            );
            
            $data = [
                'klasifikasi' => $klasifikasi,
                'mitra' => $mitra,
                'nomor' => $nomorLengkap,
                'tanggal' => now()->format('d/m/Y'),
                'nomor_entitas' => $nomorEntitasBulog,
                'unit_pelaksana' => $unitPelaksana,
                'klasifikasi_ke' => $klasifikasiKe,
                'komponen_pengeringan' => [
                    ['name' => 'Mesin Pembersih Gabah', 'unit' => 'ton/hari', 'field' => 'mesin_pembersih_gabah'],
                    ['name' => 'Lantai Jemur', 'unit' => 'ton/hari', 'field' => 'lantai_jemur'],
                    ['name' => 'Mesin Pengering', 'unit' => 'unit', 'field' => 'mesin_pengering'],
                    ['name' => 'Alat Pengering Lainnya', 'unit' => 'unit', 'field' => 'alat_pengering_lainnya'],
                ],
                'komponen_penggilingan' => [
                    ['name' => 'Mesin Pemecah Kulit', 'unit' => 'ton/hari', 'field' => 'mesin_pemecah_kulit'],
                    ['name' => 'Mesin Pemisah Gabah', 'unit' => 'ton/hari', 'field' => 'mesin_pemisah_gabah'],
                    ['name' => 'Mesin Penyosoh', 'unit' => 'unit', 'field' => 'mesin_penyosoh'],
                    ['name' => 'Alat Pemisah Beras', 'unit' => 'unit', 'field' => 'alat_pemisah_beras'],
                ],
                'komponen_pendukung' => [
                    ['name' => 'Gudang', 'unit' => 'm²', 'field' => 'gudang'],
                    ['name' => 'Timbangan', 'unit' => 'unit', 'field' => 'timbangan'],
                    ['name' => 'Alat Pengukur Kadar Air', 'unit' => 'unit', 'field' => 'alat_pengukur_kadar_air'],
                ]
            ];

            $pdf = PDF::loadView('pdf.klasifikasi-mitra-form', $data);
            $pdf->setPaper('a3', 'portrait');
            $pdf->getDomPDF()->set_option("default_font", "Times New Roman");
            
            return $pdf->download('FORM-B_Klasifikasi_Mitra_' . $mitra->nama_perusahaan . '.pdf');
            
        } catch (\Exception $e) {
            Log::error("Klasifikasi PDF Generation Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}