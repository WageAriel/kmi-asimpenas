<?php

namespace App\Exports;

use App\Models\DataSeleksiMitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataSeleksiMitraExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
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
            'Nama Perusahaan',
            'Surat Permohonan',
            'MB Surat Permohonan',
            'Akta Notaris',
            'MB Akta Notaris',
            'NIB',
            'MB NIB',
            'KTP',
            'MB KTP',
            'No Rekening',
            'MB No Rekening',
            'NPWP',
            'MB NPWP',
            'Surat Kuasa',
            'MB Surat Kuasa',
            'Lantai Jemur',
            'Sarana Lainnya',
            'Mesin Memecah Kulit',
            'Mesin Pemisah Gabah',
            'Mesin Penyosoh',
            'Alat Pemisah Beras',
            'Status Seleksi',
        ];
    }

    public function map($seleksi): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $seleksi->mitra->nama_perusahaan ?? '',
            $seleksi->surat_permohonan,
            $seleksi->mb_surat_permohonan ? date('Y-m-d', strtotime($seleksi->mb_surat_permohonan)) : '',
            $seleksi->akta_notaris,
            $seleksi->mb_akta_notaris ? date('Y-m-d', strtotime($seleksi->mb_akta_notaris)) : '',
            $seleksi->nib,
            $seleksi->mb_nib ? date('Y-m-d', strtotime($seleksi->mb_nib)) : '',
            $seleksi->ktp,
            $seleksi->mb_ktp === 'seumur hidup' ? 'seumur hidup' : ($seleksi->mb_ktp ? date('Y-m-d', strtotime($seleksi->mb_ktp)) : ''),
            $seleksi->no_rekening,
            $seleksi->mb_no_rekening ? date('Y-m-d', strtotime($seleksi->mb_no_rekening)) : '',
            $seleksi->npwp,
            $seleksi->mb_npwp ? date('Y-m-d', strtotime($seleksi->mb_npwp)) : '',
            $seleksi->surat_kuasa,
            $seleksi->mb_surat_kuasa ? date('Y-m-d', strtotime($seleksi->mb_surat_kuasa)) : '',
            $seleksi->lantai_jemur,
            $seleksi->sarana_lainnya,
            $seleksi->mesin_memecah_kulit,
            $seleksi->mesin_pemisah_gabah,
            $seleksi->mesin_penyosoh,
            $seleksi->alat_pemisah_beras,
            $seleksi->status_seleksi,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // Nama Perusahaan
            'B' => 20, // Surat Permohonan
            'C' => 20, // MB Surat Permohonan
            'D' => 20, // Akta Notaris
            'E' => 20, // MB Akta Notaris
            'F' => 15, // NIB
            'G' => 20, // MB NIB
            'H' => 15, // KTP
            'I' => 20, // MB KTP
            'J' => 20, // No Rekening
            'K' => 20, // MB No Rekening
            'L' => 15, // NPWP
            'M' => 20, // MB NPWP
            'N' => 20, // Surat Kuasa
            'O' => 20, // MB Surat Kuasa
            'P' => 20, // Lantai Jemur
            'Q' => 20, // Sarana Lainnya
            'R' => 25, // Mesin Memecah Kulit
            'S' => 25, // Mesin Pemisah Gabah
            'T' => 20, // Mesin Penyosoh
            'U' => 25, // Alat Pemisah Beras
            'V' => 20, // Status Seleksi
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
}
