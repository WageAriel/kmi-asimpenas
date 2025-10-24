<?php

namespace App\Http\Controllers;

use App\Models\DataMitra;
use Illuminate\Http\Request;
use App\Services\ActivityAggregatorService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Imports\DataMitraImport;
use App\Exports\DataMitraTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class DataMitraController extends Controller
{
    
    public function index()
    {
        $mitras = DataMitra::all();
        return response()->json($mitras);
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
            'nik' => 'nullable|string|max:30',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_telp_perusahaan' => 'nullable|string|max:20',
            'no_telp_cp' => 'nullable|string|max:20',
            'bank_korespondensi' => 'nullable|string|max:255',
            'alamat_bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:30',
            'status_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'pkp' => 'nullable|in:pkp,non pkp',
            'surat_kuasa' => 'nullable|in:ada,tidak ada',
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
            'nik' => 'nullable|string|max:30',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_telp_perusahaan' => 'nullable|string|max:20',
            'no_telp_cp' => 'nullable|string|max:20',
            'bank_korespondensi' => 'nullable|string|max:255',
            'alamat_bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:30',
            'status_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'pkp' => 'nullable|in:pkp,non pkp',
            'surat_kuasa' => 'nullable|in:ada,tidak ada',
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
    
        return response()->json($mitra);
    }

    public function destroy($id)
    {
        $mitra = DataMitra::findOrFail($id);
        $mitra->delete();
        return response()->json(['message' => 'Data mitra berhasil dihapus']);
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
     * Import data mitra from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new DataMitraImport();
            Excel::import($import, $request->file('file'));

            $summary = $import->getImportSummary();

            ActivityAggregatorService::clearUserCache(Auth::id());

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimport {$summary['imported']} data mitra",
                'summary' => $summary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        return Excel::download(new DataMitraTemplateExport(), 'template_data_mitra.xlsx');
    }
}
