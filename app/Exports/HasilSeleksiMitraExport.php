<?php

namespace App\Exports;

use App\Models\HasilSeleksiMitra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HasilSeleksiMitraExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
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
            'Nama Perusahaan',
            'Tanggal Evaluasi',
            // Dokumen Perizinan
            'Surat Permohonan',
            'Akta Notaris',
            'NIB',
            'KTP',
            'No Rekening',
            'NPWP',
            'Surat Kuasa',
            'Kesimpulan Dokumen',
            // Sarana Pengeringan
            'Lantai Jemur',
            'Sarana Lainnya',
            'Kesimpulan Sarana Pengeringan',
            // Sarana Penggilingan
            'Mesin Memecah Kulit',
            'Mesin Pemisah Gabah dengan Beras',
            'Mesin Penyosoh',
            'Alat Pemisah Beras',
            'Kesimpulan Sarana Penggilingan',
            // Kesimpulan Akhir
            'Kesimpulan Akhir',
            // Detail Arrays (sebagai string)
            'Dokumen Valid',
            'Dokumen Tidak Ada',
            'Sarana Pengeringan Ada',
            'Sarana Pengeringan Tidak Ada',
            'Sarana Penggilingan Ada',
            'Sarana Penggilingan Tidak Ada',
        ];
    }

    public function map($hasilSeleksi): array
    {
        return [
            $hasilSeleksi->mitra->nama_perusahaan ?? '-',
            $hasilSeleksi->created_at ? date('d-m-Y H:i', strtotime($hasilSeleksi->created_at)) : '-',
            // Dokumen Perizinan
            $hasilSeleksi->surat_permohonan ?? '-',
            $hasilSeleksi->akta_notaris ?? '-',
            $hasilSeleksi->nib ?? '-',
            $hasilSeleksi->ktp ?? '-',
            $hasilSeleksi->no_rekening ?? '-',
            $hasilSeleksi->npwp ?? '-',
            $hasilSeleksi->surat_kuasa ?? '-',
            $hasilSeleksi->kesimpulan_dokumen ?? '-',
            // Sarana Pengeringan
            $hasilSeleksi->lantai_jemur ?? '-',
            $hasilSeleksi->sarana_lainnya ?? '-',
            $hasilSeleksi->kesimpulan_sarana_pengeringan ?? '-',
            // Sarana Penggilingan
            $hasilSeleksi->mesin_memecah_kulit ?? '-',
            $hasilSeleksi->mesin_pemisah_gabah_dengan_beras ?? '-',
            $hasilSeleksi->mesin_penyosoh ?? '-',
            $hasilSeleksi->alat_pemisah_beras ?? '-',
            $hasilSeleksi->kesimpulan_sarana_penggilingan ?? '-',
            // Kesimpulan Akhir
            $hasilSeleksi->kesimpulan_akhir ?? '-',
            // Detail Arrays (convert to string)
            $this->arrayToString($hasilSeleksi->dokumen_ada_valid),
            $this->arrayToString($hasilSeleksi->dokumen_tidak_ada),
            $this->arrayToString($hasilSeleksi->sarana_pengeringan_ada),
            $this->arrayToString($hasilSeleksi->sarana_pengeringan_tidak_ada),
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
            'A' => 30, // Nama Perusahaan
            'B' => 20, // Tanggal Evaluasi
            'C' => 18, // Surat Permohonan
            'D' => 18, // Akta Notaris
            'E' => 15, // NIB
            'F' => 15, // KTP
            'G' => 18, // No Rekening
            'H' => 15, // NPWP
            'I' => 18, // Surat Kuasa
            'J' => 20, // Kesimpulan Dokumen
            'K' => 18, // Lantai Jemur
            'L' => 18, // Sarana Lainnya
            'M' => 25, // Kesimpulan Sarana Pengeringan
            'N' => 25, // Mesin Memecah Kulit
            'O' => 30, // Mesin Pemisah Gabah dengan Beras
            'P' => 20, // Mesin Penyosoh
            'Q' => 25, // Alat Pemisah Beras
            'R' => 25, // Kesimpulan Sarana Penggilingan
            'S' => 20, // Kesimpulan Akhir
            'T' => 40, // Dokumen Valid
            'U' => 40, // Dokumen Tidak Ada
            'V' => 40, // Sarana Pengeringan Ada
            'W' => 40, // Sarana Pengeringan Tidak Ada
            'X' => 40, // Sarana Penggilingan Ada
            'Y' => 40, // Sarana Penggilingan Tidak Ada
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FED7AA'] // Orange light color
                ]
            ],
        ];
    }
}
