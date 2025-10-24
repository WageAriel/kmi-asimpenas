<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

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

// For import modal
const showImportModal = ref(false);
const selectedFile = ref(null);
const importing = ref(false);
const importResult = ref(null);

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

// Import functions
const openImportModal = () => {
    showImportModal.value = true;
    importResult.value = null;
    selectedFile.value = null;
};

const closeImportModal = () => {
    showImportModal.value = false;
    importResult.value = null;
    selectedFile.value = null;
};

const handleFileChange = (event) => {
    selectedFile.value = event.target.files[0];
    importResult.value = null;
};

const downloadTemplate = async () => {
    try {
        const response = await axios({
            url: '/api/data-mitra/template',
            method: 'GET',
            responseType: 'blob',
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'template_data_mitra.xlsx');
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        alert('Gagal mendownload template: ' + error.message);
    }
};

const importExcel = async () => {
    if (!selectedFile.value) {
        alert('Silakan pilih file Excel terlebih dahulu');
        return;
    }

    importing.value = true;
    importResult.value = null;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const response = await axios.post('/api/data-mitra/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.data.success) {
            importResult.value = {
                type: 'success',
                message: response.data.message,
                summary: response.data.summary,
            };
            
            // Refresh page after successful import
            setTimeout(() => {
                router.reload();
            }, 2000);
        } else {
            importResult.value = {
                type: 'error',
                message: response.data.message || 'Gagal mengimport data',
            };
        }
    } catch (error) {
        importResult.value = {
            type: 'error',
            message: error.response?.data?.message || 'Terjadi kesalahan: ' + error.message,
        };
    } finally {
        importing.value = false;
    }
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
                            Kelola dan import data mitra dalam sistem
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button
                            @click="openImportModal"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Import Excel
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

        <!-- Modal Import Excel -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6 relative">
                <button @click="closeImportModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-blue-700">Import Data Mitra dari Excel</h2>
                
                <div class="space-y-4">
                    <!-- Download Template Button -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-gray-700 mb-3">
                            <strong>Langkah 1:</strong> Download template Excel terlebih dahulu
                        </p>
                        <button
                            @click="downloadTemplate"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Template Excel
                        </button>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <strong>Langkah 2:</strong> Upload file Excel yang sudah diisi
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                            <input
                                type="file"
                                @change="handleFileChange"
                                accept=".xlsx,.xls,.csv"
                                class="hidden"
                                id="fileInput"
                            />
                            <label for="fileInput" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div class="mt-2">
                                    <span class="text-blue-600 hover:text-blue-700 font-medium">Klik untuk memilih file</span>
                                    <span class="text-gray-500"> atau drag & drop</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Excel (XLSX, XLS) atau CSV - Max 10MB</p>
                            </label>
                        </div>
                        <p v-if="selectedFile" class="mt-2 text-sm text-gray-600">
                            <strong>File terpilih:</strong> {{ selectedFile.name }}
                        </p>
                    </div>

                    <!-- Import Result -->
                    <div v-if="importResult" :class="{
                        'bg-green-50 border-green-200 text-green-800': importResult.type === 'success',
                        'bg-red-50 border-red-200 text-red-800': importResult.type === 'error'
                    }" class="border rounded-lg p-4">
                        <p class="font-medium">{{ importResult.message }}</p>
                        <div v-if="importResult.summary && importResult.summary.failures && importResult.summary.failures.length > 0" class="mt-2 text-sm">
                            <p class="font-medium">Error pada baris:</p>
                            <ul class="list-disc list-inside">
                                <li v-for="(failure, index) in importResult.summary.failures.slice(0, 5)" :key="index">
                                    Baris {{ failure.row }}: {{ failure.errors.join(', ') }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button
                            @click="closeImportModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                            :disabled="importing"
                        >
                            Batal
                        </button>
                        <button
                            @click="importExcel"
                            :disabled="!selectedFile || importing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                        >
                            <svg v-if="importing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ importing ? 'Mengimport...' : 'Import Sekarang' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>