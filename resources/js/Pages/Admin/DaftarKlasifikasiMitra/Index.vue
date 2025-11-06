<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
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
    showDropdown.value = false;
    successMessage.value = '';
    errorMessage.value = '';
};

// Close modal
const closeModal = () => {
    showModal.value = false;
    selectedKlasifikasi.value = null;
    showDropdown.value = false;
    successMessage.value = '';
    errorMessage.value = '';
};

// Dropdown state
const showDropdown = ref(false);
const isUpdating = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Update classification result
const updateKlasifikasi = async (id, result) => {
    if (isUpdating.value) return;
    
    isUpdating.value = true;
    errorMessage.value = '';
    successMessage.value = '';
    
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
        
        successMessage.value = `Berhasil memverifikasi mitra ke Kelas ${result}`;
        showDropdown.value = false;
        
        // Auto close success message after 3 seconds
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
    } catch (error) {
        console.error('Error updating klasifikasi:', error);
        errorMessage.value = 'Gagal memperbarui hasil klasifikasi. Silakan coba lagi.';
    } finally {
        isUpdating.value = false;
    }
};

// Toggle dropdown
const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const dropdown = document.getElementById('classification-dropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        showDropdown.value = false;
    }
};

// Add event listener on mount
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

// Remove event listener on unmount
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Import functionality
const showImportModal = ref(false);
const selectedFile = ref(null);
const isUploading = ref(false);
const uploadError = ref(null);
const uploadSuccess = ref(null);

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
        const response = await axios.post('/klasifikasi-mitra/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        uploadSuccess.value = response.data.message || 'Data berhasil diimport';
        selectedFile.value = null;
        
        // Reload page after successful import
        setTimeout(() => {
            window.location.reload();
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
    window.location.href = '/klasifikasi-mitra/export/template';
};

// Export function
const exportData = () => {
    window.location.href = '/klasifikasi-mitra/export/data';
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
                        <p class="text-white">
                            Berikut adalah daftar klasifikasi mitra di sistem ASIMPENAS.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
                        <button
                            @click="openImportModal"
                            class="inline-flex items-center px-4 py-2 bg-white text-green-600 rounded-lg hover:bg-green-50 transition-colors duration-200 font-medium shadow-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Import Excel
                        </button>
                        <button
                            @click="exportData"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 font-medium shadow-sm"
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
                                <td class="text-xs px-4 py-3 whitespace-nowrap">{{ item.mitra?.nama_perusahaan }}</td>
                                <td class="text-xs px-4 py-3 whitespace-nowrap">{{ formatDate(item.created_at) }}</td>
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
                                        Lihat
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
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500 mb-2">Hasil Klasifikasi</p>
                                <span :class="['px-3 py-1.5 text-sm font-medium rounded-full inline-block', getClassificationColor(selectedKlasifikasi.hasil_klasifikasi)]">
                                    {{ selectedKlasifikasi.hasil_klasifikasi ? `Kelas ${selectedKlasifikasi.hasil_klasifikasi}` : 'Belum Dinilai' }}
                                </span>
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
                    
                    <!-- Success/Error Messages -->
                    <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ successMessage }}
                    </div>
                    
                    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ errorMessage }}
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <!-- Dropdown Button untuk Verifikasi Klasifikasi -->
                        <div class="relative" id="classification-dropdown">
                            <button 
                                @click="toggleDropdown"
                                :disabled="isUpdating || selectedKlasifikasi.hasil_klasifikasi"
                                :class="[
                                    'inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm transition-colors',
                                    isUpdating || selectedKlasifikasi.hasil_klasifikasi
                                        ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                        : 'bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500'
                                ]"
                            >
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span v-if="isUpdating">Memproses...</span>
                                <span v-else-if="selectedKlasifikasi.hasil_klasifikasi">
                                    Sudah Diverifikasi (Kelas {{ selectedKlasifikasi.hasil_klasifikasi }})
                                </span>
                                <span v-else>Verifikasi Klasifikasi</span>
                                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div 
                                v-if="showDropdown && !selectedKlasifikasi.hasil_klasifikasi"
                                class="absolute left-0 bottom-full mb-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                            >
                                <button
                                    @click="updateKlasifikasi(selectedKlasifikasi.id_klasifikasi_mitra, 'A')"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 flex items-center transition-colors"
                                    :disabled="isUpdating"
                                >
                                    <span class="w-6 h-6 rounded-full bg-green-100 text-green-800 flex items-center justify-center mr-3 font-bold text-xs">
                                        A
                                    </span>
                                    <span class="text-gray-700">Kelas A (Terbaik)</span>
                                </button>
                                <button
                                    @click="updateKlasifikasi(selectedKlasifikasi.id_klasifikasi_mitra, 'B')"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 flex items-center transition-colors"
                                    :disabled="isUpdating"
                                >
                                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center mr-3 font-bold text-xs">
                                        B
                                    </span>
                                    <span class="text-gray-700">Kelas B (Menengah)</span>
                                </button>
                                <button
                                    @click="updateKlasifikasi(selectedKlasifikasi.id_klasifikasi_mitra, 'C')"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-yellow-50 flex items-center transition-colors"
                                    :disabled="isUpdating"
                                >
                                    <span class="w-6 h-6 rounded-full bg-yellow-100 text-yellow-800 flex items-center justify-center mr-3 font-bold text-xs">
                                        C
                                    </span>
                                    <span class="text-gray-700">Kelas C (Standar)</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Tombol Tutup -->
                        <button 
                            @click="closeModal" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium text-sm"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Import Excel -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full p-6 relative">
                <button @click="closeImportModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-green-700">Import Data Klasifikasi Mitra dari Excel</h2>
                
                <div class="space-y-4">
                    <!-- Info Box -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-green-800">
                                <p class="font-semibold mb-1">Panduan Import:</p>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li>File harus berformat Excel (.xlsx, .xls) atau CSV</li>
                                    <li>Ukuran file maksimal 5MB</li>
                                    <li>Kolom <strong>Nama Perusahaan</strong> akan digunakan untuk mencari ID mitra</li>
                                    <li>Pastikan nama perusahaan sudah terdaftar di database</li>
                                    <li>Nilai untuk setiap kriteria: <strong>1</strong>, <strong>2</strong>, atau <strong>3</strong></li>
                                    <li><strong>1</strong> = Kriteria terbaik/tertinggi</li>
                                    <li><strong>2</strong> = Kriteria menengah</li>
                                    <li><strong>3</strong> = Kriteria terendah/tidak ada</li>
                                    <li>Hasil klasifikasi (A, B, C) akan diset oleh admin melalui interface</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Kriteria Explanation -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-blue-800 mb-2">Contoh Penilaian Kriteria:</p>
                        <div class="text-xs text-blue-700 space-y-1">
                            <p>• <strong>Mesin Pembersih Gabah:</strong> 1 = Ada & >3 unit, 2 = Ada & 1-3 unit, 3 = Tidak Ada</p>
                            <p>• <strong>Lantai Jemur:</strong> 1 = Ada & >1000m², 2 = Ada & 500-1000m², 3 = Tidak Ada / <500m²</p>
                            <p>• <strong>Tangki Penyimpanan:</strong> 1 = Ada & >10 unit, 2 = Ada & ≤10 unit, 3 = Tidak Ada</p>
                            <p class="mt-2 italic">* Lihat template Excel untuk detail lengkap semua kriteria</p>
                        </div>
                    </div>

                    <!-- Download Template Button -->
                    <div class="flex justify-center">
                        <button
                            @click="downloadTemplate"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 font-medium text-sm shadow-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Template Excel
                        </button>
                    </div>

                    <!-- File Upload -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept=".xlsx,.xls,.csv"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                        />
                        <p v-if="selectedFile" class="mt-2 text-sm text-green-600">
                            File dipilih: {{ selectedFile.name }}
                        </p>
                    </div>

                    <!-- Error Message -->
                    <div v-if="uploadError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm whitespace-pre-line">
                        {{ uploadError }}
                    </div>

                    <!-- Success Message -->
                    <div v-if="uploadSuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        {{ uploadSuccess }}
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        @click="closeImportModal"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium text-sm"
                    >
                        Batal
                    </button>
                    <button
                        @click="uploadFile"
                        :disabled="!selectedFile || isUploading"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                    >
                        <svg v-if="isUploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-if="isUploading">Mengupload...</span>
                        <span v-else>Upload & Import</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>