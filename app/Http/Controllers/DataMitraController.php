<?php

namespace App\Http\Controllers;

use App\Models\DataMitra;
use Illuminate\Http\Request;
use App\Services\ActivityAggregatorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Imports\DataMitraImport;
use App\Exports\DataMitraExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class DataMitraController extends Controller
{
    
    public function index()
    {
        $mitras = DataMitra::all();
        return response()->json($mitras);
    }

    /**
     * Import mitra from Excel file
     */
    public function import(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Max 5MB
            ], [
                'file.required' => 'File harus dipilih',
                'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
                'file.max' => 'Ukuran file maksimal 5MB',
            ]);

            // Check if file was uploaded
            if (!$request->hasFile('file')) {
                return response()->json([
                    'message' => 'File tidak ditemukan',
                ], 400);
            }

            $file = $request->file('file');
            
            // Additional file validation
            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'File tidak valid atau corrupt',
                ], 400);
            }

            Log::info('Starting import', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);

            $import = new DataMitraImport();
            Excel::import($import, $file);

            // Check if there were any failures
            $failures = $import->getFailures();
            $errors = $import->getErrors();

            if (!empty($failures) || !empty($errors)) {
                $failureMessages = [];
                
                foreach ($failures as $failure) {
                    $failureMessages[] = [
                        'row' => $failure->row(),
                        'attribute' => $failure->attribute(),
                        'errors' => $failure->errors(),
                        'values' => $failure->values(),
                    ];
                }

                Log::warning('Import completed with errors', [
                    'failures_count' => count($failureMessages),
                    'errors_count' => count($errors),
                ]);

                return response()->json([
                    'message' => 'Import selesai dengan beberapa error',
                    'failures' => $failureMessages,
                    'errors' => $errors,
                ], 422);
            }

            ActivityAggregatorService::clearAllActivitiesCache();

            Log::info('Import completed successfully');

            return response()->json([
                'message' => 'Data mitra berhasil diimport',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error during import', [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (ValidationException $e) {
            $failures = $e->failures();
            $failureMessages = [];
            
            foreach ($failures as $failure) {
                $failureMessages[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            }

            Log::error('Excel validation error', [
                'failures_count' => count($failureMessages),
            ]);

            return response()->json([
                'message' => 'Validasi data Excel gagal',
                'failures' => $failureMessages,
            ], 422);

        } catch (\Exception $e) {
            Log::error('Import exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat import',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
            ], 500);
        }
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        $headers = [
            'nama_perusahaan',
            'badan_hukum_usaha',
            'alamat_perusahaan',
            'kota_kabupaten',
            'provinsi',
            'nama_cp',
            'jabatan',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'no_telp_perusahaan',
            'no_telp_cp',
            'bank_korespondensi',
            'alamat_bank',
            'no_rekening',
            'nama_pemilik_rekening',
            'status_perusahaan',
            'npwp',
            'pkp',
            'surat_kuasa',
            'nama_yang_dikuasakan',
            'nik_yang_dikuasakan',
            'alamat_yang_dikuasakan',
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

        $exampleData = [
            'PT. Contoh Mitra',
            'PT',
            'Jl. Contoh No. 123',
            'Jakarta Selatan',
            'DKI Jakarta',
            'John Doe',
            'Direktur',
            '3174012345678901',
            'Jakarta',
            '1990-01-15',
            '021-1234567',
            '081234567890',
            'Bank Mandiri',
            'Jl. Bank No. 1',
            '1234567890',
            'PT. Contoh Mitra',
            'Penggilingan Padi',
            '01.234.567.8-901.000',
            'Pkp',
            'Ada',
            'Budi Santoso',
            '3174013456789012',
            'Jl. Kuasa No. 456, Jakarta Pusat',
            '2025-01-01',
            '2025-01-15',
            '2025-02-01',
            '2025-02-15',
            '2024-12-01',
            '2024-12-15',
            'contoh@email.com',
            'VMS001',
            'MTR001',
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $column = 'A';
        $columnIndex = 0;
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getStyle($column . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE2EFDA');
            $sheet->getColumnDimension($column)->setAutoSize(true);
            
            $column++;
            $columnIndex++;
        }

        // Add instruction row

        // Set example data (now on row 3)
        $column = 'A';
        foreach ($exampleData as $data) {
            $cellCoordinate = $column . '2';
            
            // For text columns, explicitly set as string with text format
            $currentHeader = $headers[ord($column) - ord('A')];
            $textColumns = ['nik', 'npwp', 'no_rekening', 'no_telp_perusahaan', 'no_telp_cp', 'nik_yang_dikuasakan'];
            
            if (in_array($currentHeader, $textColumns)) {
                // Set value explicitly as string
                $sheet->setCellValueExplicit(
                    $cellCoordinate,
                    $data,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            } else {
                $sheet->setCellValue($cellCoordinate, $data);
            }
            
            $column++;
        }

        // Create writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Set headers for download
        $fileName = 'template_import_mitra_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export mitra to Excel
     */
    public function export()
    {
        try {
            $fileName = 'data_mitra_' . date('Y-m-d_His') . '.xlsx';
            
            Log::info('Starting export data mitra');
            
            return Excel::download(new DataMitraExport, $fileName);
            
        } catch (\Exception $e) {
            Log::error('Export exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat export',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function myMitra()
{
    $userId = auth()->id();
    $mitra = DataMitra::where('user_id', $userId)->first();

    if (!$mitra) {
        return response()->json(['message' => 'Data mitra tidak ditemukan'], 404);
    }

    return response()->json($mitra);
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'badan_hukum_usaha' => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'kota_kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'nama_cp' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:30',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_telp_perusahaan' => 'nullable|string|max:20',
            'no_telp_cp' => 'nullable|string|max:20',
            'bank_korespondensi' => 'nullable|string|max:255',
            'alamat_bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:30',
            'nama_pemilik_rekening' => 'nullable|string|max:255',
            'status_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'pkp' => 'nullable|in:Pkp,Non Pkp',
            'surat_kuasa' => 'nullable|in:Ada,Tidak Ada',
            'nama_yang_dikuasakan' => 'nullable|string|max:255',
            'nik_yang_dikuasakan' => 'nullable|string|max:30',
            'alamat_yang_dikuasakan' => 'nullable|string',
            'tanggal_seleksi' => 'nullable|date',
            'tanggal_klasifikasi' => 'nullable|date',
            'tanggal_penilaian' => 'nullable|date',
            'tanggal_penetapan' => 'nullable|date',
            'tanggal_surat_permohonan' => 'nullable|date',
            'tanggal_pakta_integritas' => 'nullable|date',
            'email' => 'nullable|email|unique:data_mitra,email',
            'no_vms' => 'nullable|string|max:50',
            'kode_mitra' => 'nullable|string|unique:data_mitra,kode_mitra|max:50',
        ]);

        $validated['user_id'] = auth()->id();

        $mitra = DataMitra::create($validated);

        ActivityAggregatorService::clearUserCache(Auth::id());
        ActivityAggregatorService::clearAllActivitiesCache();
        
        return response()->json($mitra, 201);
    }

    public function show($id)
    {
        $mitra = DataMitra::findOrFail($id);
        return response()->json($mitra);
    }

    public function update(Request $request, $id)
    {
        $mitra = DataMitra::findOrFail($id);

        $validated = $request->validate([
            'nama_perusahaan' => 'sometimes|required|string|max:255',
            'badan_hukum_usaha' => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'kota_kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'nama_cp' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:30',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_telp_perusahaan' => 'nullable|string|max:20',
            'no_telp_cp' => 'nullable|string|max:20',
            'bank_korespondensi' => 'nullable|string|max:255',
            'alamat_bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:30',
            'nama_pemilik_rekening' => 'nullable|string|max:255',
            'nama_yang_dikuasakan' => 'nullable|string|max:255',
            'nik_yang_dikuasakan' => 'nullable|string|max:30',
            'alamat_yang_dikuasakan' => 'nullable|string',
            'status_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'pkp' => 'nullable|in:Pkp,Non Pkp',
            'surat_kuasa' => 'nullable|in:Ada,Tidak Ada',
            'tanggal_seleksi' => 'nullable|date',
            'tanggal_klasifikasi' => 'nullable|date',
            'tanggal_penilaian' => 'nullable|date',
            'tanggal_penetapan' => 'nullable|date',
            'tanggal_surat_permohonan' => 'nullable|date',
            'tanggal_pakta_integritas' => 'nullable|date',
            'email' => 'nullable|email|unique:data_mitra,email,' . $id . ',id_mitra',
            'no_vms' => 'nullable|string|max:50',
            'kode_mitra' => 'nullable|string|unique:data_mitra,kode_mitra,' . $id . ',id_mitra|max:50',
        ]);

        $mitra->update($validated);
        if ($mitra->user) {
            $userUpdateData = [];
            if (isset($validated['email'])) {
                $userUpdateData['email'] = $validated['email'];
            }
            if (isset($validated['nama_perusahaan'])) {
                $userUpdateData['name'] = $validated['nama_perusahaan'];
            }
            if (!empty($userUpdateData)) {
                $mitra->user->update($userUpdateData);
            }
        }

        ActivityAggregatorService::clearUserCache(Auth::id());
        ActivityAggregatorService::clearAllActivitiesCache();
    
        return response()->json($mitra);
    }

    public function destroy($id)
    {
        try {
            $mitra = DataMitra::findOrFail($id);
            
            // Use transaction to ensure data consistency
            DB::transaction(function () use ($id) {
                // Delete related records first (to avoid foreign key constraint violation)
                
                // 1. Delete from hasil_seleksi_mitras (if exists)
                DB::table('hasil_seleksi_mitra')
                    ->where('id_mitra', $id)
                    ->delete();

                // 2. Delete from klasifikasi_mitra_pangans (if exists)
                DB::table('data_klasifikasi_mitra')
                    ->where('id_mitra', $id)
                    ->delete();

                // 3. Delete from data_seleksi_mitras
                DB::table('data_seleksi_mitra')
                    ->where('id_mitra', $id)
                    ->delete();

                // 4. Finally, delete from data_mitras
                DataMitra::where('id_mitra', $id)->delete();
            });

            // Clear activity cache
            ActivityAggregatorService::clearAllActivitiesCache();

            return response()->json([
                'message' => 'Data mitra dan data terkait berhasil dihapus'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data mitra tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Delete mitra error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mitra birthdays (today and upcoming)
     */
    public function getBirthdays(Request $request)
    {
        $days = (int) $request->input('days', 7); // Default 7 hari ke depan, cast to int
        
        $now = now();
        $today = $now->copy()->startOfDay();
        $endDate = $now->copy()->addDays($days)->endOfDay();
        $currentYear = $now->year;
        
        // Get all mitra with tanggal_lahir
        $mitras = DataMitra::whereNotNull('tanggal_lahir')
            ->get()
            ->filter(function ($mitra) use ($today, $endDate, $currentYear) {
                if (!$mitra->tanggal_lahir) {
                    return false;
                }
                
                // Convert string to Carbon and get birthday this year
                $birthDate = \Carbon\Carbon::parse($mitra->tanggal_lahir);
                $birthday = \Carbon\Carbon::create($currentYear, $birthDate->month, $birthDate->day, 0, 0, 0);
                
                // Check if birthday is between today and endDate
                return $birthday->between($today, $endDate);
            })
            ->map(function ($mitra) use ($today, $currentYear) {
                $birthDate = \Carbon\Carbon::parse($mitra->tanggal_lahir);
                $birthday = \Carbon\Carbon::create($currentYear, $birthDate->month, $birthDate->day, 0, 0, 0);
                $age = $currentYear - $birthDate->year;
                
                // Calculate days until birthday
                $daysUntil = (int) $today->diffInDays($birthday->copy());
                
                // Check if birthday is in the past this year
                if ($birthday->lt($today)) {
                    $daysUntil = 0; // Already passed
                }
                
                // Check if today is the birthday
                $isToday = $today->isSameDay($birthday);
                if ($isToday) {
                    $daysUntil = 0;
                }
                
                return [
                    'id_mitra' => $mitra->id_mitra,
                    'nama_perusahaan' => $mitra->nama_perusahaan,
                    'nama_cp' => $mitra->nama_cp,
                    'tanggal_lahir' => $birthDate->format('Y-m-d'),
                    'birthday_this_year' => $birthday->format('Y-m-d'),
                    'age' => $age,
                    'days_until' => $daysUntil,
                    'is_today' => $isToday,
                    'formatted_date' => $birthday->format('d F Y'),
                ];
            })
            ->sortBy('days_until')
            ->values();
        
        return response()->json($mitras);
    }

    /**
     * Update mitra data by admin (only specific fields)
     */
    public function updateByAdmin(Request $request, $id)
    {
        try {
            $mitra = DataMitra::findOrFail($id);

            $validated = $request->validate([
                // Data Perusahaan
                'nama_perusahaan' => 'nullable|string|max:255',
                'badan_hukum_usaha' => 'nullable|string|max:100',
                'alamat_perusahaan' => 'nullable|string',
                'kota_kabupaten' => 'nullable|string|max:100',
                'provinsi' => 'nullable|string|max:100',
                'status_perusahaan' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'no_telp_perusahaan' => 'nullable|string|max:20',
                
                // Data Contact Person
                'nama_cp' => 'nullable|string|max:255',
                'jabatan' => 'nullable|string|max:100',
                'nik' => 'nullable|string|max:20',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'no_telp_cp' => 'nullable|string|max:20',
                
                // Data Bank
                'bank_korespondensi' => 'nullable|string|max:100',
                'alamat_bank' => 'nullable|string',
                'no_rekening' => 'nullable|string|max:50',
                'nama_pemilik_rekening' => 'nullable|string|max:255',
                
                // Data Pajak & Legalitas
                'npwp' => 'nullable|string|max:20',
                'pkp' => 'nullable|string|max:50',
                'keterangan_pkp' => 'nullable|string',
                'surat_kuasa' => 'nullable|string|max:50',
                'keterangan_surat_kuasa' => 'nullable|string',
                'nama_yang_dikuasakan' => 'nullable|string|max:255',
                'nik_yang_dikuasakan' => 'nullable|string|max:20',
                'alamat_yang_dikuasakan' => 'nullable|string',
                
                // Tanggal Proses
                'tanggal_seleksi' => 'nullable|date',
                'tanggal_klasifikasi' => 'nullable|date',
                'tanggal_penilaian' => 'nullable|date',
                'tanggal_penetapan' => 'nullable|date',
                'tanggal_pakta_integritas' => 'nullable|date',
                'tanggal_surat_permohonan' => 'nullable|date',
                
                // Data VMS & Kode Mitra
                'no_vms' => 'nullable|string|max:50',
                'kode_mitra' => 'nullable|string|max:50',
            ]);

            // Update mitra with validated data
            $mitra->update($validated);

            // Clear activity cache after update
            ActivityAggregatorService::clearAllActivitiesCache();

            return response()->json([
                'message' => 'Data mitra berhasil diperbarui',
                'data' => $mitra
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data mitra tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update mitra error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete mitras
     */
    public function bulkDelete(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'required|exists:data_mitra,id_mitra',
            ], [
                'ids.required' => 'Pilih minimal 1 mitra untuk dihapus',
                'ids.array' => 'Format data tidak valid',
                'ids.min' => 'Pilih minimal 1 mitra untuk dihapus',
                'ids.*.exists' => 'Salah satu mitra tidak ditemukan',
            ]);

            $ids = $validated['ids'];
            $count = count($ids);

            // Use transaction to ensure data consistency
            DB::transaction(function () use ($ids) {
                // Delete related records first (to avoid foreign key constraint violation)
                
                // 1. Delete from hasil_seleksi_mitras (if exists)
                DB::table('hasil_seleksi_mitra')
                    ->whereIn('id_mitra', $ids)
                    ->delete();

                // 2. Delete from klasifikasi_mitra_pangans (if exists)
                DB::table('data_klasifikasi_mitra')
                    ->whereIn('id_mitra', $ids)
                    ->delete();

                // 3. Delete from data_seleksi_mitras
                DB::table('data_seleksi_mitra')
                    ->whereIn('id_mitra', $ids)
                    ->delete();

                // 4. Finally, delete from data_mitras
                DataMitra::whereIn('id_mitra', $ids)->delete();
            });

            // Clear activity cache
            ActivityAggregatorService::clearAllActivitiesCache();

            return response()->json([
                'message' => "Berhasil menghapus {$count} mitra dan data terkait",
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}


