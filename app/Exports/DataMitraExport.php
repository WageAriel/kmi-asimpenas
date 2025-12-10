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
            $mitra->jabatan ?? '', // Jabatan
            $mitra->no_telp_cp, // Nomor Telp/HP Contact Person
            $mitra->bank_korespondensi, // Bank Korespondensi
            $mitra->alamat_bank, // Alamat Bank Korespondensi
            $mitra->surat_kuasa === 'Ada' ? 'Ada' : ($mitra->surat_kuasa === 'Tidak Ada' ? 'Tidak Ada' : ''), // Surat Kuasa - Ada/Tidak Ada
            $mitra->keterangan_surat_kuasa ?? '', // Surat Kuasa - Keterangan
            $mitra->no_rekening, // Nomor Rekening
            $mitra->nama_pemilik_rekening ?? '', // Nama Pemilik Rekening
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
                $sheet->setCellValue('O1', 'Status Perusahaan');
                $sheet->setCellValue('P1', 'Tanggal Seleksi');
                $sheet->setCellValue('Q1', 'Tanggal Klasifikasi');
                $sheet->setCellValue('R1', 'Tanggal Penilaian');
                $sheet->setCellValue('S1', 'Tanggal Penetapan');
                $sheet->setCellValue('T1', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('U1', 'Tgl Pakta Integritas');
                $sheet->setCellValue('V1', 'NIK');
                $sheet->setCellValue('W1', 'NPWP');
                $sheet->setCellValue('X1', 'PENGUSAHA KENA PAJAK');
                $sheet->setCellValue('AA1', 'Email');
                $sheet->setCellValue('AB1', 'NO VMS');
                $sheet->setCellValue('AC1', 'KODE MITRA');
                
                // HEADER ROW 2 (Sub Headers Level 1)
                $sheet->setCellValue('A2', 'No');
                $sheet->setCellValue('B2', 'NAMA PERUSAHAAN');
                $sheet->setCellValue('C2', 'BADAN HUKUM/USAHA');
                $sheet->setCellValue('D2', 'ALAMAT PERUSAHAAN');
                $sheet->setCellValue('E2', 'NOMOR TELP PERUSAHAAN');
                $sheet->setCellValue('F2', 'NAMA KONTAK PERSON');
                $sheet->setCellValue('G2', 'JABATAN');
                $sheet->setCellValue('H2', 'NOMOR TELP/HP CONTACT PERSON');
                $sheet->setCellValue('I2', 'BANK KORESPONDENSI');
                $sheet->setCellValue('J2', 'ALAMAT BANK KORESPONDENSI');
                $sheet->setCellValue('K2', 'SURAT KUASA');
                $sheet->setCellValue('M2', 'NOMOR REKENING');
                $sheet->setCellValue('N2', 'NAMA PEMILIK REKENING');
                $sheet->setCellValue('O2', 'Status Perusahaan');
                $sheet->setCellValue('P2', 'Tanggal Seleksi');
                $sheet->setCellValue('Q2', 'Tanggal Klasifikasi');
                $sheet->setCellValue('R2', 'Tanggal Penilaian');
                $sheet->setCellValue('S2', 'Tanggal Penetapan');
                $sheet->setCellValue('T2', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('U2', 'Tgl Pakta Integritas');
                $sheet->setCellValue('V2', 'NIK');
                $sheet->setCellValue('W2', 'NPWP');
                $sheet->setCellValue('X2', 'PKP');
                $sheet->setCellValue('Y2', 'NON PKP');
                $sheet->setCellValue('Z2', 'Keterangan PKP');
                $sheet->setCellValue('AA2', 'Email');
                $sheet->setCellValue('AB2', 'NO VMS');
                $sheet->setCellValue('AC2', 'KODE MITRA');
                
                // HEADER ROW 3 (Sub Headers Level 2 - hanya untuk Surat Kuasa)
                $sheet->setCellValue('A3', 'No');
                $sheet->setCellValue('B3', 'NAMA PERUSAHAAN');
                $sheet->setCellValue('C3', 'BADAN HUKUM/USAHA');
                $sheet->setCellValue('D3', 'ALAMAT PERUSAHAAN');
                $sheet->setCellValue('E3', 'NOMOR TELP PERUSAHAAN');
                $sheet->setCellValue('F3', 'NAMA KONTAK PERSON');
                $sheet->setCellValue('G3', 'JABATAN');
                $sheet->setCellValue('H3', 'NOMOR TELP/HP CONTACT PERSON');
                $sheet->setCellValue('I3', 'BANK KORESPONDENSI');
                $sheet->setCellValue('J3', 'ALAMAT BANK KORESPONDENSI');
                $sheet->setCellValue('K3', 'Ada/Tidak Ada');
                $sheet->setCellValue('L3', 'Keterangan');
                $sheet->setCellValue('M3', 'NOMOR REKENING');
                $sheet->setCellValue('N3', 'NAMA PEMILIK REKENING');
                $sheet->setCellValue('O3', 'Status Perusahaan');
                $sheet->setCellValue('P3', 'Tanggal Seleksi');
                $sheet->setCellValue('Q3', 'Tanggal Klasifikasi');
                $sheet->setCellValue('R3', 'Tanggal Penilaian');
                $sheet->setCellValue('S3', 'Tanggal Penetapan');
                $sheet->setCellValue('T3', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('U3', 'Tgl Pakta Integritas');
                $sheet->setCellValue('V3', 'NIK');
                $sheet->setCellValue('W3', 'NPWP');
                $sheet->setCellValue('X3', 'PKP');
                $sheet->setCellValue('Y3', 'NON PKP');
                $sheet->setCellValue('Z3', 'Keterangan PKP');
                $sheet->setCellValue('AA3', 'Email');
                $sheet->setCellValue('AB3', 'NO VMS');
                $sheet->setCellValue('AC3', 'KODE MITRA');
                
                // Merge cells for main headers
                $sheet->mergeCells('A1:A3'); // No
                $sheet->mergeCells('B1:N1'); // DATA CALON & MITRA KERJA (span semua kolom data calon termasuk nama pemilik rekening)
                $sheet->mergeCells('O1:O3'); // Status Perusahaan
                $sheet->mergeCells('P1:P3'); // Tanggal Seleksi
                $sheet->mergeCells('Q1:Q3'); // Tanggal Klasifikasi
                $sheet->mergeCells('R1:R3'); // Tanggal Penilaian
                $sheet->mergeCells('S1:S3'); // Tanggal Penetapan
                $sheet->mergeCells('T1:T3'); // Tanggal Surat Permohonan
                $sheet->mergeCells('U1:U3'); // Tgl Pakta Integritas
                $sheet->mergeCells('V1:V3'); // NIK
                $sheet->mergeCells('W1:W3'); // NPWP
                $sheet->mergeCells('X1:Z1'); // PENGUSAHA KENA PAJAK
                $sheet->mergeCells('AA1:AA3'); // Email
                $sheet->mergeCells('AB1:AB3'); // NO VMS
                $sheet->mergeCells('AC1:AC3'); // KODE MITRA
                
                // Merge cells for sub headers level 1 (Row 2)
                $sheet->mergeCells('B2:B3'); // NAMA PERUSAHAAN
                $sheet->mergeCells('C2:C3'); // BADAN HUKUM/USAHA
                $sheet->mergeCells('D2:D3'); // ALAMAT PERUSAHAAN
                $sheet->mergeCells('E2:E3'); // NOMOR TELP PERUSAHAAN
                $sheet->mergeCells('F2:F3'); // NAMA KONTAK PERSON
                $sheet->mergeCells('G2:G3'); // JABATAN
                $sheet->mergeCells('H2:H3'); // NOMOR TELP/HP CONTACT PERSON
                $sheet->mergeCells('I2:I3'); // BANK KORESPONDENSI
                $sheet->mergeCells('J2:J3'); // ALAMAT BANK KORESPONDENSI
                $sheet->mergeCells('K2:L2'); // SURAT KUASA (span 2 kolom: Ada/Tidak Ada & Keterangan)
                $sheet->mergeCells('M2:M3'); // NOMOR REKENING
                $sheet->mergeCells('N2:N3'); // NAMA PEMILIK REKENING
                
                // Merge cells for PENGUSAHA KENA PAJAK sub headers
                $sheet->mergeCells('X2:X3'); // PKP
                $sheet->mergeCells('Y2:Y3'); // NON PKP
                $sheet->mergeCells('Z2:Z3'); // Keterangan PKP
                
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
                
                $sheet->getStyle('A1:AC3')->applyFromArray($headerStyle);
                
                // Set row height for headers
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(30);
                $sheet->getRowDimension(3)->setRowHeight(30);
                
                // Get highest row
                $highestRow = $sheet->getHighestRow();
                
                // Apply borders to all data
                $sheet->getStyle('A1:AC' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Center align untuk kolom tertentu (data mulai dari row 4)
                $sheet->getStyle('A4:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K4:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('O4:O' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('P4:U' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('X4:Y' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Format kolom sebagai TEXT untuk mencegah scientific notation
                $textColumns = ['E', 'H', 'M', 'V', 'W']; // No Telp Perusahaan, No Telp CP, No Rekening, NIK, NPWP
                
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
