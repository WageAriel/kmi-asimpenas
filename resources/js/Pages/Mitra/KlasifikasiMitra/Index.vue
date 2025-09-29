<!-- resources/js/Pages/Mitra/KlasifikasiMitra/Index.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { ref,onMounted, computed } from 'vue';
import axios from 'axios';

const classifications = ref([]); // List of classifications

onMounted(async () => {
    const response = await axios.get('/klasifikasi-mitra/my');
    classifications.value = response.data.map(item => ({
        ...item,
        nama_perusahaan: item.mitra?.nama_perusahaan ?? '-'
    }));
});

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

const showModal = ref(false);
const selectedClassification = ref(null);

const viewDetails = (classification) => {
    selectedClassification.value = classification;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedClassification.value = null;
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mitra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil Klasifikasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Skor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Download</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="classification in classifications" :key="classification.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ classification.nama_perusahaan}}</span>
                                </td>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button
                                        @click="window.location.href = `/mitra/pengajuan-seleksi/${submission.id}/download`"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 8l-4-4m4 4l4-4M4 20h16" />
                                        </svg>
                                        Download PDF
                                    </button>
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

        <!-- Modal Detail Klasifikasi Mitra -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-xl w-full h-auto p-6 relative max-h-[80vh] flex flex-col">
                <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <h2 class="text-xl font-bold mb-4 text-green-700">Detail Klasifikasi Mitra</h2>
                <div v-if="selectedClassification" class="overflow-y-auto flex-1 pr-2">
                    <!-- Info Utama -->
                    <div class="mb-4 grid grid-cols-2 gap-x-6 gap-y-2">
                        <div>
                            <span class="block text-sm text-gray-500">Nama Mitra</span>
                            <span class="font-semibold text-gray-900">{{ selectedClassification.nama_perusahaan }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Tahun</span>
                            <span class="font-semibold text-gray-900">{{ selectedClassification.year || currentYear }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Status</span>
                            <span class="font-semibold text-gray-900">{{ getStatusText(selectedClassification.status) }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Total Skor</span>
                            <span class="font-semibold text-gray-900">{{ selectedClassification.total_score }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Hasil Klasifikasi</span>
                            <span class="font-semibold text-gray-900">{{ selectedClassification.result }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Tanggal Submit</span>
                            <span class="font-semibold text-gray-900">{{ formatDate(selectedClassification.created_at) }}</span>
                        </div>
                    </div>
                    <hr class="my-4">

                    <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                        <!-- Kolom Kiri -->
                        <div>
                            <div class="mb-3 font-semibold text-green-700 pb-1">Pengeringan</div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pembersih Gabah</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pembersih_gabah }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Lantai Jemur</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.lantai_jemur }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pengering</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pengering }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Tangki Penyimpanan</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.alat_pengering_lainnya }}</span>
                            </div>

                            <div class="mb-3 font-semibold text-green-700 pb-1">Sarana Penyimpanan</div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Gudang Konvensional</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.gudang_konvensional }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Silo GKG/Hopper</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.silo_gkg_hopper }}</span>
                            </div>
                            <div class="mb-3 font-semibold text-green-700 pb-1">Sarana Angkut</div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Truk</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.truk }}</span>
                            </div>
                            <div class="mb-3 font-semibold text-green-700 pb-1">Kelengkapan Pemeriksaan</div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mini Lab</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mini_lab }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Moisture Tester</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.moisture_tester }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Pembanding Derajat Sosoh</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.pembanding_derajat_sosoh }}</span>
                            </div>
                            <div class="mb-3 font-semibold text-green-700 pb-1">Organisasi</div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Bagian Quality Control</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.bagian_quality_control }}</span>
                            </div>
                        </div>
                        <!-- Kolom Kiri -->
                        <div>
                            <div class="mb-3 font-semibold text-green-700 pb-1">Penggilingan</div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pembersih Awal</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pembersih_awal }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pemecah Kulit</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pemecah_kulit }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pembersih Sekam</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pembersih_sekam }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pemisah Gabah Pecah Kulit</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pemisah_gabah_pecah_kulit }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pemisah Batu</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pemisah_batu }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Penyosoh</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_penyosoh }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pengkabut</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pengkabut }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pemesah Menir</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pemesah_menir }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pemisah Katul</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pemisah_katul }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pemisah Berdasarkan Ukuran</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pemisah_berdasarkan_ukuran }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pemisah Berdasarkan Warna</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pemisah_berdasarkan_warna }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Tangki Penyimpanan</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.tangki_penyimpanan }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Pengemas</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_pengemas }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Mesin Jahit</span>
                                <span class="font-medium text-gray-900 block mt-1">{{ selectedClassification.mesin_jahit }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2 justify-end mt-6">
                        <button @click="closeModal" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Tutup</button>
                        <button 
                            @click="editClassification(selectedClassification)" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </MitraLayout>
</template>