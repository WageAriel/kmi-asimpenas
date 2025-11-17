<?php

namespace App\Imports;

use App\Models\DataSeleksiMitra;
use App\Models\DataMitra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Carbon\Carbon;

class DataSeleksiMitraImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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
            throw new \Exception("Mitra dengan nama '{$row['nama_perusahaan']}' Tidak ditemukan");
        }

        // Helper function untuk convert date
        $parseDate = function($value) {
            if (empty($value)) {
                return null;
            }
            
            try {
                // Try to parse as Excel date number (Excel stores dates as numbers)
                if (is_numeric($value)) {
                    return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
                }
                
                // Try to parse as string date
                // First, try common Indonesian/European format: d/m/Y
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
                    return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                }
                
                // Try other formats (Y-m-d, d-m-Y, etc)
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                // If parsing fails, return null
                return null;
            }
        };
        
        // Helper function untuk convert date dengan support "Seumur Hidup" untuk KTP
        $parseDateOrLifetime = function($value) use ($parseDate) {
            if (empty($value)) {
                return null;
            }
            
            // Check if "Seumur Hidup" (case insensitive)
            if (is_string($value) && strtolower(trim($value)) === 'seumur hidup') {
                return 'Seumur Hidup';
            }
            
            return $parseDate($value);
        };

        // Helper function untuk convert enum value
        $parseEnum = function($value) {
            if (empty($value)) {
                return null;
            }
            
            // Normalize value: trim whitespace and capitalize properly
            $normalized = trim($value);
            $lower = strtolower($normalized);
            
            // Map variations to standard values
            // Handle format: "1. Ada", "1.Ada", "1 Ada", "ada", etc
            if (preg_match('/^(1\.?\s*)?ada$/i', $lower)) {
                return 'Ada';
            } 
            // Handle format: "2. Tidak Ada", "2.Tidak Ada", "2 Tidak Ada", "tidak ada", etc
            elseif (preg_match('/^(2\.?\s*)?(tidak\s*ada|tidakada)$/i', $lower)) {
                return 'Tidak Ada';
            }
            
            return null;
        };

        return new DataSeleksiMitra([
            'id_mitra' => $mitra->id_mitra,
            'surat_permohonan' => $parseEnum($row['surat_permohonan'] ?? null),
            'mb_surat_permohonan' => $parseDate($row['mb_surat_permohonan'] ?? null),
            'akta_notaris' => $parseEnum($row['akta_notaris'] ?? null),
            'mb_akta_notaris' => $parseDate($row['mb_akta_notaris'] ?? null),
            'nib' => $parseEnum($row['nib'] ?? null),
            'mb_nib' => $parseDate($row['mb_nib'] ?? null),
            'ktp' => $parseEnum($row['ktp'] ?? null),
            'mb_ktp' => $parseDateOrLifetime($row['mb_ktp'] ?? null),
            'no_rekening' => $parseEnum($row['no_rekening'] ?? null),
            'mb_no_rekening' => $parseDate($row['mb_no_rekening'] ?? null),
            'npwp' => $parseEnum($row['npwp'] ?? null),
            'mb_npwp' => $parseDate($row['mb_npwp'] ?? null),
            'surat_kuasa' => $parseEnum($row['surat_kuasa'] ?? null),
            'mb_surat_kuasa' => $parseDate($row['mb_surat_kuasa'] ?? null),
            'lantai_jemur' => $parseEnum($row['lantai_jemur'] ?? null),
            'sarana_lainnya' => $parseEnum($row['sarana_lainnya'] ?? null),
            'mesin_memecah_kulit' => $parseEnum($row['mesin_memecah_kulit'] ?? null),
            'mesin_pemisah_gabah' => $parseEnum($row['mesin_pemisah_gabah'] ?? null),
            'mesin_penyosoh' => $parseEnum($row['mesin_penyosoh'] ?? null),
            'alat_pemisah_beras' => $parseEnum($row['alat_pemisah_beras'] ?? null),
            'status_seleksi' => 'pending',
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string',
            // Tidak perlu validasi in: karena sudah dihandle oleh parseEnum
            'surat_permohonan' => 'nullable',
            'akta_notaris' => 'nullable',
            'nib' => 'nullable',
            'ktp' => 'nullable',
            'no_rekening' => 'nullable',
            'npwp' => 'nullable',
            'surat_kuasa' => 'nullable',
            'lantai_jemur' => 'nullable',
            'sarana_lainnya' => 'nullable',
            'mesin_memecah_kulit' => 'nullable',
            'mesin_pemisah_gabah' => 'nullable',
            'mesin_penyosoh' => 'nullable',
            'alat_pemisah_beras' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan Tidak boleh kosong',
            '*.in' => 'Nilai harus "Ada" atau "Tidak Ada"',
        ];
    }
}
