<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const form = useForm({
    // Bagian 1: Data Calon Mitra Kerja
    nama_perusahaan: '',
    badan_hukum: '',
    alamat: '',
    no_telp_perusahaan: '',
    nama_contact_person: '',
    no_telp_contact_person: '',
    nama_bank_koresponden: '',
    alamat_bank_koresponden: '',
    no_rekening: '',
    status: '',
    
    // Bagian 2: Dokumen Perijinan (Option: Ada/Tidak Ada)
    surat_permohonan: 'tidak_ada',
    akta_notaris: 'tidak_ada',
    nib: 'tidak_ada',
    ktp: 'tidak_ada',
    no_rekening_doc: 'tidak_ada',
    npwp: 'tidak_ada',
    surat_kuasa: 'tidak_ada',
    surat_pkp_non_pkp: 'tidak_ada',
    
    // Bagian 3: Sarana Pengeringan (Option: Ada/Tidak Ada)
    lantai_jemur: 'tidak_ada',
    sarana_lainnya: 'tidak_ada',
    
    // Bagian 4: Sarana Penggilingan (Option: Ada/Tidak Ada)
    mesin_pemecah_kulit: 'tidak_ada',
    mesin_pemisah_gabah: 'tidak_ada',
    mesin_penyosoh: 'tidak_ada',
    alat_pemisah_beras: 'tidak_ada'
});

const badanHukumOptions = [
    { value: 'pt', label: 'PT (Perseroan Terbatas)' },
    { value: 'cv', label: 'CV (Comanditaire Vennootschap)' },
    { value: 'ud', label: 'UD (Usaha Dagang)' },
    { value: 'koperasi', label: 'Koperasi' },
    { value: 'perorangan', label: 'Perorangan' },
    { value: 'lainnya', label: 'Lainnya' }
];

const statusOptions = [
    { value: 'aktif', label: 'Aktif' },
    { value: 'non_aktif', label: 'Non Aktif' },
    { value: 'pending', label: 'Pending' }
];

const optionChoices = [
    { value: 'ada', label: 'Ada' },
    { value: 'tidak_ada', label: 'Tidak Ada' }
];

const currentStep = ref(1);
const totalSteps = 4;

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

const submit = () => {
    form.post(route('mitra.pengajuan-seleksi.store'), {
        onSuccess: () => {
            alert('Pengajuan seleksi berhasil dikirim!');
        },
        onError: () => {
            alert('Terjadi kesalahan. Silakan periksa kembali data Anda.');
        }
    });
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
                    <span class="text-xs text-gray-500">Data Calon Mitra</span>
                    <span class="text-xs text-gray-500">Dokumen Perijinan</span>
                    <span class="text-xs text-gray-500">Sarana Pengeringan</span>
                    <span class="text-xs text-gray-500">Sarana Penggilingan</span>
                </div>
            </div>

            <!-- Form Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form @submit.prevent="submit">
                    <!-- Step 1: Data Calon Mitra Kerja (tetap sama) -->
                    <div v-show="currentStep === 1" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Calon Mitra Kerja</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="nama_perusahaan" value="Nama Perusahaan *" />
                                <TextInput
                                    id="nama_perusahaan"
                                    v-model="form.nama_perusahaan"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.nama_perusahaan" />
                            </div>

                            <div>
                                <InputLabel for="badan_hukum" value="Badan Hukum/Usaha *" />
                                <select
                                    id="badan_hukum"
                                    v-model="form.badan_hukum"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Pilih Badan Hukum</option>
                                    <option v-for="option in badanHukumOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.badan_hukum" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="alamat" value="Alamat *" />
                            <textarea
                                id="alamat"
                                v-model="form.alamat"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="3"
                                required
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.alamat" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="no_telp_perusahaan" value="No. Telp Perusahaan *" />
                                <TextInput
                                    id="no_telp_perusahaan"
                                    v-model="form.no_telp_perusahaan"
                                    type="tel"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.no_telp_perusahaan" />
                            </div>

                            <div>
                                <InputLabel for="nama_contact_person" value="Nama Contact Person *" />
                                <TextInput
                                    id="nama_contact_person"
                                    v-model="form.nama_contact_person"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.nama_contact_person" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="no_telp_contact_person" value="No. Telp Contact Person *" />
                                <TextInput
                                    id="no_telp_contact_person"
                                    v-model="form.no_telp_contact_person"
                                    type="tel"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.no_telp_contact_person" />
                            </div>

                            <div>
                                <InputLabel for="nama_bank_koresponden" value="Nama Bank Koresponden *" />
                                <TextInput
                                    id="nama_bank_koresponden"
                                    v-model="form.nama_bank_koresponden"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Bank BCA, Bank Mandiri, dll"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.nama_bank_koresponden" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="alamat_bank_koresponden" value="Alamat Bank Koresponden *" />
                            <textarea
                                id="alamat_bank_koresponden"
                                v-model="form.alamat_bank_koresponden"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="2"
                                required
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.alamat_bank_koresponden" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="no_rekening" value="No. Rekening *" />
                                <TextInput
                                    id="no_rekening"
                                    v-model="form.no_rekening"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.no_rekening" />
                            </div>

                            <div>
                                <InputLabel for="status" value="Status *" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Pilih Status</option>
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Dokumen Perijinan -->
                    <div v-show="currentStep === 2" class="p-6 space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumen Perijinan</h3>
                        
                        <div class="space-y-6">
                            <!-- Surat Permohonan -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Surat Permohonan</h4>
                                    <p class="text-sm text-gray-500">Surat permohonan resmi untuk menjadi mitra</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.surat_permohonan"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.surat_permohonan"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Akta Notaris -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Akta Notaris</h4>
                                    <p class="text-sm text-gray-500">Akta pendirian perusahaan dari notaris</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.akta_notaris"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.akta_notaris"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- NIB -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">NIB (Nomor Induk Berusaha)</h4>
                                    <p class="text-sm text-gray-500">Nomor Induk Berusaha dari OSS</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.nib"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.nib"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- KTP -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">KTP</h4>
                                    <p class="text-sm text-gray-500">Kartu Tanda Penduduk pemilik/pengurus</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.ktp"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.ktp"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- No Rekening -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">No Rekening</h4>
                                    <p class="text-sm text-gray-500">Dokumen rekening bank perusahaan</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.no_rekening_doc"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.no_rekening_doc"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- NPWP -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">NPWP</h4>
                                    <p class="text-sm text-gray-500">Nomor Pokok Wajib Pajak perusahaan</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.npwp"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.npwp"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Surat Kuasa -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Surat Kuasa</h4>
                                    <p class="text-sm text-gray-500">Surat kuasa untuk pengurusan</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.surat_kuasa"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.surat_kuasa"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Surat PKP/Non PKP -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Surat PKP / Surat Non PKP</h4>
                                    <p class="text-sm text-gray-500">Surat Pengukuhan Pengusaha Kena Pajak</p>
                                </div>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.surat_pkp_non_pkp"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.surat_pkp_non_pkp"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Sarana Pengeringan -->
                    <div v-show="currentStep === 3" class="p-6 space-y-6">
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
                                            value="tidak_ada"
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
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Sarana Penggilingan -->
                    <div v-show="currentStep === 4" class="p-6 space-y-6">
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
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemecah_kulit"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
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
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_pemisah_gabah"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
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
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.mesin_penyosoh"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
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
                                        <input
                                            type="radio"
                                            v-model="form.alat_pemisah_beras"
                                            value="ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Ada</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.alat_pemisah_beras"
                                            value="tidak_ada"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Review Summary -->
                        <div class="mt-8 bg-gray-50 rounded-lg p-6 space-y-6">
                            <h4 class="font-medium text-gray-900 text-lg">Ringkasan Lengkap Pengajuan</h4>
                            
                            <!-- Bagian 1: Data Calon Mitra Kerja -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-medium text-gray-800 mb-3 text-base">1. Data Calon Mitra Kerja</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Nama Perusahaan:</span>
                                            <span class="font-medium text-gray-900">{{ form.nama_perusahaan || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Badan Hukum:</span>
                                            <span class="font-medium text-gray-900">{{ form.badan_hukum || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">No. Telp Perusahaan:</span>
                                            <span class="font-medium text-gray-900">{{ form.no_telp_perusahaan || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Contact Person:</span>
                                            <span class="font-medium text-gray-900">{{ form.nama_contact_person || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">No. Telp Contact:</span>
                                            <span class="font-medium text-gray-900">{{ form.no_telp_contact_person || '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Bank Koresponden:</span>
                                            <span class="font-medium text-gray-900">{{ form.nama_bank_koresponden || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">No. Rekening:</span>
                                            <span class="font-medium text-gray-900">{{ form.no_rekening || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="font-medium text-gray-900">{{ form.status || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Alamat Perusahaan:</span>
                                        <span class="font-medium text-gray-900 text-right max-w-md">{{ form.alamat || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between mt-2">
                                        <span class="text-gray-600">Alamat Bank:</span>
                                        <span class="font-medium text-gray-900 text-right max-w-md">{{ form.alamat_bank_koresponden || '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 2: Dokumen Perijinan -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-medium text-gray-800 mb-3 text-base">2. Dokumen Perijinan</h5>
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
                                <h5 class="font-medium text-gray-800 mb-3 text-base">3. Sarana Pengeringan</h5>
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
                                <h5 class="font-medium text-gray-800 mb-3 text-base">4. Sarana Penggilingan</h5>
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
                                                {{ form.mesin_pemecah_kulit === 'ada' ? '✓ Ada' : '○ Tidak Ada' }}
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
                                        <div class="text-lg font-bold text-blue-600">100%</div>
                                        <div class="text-blue-700">Data Mitra</div>
                                        <div class="text-xs text-blue-600">Lengkap</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-green-600">
                                            {{ Math.round(([form.surat_permohonan, form.akta_notaris, form.nib, form.ktp, form.no_rekening_doc, form.npwp, form.surat_kuasa, form.surat_pkp_non_pkp].filter(doc => doc === 'ada').length / 8) * 100) }}%
                                        </div>
                                        <div class="text-green-700">Dokumen</div>
                                        <div class="text-xs text-green-600">
                                            {{ [form.surat_permohonan, form.akta_notaris, form.nib, form.ktp, form.no_rekening_doc, form.npwp, form.surat_kuasa, form.surat_pkp_non_pkp].filter(doc => doc === 'ada').length }}/8 Ada
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
                                            {{ Math.round(([form.mesin_pemecah_kulit, form.mesin_pemisah_gabah, form.mesin_penyosoh, form.alat_pemisah_beras].filter(mesin => mesin === 'ada').length / 4) * 100) }}%
                                        </div>
                                        <div class="text-purple-700">Penggilingan</div>
                                        <div class="text-xs text-purple-600">
                                            {{ [form.mesin_pemecah_kulit, form.mesin_pemisah_gabah, form.mesin_penyosoh, form.alat_pemisah_beras].filter(mesin => mesin === 'ada').length }}/4 Ada
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Perhatian</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Pastikan semua data yang diisi sudah benar. Pilih "Ada" atau "Tidak Ada" untuk setiap item sesuai dengan kondisi sebenarnya. Setelah mengirim pengajuan, data tidak dapat diubah.</p>
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
                                :disabled="form.processing"
                                class="px-6 py-2"
                            >
                                <span v-if="form.processing">Mengirim...</span>
                                <span v-else>Kirim Pengajuan</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </MitraLayout>
</template>