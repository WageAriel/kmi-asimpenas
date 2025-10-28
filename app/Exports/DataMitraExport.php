<?php

namespace App\Exports;

use App\Models\DataMitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class DataMitraExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $rowNumber = 0;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Order by created_at ascending untuk numbering dari data pertama
        return DataMitra::with('user')->orderBy('created_at', 'asc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Perusahaan',
            'Badan Hukum',
            'Alamat Perusahaan',
            'Kota/Kabupaten',
            'Provinsi',
            'Nama CP',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'No Telp Perusahaan',
            'No Telp CP',
            'Bank Korespondensi',
            'Alamat Bank',
            'No Rekening',
            'Status Perusahaan',
            'NPWP',
            'PKP',
            'Surat Kuasa',
            'Tanggal Seleksi',
            'Tanggal Klasifikasi',
            'Tanggal Penilaian',
            'Tanggal Penetapan',
            'Tanggal Surat Permohonan',
            'Tanggal Pakta Integritas',
            'Email',
            'No VMS',
            'Kode Mitra',
            'User Email',
            'Tanggal Daftar',
        ];
    }

    /**
     * @param mixed $mitra
     * @return array
     */
    public function map($mitra): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber, // Numbering otomatis
            $mitra->nama_perusahaan,
            $mitra->badan_hukum_usaha,
            $mitra->alamat_perusahaan,
            $mitra->kota_kabupaten,
            $mitra->provinsi,
            $mitra->nama_cp,
            $mitra->nik, // Will be formatted as text in registerEvents
            $mitra->tempat_lahir,
            $mitra->tanggal_lahir ? \Carbon\Carbon::parse($mitra->tanggal_lahir)->format('d-m-Y') : '',
            $mitra->no_telp_perusahaan,
            $mitra->no_telp_cp,
            $mitra->bank_korespondensi,
            $mitra->alamat_bank,
            $mitra->no_rekening, // Will be formatted as text in registerEvents
            $mitra->status_perusahaan,
            $mitra->npwp, // Will be formatted as text in registerEvents
            $mitra->pkp,
            $mitra->surat_kuasa,
            $mitra->tanggal_seleksi ? \Carbon\Carbon::parse($mitra->tanggal_seleksi)->format('d-m-Y') : '',
            $mitra->tanggal_klasifikasi ? \Carbon\Carbon::parse($mitra->tanggal_klasifikasi)->format('d-m-Y') : '',
            $mitra->tanggal_penilaian ? \Carbon\Carbon::parse($mitra->tanggal_penilaian)->format('d-m-Y') : '',
            $mitra->tanggal_penetapan ? \Carbon\Carbon::parse($mitra->tanggal_penetapan)->format('d-m-Y') : '',
            $mitra->tanggal_surat_permohonan ? \Carbon\Carbon::parse($mitra->tanggal_surat_permohonan)->format('d-m-Y') : '',
            $mitra->tanggal_pakta_integritas ? \Carbon\Carbon::parse($mitra->tanggal_pakta_integritas)->format('d-m-Y') : '',
            $mitra->email,
            $mitra->no_vms,
            $mitra->kode_mitra,
            $mitra->user ? $mitra->user->email : '',
            $mitra->created_at ? \Carbon\Carbon::parse($mitra->created_at)->format('d-m-Y H:i') : '',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold with background color
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
        ];
    }

    /**
     * Register events to format specific columns as text
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Format kolom NIK (H), No Rekening (O), dan NPWP (Q) sebagai TEXT
                // Ini akan mencegah scientific notation
                
                // NIK - Column H (index 8)
                $sheet->getStyle('H2:H' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);
                
                // No Rekening - Column O (index 15)
                $sheet->getStyle('O2:O' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);
                
                // NPWP - Column Q (index 17)
                $sheet->getStyle('Q2:Q' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);
                
                // No Telp Perusahaan - Column K (index 11)
                $sheet->getStyle('K2:K' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);
                
                // No Telp CP - Column L (index 12)
                $sheet->getStyle('L2:L' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Set nilai sebagai string eksplisit untuk kolom-kolom tersebut
                for ($row = 2; $row <= $highestRow; $row++) {
                    // NIK
                    $nikValue = $sheet->getCell('H' . $row)->getValue();
                    if (!empty($nikValue)) {
                        $sheet->setCellValueExplicit('H' . $row, $nikValue, DataType::TYPE_STRING);
                    }
                    
                    // No Rekening
                    $rekeningValue = $sheet->getCell('O' . $row)->getValue();
                    if (!empty($rekeningValue)) {
                        $sheet->setCellValueExplicit('O' . $row, $rekeningValue, DataType::TYPE_STRING);
                    }
                    
                    // NPWP
                    $npwpValue = $sheet->getCell('Q' . $row)->getValue();
                    if (!empty($npwpValue)) {
                        $sheet->setCellValueExplicit('Q' . $row, $npwpValue, DataType::TYPE_STRING);
                    }
                    
                    // No Telp Perusahaan
                    $telpPerusahaanValue = $sheet->getCell('K' . $row)->getValue();
                    if (!empty($telpPerusahaanValue)) {
                        $sheet->setCellValueExplicit('K' . $row, $telpPerusahaanValue, DataType::TYPE_STRING);
                    }
                    
                    // No Telp CP
                    $telpCpValue = $sheet->getCell('L' . $row)->getValue();
                    if (!empty($telpCpValue)) {
                        $sheet->setCellValueExplicit('L' . $row, $telpCpValue, DataType::TYPE_STRING);
                    }
                }
            },
        ];
    }
}
