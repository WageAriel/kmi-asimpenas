<?php

namespace App\Http\Controllers;

use App\Models\DataMitra;
use App\Models\DataSeleksiMitra;
use App\Models\KlasifikasiMitra;
use App\Models\HasilSeleksiMitra;
use App\Models\User;
use App\Services\ActivityAggregatorService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function adminDashboard()
    {
        // Get statistics
        $stats = [
            'total_mitra' => DataMitra::count(),
            'total_seleksi' => DataSeleksiMitra::count(),
            'total_klasifikasi' => KlasifikasiMitra::count(),
            'pending_seleksi' => DataSeleksiMitra::where('status_seleksi', 'pending')->count(),
        ];

        // Get recent activities (last 10)
        $recentActivities = ActivityAggregatorService::getRecentActivities(10);

        // Get latest submissions (last 10)
        $latestSubmissions = DataSeleksiMitra::with('mitra')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($seleksi) {
                return [
                    'id' => $seleksi->id_seleksi_mitra,
                    'mitra_name' => $seleksi->mitra->nama_perusahaan ?? 'N/A',
                    'type' => 'Pengajuan Seleksi',
                    'status' => $seleksi->status_seleksi ?? 'pending',
                    'created_at' => $seleksi->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'latestSubmissions' => $latestSubmissions,
        ]);
    }

    /**
     * Display super admin dashboard with comprehensive statistics
     */
    public function superAdminDashboard()
    {
        // Get comprehensive statistics
        $stats = [
            'total_users' => User::count(),
            'total_admin' => User::where('role', 'admin')->count(),
            'total_mitra' => DataMitra::count(),
            'total_seleksi' => DataSeleksiMitra::count(),
            'total_klasifikasi' => KlasifikasiMitra::count(),
            'pending_seleksi' => DataSeleksiMitra::where('status_seleksi', 'pending')->count(),
        ];

        // Get recent activities (last 10)
        $recentActivities = ActivityAggregatorService::getRecentActivities(10);

        // Get latest submissions (last 10)
        $latestSubmissions = DataSeleksiMitra::with('mitra')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($seleksi) {
                return [
                    'id' => $seleksi->id_seleksi_mitra,
                    'mitra_name' => $seleksi->mitra->nama_perusahaan ?? 'N/A',
                    'type' => 'Pengajuan Seleksi',
                    'status' => $seleksi->status_seleksi ?? 'pending',
                    'created_at' => $seleksi->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'latestSubmissions' => $latestSubmissions,
        ]);
    }

    /**
     * Display mitra dashboard with statistics
     */
    public function mitraDashboard()
    {
        $userId = auth()->id();
        $mitra = DataMitra::where('user_id', $userId)->first();

        if (!$mitra) {
            return Inertia::render('Mitra/Dashboard', [
                'statistik' => [
                    'pengajuan_total' => 0,
                    'pengajuan_approved' => 0,
                    'pengajuan_pending' => 0,
                    'po_aktif' => 0
                ],
                'message' => 'Silakan lengkapi data mitra Anda terlebih dahulu.'
            ]);
        }

        // Get statistics for this mitra
        $pengajuanTotal = DataSeleksiMitra::where('id_mitra', $mitra->id_mitra)->count();
        $pengajuanApproved = DataSeleksiMitra::where('id_mitra', $mitra->id_mitra)
            ->where('status_seleksi', 'lolos')
            ->count();
        $pengajuanPending = DataSeleksiMitra::where('id_mitra', $mitra->id_mitra)
            ->where('status_seleksi', 'pending')
            ->count();

        // Get active purchase orders (you can adjust this based on your PO model)
        $poAktif = 0; // TODO: Implement when PO integration is ready

        return Inertia::render('Mitra/Dashboard', [
            'statistik' => [
                'pengajuan_total' => $pengajuanTotal,
                'pengajuan_approved' => $pengajuanApproved,
                'pengajuan_pending' => $pengajuanPending,
                'po_aktif' => $poAktif
            ],
            'mitra' => $mitra
        ]);
    }

    /**
     * Get dashboard statistics (API endpoint)
     */
    public function getStats()
    {
        $stats = [
            'total_mitra' => DataMitra::count(),
            'total_seleksi' => DataSeleksiMitra::count(),
            'total_klasifikasi' => KlasifikasiMitra::count(),
            'pending_seleksi' => DataSeleksiMitra::where('status_seleksi', 'pending')->count(),
            'approved_seleksi' => DataSeleksiMitra::where('status_seleksi', 'lolos')->count(),
            'rejected_seleksi' => DataSeleksiMitra::where('status_seleksi', 'tidak lolos')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData()
    {
        // Get seleksi data by status
        $seleksiByStatus = DataSeleksiMitra::selectRaw('status_seleksi, COUNT(*) as count')
            ->groupBy('status_seleksi')
            ->get()
            ->pluck('count', 'status_seleksi');

        // Get klasifikasi data by hasil
        $klasifikasiByHasil = KlasifikasiMitra::selectRaw('hasil_klasifikasi, COUNT(*) as count')
            ->whereNotNull('hasil_klasifikasi')
            ->groupBy('hasil_klasifikasi')
            ->get()
            ->pluck('count', 'hasil_klasifikasi');

        // Get monthly registration data (last 6 months)
        $monthlyData = DataMitra::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        return response()->json([
            'seleksiByStatus' => $seleksiByStatus,
            'klasifikasiByHasil' => $klasifikasiByHasil,
            'monthlyData' => $monthlyData,
        ]);
    }
}
