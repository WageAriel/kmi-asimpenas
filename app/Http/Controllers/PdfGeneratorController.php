<?php
namespace App\Http\Controllers;

use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
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
}