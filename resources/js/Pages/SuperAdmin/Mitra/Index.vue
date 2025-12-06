<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';

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

// Pagination state
const currentPage = ref(1);
const itemsPerPage = ref(10);

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

// Pagination computed properties
const totalPages = computed(() => {
    return Math.ceil(filteredMitras.value.length / itemsPerPage.value);
});

const paginatedMitras = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredMitras.value.slice(start, end);
});

const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    const current = currentPage.value;
    
    // Always show first page
    pages.push(1);
    
    // Calculate range around current page
    let rangeStart = Math.max(2, current - 2);
    let rangeEnd = Math.min(total - 1, current + 2);
    
    // Add ellipsis after first page if needed
    if (rangeStart > 2) {
        pages.push('...');
    }
    
    // Add pages around current page
    for (let i = rangeStart; i <= rangeEnd; i++) {
        pages.push(i);
    }
    
    // Add ellipsis before last page if needed
    if (rangeEnd < total - 1) {
        pages.push('...');
    }
    
    // Always show last page if there's more than one page
    if (total > 1) {
        pages.push(total);
    }
    
    return pages;
});

// Pagination methods
const goToPage = (page) => {
    if (typeof page === 'number' && page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const resetPagination = () => {
    currentPage.value = 1;
};

// Bulk delete functionality
const selectedIds = ref([]);
const selectAll = ref(false);
const showBulkDeleteModal = ref(false);
const isDeleting = ref(false);
const deleteError = ref(null);
const deleteSuccess = ref(null);

// Toggle select all
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedIds.value = paginatedMitras.value.map(m => m.id_mitra);
    } else {
        selectedIds.value = [];
    }
};

// Toggle individual selection
const toggleSelection = (id) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
    selectAll.value = selectedIds.value.length === paginatedMitras.value.length;
};

// Check if item is selected
const isSelected = (id) => {
    return selectedIds.value.includes(id);
};

// Open bulk delete confirmation modal
const openBulkDeleteModal = () => {
    if (selectedIds.value.length === 0) {
        alert('Pilih minimal satu mitra untuk dihapus');
        return;
    }
    showBulkDeleteModal.value = true;
    deleteError.value = null;
    deleteSuccess.value = null;
};

// Close bulk delete modal
const closeBulkDeleteModal = () => {
    showBulkDeleteModal.value = false;
    deleteError.value = null;
    deleteSuccess.value = null;
};

// Bulk delete function
const bulkDelete = async () => {
    if (selectedIds.value.length === 0) return;
    
    isDeleting.value = true;
    deleteError.value = null;
    deleteSuccess.value = null;

    try {
        await axios.post('/super-admin/daftar-mitra/bulk-delete', {
            ids: selectedIds.value
        });
        
        deleteSuccess.value = `${selectedIds.value.length} mitra berhasil dihapus`;
        
        // Clear selection
        selectedIds.value = [];
        selectAll.value = false;
        
        // Reload page after successful delete
        setTimeout(() => {
            router.reload();
            closeBulkDeleteModal();
        }, 1500);

    } catch (error) {
        console.error('Delete error:', error);
        
        if (error.response) {
            const data = error.response.data;
            deleteError.value = data.message || 'Terjadi kesalahan saat menghapus data';
        } else {
            deleteError.value = `Terjadi kesalahan: ${error.message}`;
        }
    } finally {
        isDeleting.value = false;
    }
};

// Watch pagination to update selectAll
watch(currentPage, () => {
    selectAll.value = false;
    selectedIds.value = [];
});

// For modal functionality
const showModal = ref(false);
const selectedMitra = ref(null);

// Edit modal functionality
const showEditModal = ref(false);
const editForm = ref({
    id_mitra: null,
    nama_perusahaan: '',
    badan_hukum_usaha: '',
    alamat_perusahaan: '',
    kota_kabupaten: '',
    provinsi: '',
    nama_cp: '',
    jabatan: '',
    nik: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    no_telp_perusahaan: '',
    no_telp_cp: '',
    bank_korespondensi: '',
    alamat_bank: '',
    no_rekening: '',
    status_perusahaan: '',
    npwp: '',
    pkp: '',
    keterangan_pkp: '',
    surat_kuasa: '',
    keterangan_surat_kuasa: '',
    tanggal_seleksi: '',
    tanggal_klasifikasi: '',
    tanggal_penilaian: '',
    tanggal_penetapan: '',
    tanggal_pakta_integritas: '',
    tanggal_surat_permohonan: '',
    email: '',
    no_vms: '',
    kode_mitra: ''
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

// Delete functionality (single item)
const showDeleteModal = ref(false);
const mitraToDelete = ref(null);

// Format status class based on status_perusahaan
const getStatusClass = (status) => {
    if (!status) return 'bg-gray-100 text-gray-800';
    
    switch (status.toLowerCase()) {
        case 'penggilingan': return 'bg-green-100 text-green-800';
        case 'poktan': return 'bg-red-100 text-red-800';
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
        nama_perusahaan: mitra.nama_perusahaan || '',
        badan_hukum_usaha: mitra.badan_hukum_usaha || '',
        alamat_perusahaan: mitra.alamat_perusahaan || '',
        kota_kabupaten: mitra.kota_kabupaten || '',
        provinsi: mitra.provinsi || '',
        nama_cp: mitra.nama_cp || '',
        jabatan: mitra.jabatan || '',
        nik: mitra.nik || '',
        tempat_lahir: mitra.tempat_lahir || '',
        tanggal_lahir: mitra.tanggal_lahir || '',
        no_telp_perusahaan: mitra.no_telp_perusahaan || '',
        no_telp_cp: mitra.no_telp_cp || '',
        bank_korespondensi: mitra.bank_korespondensi || '',
        alamat_bank: mitra.alamat_bank || '',
        no_rekening: mitra.no_rekening || '',
        status_perusahaan: mitra.status_perusahaan || '',
        npwp: mitra.npwp || '',
        pkp: mitra.pkp || '',
        keterangan_pkp: mitra.keterangan_pkp || '',
        surat_kuasa: mitra.surat_kuasa || '',
        keterangan_surat_kuasa: mitra.keterangan_surat_kuasa || '',
        tanggal_seleksi: mitra.tanggal_seleksi || '',
        tanggal_klasifikasi: mitra.tanggal_klasifikasi || '',
        tanggal_penilaian: mitra.tanggal_penilaian || '',
        tanggal_penetapan: mitra.tanggal_penetapan || '',
        tanggal_pakta_integritas: mitra.tanggal_pakta_integritas || '',
        tanggal_surat_permohonan: mitra.tanggal_surat_permohonan || '',
        email: mitra.email || '',
        no_vms: mitra.no_vms || '',
        kode_mitra: mitra.kode_mitra || ''
    };
    showEditModal.value = true;
    updateError.value = null;
    updateSuccess.value = null;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.value = {
        id_mitra: null,
        nama_perusahaan: '',
        badan_hukum_usaha: '',
        alamat_perusahaan: '',
        kota_kabupaten: '',
        provinsi: '',
        nama_cp: '',
        jabatan: '',
        nik: '',
        tempat_lahir: '',
        tanggal_lahir: '',
        no_telp_perusahaan: '',
        no_telp_cp: '',
        bank_korespondensi: '',
        alamat_bank: '',
        no_rekening: '',
        status_perusahaan: '',
        npwp: '',
        pkp: '',
        keterangan_pkp: '',
        surat_kuasa: '',
        keterangan_surat_kuasa: '',
        tanggal_seleksi: '',
        tanggal_klasifikasi: '',
        tanggal_penilaian: '',
        tanggal_penetapan: '',
        tanggal_pakta_integritas: '',
        tanggal_surat_permohonan: '',
        email: '',
        no_vms: '',
        kode_mitra: ''
    };
    updateError.value = null;
    updateSuccess.value = null;
};

const updateMitra = async () => {
    isUpdating.value = true;
    updateError.value = null;
    updateSuccess.value = null;

    try {
        await axios.put(`/super-admin/daftar-mitra/${editForm.value.id_mitra}`, editForm.value);
        
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




// Delete functions
const openDeleteModal = (mitra) => {
    mitraToDelete.value = mitra;
    showDeleteModal.value = true;
    deleteError.value = null;
    deleteSuccess.value = null;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    mitraToDelete.value = null;
    deleteError.value = null;
    deleteSuccess.value = null;
};

const deleteMitra = async () => {
    if (!mitraToDelete.value) return;
    
    isDeleting.value = true;
    deleteError.value = null;
    deleteSuccess.value = null;

    try {
        await axios.delete(`/super-admin/daftar-mitra/${mitraToDelete.value.id_mitra}`);
        
        deleteSuccess.value = 'Data mitra berhasil dihapus';
        
        // Reload page after successful delete
        setTimeout(() => {
            router.reload();
            closeDeleteModal();
        }, 1500);

    } catch (error) {
        console.error('Delete error:', error);
        
        if (error.response) {
            const data = error.response.data;
            deleteError.value = data.message || 'Terjadi kesalahan saat menghapus data';
        } else {
            deleteError.value = `Terjadi kesalahan: ${error.message}`;
        }
    } finally {
        isDeleting.value = false;
    }
};

// Upload and template functions
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
        console.log('Uploading file to:', '/super-admin/daftar-mitra/import');
        console.log('File:', selectedFile.value.name, selectedFile.value.size, 'bytes');

        const response = await axios.post('/super-admin/daftar-mitra/import', formData, {
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
                const errorMessages = Object.values(data.errors).flat();
                uploadError.value = errorMessages.join('\n');
            } else {
                uploadError.value = data.message || 'Terjadi kesalahan saat import';
            }
        } else if (error.request) {
            uploadError.value = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        } else {
            uploadError.value = `Terjadi kesalahan: ${error.message}`;
        }
    } finally {
        isUploading.value = false;
    }
};

const downloadTemplate = () => {
    window.location.href = '/super-admin/daftar-mitra/template';
};

// Export function
const exportData = () => {
    window.location.href = '/super-admin/daftar-mitra/export';
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

// Watch search query to reset pagination
watch(searchQuery, () => {
    resetPagination();
});
</script>

<template>
    <Head title="Daftar Mitra - Super Admin" />

    <SuperAdminLayout>
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
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Import Excel
                        </button>
                        <button
                            @click="exportData"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                    
                </div>
            </div>

            <!--Search Bar-->
            <div class="mb-4">
                <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                    <!-- Search Input -->
                    <div class="relative w-full sm:flex-1">
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

                    <!-- Bulk Delete Button -->
                    <button
                        v-if="selectedIds.length > 0"
                        @click="openBulkDeleteModal"
                        class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors whitespace-nowrap"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus ({{ selectedIds.length }})
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
                                <th class="px-4 py-3 text-center w-12">
                                    <input 
                                        type="checkbox" 
                                        v-model="selectAll" 
                                        @change="toggleSelectAll"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                        title="Pilih semua di halaman ini"
                                    />
                                </th>
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
                                        {{ sortOrder === 'desc' ? 'â–¼' : 'â–²' }}
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
                                        {{ sortOrder === 'desc' ? 'â–¼' : 'â–²' }}
                                    </span>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="mitra in paginatedMitras" :key="mitra.id_mitra" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center">
                                    <input 
                                        type="checkbox" 
                                        :checked="isSelected(mitra.id_mitra)" 
                                        @change="toggleSelection(mitra.id_mitra)"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                    />
                                </td>
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
                                        <button
                                            @click="openDeleteModal(mitra)"
                                            class="inline-flex items-center px-2 py-1 text-red-600 hover:text-white hover:bg-red-600 border border-red-600 rounded transition-colors duration-200 text-xs"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="paginatedMitras.length === 0">
                                <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                    {{ searchQuery ? 'Tidak ada mitra yang sesuai dengan pencarian Anda.' : 'Belum ada data mitra. Silakan tambahkan mitra baru.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Info -->
                        <div class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-medium">{{ ((currentPage - 1) * itemsPerPage) + 1 }}</span>
                            sampai 
                            <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredMitras.length) }}</span>
                            dari 
                            <span class="font-medium">{{ filteredMitras.length }}</span>
                            data
                        </div>

                        <!-- Pagination Controls -->
                        <div class="flex items-center gap-2">
                            <!-- Previous Button -->
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 rounded-lg border border-gray-300 text-sm font-medium transition-colors"
                                :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            >
                                ‹ Prev
                            </button>

                            <!-- Page Numbers -->
                            <template v-for="page in visiblePages" :key="page">
                                <button
                                    v-if="page === '...'"
                                    disabled
                                    class="px-3 py-1 text-sm text-gray-400 cursor-default"
                                >
                                    ...
                                </button>
                                <button
                                    v-else
                                    @click="goToPage(page)"
                                    class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
                                    :class="currentPage === page 
                                        ? 'bg-blue-600 text-white' 
                                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                                >
                                    {{ page }}
                                </button>
                            </template>

                            <!-- Next Button -->
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="px-3 py-1 rounded-lg border border-gray-300 text-sm font-medium transition-colors"
                                :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            >
                                Next ›
                            </button>
                        </div>
                    </div>
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
                    
                    <div>
                        <span class="block text-sm text-gray-500">Jabatan</span>
                        <span class="font-medium text-gray-900">{{ selectedMitra.jabatan || '-' }}</span>
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
                                    <li>â€¢ File harus berformat Excel (.xlsx, .xls) atau CSV</li>
                                    <li>â€¢ Ukuran file maksimal 5MB</li>
                                    <li>â€¢ Download template untuk melihat format yang benar</li>
                                    <li>â€¢ Pastikan kolom sesuai dengan template</li>
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

        <!-- Modal Delete Confirmation -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click="closeDeleteModal">
            <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative" @click.stop>
                <button @click="closeDeleteModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Data Mitra</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Apakah Anda yakin ingin menghapus data mitra 
                        <span class="font-semibold">{{ mitraToDelete?.nama_perusahaan }}</span>?
                        <br>
                        <span class="text-red-600">Tindakan ini tidak dapat dibatalkan.</span>
                    </p>

                    <!-- Success Message -->
                    <div v-if="deleteSuccess" class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                        <div class="flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm font-medium text-green-800">{{ deleteSuccess }}</p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="deleteError" class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start justify-center">
                            <svg class="h-5 w-5 text-red-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm font-medium text-red-800">{{ deleteError }}</p>
                        </div>
                    </div>

                    <div class="flex justify-center gap-3 mt-6">
                        <button
                            @click="closeDeleteModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                            :disabled="isDeleting"
                        >
                            Batal
                        </button>
                        <button
                            @click="deleteMitra"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-red-300 disabled:cursor-not-allowed transition-colors"
                            :disabled="isDeleting"
                        >
                            <span v-if="isDeleting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menghapus...
                            </span>
                            <span v-else>Ya, Hapus</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Bulk Delete -->
        <div v-if="showBulkDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative">
                <button @click="closeBulkDeleteModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="ml-4 text-xl font-bold text-gray-900">Konfirmasi Hapus</h2>
                </div>

                <div class="mb-6">
                    <p class="text-gray-700 mb-2">
                        Anda yakin ingin menghapus <span class="font-bold text-red-600">{{ selectedIds.length }}</span> mitra yang dipilih?
                    </p>
                    <p class="text-sm text-gray-500">
                        Tindakan ini tidak dapat dibatalkan. Semua data mitra yang dipilih akan dihapus secara permanen.
                    </p>
                </div>

                <!-- Success Message -->
                <div v-if="deleteSuccess" class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-2 text-sm font-medium text-green-800">{{ deleteSuccess }}</p>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="deleteError" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-2 text-sm font-medium text-red-800 whitespace-pre-line">{{ deleteError }}</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        @click="closeBulkDeleteModal"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                        :disabled="isDeleting"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="bulkDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-red-300 disabled:cursor-not-allowed transition-colors"
                        :disabled="isDeleting"
                    >
                        <span v-if="isDeleting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menghapus...
                        </span>
                        <span v-else>Ya, Hapus {{ selectedIds.length }} Mitra</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Edit Mitra -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full p-6 relative max-h-[80vh] overflow-y-auto">
                <button @click="closeEditModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-blue-700">Edit Data Mitra</h2>
                
                <form @submit.prevent="updateMitra" class="space-y-6">
                    <!-- Data Perusahaan -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Perusahaan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.nama_perusahaan"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan nama perusahaan"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Badan Hukum/Usaha
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.badan_hukum_usaha"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="PT, CV, UD, dll"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Perusahaan
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.status_perusahaan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Penggilingan Padi, Poktan/Gapoktan, dll"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Perusahaan
                                </label>
                                <textarea
                                    v-model="editForm.alamat_perusahaan"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan alamat lengkap perusahaan"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kota/Kabupaten
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.kota_kabupaten"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: Kota Bandung"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Provinsi
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.provinsi"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: Jawa Barat"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    v-model="editForm.email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="email@perusahaan.com"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    No Telepon Perusahaan
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.no_telp_perusahaan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: 021-1234567"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Data Contact Person -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Contact Person</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Contact Person
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.nama_cp"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan nama contact person"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jabatan
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.jabatan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: Direktur, Manager, dll"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    NIK
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.nik"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="16 digit NIK KTP"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tempat Lahir
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.tempat_lahir"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: Jakarta"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Lahir
                                </label>
                                <input
                                    type="date"
                                    v-model="editForm.tanggal_lahir"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    No Telepon CP
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.no_telp_cp"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: 08123456789"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Data Bank -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Bank</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Korespondensi
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.bank_korespondensi"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: Bank BRI"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    No Rekening
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.no_rekening"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Nomor rekening bank"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Bank
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.alamat_bank"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Alamat cabang bank"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Data Pajak & Legalitas -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pajak & Legalitas</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    NPWP
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.npwp"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="15 digit NPWP"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    PKP
                                </label>
                                <select
                                    v-model="editForm.pkp"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Pilih Status PKP</option>
                                    <option value="Pkp">PKP</option>
                                    <option value="Non Pkp">Non PKP</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan PKP
                                </label>
                                <textarea
                                    v-model="editForm.keterangan_pkp"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan keterangan terkait status PKP"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Surat Kuasa
                                </label>
                                <select
                                    v-model="editForm.surat_kuasa"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Pilih Status Surat Kuasa</option>
                                    <option value="Ada">Ada</option>
                                    <option value="Tidak Ada">Tidak Ada</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan Surat Kuasa
                                </label>
                                <textarea
                                    v-model="editForm.keterangan_surat_kuasa"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan keterangan terkait surat kuasa"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Proses -->
                    <div class="border-t border-gray-200 pt-6">
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

                    <!-- Data VMS & Kode Mitra -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data VMS & Kode Mitra</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    No VMS
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.no_vms"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Nomor VMS"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Mitra
                                </label>
                                <input
                                    type="text"
                                    v-model="editForm.kode_mitra"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Kode mitra"
                                />
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
    </SuperAdminLayout>
</template>