<!-- resources/js/Pages/Mitra/PengajuanSeleksi/Index.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    submissions: {
        type: Array,
        default: () => []
    }
});

const currentYear = new Date().getFullYear();

// Computed untuk menentukan apakah bisa renewal
const canRenew = computed(() => {
    const lastApproved = props.submissions
        .filter(s => s.status === 'approved')
        .sort((a, b) => (b.year || 2024) - (a.year || 2024))[0];
    
    return lastApproved && (currentYear - (lastApproved.year || 2024)) >= 1;
});

const hasCurrentYearSubmission = computed(() => {
    return props.submissions.some(s => (s.year || 2025) === currentYear);
});

// Helper functions
const getStatusClass = (status) => {
    switch(status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'approved': return 'bg-green-100 text-green-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        case 'draft': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusText = (status) => {
    switch(status) {
        case 'pending': return 'Menunggu Review';
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        case 'draft': return 'Draft';
        default: return 'Belum Submit';
    }
};

const calculateCompletionPercentage = (submission) => {
    const fields = [
        submission.surat_permohonan, submission.akta_notaris, submission.nib, submission.ktp,
        submission.no_rekening, submission.npwp, submission.surat_kuasa,
        submission.lantai_jemur, submission.sarana_lainnya,
        submission.mesin_memecah_kulit, submission.mesin_pemisah_gabah,
        submission.mesin_penyosoh, submission.alat_pemisah_beras
    ];
    const adaCount = fields.filter(field => field === 'ada').length;
    return Math.round((adaCount / fields.length) * 100);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric', month: 'short', day: 'numeric'
    });
};

// Action functions
const goToForm = (type = 'new') => {
    window.location.href = `/mitra/pengajuan-seleksi/form?type=${type}&year=${currentYear}`;
};

const viewDetails = (submission) => {
    window.location.href = `/mitra/pengajuan-seleksi/${submission.id}`;
};

const editSubmission = (submission) => {
    window.location.href = `/mitra/pengajuan-seleksi/form?edit=${submission.id}`;
};
</script>

<template>
    <Head title="Pengajuan Seleksi Mitra - ASIMPENAS" />

    <MitraLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Pengajuan Seleksi Mitra</h2>
                <p class="text-gray-600">Kelola pengajuan seleksi dan registrasi ulang tahunan Anda</p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-xl font-bold text-white mb-2">Pengajuan Tahun {{ currentYear }}</h3>
                        <p class="text-blue-100">
                            <span v-if="hasCurrentYearSubmission">Anda sudah memiliki pengajuan untuk tahun ini</span>
                            <span v-else>Belum ada pengajuan untuk tahun ini</span>
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button
                            v-if="!hasCurrentYearSubmission"
                            @click="goToForm('new')"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Pengajuan Baru {{ currentYear }}
                        </button>
                        
                        <button
                            v-if="canRenew && !hasCurrentYearSubmission"
                            @click="goToForm('renewal')"
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                            Registrasi Ulang {{ currentYear }}
                        </button>

                        <div v-if="hasCurrentYearSubmission" class="flex items-center px-4 py-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5 text-white mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-white text-sm font-medium">Pengajuan {{ currentYear }} Sudah Ada</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Riwayat Pengajuan Seleksi</h3>
                </div>
                
                <div v-if="submissions.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelengkapan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Submit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="submission in submissions" :key="submission.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ submission.year || currentYear }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        submission.type === 'renewal' 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-blue-100 text-blue-800'
                                    ]">
                                        {{ submission.type === 'renewal' ? 'Registrasi Ulang' : 'Pengajuan Baru' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        getStatusClass(submission.status)
                                    ]">
                                        {{ getStatusText(submission.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                            <div :class="[
                                                'h-2 rounded-full',
                                                calculateCompletionPercentage(submission) >= 80 ? 'bg-green-500' :
                                                calculateCompletionPercentage(submission) >= 60 ? 'bg-yellow-500' : 'bg-red-500'
                                            ]" :style="`width: ${calculateCompletionPercentage(submission)}%`"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ calculateCompletionPercentage(submission) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(submission.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button 
                                            @click="viewDetails(submission)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                        >
                                            Lihat
                                        </button>
                                        <button 
                                            v-if="submission.status === 'draft'"
                                            @click="editSubmission(submission)"
                                            class="text-green-600 hover:text-green-900 transition-colors"
                                        >
                                            Lanjutkan
                                        </button>
                                        <button 
                                            v-if="submission.status === 'approved'"
                                            class="text-purple-600 hover:text-purple-900 transition-colors"
                                        >
                                            Sertifikat
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengajuan</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat pengajuan seleksi mitra baru.</p>
                    <div class="mt-6">
                        <button 
                            @click="goToForm('new')"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Buat Pengajuan Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MitraLayout>
</template>