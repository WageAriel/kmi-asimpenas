<?php

namespace App\Imports;

use App\Models\DataMitra;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DataMitraImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnError, SkipsOnFailure
{
    protected $errors = [];
    protected $failures = [];

    /**
     * Prepare data before validation
     * Convert numeric values to strings for NIK, NPWP, phone numbers, etc.
     */
    public function prepareForValidation($data, $index)
    {
        // Convert numeric fields to string to handle Excel scientific notation
        $numericFields = ['nik', 'npwp', 'no_telp_perusahaan', 'no_telp_cp', 'no_rekening', 'no_vms', 'kode_mitra'];
        
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && is_numeric($data[$field])) {
                $data[$field] = sprintf('%.0f', floatval($data[$field]));
            }
        }
        
        // Transform PKP values from "Ada/Tidak Ada" to "Pkp/Non Pkp"
        if (isset($data['pkp'])) {
            $pkpValue = trim($data['pkp']);
            if (strcasecmp($pkpValue, 'Ada') === 0) {
                $data['pkp'] = 'Pkp';
            } elseif (strcasecmp($pkpValue, 'Tidak Ada') === 0) {
                $data['pkp'] = 'Non Pkp';
            }
        }

        return $data;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Create user account if email is provided
        $user = null;
        if (!empty($row['email'])) {
            // Check if user already exists
            $existingUser = User::where('email', $row['email'])->first();
            
            if ($existingUser) {
                // User already exists, use existing user
                $user = $existingUser;
            } else {
                // Create new user with role mitra
                $user = User::create([
                    'name' => $row['nama_perusahaan'],
                    'email' => $row['email'],
                    'password' => Hash::make('mitra123'), // Default password: mitra123
                    'role' => 'mitra'
                ]);
            }
        }

        // Parse dates
        $tanggalLahir = $this->parseDate($row['tanggal_lahir'] ?? null);
        $tanggalSeleksi = $this->parseDate($row['tanggal_seleksi'] ?? null);
        $tanggalKlasifikasi = $this->parseDate($row['tanggal_klasifikasi'] ?? null);
        $tanggalPenilaian = $this->parseDate($row['tanggal_penilaian'] ?? null);
        $tanggalPenetapan = $this->parseDate($row['tanggal_penetapan'] ?? null);
        $tanggalSuratPermohonan = $this->parseDate($row['tanggal_surat_permohonan'] ?? null);
        $tanggalPaktaIntegritas = $this->parseDate($row['tanggal_pakta_integritas'] ?? null);

        return new DataMitra([
            'user_id' => $user ? $user->id : null,
            'nama_perusahaan' => $row['nama_perusahaan'],
            'badan_hukum_usaha' => $row['badan_hukum_usaha'] ?? null,
            'alamat_perusahaan' => $row['alamat_perusahaan'] ?? null,
            'kota_kabupaten' => $row['kota_kabupaten'] ?? null,
            'provinsi' => $row['provinsi'] ?? null,
            'nama_cp' => $row['nama_cp'] ?? null,
            'jabatan' => $row['jabatan'] ?? null,
            'nik' => $this->formatNumericString($row['nik'] ?? null),
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $tanggalLahir,
            'no_telp_perusahaan' => $this->formatNumericString($row['no_telp_perusahaan'] ?? null),
            'no_telp_cp' => $this->formatNumericString($row['no_telp_cp'] ?? null),
            'bank_korespondensi' => $row['bank_korespondensi'] ?? null,
            'alamat_bank' => $row['alamat_bank'] ?? null,
            'no_rekening' => $this->formatNumericString($row['no_rekening'] ?? null),
            'status_perusahaan' => $row['status_perusahaan'] ?? null,
            'npwp' => $this->formatNumericString($row['npwp'] ?? null),
            'pkp' => $row['pkp'] ?? null,
            'surat_kuasa' => $row['surat_kuasa'] ?? null,
            'tanggal_seleksi' => $tanggalSeleksi,
            'tanggal_klasifikasi' => $tanggalKlasifikasi,
            'tanggal_penilaian' => $tanggalPenilaian,
            'tanggal_penetapan' => $tanggalPenetapan,
            'tanggal_surat_permohonan' => $tanggalSuratPermohonan,
            'tanggal_pakta_integritas' => $tanggalPaktaIntegritas,
            'email' => $row['email'] ?? null,
            'no_vms' => $row['no_vms'] ?? null,
            'kode_mitra' => $row['kode_mitra'] ?? null,
        ]);
    }

    /**
     * Parse date from Excel format
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // If it's a numeric value (Excel date serial)
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            }
            
            // Try to parse string date
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format numeric string to handle scientific notation from Excel
     * Converts values like 3.17401E+13 back to proper string format
     */
    private function formatNumericString($value)
    {
        if (empty($value)) {
            return null;
        }

        // If it's already a string and not numeric, return as is
        if (is_string($value) && !is_numeric($value)) {
            return $value;
        }

        // If it's numeric (including scientific notation), convert to string without scientific notation
        if (is_numeric($value)) {
            // Use number_format to convert without decimal points for integers
            // sprintf with %.0f removes scientific notation and decimal points
            return sprintf('%.0f', floatval($value));
        }

        return (string) $value;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string|max:255',
            'email' => 'nullable|email|unique:data_mitra,email',
            'kode_mitra' => 'nullable|string|unique:data_mitra,kode_mitra|max:50',
            'nik' => 'nullable|string|max:30',
            'npwp' => 'nullable|string|max:30',
            'pkp' => 'nullable|in:Pkp,Non Pkp',
            'surat_kuasa' => 'nullable|in:Ada,Tidak Ada',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'kode_mitra.unique' => 'Kode mitra sudah terdaftar',
        ];
    }

    /**
     * Handle errors
     */
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get failures
     */
    public function getFailures()
    {
        return $this->failures;
    }
}
