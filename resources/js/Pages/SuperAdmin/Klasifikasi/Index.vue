<script setup>
import { Head } from '@inertiajs/vue3';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
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
const selectedYear = ref(''); // Filter tahun

// Get unique years from klasifikasi mitras
const availableYears = computed(() => {
    const years = sortedKlasifikasiMitras.value.map(item => {
        if (item.created_at) {
            return new Date(item.created_at).getFullYear();
        }
        return null;
    }).filter(year => year !== null);
    
    // Return unique years sorted descending
    return [...new Set(years)].sort((a, b) => b - a);
});

// Filter klasifikasis based on search query and year
const filteredKlasifikasiMitras = computed(() => {
    let filtered = sortedKlasifikasiMitras.value;
    
    // Filter by year
    if (selectedYear.value) {
        filtered = filtered.filter(item => {
            if (item.created_at) {
                const itemYear = new Date(item.created_at).getFullYear();
                return itemYear === parseInt(selectedYear.value);
            }
            return false;
        });
    }
    
    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item => {
            return (
                item.mitra?.nama_perusahaan?.toLowerCase().includes(query) ||
                (item.hasil_klasifikasi?.toLowerCase().includes(query))
            );
        });
    }
    
    return filtered;
});

// Pagination state
const currentPage = ref(1);
const itemsPerPage = ref(10);
const itemsPerPageOptions = [10, 20, 50, 100];

// Bulk delete state
const selectedIds = ref([]);
const selectAll = ref(false);
const showBulkDeleteModal = ref(false);
const isBulkDeleting = ref(false);
const bulkDeleteSuccess = ref('');
const bulkDeleteError = ref('');

// Computed: Total pages
const totalPages = computed(() => {
    return Math.ceil(filteredKlasifikasiMitras.value.length / itemsPerPage.value);
});

// Computed: Paginated data
const paginatedKlasifikasiMitras = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredKlasifikasiMitras.value.slice(start, end);
});

// Computed: Visible page numbers
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
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        selectedIds.value = [];
        selectAll.value = false;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        selectedIds.value = [];
        selectAll.value = false;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        selectedIds.value = [];
        selectAll.value = false;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// Bulk delete methods
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedIds.value = paginatedKlasifikasiMitras.value.map(item => item.id_klasifikasi_mitra);
    } else {
        selectedIds.value = [];
    }
};

const toggleSelection = (id) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
    selectAll.value = selectedIds.value.length === paginatedKlasifikasiMitras.value.length;
};

const openBulkDeleteModal = () => {
    if (selectedIds.value.length === 0) return;
    showBulkDeleteModal.value = true;
    bulkDeleteSuccess.value = '';
    bulkDeleteError.value = '';
};

const closeBulkDeleteModal = () => {
    showBulkDeleteModal.value = false;
    bulkDeleteSuccess.value = '';
    bulkDeleteError.value = '';
};

const bulkDelete = async () => {
    if (selectedIds.value.length === 0) return;

    isBulkDeleting.value = true;
    bulkDeleteError.value = '';
    bulkDeleteSuccess.value = '';

    try {
        const response = await axios.post('/api/klasifikasi-mitra/bulk-delete', {
            ids: selectedIds.value
        });

        bulkDeleteSuccess.value = response.data.message || `Berhasil menghapus ${selectedIds.value.length} data klasifikasi mitra`;
        
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    } catch (error) {
        console.error('Error bulk deleting:', error);
        if (error.response && error.response.data && error.response.data.message) {
            bulkDeleteError.value = error.response.data.message;
        } else {
            bulkDeleteError.value = 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.';
        }
        isBulkDeleting.value = false;
    }
};

// Watchers
watch(currentPage, () => {
    selectedIds.value = [];
    selectAll.value = false;
});

watch(searchQuery, () => {
    currentPage.value = 1;
    selectedIds.value = [];
    selectAll.value = false;
});

watch(selectedYear, () => {
    currentPage.value = 1;
    selectedIds.value = [];
    selectAll.value = false;
});

watch(selectedIds, () => {
    selectAll.value = selectedIds.value.length === paginatedKlasifikasiMitras.value.length && paginatedKlasifikasiMitras.value.length > 0;
});

// Method to change items per page
const changeItemsPerPage = (value) => {
    itemsPerPage.value = value;
    currentPage.value = 1;
};

// Modal functionality
const showModal = ref(false);
const selectedKlasifikasi = ref(null);

const interpretClassification = (field, value) => {
    // Jika value null atau undefined, return '-'
    if (!value) return '-';
    
    // Jika value sudah dalam format deskriptif baru (mengandung titik), langsung return
    // Format baru: '1. Tidak Ada', '2. Ada | â‰¤ 20', '3. Ada | > 20'
    if (typeof value === 'string' && value.includes('.')) {
        return value;
    }
    
    // Fallback: Legacy mapping untuk backward compatibility dengan data lama (angka 1, 2, 3)
    const legacyMap = {
        mesin_pembersih_gabah: {
            3: '3. Ada | > 20',
            2: '2. Ada | â‰¤ 20',
            1: '1. Tidak Ada',
        },
        lantai_jemur: {
            3: '3. Ada | > 10',
            2: '2. Ada | 1 s/d 10',
            1: '1. Tidak ada',
        },
        mesin_pengering: {
            3: '3. Ada | > 20',
            2: '2. Ada | â‰¤ 20',
            1: '1. Tidak ada',
        },
        alat_pengering_lainnya: {
            3: '3. Tidak Disyaratkan',
            2: '2. Tidak Disyaratkan',
            1: '1. Ada | â‰¤ 1',
        },
        mesin_pembersih_awal: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        mesin_pemecah_kulit: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        mesin_pembersih_sekam: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        mesin_pemisah_gabah_pecah_kulit: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        mesin_pemisah_batu: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        mesin_penyosoh: {
            3: '3. Ada | > 3 | 2',
            2: '2. Ada | 1 s/d 3 | 1',
            1: '1. Ada | â‰¤ 1 | 1',
        },
        mesin_pengkabut: {
            3: '3. Ada | > 3 | 2',
            2: '2. Ada | 1 s/d 3 | 1',
            1: '1. Ada | â‰¤ 1 | 1',
        },
        mesin_pemesah_menir: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        mesin_pemisah_katul: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        mesin_pemisah_berdasarkan_ukuran: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Ada | â‰¤ 1',
        },
        mesin_pemisah_berdasarkan_warna: {
            3: '3. Ada | > 3',
            2: '2. Ada | 1 s/d 3',
            1: '1. Tidak ada',
        },
        tangki_penyimpanan: {
            3: '3. Ada | > 10',
            2: '2. Ada | â‰¤ 10',
            1: '1. Tidak ada',
        },
        mesin_pengemas: {
            3: '3. Ada | Full Otomatis',
            2: '2. Ada | Semi Otomatis',
            1: '1. Tidak ada',
        },
        mesin_jahit: {
            3: '3. Ada | Full Otomatis',
            2: '2. Ada | Semi Otomatis',
            1: '1. Tidak ada',
        },
        gudang_konvensional: {
            3: '3. Ada | > 3000',
            2: '2. Ada | < 3000',
            1: '1. Tidak ada',
        },
        silo_gkg_hopper: {
            3: '3. Ada | > 2000',
            2: '2. Tidak Ada',
            1: '1. Tidak ada',
        },
        truk: {
            3: '3. Ada | > 5',
            2: '2. Ada | 1 s/d 5',
            1: '1. Tidak ada',
        },
        mini_lab: {
            3: '3. Ada | Ruang Khusus',
            2: '2. Ada | Tidak Khusus',
            1: '1. Tidak ada',
        },
        moisture_tester: {
            3: '3. Ada | Lengkap | Berfungsi',
            2: '2. Ada | Berfungsi',
            1: '1. Tidak ada',
        },
        pembanding_derajat_sosoh: {
            3: '3. Ada | Sesuai Standar',
            2: '2. Ada | Tidak Sesuai',
            1: '1. Tidak ada',
        },
        bagian_quality_control: {
            3: '3. Ada | Tidak Merangkap',
            2: '2. Ada | Merangkap',
            1: '1. Tidak ada',
        },
    };
    
    // Cek apakah ada mapping untuk field dan value ini
    if (legacyMap[field] && value in legacyMap[field]) {
        return legacyMap[field][value];
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

// Verification modal state
const showVerificationModal = ref(false);
const selectedClassificationResult = ref('');

// Open verification modal
const openVerificationModal = (result) => {
    selectedClassificationResult.value = result;
    showVerificationModal.value = true;
    showDropdown.value = false;
};

// Close verification modal
const closeVerificationModal = () => {
    showVerificationModal.value = false;
    selectedClassificationResult.value = '';
};

// Update classification result
const updateKlasifikasi = async () => {
    if (isUpdating.value || !selectedKlasifikasi.value || !selectedClassificationResult.value) return;
    
    isUpdating.value = true;
    errorMessage.value = '';
    successMessage.value = '';
    
    try {
        await axios.put(`/klasifikasi-mitra/${selectedKlasifikasi.value.id_klasifikasi_mitra}`, {
            hasil_klasifikasi: selectedClassificationResult.value
        });
        
        // Update local data
        const index = props.klasifikasiMitras.findIndex(item => item.id_klasifikasi_mitra === selectedKlasifikasi.value.id_klasifikasi_mitra);
        if (index !== -1) {
            props.klasifikasiMitras[index].hasil_klasifikasi = selectedClassificationResult.value;
        }
        
        // Update selected klasifikasi if it's the one being viewed in modal
        if (selectedKlasifikasi.value) {
            selectedKlasifikasi.value.hasil_klasifikasi = selectedClassificationResult.value;
        }
        
        successMessage.value = `Berhasil memverifikasi mitra ke Kelas ${selectedClassificationResult.value}`;
        showVerificationModal.value = false;
        selectedClassificationResult.value = '';
        
        // Auto close success message after 3 seconds
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
    } catch (error) {
        console.error('Error updating klasifikasi:', error);
        errorMessage.value = 'Gagal memperbarui hasil klasifikasi. Silakan coba lagi.';
        showVerificationModal.value = false;
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

const downloadKlasifikasiMitra = (classificationId) => {
  if (!classificationId) {
    console.error('Classification ID is undefined');
    return;
  }
  
  const downloadUrl = `/super-admin/klasifikasi-mitra/${classificationId}/download-form`;
  console.log('Attempting to download from:', downloadUrl);
  
  // Using window.open for direct download
  window.open(downloadUrl, '_blank');
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
    isDeleting.value = false;
};

const confirmDelete = async () => {
    if (!selectedItemToDelete.value) return;

    isDeleting.value = true;
    deleteError.value = null;

    try {
        await axios.delete(`/klasifikasi-mitra/${selectedItemToDelete.value.id_klasifikasi_mitra}`);
        
        // Reload page after successful deletion
        window.location.reload();
    } catch (error) {
        console.error('Error deleting klasifikasi:', error);
        if (error.response && error.response.data && error.response.data.message) {
            deleteError.value = error.response.data.message;
        } else {
            deleteError.value = 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.';
        }
        isDeleting.value = false;
    }
};

// Add new refs
const showPdfModal = ref(false);
const selectedItemForPdf = ref(null);
const selectedKaryawan = ref('');
const karyawanList = ref([]);
const isGeneratingPdf = ref(false);

// Add new refs for BA Klasifikasi
const showBaPdfModal = ref(false);
const selectedItemForBaPdf = ref(null);
const selectedPelaksana = ref('');
const selectedPengetahui = ref('');
const isGeneratingBaPdf = ref(false);

// Add new function to handle PDF modal
const showPdfDownloadModal = async (item) => {
    selectedItemForPdf.value = item;
    showPdfModal.value = true;
    try {
        const response = await axios.get('/api/karyawan');
        karyawanList.value = response.data;
    } catch (error) {
        console.error('Error fetching karyawan:', error);
    }
};

// Add generatePdf function
const generatePdf = async () => {
    if (!selectedKaryawan.value) {
        alert('Silakan pilih karyawan terlebih dahulu');
        return;
    }

    isGeneratingPdf.value = true;
    try {
        const response = await axios.get(
            `/super-admin/klasifikasi-mitra/${selectedItemForPdf.value.id_klasifikasi_mitra}/surat-penetapan`,
            {
                params: { id_karyawan: selectedKaryawan.value },
                responseType: 'blob'
            }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `surat-penetapan-klasifikasi-${selectedItemForPdf.value.mitra?.nama_perusahaan}.pdf`);
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
        selectedKaryawan.value = '';
    }
};

// Add new function for BA Klasifikasi modal
const showBaPdfDownloadModal = async (item) => {
    selectedItemForBaPdf.value = item;
    showBaPdfModal.value = true;
    try {
        const response = await axios.get('/api/karyawan');
        karyawanList.value = response.data;
    } catch (error) {
        console.error('Error fetching karyawan:', error);
    }
};

// Add generateBaPdf function
const generateBaPdf = async () => {
    if (!selectedPelaksana.value || !selectedPengetahui.value) {
        alert('Silakan pilih pelaksana dan pengetahui terlebih dahulu');
        return;
    }

    isGeneratingBaPdf.value = true;
    try {
        const response = await axios.get(
            `/super-admin/klasifikasi-mitra/${selectedItemForBaPdf.value.id_klasifikasi_mitra}/berita-acara`,
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
        link.setAttribute('download', `BA-klasifikasi-${selectedItemForBaPdf.value.mitra?.nama_perusahaan}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error generating BA PDF:', error);
        alert('Terjadi kesalahan saat generate PDF');
    } finally {
        isGeneratingBaPdf.value = false;
        showBaPdfModal.value = false;
        selectedItemForBaPdf.value = null;
        selectedPelaksana.value = '';
        selectedPengetahui.value = '';
    }
};

// Edit functionality
const showEditModal = ref(false);
const itemToEdit = ref(null);
const editFormData = ref({});
const isEditing = ref(false);
const editError = ref('');
const editSuccess = ref('');

const openEditModal = (item) => {
    itemToEdit.value = item;
    editFormData.value = {
        mesin_pembersih_gabah: item.mesin_pembersih_gabah || '',
        lantai_jemur: item.lantai_jemur || '',
        mesin_pengering: item.mesin_pengering || '',
        alat_pengering_lainnya: item.alat_pengering_lainnya || '',
        mesin_pembersih_awal: item.mesin_pembersih_awal || '',
        mesin_pemecah_kulit: item.mesin_pemecah_kulit || '',
        mesin_pembersih_sekam: item.mesin_pembersih_sekam || '',
        mesin_pemisah_gabah_pecah_kulit: item.mesin_pemisah_gabah_pecah_kulit || '',
        mesin_pemisah_batu: item.mesin_pemisah_batu || '',
        mesin_penyosoh: item.mesin_penyosoh || '',
        mesin_pengkabut: item.mesin_pengkabut || '',
        mesin_pemesah_menir: item.mesin_pemesah_menir || '',
        mesin_pemisah_katul: item.mesin_pemisah_katul || '',
        mesin_pemisah_berdasarkan_ukuran: item.mesin_pemisah_berdasarkan_ukuran || '',
        mesin_pemisah_berdasarkan_warna: item.mesin_pemisah_berdasarkan_warna || '',
        tangki_penyimpanan: item.tangki_penyimpanan || '',
        mesin_pengemas: item.mesin_pengemas || '',
        mesin_jahit: item.mesin_jahit || '',
        gudang_konvensional: item.gudang_konvensional || '',
        silo_gkg_hopper: item.silo_gkg_hopper || '',
        truk: item.truk || '',
        mini_lab: item.mini_lab || '',
        moisture_tester: item.moisture_tester || '',
        pembanding_derajat_sosoh: item.pembanding_derajat_sosoh || '',
        bagian_quality_control: item.bagian_quality_control || '',
    };
    showEditModal.value = true;
    editError.value = '';
    editSuccess.value = '';
};

const closeEditModal = () => {
    showEditModal.value = false;
    itemToEdit.value = null;
    editFormData.value = {};
    isEditing.value = false;
    editError.value = '';
    editSuccess.value = '';
};

const updateKlasifikasiMitra = async () => {
    if (isEditing.value || !itemToEdit.value) return;

    isEditing.value = true;
    editError.value = '';
    editSuccess.value = '';

    try {
        await axios.put(`/klasifikasi-mitra/${itemToEdit.value.id_klasifikasi_mitra}`, editFormData.value);
        
        editSuccess.value = 'Data klasifikasi mitra berhasil diperbarui';
        
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    } catch (error) {
        console.error('Error updating klasifikasi mitra:', error);
        if (error.response && error.response.data && error.response.data.message) {
            editError.value = error.response.data.message;
        } else {
            editError.value = 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.';
        }
        isEditing.value = false;
    }
};

</script>

<template>
    <Head title="Daftar Klasifikasi Mitra - Super Admin" />

    <SuperAdminLayout>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Import Excel
                        </button>
                        <button
                            @click="exportData"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 font-medium shadow-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Bar and Year Filter -->
            <div class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="relative md:col-span-3">
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

                <!-- Items Per Page Selector -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-700 whitespace-nowrap">Tampilkan:</label>
                    <select 
                        v-model="itemsPerPage"
                        @change="changeItemsPerPage(itemsPerPage)"
                        class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-green-500"
                    >
                        <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>

                <!-- Year Filter -->
                <div class="relative">
                    <select 
                        v-model="selectedYear"
                        class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-green-500"
                    >
                        <option value="">Semua Tahun</option>
                        <option v-for="year in availableYears" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Bulk Delete Button -->
            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 transform -translate-y-2"
                enter-to-class="opacity-100 transform translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 transform translate-y-0"
                leave-to-class="opacity-0 transform -translate-y-2"
            >
                <div v-if="selectedIds.length > 0" class="mb-4">
                    <button
                        @click="openBulkDeleteModal"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium shadow-sm"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Terpilih ({{ selectedIds.length }})
                    </button>
                </div>
            </transition>

            <!-- Tabel Daftar Klasifikasi Mitra -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Klasifikasi Mitra</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input 
                                        type="checkbox" 
                                        v-model="selectAll" 
                                        @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                    />
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil Klasifikasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Form Klasifikasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil Klasifikasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Surat Penetapan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in paginatedKlasifikasiMitras" :key="item.id_klasifikasi_mitra" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input 
                                        type="checkbox" 
                                        :value="item.id_klasifikasi_mitra"
                                        v-model="selectedIds"
                                        @change="toggleSelection(item.id_klasifikasi_mitra)"
                                        class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                    />
                                </td>
                                <td class="text-xs px-4 py-3 whitespace-nowrap">{{ item.mitra?.nama_perusahaan }}</td>
                                <td class="text-xs px-4 py-3 whitespace-nowrap">{{ formatDate(item.created_at) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getClassificationColor(item.hasil_klasifikasi)]">
                                        {{ item.hasil_klasifikasi ? `Kelas ${item.hasil_klasifikasi}` : 'Belum Dinilai' }}
                                    </span>
                                </td>
                                
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button
                                        @click="downloadKlasifikasiMitra(item.id_klasifikasi_mitra)"
                                        class="inline-flex items-center px-2 py-1 text-green-800 hover:text-white hover:bg-green-800 border border-green-800 rounded transition-colors duration-200 text-xs"
                                >
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                    </button>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button
                                        @click="showBaPdfDownloadModal(item)"
                                        class="inline-flex items-center px-2 py-1 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded transition-colors duration-200 text-xs"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download
                                    </button>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button
                                        @click="showPdfDownloadModal(item)"
                                        class="inline-flex items-center px-2 py-1 text-green-600 hover:text-white hover:bg-green-600 border border-green-600 rounded transition-colors duration-200 text-xs"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download
                                    </button>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="viewDetails(item)"
                                            class="inline-flex items-center px-2 py-1 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded transition-colors duration-200 text-xs"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat
                                        </button>
                                        <button
                                            @click="openEditModal(item)"
                                            class="inline-flex items-center px-2 py-1 text-amber-600 hover:text-white hover:bg-amber-600 border border-amber-600 rounded transition-colors duration-200 text-xs"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            @click="openDeleteModal(item)"
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
                            <tr v-if="paginatedKlasifikasiMitras.length === 0">
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    {{ searchQuery ? 'Tidak ada klasifikasi mitra yang sesuai dengan pencarian Anda.' : 'Belum ada data klasifikasi mitra.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Info -->
                        <div class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-medium">{{ ((currentPage - 1) * itemsPerPage) + 1 }}</span>
                            sampai 
                            <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredKlasifikasiMitras.length) }}</span>
                            dari 
                            <span class="font-medium">{{ filteredKlasifikasiMitras.length }}</span>
                            data
                        </div>

                        <!-- Pagination Buttons -->
                        <div class="flex items-center gap-2">
                            <!-- Previous Button -->
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            <!-- Page Numbers -->
                            <template v-for="(page, index) in visiblePages" :key="index">
                                <!-- Ellipsis -->
                                <span v-if="page === '...'" class="px-3 py-2 text-sm text-gray-700">
                                    ...
                                </span>
                                <!-- Page Button -->
                                <button
                                    v-else
                                    @click="goToPage(page)"
                                    :class="[
                                        'px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                                        currentPage === page
                                            ? 'bg-green-600 text-white'
                                            : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </template>

                            <!-- Next Button -->
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail Klasifikasi Mitra -->
        <div v-if="showModal" @click="closeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-4xl w-full h-auto p-6 relative max-h-[80vh] overflow-y-auto">
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
                        <!-- Dropdown Menu -->
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
                                    @click="openVerificationModal('A')"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 flex items-center transition-colors"
                                    :disabled="isUpdating"
                                >
                                    <span class="w-6 h-6 rounded-full bg-green-100 text-green-800 flex items-center justify-center mr-3 font-bold text-xs">
                                        A
                                    </span>
                                    <span class="text-gray-700">Kelas A (Terbaik)</span>
                                </button>
                                <button
                                    @click="openVerificationModal('B')"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 flex items-center transition-colors"
                                    :disabled="isUpdating"
                                >
                                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center mr-3 font-bold text-xs">
                                        B
                                    </span>
                                    <span class="text-gray-700">Kelas B (Menengah)</span>
                                </button>
                                <button
                                    @click="openVerificationModal('C')"
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



        <!-- Add PDF Modal -->
        <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative">
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

        <!-- Add BA Klasifikasi PDF Modal -->
        <div v-if="showBaPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative">
                <button @click="showBaPdfModal = false; selectedPelaksana = ''; selectedPengetahui = '';" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <h2 class="text-xl font-bold mb-6">Generate Berita Acara Klasifikasi</h2>

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pelaksana Klasifikasi
                        </label>
                        <select 
                            v-model="selectedPelaksana"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            <option value="">Pilih Pelaksana</option>
                            <option v-for="karyawan in karyawanList" 
                                    :key="'pelaksana-' + karyawan.id_karyawan" 
                                    :value="karyawan.id_karyawan">
                                {{ karyawan.nama_karyawan }} - {{ karyawan.jabatan }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mengetahui (Pemimpin)
                        </label>
                        <select 
                            v-model="selectedPengetahui"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            <option value="">Pilih Pengetahui</option>
                            <option v-for="karyawan in karyawanList" 
                                    :key="'pengetahui-' + karyawan.id_karyawan" 
                                    :value="karyawan.id_karyawan">
                                {{ karyawan.nama_karyawan }} - {{ karyawan.jabatan }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button 
                        @click="showBaPdfModal = false; selectedPelaksana = ''; selectedPengetahui = '';"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium text-sm"
                    >
                        Batal
                    </button>
                    <button
                        @click="generateBaPdf"
                        :disabled="!selectedPelaksana || !selectedPengetahui || isGeneratingBaPdf"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                    >
                        <svg v-if="isGeneratingBaPdf" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isGeneratingBaPdf ? 'Generating...' : 'Generate PDF' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Verification Modal -->
        <div v-if="showVerificationModal" @click="closeVerificationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative">
                <button @click="closeVerificationModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="mb-4">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto rounded-full mb-4"
                         :class="{
                             'bg-green-100': selectedClassificationResult === 'A',
                             'bg-blue-100': selectedClassificationResult === 'B',
                             'bg-yellow-100': selectedClassificationResult === 'C'
                         }">
                        <svg class="w-6 h-6" 
                             :class="{
                                 'text-green-600': selectedClassificationResult === 'A',
                                 'text-blue-600': selectedClassificationResult === 'B',
                                 'text-yellow-600': selectedClassificationResult === 'C'
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 text-center">Konfirmasi Verifikasi</h3>
                    <p class="mt-2 text-sm text-gray-600 text-center">
                        Apakah Anda yakin ingin memverifikasi klasifikasi mitra 
                        <strong>{{ selectedKlasifikasi?.mitra?.nama_perusahaan }}</strong> 
                        ke <strong>Kelas {{ selectedClassificationResult }}</strong>?
                    </p>
                    <div class="mt-4 p-3 rounded-lg"
                         :class="{
                             'bg-green-50 border border-green-200': selectedClassificationResult === 'A',
                             'bg-blue-50 border border-blue-200': selectedClassificationResult === 'B',
                             'bg-yellow-50 border border-yellow-200': selectedClassificationResult === 'C'
                         }">
                        <p class="text-xs font-medium"
                           :class="{
                               'text-green-800': selectedClassificationResult === 'A',
                               'text-blue-800': selectedClassificationResult === 'B',
                               'text-yellow-800': selectedClassificationResult === 'C'
                           }">
                            <span v-if="selectedClassificationResult === 'A'">
                                ✓ Kelas A (Terbaik)
                            </span>
                            <span v-else-if="selectedClassificationResult === 'B'">
                                ✓ Kelas B (Menengah)
                            </span>
                            <span v-else-if="selectedClassificationResult === 'C'">
                                ✓ Kelas C (Standar)
                            </span>
                        </p>
                    </div>
                    <p class="mt-3 text-xs text-gray-500 text-center">
                        <strong>Perhatian:</strong> Setelah diverifikasi, hasil klasifikasi tidak dapat diubah kembali.
                    </p>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">{{ errorMessage }}</p>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        @click="closeVerificationModal"
                        :disabled="isUpdating"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="updateKlasifikasi"
                        :disabled="isUpdating"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-lg disabled:opacity-50"
                        :class="{
                            'bg-green-600 hover:bg-green-700': selectedClassificationResult === 'A',
                            'bg-blue-600 hover:bg-blue-700': selectedClassificationResult === 'B',
                            'bg-yellow-600 hover:bg-yellow-700': selectedClassificationResult === 'C'
                        }"
                    >
                        <span v-if="!isUpdating">Verifikasi</span>
                        <span v-else>Memproses...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Import Excel -->
        <div v-if="showImportModal" @click="closeImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-3xl w-full p-6 relative">
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
                                    <li>Hasil klasifikasi (A, B, C) akan diset oleh admin melalui interface</li>
                                </ul>
                            </div>
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
                    Apakah Anda yakin ingin menghapus data klasifikasi mitra untuk 
                    <span class="font-semibold">{{ selectedItemToDelete?.mitra?.nama_perusahaan || 'mitra ini' }}</span>?
                    <br>
                    <span class="text-red-600">Tindakan ini tidak dapat dibatalkan.</span>
                </p>

                <!-- Error Message -->
                <div v-if="deleteError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
                    {{ deleteError }}
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="closeDeleteModal"
                        :disabled="isDeleting"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium text-sm"
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
                        {{ isDeleting ? 'Menghapus...' : 'Ya, Hapus' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" @click="closeEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-6xl w-full p-6 relative max-h-[90vh] overflow-y-auto">
                <button @click="closeEditModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Header -->
                <div class="mb-6">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white px-6 py-4 rounded-lg -mx-6 -mt-6 mb-6">
                        <h2 class="text-xl font-bold">Edit Data Klasifikasi Mitra</h2>
                        <p class="text-sm text-amber-50 mt-1">{{ itemToEdit?.mitra?.nama_perusahaan }}</p>
                    </div>
                </div>

                <!-- Success Message -->
                <div v-if="editSuccess" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ editSuccess }}</p>
                </div>

                <!-- Error Message -->
                <div v-if="editError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">{{ editError }}</p>
                </div>

                <!-- Form -->
                <div v-if="itemToEdit" class="space-y-6">
                    <!-- Sarana Pengeringan Section -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-green-800 mb-4">Sarana Pengeringan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pembersih Gabah (ton/hari)</label>
                                <select v-model="editFormData.mesin_pembersih_gabah" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 20">A. Ada | > 20</option>
                                    <option value="2. Ada | ≤ 20">B. Ada | ≤ 20</option>
                                    <option value="1. Tidak Ada">C. Tidak Ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lantai Jemur (ton/hari)</label>
                                <select v-model="editFormData.lantai_jemur" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 10">A. Ada | > 10</option>
                                    <option value="2. Ada | 1 s/d 10">B. Ada | 1 s/d 10</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pengering (ton/hari)</label>
                                <select v-model="editFormData.mesin_pengering" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 20">A. Ada | > 20</option>
                                    <option value="2. Ada | ≤ 20">B. Ada | ≤ 20</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alat Pengering Lainnya</label>
                                <select v-model="editFormData.alat_pengering_lainnya" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Tidak Disyaratkan">A. Tidak Disyaratkan</option>
                                    <option value="2. Tidak Disyaratkan">B. Tidak Disyaratkan</option>
                                    <option value="1. Ada | ≤ 1">C. Ada | ≤ 1</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sarana Penggilingan Section -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-purple-800 mb-4">Sarana Penggilingan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pembersih Awal</label>
                                <select v-model="editFormData.mesin_pembersih_awal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pemecah Kulit</label>
                                <select v-model="editFormData.mesin_pemecah_kulit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pembersih Sekam</label>
                                <select v-model="editFormData.mesin_pembersih_sekam" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pemisah Gabah Pecah Kulit</label>
                                <select v-model="editFormData.mesin_pemisah_gabah_pecah_kulit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pemisah Batu</label>
                                <select v-model="editFormData.mesin_pemisah_batu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Penyosoh</label>
                                <select v-model="editFormData.mesin_penyosoh" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3 | 2">A. Ada | > 3 | 2</option>
                                    <option value="2. Ada | 1 s/d 3 | 1">B. Ada | 1 s/d 3 | 1</option>
                                    <option value="1. Ada | ≤ 1 | 1">C. Ada | ≤ 1 | 1</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pengkabut</label>
                                <select v-model="editFormData.mesin_pengkabut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3 | 2">A. Ada | > 3 | 2</option>
                                    <option value="2. Ada | 1 s/d 3 | 1">B. Ada | 1 s/d 3 | 1</option>
                                    <option value="1. Ada | ≤ 1 | 1">C. Ada | ≤ 1 | 1</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pemisah Menir</label>
                                <select v-model="editFormData.mesin_pemesah_menir" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pemisah Katul</label>
                                <select v-model="editFormData.mesin_pemisah_katul" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pemisah Berdasarkan Ukuran</label>
                                <select v-model="editFormData.mesin_pemisah_berdasarkan_ukuran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Ada | ≤ 1">C. Ada | ≤ 1</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pemisah Berdasarkan Warna</label>
                                <select v-model="editFormData.mesin_pemisah_berdasarkan_warna" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3">A. Ada | > 3</option>
                                    <option value="2. Ada | 1 s/d 3">B. Ada | 1 s/d 3</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sarana Lainnya Section -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-blue-800 mb-4">Sarana Lainnya</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tangki Penyimpanan</label>
                                <select v-model="editFormData.tangki_penyimpanan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 10">A. Ada | > 10</option>
                                    <option value="2. Ada | ≤ 10">B. Ada | ≤ 10</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Pengemas</label>
                                <select v-model="editFormData.mesin_pengemas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | Full Otomatis">A. Ada | Full Otomatis</option>
                                    <option value="2. Ada | Semi Otomatis">B. Ada | Semi Otomatis</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesin Jahit</label>
                                <select v-model="editFormData.mesin_jahit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | Full Otomatis">A. Ada | Full Otomatis</option>
                                    <option value="2. Ada | Semi Otomatis">B. Ada | Semi Otomatis</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gudang Konvensional</label>
                                <select v-model="editFormData.gudang_konvensional" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 3000">A. Ada | > 3000</option>
                                    <option value="2. Ada | < 3000">B. Ada | < 3000</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Silo GKG Hopper</label>
                                <select v-model="editFormData.silo_gkg_hopper" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 2000">A. Ada | > 2000</option>
                                    <option value="2. Tidak Ada">B. Tidak Ada</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Truk</label>
                                <select v-model="editFormData.truk" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | > 5">A. Ada | > 5</option>
                                    <option value="2. Ada | 1 s/d 5">B. Ada | 1 s/d 5</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mini Lab</label>
                                <select v-model="editFormData.mini_lab" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | Ruang Khusus">A. Ada | Ruang Khusus</option>
                                    <option value="2. Ada | Tidak Khusus">B. Ada | Tidak Khusus</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Moisture Tester</label>
                                <select v-model="editFormData.moisture_tester" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | Lengkap | Berfungsi">A. Ada | Lengkap | Berfungsi</option>
                                    <option value="2. Ada | Berfungsi">B. Ada | Berfungsi</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pembanding Derajat Sosoh</label>
                                <select v-model="editFormData.pembanding_derajat_sosoh" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | Sesuai Standar">A. Ada | Sesuai Standar</option>
                                    <option value="2. Ada | Tidak Sesuai">B. Ada | Tidak Sesuai</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bagian Quality Control</label>
                                <select v-model="editFormData.bagian_quality_control" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Pilih opsi</option>
                                    <option value="3. Ada | Tidak Merangkap">A. Ada | Tidak Merangkap</option>
                                    <option value="2. Ada | Merangkap">B. Ada | Merangkap</option>
                                    <option value="1. Tidak ada">C. Tidak ada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex gap-3 mt-6 pt-4 border-t">
                    <button
                        @click="closeEditModal"
                        :disabled="isEditing"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="updateKlasifikasiMitra"
                        :disabled="isEditing"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 disabled:opacity-50"
                    >
                        <span v-if="!isEditing">Simpan Perubahan</span>
                        <span v-else>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
        
    </SuperAdminLayout>
</template>