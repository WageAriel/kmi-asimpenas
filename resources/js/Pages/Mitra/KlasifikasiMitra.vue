<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const form = useForm({
    // Informasi Umum
    nama_mitra: '',
    badan_hukum: '',
    alamat_perusahaan: '',
    status: '',
    
    // Fasilitas & Mesin Produksi (Step 2)
    mesin_pembersih_gabah: 'tidak_ada',
    lantai_jemur: 'tidak_ada',
    mesin_pengering: 'tidak_ada',
    alat_pengering_lainnya: 'ada_kurang_1',
    
    // Mesin Penggilingan (Step 3)
    mesin_pembersih_awal: 'tidak_ada',
    mesin_pemecah_kulit: 'tidak_ada',
    mesin_pembersih_sekam: 'tidak_ada',
    mesin_pemisah_gabah_beras: 'tidak_ada',
    mesin_pemisah_batu: 'tidak_ada',
    mesin_penyosoh: 'tidak_ada',
    mesin_pengkabut: 'tidak_ada',
    mesin_pemisah_menir: 'tidak_ada',
    mesin_pemisah_katul: 'tidak_ada',
    mesin_pemisah_ukuran: 'tidak_ada',
    mesin_pemisah_warna: 'tidak_ada',
    tangki_penyimpanan: 'tidak_ada',
    mesin_pengemas: 'tidak_ada',
    mesin_jahit: 'tidak_ada',
    
    // Penyimpanan (Step 4)
    gudang_konvensional: 'tidak_ada',
    silo_gkg_hopper: 'tidak_ada',

    // Transportasi (Step 5)
    truk: 'tidak_ada',

    // Kelengkapan Pemeriksaan (Step 6)
    mini_lab: 'tidak_ada',
    moisture_tester: 'tidak_ada',
    pembanding_derajat_sosoh: 'tidak_ada',

    // Organisasi (Step 7)
    bagian_quality_control: 'tidak_ada',
});

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
const totalSteps = 7;
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

// Submit function
const submit = () => {
    form.post(route('mitra.klasifikasi-mitra.store'), {
        onSuccess: () => {
            alert('Data klasifikasi mitra berhasil disimpan!');
        },
        onError: () => {
            alert('Terjadi kesalahan. Silakan periksa kembali data Anda.');
        }
    });
};
</script>

<template>
    <Head title="Klasifikasi Mitra - ASIMPENAS" />

    <MitraLayout>
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
                    <span class="text-xs text-gray-500">Info Umum</span>
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
                    <div v-show="currentStep === 1" class="p-6 space-y-6">
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

                    </div>

                    <!-- Step 2: Kapasitas Produksi -->
                    <div v-show="currentStep === 2" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengeringan</h3>
                        
                        <div class="space-y-6">
                            <!-- Mesin Pembersih Gabah -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pembersih Gabah</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_gabah"
                                            value="ada_lebih_20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 20 unit</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_gabah"
                                            value="ada_kurang_20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| ≤ 20 unit</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_gabah"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pembersih_gabah" />
                            </div>

                            <!-- Lantai Jemur -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Lantai Jemur</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="ada_lebih_10"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 10</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="ada_1_sampai_10"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 10</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.lantai_jemur"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.lantai_jemur" />
                            </div>

                            <!-- Mesin Pengering -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pengering</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengering"
                                            value="ada_lebih_20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 20</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengering"
                                            value="ada_kurang_20"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| ≤ 20</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengering"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pengering" />
                            </div>

                            <!-- Alat Pengering Lainnya -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Alat Pengering Lainnya</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.alat_pengering_lainnya"
                                            value="tidak_disyaratkan"
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
                                            value="tidak_disyaratkan"
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
                                            value="ada_kurang_1"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Ada</span>
                                            <span class="text-gray-600">| ≤ 1</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.alat_pengering_lainnya" />
                            </div>
                        </div>

                    </div>

                    <!-- Step 3: Sertifikasi & Standar -->
                    <div v-show="currentStep === 3" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mesin Penggilingan</h3>
                        
                        <div class="space-y-6">
                            <!-- Mesin Pembersih Awal -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pembersih Awal</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_awal"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_awal"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_awal"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pembersih_awal" />
                            </div>

                            <!-- Mesin Pemecah Kulit -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemecah Kulit</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pemecah_kulit" />
                            </div>

                            <!-- Mesin Pembersih Sekam -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pembersih Sekam</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_sekam"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_sekam"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pembersih_sekam"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pembersih_sekam" />
                            </div>

                            <!-- Mesin Pemisah Gabah dengan Beras Pecah Kulit -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Gabah dengan Beras Pecah Kulit</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah_beras"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah_beras"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah_beras"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pemisah_gabah_beras" />
                            </div>

                            <!-- Mesin Pemisah Batu -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Batu</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_batu"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_batu"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_batu"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pemisah_batu" />
                            </div>

                            <!-- Mesin Penyosoh -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Penyosoh</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_penyosoh" />
                            </div>

                            <!-- Mesin Pengkabut -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pengkabut</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengkabut"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengkabut"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengkabut"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pengkabut" />
                            </div>

                            <!-- Mesin Pemisah Menir -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Menir</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_menir"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_menir"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_menir"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pemisah_menir" />
                            </div>

                            <!-- Mesin Pemisah Katul -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah Katul</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_katul"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_katul"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_katul"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pemisah_katul" />
                            </div>

                            <!-- Mesin Pemisah berdasarkan Ukuran -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah berdasarkan Ukuran</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_ukuran"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_ukuran"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_ukuran"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pemisah_ukuran" />
                            </div>

                            <!-- Mesin Pemisah berdasarkan Warna -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pemisah berdasarkan Warna</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_warna"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_warna"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_warna"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pemisah_warna" />
                            </div>

                            <!-- Tangki Penyimpanan -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Tangki Penyimpanan</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.tangki_penyimpanan"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.tangki_penyimpanan"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.tangki_penyimpanan"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.tangki_penyimpanan" />
                            </div>

                            <!-- Mesin Pengemas -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Pengemas</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengemas"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengemas"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pengemas"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_pengemas" />
                            </div>

                            <!-- Mesin Jahit -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mesin Jahit</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_jahit"
                                            value="ada_lebih_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_jahit"
                                            value="ada_1_sampai_3"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 3</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_jahit"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mesin_jahit" />
                            </div>
                        </div>

                    </div>

                    <!-- Step 4: Penyimpanan -->
                    <div v-show="currentStep === 4" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Penyimpanan</h3>
                        
                        <div class="space-y-6">
                            <!-- Gudang Konvensional -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Gudang Konvensional</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.gudang_konvensional"
                                            value="ada_lebih_3000"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3000</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.gudang_konvensional"
                                            value="ada_kurang_3000"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| &lt; 3000</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.gudang_konvensional"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.gudang_konvensional" />
                            </div>

                            <!-- Silo GKG/Hopper -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Silo GKG/Hopper</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.silo_gkg_hopper"
                                            value="ada_lebih_3000"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 3000</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.silo_gkg_hopper"
                                            value="ada_kurang_3000"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| &lt; 3000</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.silo_gkg_hopper"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.silo_gkg_hopper" />
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Sarana Angkutan -->
                    <div v-show="currentStep === 5" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sarana Angkutan</h3>
                        
                        <div class="space-y-6">
                            <!-- Truk -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Truk</h4>
                                    <p class="text-sm text-gray-500">Kendaraan transportasi untuk distribusi produk</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.truk"
                                            value="ada_lebih_5"
                                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                            <span class="text-gray-600">| > 5 unit</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.truk"
                                            value="ada_1_sampai_5"
                                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                            <span class="text-gray-600">| 1 s/d 5 unit</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.truk"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.truk" />
                            </div>
                        </div>

                    </div>

                    <!-- Step 6: Kelengkapan Pemeriksaan -->
                    <div v-show="currentStep === 6" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Kelengkapan Pemeriksaan</h3>
                        
                        <div class="space-y-6">
                            <!-- Mini Lab -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Mini Lab</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mini_lab"
                                            value="ada_lengkap"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mini_lab"
                                            value="ada_sederhana"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mini_lab"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.mini_lab" />
                            </div>

                            <!-- Moisture Tester -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Moisture Tester</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.moisture_tester"
                                            value="ada_digital"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.moisture_tester"
                                            value="ada_manual"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.moisture_tester"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.moisture_tester" />
                            </div>

                            <!-- Pembanding Derajat Sosoh -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Pembanding Derajat Sosoh</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.pembanding_derajat_sosoh"
                                            value="ada_standar"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.pembanding_derajat_sosoh"
                                            value="ada_lokal"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.pembanding_derajat_sosoh"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.pembanding_derajat_sosoh" />
                            </div>
                        </div>

                    </div>

                    <!-- Step 7: Organisasi -->
                    <div v-show="currentStep === 7" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Organisasi</h3>
                        
                        <div class="space-y-6">
                            <!-- Bagian Quality Control -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-900">Bagian Quality Control</h4>
                                    <p class="text-sm text-gray-500">Divisi khusus untuk pengendalian kualitas produk</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.bagian_quality_control"
                                            value="ada_profesional"
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">A. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.bagian_quality_control"
                                            value="ada_terbatas"
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">B. Ada</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.bagian_quality_control"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                        />
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">C. Tidak Ada</span>
                                        </span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.bagian_quality_control" />
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
                                :disabled="form.processing"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 focus:ring-green-500"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Klasifikasi</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </MitraLayout>
</template>