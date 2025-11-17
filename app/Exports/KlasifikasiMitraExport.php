<?php

namespace App\Exports;

use App\Models\KlasifikasiMitra;
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

class KlasifikasiMitraExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    protected $rowNumber = 0;
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

        return KlasifikasiMitra::with('mitra')->get();
    }

    public function headings(): array
    {
        return [
            ['No', 'Nama MKP', 'Pengeringan', '', '', '', 'Penggilingan', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Sarana Penyimpanan', '', 'Sarana Angkutan', 'Kelengkapan Pemeriksaan', '', '', 'Organisasi', 'Hasil Klasifikasi'],
            ['', '', 'Mesin Pembersih Gabah', 'Lantai Jemur', 'Mesin Pengering', 'Alat Pengering Lainnya', 'Mesin Pembersih Awal', 'Mesin Pemecah Kulit', 'Mesin Pembersih Sekam', 'Mesin Pemisah Gabah Pecah Kulit', 'Mesin Pemisah Batu', 'Mesin Penyosoh', 'Mesin Pengkabut', 'Mesin Pemesah Menir', 'Mesin Pemisah Katul', 'Mesin Pemisah Berdasarkan Ukuran', 'Mesin Pemisah Berdasarkan Warna', 'Tangki Penyimpanan', 'Mesin Pengemas', 'Mesin Jahit', 'Gudang Konvensional', 'Silo GKG Hopper', 'Truk', 'Mini Lab', 'Moisture Tester', 'Pembanding Derajat Sosoh', 'Bagian Quality Control', '']
        ];
    }

    public function map($klasifikasi): array
    {
        if ($this->isTemplate) {
            return [];
        }

        $this->rowNumber++;

        return [
            $this->rowNumber,
            $klasifikasi->mitra->nama_perusahaan ?? '',
            $this->getDescription('mesin_pembersih_gabah', $klasifikasi->mesin_pembersih_gabah),
            $this->getDescription('lantai_jemur', $klasifikasi->lantai_jemur),
            $this->getDescription('mesin_pengering', $klasifikasi->mesin_pengering),
            $this->getDescription('alat_pengering_lainnya', $klasifikasi->alat_pengering_lainnya),
            $this->getDescription('mesin_pembersih_awal', $klasifikasi->mesin_pembersih_awal),
            $this->getDescription('mesin_pemecah_kulit', $klasifikasi->mesin_pemecah_kulit),
            $this->getDescription('mesin_pembersih_sekam', $klasifikasi->mesin_pembersih_sekam),
            $this->getDescription('mesin_pemisah_gabah_pecah_kulit', $klasifikasi->mesin_pemisah_gabah_pecah_kulit),
            $this->getDescription('mesin_pemisah_batu', $klasifikasi->mesin_pemisah_batu),
            $this->getDescription('mesin_penyosoh', $klasifikasi->mesin_penyosoh),
            $this->getDescription('mesin_pengkabut', $klasifikasi->mesin_pengkabut),
            $this->getDescription('mesin_pemesah_menir', $klasifikasi->mesin_pemesah_menir),
            $this->getDescription('mesin_pemisah_katul', $klasifikasi->mesin_pemisah_katul),
            $this->getDescription('mesin_pemisah_berdasarkan_ukuran', $klasifikasi->mesin_pemisah_berdasarkan_ukuran),
            $this->getDescription('mesin_pemisah_berdasarkan_warna', $klasifikasi->mesin_pemisah_berdasarkan_warna),
            $this->getDescription('tangki_penyimpanan', $klasifikasi->tangki_penyimpanan),
            $this->getDescription('mesin_pengemas', $klasifikasi->mesin_pengemas),
            $this->getDescription('mesin_jahit', $klasifikasi->mesin_jahit),
            $this->getDescription('gudang_konvensional', $klasifikasi->gudang_konvensional),
            $this->getDescription('silo_gkg_hopper', $klasifikasi->silo_gkg_hopper),
            $this->getDescription('truk', $klasifikasi->truk),
            $this->getDescription('mini_lab', $klasifikasi->mini_lab),
            $this->getDescription('moisture_tester', $klasifikasi->moisture_tester),
            $this->getDescription('pembanding_derajat_sosoh', $klasifikasi->pembanding_derajat_sosoh),
            $this->getDescription('bagian_quality_control', $klasifikasi->bagian_quality_control),
            $klasifikasi->hasil_klasifikasi,
        ];
    }

    /**
     * Get description for each criteria based on value
     */
    private function getDescription($field, $value)
    {
        if (empty($value)) {
            return '-';
        }

        $descriptions = [
            'mesin_pembersih_gabah' => [
                '1' => 'Ada | > 20',
                '2' => 'Ada | ≤ 20',
                '3' => 'Tidak Ada'
            ],
            'lantai_jemur' => [
                '1' => 'Ada | > 10',
                '2' => 'Ada | 1 s/d 10',
                '3' => 'Tidak Ada'
            ],
            'mesin_pengering' => [
                '1' => 'Ada | > 20',
                '2' => 'Ada | ≤ 20',
                '3' => 'Tidak Ada'
            ],
            'alat_pengering_lainnya' => [
                '1' => 'Tidak Disyaratkan',
                '2' => 'Tidak Disyaratkan',
                '3' => 'Ada | ≤ 1'
            ],
            'mesin_pembersih_awal' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_pemecah_kulit' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_pembersih_sekam' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_pemisah_gabah_pecah_kulit' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_pemisah_batu' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_penyosoh' => [
                '1' => 'Ada | > 3 | 2 pass',
                '2' => 'Ada | 1 s/d 3 | 1 pass',
                '3' => 'Tidak Ada'
            ],
            'mesin_pengkabut' => [
                '1' => 'Ada | > 3 | 2 pass',
                '2' => 'Ada | 1 s/d 3 | 1 pass',
                '3' => 'Tidak Ada'
            ],
            'mesin_pemesah_menir' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_pemisah_katul' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_pemisah_berdasarkan_ukuran' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'mesin_pemisah_berdasarkan_warna' => [
                '1' => 'Ada | > 3',
                '2' => 'Ada | 1 s/d 3',
                '3' => 'Tidak Ada'
            ],
            'tangki_penyimpanan' => [
                '1' => 'Ada | > 10',
                '2' => 'Ada | ≤ 10',
                '3' => 'Tidak Ada'
            ],
            'mesin_pengemas' => [
                '1' => 'Ada | Full Otomatis',
                '2' => 'Ada | Semi Otomatis',
                '3' => 'Tidak Ada'
            ],
            'mesin_jahit' => [
                '1' => 'Ada | Full Otomatis',
                '2' => 'Ada | Semi Otomatis',
                '3' => 'Tidak Ada'
            ],
            'gudang_konvensional' => [
                '1' => 'Ada | > 3000',
                '2' => 'Ada | < 3000',
                '3' => 'Tidak Ada'
            ],
            'silo_gkg_hopper' => [
                '1' => 'Ada | > 2000',
                '2' => 'Ada | < 2000',
                '3' => 'Tidak Ada'
            ],
            'truk' => [
                '1' => 'Ada | > 5',
                '2' => 'Ada | ≤ 5',
                '3' => 'Tidak Ada'
            ],
            'mini_lab' => [
                '1' => 'Ada',
                '2' => 'Ada | Tidak Lengkap',
                '3' => 'Tidak Ada'
            ],
            'moisture_tester' => [
                '1' => 'Ada | Digital',
                '2' => 'Ada | Manual',
                '3' => 'Tidak Ada'
            ],
            'pembanding_derajat_sosoh' => [
                '1' => 'Ada',
                '2' => 'Ada | Tidak Lengkap',
                '3' => 'Tidak Ada'
            ],
            'bagian_quality_control' => [
                '1' => 'Ada | Tidak Merangkap',
                '2' => 'Ada | Merangkap',
                '3' => 'Tidak Ada'
            ],
        ];

        return $descriptions[$field][$value] ?? $value;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  // No
            'B' => 30, // Nama Perusahaan
            'C' => 25, // Mesin Pembersih Gabah
            'D' => 20, // Lantai Jemur
            'E' => 20, // Mesin Pengering
            'F' => 25, // Alat Pengering Lainnya
            'G' => 25, // Mesin Pembersih Awal
            'H' => 25, // Mesin Pemecah Kulit
            'I' => 25, // Mesin Pembersih Sekam
            'J' => 30, // Mesin Pemisah Gabah Pecah Kulit
            'K' => 25, // Mesin Pemisah Batu
            'L' => 20, // Mesin Penyosoh
            'M' => 20, // Mesin Pengkabut
            'N' => 25, // Mesin Pemesah Menir
            'O' => 25, // Mesin Pemisah Katul
            'P' => 30, // Mesin Pemisah Berdasarkan Ukuran
            'Q' => 30, // Mesin Pemisah Berdasarkan Warna
            'R' => 25, // Tangki Penyimpanan
            'S' => 20, // Mesin Pengemas
            'T' => 20, // Mesin Jahit
            'U' => 25, // Gudang Konvensional
            'V' => 20, // Silo GKG Hopper
            'W' => 15, // Truk
            'X' => 15, // Mini Lab
            'Y' => 20, // Moisture Tester
            'Z' => 25, // Pembanding Derajat Sosoh
            'AA' => 25, // Bagian Quality Control
            'AB' => 20, // Hasil Klasifikasi
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for main headers (row 1)
        $sheet->mergeCells('A1:A2'); // No
        $sheet->mergeCells('B1:B2'); // Nama MKP
        $sheet->mergeCells('C1:F1'); // Pengeringan
        $sheet->mergeCells('G1:T1'); // Penggilingan
        $sheet->mergeCells('U1:V1'); // Sarana Penyimpanan
        $sheet->mergeCells('X1:Z1'); // Kelengkapan Pemeriksaan
        $sheet->mergeCells('AB1:AB2'); // Hasil Klasifikasi

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

                // Warna biru muda (Light Blue) untuk: No, Nama MKP, Pengeringan
                // Row 1
                $sheet->getStyle('A1:B1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue
                    ],
                ]);
                $sheet->getStyle('C1:F1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue - Pengeringan
                    ],
                ]);

                // Row 2 - Sub header Pengeringan
                $sheet->getStyle('C2:F2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue
                    ],
                ]);

                // Warna biru muda untuk: Sarana Penyimpanan
                $sheet->getStyle('U1:V1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue
                    ],
                ]);
                $sheet->getStyle('U2:V2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue
                    ],
                ]);

                // Warna biru muda untuk: Kelengkapan Pemeriksaan
                $sheet->getStyle('X1:Z1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue
                    ],
                ]);
                $sheet->getStyle('X2:Z2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'], // Light Blue
                    ],
                ]);

                // Warna Red Accent 2 Lighter 80% untuk: Penggilingan, Sarana Angkutan, Organisasi, Hasil Klasifikasi
                $sheet->getStyle('G1:T1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'], // Red Accent 2 Lighter 80%
                    ],
                ]);
                $sheet->getStyle('G2:T2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'], // Red Accent 2 Lighter 80%
                    ],
                ]);

                $sheet->getStyle('W1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'], // Red Accent 2 Lighter 80%
                    ],
                ]);
                $sheet->getStyle('W2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'], // Red Accent 2 Lighter 80%
                    ],
                ]);

                $sheet->getStyle('AA1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'], // Red Accent 2 Lighter 80%
                    ],
                ]);
                $sheet->getStyle('AA2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'], // Red Accent 2 Lighter 80%
                    ],
                ]);

                $sheet->getStyle('AB1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4CCCC'], // Red Accent 2 Lighter 80%
                    ],
                ]);
            },
        ];
    }
}
