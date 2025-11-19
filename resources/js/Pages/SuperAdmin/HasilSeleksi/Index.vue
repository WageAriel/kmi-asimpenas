<script setup>
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const hasilSeleksiMitras = ref([]);
const isLoading = ref(true);
const errorMessage = ref(null);
const searchQuery = ref('');
const showModal = ref(false);
const selectedItem = ref(null);

// Fetch data from API
const fetchData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/api/hasil-seleksi-mitra');
        hasilSeleksiMitras.value = response.data;
    } catch (error) {
        console.error('Error fetching data:', error);
        errorMessage.value = 'Terjadi kesalahan saat mengambil data';
    } finally {
        isLoading.value = false;
    }
};

// Filter data based on search query
const filteredData = computed(() => {
    if (!searchQuery.value) return hasilSeleksiMitras.value;
    
    const query = searchQuery.value.toLowerCase();
    return hasilSeleksiMitras.value.filter(item => 
        item.mitra?.nama_perusahaan?.toLowerCase().includes(query) ||
        item.kesimpulan_akhir?.toLowerCase().includes(query)
    );
});

// Format date helper
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

// Get status badge styling
const getStatusBadge = (status) => {
    if (!status) return 'bg-gray-100 text-gray-800';
    
    switch(status.toLowerCase()) {
        case 'lolos':
            return 'bg-green-100 text-green-800';
        case 'tidak lolos':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-yellow-100 text-yellow-800';
    }
};

// Add new refs
const showPdfModal = ref(false);
const selectedItemForPdf = ref(null);
const selectedPelaksana = ref('');
const selectedPengetahui = ref('');
const karyawanList = ref([]);
const isGeneratingPdf = ref(false);

// Modify viewDetail function
const viewDetail = async (item) => {
    selectedItemForPdf.value = item;
    showPdfModal.value = true;
    try {
        const response = await axios.get('/api/karyawan');
        karyawanList.value = response.data;
    } catch (error) {
        console.error('Error fetching karyawan:', error);
    }
};

// Add function to generate PDF
const generatePdf = async () => {
    if (!selectedPelaksana.value || !selectedPengetahui.value) {
        alert('Silakan pilih kedua karyawan terlebih dahulu');
        return;
    }

    isGeneratingPdf.value = true;
    try {
        const response = await axios.get(
            `/super-admin/hasil-seleksi-mitra/${selectedItemForPdf.value.id_hasil_seleksi_mitra}/berita-acara`,
            {
                params: { 
                    id_pelaksana: selectedPelaksana.value,
                    id_pengetahui: selectedPengetahui.value
                },
                responseType: 'blob'
            }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `berita-acara-${selectedItemForPdf.value.mitra?.nama_perusahaan}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Terjadi kesalahan saat generate PDF');
    } finally {
        isGeneratingPdf.value = false;
        showPdfModal.value = false;
        selectedItemForPdf.value = null;
        selectedPelaksana.value = '';
        selectedPengetahui.value = '';
    }
};

// Close modal handler
const closeModal = () => {
    showModal.value = false;
    selectedItem.value = null;
};

// Export function
const exportData = () => {
    window.location.href = '/hasil-seleksi-mitra/export/data';
};


onMounted(() => {
    fetchData();
});
</script>

<template>
    <Head title="Hasil Seleksi - Super Admin" />

    <SuperAdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Hasil Seleksi</h2>
                <p class="text-gray-600">Kelola hasil seleksi mitra yang terdaftar di sistem.</p>
            </div>

            <!-- Quick Actions-->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 mb-4 rounded-lg shadow-md text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Daftar Hasil Seleksi Mitra</h3>
                        <p class="text-white">
                            Kelola hasil seleksi mitra yang terdaftar di sistem.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
                        <button
                            @click="exportData"
                            class="inline-flex items-center px-4 py-2 bg-white text-orange-600 rounded-lg hover:bg-orange-50 transition-colors duration-200 font-medium shadow-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-4">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-green-500"
                        placeholder="Cari klasifikasi mitra..."
                        v-model="searchQuery"
                    />
                    <button 
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        title="Hapus pencarian"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <span class="ml-2">Memuat data...</span>
            </div>

            <!-- Error Message -->
            <div v-else-if="errorMessage" class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                {{ errorMessage }}
            </div>

            <!-- Data Table -->
            <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mitra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Evaluasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil Akhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil Seleksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in filteredData" :key="item.id_hasil_seleksi_mitra" class="hover:bg-gray-50">
                            <td class="text-xs px-6 py-4 whitespace-nowrap">{{ item.mitra?.nama_perusahaan || '-' }}</td>
                            <td class="text-xs px-6 py-4 whitespace-nowrap">{{ formatDate(item.created_at) }}</td>
                            <td class="text-xs px-6 py-4 whitespace-nowrap">
                                <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(item.kesimpulan_dokumen)]">
                                    {{ item.kesimpulan_dokumen || 'Belum Ada' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(item.kesimpulan_akhir)]">
                                    {{ item.kesimpulan_akhir || 'Belum Ada' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <button 
                                    @click="viewDetail(item)"
                                    class="inline-flex items-center px-2 py-1 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded transition-colors duration-200 text-xs"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div v-if="filteredData.length === 0" class="text-center py-12">
                    <p class="text-gray-500">
                        {{ searchQuery ? 'Tidak ada data yang sesuai dengan pencarian.' : 'Belum ada data hasil seleksi.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Add this before closing AdminLayout -->
        <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative">
                <button @click="showPdfModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <h2 class="text-xl font-bold mb-6">Generate Berita Acara</h2>

                <div class="space-y-6">
                    <!-- Pelaksana Seleksi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Pelaksana Seleksi
                        </label>
                        <select 
                            v-model="selectedPelaksana"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            <option value="">Pilih Karyawan</option>
                            <option v-for="karyawan in karyawanList" 
                                    :key="karyawan.id_karyawan" 
                                    :value="karyawan.id_karyawan">
                                {{ karyawan.nama_karyawan }} - {{ karyawan.jabatan }}
                            </option>
                        </select>
                    </div>

                    <!-- Yang Mengetahui -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Yang Mengetahui
                        </label>
                        <select 
                            v-model="selectedPengetahui"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            <option value="">Pilih Karyawan</option>
                            <option v-for="karyawan in karyawanList" 
                                    :key="karyawan.id_karyawan" 
                                    :value="karyawan.id_karyawan">
                                {{ karyawan.nama_karyawan }} - {{ karyawan.jabatan }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6">
                    <button 
                        @click="showPdfModal = false"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium text-sm"
                    >
                        Batal
                    </button>
                    <button
                        @click="generatePdf"
                        :disabled="!selectedPelaksana || !selectedPengetahui || isGeneratingPdf"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                    >
                        <svg v-if="isGeneratingPdf" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isGeneratingPdf ? 'Generating...' : 'Generate PDF' }}
                    </button>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>