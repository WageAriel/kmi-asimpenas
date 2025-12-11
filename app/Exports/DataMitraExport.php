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
            $mitra->nama_yang_dikuasakan ?? '', // Nama Yang Dikuasakan
            $mitra->nik_yang_dikuasakan ?? '', // NIK Yang Dikuasakan
            $mitra->alamat_yang_dikuasakan ?? '', // Alamat Yang Dikuasakan
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
                $sheet->setCellValue('R1', 'Status Perusahaan');
                $sheet->setCellValue('S1', 'Tanggal Seleksi');
                $sheet->setCellValue('T1', 'Tanggal Klasifikasi');
                $sheet->setCellValue('U1', 'Tanggal Penilaian');
                $sheet->setCellValue('V1', 'Tanggal Penetapan');
                $sheet->setCellValue('W1', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('X1', 'Tgl Pakta Integritas');
                $sheet->setCellValue('Y1', 'NIK');
                $sheet->setCellValue('Z1', 'NPWP');
                $sheet->setCellValue('AA1', 'PENGUSAHA KENA PAJAK');
                $sheet->setCellValue('AD1', 'Email');
                $sheet->setCellValue('AE1', 'NO VMS');
                $sheet->setCellValue('AF1', 'KODE MITRA');
                
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
                $sheet->setCellValue('P2', 'NOMOR REKENING');
                $sheet->setCellValue('Q2', 'NAMA PEMILIK REKENING');
                $sheet->setCellValue('R2', 'Status Perusahaan');
                $sheet->setCellValue('S2', 'Tanggal Seleksi');
                $sheet->setCellValue('T2', 'Tanggal Klasifikasi');
                $sheet->setCellValue('U2', 'Tanggal Penilaian');
                $sheet->setCellValue('V2', 'Tanggal Penetapan');
                $sheet->setCellValue('W2', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('X2', 'Tgl Pakta Integritas');
                $sheet->setCellValue('Y2', 'NIK');
                $sheet->setCellValue('Z2', 'NPWP');
                $sheet->setCellValue('AA2', 'PKP');
                $sheet->setCellValue('AB2', 'NON PKP');
                $sheet->setCellValue('AC2', 'Keterangan PKP');
                $sheet->setCellValue('AD2', 'Email');
                $sheet->setCellValue('AE2', 'NO VMS');
                $sheet->setCellValue('AF2', 'KODE MITRA');
                
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
                $sheet->setCellValue('M3', 'Nama Yang Dikuasakan');
                $sheet->setCellValue('N3', 'NIK Yang Dikuasakan');
                $sheet->setCellValue('O3', 'Alamat Yang Dikuasakan');
                $sheet->setCellValue('P3', 'NOMOR REKENING');
                $sheet->setCellValue('Q3', 'NAMA PEMILIK REKENING');
                $sheet->setCellValue('R3', 'Status Perusahaan');
                $sheet->setCellValue('S3', 'Tanggal Seleksi');
                $sheet->setCellValue('T3', 'Tanggal Klasifikasi');
                $sheet->setCellValue('U3', 'Tanggal Penilaian');
                $sheet->setCellValue('V3', 'Tanggal Penetapan');
                $sheet->setCellValue('W3', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('X3', 'Tgl Pakta Integritas');
                $sheet->setCellValue('Y3', 'NIK');
                $sheet->setCellValue('Z3', 'NPWP');
                $sheet->setCellValue('AA3', 'PKP');
                $sheet->setCellValue('AB3', 'NON PKP');
                $sheet->setCellValue('AC3', 'Keterangan PKP');
                $sheet->setCellValue('AD3', 'Email');
                $sheet->setCellValue('AE3', 'NO VMS');
                $sheet->setCellValue('AF3', 'KODE MITRA');
                
                // Merge cells for main headers
                $sheet->mergeCells('A1:A3'); // No
                $sheet->mergeCells('B1:Q1'); // DATA CALON & MITRA KERJA (span semua kolom data calon termasuk data kuasa)
                $sheet->mergeCells('R1:R3'); // Status Perusahaan
                $sheet->mergeCells('S1:S3'); // Tanggal Seleksi
                $sheet->mergeCells('T1:T3'); // Tanggal Klasifikasi
                $sheet->mergeCells('U1:U3'); // Tanggal Penilaian
                $sheet->mergeCells('V1:V3'); // Tanggal Penetapan
                $sheet->mergeCells('W1:W3'); // Tanggal Surat Permohonan
                $sheet->mergeCells('X1:X3'); // Tgl Pakta Integritas
                $sheet->mergeCells('Y1:Y3'); // NIK
                $sheet->mergeCells('Z1:Z3'); // NPWP
                $sheet->mergeCells('AA1:AC1'); // PENGUSAHA KENA PAJAK
                $sheet->mergeCells('AD1:AD3'); // Email
                $sheet->mergeCells('AE1:AE3'); // NO VMS
                $sheet->mergeCells('AF1:AF3'); // KODE MITRA
                
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
                $sheet->mergeCells('K2:O2'); // SURAT KUASA (span 5 kolom: Ada/Tidak Ada, Keterangan, Nama, NIK, Alamat)
                $sheet->mergeCells('P2:P3'); // NOMOR REKENING
                $sheet->mergeCells('Q2:Q3'); // NAMA PEMILIK REKENING
                
                // Merge cells for PENGUSAHA KENA PAJAK sub headers
                $sheet->mergeCells('AA2:AA3'); // PKP
                $sheet->mergeCells('AB2:AB3'); // NON PKP
                $sheet->mergeCells('AC2:AC3'); // Keterangan PKP
                
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
                
                $sheet->getStyle('A1:AF3')->applyFromArray($headerStyle);
                
                // Set row height for headers
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(30);
                $sheet->getRowDimension(3)->setRowHeight(30);
                
                // Get highest row
                $highestRow = $sheet->getHighestRow();
                
                // Apply borders to all data
                $sheet->getStyle('A1:AF' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Center align untuk kolom tertentu (data mulai dari row 4)
                $sheet->getStyle('A4:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K4:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Surat Kuasa (Ada/Tidak, Ket)
                $sheet->getStyle('N4:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NIK Yang Dikuasakan
                $sheet->getStyle('R4:R' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // (was O, shifted by 3)
                $sheet->getStyle('S4:X' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // (was P:U, shifted by 3)
                $sheet->getStyle('AA4:AB' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // PKP, NON PKP (was X:Y, shifted by 3)
                
                // Format kolom sebagai TEXT untuk mencegah scientific notation
                $textColumns = ['E', 'H', 'N', 'P', 'Y', 'Z']; // No Telp Perusahaan, No Telp CP, NIK Kuasa, No Rekening, NIK, NPWP (M->P, V->Y, W->Z)
                
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
