<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// Props dari backend (route)
const props = defineProps({
    submissions: {
        type: Array,
        default: () => []
    },
    announcements: {
        type: Array,
        default: () => []
    },
    mitra: {
        type: Object,
        default: () => ({})
    }
});

// Data perusahaan mitra
const mitraInfo = ref({
    name: 'PT. Sejahtera Abadi',
    type: 'Mitra Penggilingan Padi',
    status: 'approved',
    registration_year: 2024
});

const urgentNotifications = ref([]);

onMounted(async () => {
    try {
        const response = await axios.get('/data-mitra/my');
        const mitraData = response.data;

        // Kalau data mitra ada, simpan ke ref
        if (mitraData && mitraData.id) {
            mitraInfo.value = mitraData;
        } 
    } catch (error) {
        // Kalau error 404 -> data mitra belum ada
        if (error.response && error.response.status === 404) {
            urgentNotifications.value.push({
                id: 1,
                type: 'warning',
                title: 'Data Mitra Diperlukan',
                message: 'Lengkapi data mitra terlebih dahulu sebelum mengajukan Puschase Order (PO)',
                action: 'Lengkapi Data',
                route: 'input-data-mitra',
                urgent: true
            });
        }
    }
});


// Notifikasi urgent
// const urgentNotifications = ref([
//     {
//         id: 1,
//         type: 'warning',
//         title: 'Data Mitra Diperlukan',
//         message: 'Lengkapi data mitra terlebih dahulu sebelum mengajukan Puschase Order (PO)',
//         action: 'Lengkapi Data',
//         urgent: true
//     },
//     {
//         id: 2,
//         type: 'info',
//         title: 'Hasil Seleksi Tersedia',
//         message: 'Hasil seleksi mitra periode 2025 telah dipublikasikan. Cek status pengajuan Anda.',
//         action: 'Lihat Hasil',
//         urgent: false
//     }
// ]);

// Data dummy untuk testing
const statistik = ref({
    pengajuan_total: 3,
    pengajuan_approved: 1,
    pengajuan_pending: 2,
    po_aktif: 5
});

const currentYear = new Date().getFullYear();

// Helper functions
const getStatusClass = (status) => {
    switch(status) {
        case 'approved': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'completed': return 'bg-blue-100 text-blue-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        case 'not_submitted': return 'bg-gray-100 text-gray-800';
        case 'action_required': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusText = (status) => {
    switch(status) {
        case 'approved': return 'Lolos Seleksi';
        case 'pending': return 'Sedang Diverifikasi';
        case 'rejected': return 'Tidak Lolos';
        case 'not_submitted': return 'Belum Mengajukan';
        default: return 'Status Tidak Diketahui';
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const dismissNotification = (notificationId) => {
    const index = urgentNotifications.value.findIndex(n => n.id === notificationId);
    if (index > -1) {
        urgentNotifications.value.splice(index, 1);
    }
};

// Quick actions - Updated
const quickActions = ref([
    {
        title: 'Ajukan Seleksi',
        description: 'Daftarkan perusahaan sebagai mitra ASIMPENAS',
        icon: 'plus',
        color: 'blue',
        route: 'mitra.pengajuan-seleksi.index',
        params: { type: 'new', year: currentYear }
    },
    {
        title: 'Lihat Hasil Seleksi',
        description: 'Cek status dan hasil seleksi terbaru',
        icon: 'document-text',
        color: 'purple',
        route: 'mitra.hasil-seleksi'
    },
    {
        title: 'Ajukan Klasifikasi',
        description: 'Ajukan klasifikasi setelah dinyatakan lolos',
        icon: 'clock',
        color: 'gray',
        route: 'mitra.klasifikasi-mitra.index',
        params: { type: 'new', year: currentYear }
    },
    {
        title: 'Update Data Mitra',
        description: 'Perbarui informasi dan dokumen perusahaan',
        icon: 'edit',
        color: 'green',
        route: 'input-data-mitra',
        params: { type: 'update' }
    }
]);

// Recent activities dummy data
const recentActivities = ref([
    {
        id: 1,
        type: 'submission',
        title: 'Pengajuan Seleksi 2025 Disubmit',
        description: 'Dokumen pengajuan telah berhasil dikirim untuk diverifikasi',
        status: 'pending',
        date: '2025-01-15',
        admin_note: 'Sedang dalam tahap verifikasi dokumen'
    },
    {
        id: 2,
        type: 'selection_result',
        title: 'Hasil Seleksi Disetujui',
        description: 'Selamat! Pengajuan seleksi mitra Anda telah disetujui',
        status: 'approved',
        date: '2025-02-10',
        admin_note: 'Memenuhi semua kriteria seleksi. Silakan lanjutkan ke tahap klasifikasi'
    },
    {
        id: 3,
        type: 'classification_submission',
        title: 'Pengajuan Klasifikasi Mitra',
        description: 'Dokumen klasifikasi mitra telah disubmit untuk evaluasi',
        status: 'pending',
        date: '2025-02-15',
        admin_note: 'Sedang dalam tahap verifikasi peralatan dan fasilitas'
    },
]);

const goToAction = (action) => {
    if (action.route) {
        window.location.href = route(action.route, action.params ?? {});
    }
};

</script>

<template>
    <Head title="Dashboard Mitra - ASIMPENAS" />

    <MitraLayout>
        <!-- Dashboard Content -->
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Welcome Message -->
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-lg p-6 mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between">
                        <div class="mb-4 lg:mb-0">
                            <h1 class="text-2xl font-bold text-white mb-3">
                                Selamat datang, {{ mitraInfo.name }}
                            </h1>
                            <p class="text-blue-100 text-sm">
                                {{ mitraInfo.type }} • Mitra sejak {{ mitraInfo.registration_year }}
                            </p>
                        </div>
                        <div class="hidden lg:block">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Urgent Notifications -->
                <div v-if="urgentNotifications.length > 0" class="mb-6">
                    <div v-for="notification in urgentNotifications" :key="notification.id"
                         :class="[
                             'p-6 rounded-xl border-l-4 mb-4 shadow-sm',
                             notification.type === 'warning' ? 'bg-red-50 border-red-400' : 'bg-blue-50 border-blue-400'
                         ]">
                        <div class="flex justify-between items-start">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg v-if="notification.type === 'warning'" class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg v-else class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 :class="[
                                        'text-base font-semibold',
                                        notification.type === 'warning' ? 'text-red-800' : 'text-blue-800'
                                    ]">{{ notification.title }}</h3>
                                    <p :class="[
                                        'text-sm mt-1',
                                        notification.type === 'warning' ? 'text-red-700' : 'text-blue-700'
                                    ]">{{ notification.message }}</p>
                                    <div class="mt-4">
                                        <button :class="[
                                            'text-sm font-medium px-4 py-2 rounded-lg transition-colors',
                                            notification.type === 'warning' 
                                                ? 'bg-red-100 text-red-800 hover:bg-red-200' 
                                                : 'bg-blue-100 text-blue-800 hover:bg-blue-200'
                                        ]">
                                            {{ notification.action }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button @click="dismissNotification(notification.id)" class="flex-shrink-0 ml-4 p-1 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Pengajuan -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Total Pengajuan</h3>
                                <p class="text-2xl font-semibold text-blue-600">{{ statistik.pengajuan_total }}</p>
                                <p class="text-xs text-gray-500 mt-1">Seluruh periode</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pengajuan Disetujui -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Disetujui</h3>
                                <p class="text-2xl font-semibold text-green-600">{{ statistik.pengajuan_approved }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ statistik.pengajuan_pending }} pending</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Klasifikasi -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Hasil Klasifikasi</h3>
                                <p class="text-2xl font-semibold text-orange-600">Lolos</p>
                                <p class="text-xs text-gray-500 mt-1">Terverifikasi</p>
                            </div>
                        </div>
                    </div>

                    <!-- PO Aktif -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm font-medium text-gray-900 mb-1">PO Aktif</h3>
                                <p class="text-2xl font-semibold text-purple-600">{{ statistik.po_aktif }}</p>
                                <p class="text-xs text-gray-500 mt-1">Purchase Order</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="bg-gradient-to-r from-blue-300 via-blue-500 to-blue-700 rounded-2xl shadow-lg p-8 mb-8">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-white mb-3">Aksi Cepat</h3>
                        <p class="text-green-100 text-base">Shortcut ke halaman utama yang sering digunakan</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <button
                            v-for="action in quickActions"
                            :key="action.title"
                            @click="goToAction(action)"
                            :class="[
                                'bg-white rounded-xl p-6 text-left hover:shadow-xl transition-all duration-300 transform hover:scale-105',
                                'border-l-4',
                                action.color === 'blue' ? 'border-blue-500' :
                                action.color === 'green' ? 'border-yellow-500' :
                                action.color === 'purple' ? 'border-purple-500' : 'border-gray-500'
                            ]"
                        >
                            <div class="flex items-center mb-4">
                                <div :class="[
                                    'w-12 h-12 rounded-xl flex items-center justify-center mr-4 shadow-sm',
                                    action.color === 'blue' ? 'bg-blue-100' :
                                    action.color === 'green' ? 'bg-yellow-100' :
                                    action.color === 'purple' ? 'bg-purple-100' : 'bg-gray-100'
                                ]">
                                    <svg :class="[
                                        'w-6 h-6',
                                        action.color === 'blue' ? 'text-blue-600' :
                                        action.color === 'green' ? 'text-yellow-600' :
                                        action.color === 'purple' ? 'text-purple-600' : 'text-gray-600'
                                    ]" fill="currentColor" viewBox="0 0 20 20">
                                        <path v-if="action.icon === 'plus'" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                        <path v-else-if="action.icon === 'edit'" d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        <path v-else-if="action.icon === 'document-text'" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z M9 16a1 1 0 100 2h2a1 1 0 100-2H9zm-5-2a2 2 0 01-2-2V6a2 2 0 012-2h1v1a3 3 0 006 0V4h1a2 2 0 012 2v8a2 2 0 01-2 2H4zm8-8a1 1 0 100 2h3a1 1 0 100-2h-3zm0 4a1 1 0 100 2h3a1 1 0 100-2h-3z"/>
                                        <path v-else d="M10 12a2 2 0 100-4 2 2 0 000 4z M10 2C5.582 2 2 5.582 2 10s3.582 8 8 8 8-3.582 8-8-3.582-8-8-8zm0 14c-3.314 0-6-2.686-6-6s2.686-6 6-6 6 2.686 6 6-2.686 6-6 6z"/>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2 text-base">{{ action.title }}</h4>
                            <p class="text-sm text-gray-600">{{ action.description }}</p>
                        </button>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Recent Activities -->
                    <div class="xl:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                            <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-gray-900">Riwayat Aktivitas Terbaru</h3>
                                <button class="text-sm text-blue-600 hover:text-blue-800 font-medium px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors">
                                    Lihat Semua
                                </button>
                            </div>
                            <div class="p-8">
                                <div class="space-y-6">
                                    <div v-for="activity in recentActivities" :key="activity.id" 
                                         class="flex items-start p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex-shrink-0 mr-6">
                                            <div :class="[
                                                'w-10 h-10 rounded-xl flex items-center justify-center shadow-sm',
                                                activity.status === 'completed' ? 'bg-green-100' :
                                                activity.status === 'pending' ? 'bg-yellow-100' : 'bg-red-100'
                                            ]">
                                                <svg :class="[
                                                    'w-5 h-5',
                                                    activity.status === 'completed' ? 'text-green-600' :
                                                    activity.status === 'pending' ? 'text-yellow-600' : 'text-red-600'
                                                ]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ activity.title }}</h4>
                                                <span :class="[
                                                    'px-3 py-1 text-xs font-medium rounded-full',
                                                    getStatusClass(activity.status)
                                                ]">
                                                    {{ activity.status === 'completed' ? 'Selesai' :
                                                       activity.status === 'pending' ? 'Pending' :
                                                       activity.status === 'action_required' ? 'Perlu Tindakan' : 'Diterima' }}
                                                </span>
                                            </div>
                                            <p class="text-gray-600 text-sm mb-2">{{ activity.description }}</p>
                                            <p v-if="activity.admin_note" class="text-xs text-blue-600 mb-2 italic">
                                                Admin: {{ activity.admin_note }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ formatDate(activity.date) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Panel -->
                    <div class="space-y-6">
                        <!-- Important Announcements -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                            <div class="px-6 py-5 border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900">Pengumuman Penting</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="p-5 bg-yellow-50 border-l-4 border-yellow-400 rounded-xl">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-base font-semibold text-yellow-800">Deadline Pengajuan 2025</h4>
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                            Urgent
                                        </span>
                                    </div>
                                    <p class="text-sm text-yellow-700 mb-3">Batas waktu pengajuan seleksi mitra 2025 adalah 31 Maret 2025.</p>
                                    <p class="text-xs text-gray-500">1 Februari 2025</p>
                                </div>
                                
                                <div class="p-5 bg-blue-50 border-l-4 border-blue-400 rounded-xl">
                                    <h4 class="text-base font-semibold text-blue-800 mb-2">Update Kebijakan Dokumen</h4>
                                    <p class="text-sm text-blue-700 mb-3">Mulai 2025, diperlukan tambahan dokumen sertifikat ISO untuk pengajuan baru.</p>
                                    <p class="text-xs text-gray-500">25 Januari 2025</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 shadow-sm">
                            <h3 class="text-base font-bold text-gray-900 mb-4">Butuh Bantuan?</h3>
                            <div class="space-y-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    <span class="font-medium">+62 21 1234 5678</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    <span class="font-medium">support@asimpenas.go.id</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MitraLayout>
</template>