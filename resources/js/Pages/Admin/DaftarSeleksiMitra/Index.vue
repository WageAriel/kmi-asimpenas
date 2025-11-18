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
const hasilSeleksi = ref(null);
const loadingHasilSeleksi = ref(false);

// Ref untuk kesimpulan kriteria
const kesimpulanDokumen = ref('Lolos');
const kesimpulanSaranaPengeringan = ref('Lolos');
const kesimpulanSaranaPenggilingan = ref('Lolos');
const kesimpulanAkhir = ref('Lolos');

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
    return val === 'Ada'
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
};

// Function to fetch hasil seleksi
const fetchHasilSeleksi = async (idSeleksiMitra) => {
    loadingHasilSeleksi.value = true;
    hasilSeleksi.value = null;
    
    try {
        const response = await axios.get(`/hasil-seleksi-mitra/by-seleksi/${idSeleksiMitra}`);
        hasilSeleksi.value = response.data;
        console.log('Hasil Seleksi Data:', response.data);
        console.log('Dokumen Tidak Ada:', response.data.dokumen_tidak_ada);
        console.log('Pengeringan Tidak Ada:', response.data.sarana_pengeringan_tidak_ada);
        console.log('Penggilingan Tidak Ada:', response.data.sarana_penggilingan_tidak_ada);
        
        // PENTING: Update ref kesimpulan dengan data yang sudah tersimpan
        if (response.data) {
            kesimpulanDokumen.value = response.data.kesimpulan_dokumen || 'Lolos';
            kesimpulanSaranaPengeringan.value = response.data.kesimpulan_sarana_pengeringan || 'Lolos';
            kesimpulanSaranaPenggilingan.value = response.data.kesimpulan_sarana_penggilingan || 'Lolos';
            kesimpulanAkhir.value = response.data.kesimpulan_akhir || 'Lolos';
            
            console.log('=== KESIMPULAN DI-LOAD DARI DATABASE ===');
            console.log('Dokumen:', kesimpulanDokumen.value);
            console.log('Pengeringan:', kesimpulanSaranaPengeringan.value);
            console.log('Penggilingan:', kesimpulanSaranaPenggilingan.value);
            console.log('Akhir:', kesimpulanAkhir.value);
        }
    } catch (error) {
        // Jika 404, berarti belum Ada hasil seleksi
        if (error.response?.status === 404) {
            hasilSeleksi.value = null;
            // Reset ke default jika belum ada hasil
            kesimpulanDokumen.value = 'Lolos';
            kesimpulanSaranaPengeringan.value = 'Lolos';
            kesimpulanSaranaPenggilingan.value = 'Lolos';
            kesimpulanAkhir.value = 'Lolos';
        } else {
            console.error('Error fetching hasil seleksi:', error);
        }
    } finally {
        loadingHasilSeleksi.value = false;
    }
};

// Function to open modal instead of navigating
const lihatSeleksi = async (item) => {
    selectedItem.value = item;
    showModal.value = true;
    errorMessage.value = null;
    successMessage.value = null;
    
    // Always try to fetch hasil seleksi
    await fetchHasilSeleksi(item.id_seleksi_mitra);
};

// Function to close modal
const closeModal = () => {
    showModal.value = false;
    selectedItem.value = null;
    hasilSeleksi.value = null;
    // Reset kesimpulan
    kesimpulanDokumen.value = 'Lolos';
    kesimpulanSaranaPengeringan.value = 'Lolos';
    kesimpulanSaranaPenggilingan.value = 'Lolos';
    kesimpulanAkhir.value = 'Lolos';
};

// Function to save validation results
const saveValidation = async () => {
    if (!selectedItem.value || isLoading.value) return;
    
    isLoading.value = true;
    errorMessage.value = null;
    successMessage.value = null;
    
    try {
        const validationData = {
            id_seleksi_mitra: selectedItem.value.id_seleksi_mitra,
            kesimpulan_dokumen: kesimpulanDokumen.value,
            kesimpulan_sarana_pengeringan: kesimpulanSaranaPengeringan.value,
            kesimpulan_sarana_penggilingan: kesimpulanSaranaPenggilingan.value,
            kesimpulan_akhir: kesimpulanAkhir.value,
        };

        // Add individual item validations if they exist
        if (selectedItem.value.status_surat_permohonan) {
            validationData.surat_permohonan = selectedItem.value.status_surat_permohonan === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_akta_notaris) {
            validationData.akta_notaris = selectedItem.value.status_akta_notaris === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_nib) {
            validationData.nib = selectedItem.value.status_nib === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_ktp) {
            validationData.ktp = selectedItem.value.status_ktp === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_no_rekening) {
            validationData.no_rekening = selectedItem.value.status_no_rekening === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_npwp) {
            validationData.npwp = selectedItem.value.status_npwp === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_surat_kuasa) {
            validationData.surat_kuasa = selectedItem.value.status_surat_kuasa === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_lantai_jemur) {
            validationData.lantai_jemur = selectedItem.value.status_lantai_jemur === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_sarana_lainnya) {
            validationData.sarana_lainnya = selectedItem.value.status_sarana_lainnya === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_mesin_memecah_kulit) {
            validationData.mesin_memecah_kulit = selectedItem.value.status_mesin_memecah_kulit === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_mesin_pemisah_gabah) {
            validationData.mesin_pemisah_gabah_dengan_beras = selectedItem.value.status_mesin_pemisah_gabah === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_mesin_penyosoh) {
            validationData.mesin_penyosoh = selectedItem.value.status_mesin_penyosoh === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }
        if (selectedItem.value.status_alat_pemisah_beras) {
            validationData.alat_pemisah_beras = selectedItem.value.status_alat_pemisah_beras === 'lolos' ? 'Lolos' : 'Tidak Lolos';
        }

        // Check if hasil seleksi exists (update) or create new
        if (hasilSeleksi.value) {
            await axios.put(`/hasil-seleksi-mitra/${hasilSeleksi.value.id_hasil_seleksi_mitra}`, validationData);
            successMessage.value = 'Hasil validasi berhasil diperbarui!';
        } else {
            await axios.post('/hasil-seleksi-mitra', validationData);
            successMessage.value = 'Hasil validasi berhasil disimpan!';
        }
        
        // Update status seleksi based on kesimpulan akhir
        const statusSeleksi = kesimpulanAkhir.value === 'Lolos' ? 'lolos' : 'tidak lolos';
        
        await axios.put(`/data-seleksi-mitra/${selectedItem.value.id_seleksi_mitra}`, {
            id_mitra: selectedItem.value.id_mitra,
            status_seleksi: statusSeleksi
        });
        
        // Close modal and reload after delay
        setTimeout(() => {
            closeModal();
            window.location.reload();
        }, 2000);
        
    } catch (error) {
        console.error('Error saving validation:', error);
        errorMessage.value = error.response?.data?.message || 'Gagal menyimpan validasi. Silakan coba lagi.';
    } finally {
        isLoading.value = false;
    }
};

// Add new refs
const showPdfModal = ref(false);
const selectedItemForPdf = ref(null);
const selectedKaryawan = ref('');
const karyawanList = ref([]);
const isGeneratingPdf = ref(false);

// Add new function to handle PDF modal
const showPdfDownloadModal = async (item) => {
    selectedItemForPdf.value = item;
    showPdfModal.value = true;
    try {
        const response = await axios.get('/api/karyawan');
        console.log(response.data); // Check data di console
        karyawanList.value = response.data;
    } catch (error) {
        console.error('Error fetching karyawan:', error);
    }
};

// Add function to generate PDF
const generatePdf = async () => {
    if (!selectedKaryawan.value) {
        alert('Silakan pilih karyawan terlebih dahulu');
        return;
    }

    isGeneratingPdf.value = true;
    try {
        const response = await axios.get(
            `/admin/seleksi-mitra/${selectedItemForPdf.value.id_seleksi_mitra}/surat-penetapan`, // Tambahkan prefix /admin/
            {
                params: { id_karyawan: selectedKaryawan.value },
                responseType: 'blob'
            }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `surat-penetapan-${selectedItemForPdf.value.mitra?.nama_perusahaan}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Terjadi kesalahan saat generate PDF: ' + (error.response?.data?.message || error.message));
    } finally {
        isGeneratingPdf.value = false;
        showPdfModal.value = false;
        selectedItemForPdf.value = null;
        selectedKaryawan.value = '';
    }
};

// Function to update status seleksi to "lolos"
const approveSeleksi = async () => {
    if (!selectedItem.value || isLoading.value) return;
    
    isLoading.value = true;
    errorMessage.value = null;
    successMessage.value = null;
    
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
            selectedItem.value.status_seleksi = 'lolos';
        }
        
        successMessage.value = 'Status berhasil diubah menjadi LOLOS. Data hasil seleksi telah dibuat/diperbarui.';
        
        // Close modal after delay
        setTimeout(() => {
            closeModal();
            // Reload halaman untuk refresh data
            window.location.reload();
        }, 2000);
    } catch (error) {
        console.error('Error approving seleksi:', error);
        errorMessage.value = error.response?.data?.message || 'Gagal mengubah status. Silakan coba lagi.';
    } finally {
        isLoading.value = false;
    }
};

// Function to update status seleksi to "tidak lolos"
const rejectSeleksi = async () => {
    if (!selectedItem.value || isLoading.value) return;
    
    isLoading.value = true;
    errorMessage.value = null;
    successMessage.value = null;
    
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
            selectedItem.value.status_seleksi = 'tidak lolos';
        }
        
        successMessage.value = 'Status berhasil diubah menjadi TIDAK LOLOS. Data hasil seleksi telah dibuat/diperbarui.';
        
        // Close modal after delay
        setTimeout(() => {
            closeModal();
            // Reload halaman untuk refresh data
            window.location.reload();
        }, 2000);
    } catch (error) {
        console.error('Error rejecting seleksi:', error);
        errorMessage.value = error.response?.data?.message || 'Gagal mengubah status. Silakan coba lagi.';
    } finally {
        isLoading.value = false;
    }
};

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
        const response = await axios.post('/data-seleksi-mitra/import', formData, {
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
                uploadError.value = `Terdapat error pAda file:\n${errorDetails}`;
                
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
    window.location.href = '/data-seleksi-mitra/export/template';
};

// Export function
const exportData = () => {
    window.location.href = '/data-seleksi-mitra/export/data';
};

// Delete functionality
const showDeleteModal = ref(false);
const selectedItemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref(null);

const openDeleteModal = (item) => {
    selectedItemToDelete.value = item;
    showDeleteModal.value = true;
    deleteError.value = null;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedItemToDelete.value = null;
    deleteError.value = null;
};

const confirmDelete = async () => {
    if (!selectedItemToDelete.value || isDeleting.value) return;
    
    isDeleting.value = true;
    deleteError.value = null;
    
    try {
        await axios.delete(`/data-seleksi-mitra/${selectedItemToDelete.value.id_seleksi_mitra}`);
        
        // Reload page after successful delete
        window.location.reload();
    } catch (error) {
        console.error('Delete error:', error);
        deleteError.value = error.response?.data?.message || 'Gagal menghapus data. Silakan coba lagi.';
    } finally {
        isDeleting.value = false;
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
                        <p class="text-white">
                            Berikut Adalah daftar mitra yang melakukan seleksi di sistem ASIMPENAS.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
                        <button
                            @click="openImportModal"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-200 font-medium shadow-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Import Excel
                        </button>
                        <button
                            @click="exportData"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium shadow-sm"
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
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Surat Penetapan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in filteredSeleksiMitras" :key="item.id_seleksi_mitra" class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">{{ item.mitra?.nama_perusahaan || '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.surat_permohonan)]">
                                    {{ item.surat_permohonan === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_surat_permohonan ? formatDate(item.mb_surat_permohonan) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.akta_notaris)]">
                                    {{ item.akta_notaris === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_akta_notaris ? formatDate(item.mb_akta_notaris) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.nib)]">
                                    {{ item.nib === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_nib ? formatDate(item.mb_nib) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.ktp)]">
                                    {{ item.ktp === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.no_rekening)]">
                                    {{ item.no_rekening === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_no_rekening ? formatDate(item.mb_no_rekening) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.npwp)]">
                                    {{ item.npwp === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_npwp ? formatDate(item.mb_npwp) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.surat_kuasa)]">
                                    {{ item.surat_kuasa === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ item.mb_surat_kuasa ? formatDate(item.mb_surat_kuasa) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.lantai_jemur)]">
                                    {{ item.lantai_jemur === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.sarana_lainnya)]">
                                    {{ item.sarana_lainnya === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_memecah_kulit)]">
                                    {{ item.mesin_memecah_kulit === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_pemisah_gabah)]">
                                    {{ item.mesin_pemisah_gabah === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_penyosoh)]">
                                    {{ item.mesin_penyosoh === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.alat_pemisah_beras)]">
                                    {{ item.alat_pemisah_beras === 'Ada' ? 'Ada' : 'Tidak Ada' }}
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
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="lihatSeleksi(item)"
                                        class="inline-flex items-center px-2 py-1 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded transition-colors duration-200 text-xs"
                                        title="Lihat detail seleksi"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat
                                    </button>
                                    <button
                                        @click="openDeleteModal(item)"
                                        class="inline-flex items-center px-2 py-1 text-red-600 hover:text-white hover:bg-red-600 border border-red-600 rounded transition-colors duration-200 text-xs"
                                        title="Hapus data seleksi"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button
                                    @click="showPdfDownloadModal(item)"
                                    class="inline-flex items-center px-2 py-1 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded transition-colors duration-200 text-xs"
                                    title="Download surat penetapan"
                                >
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredSeleksiMitras.length === 0">
                            <td colspan="22" class="px-4 py-6 text-center text-gray-500">
                                {{ searchQuery ? 'Tidak Ada data seleksi mitra yang sesuai dengan pencarian.' : 'Belum Ada data seleksi mitra.' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Seleksi Modal -->
        <div v-if="showModal" @click="closeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-4xl w-full h-auto p-6 relative max-h-[90vh] overflow-y-auto">
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
                    
                    <!-- Form Seleksi - Hanya tampil jika belum Ada hasil validasi -->
                    <template v-if="!hasilSeleksi && !loadingHasilSeleksi">
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
                                            selectedItem.surat_permohonan === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.surat_permohonan === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_surat_permohonan ? formatDate(selectedItem.mb_surat_permohonan) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_surat_permohonan"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                            selectedItem.akta_notaris === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.akta_notaris === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_akta_notaris ? formatDate(selectedItem.mb_akta_notaris) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_akta_notaris"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                            selectedItem.nib === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.nib === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_nib ? formatDate(selectedItem.mb_nib) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_nib"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                            selectedItem.ktp === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.ktp === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_ktp"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                            selectedItem.no_rekening === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.no_rekening === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_no_rekening ? formatDate(selectedItem.mb_no_rekening) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_no_rekening"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                            selectedItem.npwp === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.npwp === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_npwp ? formatDate(selectedItem.mb_npwp) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_npwp"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                            selectedItem.surat_kuasa === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ selectedItem.surat_kuasa === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">
                                            {{ selectedItem.mb_surat_kuasa ? formatDate(selectedItem.mb_surat_kuasa) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_surat_kuasa"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                        selectedItem.lantai_jemur === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.lantai_jemur === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_lantai_jemur"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                        selectedItem.sarana_lainnya === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.sarana_lainnya === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_sarana_lainnya"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                        selectedItem.mesin_memecah_kulit === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.mesin_memecah_kulit === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_mesin_memecah_kulit"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                        selectedItem.mesin_pemisah_gabah === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.mesin_pemisah_gabah === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_mesin_pemisah_gabah"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                        selectedItem.mesin_penyosoh === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.mesin_penyosoh === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_mesin_penyosoh"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
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
                                        selectedItem.alat_pemisah_beras === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ selectedItem.alat_pemisah_beras === 'Ada' ? 'Ada' : 'Tidak Ada' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Kelayakan</p>
                                    <select 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        v-model="selectedItem.status_alat_pemisah_beras"
                                        :disabled="selectedItem.status_seleksi !== 'pending'"
                                    >
                                        <option disabled value="">Pilih status</option>
                                        <option value="lolos">Lolos</option>
                                        <option value="tidak lolos">Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kesimpulan Kriteria - Hanya tampil jika belum ada hasil validasi -->
                    <div v-if="!hasilSeleksi && !loadingHasilSeleksi && selectedItem.status_seleksi === 'pending'" class="border-t border-gray-200 pt-5 mt-5">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700">📊 Kesimpulan Setiap Kriteria</h3>
                        <p class="text-sm text-gray-600 mb-4">Tentukan kesimpulan untuk setiap kategori berdasarkan validasi yang sudah dilakukan:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Kesimpulan Dokumen -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kesimpulan Dokumen Perizinan
                                </label>
                                <select 
                                    v-model="kesimpulanDokumen"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                    <option value="Lolos">✓ Lolos</option>
                                    <option value="Tidak Lolos">✗ Tidak Lolos</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Terpilih: {{ kesimpulanDokumen }}</p>
                            </div>
                            
                            <!-- Kesimpulan Sarana Pengeringan -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kesimpulan Sarana Pengeringan
                                </label>
                                <select 
                                    v-model="kesimpulanSaranaPengeringan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    @change="console.log('Pengeringan changed to:', kesimpulanSaranaPengeringan)"
                                >
                                    <option value="Lolos">✓ Lolos</option>
                                    <option value="Tidak Lolos">✗ Tidak Lolos</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Terpilih: {{ kesimpulanSaranaPengeringan }}</p>
                            </div>
                            
                            <!-- Kesimpulan Sarana Penggilingan -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kesimpulan Sarana Penggilingan
                                </label>
                                <select 
                                    v-model="kesimpulanSaranaPenggilingan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                    <option value="Lolos">✓ Lolos</option>
                                    <option value="Tidak Lolos">✗ Tidak Lolos</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Terpilih: {{ kesimpulanSaranaPenggilingan }}</p>
                            </div>
                        </div>
                        
                        <!-- Kesimpulan Akhir -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-5 mb-4">
                            <label class="block text-base font-bold text-purple-900 mb-3">
                                🏆 Kesimpulan Akhir Validasi
                            </label>
                            <select 
                                v-model="kesimpulanAkhir"
                                class="w-full px-4 py-3 text-lg font-semibold border-2 border-purple-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                :class="kesimpulanAkhir === 'Lolos' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'"
                            >
                                <option value="Lolos">✓ LOLOS</option>
                                <option value="Tidak Lolos">✗ TIDAK LOLOS</option>
                            </select>
                            <p class="text-xs font-semibold text-purple-700 mt-2">Terpilih: {{ kesimpulanAkhir }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <strong>Catatan:</strong> Kesimpulan akhir ditentukan oleh admin dan tidak otomatis mengikuti kesimpulan kriteria.
                            </p>
                        </div>
                    </div>
                    </template>
                    <!-- End Form Seleksi -->
                    
                    <!-- Status messages -->
                    <div v-if="errorMessage" class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded">
                        {{ errorMessage }}
                    </div>
                    
                    <div v-if="successMessage" class="bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded">
                        {{ successMessage }}
                    </div>
                    
                    <!-- Hasil Seleksi Section -->
                    <div v-if="loadingHasilSeleksi" class="border-t border-gray-200 pt-5">
                        <div class="flex items-center justify-center py-4">
                            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="ml-2 text-gray-600">Memuat hasil seleksi...</span>
                        </div>
                    </div>
                    
                    <div v-if="!loadingHasilSeleksi && hasilSeleksi" class="border-t border-gray-200 pt-5">
                        <h3 class="text-lg font-semibold mb-4 text-green-700">📋 Hasil Validasi Seleksi</h3>
                        
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg mb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Kesimpulan Akhir</p>
                                    <span :class="['inline-flex items-center px-3 py-1 rounded-full text-sm font-bold mt-1', 
                                        hasilSeleksi.kesimpulan_akhir === 'Lolos' ? 'bg-green-500 text-white' : 'bg-red-500 text-white']">
                                        {{ hasilSeleksi.kesimpulan_akhir === 'Lolos' ? '✓ LOLOS' : '✗ TIDAK LOLOS' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Tanggal Validasi</p>
                                    <p class="text-sm font-medium">{{ formatDate(hasilSeleksi.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kesimpulan per Kategori -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">Dokumen</p>
                                <span :class="['inline-flex items-center px-2 py-1 rounded text-xs font-semibold', 
                                    hasilSeleksi.kesimpulan_dokumen === 'Lolos' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                    {{ hasilSeleksi.kesimpulan_dokumen }}
                                </span>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">Sarana Pengeringan</p>
                                <span :class="['inline-flex items-center px-2 py-1 rounded text-xs font-semibold', 
                                    hasilSeleksi.kesimpulan_sarana_pengeringan === 'Lolos' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                    {{ hasilSeleksi.kesimpulan_sarana_pengeringan }}
                                </span>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">Sarana Penggilingan</p>
                                <span :class="['inline-flex items-center px-2 py-1 rounded text-xs font-semibold', 
                                    hasilSeleksi.kesimpulan_sarana_penggilingan === 'Lolos' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                    {{ hasilSeleksi.kesimpulan_sarana_penggilingan }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Detail Dokumen -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Dokumen Ada & Valid (Lolos) -->
                            <div v-if="hasilSeleksi.dokumen_Ada_valid && hasilSeleksi.dokumen_Ada_valid.length > 0" 
                                 class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-green-800 mb-2">✓ Dokumen Lolos</p>
                                <ul class="space-y-1">
                                    <li v-for="(dok, index) in hasilSeleksi.dokumen_Ada_valid" :key="index" 
                                        class="text-xs text-green-700 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ dok }}
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Dokumen Ada Tidak Valid (Ada tapi Tidak Lolos) -->
                            <div v-if="hasilSeleksi.dokumen_ada_tidak_valid && hasilSeleksi.dokumen_ada_tidak_valid.length > 0" 
                                 class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-orange-800 mb-2">⚠ Dokumen Ada Tidak Valid</p>
                                <ul class="space-y-1">
                                    <li v-for="(dok, index) in hasilSeleksi.dokumen_ada_tidak_valid" :key="'ada-tidak-valid-' + index" 
                                        class="text-xs text-orange-700 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ dok }}
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Dokumen Tidak Ada -->
                            <div v-if="hasilSeleksi.dokumen_tidak_ada && hasilSeleksi.dokumen_tidak_ada.length > 0" 
                                 class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-red-800 mb-2">✗ Dokumen Tidak Ada</p>
                                <ul class="space-y-1">
                                    <li v-for="(dok, index) in hasilSeleksi.dokumen_tidak_ada" :key="index" 
                                        class="text-xs text-red-700 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ dok }}
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Sarana Pengeringan Lolos -->
                            <div v-if="hasilSeleksi.sarana_pengeringan_Ada && hasilSeleksi.sarana_pengeringan_Ada.length > 0" 
                                 class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-green-800 mb-2">✓ Sarana Pengeringan Lolos</p>
                                <ul class="space-y-1">
                                    <li v-for="(sarana, index) in hasilSeleksi.sarana_pengeringan_Ada" :key="index" 
                                        class="text-xs text-green-700 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ sarana }}
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Sarana Pengeringan Tidak Lolos -->
                            <div v-if="hasilSeleksi.sarana_pengeringan_tidak_ada && hasilSeleksi.sarana_pengeringan_tidak_ada.length > 0" 
                                 class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-red-800 mb-2">✗ Sarana Pengeringan Tidak Lolos</p>
                                <ul class="space-y-1">
                                    <li v-for="(sarana, index) in hasilSeleksi.sarana_pengeringan_tidak_ada" :key="index" 
                                        class="text-xs text-red-700 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ sarana }}
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Sarana Penggilingan Lolos -->
                            <div v-if="hasilSeleksi.sarana_penggilingan_Ada && hasilSeleksi.sarana_penggilingan_Ada.length > 0" 
                                 class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-green-800 mb-2">✓ Sarana Penggilingan Lolos</p>
                                <ul class="space-y-1">
                                    <li v-for="(sarana, index) in hasilSeleksi.sarana_penggilingan_Ada" :key="index" 
                                        class="text-xs text-green-700 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ sarana }}
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Sarana Penggilingan Tidak Lolos -->
                            <div v-if="hasilSeleksi.sarana_penggilingan_tidak_ada && hasilSeleksi.sarana_penggilingan_tidak_ada.length > 0" 
                                 class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-red-800 mb-2">✗ Sarana Penggilingan Tidak Lolos</p>
                                <ul class="space-y-1">
                                    <li v-for="(sarana, index) in hasilSeleksi.sarana_penggilingan_tidak_ada" :key="index" 
                                        class="text-xs text-red-700 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ sarana }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Status Individual Fields -->
                        <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm font-semibold text-gray-700 mb-3">Detail Status per Item:</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                <div v-if="hasilSeleksi.surat_permohonan" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.surat_permohonan === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Surat Permohonan: {{ hasilSeleksi.surat_permohonan }}</span>
                                </div>
                                <div v-if="hasilSeleksi.akta_notaris" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.akta_notaris === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Akta Notaris: {{ hasilSeleksi.akta_notaris }}</span>
                                </div>
                                <div v-if="hasilSeleksi.nib" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.nib === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">NIB: {{ hasilSeleksi.nib }}</span>
                                </div>
                                <div v-if="hasilSeleksi.ktp" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.ktp === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">KTP: {{ hasilSeleksi.ktp }}</span>
                                </div>
                                <div v-if="hasilSeleksi.no_rekening" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.no_rekening === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">No Rekening: {{ hasilSeleksi.no_rekening }}</span>
                                </div>
                                <div v-if="hasilSeleksi.npwp" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.npwp === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">NPWP: {{ hasilSeleksi.npwp }}</span>
                                </div>
                                <div v-if="hasilSeleksi.surat_kuasa" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.surat_kuasa === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Surat Kuasa: {{ hasilSeleksi.surat_kuasa }}</span>
                                </div>
                                <div v-if="hasilSeleksi.lantai_jemur" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.lantai_jemur === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Lantai Jemur: {{ hasilSeleksi.lantai_jemur }}</span>
                                </div>
                                <div v-if="hasilSeleksi.sarana_lainnya" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.sarana_lainnya === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Sarana Lainnya: {{ hasilSeleksi.sarana_lainnya }}</span>
                                </div>
                                <div v-if="hasilSeleksi.mesin_memecah_kulit" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.mesin_memecah_kulit === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Mesin Memecah Kulit: {{ hasilSeleksi.mesin_memecah_kulit }}</span>
                                </div>
                                <div v-if="hasilSeleksi.mesin_pemisah_gabah_dengan_beras" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.mesin_pemisah_gabah_dengan_beras === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Mesin Pemisah Gabah: {{ hasilSeleksi.mesin_pemisah_gabah_dengan_beras }}</span>
                                </div>
                                <div v-if="hasilSeleksi.mesin_penyosoh" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.mesin_penyosoh === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Mesin Penyosoh: {{ hasilSeleksi.mesin_penyosoh }}</span>
                                </div>
                                <div v-if="hasilSeleksi.alat_pemisah_beras" class="flex items-center">
                                    <span :class="['w-2 h-2 rounded-full mr-2', hasilSeleksi.alat_pemisah_beras === 'Lolos' ? 'bg-green-500' : 'bg-red-500']"></span>
                                    <span class="text-gray-600">Alat Pemisah Beras: {{ hasilSeleksi.alat_pemisah_beras }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 mt-6">
                        <!-- Tombol Simpan Validasi - hanya tampil jika belum ada hasil validasi -->
                        <button 
                            v-if="!hasilSeleksi && selectedItem.status_seleksi === 'pending'"
                            @click="saveValidation" 
                            :disabled="isLoading"
                            :class="[
                                'px-6 py-2 rounded-md font-semibold text-sm transition-colors shadow-md',
                                isLoading
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed' 
                                    : 'bg-blue-600 text-white hover:bg-blue-700'
                            ]"
                        >
                            <span v-if="isLoading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                            <span v-else>💾 Simpan Hasil Validasi</span>
                        </button>
                        
                        <!-- Info jika sudah divalidasi -->
                        <div v-if="hasilSeleksi" class="flex items-center bg-green-50 px-4 py-2 rounded-md border border-green-200">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-green-700">Sudah Divalidasi</span>
                        </div>
                        
                        <button @click="closeModal" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-semibold text-sm shadow-md">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Import Excel -->
        <div v-if="showImportModal" @click="closeImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative">
                <button @click="closeImportModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h2 class="text-xl font-bold mb-6 text-blue-700">Import Data Seleksi Mitra dari Excel</h2>
                
                <div class="space-y-4">
                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-1">Panduan Import:</p>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li>File harus berformat Excel (.xlsx, .xls) atau CSV</li>
                                    <li>Ukuran file maksimal 5MB</li>
                                    <li>Kolom <strong>Nama Perusahaan</strong> akan digunakan untuk mencari ID mitra</li>
                                    <li>Pastikan nama perusahaan sudah terdaftar di database</li>
                                    <li>Nilai dokumen/sarana: "1. Ada" atau "2. Tidak Ada"</li>
                                    <li>Format tanggal: DD/MM/YYYY atau gunakan format tanggal Excel</li>
                                    <li>Untuk MB KTP bisa diisi "Seumur Hidup"</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Download Template Button -->
                    <div class="flex justify-center">
                        <button
                            @click="downloadTemplate"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium text-sm shadow-sm"
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
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
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
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
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

        <!-- Add PDF Modal -->
        <div v-if="showPdfModal" @click="showPdfModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative">
                <button @click="showPdfModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <h2 class="text-xl font-bold mb-6">Generate Surat Penetapan</h2>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Penandatangan
                    </label>
                    <select 
                        v-model="selectedKaryawan"
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

                <div class="flex justify-end space-x-4">
                    <button 
                        @click="showPdfModal = false"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium text-sm"
                    >
                        Batal
                    </button>
                    <button
                        @click="generatePdf"
                        :disabled="!selectedKaryawan || isGeneratingPdf"
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

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" @click="closeDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative">
                <button @click="closeDeleteModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h2 class="text-xl font-bold mb-2 text-center text-gray-900">Konfirmasi Hapus Data</h2>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Apakah Anda yakin ingin menghapus data seleksi mitra untuk 
                    <strong class="text-gray-900">{{ selectedItemToDelete?.mitra?.nama_perusahaan }}</strong>?
                    <br><br>
                    <span class="text-red-600 font-semibold">Tindakan ini tidak dapat dibatalkan!</span>
                </p>

                <!-- Error Message -->
                <div v-if="deleteError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
                    {{ deleteError }}
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="closeDeleteModal"
                        :disabled="isDeleting"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Batal
                    </button>
                    <button
                        @click="confirmDelete"
                        :disabled="isDeleting"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                    >
                        <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ isDeleting ? 'Menghapus...' : 'Ya, Hapus Data' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
