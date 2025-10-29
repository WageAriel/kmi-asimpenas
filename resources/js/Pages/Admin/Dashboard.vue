<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Define props to receive data from controller
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_mitra: 0,
            total_seleksi: 0,
            total_klasifikasi: 0,
            pending_seleksi: 0
        })
    },
    recentActivities: {
        type: Array,
        default: () => []
    },
    latestSubmissions: {
        type: Array,
        default: () => []
    }
});

// Use reactive refs from props
const stats = ref(props.stats);
const recentActivities = ref(props.recentActivities);
const latestSubmissions = ref(props.latestSubmissions);

// Format status badge text
const getStatusText = (status) => {
    const statusMap = {
        'pending': 'Pending',
        'lolos': 'Lolos',
        'tidak lolos': 'Tidak Lolos',
        'approved': 'Disetujui',
        'rejected': 'Ditolak'
    };
    return statusMap[status] || status;
};

// Get icon for activity type
const getActivityIcon = (type) => {
    const icons = {
        'mitra_registered': 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'seleksi_submitted': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'klasifikasi_submitted': 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        'default': 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    return icons[type] || icons.default;
};

onMounted(() => {
    // Data already loaded from props
    console.log('Dashboard loaded with stats:', stats.value);
});
</script>

<template>
    <Head title="Dashboard Admin - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Dashboard</h2>
                <p class="text-gray-600">Overview sistem ASIMPENAS</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Mitra -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Mitra</p>
                            <p class="text-2xl font-semibold">{{ stats.total_mitra }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pengajuan Seleksi -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Seleksi</p>
                            <p class="text-2xl font-semibold">{{ stats.total_seleksi }}</p>
                        </div>
                    </div>
                </div>

                <!-- Klasifikasi -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Klasifikasi</p>
                            <p class="text-2xl font-semibold">{{ stats.total_klasifikasi }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Review -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Menunggu Review</p>
                            <p class="text-2xl font-semibold">{{ stats.pending_seleksi }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activities -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Aktivitas Terbaru</h3>
                    <div v-if="recentActivities.length > 0" class="space-y-4">
                        <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActivityIcon(activity.type)" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ activity.title }}</p>
                                <p class="text-sm text-gray-500">{{ activity.description }}</p>
                                <p class="text-xs text-gray-400">{{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <p>Belum ada aktivitas</p>
                    </div>
                </div>

                <!-- Latest Submissions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Pengajuan Terbaru</h3>
                    <div v-if="latestSubmissions.length > 0" class="space-y-4">
                        <div v-for="submission in latestSubmissions" :key="submission.id" 
                             class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                            <div>
                                <p class="font-medium">{{ submission.mitra_name }}</p>
                                <p class="text-sm text-gray-500">{{ submission.type }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ submission.created_at }}</p>
                            </div>
                            <div>
                                <span :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full',
                                    submission.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    submission.status === 'lolos' ? 'bg-green-100 text-green-800' :
                                    'bg-red-100 text-red-800'
                                ]">
                                    {{ getStatusText(submission.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <p>Belum ada pengajuan</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>