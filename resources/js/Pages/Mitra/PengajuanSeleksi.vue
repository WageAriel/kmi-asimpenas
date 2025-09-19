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
        currentStep.value++;
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
        } else {
            alert('Data mitra user tidak ditemukan.');
        }
    } catch (error) {
        alert('Gagal mengambil data mitra user: ' + (error.response?.data?.message || error.message));
    }
});

const errors = ref({});

const submit = async () => {
    isSubmitting.value = true;
    errors.value = {}; // Reset error sebelumnya
    try {
        const response = await axios.post('/data-seleksi-mitra', form);
        responseData.value = response.data;
        alert('Pengajuan seleksi berhasil dikirim!');

        // Reset form: set nilai field enum ke empty string atau 'tidak ada' sesuai kebutuhan
        Object.keys(form).forEach(key => {
            if (key === 'id_mitra') return; // jangan reset id_mitra karena otomatis sesuai user login
            form[key] = '';
        });
        currentStep.value = 1;
    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Validasi error dari Laravel
            errors.value = error.response.data.errors || {};
        } else {
            alert('Terjadi kesalahan saat mengirim data.');
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head title="Pengajuan Seleksi Mitra - ASIMPENAS" />

    <MitraLayout>
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
                    <!-- Step 1: Data Calon Mitra Kerja (tetap sama) -->

                    <!-- Step 2: Dokumen Perijinan -->
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

                    <!-- Step 3: Sarana Pengeringan -->
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

                    <!-- Step 4: Sarana Penggilingan -->
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
                                        <input type="radio" v-model="form.mesin_memecah_kulit" value="tidak_ada"
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
                                        <input type="radio" v-model="form.mesin_pemisah_gabah" value="tidak_ada"
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
                                        <input type="radio" v-model="form.mesin_penyosoh" value="tidak_ada"
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
                                        <input type="radio" v-model="form.alat_pemisah_beras" value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Review Summary -->
                        <div class="mt-8 bg-gray-50 rounded-lg p-6 space-y-6">
                            <h4 class="font-medium text-gray-900 text-lg">Ringkasan Lengkap Pengajuan</h4>

                            <!-- Bagian 1: Data Calon Mitra Kerja -->

                            <!-- Bagian 2: Dokumen Perijinan -->
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
                                            <span class="text-gray-600">Dokumen Rekening:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.no_rekening_doc === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.no_rekening_doc === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
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
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Surat PKP/Non PKP:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.surat_pkp_non_pkp === 'ada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ form.surat_pkp_non_pkp === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 3: Sarana Pengeringan -->
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

                            <!-- Bagian 4: Sarana Penggilingan -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-medium text-gray-800 mb-3 text-base">3. Sarana Penggilingan</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Mesin Pemecah Kulit:</span>
                                            <span :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                form.mesin_pemecah_kulit === 'ada'
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
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-green-600">
                                            {{Math.round(([form.surat_permohonan, form.akta_notaris, form.nib,
                                                form.ktp, form.no_rekening_doc, form.npwp, form.surat_kuasa,
                                            form.surat_pkp_non_pkp].filter(doc => doc === 'ada').length / 8) * 100) }}%
                                        </div>
                                        <div class="text-green-700">Dokumen</div>
                                        <div class="text-xs text-green-600">
                                            {{[form.surat_permohonan, form.akta_notaris, form.nib, form.ktp,
                                                form.no_rekening_doc, form.npwp, form.surat_kuasa,
                                            form.surat_pkp_non_pkp].filter(doc => doc === 'ada').length }}/8 Ada
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-orange-600">
                                            {{Math.round(([form.lantai_jemur, form.sarana_lainnya].filter(sarana =>
                                            sarana === 'ada').length / 2) * 100) }}%
                                        </div>
                                        <div class="text-orange-700">Pengeringan</div>
                                        <div class="text-xs text-orange-600">
                                            {{[form.lantai_jemur, form.sarana_lainnya].filter(sarana => sarana ===
                                            'ada').length }}/2 Ada
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-purple-600">
                                            {{Math.round(([form.mesin_memecah_kulit, form.mesin_pemisah_gabah,
                                            form.mesin_penyosoh, form.alat_pemisah_beras].filter(mesin => mesin ===
                                            'ada').length / 4) * 100) }}%
                                        </div>
                                        <div class="text-purple-700">Penggilingan</div>
                                        <div class="text-xs text-purple-600">
                                            {{[form.mesin_pemecah_kulit, form.mesin_pemisah_gabah, form.mesin_penyosoh,
                                            form.alat_pemisah_beras].filter(mesin => mesin === 'ada').length }}/4 Ada
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

                            <PrimaryButton
                                v-if="currentStep === totalSteps"
                                :disabled="isSubmitting"
                                class="px-6 py-2"
                                @click="submit"
                            >
                                <span v-if="isSubmitting">Mengirim...</span>
                                <span v-else>Kirim Pengajuan</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </MitraLayout>
</template>