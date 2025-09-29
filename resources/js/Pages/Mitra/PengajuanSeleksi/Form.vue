<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { reactive, ref, onMounted } from 'vue';
import axios from 'axios';

const isSubmitting = ref(false);

// Notification state
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

const form = reactive({
    // Bagian 2: Dokumen Perijinan (Option: Ada/Tidak Ada)
    id_mitra: '',
    surat_permohonan: '',
    mb_surat_permohonan: '',
    akta_notaris: '',
    mb_akta_notaris: '',
    nib: '',
    mb_nib: '',
    ktp: '',
    no_rekening: '',
    mb_no_rekening: '',
    npwp: '',
    mb_npwp: '',
    surat_kuasa: '',
    mb_surat_kuasa: '',

    // Bagian 3: Sarana Pengeringan (Option: Ada/Tidak Ada)
    lantai_jemur: '',
    sarana_lainnya: '',

    // Bagian 4: Sarana Penggilingan (Option: Ada/Tidak Ada)
    mesin_memecah_kulit: '',
    mesin_pemisah_gabah: '',
    mesin_penyosoh: '',
    alat_pemisah_beras: ''
});

const validateForm = () => {
    const errors = {};
    
    // Validasi Dokumen Perijinan
    if (!form.surat_permohonan) errors.surat_permohonan = 'Pilihan Surat Permohonan harus dipilih';
    if (!form.akta_notaris) errors.akta_notaris = 'Pilihan Akta Notaris harus dipilih';
    if (!form.nib) errors.nib = 'Pilihan NIB harus dipilih';
    if (!form.ktp) errors.ktp = 'Pilihan KTP harus dipilih';
    if (!form.no_rekening) errors.no_rekening = 'Pilihan No Rekening harus dipilih';
    if (!form.npwp) errors.npwp = 'Pilihan NPWP harus dipilih';
    if (!form.surat_kuasa) errors.surat_kuasa = 'Pilihan Surat Kuasa harus dipilih';
    
    // Validasi Sarana Pengeringan
    if (!form.lantai_jemur) errors.lantai_jemur = 'Pilihan Lantai Jemur harus dipilih';
    if (!form.sarana_lainnya) errors.sarana_lainnya = 'Pilihan Sarana Lainnya harus dipilih';
    
    // Validasi Sarana Penggilingan
    if (!form.mesin_memecah_kulit) errors.mesin_memecah_kulit = 'Pilihan Mesin Pemecah Kulit harus dipilih';
    if (!form.mesin_pemisah_gabah) errors.mesin_pemisah_gabah = 'Pilihan Mesin Pemisah Gabah harus dipilih';
    if (!form.mesin_penyosoh) errors.mesin_penyosoh = 'Pilihan Mesin Penyosoh harus dipilih';
    if (!form.alat_pemisah_beras) errors.alat_pemisah_beras = 'Pilihan Alat Pemisah Beras harus dipilih';
    
    return errors;
};

const isCurrentStepValid = () => {
    const validationErrors = validateForm();
    
    if (currentStep.value === 1) {
        // Check dokumen perijinan
        const step1Fields = ['surat_permohonan', 'akta_notaris', 'nib', 'ktp', 'no_rekening', 'npwp', 'surat_kuasa'];
        return !step1Fields.some(field => validationErrors[field]);
    } else if (currentStep.value === 2) {
        // Check sarana pengeringan
        const step2Fields = ['lantai_jemur', 'sarana_lainnya'];
        return !step2Fields.some(field => validationErrors[field]);
    } else if (currentStep.value === 3) {
        // Check sarana penggilingan
        const step3Fields = ['mesin_memecah_kulit', 'mesin_pemisah_gabah', 'mesin_penyosoh', 'alat_pemisah_beras'];
        return !step3Fields.some(field => validationErrors[field]);
    }
    
    return true;
};

const statusOptions = [
    { value: 'penggilingan', label: 'Penggilingan' },
    { value: 'distributor', label: 'Distributor' },
    { value: 'lainnya', label: 'Lainnya' }
];

const optionChoices = [
    { value: 'ada', label: 'Ada' },
    { value: 'tidak ada', label: 'Tidak Ada' }
];

const currentStep = ref(1);
const totalSteps = 3;

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        if (isCurrentStepValid()) {
            currentStep.value++;
        } else {
            showNotification('warning', 'Data Belum Lengkap', 'Silakan pilih semua opsi pada step ini sebelum melanjutkan.');
        }
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const responseData = ref(null);

onMounted(async () => {
    try {
        // Panggil API untuk mendapatkan data mitra user login
        const response = await axios.get('/data-mitra/my');
        const mitra = response.data;
        
        if (mitra && mitra.id_mitra) {
            form.id_mitra = mitra.id_mitra;
            showNotification('success', 'Berhasil', 'Data mitra berhasil dimuat',
                {
                    text: 'Lanjutkan',
                    action: () => {
                        hideNotification();
                    }
                }
            );
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

const errors = ref({});

const submit = async () => {
    // Validasi semua form sebelum submit
    const validationErrors = validateForm();
    
    if (Object.keys(validationErrors).length > 0) {
        // Ada field yang belum diisi
        const missingFields = Object.keys(validationErrors);
        const fieldCount = missingFields.length;
        
        showNotification(
            'error', 
            'Data Belum Lengkap', 
            `Terdapat ${fieldCount} pilihan yang belum dipilih. Silakan lengkapi semua pilihan sebelum mengirim pengajuan.`
        );
        
        // Kembali ke step pertama yang memiliki error
        if (missingFields.some(field => ['surat_permohonan', 'akta_notaris', 'nib', 'ktp', 'no_rekening', 'npwp', 'surat_kuasa'].includes(field))) {
            currentStep.value = 1;
        } else if (missingFields.some(field => ['lantai_jemur', 'sarana_lainnya'].includes(field))) {
            currentStep.value = 2;
        } else {
            currentStep.value = 3;
        }
        
        return;
    }
    
    isSubmitting.value = true;
    errors.value = {};
    
    try {
        const response = await axios.post('/data-seleksi-mitra', form);
        responseData.value = response.data;
        
        showNotification(
            'success',
            'Pengajuan Berhasil',
            'Pengajuan seleksi Anda telah berhasil dikirim.',
            {
                text: 'Ok',
                action: () => {
                    window.location.href = '/mitra/pengajuan-seleksi';
                }
            }
        );
        
        // Reset form
        Object.keys(form).forEach(key => {
            if (key === 'id_mitra') return;
            form[key] = '';
        });
        currentStep.value = 1;
        
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};
            showNotification('error', 'Validasi Gagal', 'Terdapat kesalahan pada data yang diisi. Silakan periksa kembali.');
        } else {
            const errorMessage = error.response?.data?.message || error.message;
            showNotification('error', 'Terjadi Kesalahan', `Gagal mengirim data: ${errorMessage}`);
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head title="Pengajuan Seleksi Mitra - ASIMPENAS" />

    <MitraLayout>
        <!-- Enhanced Notification Component -->
        <div 
            v-if="notification.show"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <!-- Notification Modal -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div :class="[
                    'relative w-full max-w-md transform rounded-xl border-2 p-3 shadow-2xl transition-all bg-white',
                    getNotificationClasses(notification.type)
                ]">
                    <!-- Close Button -->
                    <button 
                        @click="hideNotification"
                        :class="[
                            'absolute right-4 top-4 inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2',
                            getCloseButtonColors(notification.type)
                        ]"
                    >
                        <span class="sr-only">Tutup</span>
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

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <!-- Primary Action Button (jika ada) -->
                            <button
                                v-if="notification.showButton"
                                @click="handleNotificationButton"
                                :class="[
                                    'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
                                    getButtonColors(notification.type)
                                ]"
                            >
                                {{ notification.buttonText }}
                            </button>

                            <!-- Close Button -->
                            <!-- <button
                                @click="hideNotification"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                            >
                                {{ notification.showButton ? 'Tutup' : 'OK' }}
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Pengajuan Seleksi Mitra</h2>
                <p class="text-gray-600">Lengkapi formulir berikut untuk mengajukan diri sebagai mitra ASIMPENAS</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div v-for="step in totalSteps" :key="step" class="flex items-center">
                        <div :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                            step <= currentStep ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'
                        ]">
                            {{ step }}
                        </div>
                        <div v-if="step < totalSteps" :class="[
                            'w-20 h-1 mx-2',
                            step < currentStep ? 'bg-blue-600' : 'bg-gray-200'
                        ]"></div>
                    </div>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-500">Dokumen Perijinan</span>
                    <span class="text-xs text-gray-500">Sarana Pengeringan</span>
                    <span class="text-xs text-gray-500">Sarana Penggilingan</span>
                </div>
            </div>

            <!-- Form Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form @submit.prevent="submit">
                    <!-- Step 1: Dokumen Perijinan -->
                    <div v-show="currentStep === 1" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumen Perijinan</h3>

                        <div class="space-y-6">
                            <!-- Surat Permohonan -->
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Surat Permohonan</h4>
                                        <p class="text-sm text-gray-500">Surat permohonan resmi untuk menjadi mitra</p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.surat_permohonan" value="ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.surat_permohonan" value="tidak ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                        </label>
                                    </div>
                                </div>
                                <input type="date" v-model="form.mb_surat_permohonan" class="border rounded p-1"
                                    placeholder="Masa Berlaku Surat Permohonan" />
                            </div>

                            <!-- Akta Notaris -->
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Akta Notaris</h4>
                                        <p class="text-sm text-gray-500">Akta pendirian perusahaan dari notaris</p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.akta_notaris" value="ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.akta_notaris" value="tidak ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                        </label>
                                    </div>
                                </div>
                                <input type="date" v-model="form.mb_akta_notaris" class="border rounded p-1"
                                    placeholder="Masa Berlaku Akta Notaris" />
                            </div>

                            <!-- NIB -->
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-medium text-gray-900">NIB (Nomor Induk Berusaha)</h4>
                                        <p class="text-sm text-gray-500">Nomor Induk Berusaha dari OSS</p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.nib" value="ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.nib" value="tidak ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                        </label>
                                    </div>
                                </div>
                                <input type="date" v-model="form.mb_nib" class="border rounded p-1"
                                    placeholder="Masa Berlaku NIB" />
                            </div>

                            <!-- KTP -->
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-medium text-gray-900">KTP</h4>
                                        <p class="text-sm text-gray-500">Kartu Tanda Penduduk pemilik/pengurus</p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.ktp" value="ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.ktp" value="tidak ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- No Rekening -->
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-medium text-gray-900">No Rekening</h4>
                                        <p class="text-sm text-gray-500">Dokumen rekening bank perusahaan</p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.no_rekening" value="ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.no_rekening" value="tidak ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                        </label>
                                    </div>
                                </div>
                                <input type="date" v-model="form.mb_no_rekening" class="border rounded p-1"
                                    placeholder="Masa Berlaku No Rekening" />
                            </div>

                            <!-- NPWP -->
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-medium text-gray-900">NPWP</h4>
                                        <p class="text-sm text-gray-500">Nomor Pokok Wajib Pajak perusahaan</p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.npwp" value="ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.npwp" value="tidak ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                        </label>
                                    </div>
                                </div>
                                <input type="date" v-model="form.mb_npwp" class="border rounded p-1"
                                    placeholder="Masa Berlaku NPWP" />
                            </div>

                            <!-- Surat Kuasa -->
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Surat Kuasa</h4>
                                        <p class="text-sm text-gray-500">Surat kuasa untuk pengurusan</p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.surat_kuasa" value="ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Ada</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" v-model="form.surat_kuasa" value="tidak ada"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                            <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                        </label>
                                    </div>
                                </div>
                                <input type="date" v-model="form.mb_surat_kuasa" class="border rounded p-1"
                                    placeholder="Masa Berlaku Surat Kuasa" />
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Sarana Pengeringan -->
                    <div v-show="currentStep === 2" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sarana Pengeringan</h3>

                        <div class="space-y-6">
                            <!-- Lantai Jemur -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Lantai Jemur</h4>
                                    <p class="text-sm text-gray-500">Fasilitas lantai untuk penjemuran padi</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Sarana Lainnya -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Sarana Lainnya</h4>
                                    <p class="text-sm text-gray-500">Sarana pengeringan lainnya</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.sarana_lainnya"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.sarana_lainnya"
                                            value="tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Sarana Penggilingan -->
                    <div v-show="currentStep === 3" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sarana Penggilingan</h3>

                        <div class="space-y-6">
                            <!-- Mesin Pemecah Kulit -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Mesin Pemecah Kulit</h4>
                                    <p class="text-sm text-gray-500">Mesin untuk memecah kulit gabah</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.mesin_memecah_kulit" value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.mesin_memecah_kulit" value="tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Mesin Pemisah Gabah -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Gabah</h4>
                                    <p class="text-sm text-gray-500">Mesin untuk memisahkan gabah dari kotoran</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.mesin_pemisah_gabah" value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.mesin_pemisah_gabah" value="tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Mesin Penyosoh -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Mesin Penyosoh</h4>
                                    <p class="text-sm text-gray-500">Mesin untuk menyosoh beras</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.mesin_penyosoh" value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.mesin_penyosoh" value="tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Alat Pemisah Beras -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Alat Pemisah Beras</h4>
                                    <p class="text-sm text-gray-500">Alat untuk memisahkan grade beras</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.alat_pemisah_beras" value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" v-model="form.alat_pemisah_beras" value="tidak ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Review Summary -->
                        <div class="mt-8 bg-gray-50 rounded-lg p-6 space-y-6">
                            <h4 class="font-medium text-gray-900 text-lg">Ringkasan Lengkap Pengajuan</h4>

                            <!-- Dokumen Perijinan -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-medium text-gray-800 mb-3 text-base">1. Dokumen Perijinan</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Surat Permohonan:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.surat_permohonan === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.surat_permohonan === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Akta Notaris:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.akta_notaris === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.akta_notaris === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">NIB:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.nib === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.nib === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">KTP:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.ktp === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.ktp === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">No Rekening:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.no_rekening === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.no_rekening === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">NPWP:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.npwp === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.npwp === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Surat Kuasa:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.surat_kuasa === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.surat_kuasa === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sarana Pengeringan -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-medium text-gray-800 mb-3 text-base">2. Sarana Pengeringan</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Lantai Jemur:</span>
                                        <span :class="[
                                            'px-2 py-1 rounded-full text-xs font-medium',
                                            form.lantai_jemur === 'ada'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-gray-100 text-gray-600'
                                        ]">
                                            {{ form.lantai_jemur === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Sarana Lainnya:</span>
                                        <span :class="[
                                            'px-2 py-1 rounded-full text-xs font-medium',
                                            form.sarana_lainnya === 'ada'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-gray-100 text-gray-600'
                                        ]">
                                            {{ form.sarana_lainnya === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sarana Penggilingan -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-medium text-gray-800 mb-3 text-base">3. Sarana Penggilingan</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Mesin Pemecah Kulit:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.mesin_memecah_kulit === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.mesin_memecah_kulit === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Mesin Pemisah Gabah:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.mesin_pemisah_gabah === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.mesin_pemisah_gabah === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Mesin Penyosoh:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.mesin_penyosoh === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.mesin_penyosoh === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Alat Pemisah Beras:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.alat_pemisah_beras === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.alat_pemisah_beras === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Statistics -->
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <h6 class="font-medium text-blue-900 mb-2">Statistik Kelengkapan</h6>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-green-600">
                                            {{ Math.round(([form.surat_permohonan, form.akta_notaris, form.nib, form.ktp, form.no_rekening, form.npwp, form.surat_kuasa].filter(doc => doc === 'ada').length / 7) * 100) }}%
                                        </div>
                                        <div class="text-green-700">Dokumen</div>
                                        <div class="text-xs text-green-600">
                                            {{ [form.surat_permohonan, form.akta_notaris, form.nib, form.ktp, form.no_rekening, form.npwp, form.surat_kuasa].filter(doc => doc === 'ada').length }}/7 Ada
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-orange-600">
                                            {{ Math.round(([form.lantai_jemur, form.sarana_lainnya].filter(sarana => sarana === 'ada').length / 2) * 100) }}%
                                        </div>
                                        <div class="text-orange-700">Pengeringan</div>
                                        <div class="text-xs text-orange-600">
                                            {{ [form.lantai_jemur, form.sarana_lainnya].filter(sarana => sarana === 'ada').length }}/2 Ada
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-purple-600">
                                            {{ Math.round(([form.mesin_memecah_kulit, form.mesin_pemisah_gabah, form.mesin_penyosoh, form.alat_pemisah_beras].filter(mesin => mesin === 'ada').length / 4) * 100) }}%
                                        </div>
                                        <div class="text-purple-700">Penggilingan</div>
                                        <div class="text-xs text-purple-600">
                                            {{ [form.mesin_memecah_kulit, form.mesin_pemisah_gabah, form.mesin_penyosoh, form.alat_pemisah_beras].filter(mesin => mesin === 'ada').length }}/4 Ada
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Perhatian</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Pastikan semua data yang diisi sudah benar. Pilih "Ada" atau "Tidak Ada"
                                            untuk setiap item sesuai dengan kondisi sebenarnya. Setelah mengirim
                                            pengajuan, data tidak dapat diubah.</p>
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
                                    : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500'
                            ]"
                        >
                            Sebelumnya
                        </button>

                        <div class="flex space-x-3">
                            <button
                                v-if="currentStep < totalSteps"
                                type="button"
                                @click="nextStep"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                Selanjutnya
                            </button>

                            <button
                                v-if="currentStep === totalSteps"
                                type="button"
                                @click="submit"
                                :disabled="isSubmitting || !isCurrentStepValid()"
                                :class="[
                                    'px-6 py-2 text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500',
                                    isSubmitting || !isCurrentStepValid()
                                        ? 'bg-blue-400 text-white cursor-not-allowed' 
                                        : 'bg-blue-600 text-white hover:bg-blue-700'
                                ]"
                            >
                                <span v-if="isSubmitting">Mengirim...</span>
                                <span v-else-if="!isCurrentStepValid()">Lengkapi Data</span>
                                <span v-else>Kirim Pengajuan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </MitraLayout>
</template>