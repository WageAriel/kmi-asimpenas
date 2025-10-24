<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataMitraTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return empty array for template (or sample data)
     */
    public function array(): array
    {
        // Return sample data
        return [
            [
                'PT Mitra Sejahtera',
                'PT',
                'Jl. Contoh No. 123',
                'Jakarta Selatan',
                'DKI Jakarta',
                'Budi Santoso',
                '3201012345678901',
                'Jakarta',
                '1990-05-15',
                '021-12345678',
                '081234567890',
                'Bank BCA',
                'Jl. Bank No. 456',
                '1234567890',
                'Distributor',
                '01.234.567.8-901.000',
                'pkp',
                'ada',
                '2025-01-15',
                '2025-02-20',
                '2025-03-10',
                '2025-04-01',
                '2025-01-01',
                '2025-01-05',
                'mitra@example.com',
                'VMS001',
                'MTR001',
            ]
        ];
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'nama_perusahaan',
            'badan_hukum_usaha',
            'alamat_perusahaan',
            'kota_kabupaten',
            'provinsi',
            'nama_cp',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'no_telp_perusahaan',
            'no_telp_cp',
            'bank_korespondensi',
            'alamat_bank',
            'no_rekening',
            'status_perusahaan',
            'npwp',
            'pkp',
            'surat_kuasa',
            'tanggal_seleksi',
            'tanggal_klasifikasi',
            'tanggal_penilaian',
            'tanggal_penetapan',
            'tanggal_surat_permohonan',
            'tanggal_pakta_integritas',
            'email',
            'no_vms',
            'kode_mitra',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // nama_perusahaan
            'B' => 20, // badan_hukum_usaha
            'C' => 30, // alamat_perusahaan
            'D' => 20, // kota_kabupaten
            'E' => 20, // provinsi
            'F' => 25, // nama_cp
            'G' => 20, // nik
            'H' => 20, // tempat_lahir
            'I' => 15, // tanggal_lahir
            'J' => 18, // no_telp_perusahaan
            'K' => 18, // no_telp_cp
            'L' => 20, // bank_korespondensi
            'M' => 30, // alamat_bank
            'N' => 18, // no_rekening
            'O' => 18, // status_perusahaan
            'P' => 22, // npwp
            'Q' => 12, // pkp
            'R' => 15, // surat_kuasa
            'S' => 18, // tanggal_seleksi
            'T' => 20, // tanggal_klasifikasi
            'U' => 18, // tanggal_penilaian
            'V' => 18, // tanggal_penetapan
            'W' => 25, // tanggal_surat_permohonan
            'X' => 25, // tanggal_pakta_integritas
            'Y' => 25, // email
            'Z' => 15, // no_vms
            'AA' => 15, // kode_mitra
        ];
    }
}
