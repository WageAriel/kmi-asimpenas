<script setup>
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
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

// View detail handler
const viewDetail = (item) => {
    selectedItem.value = item;
    showModal.value = true;
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
    <Head title="Daftar Hasil Seleksi Mitra - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Hasil Seleksi Mitra</h2>
                <p class="text-gray-600">Kelola klasifikasi mitra yang terdaftar di sistem.</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Lihat Detail
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
    </AdminLayout>
</template>