<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    mitras: Array,
    jenisKomoditasOptions: Array,
    komplekPergudanganOptions: Array
});

const form = useForm({
    nama_perusahaan: '',
    jenis_komoditas: '',
    jenis_komoditas_custom: '',
    jenis_pengadaan: '',
    tanggal_pembuatan: new Date().toISOString().split('T')[0],
    kualitas_items: [
        {
            id: 1,
            harga: '',
            kuantum: '',
            komplek_pergudangan: '',
            komplek_pergudangan_custom: '',
            kualitas: '',
            kualitas_custom: '',
            satuan: 'Kg'
        }
    ]
});

// Search functionality for Nama Perusahaan
const searchQuery = ref('');
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

const filteredMitras = computed(() => {
    if (!searchQuery.value) {
        return props.mitras;
    }
    return props.mitras.filter(mitra => 
        mitra.nama_perusahaan.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectMitra = (nama) => {
    form.nama_perusahaan = nama;
    searchQuery.value = nama;
    isDropdownOpen.value = false;
};

const handleSearchFocus = () => {
    isDropdownOpen.value = true;
};

const handleSearchBlur = () => {
    setTimeout(() => {
        isDropdownOpen.value = false;
    }, 200);
};

// Update search query when form value changes
watch(() => form.nama_perusahaan, (newValue) => {
    if (newValue && !searchQuery.value) {
        searchQuery.value = newValue;
    }
});

const kualitasOptions = ref([]);
const showJenisKomoditasCustom = computed(() => form.jenis_komoditas === 'Lain-lain');

// Helper function untuk menentukan satuan berdasarkan komoditas
const getSatuanByKomoditas = (jenisKomoditas, customKomoditas) => {
    const komoditas = jenisKomoditas === 'Lain-lain' ? customKomoditas : jenisKomoditas;
    const komoditasLower = (komoditas || '').toLowerCase();
    
    if (komoditasLower.includes('minyak')) {
        return 'Liter';
    }
    return 'Kg';
};

// Helper functions for managing quality items
const addKualitasItem = () => {
    const newId = Math.max(...form.kualitas_items.map(item => item.id)) + 1;
    // Auto-detect satuan berdasarkan jenis komoditas
    const defaultSatuan = getSatuanByKomoditas(form.jenis_komoditas, form.jenis_komoditas_custom);
    form.kualitas_items.push({
        id: newId,
        harga: '',
        kuantum: '',
        komplek_pergudangan: '',
        komplek_pergudangan_custom: '',
        kualitas: '',
        kualitas_custom: '',
        satuan: defaultSatuan
    });
};

const removeKualitasItem = (id) => {
    if (form.kualitas_items.length > 1) {
        form.kualitas_items = form.kualitas_items.filter(item => item.id !== id);
    }
};

const showKomplekPergudanganCustom = (item) => {
    return item.komplek_pergudangan === 'Custom';
};

const showKualitasCustom = (item) => {
    return item.kualitas === 'Lain-lain';
};

// Hitung nilai total secara real-time untuk semua items
const nilaiTotalKeseluruhan = computed(() => {
    const total = form.kualitas_items.reduce((sum, item) => {
        const harga = parseFloat(item.harga) || 0;
        const kuantum = parseFloat(item.kuantum) || 0;
        return sum + (harga * kuantum);
    }, 0);
    return total.toLocaleString('id-ID');
});

// Hitung nilai total per item
const getNilaiTotal = (item) => {
    const harga = parseFloat(item.harga) || 0;
    const kuantum = parseFloat(item.kuantum) || 0;
    return (harga * kuantum).toLocaleString('id-ID');
};

// Format angka dengan separator ribuan
const formatNumber = (value) => {
    if (!value) return '';
    // Hapus semua karakter non-digit
    const number = value.toString().replace(/\D/g, '');
    // Format dengan separator ribuan
    return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

// Handle input harga
const handleHargaInput = (event, item) => {
    const value = event.target.value.replace(/\./g, '');
    item.harga = value;
    // Update tampilan input dengan format
    event.target.value = formatNumber(value);
};

// Handle input kuantum
const handleKuantumInput = (event, item) => {
    const value = event.target.value.replace(/\./g, '');
    item.kuantum = value;
    // Update tampilan input dengan format
    event.target.value = formatNumber(value);
};

// Watch perubahan jenis komoditas untuk update kualitas dan satuan
watch(() => form.jenis_komoditas, (newValue) => {
    // Auto-detect satuan berdasarkan jenis komoditas
    const defaultSatuan = getSatuanByKomoditas(newValue, form.jenis_komoditas_custom);
    
    // Reset kualitas items dan update satuan saat jenis komoditas berubah
    form.kualitas_items.forEach(item => {
        item.kualitas = '';
        item.kualitas_custom = '';
        item.satuan = defaultSatuan;
    });
    
    // Update opsi kualitas berdasarkan jenis komoditas
    switch (newValue) {
        case 'GKP':
            kualitasOptions.value = ['Polos', 'Kemasan', 'Lain-lain'];
            break;
        case 'Beras':
            kualitasOptions.value = ['Medium', 'Premium', 'Khusus', 'Lain-lain'];
            break;
        case 'GKG':
            kualitasOptions.value = ['GKG', 'Lain-lain'];
            // Set default ke nama komoditas untuk item pertama
            if (form.kualitas_items.length > 0) {
                form.kualitas_items[0].kualitas = 'GKG';
            }
            break;
        case 'Jagung':
            kualitasOptions.value = ['Jagung', 'Lain-lain'];
            // Set default ke nama komoditas untuk item pertama
            if (form.kualitas_items.length > 0) {
                form.kualitas_items[0].kualitas = 'Jagung';
            }
            break;
        case 'Gula Pasir':
            kualitasOptions.value = ['Gula Pasir', 'Lain-lain'];
            // Set default ke nama komoditas untuk item pertama
            if (form.kualitas_items.length > 0) {
                form.kualitas_items[0].kualitas = 'Gula Pasir';
            }
            break;
        case 'Minyak Goreng':
            kualitasOptions.value = ['Minyak Goreng', 'Lain-lain'];
            // Set default ke nama komoditas untuk item pertama
            if (form.kualitas_items.length > 0) {
                form.kualitas_items[0].kualitas = 'Minyak Goreng';
            }
            break;
        case 'Lain-lain':
            kualitasOptions.value = ['Lain-lain'];
            // Set default ke nama komoditas untuk item pertama
            if (form.kualitas_items.length > 0) {
                form.kualitas_items[0].kualitas = 'Lain-lain';
            }
            break;
        default:
            kualitasOptions.value = ['Lain-lain'];
            // Set default ke nama komoditas untuk item pertama
            if (form.kualitas_items.length > 0) {
                form.kualitas_items[0].kualitas = 'Lain-lain';
            }
            break;
    }
});

const submit = () => {
    form.post(route('admin.purchase-orders.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Buat Purchase Order - ASIMPENAS" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-4">
                            <button 
                                @click="$inertia.visit(route('admin.purchase-orders.index'))"
                                class="text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Buat Purchase Order Baru</h1>
                                <p class="text-gray-600 mt-1">Isi form di bawah untuk membuat surat permohonan order pembelian</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Nama Perusahaan -->
                        <div>
                            <InputLabel for="nama_perusahaan" value="Nama Perusahaan" />
                            <div class="relative mt-1">
                                <div class="relative">
                                    <input
                                        type="text"
                                        v-model="searchQuery"
                                        @focus="handleSearchFocus"
                                        @blur="handleSearchBlur"
                                        @input="isDropdownOpen = true"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Cari atau pilih nama perusahaan..."
                                        required
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Dropdown List -->
                                <div 
                                    v-if="isDropdownOpen && filteredMitras.length > 0"
                                    class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                >
                                    <div
                                        v-for="mitra in filteredMitras"
                                        :key="mitra.id"
                                        @mousedown="selectMitra(mitra.nama_perusahaan)"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 hover:text-indigo-900"
                                        :class="{ 'bg-indigo-100': form.nama_perusahaan === mitra.nama_perusahaan }"
                                    >
                                        <span class="block truncate" :class="{ 'font-semibold': form.nama_perusahaan === mitra.nama_perusahaan }">
                                            {{ mitra.nama_perusahaan }}
                                        </span>
                                        <span v-if="form.nama_perusahaan === mitra.nama_perusahaan" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- No Results -->
                                <div 
                                    v-if="isDropdownOpen && filteredMitras.length === 0 && searchQuery"
                                    class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-2 px-3 text-base ring-1 ring-black ring-opacity-5 sm:text-sm"
                                >
                                    <p class="text-gray-500 text-center">Tidak ada hasil ditemukan</p>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.nama_perusahaan" />
                        </div>

                        <!-- Jenis Komoditas -->
                        <div>
                            <InputLabel for="jenis_komoditas" value="Jenis Komoditas" />
                            <select
                                id="jenis_komoditas"
                                v-model="form.jenis_komoditas"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="">Pilih jenis komoditas</option>
                                <option v-for="option in jenisKomoditasOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.jenis_komoditas" />
                            
                            <!-- Custom input untuk "Lain-lain" -->
                            <div v-if="showJenisKomoditasCustom" class="mt-3">
                                <InputLabel for="jenis_komoditas_custom" value="Spesifikasi Komoditas" />
                                <TextInput
                                    id="jenis_komoditas_custom"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.jenis_komoditas_custom"
                                    placeholder="Masukkan jenis komoditas lainnya"
                                />
                                <InputError class="mt-2" :message="form.errors.jenis_komoditas_custom" />
                            </div>
                        </div>

                        <!-- Jenis Pengadaan -->
                        <div>
                            <InputLabel for="jenis_pengadaan" value="Jenis Pengadaan" />
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.jenis_pengadaan"
                                        value="PSO"
                                        class="border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">PSO</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.jenis_pengadaan"
                                        value="Komersial"
                                        class="border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Komersial</span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.jenis_pengadaan" />
                        </div>

                        <!-- Tanggal Pembuatan -->
                        <div>
                            <InputLabel for="tanggal_pembuatan" value="Tanggal Pembuatan" />
                            <TextInput
                                id="tanggal_pembuatan"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.tanggal_pembuatan"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.tanggal_pembuatan" />
                        </div>

                        <!-- Quality Items Section -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Detail Kualitas Barang</h3>
                                <button
                                    type="button"
                                    @click="addKualitasItem"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center space-x-2"
                                    :disabled="!form.jenis_komoditas"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Tambah Kualitas</span>
                                </button>
                            </div>
                            
                            <div class="space-y-6">
                                <div v-for="(item, index) in form.kualitas_items" :key="item.id" class="bg-gray-50 p-4 rounded-lg border">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-medium text-gray-800">Kualitas {{ index + 1 }}</h4>
                                        <button
                                            v-if="form.kualitas_items.length > 1"
                                            type="button"
                                            @click="removeKualitasItem(item.id)"
                                            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors duration-200"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                    
                                    <!-- Kualitas -->
                                    <div class="mb-4">
                                        <InputLabel :for="`kualitas_${item.id}`" value="Kualitas" />
                                        <select
                                            :id="`kualitas_${item.id}`"
                                            v-model="item.kualitas"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                            :disabled="!form.jenis_komoditas"
                                        >
                                            <option value="">{{ form.jenis_komoditas ? 'Pilih kualitas' : 'Pilih jenis komoditas terlebih dahulu' }}</option>
                                            <option v-for="option in kualitasOptions" :key="option" :value="option">
                                                {{ option }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.kualitas`]" />
                                        
                                        <!-- Custom input untuk "Lain-lain" -->
                                        <div v-if="showKualitasCustom(item)" class="mt-3">
                                            <InputLabel :for="`kualitas_custom_${item.id}`" value="Spesifikasi Kualitas" />
                                            <TextInput
                                                :id="`kualitas_custom_${item.id}`"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="item.kualitas_custom"
                                                placeholder="Masukkan spesifikasi kualitas lainnya"
                                            />
                                            <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.kualitas_custom`]" />
                                        </div>
                                    </div>

                                    <!-- Harga, Kuantum, dan Satuan -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <InputLabel :for="`harga_${item.id}`" :value="`Harga (Rp/${item.satuan})`" />
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                                </div>
                                                <input
                                                    :id="`harga_${item.id}`"
                                                    type="text"
                                                    @input="(event) => handleHargaInput(event, item)"
                                                    class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="0"
                                                    required
                                                />
                                            </div>
                                            <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.harga`]" />
                                        </div>

                                        <div>
                                            <InputLabel :for="`kuantum_${item.id}`" :value="`Kuantum (${item.satuan})`" />
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input
                                                    :id="`kuantum_${item.id}`"
                                                    type="text"
                                                    @input="(event) => handleKuantumInput(event, item)"
                                                    class="block w-full pr-16 py-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="0"
                                                    required
                                                />
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">{{ item.satuan }}</span>
                                                </div>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.kuantum`]" />
                                        </div>

                                        <div>
                                            <InputLabel :for="`satuan_${item.id}`" value="Satuan" />
                                            <select
                                                :id="`satuan_${item.id}`"
                                                v-model="item.satuan"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required
                                            >
                                                <option value="Kg">Kg</option>
                                                <option value="Liter">Liter</option>
                                                <option value="Ton">Ton</option>
                                                <option value="Kwintal">Kwintal</option>
                                                <option value="Pcs">Pcs</option>
                                                <option value="Karton">Karton</option>
                                                <option value="Box">Box</option>
                                            </select>
                                            <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.satuan`]" />
                                        </div>
                                    </div>

                                    <!-- Nilai Total per item -->
                                    <div class="mb-4">
                                        <InputLabel value="Nilai Total Item" />
                                        <div class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-900 font-medium">
                                            Rp {{ getNilaiTotal(item) }}
                                        </div>
                                    </div>

                                    <!-- Komplek Pergudangan -->
                                    <div>
                                        <InputLabel :for="`komplek_pergudangan_${item.id}`" value="Komplek Pergudangan" />
                                        <select
                                            :id="`komplek_pergudangan_${item.id}`"
                                            v-model="item.komplek_pergudangan"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="">Pilih komplek pergudangan</option>
                                            <option v-for="option in komplekPergudanganOptions" :key="option" :value="option">
                                                {{ option === 'Custom' ? 'Lainnya (Input Manual)' : 'Komplek ' + option }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.komplek_pergudangan`]" />
                                        
                                        <!-- Custom input untuk "Custom" -->
                                        <div v-if="showKomplekPergudanganCustom(item)" class="mt-3">
                                            <InputLabel :for="`komplek_pergudangan_custom_${item.id}`" value="Nama Komplek Pergudangan" />
                                            <TextInput
                                                :id="`komplek_pergudangan_custom_${item.id}`"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="item.komplek_pergudangan_custom"
                                                placeholder="Masukkan nama komplek pergudangan"
                                            />
                                            <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.komplek_pergudangan_custom`]" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Keseluruhan -->
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-medium text-blue-900">Nilai Total Keseluruhan:</span>
                                    <span class="text-xl font-bold text-blue-900">Rp {{ nilaiTotalKeseluruhan }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                            <button
                                type="button"
                                @click="$inertia.visit(route('admin.purchase-orders.index'))"
                                class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Batal
                            </button>
                            <PrimaryButton 
                                :class="{ 'opacity-25': form.processing }" 
                                :disabled="form.processing"
                                class="flex items-center space-x-2"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Menyimpan...' : 'Buat Purchase Order' }}</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>