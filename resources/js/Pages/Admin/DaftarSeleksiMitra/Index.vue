<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import axios from 'axios';

// Accept the data from the controller
const props = defineProps({
    seleksiMitras: {
        type: Array,
        default: () => []
    }
});

// Sort seleksi mitras by created_at (newest first)
const sortedSeleksiMitras = computed(() => {
    return [...props.seleksiMitras].sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at);
    });
});

// Add search functionality
const searchQuery = ref('');

// Filter seleksi mitras based on search query
const filteredSeleksiMitras = computed(() => {
    if (!searchQuery.value) return sortedSeleksiMitras.value;
    
    const query = searchQuery.value.toLowerCase();
    return sortedSeleksiMitras.value.filter(item => {
        return (
            item.mitra?.nama_perusahaan?.toLowerCase().includes(query) ||
            item.mitra?.no_telp_perusahaan?.toLowerCase().includes(query) ||
            item.mitra?.nama_cp?.toLowerCase().includes(query) ||
            (item.status_seleksi?.toLowerCase().includes(query))
        );
    });
});

// Modal state
const showModal = ref(false);
const selectedItem = ref(null);
const isLoading = ref(false);
const errorMessage = ref(null);
const successMessage = ref(null);

// Format the date values
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    });
};

const getStatusBadge = (val) => {
    return val === 'ada'
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
};

// Function to open modal instead of navigating
const lihatSeleksi = (item) => {
    selectedItem.value = item;
    showModal.value = true;
    errorMessage.value = null;
    successMessage.value = null;
};

// Function to close modal
const closeModal = () => {
    showModal.value = false;
    selectedItem.value = null;
};

// Function to update status seleksi to "lolos"
const approveSeleksi = async () => {
    if (!selectedItem.value || isLoading.value) return;
    
    isLoading.value = true;
    errorMessage.value = null;
    
    try {
        const response = await axios.put(`/data-seleksi-mitra/${selectedItem.value.id_seleksi_mitra}`, {
            id_mitra: selectedItem.value.id_mitra,
            status_seleksi: 'lolos'
        });
        
        // Update the status in the local data
        const index = props.seleksiMitras.findIndex(
            item => item.id_seleksi_mitra === selectedItem.value.id_seleksi_mitra
        );
        
        if (index !== -1) {
            props.seleksiMitras[index].status_seleksi = 'lolos';
        }
        
        successMessage.value = 'Status berhasil diubah menjadi LOLOS';
        
        // Optional: Close modal after short delay
        // setTimeout(() => closeModal(), 2000);
    } catch (error) {
        console.error('Error approving seleksi:', error);
        errorMessage.value = 'Gagal mengubah status. Silakan coba lagi.';
    } finally {
        isLoading.value = false;
    }
};

// Function to update status seleksi to "tidak lolos"
const rejectSeleksi = async () => {
    if (!selectedItem.value || isLoading.value) return;
    
    isLoading.value = true;
    errorMessage.value = null;
    
    try {
        const response = await axios.put(`/data-seleksi-mitra/${selectedItem.value.id_seleksi_mitra}`, {
            id_mitra: selectedItem.value.id_mitra,
            status_seleksi: 'tidak lolos'
        });
        
        // Update the status in the local data
        const index = props.seleksiMitras.findIndex(
            item => item.id_seleksi_mitra === selectedItem.value.id_seleksi_mitra
        );
        
        if (index !== -1) {
            props.seleksiMitras[index].status_seleksi = 'tidak lolos';
        }
        
        successMessage.value = 'Status berhasil diubah menjadi TIDAK LOLOS';
        
        // Optional: Close modal after short delay
        // setTimeout(() => closeModal(), 2000);
    } catch (error) {
        console.error('Error rejecting seleksi:', error);
        errorMessage.value = 'Gagal mengubah status. Silakan coba lagi.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Daftar Seleksi Mitra - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Seleksi Mitra</h2>
                <p class="text-gray-600">Kelola seleksi mitra yang terdaftar di sistem.</p>
            </div>

            <!-- Quick Actions Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-6 mb-4 rounded-lg shadow-md text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Daftar Seleksi Mitra</h3>
                        <p class="text-blue-100">
                            Berikut adalah daftar mitra yang melakukan seleksi di sistem ASIMPENAS.
                        </p>
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
                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari nama mitra..."
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

            <!-- Tabel Daftar Seleksi Mitra -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Nama Perusahaan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Surat Permohonan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB Surat Permohonan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Akta Notaris</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB Akta Notaris</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">NIB</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB NIB</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">KTP</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">No Rekening</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB No Rekening</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">NPWP</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB NPWP</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Surat Kuasa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB Surat Kuasa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Lantai Jemur</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Sarana Lainnya</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Mesin Pemecah Kulit</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Mesin Pemisah Gabah</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Mesin Penyosoh</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Alat Pemisah Beras</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Status Seleksi</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in filteredSeleksiMitras" :key="item.id_seleksi_mitra" class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">{{ item.mitra?.nama_perusahaan || '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.surat_permohonan)]">
                                    {{ item.surat_permohonan === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_surat_permohonan ? formatDate(item.mb_surat_permohonan) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.akta_notaris)]">
                                    {{ item.akta_notaris === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_akta_notaris ? formatDate(item.mb_akta_notaris) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.nib)]">
                                    {{ item.nib === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_nib ? formatDate(item.mb_nib) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.ktp)]">
                                    {{ item.ktp === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.no_rekening)]">
                                    {{ item.no_rekening === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_no_rekening ? formatDate(item.mb_no_rekening) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.npwp)]">
                                    {{ item.npwp === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_npwp ? formatDate(item.mb_npwp) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.surat_kuasa)]">
                                    {{ item.surat_kuasa === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_surat_kuasa ? formatDate(item.mb_surat_kuasa) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.lantai_jemur)]">
                                    {{ item.lantai_jemur === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.sarana_lainnya)]">
                                    {{ item.sarana_lainnya === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_memecah_kulit)]">
                                    {{ item.mesin_memecah_kulit === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_pemisah_gabah)]">
                                    {{ item.mesin_pemisah_gabah === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_penyosoh)]">
                                    {{ item.mesin_penyosoh === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.alat_pemisah_beras)]">
                                    {{ item.alat_pemisah_beras === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', 
                                    item.status_seleksi === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    item.status_seleksi === 'lolos' ? 'bg-green-100 text-green-800' : 
                                    'bg-red-100 text-red-800']">
                                    {{ item.status_seleksi === 'pending' ? 'Pending' : 
                                       item.status_seleksi === 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button
                                    @click="lihatSeleksi(item)"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-900 text-xs"
                                >
                                    Lihat
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredSeleksiMitras.length === 0">
                            <td colspan="22" class="px-4 py-6 text-center text-gray-500">
                                {{ searchQuery ? 'Tidak ada data seleksi mitra yang sesuai dengan pencarian.' : 'Belum ada data seleksi mitra.' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Seleksi Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full h-auto p-6 relative max-h-[90vh] overflow-y-auto">
                <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-blue-700">Detail Seleksi Mitra</h2>
                
                <div v-if="selectedItem" class="space-y-6">
                    <!-- Info Perusahaan -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Informasi Perusahaan</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nama Perusahaan</p>
                                <p class="font-medium">{{ selectedItem.mitra?.nama_perusahaan || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status Seleksi</p>
                                <span :class="['px-2 py-1 rounded-full text-xs font-medium', 
                                    selectedItem.status_seleksi === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    selectedItem.status_seleksi === 'lolos' ? 'bg-green-100 text-green-800' : 
                                    'bg-red-100 text-red-800']">
                                    {{ selectedItem.status_seleksi === 'pending' ? 'Pending' : 
                                    selectedItem.status_seleksi === 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dokumen Perizinan -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Dokumen Perizinan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Surat Permohonan -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Surat Permohonan</p>
                                    <div class="flex items-center mt-1">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                            selectedItem.surat_permohonan === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.surat_permohonan === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_surat_permohonan ? formatDate(selectedItem.mb_surat_permohonan) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_surat_permohonan"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Akta Notaris -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Akta Notaris</p>
                                    <div class="flex items-center mt-1">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                            selectedItem.akta_notaris === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.akta_notaris === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_akta_notaris ? formatDate(selectedItem.mb_akta_notaris) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_akta_notaris"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- NIB -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">NIB</p>
                                    <div class="flex items-center mt-1">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                            selectedItem.nib === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.nib === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_nib ? formatDate(selectedItem.mb_nib) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_nib"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- KTP -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">KTP</p>
                                    <div class="flex items-center mt-1">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                            selectedItem.ktp === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.ktp === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_ktp"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- No Rekening -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">No Rekening</p>
                                    <div class="flex items-center mt-1">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                            selectedItem.no_rekening === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.no_rekening === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_no_rekening ? formatDate(selectedItem.mb_no_rekening) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_no_rekening"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- NPWP -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">NPWP</p>
                                    <div class="flex items-center mt-1">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                            selectedItem.npwp === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.npwp === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_npwp ? formatDate(selectedItem.mb_npwp) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_npwp"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Surat Kuasa -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Surat Kuasa</p>
                                    <div class="flex items-center mt-1">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                            selectedItem.surat_kuasa === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.surat_kuasa === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_surat_kuasa ? formatDate(selectedItem.mb_surat_kuasa) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_surat_kuasa"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sarana Pengeringan -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Sarana Pengeringan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Lantai Jemur -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Lantai Jemur</p>
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                        selectedItem.lantai_jemur === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.lantai_jemur === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_lantai_jemur"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Sarana Lainnya -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Sarana Lainnya</p>
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                        selectedItem.sarana_lainnya === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.sarana_lainnya === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_sarana_lainnya"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sarana Penggilingan -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Sarana Penggilingan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Mesin Pemecah Kulit -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Mesin Pemecah Kulit</p>
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                        selectedItem.mesin_memecah_kulit === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.mesin_memecah_kulit === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_mesin_memecah_kulit"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Mesin Pemisah Gabah -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Mesin Pemisah Gabah</p>
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                        selectedItem.mesin_pemisah_gabah === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.mesin_pemisah_gabah === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_mesin_pemisah_gabah"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Mesin Penyosoh -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Mesin Penyosoh</p>
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                        selectedItem.mesin_penyosoh === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.mesin_penyosoh === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_mesin_penyosoh"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Alat Pemisah Beras -->
                            <div class="grid grid-cols-1 md:grid-cols-3 items-center p-2 rounded-lg hover:bg-gray-50">
                                <div>
                                    <p class="text-sm text-gray-500">Alat Pemisah Beras</p>
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                                        selectedItem.alat_pemisah_beras === 'ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.alat_pemisah_beras === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        v-model="selectedItem.status_alat_pemisah_beras"
                                    >
                                        <option value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status messages -->
                    <div v-if="errorMessage" class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded">
                        {{ errorMessage }}
                    </div>
                    
                    <div v-if="successMessage" class="bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded">
                        {{ successMessage }}
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 mt-4">
                        <button 
                            @click="rejectSeleksi" 
                            :disabled="isLoading || selectedItem.status_seleksi === 'tidak lolos'"
                            :class="[
                                'px-4 py-2 rounded-md font-medium text-sm transition-colors',
                                isLoading || selectedItem.status_seleksi === 'tidak lolos' 
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed' 
                                    : 'bg-red-600 text-white hover:bg-red-700'
                            ]"
                        >
                            <span v-if="selectedItem.status_seleksi === 'tidak lolos'">Sudah Tidak Lolos</span>
                            <span v-else-if="isLoading">Processing...</span>
                            <span v-else>Tidak Lolos</span>
                        </button>
                        
                        <button 
                            @click="approveSeleksi"
                            :disabled="isLoading || selectedItem.status_seleksi === 'lolos'"
                            :class="[
                                'px-4 py-2 rounded-md font-medium text-sm transition-colors',
                                isLoading || selectedItem.status_seleksi === 'lolos'
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed' 
                                    : 'bg-green-600 text-white hover:bg-green-700'
                            ]"
                        >
                            <span v-if="selectedItem.status_seleksi === 'lolos'">Sudah Lolos</span>
                            <span v-else-if="isLoading">Processing...</span>
                            <span v-else>Lolos</span>
                        </button>
                        
                        <button @click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-medium text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
