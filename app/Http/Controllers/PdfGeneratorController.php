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
use Carbon\Carbon;

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
            
            // Calculate sequential order number (urutan data keberapa di database)
            $urutanData = DataSeleksiMitra::where('id_seleksi_mitra', '<=', $id)
                ->orderBy('id_seleksi_mitra', 'asc')
                ->count();
            
            // Format nomor urut seleksi (2 digit)
            $nomorUrutSeleksi = sprintf("%02d", $urutanData);
            
            // Fixed values as requested
            $nomorEntitasBulog = '11030';
            $unitPelaksana = 'KANTOR CABANG SURAKARTA';
            
            // Get current month in Roman numeral
            $bulanRomawi = $this->getRomanMonth(date('n'));
            
            // Format nomor lengkap: No urut seleksi/No Entitas Bulog/A/SELEKSI/bulan romawi/tahun
            $nomorLengkap = sprintf("%s/%s/A/SELEKSI/%s/%s", 
                $nomorUrutSeleksi,
                $nomorEntitasBulog,
                $bulanRomawi,
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
            
            // Ambil tahun dari created_at klasifikasi
            $tahun = $klasifikasi->created_at->year;
            
            // Hitung klasifikasi keberapa untuk mitra ini
            $klasifikasiKe = KlasifikasiMitra::where('id_mitra', $mitra->id_mitra)
                ->where('id_klasifikasi_mitra', '<=', $id)
                ->orderBy('created_at', 'asc')
                ->count();
            
            // Calculate sequential order number (urutan data keberapa di database)
            $urutanDataKlasifikasi = KlasifikasiMitra::where('id_klasifikasi_mitra', '<=', $id)
                ->orderBy('id_klasifikasi_mitra', 'asc')
                ->count();
            
            // Konversi klasifikasi keberapa ke romawi
            $klasifikasiKeRomawi = $this->toRoman($klasifikasiKe);
            
            $nomorUrutKlasifikasi = sprintf("%02d", $urutanDataKlasifikasi);
            $nomorEntitasBulog = '11030';
            $unitPelaksana = 'KANTOR CABANG SURAKARTA';

            $bulanRomawi = $this->getRomanMonth(date('n'));
            
            // Format nomor lengkap: No urut klasifikasi/No Entitas Bulog/B/KLASIFIKASI/klasifikasi ke berapa romawi/tahun
            $nomorLengkap = sprintf("%s/%s/B/KLASIFIKASI/%s/%s", 
                $nomorUrutKlasifikasi,
                $nomorEntitasBulog,
                $bulanRomawi,
                $tahun
            );
            
            $data = [
                'klasifikasi' => $klasifikasi,
                'mitra' => $mitra,
                'nomor' => $nomorLengkap,
                'tahun' => $tahun,
                'tanggal' => now()->format('d/m/Y'),
                'nomor_entitas' => $nomorEntitasBulog,
                'unit_pelaksana' => $unitPelaksana,
                'klasifikasi_ke' => $klasifikasiKe,
                'nomor_urut_klasifikasi' => $nomorUrutKlasifikasi,
                'getKlasifikasiHasil' => function($field, $value) {
                    return $this->getKlasifikasiHasil($field, $value);
                },
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
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
        );
        
        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return $baca[$angka - 10] . ' Belas';
        } elseif ($angka < 100) {
            return $baca[intval($angka / 10)] . ' Puluh' . ($angka % 10 != 0 ? ' ' . $baca[$angka % 10] : '');
        } else {
            return $angka; // Fallback untuk angka >= 100
        }
    }

    private function bulanIndo($bulan) {
        $arrBulan = array(
            1 => 'Januari', 
            2 => 'Februari', 
            3 => 'Maret', 
            4 => 'April', 
            5 => 'Mei', 
            6 => 'Juni',
            7 => 'Juli', 
            8 => 'Agustus', 
            9 => 'September', 
            10 => 'Oktober', 
            11 => 'November', 
            12 => 'Desember'
        );
        
        return $arrBulan[$bulan];
    }

    public function generateSuratPenetapan($id, Request $request) {
        try {
            $seleksi = DataSeleksiMitra::with('mitra')->findOrFail($id);
            $karyawan = Karyawan::findOrFail($request->id_karyawan);
            
            $today = now();
            
            // Calculate sequential order number (urutan data seleksi keberapa di database)
            $urutanDataSeleksi = DataSeleksiMitra::where('id_seleksi_mitra', '<=', $id)
                ->orderBy('id_seleksi_mitra', 'asc')
                ->count();
            
            // Hitung seleksi keberapa untuk mitra ini
            $seleksiKe = DataSeleksiMitra::where('id_mitra', $seleksi->id_mitra)
                ->where('id_seleksi_mitra', '<=', $id)
                ->orderBy('id_seleksi_mitra', 'asc')
                ->count();
            
            // Konversi seleksi ke berapa ke angka romawi
            $seleksiKeRomawi = $this->toRoman($seleksiKe);
            
            // Ambil tahun dari seleksi
            $tahunSeleksi = $seleksi->created_at->year;

            $bulanRomawi = $this->getRomanMonth(date('n'));
            
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
                    "%02d/11030/SP/MITRAPANGAN/%s/%s",
                    $urutanDataSeleksi,
                    $bulanRomawi,
                    $tahunSeleksi
                ),
                'nomor_BA' => sprintf(
                    "%02d/11030/BA/SELEKSI/%s/%s",
                    $urutanDataSeleksi,
                    $bulanRomawi,
                    $tahunSeleksi
                ),
                'nomor_urut_seleksi' => sprintf("%02d", $urutanDataSeleksi),
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

    private function toRoman($num) 
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
        ];
        
        $result = '';
        foreach ($map as $roman => $value) {
            $matches = intval($num / $value);
            $result .= str_repeat($roman, $matches);
            $num = $num % $value;
        }
        
        return $result;
    }

    public function generateBeritaAcara($id, Request $request)
    {
        try {
            $hasilSeleksi = HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])->findOrFail($id);
            $pelaksana = Karyawan::findOrFail($request->id_pelaksana);
            $pengetahui = Karyawan::findOrFail($request->id_pengetahui);
            
            $today = now();
            
            // Hitung urutan hasil seleksi untuk mitra ini (hasil seleksi keberapa dari mitra tersebut)
            $urutanHasilSeleksi = HasilSeleksiMitra::where('id_mitra', $hasilSeleksi->id_mitra)
                ->where('id_hasil_seleksi_mitra', '<=', $hasilSeleksi->id_hasil_seleksi_mitra)
                ->orderBy('id_hasil_seleksi_mitra', 'asc')
                ->count();
            
            // Calculate sequential order number hasil seleksi (urutan data keberapa di database hasil_seleksi_mitra)
            $urutanDataHasilSeleksi = HasilSeleksiMitra::where('id_hasil_seleksi_mitra', '<=', $hasilSeleksi->id_hasil_seleksi_mitra)
                ->orderBy('id_hasil_seleksi_mitra', 'asc')
                ->count();
            
            // Calculate sequential order number seleksi (urutan data keberapa di database data_seleksi_mitra)
            $urutanDataSeleksi = DataSeleksiMitra::where('id_seleksi_mitra', '<=', $hasilSeleksi->id_seleksi_mitra)
                ->orderBy('id_seleksi_mitra', 'asc')
                ->count();
            
            // Ambil tahun dari created_at hasil seleksi
            $tahunPengajuan = $hasilSeleksi->created_at->year;
            
            // Konversi urutan hasil seleksi mitra ke angka romawi
            $urutanHasilSeleksiRomawi = $this->toRoman($urutanHasilSeleksi);

            $bulanRomawi = $this->getRomanMonth(date('n'));
            
            // Format nomor surat: {urutan_data}/11030/BA/SELEKSI/{urutan_hasil_seleksi_romawi}/{tahun}
            $nomorSurat = sprintf(
                "%02d/11030/BA/SELEKSI/%s/%s",
                $urutanDataHasilSeleksi,
                $bulanRomawi,
                $tahunPengajuan
            );
            
            // Siapkan detail dokumen (sudah auto-cast ke array oleh model)
            $dokumenAda = $hasilSeleksi->dokumen_ada_valid ?: [];
            $dokumenTidakAda = $hasilSeleksi->dokumen_tidak_ada ?: [];
            $dokumenAdaTidakValid = $hasilSeleksi->dokumen_ada_tidak_valid ?: [];
            
            $keteranganDokumen = '';
            if (!empty($dokumenAda)) {
                $keteranganDokumen .= "Dokumen Ada dan Valid : " . implode(', ', $dokumenAda);
            }
            if (!empty($dokumenAdaTidakValid)) {
                if ($keteranganDokumen) $keteranganDokumen .= "\n\n";
                $keteranganDokumen .= "Dokumen Ada Tetapi Tidak Valid : " . implode(', ', $dokumenAdaTidakValid);
            }
            if (!empty($dokumenTidakAda)) {
                if ($keteranganDokumen) $keteranganDokumen .= "\n\n";
                $keteranganDokumen .= "Dokumen Belum Lengkap : " . implode(', ', $dokumenTidakAda);
            }
            
            // Siapkan detail sarana pengeringan
            $pengeringanAda = $hasilSeleksi->sarana_pengeringan_ada ?: [];
            $pengeringanTidakAda = $hasilSeleksi->sarana_pengeringan_tidak_ada ?: [];
            
            $keteranganPengeringan = '';
            if (!empty($pengeringanAda)) {
                $keteranganPengeringan .= "Sarana Ada : " . implode(', ', $pengeringanAda);
            }
            if (!empty($pengeringanTidakAda)) {
                if ($keteranganPengeringan) $keteranganPengeringan .= "\n\n";
                $keteranganPengeringan .= "Sarana Tidak Memenuhi : " . implode(', ', $pengeringanTidakAda);
            }
            
            // Siapkan detail sarana penggilingan
            $penggilinganAda = $hasilSeleksi->sarana_penggilingan_ada ?: [];
            $penggilinganTidakAda = $hasilSeleksi->sarana_penggilingan_tidak_ada ?: [];
            
            $keteranganPenggilingan = '';
            if (!empty($penggilinganAda)) {
                $keteranganPenggilingan .= "Sarana Ada : " . implode(', ', $penggilinganAda);
            }
            if (!empty($penggilinganTidakAda)) {
                if ($keteranganPenggilingan) $keteranganPenggilingan .= "\n\n";
                $keteranganPenggilingan .= "Sarana Tidak Memenuhi : " . implode(', ', $penggilinganTidakAda);
            }
            
            $data = [
                'nomor_surat' => $nomorSurat,
                'tahun' => $tahunPengajuan,
                'hari' => $today->locale('id')->isoFormat('dddd'),
                'tanggal' => sprintf(
                    "%s bulan %s tahun %s (%s)",
                    $this->terbilang($today->day),
                    $this->bulanIndo($today->month),
                    'Dua Ribu Dua Puluh Lima',
                    $today->format('d-m-Y')
                ),
                'nomor_urut_seleksi' => sprintf("%02d", $urutanDataSeleksi),
                'tanggal_seleksi' => $hasilSeleksi->created_at->isoFormat('D MMMM Y'),
                'nomor_entitas_bulog' => '11030',
                'unit_pelaksana' => 'SURAKARTA',
                'nama_perusahaan' => $hasilSeleksi->mitra->nama_perusahaan,
                'badan_usaha' => $hasilSeleksi->mitra->badan_hukum_usaha,
                'alamat' => $hasilSeleksi->mitra->alamat_perusahaan,
                'status_mitra' => $hasilSeleksi->mitra->status_perusahaan ?? 'Penggilingan',
                'hasil_seleksi' => $hasilSeleksi,
                'hasil_akhir' => strtoupper($hasilSeleksi->kesimpulan_akhir),
                'nama_pj_mitra' => $hasilSeleksi->mitra->nama_cp,
                'nama_pj_bulog' => $pengetahui->nama_karyawan,
                'pelaksana' => $pelaksana,
                'pengetahui' => $pengetahui,
                'keterangan_dokumen' => $keteranganDokumen,
                'keterangan_pengeringan' => $keteranganPengeringan,
                'keterangan_penggilingan' => $keteranganPenggilingan
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

    public function generateBeritaAcaraKlasifikasi($id, Request $request)
    {
        try {
            $klasifikasi = KlasifikasiMitra::with('mitra')->findOrFail($id);
            $pelaksana = Karyawan::findOrFail($request->id_pelaksana);
            $pengetahui = Karyawan::findOrFail($request->id_pengetahui);
            
            $today = now();
            
            // Hitung urutan klasifikasi untuk mitra ini
            $urutanKlasifikasi = KlasifikasiMitra::where('id_mitra', $klasifikasi->id_mitra)
                ->where('id_klasifikasi_mitra', '<=', $klasifikasi->id_klasifikasi_mitra)
                ->orderBy('id_klasifikasi_mitra', 'asc')
                ->count();
            
            // Calculate sequential order number (urutan data keberapa di database)
            $urutanDataKlasifikasi = KlasifikasiMitra::where('id_klasifikasi_mitra', '<=', $klasifikasi->id_klasifikasi_mitra)
                ->orderBy('id_klasifikasi_mitra', 'asc')
                ->count();
            
            // Ambil tahun dari created_at klasifikasi
            $tahunPengajuan = $klasifikasi->created_at->year;
            
            // Konversi id_klasifikasi ke angka romawi
            $idKlasifikasiRomawi = $this->toRoman($klasifikasi->id_klasifikasi_mitra);

            $bulanRomawi = $this->getRomanMonth(date('n'));
            
            // Format nomor surat: {urutan_data}/11030/BA/KLASIFIKASI/{id_klasifikasi_romawi}/{tahun}
            $nomorSurat = sprintf(
                "%02d/11030/BA/KLASIFIKASI/%s/%s",
                $urutanDataKlasifikasi,
                $bulanRomawi,
                $tahunPengajuan
            );
            
            // Mapping hasil untuk setiap komponen
            $hasilMapping = [
                'mesin_pembersih_gabah' => $this->getKlasifikasiHasil('mesin_pembersih_gabah', $klasifikasi->mesin_pembersih_gabah),
                'lantai_jemur' => $this->getKlasifikasiHasil('lantai_jemur', $klasifikasi->lantai_jemur),
                'mesin_pengering' => $this->getKlasifikasiHasil('mesin_pengering', $klasifikasi->mesin_pengering),
                'alat_pengering_lainnya' => $this->getKlasifikasiHasil('alat_pengering_lainnya', $klasifikasi->alat_pengering_lainnya),
                'mesin_pembersih_awal' => $this->getKlasifikasiHasil('mesin_pembersih_awal', $klasifikasi->mesin_pembersih_awal),
                'mesin_pemecah_kulit' => $this->getKlasifikasiHasil('mesin_pemecah_kulit', $klasifikasi->mesin_pemecah_kulit),
                'mesin_pembersih_sekam' => $this->getKlasifikasiHasil('mesin_pembersih_sekam', $klasifikasi->mesin_pembersih_sekam),
                'mesin_pemisah_gabah_pecah_kulit' => $this->getKlasifikasiHasil('mesin_pemisah_gabah_pecah_kulit', $klasifikasi->mesin_pemisah_gabah_pecah_kulit),
                'mesin_pemisah_batu' => $this->getKlasifikasiHasil('mesin_pemisah_batu', $klasifikasi->mesin_pemisah_batu),
                'mesin_penyosoh' => $this->getKlasifikasiHasil('mesin_penyosoh', $klasifikasi->mesin_penyosoh),
                'mesin_pengkabut' => $this->getKlasifikasiHasil('mesin_pengkabut', $klasifikasi->mesin_pengkabut),
                'mesin_pemesah_menir' => $this->getKlasifikasiHasil('mesin_pemesah_menir', $klasifikasi->mesin_pemesah_menir),
                'mesin_pemisah_katul' => $this->getKlasifikasiHasil('mesin_pemisah_katul', $klasifikasi->mesin_pemisah_katul),
                'mesin_pemisah_berdasarkan_ukuran' => $this->getKlasifikasiHasil('mesin_pemisah_berdasarkan_ukuran', $klasifikasi->mesin_pemisah_berdasarkan_ukuran),
                'mesin_pemisah_berdasarkan_warna' => $this->getKlasifikasiHasil('mesin_pemisah_berdasarkan_warna', $klasifikasi->mesin_pemisah_berdasarkan_warna),
                'tangki_penyimpanan' => $this->getKlasifikasiHasil('tangki_penyimpanan', $klasifikasi->tangki_penyimpanan),
                'mesin_pengemas' => $this->getKlasifikasiHasil('mesin_pengemas', $klasifikasi->mesin_pengemas),
                'mesin_jahit' => $this->getKlasifikasiHasil('mesin_jahit', $klasifikasi->mesin_jahit),
                'gudang_konvensional' => $this->getKlasifikasiHasil('gudang_konvensional', $klasifikasi->gudang_konvensional),
                'silo_gkg_hopper' => $this->getKlasifikasiHasil('silo_gkg_hopper', $klasifikasi->silo_gkg_hopper),
                'truk' => $this->getKlasifikasiHasil('truk', $klasifikasi->truk),
                'mini_lab' => $this->getKlasifikasiHasil('mini_lab', $klasifikasi->mini_lab),
                'moisture_tester' => $this->getKlasifikasiHasil('moisture_tester', $klasifikasi->moisture_tester),
                'pembanding_derajat_sosoh' => $this->getKlasifikasiHasil('pembanding_derajat_sosoh', $klasifikasi->pembanding_derajat_sosoh),
                'bagian_quality_control' => $this->getKlasifikasiHasil('bagian_quality_control', $klasifikasi->bagian_quality_control),
            ];
            
            $data = [
                'nomor_surat' => $nomorSurat,
                'tahun' => $tahunPengajuan,
                'hari' => $today->locale('id')->isoFormat('dddd'),
                'tanggal' => sprintf(
                    "%s bulan %s tahun %s (%s)",
                    $this->terbilang($today->day),
                    $this->bulanIndo($today->month),
                    'Dua Ribu Dua Puluh Lima',
                    $today->format('d-m-Y')
                ),
                'nomor_urut' => sprintf("%d", $urutanKlasifikasi),
                'tanggal_klasifikasi' => $klasifikasi->created_at->isoFormat('D MMMM Y'),
                'nomor_entitas' => '11030',
                'unit_pelaksana' => 'SURAKARTA',
                'nama_perusahaan' => $klasifikasi->mitra->nama_perusahaan,
                'badan_usaha' => $klasifikasi->mitra->badan_hukum_usaha,
                'alamat' => $klasifikasi->mitra->alamat_perusahaan,
                'status' => $klasifikasi->mitra->status_perusahaan ?? 'Penggilingan',
                'nama_cp' => $klasifikasi->mitra->nama_cp,
                'klasifikasi' => $klasifikasi,
                'hasil' => $hasilMapping,
                'pelaksana' => $pelaksana,
                'pengetahui' => $pengetahui
            ];

            $pdf = PDF::loadView('pdf.berita-acara-klasifikasi', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = sprintf(
                'BA-KLASIFIKASI-%s-%s.pdf',
                str_replace(' ', '-', $klasifikasi->mitra->nama_perusahaan),
                $today->format('dmY')
            );

            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getKlasifikasiHasil($field, $value)
    {
        // Jika value null atau kosong, return '-'
        if (!$value) {
            return '-';
        }
        
        // Jika value sudah dalam format deskriptif baru (mengandung titik), konversi simbol untuk PDF
        // Format baru: '1. Tidak Ada', '2. Ada | ≤ 20', '3. Ada | > 20'
        if (is_string($value) && strpos($value, '.') !== false) {
            // Ganti simbol matematika yang tidak didukung PDF dengan karakter ASCII
            $value = str_replace('≤', '<', $value);
            $value = str_replace('≥', '>', $value);
            return $value;
        }
        
        // Fallback: Mapping untuk backward compatibility dengan data lama (angka 1, 2, 3)
        $legacyMapping = [
            'mesin_pembersih_gabah' => [3 => '3. Ada | > 20', 2 => '2. Ada | < 20', 1 => '1. Tidak Ada'],
            'lantai_jemur' => [3 => '3. Ada | > 10', 2 => '2. Ada | 1 s/d 10', 1 => '1. Tidak ada'],
            'mesin_pengering' => [3 => '3. Ada | > 20', 2 => '2. Ada | < 20', 1 => '1. Tidak ada'],
            'alat_pengering_lainnya' => [3 => '3. Tidak Disyaratkan', 2 => '2. Tidak Disyaratkan', 1 => '1. Ada | < 1'],
            'mesin_pembersih_awal' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_pemecah_kulit' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_pembersih_sekam' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_pemisah_gabah_pecah_kulit' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_pemisah_batu' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_penyosoh' => [3 => '3. Ada | > 3 | 2', 2 => '2. Ada | 1 s/d 3 | 1', 1 => '1. Tidak ada'],
            'mesin_pengkabut' => [3 => '3. Ada | > 3 | 2', 2 => '2. Ada | 1 s/d 3 | 1', 1 => '1. Tidak ada'],
            'mesin_pemesah_menir' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_pemisah_katul' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_pemisah_berdasarkan_ukuran' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'mesin_pemisah_berdasarkan_warna' => [3 => '3. Ada | > 3', 2 => '2. Ada | 1 s/d 3', 1 => '1. Tidak ada'],
            'tangki_penyimpanan' => [3 => '3. Ada | > 10', 2 => '2. Ada | < 10', 1 => '1. Tidak ada'],
            'mesin_pengemas' => [3 => '3. Ada | Full Otomatis', 2 => '2. Ada | Semi Otomatis', 1 => '1. Tidak ada'],
            'mesin_jahit' => [3 => '3. Ada | Full Otomatis', 2 => '2. Ada | Semi Otomatis', 1 => '1. Tidak ada'],
            'gudang_konvensional' => [3 => '3. Ada | > 3000', 2 => '2. Ada | < 3000', 1 => '1. Tidak ada'],
            'silo_gkg_hopper' => [3 => '3. Ada | > 2000', 2 => '2. Ada | < 2000', 1 => '1. Tidak ada'],
            'truk' => [3 => '3. Ada | > 5', 2 => '2. Ada | 1 s/d 5', 1 => '1. Tidak ada'],
            'mini_lab' => [3 => '3. Ada | Ruang Khusus', 2 => '2. Ada | Tidak Khusus', 1 => '1. Tidak ada'],
            'moisture_tester' => [3 => '3. Ada | Berfungsi', 2 => '2. Ada | Tidak Berfunggi', 1 => '1. Tidak ada'],
            'pembanding_derajat_sosoh' => [3 => '3. Ada | Sesuai Standar', 2 => '2. Ada | Tidak Sesuai Standar', 1 => '1. Tidak ada'],
            'bagian_quality_control' => [3 => '3. Ada | Tidak Merangkap', 2 => '2. Ada | Merangkap', 1 => '1. Tidak ada'],
        ];

        // Cek apakah value adalah angka dan ada di mapping legacy
        if (isset($legacyMapping[$field][$value])) {
            return $legacyMapping[$field][$value];
        }

        // Jika tidak ada mapping, return value dengan konversi simbol atau '-'
        if (is_string($value)) {
            $value = str_replace('≤', '<', $value);
            $value = str_replace('≥', '>', $value);
            return $value;
        }
        
        return '-';
    }

    public function generateSuratPenetapanKlasifikasi($id, Request $request)
    {
        try {
            $klasifikasi = KlasifikasiMitra::with('mitra')->findOrFail($id);
            $karyawan = Karyawan::findOrFail($request->id_karyawan);
            
            $today = now();
            
            // Calculate sequential order number (urutan data keberapa di database)
            $urutanDataKlasifikasi = KlasifikasiMitra::where('id_klasifikasi_mitra', '<=', $id)
                ->orderBy('id_klasifikasi_mitra', 'asc')
                ->count();
            
            // Convert urutan to Roman numeral
            $urutanRoman = $this->toRoman($urutanDataKlasifikasi);

            $bulanRomawi = $this->getRomanMonth(date('n'));
            
            $data = [
                'nomor_surat' => sprintf(
                    "%d/11030/SP/KLASIFIKASI/%s/%s",
                    $urutanDataKlasifikasi,
                    $bulanRomawi,
                    $today->year
                ),
                'tahun' => $today->year,
                'hari' => \Carbon\Carbon::now()->locale('id')->isoFormat('dddd'),
                'tanggal' => sprintf(
                    "%s bulan %s tahun %s (%s)",
                    $this->terbilang($today->day),
                    $this->bulanIndo($today->month),
                    'Dua Ribu Dua Puluh Lima',
                    $today->format('d-m-Y')
                ),
                'nomor_BA' => sprintf(
                    "%d/11030/BA/KLASIFIKASI/%s/%s",
                    $urutanDataKlasifikasi,
                    $bulanRomawi,
                    $today->year
                ),
                'nama_perusahaan' => $klasifikasi->mitra->nama_perusahaan,
                'badan_usaha' => $klasifikasi->mitra->badan_hukum_usaha,
                'alamat_perusahaan' => $klasifikasi->mitra->alamat_perusahaan,
                'status_mitra' => $klasifikasi->mitra->status_perusahaan ?? 'Penggilingan',
                'nomor_urut_klasifikasi' => sprintf("%02d", $urutanDataKlasifikasi),
                'hasil_klasifikasi' => $klasifikasi->hasil_klasifikasi,
                'kantor_cabang' => 'Surakarta',
                'nama_mitra' => $klasifikasi->mitra->nama_cp,
                'nama_pejabat' => $karyawan->nama_karyawan,
                'jabatan_pejabat' => $karyawan->jabatan
            ];

            $pdf = PDF::loadView('pdf.surat-penetapan-klasifikasi', $data);
            return $pdf->download('surat-penetapan-klasifikasi-'.$klasifikasi->mitra->nama_perusahaan.'.pdf');
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateSuratPermohonanPdf($id)
    {
        try {
            $mitra = DataMitra::where('id_mitra', $id)
                            ->where('user_id', auth()->id())
                            ->firstOrFail();
            
            // Buat daftar lampiran berdasarkan data mitra
            $lampiranList = [
                '1. Permohonan',
                '2. KTP',
                '3. NPWP',
                '4. NIB',
            ];
            
            $nomorLampiran = 5;
            
            // Tambahkan Akta Pendirian hanya untuk CV atau PT
            if (!empty($mitra->badan_hukum_usaha) && 
                (strtoupper($mitra->badan_hukum_usaha) === 'CV' || 
                 strtoupper($mitra->badan_hukum_usaha) === 'PT')) {
                $lampiranList[] = $nomorLampiran . '. Akta Pendirian';
                $nomorLampiran++;
            }
            
            // Tambahkan No Rekening
            $lampiranList[] = $nomorLampiran . '. No Rekening';
            $nomorLampiran++;
            
            // Tambahkan lampiran berdasarkan data PKP (hanya jika ada)
            if (!empty($mitra->pkp) && $mitra->pkp === 'Non Pkp') {
                $lampiranList[] = $nomorLampiran . '. Surat Pernyataan Non PKP';
                $nomorLampiran++;
            }
            else if (!empty($mitra->pkp) && $mitra->pkp === 'Pkp') {
                $lampiranList[] = $nomorLampiran . '. Surat Pernyataan PKP';
                $nomorLampiran++;
            }
            
            // Tambahkan Surat Kuasa jika ada
            if (!empty($mitra->surat_kuasa) && $mitra->surat_kuasa === 'Ada') {
                $lampiranList[] = $nomorLampiran . '. Surat Kuasa';
            }
            
            $data = [
                'tanggal_permohonan' => \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y'),
                'tahun' => date('Y'),
                'nama_cp' => $mitra->nama_cp,
                'alamat_perusahaan' => $mitra->alamat_perusahaan,
                'jabatan' => $mitra->jabatan ?? 'Direktur',
                'no_telp' => $mitra->no_telp_cp ?? '-',
                'nama_perusahaan' => $mitra->nama_perusahaan,
                'lampiran_list' => $lampiranList,
                'mitra' => $mitra
            ];

            $pdf = PDF::loadView('pdf.surat-permohonan-mpp', $data);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('Surat-Permohonan-MPP-' . str_replace(' ', '-', $mitra->nama_perusahaan) . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Surat Permohonan PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateSuratPernyataanNonPkp($id)
    {
        try {
            $mitra = DataMitra::where('id_mitra', $id)
                            ->where('user_id', auth()->id())
                            ->firstOrFail();
            
            $data = [
                'tanggal' => Carbon::now()->locale('id')->isoFormat('D MMMM Y'),
                'nama_cp' => $mitra->nama_cp,
                'alamat_perusahaan' => $mitra->alamat_perusahaan,
                'jabatan' => $mitra->jabatan ?? 'Direktur',
                'nama_perusahaan' => $mitra->nama_perusahaan
            ];

            $pdf = PDF::loadView('pdf.surat-pernyataan-non-pkp', $data);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('Surat-Pernyataan-Non-PKP-' . str_replace(' ', '-', $mitra->nama_perusahaan) . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Surat Pernyataan Non PKP PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generatePaktaIntegritas($id)
    {
        try {
            $mitra = DataMitra::where('id_mitra', $id)
                            ->where('user_id', auth()->id())
                            ->firstOrFail();
            
            $data = [
                'tanggal' => Carbon::now()->locale('id')->isoFormat('D MMMM Y'),
                'tahun' => Carbon::now()->year,
                'nama_cp' => $mitra->nama_cp,
                'nama_perusahaan' => $mitra->nama_perusahaan,
                'alamat_perusahaan' => $mitra->alamat_perusahaan
            ];

            $pdf = PDF::loadView('pdf.pakta-integritas', $data);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('Pakta-Integritas-' . str_replace(' ', '-', $mitra->nama_perusahaan) . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Pakta Integritas PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}