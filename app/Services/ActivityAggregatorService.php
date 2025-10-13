<?php
// File: app/Services/ActivityAggregatorService.php

namespace App\Services;

use App\Models\DataMitra;
use App\Models\DataSeleksiMitra;
use App\Models\KlasifikasiMitra;
use Illuminate\Support\Facades\Cache;

class ActivityAggregatorService
{
    /**
     * Aggregate aktivitas dari berbagai tabel
     */
    public static function getUserActivities($userId, $limit = 10)
    {
        // Cache untuk 5 menit agar tidak query terus-menerus
        $cacheKey = "user_activities_aggregated:{$userId}";
        
        return Cache::remember($cacheKey, 300, function() use ($userId, $limit) {
            $activities = [];

            // Ambil mitra user
            $mitra = DataMitra::where('user_id', $userId)->first();
            
            if (!$mitra) {
                return [];
            }

            // 1. Aktivitas Data Mitra - Created
            if ($mitra->created_at) {
                $activities[] = [
                    'id' => 'mitra_created_' . $mitra->id_mitra,
                    'type' => 'mitra_created',
                    'title' => 'Data Mitra Berhasil Dibuat',
                    'description' => 'Data mitra ' . $mitra->nama_lengkap . ' telah berhasil didaftarkan',
                    'status' => 'completed',
                    'date' => $mitra->created_at->toISOString(),
                    'formatted_date' => $mitra->created_at->format('d M Y H:i'),
                    'admin_note' => null,
                    'performed_by' => null,
                    'metadata' => [
                        'mitra_id' => $mitra->id_mitra,
                        'mitra_name' => $mitra->nama_lengkap
                    ]
                ];
            }

            // 2. Aktivitas Data Mitra - Updated (jika ada perubahan)
            if ($mitra->updated_at && $mitra->updated_at->gt($mitra->created_at->addMinutes(1))) {
                $activities[] = [
                    'id' => 'mitra_updated_' . $mitra->id_mitra . '_' . $mitra->updated_at->timestamp,
                    'type' => 'mitra_updated',
                    'title' => 'Data Mitra Diperbarui',
                    'description' => 'Informasi mitra ' . $mitra->nama_lengkap . ' telah diperbarui',
                    'status' => 'completed',
                    'date' => $mitra->updated_at->toISOString(),
                    'formatted_date' => $mitra->updated_at->format('d M Y H:i'),
                    'admin_note' => 'Perubahan data telah disimpan',
                    'performed_by' => null,
                    'metadata' => [
                        'mitra_id' => $mitra->id_mitra,
                        'mitra_name' => $mitra->nama_lengkap
                    ]
                ];
            }

            // 3. Aktivitas Seleksi Mitra
            $seleksis = DataSeleksiMitra::where('id_mitra', $mitra->id_mitra)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($seleksis as $seleksi) {
                // Submit seleksi
                $activities[] = [
                    'id' => 'seleksi_submitted_' . $seleksi->id_seleksi_mitra,
                    'type' => 'seleksi_submitted',
                    'title' => 'Pengajuan Seleksi Mitra Disubmit',
                    'description' => 'Dokumen pengajuan seleksi telah berhasil dikirim untuk diverifikasi',
                    'status' => $seleksi->status_seleksi === 'pending' ? 'pending' : 'completed',
                    'date' => $seleksi->created_at->toISOString(),
                    'formatted_date' => $seleksi->created_at->format('d M Y H:i'),
                    'admin_note' => $seleksi->status_seleksi === 'pending' 
                        ? 'Sedang dalam tahap verifikasi dokumen' 
                        : null,
                    'performed_by' => null,
                    'metadata' => [
                        'id_seleksi_mitra' => $seleksi->id_seleksi_mitra,
                        'status' => $seleksi->status_seleksi
                    ]
                ];

                // Hasil seleksi (jika sudah ada status)
                if ($seleksi->status_seleksi !== 'pending') {
                    $isLolos = strtolower($seleksi->status_seleksi) === 'lolos';
                    
                    $activities[] = [
                        'id' => 'seleksi_result_' . $seleksi->id_seleksi_mitra,
                        'type' => $isLolos ? 'seleksi_approved' : 'seleksi_rejected',
                        'title' => $isLolos 
                            ? 'Selamat! Pengajuan Seleksi Disetujui' 
                            : 'Pengajuan Seleksi Tidak Lolos',
                        'description' => $isLolos
                            ? 'Selamat! Pengajuan seleksi mitra Anda telah disetujui'
                            : 'Pengajuan seleksi mitra Anda belum memenuhi kriteria',
                        'status' => $isLolos ? 'approved' : 'rejected',
                        'date' => $seleksi->updated_at->toISOString(),
                        'formatted_date' => $seleksi->updated_at->format('d M Y H:i'),
                        'admin_note' => $isLolos 
                            ? 'Memenuhi semua kriteria seleksi. Silakan lanjutkan ke tahap klasifikasi'
                            : 'Mohon lengkapi dokumen yang kurang dan ajukan kembali',
                        'performed_by' => 'Admin ASIMPENAS',
                        'metadata' => [
                            'id_seleksi_mitra' => $seleksi->id_seleksi_mitra,
                            'status' => $seleksi->status_seleksi
                        ]
                    ];
                }
            }

            // 4. Aktivitas Klasifikasi Mitra
            $klasifikasis = KlasifikasiMitra::where('id_mitra', $mitra->id_mitra)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($klasifikasis as $klasifikasi) {
                // Submit klasifikasi
                $activities[] = [
                    'id' => 'klasifikasi_submitted_' . $klasifikasi->id,
                    'type' => 'klasifikasi_submitted',
                    'title' => 'Pengajuan Klasifikasi Mitra',
                    'description' => 'Dokumen klasifikasi mitra telah disubmit untuk evaluasi',
                    'status' => !empty($klasifikasi->hasil_klasifikasi) ? 'completed' : 'pending',
                    'date' => $klasifikasi->created_at->toISOString(),
                    'formatted_date' => $klasifikasi->created_at->format('d M Y H:i'),
                    'admin_note' => !empty($klasifikasi->hasil_klasifikasi)
                        ? null
                        : 'Sedang dalam tahap verifikasi peralatan dan fasilitas',
                    'performed_by' => null,
                    'metadata' => [
                        'id_klasifikasi' => $klasifikasi->id
                    ]
                ];

                // Hasil klasifikasi (jika sudah ada)
                if (!empty($klasifikasi->hasil_klasifikasi)) {
                    $activities[] = [
                        'id' => 'klasifikasi_completed_' . $klasifikasi->id,
                        'type' => 'klasifikasi_completed',
                        'title' => 'Hasil Klasifikasi Tersedia',
                        'description' => 'Proses klasifikasi mitra telah selesai',
                        'status' => 'approved',
                        'date' => $klasifikasi->updated_at->toISOString(),
                        'formatted_date' => $klasifikasi->updated_at->format('d M Y H:i'),
                        'admin_note' => 'Hasil klasifikasi: ' . $klasifikasi->hasil_klasifikasi,
                        'performed_by' => 'Admin ASIMPENAS',
                        'metadata' => [
                            'id_klasifikasi' => $klasifikasi->id,
                            'hasil_klasifikasi' => $klasifikasi->hasil_klasifikasi
                        ]
                    ];
                }
            }

            // Sort by date descending (terbaru di atas)
            usort($activities, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            // Limit results
            return array_slice($activities, 0, $limit);
        });
    }

    /**
     * Clear cache untuk user tertentu
     */
    public static function clearUserCache($userId)
    {
        $cacheKey = "user_activities_aggregated:{$userId}";
        Cache::forget($cacheKey);
    }

    /**
     * Refresh cache setelah update data
     */
    public static function refreshUserActivities($userId, $limit = 10)
    {
        self::clearUserCache($userId);
        return self::getUserActivities($userId, $limit);
    }
}