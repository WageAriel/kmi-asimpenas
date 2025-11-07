<?php
namespace App\Http\Controllers;

use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
use App\Models\KlasifikasiMitra;
use App\Models\Karyawan;
use App\Models\HasilSeleksiMitra;
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

    private function terbilang($angka) {
        $angka = abs($angka);
        $baca = array(
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh'
        );
        
        return $baca[$angka];
    }

    private function bulanIndo($bulan) {
        $arrBulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        
        return $arrBulan[$bulan];
    }

    public function generateSuratPenetapan($id, Request $request) {
        try {
            $seleksi = DataSeleksiMitra::with('mitra')->findOrFail($id);
            $karyawan = Karyawan::findOrFail($request->id_karyawan);
            
            $today = now();
            
            $data = [
                'seleksi' => $seleksi,
                'mitra' => $seleksi->mitra,
                'karyawan' => $karyawan,
                'hari' => \Carbon\Carbon::now()->locale('id')->isoFormat('dddd'),
                'tanggal' => sprintf(
                    "%s bulan %s tahun %s (%s)",
                    $this->terbilang($today->day),
                    $this->bulanIndo($today->month),
                    'Dua Ribu Dua Puluh Lima',
                    $today->format('d-m-Y')
                ),
                'nomor_surat' => sprintf(
                    "%d/11030/SP/MITRAPANGAN/%s/%s",
                    $id,
                    $this->getRomanMonth($today->month),
                    $today->year
                ),
                'nomor_BA' => sprintf(
                    "%d/11030/BA/SELEKSI/%s/%s",
                    $id,
                    $this->getRomanMonth($today->month),
                    $today->year
                ),
            ];
            
            $pdf = PDF::loadView('pdf.surat-penetapan-mitra', $data);
            return $pdf->download('surat-penetapan-'.$seleksi->mitra->nama_perusahaan.'.pdf');
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    private function getRomanMonth($month) 
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        
        return $romans[$month];
    }

    public function generateBeritaAcara($id, Request $request)
    {
        try {
            $hasilSeleksi = HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])->findOrFail($id);
            $pelaksana = Karyawan::findOrFail($request->id_pelaksana);
            $pengetahui = Karyawan::findOrFail($request->id_pengetahui);
            
            $today = now();
            
            $data = [
                'nomor_surat' => sprintf(
                    "%d/11030/BA/SELEKSI/%s/%s",
                    $id,
                    $this->getRomanMonth($today->month),
                    $today->year
                ),
                'tahun' => $today->year,
                'hari' => $today->locale('id')->isoFormat('dddd'),
                'tanggal' => sprintf(
                    "%s bulan %s tahun %s (%s)",
                    $this->terbilang($today->day),
                    $this->bulanIndo($today->month),
                    'Dua Ribu Dua Puluh Lima',
                    $today->format('d-m-Y')
                ),
                'nomor_urut_seleksi' => sprintf("%03d", $hasilSeleksi->seleksiMitra->id_seleksi_mitra),
                'tanggal_seleksi' => $hasilSeleksi->created_at->isoFormat('D MMMM Y'),
                'nomor_entitas_bulog' => '11030',
                'unit_pelaksana' => 'SURAKARTA',
                'nama_perusahaan' => $hasilSeleksi->mitra->nama_perusahaan,
                'badan_usaha' => $hasilSeleksi->mitra->badan_hukum_usaha,
                'alamat' => $hasilSeleksi->mitra->alamat_perusahaan,
                'status_mitra' => $hasilSeleksi->mitra->status ?? 'Penggilingan',
                'hasil_seleksi' => $hasilSeleksi,
                'hasil_akhir' => strtoupper($hasilSeleksi->kesimpulan_akhir),
                'nama_pj_mitra' => $hasilSeleksi->mitra->nama_cp,
                'nama_pj_bulog' => $pengetahui->nama_karyawan,
                'pelaksana' => $pelaksana,
                'pengetahui' => $pengetahui
            ];

            $pdf = PDF::loadView('pdf.berita-acara-hasil-seleksi', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = sprintf(
                'BA-SELEKSI-%s-%s.pdf',
                str_replace(' ', '-', $hasilSeleksi->mitra->nama_perusahaan),
                $today->format('dmY')
            );

            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}