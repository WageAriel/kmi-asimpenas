<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Accept data from the controller as props
const props = defineProps({
    mitras: {
        type: Array,
        default: () => []
    }
});

// Sort mitras by created_at (newest first)
const sortedMitras = computed(() => {
    // Create a copy to avoid modifying props directly
    return [...props.mitras].sort((a, b) => {
        // Sort by created_at in descending order (newest first)
        return new Date(b.created_at) - new Date(a.created_at);
    });
});

// Add search functionality
const searchQuery = ref('');

// Filter mitras based on search query
const filteredMitras = computed(() => {
    if (!searchQuery.value) return sortedMitras.value;
    
    const query = searchQuery.value.toLowerCase();
    return sortedMitras.value.filter(mitra => {
        return (
            mitra.nama_perusahaan?.toLowerCase().includes(query) ||
            mitra.nama_cp?.toLowerCase().includes(query) ||
            mitra.alamat_perusahaan?.toLowerCase().includes(query) ||
            mitra.no_telp_perusahaan?.toLowerCase().includes(query) ||
            mitra.status_perusahaan?.toLowerCase().includes(query)
        );
    });
});

// For modal functionality
const showModal = ref(false);
const selectedMitra = ref(null);

// Format status class based on status_perusahaan
const getStatusClass = (status) => {
    if (!status) return 'bg-gray-100 text-gray-800';
    
    switch (status.toLowerCase()) {
        case 'penggilingan': return 'bg-green-100 text-green-800';
        case 'distributor': return 'bg-red-100 text-red-800';
        default: return 'bg-blue-100 text-blue-800';
    }
};

// Changed to open modal instead of navigating
const lihatMitra = (mitra) => {
    selectedMitra.value = mitra;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedMitra.value = null;
};

// Function to handle new mitra form
const goToForm = (type) => {
    window.location.href = route('input-data-mitra');
};
</script>

<template>
    <Head title="Daftar Mitra - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Mitra</h2>
                <p class="text-gray-600">Kelola mitra yang terdaftar di sistem.</p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-xl font-bold text-white mb-2">Daftar Mitra</h3>
                        <p class="text-blue-100">
                            Berikut adalah daftar mitra yang terdaftar di sistem ASIMPENAS.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button
                            @click="goToForm('new')"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Mitra Baru
                        </button>
                    </div>
                </div>
            </div>

            <!--Search Bar-->
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
                        placeholder="Cari nama mitra atau perusahaan..."
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
            
            <!-- Tabel Daftar Mitra -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Mitra</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badan Hukum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama CP</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Telp Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Telp CP</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="mitra in filteredMitras" :key="mitra.id_mitra" class="hover:bg-gray-50">
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.nama_perusahaan }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.badan_hukum_usaha }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.alamat_perusahaan }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.nama_cp }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.no_telp_perusahaan }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.no_telp_cp }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusClass(mitra.status_perusahaan)]">
                                        {{ mitra.status_perusahaan || 'Belum diatur' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button
                                        @click="lihatMitra(mitra)"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-900 text-xs"
                                    >
                                        Lihat
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredMitras.length === 0">
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    {{ searchQuery ? 'Tidak ada mitra yang sesuai dengan pencarian Anda.' : 'Belum ada data mitra. Silakan tambahkan mitra baru.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Detail Mitra -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full h-auto p-6 relative max-h-[80vh] overflow-y-auto">
                <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-blue-700">Detail Mitra</h2>
                
                <div v-if="selectedMitra" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Perusahaan</h3>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Nama Perusahaan</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.nama_perusahaan }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Badan Hukum</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.badan_hukum_usaha || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Alamat</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.alamat_perusahaan || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Kota/Kabupaten</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.kota_kabupaten || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Provinsi</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.provinsi || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Status Perusahaan</span>
                        <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusClass(selectedMitra.status_perusahaan)]">
                            {{ selectedMitra.status_perusahaan || 'Belum diatur' }}
                        </span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Email</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.email || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">No Telepon Perusahaan</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.no_telp_perusahaan || '-' }}</span>
                    </div>
                    
                    <div class="col-span-2 border-t border-gray-200 pt-4 mt-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Contact Person</h3>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Nama CP</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.nama_cp || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">NIK</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.nik || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Tempat Lahir</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.tempat_lahir || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Tanggal Lahir</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.tanggal_lahir || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">No Telp CP</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.no_telp_cp || '-' }}</span>
                    </div>
                    
                    <div class="col-span-2 border-t border-gray-200 pt-4 mt-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Bank</h3>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Bank Korespondensi</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.bank_korespondensi || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Alamat Bank</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.alamat_bank || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">No Rekening</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.no_rekening || '-' }}</span>
                    </div>
                    
                    <div class="col-span-2 border-t border-gray-200 pt-4 mt-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Pajak</h3>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">NPWP</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.npwp || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">PKP</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.pkp || '-' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Surat Kuasa</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.surat_kuasa === 'ada' ? 'Ada' : 'Tidak Ada' }}</span>
                    </div>

                    <div class="col-span-2 border-t border-gray-200 pt-4 mt-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Lainnya</h3>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">No VMS</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.no_vms || '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">Kode Mitra</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.kode_mitra || '-' }}</span>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button @click="closeModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>