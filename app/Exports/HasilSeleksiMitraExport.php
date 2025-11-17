<?php

namespace App\Exports;

use App\Models\HasilSeleksiMitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HasilSeleksiMitraExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    protected $rowNumber = 0;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return HasilSeleksiMitra::with(['mitra', 'seleksiMitra'])->get();
    }

    public function headings(): array
    {
        return [
            ['No', 'Profil Mitra Kerja', 'Status Mitra', 'Kesimpulan Akhir', 'Persyaratan Administrasi - Dokumen', '', '', '', '', '', '', 'Persyaratan Teknis - Sarana Pengeringan', '', 'Persyaratan Teknis - Sarana Penggilingan', '', '', '', 'Syarat Administrasi - Dokumen', '', '', '', 'Syarat Teknis - Sarana Pengeringan', '', '', 'Syarat Teknis - Sarana Penggilingan', '', ''],
            ['', '', '', '', 'Surat Permohonan', 'Akta Notaris', 'NIB', 'KTP', 'No Rekening', 'NPWP', 'Surat Kuasa', 'Lantai Jemur', 'Sarana Lainnya', 'Mesin Memecah Kulit', 'Mesin Pemisah Gabah dengan Beras', 'Mesin Penyosoh', 'Alat Pemisah Beras atau Ayakan', 'Kesimpulan Dokumen', 'Dokumen Ada dan Valid', 'Dokumen Ada Tidak Valid', 'Dokumen Tidak Ada', 'Kesimpulan Pengeringan', 'Sarana Ada', 'Sarana Tidak Ada', 'Kesimpulan Penggilingan', 'Sarana Penggilingan Ada', 'Sarana Penggilingan Tidak Ada']
        ];
    }

    public function map($hasilSeleksi): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $hasilSeleksi->mitra->nama_perusahaan ?? '-',
            $hasilSeleksi->mitra->status_perusahaan ?? '-',
            $hasilSeleksi->kesimpulan_akhir ?? '-',
            // Persyaratan Administrasi - Dokumen
            $hasilSeleksi->surat_permohonan ?? '-',
            $hasilSeleksi->akta_notaris ?? '-',
            $hasilSeleksi->nib ?? '-',
            $hasilSeleksi->ktp ?? '-',
            $hasilSeleksi->no_rekening ?? '-',
            $hasilSeleksi->npwp ?? '-',
            $hasilSeleksi->surat_kuasa ?? '-',
            // Persyaratan Teknis - Sarana Pengeringan
            $hasilSeleksi->lantai_jemur ?? '-',
            $hasilSeleksi->sarana_lainnya ?? '-',
            // Persyaratan Teknis - Sarana Penggilingan
            $hasilSeleksi->mesin_memecah_kulit ?? '-',
            $hasilSeleksi->mesin_pemisah_gabah_dengan_beras ?? '-',
            $hasilSeleksi->mesin_penyosoh ?? '-',
            $hasilSeleksi->alat_pemisah_beras ?? '-',
            // Syarat Administrasi - Dokumen
            $hasilSeleksi->kesimpulan_dokumen ?? '-',
            $this->arrayToString($hasilSeleksi->dokumen_ada_valid),
            $this->arrayToString($hasilSeleksi->dokumen_ada_tidak_valid),
            $this->arrayToString($hasilSeleksi->dokumen_tidak_ada),
            // Syarat Teknis - Sarana Pengeringan
            $hasilSeleksi->kesimpulan_sarana_pengeringan ?? '-',
            $this->arrayToString($hasilSeleksi->sarana_pengeringan_ada),
            $this->arrayToString($hasilSeleksi->sarana_pengeringan_tidak_ada),
            // Syarat Teknis - Sarana Penggilingan
            $hasilSeleksi->kesimpulan_sarana_penggilingan ?? '-',
            $this->arrayToString($hasilSeleksi->sarana_penggilingan_ada),
            $this->arrayToString($hasilSeleksi->sarana_penggilingan_tidak_ada),
        ];
    }

    /**
     * Convert array to comma-separated string
     */
    private function arrayToString($array)
    {
        if (empty($array) || !is_array($array)) {
            return '-';
        }
        return implode(', ', $array);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  // No
            'B' => 30, // Profil Mitra Kerja
            'C' => 20, // Status Mitra
            'D' => 20, // Kesimpulan Akhir
            'E' => 18, // Surat Permohonan
            'F' => 18, // Akta Notaris
            'G' => 15, // NIB
            'H' => 15, // KTP
            'I' => 18, // No Rekening
            'J' => 15, // NPWP
            'K' => 18, // Surat Kuasa
            'L' => 18, // Lantai Jemur
            'M' => 18, // Sarana Lainnya
            'N' => 25, // Mesin Memecah Kulit
            'O' => 30, // Mesin Pemisah Gabah dengan Beras
            'P' => 20, // Mesin Penyosoh
            'Q' => 25, // Alat Pemisah Beras
            'R' => 20, // Kesimpulan Dokumen
            'S' => 40, // Dokumen Ada dan Valid
            'T' => 40, // Dokumen Ada Tidak Valid
            'U' => 40, // Dokumen Tidak Ada
            'V' => 25, // Kesimpulan Pengeringan
            'W' => 40, // Sarana Ada
            'X' => 40, // Sarana Tidak Ada
            'Y' => 25, // Kesimpulan Penggilingan
            'Z' => 40, // Sarana Penggilingan Ada
            'AA' => 40, // Sarana Penggilingan Tidak Ada
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for main headers (row 1)
        $sheet->mergeCells('A1:A2'); // No
        $sheet->mergeCells('B1:B2'); // Profil Mitra Kerja
        $sheet->mergeCells('C1:C2'); // Status Mitra
        $sheet->mergeCells('D1:D2'); // Kesimpulan Akhir
        $sheet->mergeCells('E1:K1'); // Persyaratan Administrasi - Dokumen
        $sheet->mergeCells('L1:M1'); // Persyaratan Teknis - Sarana Pengeringan
        $sheet->mergeCells('N1:Q1'); // Persyaratan Teknis - Sarana Penggilingan
        $sheet->mergeCells('R1:U1'); // Syarat Administrasi - Dokumen (updated to include new column)
        $sheet->mergeCells('V1:X1'); // Syarat Teknis - Sarana Pengeringan (shifted)
        $sheet->mergeCells('Y1:AA1'); // Syarat Teknis - Sarana Penggilingan (shifted)

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 10],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Apply borders to all cells
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Warna biru muda untuk: No, Profil Mitra Kerja, Status Mitra, Kesimpulan Akhir
                $sheet->getStyle('A1:D2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue
                    ],
                ]);

                // Warna hijau untuk: Persyaratan Administrasi - Dokumen
                $sheet->getStyle('E1:K1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C6E0B4'], // Green
                    ],
                ]);
                $sheet->getStyle('E2:K2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA'], // Light Green
                    ],
                ]);

                // Warna kuning untuk: Persyaratan Teknis - Sarana Pengeringan
                $sheet->getStyle('L1:M1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE699'], // Yellow
                    ],
                ]);
                $sheet->getStyle('L2:M2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2CC'], // Light Yellow
                    ],
                ]);

                // Warna orange untuk: Persyaratan Teknis - Sarana Penggilingan
                $sheet->getStyle('N1:Q1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4B084'], // Orange
                    ],
                ]);
                $sheet->getStyle('N2:Q2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FCE4D6'], // Light Orange
                    ],
                ]);

                // Warna hijau untuk: Syarat Administrasi - Dokumen (updated range)
                $sheet->getStyle('R1:U1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C5E0B4'], // Green
                    ],
                ]);
                $sheet->getStyle('R2:U2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA'], // Light Green
                    ],
                ]);

                // Warna kuning untuk: Syarat Teknis - Sarana Pengeringan (shifted)
                $sheet->getStyle('V1:X1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE699'], // Yellow
                    ],
                ]);
                $sheet->getStyle('V2:X2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2CC'], // Light Yellow
                    ],
                ]);

                // Warna orange untuk: Syarat Teknis - Sarana Penggilingan (shifted)
                $sheet->getStyle('Y1:AA1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4B084'], // Orange
                    ],
                ]);
                $sheet->getStyle('Y2:AA2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FCE4D6'], // Light Orange
                    ],
                ]);
            },
        ];
    }
}   
