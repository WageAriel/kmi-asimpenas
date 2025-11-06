<?php

namespace App\Imports;

use App\Models\KlasifikasiMitra;
use App\Models\DataMitra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class KlasifikasiMitraImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cari ID mitra berdasarkan nama perusahaan
        $mitra = DataMitra::where('nama_perusahaan', $row['nama_perusahaan'])->first();
        
        if (!$mitra) {
            throw new \Exception("Mitra dengan nama '{$row['nama_perusahaan']}' tidak ditemukan");
        }

        // Helper function untuk convert nilai ke enum (1,2,3)
        $parseEnumValue = function($value) {
            if (empty($value)) {
                return null;
            }
            
            $value = trim($value);
            return in_array($value, ['1', '2', '3']) ? $value : null;
        };

        return new KlasifikasiMitra([
            'id_mitra' => $mitra->id_mitra,
            'mesin_pembersih_gabah' => $parseEnumValue($row['mesin_pembersih_gabah'] ?? null),
            'lantai_jemur' => $parseEnumValue($row['lantai_jemur'] ?? null),
            'mesin_pengering' => $parseEnumValue($row['mesin_pengering'] ?? null),
            'alat_pengering_lainnya' => $parseEnumValue($row['alat_pengering_lainnya'] ?? null),
            'mesin_pembersih_awal' => $parseEnumValue($row['mesin_pembersih_awal'] ?? null),
            'mesin_pemecah_kulit' => $parseEnumValue($row['mesin_pemecah_kulit'] ?? null),
            'mesin_pembersih_sekam' => $parseEnumValue($row['mesin_pembersih_sekam'] ?? null),
            'mesin_pemisah_gabah_pecah_kulit' => $parseEnumValue($row['mesin_pemisah_gabah_pecah_kulit'] ?? null),
            'mesin_pemisah_batu' => $parseEnumValue($row['mesin_pemisah_batu'] ?? null),
            'mesin_penyosoh' => $parseEnumValue($row['mesin_penyosoh'] ?? null),
            'mesin_pengkabut' => $parseEnumValue($row['mesin_pengkabut'] ?? null),
            'mesin_pemesah_menir' => $parseEnumValue($row['mesin_pemesah_menir'] ?? null),
            'mesin_pemisah_katul' => $parseEnumValue($row['mesin_pemisah_katul'] ?? null),
            'mesin_pemisah_berdasarkan_ukuran' => $parseEnumValue($row['mesin_pemisah_berdasarkan_ukuran'] ?? null),
            'mesin_pemisah_berdasarkan_warna' => $parseEnumValue($row['mesin_pemisah_berdasarkan_warna'] ?? null),
            'tangki_penyimpanan' => $parseEnumValue($row['tangki_penyimpanan'] ?? null),
            'mesin_pengemas' => $parseEnumValue($row['mesin_pengemas'] ?? null),
            'mesin_jahit' => $parseEnumValue($row['mesin_jahit'] ?? null),
            'gudang_konvensional' => $parseEnumValue($row['gudang_konvensional'] ?? null),
            'silo_gkg_hopper' => $parseEnumValue($row['silo_gkg_hopper'] ?? null),
            'truk' => $parseEnumValue($row['truk'] ?? null),
            'mini_lab' => $parseEnumValue($row['mini_lab'] ?? null),
            'moisture_tester' => $parseEnumValue($row['moisture_tester'] ?? null),
            'pembanding_derajat_sosoh' => $parseEnumValue($row['pembanding_derajat_sosoh'] ?? null),
            'bagian_quality_control' => $parseEnumValue($row['bagian_quality_control'] ?? null),
            'hasil_klasifikasi' => null, // Admin yang akan set melalui interface
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string',
            'mesin_pembersih_gabah' => 'nullable|in:1,2,3',
            'lantai_jemur' => 'nullable|in:1,2,3',
            'mesin_pengering' => 'nullable|in:1,2,3',
            'alat_pengering_lainnya' => 'nullable|in:1,2,3',
            'mesin_pembersih_awal' => 'nullable|in:1,2,3',
            'mesin_pemecah_kulit' => 'nullable|in:1,2,3',
            'mesin_pembersih_sekam' => 'nullable|in:1,2,3',
            'mesin_pemisah_gabah_pecah_kulit' => 'nullable|in:1,2,3',
            'mesin_pemisah_batu' => 'nullable|in:1,2,3',
            'mesin_penyosoh' => 'nullable|in:1,2,3',
            'mesin_pengkabut' => 'nullable|in:1,2,3',
            'mesin_pemesah_menir' => 'nullable|in:1,2,3',
            'mesin_pemisah_katul' => 'nullable|in:1,2,3',
            'mesin_pemisah_berdasarkan_ukuran' => 'nullable|in:1,2,3',
            'mesin_pemisah_berdasarkan_warna' => 'nullable|in:1,2,3',
            'tangki_penyimpanan' => 'nullable|in:1,2,3',
            'mesin_pengemas' => 'nullable|in:1,2,3',
            'mesin_jahit' => 'nullable|in:1,2,3',
            'gudang_konvensional' => 'nullable|in:1,2,3',
            'silo_gkg_hopper' => 'nullable|in:1,2,3',
            'truk' => 'nullable|in:1,2,3',
            'mini_lab' => 'nullable|in:1,2,3',
            'moisture_tester' => 'nullable|in:1,2,3',
            'pembanding_derajat_sosoh' => 'nullable|in:1,2,3',
            'bagian_quality_control' => 'nullable|in:1,2,3',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan tidak boleh kosong',
            '*.in' => 'Nilai harus 1, 2, atau 3',
        ];
    }
}
