<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import axios from 'axios';

// Accept data from the controller as props
const props = defineProps({
    klasifikasiMitras: {
        type: Array,
        default: () => []
    }
});

// Sort klasifikasis by created_at (newest first)
const sortedKlasifikasiMitras = computed(() => {
    return [...props.klasifikasiMitras].sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at);
    });
});

// Add search functionality
const searchQuery = ref('');

// Filter klasifikasis based on search query
const filteredKlasifikasiMitras = computed(() => {
    if (!searchQuery.value) return sortedKlasifikasiMitras.value;
    
    const query = searchQuery.value.toLowerCase();
    return sortedKlasifikasiMitras.value.filter(item => {
        return (
            item.mitra?.nama_perusahaan?.toLowerCase().includes(query) ||
            (item.hasil_klasifikasi?.toLowerCase().includes(query))
        );
    });
});

// Modal functionality
const showModal = ref(false);
const selectedKlasifikasi = ref(null);

// Classification interpretation table
const klasifikasiDescriptions = {
    mesin_pembersih_gabah: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    lantai_jemur: { 1: 'Ada | > 1000m²', 2: 'Ada | 500 - 1000m²', 3: 'Tidak Ada / < 500m²' },
    mesin_pengering: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    alat_pengering_lainnya: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pembersih_awal: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pemecah_kulit: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pembersih_sekam: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pemisah_gabah_pecah_kulit: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pemisah_batu: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_penyosoh: { 1: 'Ada | > 3 | 2', 2: 'Ada | 1 s/d 3 | 1', 3: 'Tidak Ada' },
    mesin_pengkabut: { 1: 'Ada | > 3 | 2', 2: 'Ada | 1 s/d 3 | 1', 3: 'Tidak Ada' },
    mesin_pemesah_menir: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pemisah_katul: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pemisah_berdasarkan_ukuran: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    mesin_pemisah_berdasarkan_warna: { 1: 'Ada | > 3', 2: 'Ada | 1 s/d 3', 3: 'Tidak Ada' },
    tangki_penyimpanan: { 1: 'Ada | > 10', 2: 'Ada | ≤ 10', 3: 'Tidak Ada' },
    mesin_pengemas: { 1: 'Ada | Full Otomatis', 2: 'Ada | Semi Otomatis', 3: 'Tidak Ada' },
    mesin_jahit: { 1: 'Ada | Full Otomatis', 2: 'Ada | Semi Otomatis', 3: 'Tidak Ada' },
    gudang_konvensional: { 1: 'Ada | > 3000', 2: 'Ada | < 3000', 3: 'Tidak Ada' },
    silo_gkg_hopper: { 1: 'Ada | > 2000', 2: 'Ada | < 2000', 3: 'Tidak Ada' },
    truk: { 1: 'Ada | > 5', 2: 'Ada | ≤ 5', 3: 'Tidak Ada' },
    mini_lab: { 1: 'Ada', 2: 'Ada | Tidak Lengkap', 3: 'Tidak Ada' },
    moisture_tester: { 1: 'Ada | Digital', 2: 'Ada | Manual', 3: 'Tidak Ada' },
    pembanding_derajat_sosoh: { 1: 'Ada', 2: 'Ada | Tidak Lengkap', 3: 'Tidak Ada' },
    bagian_quality_control: { 1: 'Ada | Tidak Merangkap', 2: 'Ada | Merangkap', 3: 'Tidak Ada' },
};

const interpretClassification = (field, value) => {
    if (klasifikasiDescriptions[field] && value in klasifikasiDescriptions[field]) {
        return klasifikasiDescriptions[field][value];
    }
    return '-';
};

// Get classification result color
const getClassificationColor = (result) => {
    if (!result) return 'bg-gray-100 text-gray-800';
    
    switch (result) {
        case 'A': return 'bg-green-100 text-green-800';
        case 'B': return 'bg-blue-100 text-blue-800';
        case 'C': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

// Format date
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    });
};

// View classification details
const viewDetails = (item) => {
    selectedKlasifikasi.value = item;
    showModal.value = true;
};

// Close modal
const closeModal = () => {
    showModal.value = false;
    selectedKlasifikasi.value = null;
};

// Update classification result
const updateKlasifikasi = async (id, result) => {
    try {
        await axios.put(`/klasifikasi-mitra/${id}`, {
            hasil_klasifikasi: result
        });
        
        // Update local data
        const index = props.klasifikasiMitras.findIndex(item => item.id_klasifikasi_mitra === id);
        if (index !== -1) {
            props.klasifikasiMitras[index].hasil_klasifikasi = result;
        }
        
        // Update selected klasifikasi if it's the one being viewed in modal
        if (selectedKlasifikasi.value && selectedKlasifikasi.value.id_klasifikasi_mitra === id) {
            selectedKlasifikasi.value.hasil_klasifikasi = result;
        }
    } catch (error) {
        console.error('Error updating klasifikasi:', error);
        alert('Gagal memperbarui hasil klasifikasi.');
    }
};
</script>

<template>
    <Head title="Daftar Klasifikasi Mitra - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Klasifikasi Mitra</h2>
                <p class="text-gray-600">Kelola klasifikasi mitra yang terdaftar di sistem.</p>
            </div>

            <!-- Quick Actions-->
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 mb-4 rounded-lg shadow-md text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Daftar Klasifikasi Mitra</h3>
                        <p class="text-blue-100">
                            Kelola klasifikasi mitra yang terdaftar di sistem.
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

            <!-- Tabel Daftar Klasifikasi Mitra -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Klasifikasi Mitra</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil Klasifikasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in filteredKlasifikasiMitras" :key="item.id_klasifikasi_mitra" class="hover:bg-gray-50">
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ item.mitra?.nama_perusahaan }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ formatDate(item.created_at) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getClassificationColor(item.hasil_klasifikasi)]">
                                        {{ item.hasil_klasifikasi ? `Kelas ${item.hasil_klasifikasi}` : 'Belum Dinilai' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button
                                        @click="viewDetails(item)"
                                        class="inline-flex items-center text-green-600 hover:text-green-900 text-xs mr-3"
                                    >
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredKlasifikasiMitras.length === 0">
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    {{ searchQuery ? 'Tidak ada klasifikasi mitra yang sesuai dengan pencarian Anda.' : 'Belum ada data klasifikasi mitra.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Detail Klasifikasi Mitra -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full h-auto p-6 relative max-h-[80vh] overflow-y-auto">
                <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-green-700">Detail Klasifikasi Mitra</h2>
                
                <div v-if="selectedKlasifikasi" class="space-y-6">
                    <!-- Info Perusahaan -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Informasi Perusahaan</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nama Perusahaan</p>
                                <p class="font-medium">{{ selectedKlasifikasi.mitra?.nama_perusahaan || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Klasifikasi</p>
                                <p class="font-medium">{{ formatDate(selectedKlasifikasi.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Hasil Klasifikasi</p>
                                <div class="flex items-center space-x-2">
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getClassificationColor(selectedKlasifikasi.hasil_klasifikasi)]">
                                        {{ selectedKlasifikasi.hasil_klasifikasi ? `Kelas ${selectedKlasifikasi.hasil_klasifikasi}` : 'Belum Dinilai' }}
                                    </span>
                                    
                                    <div class="flex space-x-1 ml-2" v-if="!selectedKlasifikasi.hasil_klasifikasi">
                                        <button 
                                            @click="updateKlasifikasi(selectedKlasifikasi.id_klasifikasi_mitra, 'A')"
                                            class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs hover:bg-green-200"
                                        >
                                            A
                                        </button>
                                        <button 
                                            @click="updateKlasifikasi(selectedKlasifikasi.id_klasifikasi_mitra, 'B')"
                                            class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs hover:bg-blue-200"
                                        >
                                            B
                                        </button>
                                        <button 
                                            @click="updateKlasifikasi(selectedKlasifikasi.id_klasifikasi_mitra, 'C')"
                                            class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs hover:bg-yellow-200"
                                        >
                                            C
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sarana Produksi -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Sarana Produksi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pembersih Gabah</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pembersih_gabah', selectedKlasifikasi.mesin_pembersih_gabah) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Lantai Jemur</p>
                                <p class="font-medium">{{ interpretClassification('lantai_jemur', selectedKlasifikasi.lantai_jemur) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pengering</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pengering', selectedKlasifikasi.mesin_pengering) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Alat Pengering Lainnya</p>
                                <p class="font-medium">{{ interpretClassification('alat_pengering_lainnya', selectedKlasifikasi.alat_pengering_lainnya) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pembersih Awal</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pembersih_awal', selectedKlasifikasi.mesin_pembersih_awal) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pemecah Kulit</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pemecah_kulit', selectedKlasifikasi.mesin_pemecah_kulit) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sarana Pengolahan -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Sarana Pengolahan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pembersih Sekam</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pembersih_sekam', selectedKlasifikasi.mesin_pembersih_sekam) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pemisah Gabah Pecah Kulit</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pemisah_gabah_pecah_kulit', selectedKlasifikasi.mesin_pemisah_gabah_pecah_kulit) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pemisah Batu</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pemisah_batu', selectedKlasifikasi.mesin_pemisah_batu) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Penyosoh</p>
                                <p class="font-medium">{{ interpretClassification('mesin_penyosoh', selectedKlasifikasi.mesin_penyosoh) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pengkabut</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pengkabut', selectedKlasifikasi.mesin_pengkabut) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pemesah Menir</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pemesah_menir', selectedKlasifikasi.mesin_pemesah_menir) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sarana Penyimpanan dan Distribusi -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Sarana Penyimpanan dan Distribusi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Tangki Penyimpanan</p>
                                <p class="font-medium">{{ interpretClassification('tangki_penyimpanan', selectedKlasifikasi.tangki_penyimpanan) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Pengemas</p>
                                <p class="font-medium">{{ interpretClassification('mesin_pengemas', selectedKlasifikasi.mesin_pengemas) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mesin Jahit</p>
                                <p class="font-medium">{{ interpretClassification('mesin_jahit', selectedKlasifikasi.mesin_jahit) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Gudang Konvensional</p>
                                <p class="font-medium">{{ interpretClassification('gudang_konvensional', selectedKlasifikasi.gudang_konvensional) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Silo GKG / Hopper</p>
                                <p class="font-medium">{{ interpretClassification('silo_gkg_hopper', selectedKlasifikasi.silo_gkg_hopper) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Truk</p>
                                <p class="font-medium">{{ interpretClassification('truk', selectedKlasifikasi.truk) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sarana Quality Control -->
                    <div class="border-b border-gray-200 pb-5">
                        <h3 class="text-lg font-semibold mb-3">Sarana Quality Control</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Mini Lab</p>
                                <p class="font-medium">{{ interpretClassification('mini_lab', selectedKlasifikasi.mini_lab) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Moisture Tester</p>
                                <p class="font-medium">{{ interpretClassification('moisture_tester', selectedKlasifikasi.moisture_tester) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pembanding Derajat Sosoh</p>
                                <p class="font-medium">{{ interpretClassification('pembanding_derajat_sosoh', selectedKlasifikasi.pembanding_derajat_sosoh) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Bagian Quality Control</p>
                                <p class="font-medium">{{ interpretClassification('bagian_quality_control', selectedKlasifikasi.bagian_quality_control) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button 
                            @click="closeModal" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>