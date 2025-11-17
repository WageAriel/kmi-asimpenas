<?php

namespace App\Exports;

use App\Models\DataSeleksiMitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DataSeleksiMitraExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    protected $isTemplate;

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->isTemplate) {
            // Return empty collection for template
            return collect([]);
        }

        return DataSeleksiMitra::with('mitra')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Perusahaan',
            'Surat Permohonan', '', // Merge untuk 2 kolom
            'Akta Notaris', '',
            'NIB', '',
            'KTP', '',
            'No Rekening', '',
            'NPWP', '',
            'Surat Kuasa', '',
            'Persyaratan Teknis - Sarana Pengeringan', '', // Merge untuk Lantai Jemur dan Sarana Lainnya
            'Persyaratan Teknis - Sarana Penggilingan', '', '', '', // Merge untuk 4 jenis mesin
        ];
    }

    public function map($seleksi): array
    {
        if ($this->isTemplate) {
            return [];
        }

        // Helper function untuk format enum
        $formatEnum = function($value) {
            if ($value === 'Ada') {
                return '1. Ada';
            } elseif ($value === 'Tidak Ada') {
                return '2. Tidak Ada';
            }
            return $value;
        };

        // Helper function untuk format tanggal ke format "03-Mar-25" (tahun 2 digit)
        $formatDate = function($date) {
            if (!$date) return '';
            return date('d-M-y', strtotime($date)); // 'y' untuk tahun 2 digit
        };

        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber, // Kolom No
            $seleksi->mitra->nama_perusahaan ?? '',
            // Surat Permohonan - Ada/Tidak Ada | Masa Berlaku
            $formatEnum($seleksi->surat_permohonan),
            $seleksi->mb_surat_permohonan ? $formatDate($seleksi->mb_surat_permohonan) : '',
            // Akta Notaris - Ada/Tidak Ada | Masa Berlaku
            $formatEnum($seleksi->akta_notaris),
            $seleksi->mb_akta_notaris ? $formatDate($seleksi->mb_akta_notaris) : '',
            // NIB - Ada/Tidak Ada | Masa Berlaku
            $formatEnum($seleksi->nib),
            $seleksi->mb_nib ? $formatDate($seleksi->mb_nib) : '',
            // KTP - Ada/Tidak Ada | Masa Berlaku
            $formatEnum($seleksi->ktp),
            $seleksi->mb_ktp === 'Seumur Hidup' ? 'Seumur Hidup' : ($seleksi->mb_ktp ? $formatDate($seleksi->mb_ktp) : ''),
            // No Rekening - Ada/Tidak Ada | Masa Berlaku
            $formatEnum($seleksi->no_rekening),
            $seleksi->mb_no_rekening ? $formatDate($seleksi->mb_no_rekening) : '',
            // NPWP - Ada/Tidak Ada | Masa Berlaku
            $formatEnum($seleksi->npwp),
            $seleksi->mb_npwp ? $formatDate($seleksi->mb_npwp) : '',
            // Surat Kuasa - Ada/Tidak Ada | Masa Berlaku
            $formatEnum($seleksi->surat_kuasa),
            $seleksi->mb_surat_kuasa ? $formatDate($seleksi->mb_surat_kuasa) : '',
            // Lantai Jemur - Ada/Tidak Ada only
            $formatEnum($seleksi->lantai_jemur),
            // Sarana Lainnya - Ada/Tidak Ada only
            $formatEnum($seleksi->sarana_lainnya),
            // Mesin Memecah Kulit - Ada/Tidak Ada only
            $formatEnum($seleksi->mesin_memecah_kulit),
            // Mesin Pemisah Gabah - Ada/Tidak Ada only
            $formatEnum($seleksi->mesin_pemisah_gabah),
            // Mesin Penyosoh - Ada/Tidak Ada only
            $formatEnum($seleksi->mesin_penyosoh),
            // Alat Pemisah Beras - Ada/Tidak Ada only
            $formatEnum($seleksi->alat_pemisah_beras),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // No
            'B' => 30, // Nama Perusahaan
            'C' => 15, // Surat Permohonan - Ada/Tidak Ada
            'D' => 15, // Surat Permohonan - Masa Berlaku
            'E' => 15, // Akta Notaris - Ada/Tidak Ada
            'F' => 15, // Akta Notaris - Masa Berlaku
            'G' => 15, // NIB - Ada/Tidak Ada
            'H' => 15, // NIB - Masa Berlaku
            'I' => 15, // KTP - Ada/Tidak Ada
            'J' => 15, // KTP - Masa Berlaku
            'K' => 15, // No Rekening - Ada/Tidak Ada
            'L' => 15, // No Rekening - Masa Berlaku
            'M' => 15, // NPWP - Ada/Tidak Ada
            'N' => 15, // NPWP - Masa Berlaku
            'O' => 15, // Surat Kuasa - Ada/Tidak Ada
            'P' => 15, // Surat Kuasa - Masa Berlaku
            'Q' => 20, // Lantai Jemur
            'R' => 20, // Sarana Lainnya
            'S' => 25, // Mesin Memecah Kulit
            'T' => 25, // Mesin Pemisah Gabah
            'U' => 20, // Mesin Penyosoh
            'V' => 20, // Alat Pemisah Beras
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ],
            // 2 => [
            //     'font' => ['bold' => true, 'size' => 10],
            //     'fill' => [
            //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            //         'startColor' => ['rgb' => 'F1F5F9']
            //     ],
            //     'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            // ],
            // 3 => [
            //     'font' => ['bold' => true, 'size' => 9],
            //     'fill' => [
            //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            //         'startColor' => ['rgb' => 'F3F4F6']
            //     ],
            //     'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            // ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert 2 rows untuk sub-header
                $sheet->insertNewRowBefore(2, 1);
                
                // ROW 1: Header utama
                // Sudah diset oleh headings()
                
                // ROW 2: Sub-header level 2
                $sheet->setCellValue('A2', 'No');
                $sheet->setCellValue('B2', 'Nama Perusahaan');
                // Persyaratan Administratif
                // Persyaratan Teknis - Sarana Pengeringan
                $sheet->setCellValue('Q2', 'Lantai Jemur');
                $sheet->setCellValue('R2', 'Sarana Lainnya');
                // Persyaratan Teknis - Sarana Penggilingan
                $sheet->setCellValue('S2', 'Mesin Memecah Kulit');
                $sheet->setCellValue('T2', 'Mesin Pemisah Gabah');
                $sheet->setCellValue('U2', 'Mesin Penyosoh');
                $sheet->setCellValue('V2', 'Alat Pemisah Beras');
                
                // ROW 3: Sub-header level 3 (Ada/Tidak Ada & Masa Berlaku)
                // Surat Permohonan (C-D)
                $sheet->setCellValue('C2', 'Ada/Tidak Ada');
                $sheet->setCellValue('D2', 'Masa Berlaku');
                // Akta Notaris (E-F)
                $sheet->setCellValue('E2', 'Ada/Tidak Ada');
                $sheet->setCellValue('F2', 'Masa Berlaku');
                // NIB (G-H)
                $sheet->setCellValue('G2', 'Ada/Tidak Ada');
                $sheet->setCellValue('H2', 'Masa Berlaku');
                // KTP (I-J)
                $sheet->setCellValue('I2', 'Ada/Tidak Ada');
                $sheet->setCellValue('J2', 'Masa Berlaku');
                // No Rekening (K-L)
                $sheet->setCellValue('K2', 'Ada/Tidak Ada');
                $sheet->setCellValue('L2', 'Masa Berlaku');
                // NPWP (M-N)
                $sheet->setCellValue('M2', 'Ada/Tidak Ada');
                $sheet->setCellValue('N2', 'Masa Berlaku');
                // Surat Kuasa (O-P)
                $sheet->setCellValue('O2', 'Ada/Tidak Ada');
                $sheet->setCellValue('P2', 'Masa Berlaku');
                // Kriteria teknis tanpa masa berlaku (Q-V)
                
                // MERGE CELLS
                // Row 1 - Header utama
                $sheet->mergeCells('A1:A2'); // No
                $sheet->mergeCells('B1:B2'); // Nama Perusahaan
                $sheet->mergeCells('C1:D1'); // Surat Permohonan
                $sheet->mergeCells('E1:F1'); // Akta Notaris
                $sheet->mergeCells('G1:H1'); // NIB
                $sheet->mergeCells('I1:J1'); // KTP
                $sheet->mergeCells('K1:L1'); // No Rekening
                $sheet->mergeCells('M1:N1'); // NPWP
                $sheet->mergeCells('O1:P1'); // Surat Kuasa
                $sheet->mergeCells('Q1:R1'); // Persyaratan Teknis - Sarana Pengeringan
                $sheet->mergeCells('S1:V1'); // Persyaratan Teknis - Sarana Penggilingan
                
                // Row 2 - Sub-header
                // $sheet->mergeCells('Q2:Q3'); // Lantai Jemur
                // $sheet->mergeCells('R2:R3'); // Sarana Lainnya
                // $sheet->mergeCells('S2:S3'); // Mesin Memecah Kulit
                // $sheet->mergeCells('T2:T3'); // Mesin Pemisah Gabah
                // $sheet->mergeCells('U2:U3'); // Mesin Penyosoh
                // $sheet->mergeCells('V2:V3'); // Alat Pemisah Beras
                
                // STYLING
                // Style untuk A1, A2, B1, B2 - Biru Muda
                $sheet->getStyle('A1:B2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BFDBFE'] // Biru muda (blue-200)
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Style untuk C1-P1, C2-P2 - Red accent 2 Lighter 80%
                $sheet->getStyle('C1:P2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'] // Red accent 2 Lighter 80%
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Style untuk Q1-R1, Q2, R2 - Biru Muda (Sarana Pengeringan)
                $sheet->getStyle('Q1:R2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BFDBFE'] // Biru muda (blue-200)
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Style untuk S1-V1, S2-V2 - Red accent 2 Lighter 80%
                $sheet->getStyle('S1:V2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'] // Red accent 2 Lighter 80%
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Apply border untuk semua data rows (mulai dari row 3 hingga akhir)
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 3) {
                    $sheet->getStyle('A3:V' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);
                }
            },
        ];
    }
}
