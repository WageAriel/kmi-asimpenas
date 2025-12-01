<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    documents: {
        type: Array,
        default: () => []
    }
});

// State
const showModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'
const selectedDocument = ref(null);
const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Delete Modal State
const showDeleteModal = ref(false);
const selectedItemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref('');

// Form data
const form = ref({
    jenis_hpp: 'Gabah dan Beras',
    nomor_keputusan: '',
    tahun: new Date().getFullYear(),
    tentang: '',
    is_active: true,
    komoditas: [
        {
            nama_komoditas: '',
            tempat: '',
            harga_per_kg: '',
            ketentuan: '',
            urutan: 0
        }
    ]
});

// Computed
const sortedDocuments = computed(() => {
    return [...props.documents].sort((a, b) => {
        // Sort by tahun desc, then created_at desc
        if (b.tahun !== a.tahun) return b.tahun - a.tahun;
        return new Date(b.created_at) - new Date(a.created_at);
    });
});

// Methods
const openCreateModal = () => {
    modalMode.value = 'create';
    resetForm();
    showModal.value = true;
    errorMessage.value = '';
    successMessage.value = '';
};

const openEditModal = (document) => {
    modalMode.value = 'edit';
    selectedDocument.value = document;
    
    form.value = {
        jenis_hpp: document.jenis_hpp,
        nomor_keputusan: document.nomor_keputusan,
        tahun: document.tahun,
        tentang: document.tentang,
        is_active: document.is_active,
        komoditas: document.komoditas.map(k => ({
            id: k.id,
            nama_komoditas: k.nama_komoditas,
            tempat: k.tempat,
            harga_per_kg: k.harga_per_kg,
            ketentuan: k.ketentuan || '',
            urutan: k.urutan
        }))
    };
    
    showModal.value = true;
    errorMessage.value = '';
    successMessage.value = '';
};

const closeModal = () => {
    showModal.value = false;
    selectedDocument.value = null;
    errorMessage.value = '';
    // Don't reset successMessage here so it can be shown after modal closes
};

const resetForm = () => {
    form.value = {
        jenis_hpp: 'Gabah dan Beras',
        nomor_keputusan: '',
        tahun: new Date().getFullYear(),
        tentang: '',
        is_active: true,
        komoditas: [
            {
                nama_komoditas: '',
                tempat: '',
                harga_per_kg: '',
                ketentuan: '',
                urutan: 0
            }
        ]
    };
};

const addKomoditas = () => {
    form.value.komoditas.push({
        nama_komoditas: '',
        tempat: '',
        harga_per_kg: '',
        ketentuan: '',
        urutan: form.value.komoditas.length
    });
};

const removeKomoditas = (index) => {
    if (form.value.komoditas.length > 1) {
        form.value.komoditas.splice(index, 1);
        // Update urutan
        form.value.komoditas.forEach((k, i) => {
            k.urutan = i;
        });
    }
};

const submitForm = async () => {
    if (isSubmitting.value) return;
    
    // Validation
    if (!form.value.nomor_keputusan) {
        errorMessage.value = 'Nomor Keputusan harus diisi';
        return;
    }
    
    if (!form.value.tentang) {
        errorMessage.value = 'Tentang harus diisi';
        return;
    }
    
    const hasEmptyKomoditas = form.value.komoditas.some(k => 
        !k.nama_komoditas || !k.tempat || !k.harga_per_kg
    );
    
    if (hasEmptyKomoditas) {
        errorMessage.value = 'Semua field komoditas harus diisi';
        return;
    }
    
    isSubmitting.value = true;
    errorMessage.value = '';
    
    try {
        if (modalMode.value === 'create') {
            await axios.post('/admin/hpp', form.value);
            successMessage.value = 'Data HPP berhasil ditambahkan';
            console.log('Success message set:', successMessage.value);
        } else {
            await axios.put(`/admin/hpp/${selectedDocument.value.id}`, form.value);
            successMessage.value = 'Data HPP berhasil diperbarui';
            console.log('Success message set:', successMessage.value);
        }
        
        // Close modal after showing success message
        setTimeout(() => {
            closeModal();
        }, 500);
        
        // Auto reload after 2 seconds
        setTimeout(() => {
            window.location.reload();
        }, 2000);
        
    } catch (error) {
        console.error('Error submitting form:', error);
        errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan data';
        
        // Auto dismiss error after 5 seconds
        setTimeout(() => {
            errorMessage.value = '';
        }, 5000);
    } finally {
        isSubmitting.value = false;
    }
};

const openDeleteModal = (doc) => {
    selectedItemToDelete.value = doc;
    deleteError.value = '';
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedItemToDelete.value = null;
    deleteError.value = '';
};

const confirmDelete = async () => {
    if (!selectedItemToDelete.value) return;
    
    isDeleting.value = true;
    deleteError.value = '';
    
    try {
        await axios.delete(`/admin/hpp/${selectedItemToDelete.value.id}`);
        successMessage.value = 'Data HPP berhasil dihapus';
        closeDeleteModal();
        
        // Auto dismiss after 2 seconds
        setTimeout(() => {
            successMessage.value = '';
            window.location.reload();
        }, 2000);
        
    } catch (error) {
        console.error('Error deleting document:', error);
        deleteError.value = error.response?.data?.message || 'Terjadi kesalahan saat menghapus data';
    } finally {
        isDeleting.value = false;
    }
};

const toggleActive = async (id) => {
    try {
        const response = await axios.patch(`/admin/hpp/${id}/toggle-active`);
        successMessage.value = response.data.message;
        
        // Auto dismiss after 2 seconds
        setTimeout(() => {
            successMessage.value = '';
            window.location.reload();
        }, 2000);
        
    } catch (error) {
        console.error('Error toggling status:', error);
        errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat mengubah status';
        
        // Auto dismiss error after 5 seconds
        setTimeout(() => {
            errorMessage.value = '';
        }, 5000);
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <Head title="Manajemen HPP - ASIMPENAS" />

    <AdminLayout>
        <!-- Fixed Alert Notifications at Top -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform -translate-y-2"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform translate-y-0"
            leave-to-class="opacity-0 transform -translate-y-2"
        >
            <div v-if="successMessage" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md">
                <div class="bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-medium">{{ successMessage }}</p>
                        </div>
                    </div>
                    <button @click="successMessage = ''" class="text-green-700 hover:text-green-900 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>

        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform -translate-y-2"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform translate-y-0"
            leave-to-class="opacity-0 transform -translate-y-2"
        >
            <div v-if="errorMessage" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md">
                <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-medium">{{ errorMessage }}</p>
                        </div>
                    </div>
                    <button @click="errorMessage = ''" class="text-red-700 hover:text-red-900 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
        
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Manajemen HPP</h2>
                <p class="text-gray-600">Kelola informasi Harga Pembelian Pemerintah (HPP) Gabah, Beras, dan Jagung</p>
            </div>

            <!-- Action Button -->
            <div class="mb-6">
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium shadow-sm"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Data HPP
                </button>
            </div>

            <!-- Documents List -->
            <div class="space-y-6">
                <div v-for="doc in sortedDocuments" :key="doc.id" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ doc.jenis_hpp }}</h3>
                                <span 
                                    :class="doc.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                >
                                    {{ doc.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Tahun {{ doc.tahun }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="toggleActive(doc.id)"
                                class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors duration-200"
                                :class="doc.is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200'"
                            >
                                {{ doc.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                            <button
                                @click="openEditModal(doc)"
                                class="px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors duration-200"
                            >
                                Edit
                            </button>
                            <button
                                @click="openDeleteModal(doc)"
                                class="px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors duration-200"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Nomor Keputusan:</h4>
                            <p class="text-sm text-gray-900">Nomor {{ doc.nomor_keputusan }} Tahun {{ doc.tahun }}</p>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Tentang:</h4>
                            <p class="text-sm text-gray-600">{{ doc.tentang }}</p>
                        </div>

                        <!-- Komoditas Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga/kg</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ketentuan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="komoditas in doc.komoditas" :key="komoditas.id">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ komoditas.nama_komoditas }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ komoditas.tempat }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 font-semibold">{{ formatCurrency(komoditas.harga_per_kg) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ komoditas.ketentuan || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div v-if="sortedDocuments.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2 text-gray-600">Belum ada data HPP</p>
                    <button
                        @click="openCreateModal"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium text-sm"
                    >
                        Tambah Data Pertama
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <div v-if="showModal"  class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 overflow-y-auto py-8">
            <div  class="bg-white rounded-xl shadow-lg max-w-4xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center z-10">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ modalMode === 'create' ? 'Tambah Data HPP' : 'Edit Data HPP' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-6">
                    <!-- Jenis HPP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis HPP *</label>
                        <select v-model="form.jenis_hpp" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="Gabah dan Beras">Gabah dan Beras</option>
                            <option value="Jagung">Jagung</option>
                        </select>
                    </div>

                    <!-- Nomor Keputusan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Keputusan *</label>
                            <input
                                v-model="form.nomor_keputusan"
                                type="text"
                                placeholder="Contoh: 14"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                            />
                            <p class="text-xs text-gray-500 mt-1">Format: Nomor {{ form.nomor_keputusan || '...' }} Tahun {{ form.tahun }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun *</label>
                            <input
                                v-model.number="form.tahun"
                                type="number"
                                min="2000"
                                max="2100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                            />
                        </div>
                    </div>

                    <!-- Tentang -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tentang *</label>
                        <textarea
                            v-model="form.tentang"
                            rows="3"
                            placeholder="Isi tentang keputusan ini..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                        ></textarea>
                    </div>

                    <!-- Status Aktif -->
                    <div class="flex items-center">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            id="is_active"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                        />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif (ditampilkan di landing page)</label>
                    </div>

                    <!-- Komoditas Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Daftar Komoditas</h4>
                            <button
                                @click="addKomoditas"
                                type="button"
                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Komoditas
                            </button>
                        </div>

                        <div v-for="(komoditas, index) in form.komoditas" :key="index" class="mb-6 p-4 bg-gray-50 rounded-lg relative">
                            <button
                                v-if="form.komoditas.length > 1"
                                @click="removeKomoditas(index)"
                                type="button"
                                class="absolute top-2 right-2 p-1 text-red-600 hover:text-red-800 hover:bg-red-100 rounded"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <h5 class="text-sm font-semibold text-gray-700 mb-3">Komoditas #{{ index + 1 }}</h5>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Komoditas *</label>
                                    <input
                                        v-model="komoditas.nama_komoditas"
                                        type="text"
                                        placeholder="GKP, Beras, Jagung Pipil Kering"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 bg-white"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempat *</label>
                                    <input
                                        v-model="komoditas.tempat"
                                        type="text"
                                        placeholder="Tingkat Petani, Gudang Perum BULOG"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 bg-white"
                                    />
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga per Kg *</label>
                                <input
                                    v-model.number="komoditas.harga_per_kg"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="6500"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 bg-white"
                                />
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ketentuan</label>
                                <textarea
                                    v-model="komoditas.ketentuan"
                                    rows="2"
                                    placeholder="Derajat Sosoh Min 95%, Kadar Air maks 14%, dll"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 bg-white"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button
                        @click="closeModal"
                        type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitForm"
                        :disabled="isSubmitting"
                        type="button"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                    >
                        <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" @click="closeDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative mx-4">
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
                    Apakah Anda yakin ingin menghapus data HPP 
                    <strong class="text-gray-900">{{ selectedItemToDelete?.jenis_hpp }}</strong>
                    tahun <strong class="text-gray-900">{{ selectedItemToDelete?.tahun }}</strong>?
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
