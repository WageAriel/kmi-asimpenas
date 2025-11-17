<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, reactive, onMounted } from 'vue';
import { router } from '@inertiajs/vue3'


const isSubmitting = ref(false);
const errors = ref({});

const form = reactive({
    // Informasi Umum
    // nama_mitra: '',
    // badan_hukum: '',
    // alamat_perusahaan: '',
    // status: '',
    
    // Fasilitas & Mesin Produksi (Step 2)
    id_mitra: '3',
    mesin_pembersih_gabah: '3. Ada | > 20',
    lantai_jemur: '3. Ada | > 10',
    mesin_pengering: '3. Ada | > 20',
    alat_pengering_lainnya: '3. Tidak Disyaratkan',
    
    // Mesin Penggilingan (Step 3)
    mesin_pembersih_awal: '3. Ada | > 3',
    mesin_pemecah_kulit: '3. Ada | > 3',
    mesin_pembersih_sekam: '3. Ada | > 3',
    mesin_pemisah_gabah_pecah_kulit: '3. Ada | > 3',
    mesin_pemisah_batu: '3. Ada | > 3',
    mesin_penyosoh: '3. Ada | > 3 | 2',
    mesin_pengkabut: '3. Ada | > 3 | 2',
    mesin_pemesah_menir: '3. Ada | > 3',
    mesin_pemisah_katul: '3. Ada | > 3',
    mesin_pemisah_berdasarkan_ukuran: '3. Ada | > 3',
    mesin_pemisah_berdasarkan_warna: '3. Ada | > 3',
    tangki_penyimpanan: '3. Ada | > 10',
    mesin_pengemas: '3. Ada | Full Otomatis',
    mesin_jahit: '3. Ada | Full Otomatis',
    
    // Penyimpanan (Step 4)
    gudang_konvensional: '3. Ada | > 3000',
    silo_gkg_hopper: '3. Ada | > 2000',

    // Transportasi (Step 5)
    truk: '3. Ada | > 5',

    // Kelengkapan Pemeriksaan (Step 6)
    mini_lab: '3. Ada | Ruang Khusus',
    moisture_tester: '3. Ada | Lengkap | Berfungsi',
    pembanding_derajat_sosoh: '3. Ada | Sesuai Standar',

    // Organisasi (Step 7)
    bagian_quality_control: '3. Ada | Tidak Merangkap',
});

const notification = ref({
    show: false,
    type: 'error', // 'success', 'error', 'warning', 'info'
    title: '',
    message: '',
    showButton: false,
    buttonText: '',
    buttonAction: null
});

// Function to show notification
const showNotification = (type, title, message, buttonConfig = null) => {
    notification.value = {
        show: true,
        type,
        title,
        message,
        showButton: buttonConfig ? true : false,
        buttonText: buttonConfig?.text || '',
        buttonAction: buttonConfig?.action || null
    };
};

// Function to hide notification
const hideNotification = () => {
    notification.value.show = false;
};

// Function to handle button click in notification
const handleNotificationButton = () => {
    if (notification.value.buttonAction) {
        notification.value.buttonAction();
    }
    hideNotification();
};

// Get notification classes based on type
const getNotificationClasses = (type) => {
    switch(type) {
        case 'success':
            return 'bg-green-50';
        case 'error':
            return 'bg-red-50';
        case 'warning':
            return 'bg-yellow-50';
        case 'info':
            return 'bg-blue-50';
        default:
            return 'bg-gray-50';
    }
};

// Get notification icon based on type
const getNotificationIcon = (type) => {
    switch(type) {
        case 'success':
            return 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z';
        case 'error':
            return 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z';
        case 'warning':
            return 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z';
        case 'info':
            return 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z';
        default:
            return 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z';
    }
};

// Get icon color based on type
const getIconColor = (type) => {
    switch(type) {
        case 'success': return 'text-green-400';
        case 'error': return 'text-red-400';
        case 'warning': return 'text-yellow-400';
        case 'info': return 'text-blue-400';
        default: return 'text-gray-400';
    }
};

// Get text colors based on type
const getTextColors = (type) => {
    switch(type) {
        case 'success': return { title: 'text-green-800', message: 'text-green-700' };
        case 'error': return { title: 'text-red-800', message: 'text-red-700' };
        case 'warning': return { title: 'text-yellow-800', message: 'text-yellow-700' };
        case 'info': return { title: 'text-blue-800', message: 'text-blue-700' };
        default: return { title: 'text-gray-800', message: 'text-gray-700' };
    }
};

// Get button colors based on type
const getButtonColors = (type) => {
    switch(type) {
        case 'success': return 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
        case 'error': return 'bg-red-600 hover:bg-red-700 focus:ring-red-500';
        case 'warning': return 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500';
        case 'info': return 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
        default: return 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500';
    }
};

// Get close button colors based on type
const getCloseButtonColors = (type) => {
    switch(type) {
        case 'success': return 'text-green-500 hover:bg-green-100 focus:ring-green-600';
        case 'error': return 'text-red-500 hover:bg-red-100 focus:ring-red-600';
        case 'warning': return 'text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-600';
        case 'info': return 'text-blue-500 hover:bg-blue-100 focus:ring-blue-600';
        default: return 'text-gray-500 hover:bg-gray-100 focus:ring-gray-600';
    }
};

const jenisUsahaOptions = [
    { value: 'pt', label: 'PT (Perseroan Terbatas)' },
    { value: 'cv', label: 'CV (Commanditaire Vennootschap)' },
    { value: 'firma', label: 'Firma' },
    { value: 'ud', label: 'UD (Usaha Dagang)' },
    { value: 'koperasi', label: 'Koperasi' },
    { value: 'perorangan', label: 'Perorangan' },
    { value: 'yayasan', label: 'Yayasan' },
    { value: 'perkumpulan', label: 'Perkumpulan' }
];

const skalaUsahaOptions = [
    { value: 'mikro', label: 'Mikro (< 50 ton/bulan)' },
    { value: 'kecil', label: 'Kecil (50-200 ton/bulan)' },
    { value: 'menengah', label: 'Menengah (200-1000 ton/bulan)' },
    { value: 'besar', label: 'Besar (> 1000 ton/bulan)' }
];

const targetPasarOptions = [
    { value: 'lokal', label: 'Pasar Lokal' },
    { value: 'regional', label: 'Pasar Regional' },
    { value: 'nasional', label: 'Pasar Nasional' },
    { value: 'internasional', label: 'Pasar Internasional' }
];

const currentStep = ref(1);
const totalSteps = 6;
const currentYear = new Date().getFullYear();

// Navigation functions
const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const isEditMode = ref(false);
const editId = ref(null);

onMounted(async () => {
    // Ambil id edit dari query string
    const params = new URLSearchParams(window.location.search);
    if (params.has('edit')) {
        editId.value = params.get('edit');
        isEditMode.value = true;
        // Fetch data yang ingin diedit
        try {
            const res = await axios.get(`/klasifikasi-mitra/${editId.value}`); // pastikan ada route GET buat detail data
            Object.assign(form, res.data); // autoset ke state form, pastikan field sama
        } catch (e) {
            alert('Gagal mengambil data untuk edit!');
        }
    }

    // (opsional) ambil data mitra juga jika tidak edit (untuk mode tambah)
});

onMounted(async () => {
    try {
        // Panggil API untuk mendapatkan data mitra user login
        const response = await axios.get('/data-mitra/my');
        const mitra = response.data;
        
        if (mitra && mitra.id_mitra) {
            form.id_mitra = mitra.id_mitra;
            showNotification('success', 'Berhasil', 'Data mitra berhasil dimuat');
        } else {
            // Case 1: Data mitra tidak ditemukan (response sukses tapi data kosong)
            showNotification(
                'warning', 
                'Data Mitra Tidak Ditemukan', 
                'Anda belum terdaftar sebagai mitra. Silakan lengkapi data mitra terlebih dahulu.',
                {
                    text: 'Lengkapi Data',
                    action: () => {
                        window.location.href = route('input-data-mitra');
                    }
                }
            );
        } 

    } catch (error) {
        const errorMessage = error.response?.data?.message || error.message;
        
        // Case 2: Cek jenis error berdasarkan message atau status
        if (errorMessage.toLowerCase().includes('data mitra tidak ditemukan')) {
            window.location.href = route('input-data-mitra');
            
        } else if (error.response?.status === 401 || error.response?.status === 403 || errorMessage.toLowerCase().includes('unauthenticated') || errorMessage.toLowerCase().includes('unauthorized')) {
            // Error: Authentication/Authorization (perlu login ulang)
            showNotification(
                'error', 
                'Login Terlebih Dahulu', 
                'Silakan melakukan login untuk mengakses halaman ini dan melanjutkan proses pengajuan.',
                {
                    text: 'Login Sekarang',
                    action: () => {
                        window.location.href = route('login');
                    }
                }
            );
        } else if (error.response?.status === 500 || errorMessage.toLowerCase().includes('server error')) {
            // Error: Server Error
            showNotification(
                'error', 
                'Kesalahan Server', 
                'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.',
                {
                    text: 'Coba Lagi',
                    action: () => {
                        window.location.reload();
                    }
                }
            );
        } else if (error.code === 'NETWORK_ERROR' || !navigator.onLine) {
            // Error: Network/Connection
            showNotification(
                'error', 
                'Koneksi Bermasalah', 
                'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
                {
                    text: 'Coba Lagi',
                    action: () => {
                        window.location.reload();
                    }
                }
            );
        } else {
            // Error: General/Unknown (fallback ke login)
            showNotification(
                'error', 
                'Terjadi Kesalahan', 
                `Gagal mengambil data mitra: ${errorMessage}. Silakan login ulang.`,
                {
                    text: 'Login Ulang',
                    action: () => {
                        window.location.href = route('login');
                    }
                }
            );
        }
    }
});

// Submit function

const prepareFormData = () => ({
    ...form,
    mesin_pembersih_gabah: form.mesin_pembersih_gabah || null,
    lantai_jemur: form.lantai_jemur || null,
    mesin_pengering: form.mesin_pengering || null,
    alat_pengering_lainnya: form.alat_pengering_lainnya || null,
    mesin_pembersih_awal: form.mesin_pembersih_awal || null,
    mesin_pemecah_kulit: form.mesin_pemecah_kulit || null,
    mesin_pembersih_sekam: form.mesin_pembersih_sekam || null,
    mesin_pemisah_gabah_pecah_kulit: form.mesin_pemisah_gabah_pecah_kulit || null,
    mesin_pemisah_batu: form.mesin_pemisah_batu || null,
    mesin_penyosoh: form.mesin_penyosoh || null,
    mesin_pengkabut: form.mesin_pengkabut || null,
    mesin_pemesah_menir: form.mesin_pemesah_menir || null,
    mesin_pemisah_katul: form.mesin_pemisah_katul || null,
    mesin_pemisah_berdasarkan_ukuran: form.mesin_pemisah_berdasarkan_ukuran || null,
    mesin_pemisah_berdasarkan_warna: form.mesin_pemisah_berdasarkan_warna || null,
    tangki_penyimpanan: form.tangki_penyimpanan || null,
    mesin_pengemas: form.mesin_pengemas || null,
    mesin_jahit: form.mesin_jahit || null,
    gudang_konvensional: form.gudang_konvensional || null,
    silo_gkg_hopper: form.silo_gkg_hopper || null,
    truk: form.truk || null,
    mini_lab: form.mini_lab || null,
    moisture_tester: form.moisture_tester || null,
    pembanding_derajat_sosoh: form.pembanding_derajat_sosoh || null,
    bagian_quality_control: form.bagian_quality_control || null,

});


const submit = async () => {
    isSubmitting.value = true;
    errors.value = {};
    try {
        const dataToSend = prepareFormData();
        let response;
        if (isEditMode.value && editId.value) {
            response = await axios.put(`/klasifikasi-mitra/${editId.value}`, dataToSend);
        } else {
            response = await axios.post('/klasifikasi-mitra', dataToSend);
        }

        showNotification(
            'success',
            'Data Berhasil Disimpan',
            `Data berhasil ${isEditMode.value ? 'diupdate' : 'ditambahkan'}`,
            {
                text: 'Ok',
                action: () => {
                    window.location.href = '/mitra/klasifikasi-mitra';
                }
            }
        );
        // Redirect akan terjadi saat tombol Ok ditekan
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {};
            console.error('Error validasi server:', errors.value);
            showNotification('error', 'Validasi Gagal', 'Periksa kembali data yang anda isi.');
        } else {
            showNotification('error', 'Error', error.message || 'Terjadi kesalahan');
        }
    } finally {
        isSubmitting.value = false;
    }
};

</script>

<template>
    <Head title="Klasifikasi Mitra - ASIMPENAS" />

    <MitraLayout>

        <!-- Notification Modal -->
        <div 
            v-if="notification.show"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <!-- Notification Modal -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div :class="[
                    'relative w-full max-w-md transform rounded-xl border-2 p-3 shadow-2xl bg-white',
                    getNotificationClasses(notification.type)
                ]">
                    <!-- Close Button (hanya untuk error) -->
                    <button 
                        v-if="notification.type === 'error'"
                        @click="hideNotification"
                        :class="[
                            'absolute right-4 top-4 inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2',
                            getCloseButtonColors(notification.type)
                        ]"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <!-- Icon -->
                    <div class="flex items-center justify-center mx-auto w-12 h-12 mb-4">
                        <svg 
                            :class="[
                                'h-8 w-8',
                                getIconColor(notification.type)
                            ]" 
                            fill="currentColor" 
                            viewBox="0 0 20 20"
                        >
                            <path 
                                fill-rule="evenodd" 
                                :d="getNotificationIcon(notification.type)" 
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="text-center">
                        <h3 :class="[
                            'text-base font-semibold mb-2',
                            getTextColors(notification.type).title
                        ]">
                            {{ notification.title }}
                        </h3>
                        <p :class="[
                            'text-xs mb-6',
                            getTextColors(notification.type).message
                        ]">
                            {{ notification.message }}
                        </p>

                        <!-- Single Action Button -->
                        <div class="flex justify-center">
                            <!-- Primary Action Button (jika ada) -->
                            <button
                                v-if="notification.showButton"
                                @click="handleNotificationButton"
                                :class="[
                                    'px-6 py-2 text-sm font-medium text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
                                    getButtonColors(notification.type)
                                ]"
                            >
                                {{ notification.buttonText }}
                            </button>

                            <!-- Default OK Button (jika tidak ada action button) -->
                            <button
                                v-else
                                @click="hideNotification"
                                class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                            >
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Klasifikasi Mitra</h2>
                <p class="text-gray-600">Formulir klasifikasi untuk menentukan kategori dan tingkat kemitraan</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div v-for="step in totalSteps" :key="step" class="flex items-center">
                        <div :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                            step <= currentStep ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600'
                        ]">
                            {{ step }}
                        </div>
                        <div v-if="step < totalSteps" :class="[
                            'w-20 h-1 mx-2',
                            step < currentStep ? 'bg-green-600' : 'bg-gray-200'
                        ]"></div>
                    </div>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-500">Pengeringan</span>
                    <span class="text-xs text-gray-500">Penggilingan</span>
                    <span class="text-xs text-gray-500">Sarana Penyimpanan</span>
                    <span class="text-xs text-gray-500">Sarana Angkutan</span>
                    <span class="text-xs text-gray-500">Kelengkapan Pemeriksaan</span>
                    <span class="text-xs text-gray-500">Organisasi</span>
                </div>
            </div>

            <!-- Form Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form @submit.prevent="submit">
                    <!-- Step 1: Informasi Umum -->
                    <!-- <div v-show="currentStep === 1" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum Mitra</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="nama_mitra" value="Nama Mitra/Perusahaan *" />
                                <TextInput
                                    id="nama_mitra"
                                    v-model="form.nama_mitra"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.nama_mitra" />
                            </div>

                            <div>
                                <InputLabel for="badan_hukum" value="Badan Hukum/Usaha *" />
                                <select
                                    id="badan_hukum"
                                    v-model="form.badan_hukum"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Pilih Badan Hukum/Usaha</option>
                                    <option v-for="option in jenisUsahaOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.badan_hukum" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="alamat_perusahaan" value="Alamat Perusahaan *" />
                                <TextInput
                                    id="alamat_perusahaan"
                                    v-model="form.alamat_perusahaan"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Masukkan alamat lengkap perusahaan"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.alamat_perusahaan" />
                            </div>

                            <div>
                                <InputLabel for="status" value="Status *" />
                                <TextInput
                                    id="status"
                                    v-model="form.status"
                                    type="number"
                                    min="1900"
                                    :max="currentYear"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>
                        </div>

                    </div> -->

                    <!-- Step 2: Kapasitas Produksi -->
                    <div v-show="currentStep === 1" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengeringan</h3>
                        
                        <div class="space-y-6">
                            <!-- Mesin Pembersih Gabah -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pembersih Gabah (ton/hari)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_gabah"
                                            value="3. Ada | > 20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 20 </span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_gabah"
                                            value="2. Ada | ≤ 20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | ≤ 20 unit</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_gabah"
                                            value="1. Tidak Ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pembersih_gabah" /> -->
                            </div>

                            <!-- Lantai Jemur -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Lantai Jemur (ton/hari)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="3. Ada | > 10"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 10</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="2. Ada | 1 s/d 10"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 10</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.lantai_jemur" /> -->
                            </div>

                            <!-- Mesin Pengering -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pengering (ton/hari)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengering"
                                            value="3. Ada | > 20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 20</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengering"
                                            value="2. Ada | ≤ 20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | ≤ 20</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengering"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pengering" /> -->
                            </div>

                            <!-- Alat Pengering Lainnya -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Alat Pengering Lainnya (ton/hari)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.alat_pengering_lainnya"
                                            value="3. Tidak Disyaratkan"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Tidak Disyaratkan</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.alat_pengering_lainnya"
                                            value="2. Tidak Disyaratkan"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Tidak Disyaratkan</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.alat_pengering_lainnya"
                                            value="1. Ada | ≤ 1"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Ada</span>
                                            <span class="text-gray-600"> | ≤ 1</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.alat_pengering_lainnya" /> -->
                            </div>
                        </div>

                    </div>

                    <!-- Step 3: Sertifikasi & Standar -->
                    <div v-show="currentStep === 2" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mesin Penggilingan</h3>
                        
                        <div class="space-y-6">
                            <!-- Mesin Pembersih Awal -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pembersihan Awal (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_awal"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_awal"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_awal"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pembersih_awal" /> -->
                            </div>

                            <!-- Mesin Pemecah Kulit -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemecah Kulit (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pemecah_kulit" /> -->
                            </div>

                            <!-- Mesin Pembersih Sekam -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pembersih Sekam (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_sekam"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_sekam"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_sekam"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pembersih_sekam" /> -->
                            </div>

                            <!-- Mesin Pemisah Gabah dengan Beras Pecah Kulit -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Gabah dengan Beras Pecah Kulit (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah_pecah_kulit"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah_pecah_kulit"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah_pecah_kulit"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pemisah_gabah_pecah_kulit" /> -->
                            </div>

                            <!-- Mesin Pemisah Batu -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Batu (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_batu"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_batu"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_batu"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pemisah_batu" /> -->
                            </div>

                            <!-- Mesin Penyosoh -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Penyosoh (ton/jam; pass)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="3. Ada | > 3 | 2"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3 | 2</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="2. Ada | 1 s/d 3 | 1"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3 | 1</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="1. Ada | ≤ 1 | 1"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Ada | ≤ 1 | 1</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_penyosoh" /> -->
                            </div>

                            <!-- Mesin Pengkabut -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pengkabut (ton/jam; pass)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengkabut"
                                            value="3. Ada | > 3 | 2"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3 | 2</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengkabut"
                                            value="2. Ada | 1 s/d 3 | 1"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3 | 1</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengkabut"
                                            value="1. Ada | ≤ 1 | 1"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Ada | ≤ 1 | 1</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pengkabut" /> -->
                            </div>

                            <!-- Mesin Pemisah Menir -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Menir (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemesah_menir"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemesah_menir"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemesah_menir"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pemesah_menir" /> -->
                            </div>

                            <!-- Mesin Pemisah Katul -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Katul (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_katul"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_katul"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_katul"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pemisah_katul" /> -->
                            </div>

                            <!-- Mesin Pemisah berdasarkan Ukuran -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah berdasarkan Ukuran (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_berdasarkan_ukuran"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_berdasarkan_ukuran"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_berdasarkan_ukuran"
                                            value="1. Ada | ≤ 1"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Ada | ≤ 1</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pemisah_berdasarkan_ukuran" /> -->
                            </div>

                            <!-- Mesin Pemisah berdasarkan Warna -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah berdasarkan Warna (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_berdasarkan_warna"
                                            value="3. Ada | > 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_berdasarkan_warna"
                                            value="2. Ada | 1 s/d 3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_berdasarkan_warna"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pemisah_berdasarkan_warna" /> -->
                            </div>

                            <!-- Tangki Penyimpanan -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Tangki Penyimpanan (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.tangki_penyimpanan"
                                            value="3. Ada | > 10"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 10</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.tangki_penyimpanan"
                                            value="2. Ada | ≤ 10"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | ≤ 10</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.tangki_penyimpanan"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.tangki_penyimpanan" /> -->
                            </div>

                            <!-- Mesin Pengemas -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pengemas (ton/jam)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengemas"
                                            value="3. Ada | Full Otomatis"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | Full Otomatis</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengemas"
                                            value="2. Ada | Semi Otomatis"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | Semi Otomatis</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengemas"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_pengemas" /> -->
                            </div>

                            <!-- Mesin Jahit -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Jahit (unit)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_jahit"
                                            value="3. Ada | Full Otomatis"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | Full Otomatis</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_jahit"
                                            value="2. Ada | Semi Otomatis"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | Semi Otomatis</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_jahit"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mesin_jahit" /> -->
                            </div>
                        </div>

                    </div>

                    <!-- Step 4: Penyimpanan -->
                    <div v-show="currentStep === 3" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Penyimpanan</h3>
                        
                        <div class="space-y-6">
                            <!-- Gudang Konvensional -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Gudang Konvensional (ton)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.gudang_konvensional"
                                            value="3. Ada | > 3000"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 3000</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.gudang_konvensional"
                                            value="2. Ada | < 3000"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | &lt; 3000</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.gudang_konvensional"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.gudang_konvensional" /> -->
                            </div>

                            <!-- Silo GKG/Hopper -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Silo GKG/Hopper (ton)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.silo_gkg_hopper"
                                            value="3. Ada | > 2000"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 2000</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.silo_gkg_hopper"
                                            value="2. Tidak Ada"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Tidak Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.silo_gkg_hopper"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.silo_gkg_hopper" /> -->
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Sarana Angkutan -->
                    <div v-show="currentStep === 4" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sarana Angkutan</h3>
                        
                        <div class="space-y-6">
                            <!-- Truk -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Truk (unit)</h4>
                                    <p class="text-sm text-gray-500">Kendaraan transportasi untuk distribusi produk</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.truk"
                                            value="3. Ada | > 5"
                                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | > 5 unit</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.truk"
                                            value="2. Ada | 1 s/d 5"
                                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | 1 s/d 5 unit</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.truk"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.truk" /> -->
                            </div>
                        </div>

                    </div>

                    <!-- Step 6: Kelengkapan Pemeriksaan -->
                    <div v-show="currentStep === 5" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Kelengkapan Pemeriksaan</h3>
                        
                        <div class="space-y-6">
                            <!-- Mini Lab -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mini Lab (unit)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mini_lab"
                                            value="3. Ada | Ruang Khusus"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | Ruang Khusus</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mini_lab"
                                            value="2. Ada | Tidak Khusus"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | Tidak Khusus</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mini_lab"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.mini_lab" /> -->
                            </div>

                            <!-- Moisture Tester -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Moisture Tester (G-Won + KETT) (unit)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.moisture_tester"
                                            value="3. Ada | Lengkap | Berfungsi"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | Lengkap | Berfungsi</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.moisture_tester"
                                            value="2. Ada | Berfungsi"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | Berfungsi</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.moisture_tester"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.moisture_tester" /> -->
                            </div>

                            <!-- Pembanding Derajat Sosoh -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Pembanding Derajat Sosoh (Monster) (unit)</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.pembanding_derajat_sosoh"
                                            value="3. Ada | Sesuai Standar"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | Sesuai Standar</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.pembanding_derajat_sosoh"
                                            value="2. Ada | Tidak Sesuai"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | Tidak Sesuai</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.pembanding_derajat_sosoh"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.pembanding_derajat_sosoh" /> -->
                            </div>
                        </div>

                    </div>

                    <!-- Step 7: Organisasi -->
                    <div v-show="currentStep === 6" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Organisasi</h3>
                        
                        <div class="space-y-6">
                            <!-- Bagian Quality Control -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Bagian Quality Control (orang)</h4>
                                    <p class="text-sm text-gray-500">Divisi khusus untuk pengendalian kualitas produk</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.bagian_quality_control"
                                            value="3. Ada | Tidak Merangkap"
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600"> | Tidak Merangkap</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.bagian_quality_control"
                                            value="2. Ada | Merangkap"
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600"> | Merangkap</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.bagian_quality_control"
                                            value="1. Tidak ada"
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <!-- <InputError class="mt-2" :message="form.errors.bagian_quality_control" /> -->
                            </div>
                        </div>

                        <!-- Final Information -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Perhatian</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Pastikan semua data yang diisi sudah benar. Pilih option untuk setiap item sesuai dengan kondisi sebenarnya. Setelah mengirim pengajuan, data tidak dapat diubah.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                        <button
                            type="button"
                            @click="prevStep"
                            :disabled="currentStep === 1"
                            :class="[
                                'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                                currentStep === 1 
                                    ? 'text-gray-400 cursor-not-allowed' 
                                    : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500'
                            ]"
                        >
                            Sebelumnya
                        </button>

                        <div class="flex space-x-3">
                            <button
                                v-if="currentStep < totalSteps"
                                type="button"
                                @click="nextStep"
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            >
                                Selanjutnya
                            </button>

                            <PrimaryButton
                                v-if="currentStep === totalSteps"
                                type="submit"
                                :disabled="isSubmitting"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 focus:ring-green-500"
                            >
                                <span>Simpan Klasifikasi</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </MitraLayout>
</template>