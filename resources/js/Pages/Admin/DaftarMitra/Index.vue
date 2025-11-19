<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Accept data from the controller as props
const props = defineProps({
    mitras: {
        type: Array,
        default: () => []
    }
});

// Add search functionality
const searchQuery = ref('');

// Sort state
const sortBy = ref('created_at');
const sortOrder = ref('desc');

// Sort function
const toggleSort = (column) => {
    if (sortBy.value === column) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortOrder.value = 'asc';
    }
};

// Filter mitras based on search query
const filteredMitras = computed(() => {
    let filtered = [...props.mitras];
    
    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(mitra => {
            return (
                mitra.nama_perusahaan?.toLowerCase().includes(query) ||
                mitra.badan_hukum_usaha?.toLowerCase().includes(query) ||
                mitra.status_perusahaan?.toLowerCase().includes(query)
            );
        });
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let comparison = 0;
        
        switch (sortBy.value) {
            case 'nama_perusahaan':
                comparison = a.nama_perusahaan?.localeCompare(b.nama_perusahaan);
                break;
            case 'badan_hukum':
                comparison = a.badan_hukum_usaha?.localeCompare(b.badan_hukum_usaha);
                break;
            case 'status':
                comparison = a.status_perusahaan?.localeCompare(b.status_perusahaan);
                break;
            default:
                comparison = new Date(b.created_at) - new Date(a.created_at);
        }
        
        return sortOrder.value === 'desc' ? comparison * -1 : comparison;
    });

    return filtered;
});

// For modal functionality
const showModal = ref(false);
const selectedMitra = ref(null);

// Edit modal functionality
const showEditModal = ref(false);
const editForm = ref({
    id_mitra: null,
    tanggal_seleksi: '',
    tanggal_klasifikasi: '',
    tanggal_penilaian: '',
    tanggal_penetapan: '',
    tanggal_pakta_integritas: '',
    tanggal_surat_permohonan: '',
    keterangan_pkp: '',
    keterangan_surat_kuasa: ''
});
const isUpdating = ref(false);
const updateError = ref(null);
const updateSuccess = ref(null);

// Import functionality
const showImportModal = ref(false);
const selectedFile = ref(null);
const isUploading = ref(false);
const uploadError = ref(null);
const uploadSuccess = ref(null);

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

// Edit functions
const openEditModal = (mitra) => {
    editForm.value = {
        id_mitra: mitra.id_mitra,
        tanggal_seleksi: mitra.tanggal_seleksi || '',
        tanggal_klasifikasi: mitra.tanggal_klasifikasi || '',
        tanggal_penilaian: mitra.tanggal_penilaian || '',
        tanggal_penetapan: mitra.tanggal_penetapan || '',
        tanggal_pakta_integritas: mitra.tanggal_pakta_integritas || '',
        tanggal_surat_permohonan: mitra.tanggal_surat_permohonan || '',
        keterangan_pkp: mitra.keterangan_pkp || '',
        keterangan_surat_kuasa: mitra.keterangan_surat_kuasa || ''
    };
    showEditModal.value = true;
    updateError.value = null;
    updateSuccess.value = null;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.value = {
        id_mitra: null,
        tanggal_seleksi: '',
        tanggal_klasifikasi: '',
        tanggal_penilaian: '',
        tanggal_penetapan: '',
        tanggal_pakta_integritas: '',
        tanggal_surat_permohonan: '',
        keterangan_pkp: '',
        keterangan_surat_kuasa: ''
    };
    updateError.value = null;
    updateSuccess.value = null;
};

const updateMitra = async () => {
    isUpdating.value = true;
    updateError.value = null;
    updateSuccess.value = null;

    try {
        await axios.put(`/admin/daftar-mitra/${editForm.value.id_mitra}`, editForm.value);
        
        updateSuccess.value = 'Data mitra berhasil diperbarui';
        
        // Reload page after successful update
        setTimeout(() => {
            router.reload();
            closeEditModal();
        }, 1500);

    } catch (error) {
        console.error('Update error:', error);
        
        if (error.response) {
            const data = error.response.data;
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                updateError.value = errorMessages.join('\n');
            } else {
                updateError.value = data.message || 'Terjadi kesalahan saat memperbarui data';
            }
        } else {
            updateError.value = `Terjadi kesalahan: ${error.message}`;
        }
    } finally {
        isUpdating.value = false;
    }
};

// Function to handle new mitra form
const goToForm = (type) => {
    window.location.href = route('input-data-mitra');
};

// Import functions
const openImportModal = () => {
    showImportModal.value = true;
    selectedFile.value = null;
    uploadError.value = null;
    uploadSuccess.value = null;
};

const closeImportModal = () => {
    showImportModal.value = false;
    selectedFile.value = null;
    uploadError.value = null;
    uploadSuccess.value = null;
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'];
        if (!validTypes.includes(file.type)) {
            uploadError.value = 'File harus berformat Excel (.xlsx, .xls) atau CSV';
            selectedFile.value = null;
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            uploadError.value = 'Ukuran file maksimal 5MB';
            selectedFile.value = null;
            return;
        }
        
        selectedFile.value = file;
        uploadError.value = null;
    }
};

const uploadFile = async () => {
    if (!selectedFile.value) {
        uploadError.value = 'Pilih file terlebih dahulu';
        return;
    }

    isUploading.value = true;
    uploadError.value = null;
    uploadSuccess.value = null;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        console.log('Uploading file to:', route('admin.daftar-mitra.import'));
        console.log('File:', selectedFile.value.name, selectedFile.value.size, 'bytes');

        // Use axios which automatically handles CSRF token
        const response = await axios.post(route('admin.daftar-mitra.import'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        console.log('Upload success:', response.data);

        uploadSuccess.value = response.data.message || 'Data berhasil diimport';
        selectedFile.value = null;
        
        // Reload page after successful import
        setTimeout(() => {
            router.reload();
        }, 1500);

    } catch (error) {
        console.error('Upload error:', error);
        
        if (error.response) {
            // Server responded with error
            const data = error.response.data;
            
            if (data.failures && data.failures.length > 0) {
                const errorDetails = data.failures.slice(0, 5).map(f => 
                    `Baris ${f.row}: ${f.errors.join(', ')}`
                ).join('\n');
                uploadError.value = `Terdapat error pada file:\n${errorDetails}`;
                
                if (data.failures.length > 5) {
                    uploadError.value += `\n... dan ${data.failures.length - 5} error lainnya`;
                }
            } else if (data.errors) {
                // Laravel validation errors
                const errorMessages = Object.values(data.errors).flat();
                uploadError.value = errorMessages.join('\n');
            } else {
                uploadError.value = data.message || 'Terjadi kesalahan saat import';
            }
        } else if (error.request) {
            // Request made but no response
            uploadError.value = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        } else {
            // Something else happened
            uploadError.value = `Terjadi kesalahan: ${error.message}`;
        }
    } finally {
        isUploading.value = false;
    }
};

const downloadTemplate = () => {
    window.location.href = route('admin.daftar-mitra.template');
};

// Export function
const exportData = () => {
    window.location.href = route('admin.daftar-mitra.export');
};

// Format date function
const formatDate = (dateString) => {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
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
                            @click="openImportModal"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Import Excel
                        </button>
                        <button
                            @click="exportData"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Export Excel
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
                                <th 
                                    @click="toggleSort('nama_perusahaan')" 
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                >
                                    Nama Perusahaan
                                    <span v-if="sortBy === 'nama_perusahaan'" class="ml-1">
                                        {{ sortOrder === 'desc' ? '▼' : '▲' }}
                                    </span>
                                </th>
                                <th 
                                    @click="toggleSort('badan_hukum')" 
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                >
                                    Badan Hukum
                                    <span v-if="sortBy === 'badan_hukum'" class="ml-1">
                                        {{ sortOrder === 'desc' ? '▼' : '▲' }}
                                    </span>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama CP</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Telp Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Telp CP</th>
                                <th 
                                    @click="toggleSort('status')" 
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                >
                                    Status
                                    <span v-if="sortBy === 'status'" class="ml-1">
                                        {{ sortOrder === 'desc' ? '▼' : '▲' }}
                                    </span>
                                </th>
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
                                    <div class="flex space-x-2">
                                        <button
                                            @click="lihatMitra(mitra)"
                                            class="inline-flex items-center px-2 py-1 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded transition-colors duration-200 text-xs"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat
                                        </button>
                                        <button
                                            @click="openEditModal(mitra)"
                                            class="inline-flex items-center px-2 py-1 text-green-600 hover:text-white hover:bg-green-600 border border-green-600 rounded transition-colors duration-200 text-xs"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                    </div>
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
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click="closeModal">
            <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full h-auto p-6 relative max-h-[80vh] overflow-y-auto" @click.stop>
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
                        <span class="font-medium text-gray-900">{{ formatDate(selectedMitra.tanggal_lahir) }}</span>
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

                    <div v-if="selectedMitra.keterangan_pkp" class="col-span-2">
                        <span class="block text-sm text-gray-500">Keterangan PKP</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.keterangan_pkp }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-sm text-gray-500">Surat Kuasa</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.surat_kuasa === 'Ada' ? 'Ada' : 'Tidak Ada' }}</span>
                    </div>

                    <div v-if="selectedMitra.keterangan_surat_kuasa" class="col-span-2">
                        <span class="block text-sm text-gray-500">Keterangan Surat Kuasa</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.keterangan_surat_kuasa }}</span>
                    </div>

                    <div class="col-span-2 border-t border-gray-200 pt-4 mt-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tanggal Proses</h3>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">Tanggal Surat Permohonan</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedMitra.tanggal_surat_permohonan) }}</span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">Tanggal Pakta Integritas</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedMitra.tanggal_pakta_integritas) }}</span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">Tanggal Seleksi</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedMitra.tanggal_seleksi) }}</span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">Tanggal Klasifikasi</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedMitra.tanggal_klasifikasi) }}</span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">Tanggal Penilaian</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedMitra.tanggal_penilaian) }}</span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">Tanggal Penetapan</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedMitra.tanggal_penetapan) }}</span>
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
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click="closeImportModal">
            <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative" @click.stop>
                <button @click="closeImportModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-blue-700">Import Data Mitra dari Excel</h2>
                
                <div class="space-y-4">
                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 mb-1">Panduan Import</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>• File harus berformat Excel (.xlsx, .xls) atau CSV</li>
                                    <li>• Ukuran file maksimal 5MB</li>
                                    <li>• Download template untuk melihat format yang benar</li>
                                    <li>• Pastikan kolom sesuai dengan template</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Download Template Button -->
                    <div class="flex justify-center">
                        <button
                            @click="downloadTemplate"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Template Excel
                        </button>
                    </div>

                    <!-- File Upload Area -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept=".xlsx,.xls,.csv"
                            class="hidden"
                            id="fileInput"
                        />
                        <label
                            for="fileInput"
                            class="flex flex-col items-center cursor-pointer"
                        >
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 mb-1">
                                {{ selectedFile ? selectedFile.name : 'Klik untuk pilih file' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Excel (.xlsx, .xls) atau CSV (max. 5MB)
                            </span>
                        </label>
                    </div>

                    <!-- Success Message -->
                    <div v-if="uploadSuccess" class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-green-800">{{ uploadSuccess }}</span>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="uploadError" class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-48 overflow-y-auto">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-red-800 whitespace-pre-wrap">{{ uploadError }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        @click="closeImportModal"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                        :disabled="isUploading"
                    >
                        Batal
                    </button>
                    <button
                        @click="uploadFile"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition-colors"
                        :disabled="!selectedFile || isUploading"
                    >
                        <span v-if="isUploading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengupload...
                        </span>
                        <span v-else>Upload File</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Edit Mitra -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" >
            <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full p-6 relative max-h-[80vh] overflow-y-auto">
                <button @click="closeEditModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-blue-700">Edit Data Mitra</h2>
                
                <form @submit.prevent="updateMitra" class="space-y-6">
                    <!-- Tanggal-tanggal -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tanggal Proses</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Surat Permohonan
                                </label>
                                <input
                                    type="date"
                                    v-model="editForm.tanggal_surat_permohonan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Pakta Integritas
                                </label>
                                <input
                                    type="date"
                                    v-model="editForm.tanggal_pakta_integritas"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Seleksi
                                </label>
                                <input
                                    type="date"
                                    v-model="editForm.tanggal_seleksi"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Klasifikasi
                                </label>
                                <input
                                    type="date"
                                    v-model="editForm.tanggal_klasifikasi"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Penilaian
                                </label>
                                <input
                                    type="date"
                                    v-model="editForm.tanggal_penilaian"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Penetapan
                                </label>
                                <input
                                    type="date"
                                    v-model="editForm.tanggal_penetapan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Keterangan Tambahan</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan PKP
                                </label>
                                <textarea
                                    v-model="editForm.keterangan_pkp"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan keterangan terkait status PKP"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan Surat Kuasa
                                </label>
                                <textarea
                                    v-model="editForm.keterangan_surat_kuasa"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan keterangan terkait surat kuasa"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div v-if="updateSuccess" class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ updateSuccess }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="updateError" class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800 whitespace-pre-line">{{ updateError }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                            :disabled="isUpdating"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition-colors"
                            :disabled="isUpdating"
                        >
                            <span v-if="isUpdating" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>