<?php<?php



namespace App\Imports;namespace App\Imports;



use App\Models\DataMitra;use App\Models\DataMitra;

use Maatwebsite\Excel\Concerns\ToModel;use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithHeadingRow;use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Maatwebsite\Excel\Concerns\WithValidation;use Maatwebsite\Excel\Concerns\WithValidation;

use Maatwebsite\Excel\Concerns\SkipsOnFailure;use Maatwebsite\Excel\Concerns\SkipsOnFailure;

use Maatwebsite\Excel\Concerns\SkipsOnError;use Maatwebsite\Excel\Concerns\SkipsOnError;

use Maatwebsite\Excel\Concerns\SkipsFailures;use Maatwebsite\Excel\Concerns\SkipsFailures;

use Maatwebsite\Excel\Concerns\SkipsErrors;use Maatwebsite\Excel\Concerns\SkipsErrors;

use Maatwebsite\Excel\Concerns\WithBatchInserts;use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Maatwebsite\Excel\Concerns\WithChunkReading;use Maatwebsite\Excel\Concerns\WithChunkReading;



class DataMitraImport implements class DataMitraImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError, WithBatchInserts, WithChunkReading

    ToModel, {

    WithHeadingRow,     use SkipsFailures, SkipsErrors;

    WithValidation, 

    SkipsOnFailure,     protected $userId;

    SkipsOnError, 

    WithBatchInserts,     public function __construct($userId = null)

    WithChunkReading    {

{        $this->userId = $userId;

    use SkipsFailures, SkipsErrors;

    /**    }

    protected $userId;

    protected $imported = 0;     * Transform each row to model



    public function __construct($userId = null)     */    /**

    {

        $this->userId = $userId;    public function model(array $row)     * Map row ke model

    }

    {     * Pastikan header Excel sesuai dengan key yang digunakan di sini (lihat template)

    /**

     * Transform each row to model        // Skip if nama_perusahaan is empty     */

     */

    public function model(array $row)        if (empty($row['nama_perusahaan'])) {    public function model(array $row)

    {

        // Skip if nama_perusahaan is empty            return null;    {

        if (empty($row['nama_perusahaan'])) {

            return null;        }        return new DataMitra([

        }

            'user_id' => $this->userId,

        $this->imported++;

        // Create or find user for this mitra            'nama_perusahaan' => $row['nama_perusahaan'] ?? null,

        return new DataMitra([

            'user_id' => $this->userId,        $user = null;            'badan_hukum_usaha' => $row['badan_hukum_usaha'] ?? null,

            'nama_perusahaan' => $row['nama_perusahaan'] ?? null,

            'badan_hukum_usaha' => $row['badan_hukum_usaha'] ?? null,        if (!empty($row['email'])) {            'alamat_perusahaan' => $row['alamat_perusahaan'] ?? null,

            'alamat_perusahaan' => $row['alamat_perusahaan'] ?? null,

            'kota_kabupaten' => $row['kota_kabupaten'] ?? null,            $user = User::firstOrCreate(            'kota_kabupaten' => $row['kota_kabupaten'] ?? null,

            'provinsi' => $row['provinsi'] ?? null,

            'nama_cp' => $row['nama_cp'] ?? null,                ['email' => $row['email']],            'provinsi' => $row['provinsi'] ?? null,

            'nik' => $row['nik'] ?? null,

            'tempat_lahir' => $row['tempat_lahir'] ?? null,                [            'nama_cp' => $row['nama_cp'] ?? null,

            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,

            'no_telp_perusahaan' => $row['no_telp_perusahaan'] ?? null,                    'name' => $row['nama_perusahaan'],            'nik' => $row['nik'] ?? null,

            'no_telp_cp' => $row['no_telp_cp'] ?? null,

            'bank_korespondensi' => $row['bank_korespondensi'] ?? null,                    'password' => Hash::make('password123'), // Default password            'tempat_lahir' => $row['tempat_lahir'] ?? null,

            'alamat_bank' => $row['alamat_bank'] ?? null,

            'no_rekening' => $row['no_rekening'] ?? null,                    'role' => 'mitra',            // tanggal_lahir gunakan format YYYY-MM-DD di file

            'status_perusahaan' => $row['status_perusahaan'] ?? null,

            'npwp' => $row['npwp'] ?? null,                ]            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,

            'pkp' => $row['pkp'] ?? null,

            'surat_kuasa' => $row['surat_kuasa'] ?? null,            );            'no_telp_perusahaan' => $row['no_telp_perusahaan'] ?? null,

            'tanggal_seleksi' => $row['tanggal_seleksi'] ?? null,

            'tanggal_klasifikasi' => $row['tanggal_klasifikasi'] ?? null,        }            'no_telp_cp' => $row['no_telp_cp'] ?? null,

            'tanggal_penilaian' => $row['tanggal_penilaian'] ?? null,

            'tanggal_penetapan' => $row['tanggal_penetapan'] ?? null,            'bank_korespondensi' => $row['bank_korespondensi'] ?? null,

            'tanggal_surat_permohonan' => $row['tanggal_surat_permohonan'] ?? null,

            'tanggal_pakta_integritas' => $row['tanggal_pakta_integritas'] ?? null,        $this->imported++;            'alamat_bank' => $row['alamat_bank'] ?? null,

            'email' => $row['email'] ?? null,

            'no_vms' => $row['no_vms'] ?? null,            'no_rekening' => $row['no_rekening'] ?? null,

            'kode_mitra' => $row['kode_mitra'] ?? null,

        ]);        return new DataMitra([            'status_perusahaan' => $row['status_perusahaan'] ?? null,

    }

            'user_id' => $user ? $user->id : null,            'npwp' => $row['npwp'] ?? null,

    /**

     * Validation rules            'nama_perusahaan' => $row['nama_perusahaan'] ?? null,            'pkp' => $row['pkp'] ?? null,

     */

    public function rules(): array            'badan_hukum_usaha' => $row['badan_hukum_usaha'] ?? null,            'surat_kuasa' => $row['surat_kuasa'] ?? null,

    {

        return [            'alamat_perusahaan' => $row['alamat_perusahaan'] ?? null,            'tanggal_seleksi' => $row['tanggal_seleksi'] ?? null,

            'nama_perusahaan' => 'required|string|max:255',

            'email' => 'nullable|email|unique:data_mitra,email',            'kota_kabupaten' => $row['kota_kabupaten'] ?? null,            'tanggal_klasifikasi' => $row['tanggal_klasifikasi'] ?? null,

            'kode_mitra' => 'nullable|unique:data_mitra,kode_mitra',

            'nik' => 'nullable|string|max:30',            'provinsi' => $row['provinsi'] ?? null,            'tanggal_penilaian' => $row['tanggal_penilaian'] ?? null,

            'no_telp_perusahaan' => 'nullable|string|max:20',

            'no_telp_cp' => 'nullable|string|max:20',            'nama_cp' => $row['nama_cp'] ?? null,            'tanggal_penetapan' => $row['tanggal_penetapan'] ?? null,

            'npwp' => 'nullable|string|max:30',

            'pkp' => 'nullable|in:pkp,non pkp',            'nik' => $row['nik'] ?? null,            'tanggal_surat_permohonan' => $row['tanggal_surat_permohonan'] ?? null,

            'surat_kuasa' => 'nullable|in:ada,tidak ada',

        ];            'tempat_lahir' => $row['tempat_lahir'] ?? null,            'tanggal_pakta_integritas' => $row['tanggal_pakta_integritas'] ?? null,

    }

            'tanggal_lahir' => !empty($row['tanggal_lahir']) ? $this->transformDate($row['tanggal_lahir']) : null,            'email' => $row['email'] ?? null,

    /**

     * Custom validation messages            'no_telp_perusahaan' => $row['no_telp_perusahaan'] ?? null,            'no_vms' => $row['no_vms'] ?? null,

     */

    public function customValidationMessages()            'no_telp_cp' => $row['no_telp_cp'] ?? null,            'kode_mitra' => $row['kode_mitra'] ?? null,

    {

        return [            'bank_korespondensi' => $row['bank_korespondensi'] ?? null,        ]);

            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',

            'email.email' => 'Format email tidak valid',            'alamat_bank' => $row['alamat_bank'] ?? null,    }

            'email.unique' => 'Email sudah terdaftar',

            'kode_mitra.unique' => 'Kode mitra sudah terdaftar',            'no_rekening' => $row['no_rekening'] ?? null,

        ];

    }            'status_perusahaan' => $row['status_perusahaan'] ?? null,    /**



    /**            'npwp' => $row['npwp'] ?? null,     * Rules validasi per kolom (sesuaikan kebutuhan)

     * Batch size for bulk insert

     */            'pkp' => $row['pkp'] ?? null,     */

    public function batchSize(): int

    {            'surat_kuasa' => $row['surat_kuasa'] ?? null,    public function rules(): array

        return 100;

    }            'tanggal_seleksi' => !empty($row['tanggal_seleksi']) ? $this->transformDate($row['tanggal_seleksi']) : null,    {



    /**            'tanggal_klasifikasi' => !empty($row['tanggal_klasifikasi']) ? $this->transformDate($row['tanggal_klasifikasi']) : null,        return [

     * Chunk size for reading

     */            'tanggal_penilaian' => !empty($row['tanggal_penilaian']) ? $this->transformDate($row['tanggal_penilaian']) : null,            'nama_perusahaan' => 'required|string|max:255',

    public function chunkSize(): int

    {            'tanggal_penetapan' => !empty($row['tanggal_penetapan']) ? $this->transformDate($row['tanggal_penetapan']) : null,            'email' => 'nullable|email|unique:data_mitra,email',

        return 100;

    }            'tanggal_surat_permohonan' => !empty($row['tanggal_surat_permohonan']) ? $this->transformDate($row['tanggal_surat_permohonan']) : null,            'kode_mitra' => 'nullable|unique:data_mitra,kode_mitra',



    /**            'tanggal_pakta_integritas' => !empty($row['tanggal_pakta_integritas']) ? $this->transformDate($row['tanggal_pakta_integritas']) : null,            'tanggal_lahir' => 'nullable|date_format:Y-m-d',

     * Get import summary

     */            'email' => $row['email'] ?? null,            'pkp' => 'nullable|in:pkp,non pkp',

    public function getImportSummary()

    {            'no_vms' => $row['no_vms'] ?? null,            'surat_kuasa' => 'nullable|in:ada,tidak ada',

        return [

            'imported' => $this->imported,            'kode_mitra' => $row['kode_mitra'] ?? null,            // tambahkan rule lain sesuai kebutuhan

            'failures' => collect($this->failures())->map(function ($failure) {

                return [        ]);        ];

                    'row' => $failure->row(),

                    'attribute' => $failure->attribute(),    }    }

                    'errors' => $failure->errors(),

                ];

            })->toArray(),

        ];    /**    /**

    }

}     * Transform date from Excel     * Ukuran batch insert untuk performance


     */     */

    private function transformDate($value)    public function batchSize(): int

    {    {

        if (empty($value)) {        return 500;

            return null;    }

        }

    /**

        // If it's already a valid date string     * Chunk reading agar memory friendly

        if (is_string($value)) {     */

            try {    public function chunkSize(): int

                return \Carbon\Carbon::parse($value)->format('Y-m-d');    {

            } catch (\Exception $e) {        return 500;

                return null;    }

            }}
        }

        // If it's an Excel date number
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string|max:255',
            'email' => 'nullable|email',
            'nik' => 'nullable|string|max:30',
            'no_telp_perusahaan' => 'nullable|string|max:20',
            'no_telp_cp' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:30',
            'pkp' => 'nullable|in:pkp,non pkp',
            'surat_kuasa' => 'nullable|in:ada,tidak ada',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',
            'email.email' => 'Format email tidak valid',
        ];
    }

    /**
     * Handle errors
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    /**
     * Batch size for bulk insert
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get import summary
     */
    public function getImportSummary()
    {
        return [
            'imported' => $this->imported,
            'errors' => $this->errors,
            'failures' => collect($this->failures)->map(function ($failure) {
                return [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            })->toArray(),
        ];
    }
}
