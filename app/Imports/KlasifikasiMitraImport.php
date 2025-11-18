<?php

namespace App\Imports;

use App\Models\KlasifikasiMitra;
use App\Models\DataMitra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class KlasifikasiMitraImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow, SkipsEmptyRows
{
    use SkipsFailures;

    /**
     * Mulai import dari baris ke-3 (melewati 2 baris header)
     *
     * @return int
     */
    public function startRow(): int
    {
        return 3;
    }

    /**
     * Tentukan baris mana yang dijadikan heading
     * Baris 2 akan dijadikan sebagai heading/key untuk array
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Debug: Log struktur row untuk memahami mapping
        \Log::info('===== IMPORT ROW DEBUG =====');
        \Log::info('Row keys: ' . implode(', ', array_keys($row)));
        \Log::info('Full row data:', $row);
        
        // Laravel Excel akan convert header ke snake_case
        // "No" -> "no"
        // "Nama MKP" -> "nama_mkp"
        
        // Periksa apakah ada kolom 'no' yang berisi angka
        if (isset($row['no']) && is_numeric($row['no'])) {
            \Log::info('Detected "no" column with value: ' . $row['no']);
        }
        
        // Ambil nama mitra dari kolom yang tepat
        $namaMitra = null;
        
        // Coba ambil dari 'nama_mkp' dulu
        if (isset($row['nama_mkp']) && !empty($row['nama_mkp'])) {
            $namaMitra = trim($row['nama_mkp']);
            \Log::info('Found nama_mkp: ' . $namaMitra);
            
            // Validasi: jika nama_mkp adalah angka saja, berarti mapping salah
            if (is_numeric($namaMitra)) {
                \Log::warning('nama_mkp contains only numbers, likely wrong mapping');
                $namaMitra = null;
            }
        }
        
        // Jika masih null, cari di kolom lain
        if (!$namaMitra) {
            \Log::info('nama_mkp not found or invalid, searching other columns...');
            foreach ($row as $key => $value) {
                if ($key === 'no' || empty($value)) continue;
                
                $value = trim($value);
                // Cari kolom yang berisi string (bukan angka murni)
                if (is_string($value) && !is_numeric($value) && strlen($value) > 3) {
                    $namaMitra = $value;
                    \Log::info("Found potential nama_mitra in column '$key': $namaMitra");
                    break;
                }
            }
        }
        
        // Jika masih tidak ada nama mitra, skip row
        if (!$namaMitra) {
            \Log::warning('Nama MKP tidak ditemukan di row, skipping...');
            return null;
        }
        
        \Log::info("Processing mitra: $namaMitra");
        
        // Cari ID mitra berdasarkan nama perusahaan
        $mitra = DataMitra::where('nama_perusahaan', $namaMitra)->first();
        
        if (!$mitra) {
            throw new \Exception("Mitra dengan nama '{$namaMitra}' tidak ditemukan");
        }

        // Helper function untuk convert nilai ke format enum descriptive
        // Mendukung input angka (1,2,3) atau format descriptive langsung
        $parseEnumValue = function($value, $field) {
            if (empty($value)) {
                return null;
            }
            
            $value = trim($value);
            
            // Jika sudah format descriptive (mengandung titik), langsung return
            if (strpos($value, '.') !== false) {
                return $value;
            }
            
            // Mapping dari angka ke format descriptive sesuai migration
            $enumMappings = [
                'mesin_pembersih_gabah' => [
                    '1' => '1. Tidak Ada',
                    '2' => '2. Ada | ≤ 20',
                    '3' => '3. Ada | > 20'
                ],
                'lantai_jemur' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 10',
                    '3' => '3. Ada | > 10'
                ],
                'mesin_pengering' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | ≤ 20',
                    '3' => '3. Ada | > 20'
                ],
                'alat_pengering_lainnya' => [
                    '1' => '1. Ada | ≤ 1',
                    '2' => '2. Tidak Disyaratkan',
                    '3' => '3. Tidak Disyaratkan'
                ],
                'mesin_pembersih_awal' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_pemecah_kulit' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_pembersih_sekam' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_pemisah_gabah_pecah_kulit' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_pemisah_batu' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_penyosoh' => [
                    '1' => '1. Ada | ≤ 1 | 1',
                    '2' => '2. Ada | 1 s/d 3 | 1',
                    '3' => '3. Ada | > 3 | 2'
                ],
                'mesin_pengkabut' => [
                    '1' => '1. Ada | ≤ 1 | 1',
                    '2' => '2. Ada | 1 s/d 3 | 1',
                    '3' => '3. Ada | > 3 | 2'
                ],
                'mesin_pemesah_menir' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_pemisah_katul' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_pemisah_berdasarkan_ukuran' => [
                    '1' => '1. Ada | ≤ 1',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'mesin_pemisah_berdasarkan_warna' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 3',
                    '3' => '3. Ada | > 3'
                ],
                'tangki_penyimpanan' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | ≤ 10',
                    '3' => '3. Ada | > 10'
                ],
                'mesin_pengemas' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | Semi Otomatis',
                    '3' => '3. Ada | Full Otomatis'
                ],
                'mesin_jahit' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | Semi Otomatis',
                    '3' => '3. Ada | Full Otomatis'
                ],
                'gudang_konvensional' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | < 3000',
                    '3' => '3. Ada | > 3000'
                ],
                'silo_gkg_hopper' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Tidak Ada',
                    '3' => '3. Ada | > 2000'
                ],
                'truk' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | 1 s/d 5',
                    '3' => '3. Ada | > 5'
                ],
                'mini_lab' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | Tidak Khusus',
                    '3' => '3. Ada | Ruang Khusus'
                ],
                'moisture_tester' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | Berfungsi',
                    '3' => '3. Ada | Lengkap | Berfungsi'
                ],
                'pembanding_derajat_sosoh' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | Tidak Sesuai',
                    '3' => '3. Ada | Sesuai Standar'
                ],
                'bagian_quality_control' => [
                    '1' => '1. Tidak ada',
                    '2' => '2. Ada | Merangkap',
                    '3' => '3. Ada | Tidak Merangkap'
                ]
            ];
            
            // Jika value adalah angka (1,2,3), konversi ke format descriptive
            if (isset($enumMappings[$field][$value])) {
                return $enumMappings[$field][$value];
            }
            
            return null;
        };

        // Parse hasil_klasifikasi dari Excel
        // Format yang diterima: "A", "B", "C" (case-insensitive)
        $hasilKlasifikasi = null;
        
        // Debug: Log untuk melihat key hasil_klasifikasi
        \Log::info('Checking hasil_klasifikasi...');
        \Log::info('Available keys in row: ' . implode(', ', array_keys($row)));
        
        // Coba cari dari key 'hasil_klasifikasi' dulu
        if (isset($row['hasil_klasifikasi']) && !empty($row['hasil_klasifikasi'])) {
            $nilai = strtoupper(trim($row['hasil_klasifikasi']));
            \Log::info('Found hasil_klasifikasi from named key: ' . $nilai);
            
            if (in_array($nilai, ['A', 'B', 'C'])) {
                $hasilKlasifikasi = $nilai;
            }
        } else {
            // Jika tidak ada key 'hasil_klasifikasi', cari di key numerik terakhir
            // atau cari value yang berisi A, B, atau C saja
            \Log::info('hasil_klasifikasi key not found, searching in all columns...');
            
            foreach ($row as $key => $value) {
                if (!empty($value)) {
                    $cleanValue = strtoupper(trim($value));
                    // Cek apakah value adalah A, B, atau C (tepat 1 karakter)
                    if (strlen($cleanValue) === 1 && in_array($cleanValue, ['A', 'B', 'C'])) {
                        $hasilKlasifikasi = $cleanValue;
                        \Log::info("Found hasil_klasifikasi '$cleanValue' in key '$key'");
                        break;
                    }
                }
            }
            
            if (!$hasilKlasifikasi) {
                \Log::warning('hasil_klasifikasi not found in any column');
            }
        }
        
        \Log::info('Final hasil_klasifikasi: ' . ($hasilKlasifikasi ?? 'NULL'));
        
        return new KlasifikasiMitra([
            'id_mitra' => $mitra->id_mitra,
            'mesin_pembersih_gabah' => $parseEnumValue($row['mesin_pembersih_gabah'] ?? null, 'mesin_pembersih_gabah'),
            'lantai_jemur' => $parseEnumValue($row['lantai_jemur'] ?? null, 'lantai_jemur'),
            'mesin_pengering' => $parseEnumValue($row['mesin_pengering'] ?? null, 'mesin_pengering'),
            'alat_pengering_lainnya' => $parseEnumValue($row['alat_pengering_lainnya'] ?? null, 'alat_pengering_lainnya'),
            'mesin_pembersih_awal' => $parseEnumValue($row['mesin_pembersih_awal'] ?? null, 'mesin_pembersih_awal'),
            'mesin_pemecah_kulit' => $parseEnumValue($row['mesin_pemecah_kulit'] ?? null, 'mesin_pemecah_kulit'),
            'mesin_pembersih_sekam' => $parseEnumValue($row['mesin_pembersih_sekam'] ?? null, 'mesin_pembersih_sekam'),
            'mesin_pemisah_gabah_pecah_kulit' => $parseEnumValue($row['mesin_pemisah_gabah_pecah_kulit'] ?? null, 'mesin_pemisah_gabah_pecah_kulit'),
            'mesin_pemisah_batu' => $parseEnumValue($row['mesin_pemisah_batu'] ?? null, 'mesin_pemisah_batu'),
            'mesin_penyosoh' => $parseEnumValue($row['mesin_penyosoh'] ?? null, 'mesin_penyosoh'),
            'mesin_pengkabut' => $parseEnumValue($row['mesin_pengkabut'] ?? null, 'mesin_pengkabut'),
            'mesin_pemesah_menir' => $parseEnumValue($row['mesin_pemesah_menir'] ?? null, 'mesin_pemesah_menir'),
            'mesin_pemisah_katul' => $parseEnumValue($row['mesin_pemisah_katul'] ?? null, 'mesin_pemisah_katul'),
            'mesin_pemisah_berdasarkan_ukuran' => $parseEnumValue($row['mesin_pemisah_berdasarkan_ukuran'] ?? null, 'mesin_pemisah_berdasarkan_ukuran'),
            'mesin_pemisah_berdasarkan_warna' => $parseEnumValue($row['mesin_pemisah_berdasarkan_warna'] ?? null, 'mesin_pemisah_berdasarkan_warna'),
            'tangki_penyimpanan' => $parseEnumValue($row['tangki_penyimpanan'] ?? null, 'tangki_penyimpanan'),
            'mesin_pengemas' => $parseEnumValue($row['mesin_pengemas'] ?? null, 'mesin_pengemas'),
            'mesin_jahit' => $parseEnumValue($row['mesin_jahit'] ?? null, 'mesin_jahit'),
            'gudang_konvensional' => $parseEnumValue($row['gudang_konvensional'] ?? null, 'gudang_konvensional'),
            'silo_gkg_hopper' => $parseEnumValue($row['silo_gkg_hopper'] ?? null, 'silo_gkg_hopper'),
            'truk' => $parseEnumValue($row['truk'] ?? null, 'truk'),
            'mini_lab' => $parseEnumValue($row['mini_lab'] ?? null, 'mini_lab'),
            'moisture_tester' => $parseEnumValue($row['moisture_tester'] ?? null, 'moisture_tester'),
            'pembanding_derajat_sosoh' => $parseEnumValue($row['pembanding_derajat_sosoh'] ?? null, 'pembanding_derajat_sosoh'),
            'bagian_quality_control' => $parseEnumValue($row['bagian_quality_control'] ?? null, 'bagian_quality_control'),
            'hasil_klasifikasi' => $hasilKlasifikasi, // Bisa diisi dari Excel atau null jika tidak ada
        ]);
    }

    public function rules(): array
    {
        // Validasi akan dilakukan di parseEnumValue function
        // Terima angka (1,2,3) atau format descriptive
        // CATATAN: 'nama_mkp' bisa jadi berisi angka dari kolom "No" jika mapping salah
        // Jadi kita hilangkan required, dan lakukan validasi manual di model()
        return [
            // 'nama_mkp' => 'required|string', // DIHAPUS - validasi manual di model()
            'mesin_pembersih_gabah' => 'nullable',
            'lantai_jemur' => 'nullable',
            'mesin_pengering' => 'nullable',
            'alat_pengering_lainnya' => 'nullable',
            'mesin_pembersih_awal' => 'nullable',
            'mesin_pemecah_kulit' => 'nullable',
            'mesin_pembersih_sekam' => 'nullable',
            'mesin_pemisah_gabah_pecah_kulit' => 'nullable',
            'mesin_pemisah_batu' => 'nullable',
            'mesin_penyosoh' => 'nullable',
            'mesin_pengkabut' => 'nullable',
            'mesin_pemesah_menir' => 'nullable',
            'mesin_pemisah_katul' => 'nullable',
            'mesin_pemisah_berdasarkan_ukuran' => 'nullable',
            'mesin_pemisah_berdasarkan_warna' => 'nullable',
            'tangki_penyimpanan' => 'nullable',
            'mesin_pengemas' => 'nullable',
            'mesin_jahit' => 'nullable',
            'gudang_konvensional' => 'nullable',
            'silo_gkg_hopper' => 'nullable',
            'truk' => 'nullable',
            'mini_lab' => 'nullable',
            'moisture_tester' => 'nullable',
            'pembanding_derajat_sosoh' => 'nullable',
            'bagian_quality_control' => 'nullable',
            'hasil_klasifikasi' => 'nullable|in:A,B,C,a,b,c',
        ];
    }

    public function customValidationMessages()
    {
        return [
            // Validation messages - kosongkan karena validasi nama_mkp dilakukan manual di model()
        ];
    }
}
