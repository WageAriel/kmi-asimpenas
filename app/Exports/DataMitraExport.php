<?php

namespace App\Exports;

use App\Models\DataMitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class DataMitraExport implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
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
     * Tidak menggunakan WithHeadings karena kita akan custom header di AfterSheet
     */

    /**
     * @param mixed $mitra
     * @return array
     */
    public function map($mitra): array
    {
        $this->rowNumber++;
        
        // Logika untuk PKP dan NON PKP
        $pkpAda = '';
        $nonPkpAda = '';
        if ($mitra->pkp) {
            if (strtolower($mitra->pkp) === 'pkp') {
                $pkpAda = 'Ada';
            } elseif (strtolower($mitra->pkp) === 'non pkp') {
                $nonPkpAda = 'Ada';
            }
        }
        
        return [
            $this->rowNumber, // No
            $mitra->nama_perusahaan, // Nama Perusahaan
            $mitra->badan_hukum_usaha, // Badan Hukum/Usaha
            $mitra->alamat_perusahaan, // Alamat Perusahaan
            $mitra->no_telp_perusahaan, // Nomor Telp Perusahaan
            $mitra->nama_cp, // Nama Kontak Person
            $mitra->no_telp_cp, // Nomor Telp/HP Contact Person
            $mitra->bank_korespondensi, // Bank Korespondensi
            $mitra->alamat_bank, // Alamat Bank Korespondensi
            $mitra->surat_kuasa === 'Ada' ? 'Ada' : ($mitra->surat_kuasa === 'Tidak Ada' ? 'Tidak Ada' : ''), // Surat Kuasa - Ada/Tidak Ada
            $mitra->keterangan_surat_kuasa ?? '', // Surat Kuasa - Keterangan
            $mitra->no_rekening, // Nomor Rekening
            $mitra->status_perusahaan, // Status Perusahaan
            $mitra->tanggal_seleksi ? \Carbon\Carbon::parse($mitra->tanggal_seleksi)->format('d/m/Y') : '', // Tanggal Seleksi
            $mitra->tanggal_klasifikasi ? \Carbon\Carbon::parse($mitra->tanggal_klasifikasi)->format('d/m/Y') : '', // Tanggal Klasifikasi
            $mitra->tanggal_penilaian ? \Carbon\Carbon::parse($mitra->tanggal_penilaian)->format('d/m/Y') : '', // Tanggal Penilaian
            $mitra->tanggal_penetapan ? \Carbon\Carbon::parse($mitra->tanggal_penetapan)->format('d/m/Y') : '', // Tanggal Penetapan
            $mitra->tanggal_surat_permohonan ? \Carbon\Carbon::parse($mitra->tanggal_surat_permohonan)->format('d/m/Y') : '', // Tanggal Surat Permohonan
            $mitra->tanggal_pakta_integritas ? \Carbon\Carbon::parse($mitra->tanggal_pakta_integritas)->format('d/m/Y') : '', // Tgl Pakta Integritas
            $mitra->nik, // NIK
            $mitra->npwp, // NPWP
            $pkpAda, // PKP
            $nonPkpAda, // NON PKP
            $mitra->keterangan_pkp ?? '', // Keterangan PKP
            $mitra->email, // Email
            $mitra->no_vms, // NO VMS
            $mitra->kode_mitra, // KODE MITRA
        ];
    }

    /**
     * Register events to create custom headers and formatting
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert 3 rows at the top for headers
                $sheet->insertNewRowBefore(1, 3);
                
                // HEADER ROW 1 (Main Headers)
                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'DATA CALON & MITRA KERJA ADA GABAH/BERAS DN');
                $sheet->setCellValue('M1', 'Status Perusahaan');
                $sheet->setCellValue('N1', 'Tanggal Seleksi');
                $sheet->setCellValue('O1', 'Tanggal Klasifikasi');
                $sheet->setCellValue('P1', 'Tanggal Penilaian');
                $sheet->setCellValue('Q1', 'Tanggal Penetapan');
                $sheet->setCellValue('R1', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('S1', 'Tgl Pakta Integritas');
                $sheet->setCellValue('T1', 'NIK');
                $sheet->setCellValue('U1', 'NPWP');
                $sheet->setCellValue('V1', 'PENGUSAHA KENA PAJAK');
                $sheet->setCellValue('Y1', 'Email');
                $sheet->setCellValue('Z1', 'NO VMS');
                $sheet->setCellValue('AA1', 'KODE MITRA');
                
                // HEADER ROW 2 (Sub Headers Level 1)
                $sheet->setCellValue('A2', 'No');
                $sheet->setCellValue('B2', 'NAMA PERUSAHAAN');
                $sheet->setCellValue('C2', 'BADAN HUKUM/USAHA');
                $sheet->setCellValue('D2', 'ALAMAT PERUSAHAAN');
                $sheet->setCellValue('E2', 'NOMOR TELP PERUSAHAAN');
                $sheet->setCellValue('F2', 'NAMA KONTAK PERSON');
                $sheet->setCellValue('G2', 'NOMOR TELP/HP CONTACT PERSON');
                $sheet->setCellValue('H2', 'BANK KORESPONDENSI');
                $sheet->setCellValue('I2', 'ALAMAT BANK KORESPONDENSI');
                $sheet->setCellValue('J2', 'SURAT KUASA');
                $sheet->setCellValue('L2', 'NOMOR REKENING');
                $sheet->setCellValue('M2', 'Status Perusahaan');
                $sheet->setCellValue('N2', 'Tanggal Seleksi');
                $sheet->setCellValue('O2', 'Tanggal Klasifikasi');
                $sheet->setCellValue('P2', 'Tanggal Penilaian');
                $sheet->setCellValue('Q2', 'Tanggal Penetapan');
                $sheet->setCellValue('R2', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('S2', 'Tgl Pakta Integritas');
                $sheet->setCellValue('T2', 'NIK');
                $sheet->setCellValue('U2', 'NPWP');
                $sheet->setCellValue('V2', 'PKP');
                $sheet->setCellValue('W2', 'NON PKP');
                $sheet->setCellValue('X2', 'Keterangan PKP');
                $sheet->setCellValue('Y2', 'Email');
                $sheet->setCellValue('Z2', 'NO VMS');
                $sheet->setCellValue('AA2', 'KODE MITRA');
                
                // HEADER ROW 3 (Sub Headers Level 2 - hanya untuk Surat Kuasa)
                $sheet->setCellValue('A3', 'No');
                $sheet->setCellValue('B3', 'NAMA PERUSAHAAN');
                $sheet->setCellValue('C3', 'BADAN HUKUM/USAHA');
                $sheet->setCellValue('D3', 'ALAMAT PERUSAHAAN');
                $sheet->setCellValue('E3', 'NOMOR TELP PERUSAHAAN');
                $sheet->setCellValue('F3', 'NAMA KONTAK PERSON');
                $sheet->setCellValue('G3', 'NOMOR TELP/HP CONTACT PERSON');
                $sheet->setCellValue('H3', 'BANK KORESPONDENSI');
                $sheet->setCellValue('I3', 'ALAMAT BANK KORESPONDENSI');
                $sheet->setCellValue('J3', 'Ada/Tidak Ada');
                $sheet->setCellValue('K3', 'Keterangan');
                $sheet->setCellValue('L3', 'NOMOR REKENING');
                $sheet->setCellValue('M3', 'Status Perusahaan');
                $sheet->setCellValue('N3', 'Tanggal Seleksi');
                $sheet->setCellValue('O3', 'Tanggal Klasifikasi');
                $sheet->setCellValue('P3', 'Tanggal Penilaian');
                $sheet->setCellValue('Q3', 'Tanggal Penetapan');
                $sheet->setCellValue('R3', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('S3', 'Tgl Pakta Integritas');
                $sheet->setCellValue('T3', 'NIK');
                $sheet->setCellValue('U3', 'NPWP');
                $sheet->setCellValue('V3', 'PKP');
                $sheet->setCellValue('W3', 'NON PKP');
                $sheet->setCellValue('X3', 'Keterangan PKP');
                $sheet->setCellValue('Y3', 'Email');
                $sheet->setCellValue('Z3', 'NO VMS');
                $sheet->setCellValue('AA3', 'KODE MITRA');
                
                // Merge cells for main headers
                $sheet->mergeCells('A1:A3'); // No
                $sheet->mergeCells('B1:L1'); // DATA CALON & MITRA KERJA (span semua kolom data calon)
                $sheet->mergeCells('M1:M3'); // Status Perusahaan
                $sheet->mergeCells('N1:N3'); // Tanggal Seleksi
                $sheet->mergeCells('O1:O3'); // Tanggal Klasifikasi
                $sheet->mergeCells('P1:P3'); // Tanggal Penilaian
                $sheet->mergeCells('Q1:Q3'); // Tanggal Penetapan
                $sheet->mergeCells('R1:R3'); // Tanggal Surat Permohonan
                $sheet->mergeCells('S1:S3'); // Tgl Pakta Integritas
                $sheet->mergeCells('T1:T3'); // NIK
                $sheet->mergeCells('U1:U3'); // NPWP
                $sheet->mergeCells('V1:X1'); // PENGUSAHA KENA PAJAK
                $sheet->mergeCells('Y1:Y3'); // Email
                $sheet->mergeCells('Z1:Z3'); // NO VMS
                $sheet->mergeCells('AA1:AA3'); // KODE MITRA
                
                // Merge cells for sub headers level 1 (Row 2)
                $sheet->mergeCells('B2:B3'); // NAMA PERUSAHAAN
                $sheet->mergeCells('C2:C3'); // BADAN HUKUM/USAHA
                $sheet->mergeCells('D2:D3'); // ALAMAT PERUSAHAAN
                $sheet->mergeCells('E2:E3'); // NOMOR TELP PERUSAHAAN
                $sheet->mergeCells('F2:F3'); // NAMA KONTAK PERSON
                $sheet->mergeCells('G2:G3'); // NOMOR TELP/HP CONTACT PERSON
                $sheet->mergeCells('H2:H3'); // BANK KORESPONDENSI
                $sheet->mergeCells('I2:I3'); // ALAMAT BANK KORESPONDENSI
                $sheet->mergeCells('J2:K2'); // SURAT KUASA (span 2 kolom: Ada/Tidak Ada & Keterangan)
                $sheet->mergeCells('L2:L3'); // NOMOR REKENING
                
                // Merge cells for PENGUSAHA KENA PAJAK sub headers
                $sheet->mergeCells('V2:V3'); // PKP
                $sheet->mergeCells('W2:W3'); // NON PKP
                $sheet->mergeCells('X2:X3'); // Keterangan PKP
                
                // Style headers
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BFDBFE'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];
                
                $sheet->getStyle('A1:AA3')->applyFromArray($headerStyle);
                
                // Set row height for headers
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(30);
                $sheet->getRowDimension(3)->setRowHeight(30);
                
                // Get highest row
                $highestRow = $sheet->getHighestRow();
                
                // Apply borders to all data
                $sheet->getStyle('A1:AA' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Center align untuk kolom tertentu (data mulai dari row 4)
                $sheet->getStyle('A4:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J4:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M4:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('N4:S' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('V4:W' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Format kolom sebagai TEXT untuk mencegah scientific notation
                $textColumns = ['E', 'G', 'L', 'T', 'U']; // No Telp Perusahaan, No Telp CP, No Rekening, NIK, NPWP
                
                foreach ($textColumns as $col) {
                    $sheet->getStyle($col . '4:' . $col . $highestRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_TEXT);
                }
                
                // Set nilai sebagai string eksplisit untuk kolom-kolom tersebut (data mulai dari row 4)
                for ($row = 4; $row <= $highestRow; $row++) {
                    foreach ($textColumns as $col) {
                        $value = $sheet->getCell($col . $row)->getValue();
                        if (!empty($value)) {
                            $sheet->setCellValueExplicit($col . $row, $value, DataType::TYPE_STRING);
                        }
                    }
                }
            },
        ];
    }
}
