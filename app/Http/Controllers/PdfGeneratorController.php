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
            $currentYear = date('Y');
            
            // Format data for PDF
            $data = [
                'seleksi' => $seleksi,
                'mitra' => $mitra,
                'tahun' => $currentYear,
                'nomor' => sprintf("%03d/ASIMPENAS/%s", $id, $currentYear),
                'tanggal_seleksi' => date('d-m-Y'),
                'nomor_urut_seleksi' => sprintf("%03d", $id),
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