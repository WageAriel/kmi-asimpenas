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
        // Order by kota_kabupaten ascending, kemudian created_at ascending
        return DataMitra::with('user')
            ->orderBy('kota_kabupaten', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
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
            $mitra->kota_kabupaten ?? '', // Kabupaten/Kota
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
                $sheet->setCellValue('S1', 'Status Perusahaan');
                $sheet->setCellValue('T1', 'Tanggal Seleksi');
                $sheet->setCellValue('U1', 'Tanggal Klasifikasi');
                $sheet->setCellValue('V1', 'Tanggal Penilaian');
                $sheet->setCellValue('W1', 'Tanggal Penetapan');
                $sheet->setCellValue('X1', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('Y1', 'Tgl Pakta Integritas');
                $sheet->setCellValue('Z1', 'NIK');
                $sheet->setCellValue('AA1', 'NPWP');
                $sheet->setCellValue('AB1', 'PENGUSAHA KENA PAJAK');
                $sheet->setCellValue('AE1', 'Email');
                $sheet->setCellValue('AF1', 'NO VMS');
                $sheet->setCellValue('AG1', 'KODE MITRA');
                
                // HEADER ROW 2 (Sub Headers Level 1)
                $sheet->setCellValue('A2', 'No');
                $sheet->setCellValue('B2', 'NAMA PERUSAHAAN');
                $sheet->setCellValue('C2', 'BADAN HUKUM/USAHA');
                $sheet->setCellValue('D2', 'ALAMAT PERUSAHAAN');
                $sheet->setCellValue('E2', 'KABUPATEN/KOTA');
                $sheet->setCellValue('F2', 'NOMOR TELP PERUSAHAAN');
                $sheet->setCellValue('G2', 'NAMA KONTAK PERSON');
                $sheet->setCellValue('H2', 'JABATAN');
                $sheet->setCellValue('I2', 'NOMOR TELP/HP CONTACT PERSON');
                $sheet->setCellValue('J2', 'BANK KORESPONDENSI');
                $sheet->setCellValue('K2', 'ALAMAT BANK KORESPONDENSI');
                $sheet->setCellValue('L2', 'SURAT KUASA');
                $sheet->setCellValue('Q2', 'NOMOR REKENING');
                $sheet->setCellValue('R2', 'NAMA PEMILIK REKENING');
                $sheet->setCellValue('S2', 'Status Perusahaan');
                $sheet->setCellValue('T2', 'Tanggal Seleksi');
                $sheet->setCellValue('U2', 'Tanggal Klasifikasi');
                $sheet->setCellValue('V2', 'Tanggal Penilaian');
                $sheet->setCellValue('W2', 'Tanggal Penetapan');
                $sheet->setCellValue('X2', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('Y2', 'Tgl Pakta Integritas');
                $sheet->setCellValue('Z2', 'NIK');
                $sheet->setCellValue('AA2', 'NPWP');
                $sheet->setCellValue('AB2', 'PKP');
                $sheet->setCellValue('AC2', 'NON PKP');
                $sheet->setCellValue('AD2', 'Keterangan PKP');
                $sheet->setCellValue('AE2', 'Email');
                $sheet->setCellValue('AF2', 'NO VMS');
                $sheet->setCellValue('AG2', 'KODE MITRA');
                
                // HEADER ROW 3 (Sub Headers Level 2 - hanya untuk Surat Kuasa)
                $sheet->setCellValue('A3', 'No');
                $sheet->setCellValue('B3', 'NAMA PERUSAHAAN');
                $sheet->setCellValue('C3', 'BADAN HUKUM/USAHA');
                $sheet->setCellValue('D3', 'ALAMAT PERUSAHAAN');
                $sheet->setCellValue('E3', 'KABUPATEN/KOTA');
                $sheet->setCellValue('F3', 'NOMOR TELP PERUSAHAAN');
                $sheet->setCellValue('G3', 'NAMA KONTAK PERSON');
                $sheet->setCellValue('H3', 'JABATAN');
                $sheet->setCellValue('I3', 'NOMOR TELP/HP CONTACT PERSON');
                $sheet->setCellValue('J3', 'BANK KORESPONDENSI');
                $sheet->setCellValue('K3', 'ALAMAT BANK KORESPONDENSI');
                $sheet->setCellValue('L3', 'Ada/Tidak Ada');
                $sheet->setCellValue('M3', 'Keterangan');
                $sheet->setCellValue('N3', 'Nama Yang Dikuasakan');
                $sheet->setCellValue('O3', 'NIK Yang Dikuasakan');
                $sheet->setCellValue('P3', 'Alamat Yang Dikuasakan');
                $sheet->setCellValue('Q3', 'NOMOR REKENING');
                $sheet->setCellValue('R3', 'NAMA PEMILIK REKENING');
                $sheet->setCellValue('S3', 'Status Perusahaan');
                $sheet->setCellValue('T3', 'Tanggal Seleksi');
                $sheet->setCellValue('U3', 'Tanggal Klasifikasi');
                $sheet->setCellValue('V3', 'Tanggal Penilaian');
                $sheet->setCellValue('W3', 'Tanggal Penetapan');
                $sheet->setCellValue('X3', 'Tanggal Surat Permohonan');
                $sheet->setCellValue('Y3', 'Tgl Pakta Integritas');
                $sheet->setCellValue('Z3', 'NIK');
                $sheet->setCellValue('AA3', 'NPWP');
                $sheet->setCellValue('AB3', 'PKP');
                $sheet->setCellValue('AC3', 'NON PKP');
                $sheet->setCellValue('AD3', 'Keterangan PKP');
                $sheet->setCellValue('AE3', 'Email');
                $sheet->setCellValue('AF3', 'NO VMS');
                $sheet->setCellValue('AG3', 'KODE MITRA');
                
                // Merge cells for main headers
                $sheet->mergeCells('A1:A3'); // No
                $sheet->mergeCells('B1:R1'); // DATA CALON & MITRA KERJA (span semua kolom data calon termasuk data kuasa)
                $sheet->mergeCells('S1:S3'); // Status Perusahaan
                $sheet->mergeCells('T1:T3'); // Tanggal Seleksi
                $sheet->mergeCells('U1:U3'); // Tanggal Klasifikasi
                $sheet->mergeCells('V1:V3'); // Tanggal Penilaian
                $sheet->mergeCells('W1:W3'); // Tanggal Penetapan
                $sheet->mergeCells('X1:X3'); // Tanggal Surat Permohonan
                $sheet->mergeCells('Y1:Y3'); // Tgl Pakta Integritas
                $sheet->mergeCells('Z1:Z3'); // NIK
                $sheet->mergeCells('AA1:AA3'); // NPWP
                $sheet->mergeCells('AB1:AD1'); // PENGUSAHA KENA PAJAK
                $sheet->mergeCells('AE1:AE3'); // Email
                $sheet->mergeCells('AF1:AF3'); // NO VMS
                $sheet->mergeCells('AG1:AG3'); // KODE MITRA
                
                // Merge cells for sub headers level 1 (Row 2)
                $sheet->mergeCells('B2:B3'); // NAMA PERUSAHAAN
                $sheet->mergeCells('C2:C3'); // BADAN HUKUM/USAHA
                $sheet->mergeCells('D2:D3'); // ALAMAT PERUSAHAAN
                $sheet->mergeCells('E2:E3'); // KABUPATEN/KOTA
                $sheet->mergeCells('F2:F3'); // NOMOR TELP PERUSAHAAN
                $sheet->mergeCells('G2:G3'); // NAMA KONTAK PERSON
                $sheet->mergeCells('H2:H3'); // JABATAN
                $sheet->mergeCells('I2:I3'); // NOMOR TELP/HP CONTACT PERSON
                $sheet->mergeCells('J2:J3'); // BANK KORESPONDENSI
                $sheet->mergeCells('K2:K3'); // ALAMAT BANK KORESPONDENSI
                $sheet->mergeCells('L2:P2'); // SURAT KUASA (span 5 kolom: Ada/Tidak Ada, Keterangan, Nama, NIK, Alamat)
                $sheet->mergeCells('Q2:Q3'); // NOMOR REKENING
                $sheet->mergeCells('R2:R3'); // NAMA PEMILIK REKENING
                
                // Merge cells for PENGUSAHA KENA PAJAK sub headers
                $sheet->mergeCells('AB2:AB3'); // PKP
                $sheet->mergeCells('AC2:AC3'); // NON PKP
                $sheet->mergeCells('AD2:AD3'); // Keterangan PKP
                
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
                
                $sheet->getStyle('A1:AG3')->applyFromArray($headerStyle);
                
                // Set row height for headers
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(30);
                $sheet->getRowDimension(3)->setRowHeight(30);
                
                // Get highest row
                $highestRow = $sheet->getHighestRow();
                
                // Apply borders to all data
                $sheet->getStyle('A1:AG' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Center align untuk kolom tertentu (data mulai dari row 4)
                $sheet->getStyle('A4:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L4:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Surat Kuasa (Ada/Tidak, Ket)
                $sheet->getStyle('O4:O' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NIK Yang Dikuasakan
                $sheet->getStyle('S4:S' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status Perusahaan
                $sheet->getStyle('T4:Y' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal-tanggal
                $sheet->getStyle('AB4:AC' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // PKP, NON PKP
                
                // Format kolom sebagai TEXT untuk mencegah scientific notation
                $textColumns = ['F', 'I', 'O', 'Q', 'Z', 'AA']; // No Telp Perusahaan, No Telp CP, NIK Kuasa, No Rekening, NIK, NPWP
                
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
