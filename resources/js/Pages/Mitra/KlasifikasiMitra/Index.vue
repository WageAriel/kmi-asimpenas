<!-- resources/js/Pages/Mitra/KlasifikasiMitra/Index.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    classifications: {
        type: Array,
        default: () => []
    },
});

const currentYear = new Date().getFullYear();

const hasCurrentYearClassification = computed(() => {
    return props.classifications.some(c => (c.year || 2025) === currentYear);
});

// Helper functions  
const getStatusClass = (status) => {
    switch(status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'completed': return 'bg-green-100 text-green-800';
        case 'draft': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusText = (status) => {
    switch(status) {
        case 'pending': return 'Sedang Dinilai';
        case 'completed': return 'Selesai';
        case 'draft': return 'Draft';
        default: return 'Belum Submit';
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric', month: 'short', day: 'numeric'
    });
};

// Action functions
const goToForm = () => {
    window.location.href = `/mitra/klasifikasi-mitra/form?year=${currentYear}`;
};

const viewDetails = (classification) => {
    window.location.href = `/mitra/klasifikasi-mitra/${classification.id}`;
};

const editClassification = (classification) => {
    window.location.href = `/mitra/klasifikasi-mitra/form?edit=${classification.id}`;
};
</script>

<template>
    <Head title="Klasifikasi Mitra - ASIMPENAS" />

    <MitraLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Klasifikasi Mitra</h2>
                <p class="text-gray-600">Kelola klasifikasi dan penilaian kapabilitas mitra Anda</p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-xl font-bold text-white mb-2">Klasifikasi Tahun {{ currentYear }}</h3>
                        <p class="text-green-100">
                            <span v-if="!hasApprovedSubmission">Selesaikan pengajuan seleksi terlebih dahulu</span>
                            <span v-else-if="hasCurrentYearClassification">Anda sudah memiliki klasifikasi untuk tahun ini</span>
                            <span v-else>Siap untuk melakukan klasifikasi mitra</span>
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- v-if="hasApprovedSubmission && !hasCurrentYearClassification" -->
                        <button
                            @click="goToForm()"
                            class="inline-flex items-center px-6 py-3 bg-white text-green-600 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Klasifikasi Baru {{ currentYear }}
                        </button>

                        <!-- <div v-if="!hasApprovedSubmission" class="flex items-center px-4 py-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5 text-white mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-white text-sm font-medium">Perlu Approval Seleksi</span>
                        </div>

                        <div v-else-if="hasCurrentYearClassification" class="flex items-center px-4 py-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5 text-white mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-white text-sm font-medium">Klasifikasi {{ currentYear }} Sudah Ada</span>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Riwayat Klasifikasi Mitra</h3>
                </div>
                
                <div v-if="classifications.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil Klasifikasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Skor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="classification in classifications" :key="classification.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ classification.year || currentYear }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        getStatusClass(classification.status)
                                    ]">
                                        {{ getStatusText(classification.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="classification.result" :class="[
                                        'px-3 py-1 text-sm font-bold rounded-full',
                                        classification.result === 'UTAMA' ? 'bg-green-100 text-green-800' :
                                        classification.result === 'MADYA' ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-red-100 text-red-800'
                                    ]">
                                        {{ classification.result }}
                                    </span>
                                    <span v-else class="text-sm text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm">
                                        <span class="font-bold text-blue-600">{{ classification.total_score || 0 }}</span>
                                        <span class="text-gray-500">/75</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div :class="[
                                            'h-2 rounded-full',
                                            (classification.total_score || 0) >= 60 ? 'bg-green-500' :
                                            (classification.total_score || 0) >= 40 ? 'bg-yellow-500' : 'bg-red-500'
                                        ]" :style="`width: ${Math.min(((classification.total_score || 0) / 75) * 100, 100)}%`"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(classification.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button 
                                            @click="viewDetails(classification)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                        >
                                            Lihat
                                        </button>
                                        <button 
                                            v-if="classification.status === 'draft'"
                                            @click="editClassification(classification)"
                                            class="text-green-600 hover:text-green-900 transition-colors"
                                        >
                                            Lanjutkan
                                        </button>
                                        <button 
                                            v-if="classification.status === 'completed'"
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
                        <path d="M9 12h6m6 0h6m-6 6v6m-6-6v6m6-6v6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada klasifikasi</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        <span v-if="!hasApprovedSubmission">Selesaikan pengajuan seleksi terlebih dahulu untuk dapat melakukan klasifikasi.</span>
                        <span v-else>Mulai dengan membuat klasifikasi mitra baru.</span>
                    </p>
                    <div v-if="hasApprovedSubmission" class="mt-6">
                        <button 
                            @click="goToForm()"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Buat Klasifikasi Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MitraLayout>
</template>