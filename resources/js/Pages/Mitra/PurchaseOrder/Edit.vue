<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
    purchaseOrder: Object,
    jenisKomoditasOptions: Array,
    komplekPergudanganOptions: Array
});

const form = useForm({
    nama_perusahaan: props.purchaseOrder.nama_perusahaan,
    jenis_komoditas: props.purchaseOrder.jenis_komoditas,
    jenis_komoditas_custom: props.purchaseOrder.jenis_komoditas_custom,
    jenis_pengadaan: props.purchaseOrder.jenis_pengadaan,
    harga: props.purchaseOrder.harga.toString(),
    kuantum: props.purchaseOrder.kuantum.toString(),
    komplek_pergudangan: props.purchaseOrder.komplek_pergudangan,
    komplek_pergudangan_custom: props.purchaseOrder.komplek_pergudangan_custom,
    kualitas: props.purchaseOrder.kualitas,
    kualitas_custom: props.purchaseOrder.kualitas_custom,
    agenda_no: props.purchaseOrder.agenda_no,
    tanggal_terima: props.purchaseOrder.tanggal_terima,
    paraf: props.purchaseOrder.paraf,
    kontrak_yll: props.purchaseOrder.kontrak_yll
});

const kualitasOptions = ref([]);
const showJenisKomoditasCustom = computed(() => form.jenis_komoditas === 'Lain-lain');
const showKomplekPergudanganCustom = computed(() => form.komplek_pergudangan === 'Custom');
const showKualitasCustom = computed(() => form.kualitas === 'Lain-lain');

// Hitung nilai total secara real-time
const nilaiTotal = computed(() => {
    const harga = parseFloat(form.harga) || 0;
    const kuantum = parseFloat(form.kuantum) || 0;
    return (harga * kuantum).toLocaleString('id-ID');
});

// Format angka dengan separator ribuan
const formatNumber = (value) => {
    if (!value) return '';
    // Hapus semua karakter non-digit
    const number = value.toString().replace(/\D/g, '');
    // Format dengan separator ribuan
    return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

// Handle input harga
const handleHargaInput = (event) => {
    const value = event.target.value.replace(/\./g, '');
    form.harga = value;
    // Update tampilan input dengan format
    event.target.value = formatNumber(value);
};

// Handle input kuantum
const handleKuantumInput = (event) => {
    const value = event.target.value.replace(/\./g, '');
    form.kuantum = value;
    // Update tampilan input dengan format
    event.target.value = formatNumber(value);
};

// Function untuk update kualitas options
const updateKualitasOptions = (jenisKomoditas) => {
    switch (jenisKomoditas) {
        case 'GKP':
            kualitasOptions.value = ['Polos', 'Kemasan', 'Lain-lain'];
            break;
        case 'Beras':
            kualitasOptions.value = ['Medium', 'Premium', 'Khusus', 'Lain-lain'];
            break;
        case 'GKG':
            kualitasOptions.value = ['GKG', 'Lain-lain'];
            // Set default ke nama komoditas jika kualitas kosong
            if (!form.kualitas) form.kualitas = 'GKG';
            break;
        case 'Jagung':
            kualitasOptions.value = ['Jagung', 'Lain-lain'];
            // Set default ke nama komoditas jika kualitas kosong
            if (!form.kualitas) form.kualitas = 'Jagung';
            break;
        case 'Gula Pasir':
            kualitasOptions.value = ['Gula Pasir', 'Lain-lain'];
            // Set default ke nama komoditas jika kualitas kosong
            if (!form.kualitas) form.kualitas = 'Gula Pasir';
            break;
        case 'Minyak Goreng':
            kualitasOptions.value = ['Minyak Goreng', 'Lain-lain'];
            // Set default ke nama komoditas jika kualitas kosong
            if (!form.kualitas) form.kualitas = 'Minyak Goreng';
            break;
        case 'Lain-lain':
            kualitasOptions.value = ['Lain-lain'];
            // Set default ke nama komoditas jika kualitas kosong
            if (!form.kualitas) form.kualitas = 'Lain-lain';
            break;
        default:
            kualitasOptions.value = ['Lain-lain'];
            // Set default ke nama komoditas jika kualitas kosong
            if (!form.kualitas) form.kualitas = 'Lain-lain';
            break;
    }
};

// Watch perubahan jenis komoditas untuk update kualitas
watch(() => form.jenis_komoditas, (newValue) => {
    // Reset kualitas saat jenis komoditas berubah (kecuali saat pertama load)
    if (newValue !== props.purchaseOrder.jenis_komoditas) {
        form.kualitas = '';
        form.kualitas_custom = '';
    }
    
    updateKualitasOptions(newValue);
});

// Initialize kualitas options saat component mounted
onMounted(() => {
    updateKualitasOptions(form.jenis_komoditas);
    
    // Format input numbers on mount
    const hargaInput = document.getElementById('harga');
    const kuantumInput = document.getElementById('kuantum');
    
    if (hargaInput) {
        hargaInput.value = formatNumber(form.harga);
    }
    if (kuantumInput) {
        kuantumInput.value = formatNumber(form.kuantum);
    }
});


const submit = () => {
    form.put(route('mitra.purchase-orders.update', props.purchaseOrder.id));
};
</script>

<template>
    <Head title="Edit Purchase Order - ASIMPENAS" />

    <MitraLayout>
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-4">
                            <button 
                                @click="$inertia.visit(route('mitra.purchase-orders.show', purchaseOrder.id))"
                                class="text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Edit Purchase Order</h1>
                                <p class="text-gray-600 mt-1">Edit data PO #{{ purchaseOrder.id }} - {{ purchaseOrder.nama_perusahaan }}</p>
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
                            <TextInput
                                id="nama_perusahaan"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.nama_perusahaan"
                                required
                                placeholder="Masukkan nama perusahaan"
                            />
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

                        <!-- Harga dan Kuantum -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="harga" value="Harga (Rp/Kg)" />
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input
                                        id="harga"
                                        type="text"
                                        @input="handleHargaInput"
                                        class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="0"
                                        required
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.harga" />
                            </div>

                            <div>
                                <InputLabel for="kuantum" value="Kuantum (Kg)" />
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input
                                        id="kuantum"
                                        type="text"
                                        @input="handleKuantumInput"
                                        class="block w-full pr-12 py-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="0"
                                        required
                                    />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Kg</span>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.kuantum" />
                            </div>
                        </div>

                        <!-- Nilai Total (readonly) -->
                        <div>
                            <InputLabel value="Nilai Total" />
                            <div class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 font-medium">
                                Rp {{ nilaiTotal }}
                            </div>
                        </div>

                        <!-- Komplek Pergudangan -->
                        <div>
                            <InputLabel for="komplek_pergudangan" value="Komplek Pergudangan" />
                            <select
                                id="komplek_pergudangan"
                                v-model="form.komplek_pergudangan"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="">Pilih komplek pergudangan</option>
                                <option v-for="option in komplekPergudanganOptions" :key="option" :value="option">
                                    {{ option === 'Custom' ? 'Lainnya (Input Manual)' : 'Komplek ' + option }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.komplek_pergudangan" />
                            
                            <!-- Custom input untuk "Custom" -->
                            <div v-if="showKomplekPergudanganCustom" class="mt-3">
                                <InputLabel for="komplek_pergudangan_custom" value="Nama Komplek Pergudangan" />
                                <TextInput
                                    id="komplek_pergudangan_custom"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.komplek_pergudangan_custom"
                                    placeholder="Masukkan nama komplek pergudangan"
                                />
                                <InputError class="mt-2" :message="form.errors.komplek_pergudangan_custom" />
                            </div>
                        </div>

                        <!-- Kualitas -->
                        <div>
                            <InputLabel for="kualitas" value="Kualitas" />
                            <select
                                id="kualitas"
                                v-model="form.kualitas"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                :disabled="!form.jenis_komoditas"
                            >
                                <option value="">{{ form.jenis_komoditas ? 'Pilih kualitas' : 'Pilih jenis komoditas terlebih dahulu' }}</option>
                                <option v-for="option in kualitasOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.kualitas" />
                            
                            <!-- Custom input untuk "Lain-lain" -->
                            <div v-if="showKualitasCustom" class="mt-3">
                                <InputLabel for="kualitas_custom" value="Spesifikasi Kualitas" />
                                <TextInput
                                    id="kualitas_custom"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.kualitas_custom"
                                    placeholder="Masukkan spesifikasi kualitas lainnya"
                                />
                                <InputError class="mt-2" :message="form.errors.kualitas_custom" />
                            </div>
                        </div>

                        <!--
                        //Additional Fields
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="agenda_no" value="Agenda No" />
                                    <TextInput
                                        id="agenda_no"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.agenda_no"
                                        placeholder="Masukkan agenda no"
                                    />
                                    <InputError class="mt-2" :message="form.errors.agenda_no" />
                                </div>

                                <div>
                                    <InputLabel for="tanggal_terima" value="Tanggal Terima" />
                                    <TextInput
                                        id="tanggal_terima"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="form.tanggal_terima"
                                    />
                                    <InputError class="mt-2" :message="form.errors.tanggal_terima" />
                                </div>

                                <div>
                                    <InputLabel for="paraf" value="Paraf" />
                                    <TextInput
                                        id="paraf"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.paraf"
                                        placeholder="Masukkan paraf"
                                    />
                                    <InputError class="mt-2" :message="form.errors.paraf" />
                                </div>

                                <div>
                                    <InputLabel for="kontrak_yll" value="Kontrak YLL" />
                                    <select
                                        id="kontrak_yll"
                                        v-model="form.kontrak_yll"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Pilih status kontrak</option>
                                        <option value="REALISASI S/D">REALISASI S/D</option>
                                        <option value="DISETUJUI/TIDAK">DISETUJUI/TIDAK</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.kontrak_yll" />
                                </div>
                            </div>
                        </div>
                        -->

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                            <button
                                type="button"
                                @click="$inertia.visit(route('mitra.purchase-orders.show', purchaseOrder.id))"
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
                                <span>{{ form.processing ? 'Menyimpan...' : 'Update Purchase Order' }}</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </MitraLayout>
</template>