<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
    purchaseOrder: Object,
    mitras: Array,
    jenisKomoditasOptions: {
        type: Array,
        default: () => ['GKP', 'GKG', 'Beras', 'Jagung', 'Gula Pasir', 'Minyak Goreng', 'Lain-lain']
    },
    komplekPergudanganOptions: {
        type: Array,
        default: () => ['Karanganom', 'Krikilan', 'Ngabeyan', 'Banaran', 'Telukan', 'Triyagan', 'Gedong', 'Meger', 'Duyungan', 'Virtual', 'Custom']
    }
});

// Initialize form dengan data dari items
const initialItems = (props.purchaseOrder.items && props.purchaseOrder.items.length > 0) 
    ? props.purchaseOrder.items.map(item => ({
        id: item.id,
        harga: item.harga ? item.harga.toString() : '',
        kuantum: item.kuantum ? parseFloat(item.kuantum).toString() : '',
        komplek_pergudangan: item.komplek_pergudangan || '',
        komplek_pergudangan_custom: item.komplek_pergudangan_custom || '',
        kualitas: item.kualitas || '',
        kualitas_custom: item.kualitas_custom || '',
    }))
    : [{
        harga: '',
        kuantum: '',
        komplek_pergudangan: '',
        komplek_pergudangan_custom: '',
        kualitas: '',
        kualitas_custom: '',
    }];

const form = useForm({
    nama_perusahaan: props.purchaseOrder.nama_perusahaan || '',
    jenis_komoditas: props.purchaseOrder.jenis_komoditas || '',
    jenis_komoditas_custom: props.purchaseOrder.jenis_komoditas_custom || '',
    jenis_pengadaan: props.purchaseOrder.jenis_pengadaan || '',
    tanggal_pembuatan: props.purchaseOrder.tanggal_pembuatan || '',
    agenda_no: props.purchaseOrder.agenda_no || '',
    tanggal_terima: props.purchaseOrder.tanggal_terima || '',
    paraf: props.purchaseOrder.paraf || '',
    kontrak_yll: props.purchaseOrder.kontrak_yll || '',
    kualitas_items: initialItems
});

const kualitasOptions = ref([]);
const showJenisKomoditasCustom = computed(() => form.jenis_komoditas === 'Lain-lain');

// Computed untuk total keseluruhan
const totalNilai = computed(() => {
    return form.kualitas_items.reduce((total, item) => {
        const harga = parseFloat(item.harga) || 0;
        const kuantum = parseFloat(item.kuantum) || 0;
        return total + (harga * kuantum);
    }, 0);
});

// Format angka dengan separator ribuan
const formatNumber = (value) => {
    if (!value) return '';
    const number = value.toString().replace(/\D/g, '');
    return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

// Handle input harga untuk setiap item
const handleHargaInput = (event, index) => {
    const value = event.target.value.replace(/\./g, '');
    form.kualitas_items[index].harga = value;
    event.target.value = formatNumber(value);
};

// Handle input kuantum untuk setiap item
const handleKuantumInput = (event, index) => {
    const value = event.target.value.replace(/\./g, '');
    form.kualitas_items[index].kuantum = value;
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
            break;
        case 'Jagung':
            kualitasOptions.value = ['Jagung', 'Lain-lain'];
            break;
        case 'Gula Pasir':
            kualitasOptions.value = ['Gula Pasir', 'Lain-lain'];
            break;
        case 'Minyak Goreng':
            kualitasOptions.value = ['Minyak Goreng', 'Lain-lain'];
            break;
        case 'Lain-lain':
            kualitasOptions.value = ['Lain-lain'];
            break;
        default:
            kualitasOptions.value = ['Lain-lain'];
            break;
    }
};

// Functions untuk mengelola items
const addKualitasItem = () => {
    form.kualitas_items.push({
        harga: '',
        kuantum: '',
        komplek_pergudangan: '',
        komplek_pergudangan_custom: '',
        kualitas: '',
        kualitas_custom: '',
    });
};

const removeKualitasItem = (index) => {
    if (form.kualitas_items.length > 1) {
        form.kualitas_items.splice(index, 1);
    }
};

// Computed untuk show custom fields per item
const showKomplekPergudanganCustom = (index) => {
    return form.kualitas_items[index]?.komplek_pergudangan === 'Custom';
};

const showKualitasCustom = (index) => {
    return form.kualitas_items[index]?.kualitas === 'Lain-lain';
};

// Watch perubahan jenis komoditas untuk update kualitas
watch(() => form.jenis_komoditas, (newValue) => {
    updateKualitasOptions(newValue);
    
    // Reset kualitas untuk semua items jika jenis komoditas berubah
    if (newValue !== props.purchaseOrder.jenis_komoditas) {
        form.kualitas_items.forEach(item => {
            item.kualitas = '';
            item.kualitas_custom = '';
        });
    }
});

// Initialize kualitas options saat component mounted
onMounted(() => {
    updateKualitasOptions(form.jenis_komoditas);
});


const submit = () => {
    form.put(route('super-admin.purchase-orders.update', props.purchaseOrder.id));
};
</script>

<template>
    <Head title="Edit Purchase Order - ASIMPENAS" />

    <SuperAdminLayout>
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-4">
                            <button 
                                @click="$inertia.visit(route('super-admin.purchase-orders.show', purchaseOrder.id))"
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
                            <select
                                id="nama_perusahaan"
                                v-model="form.nama_perusahaan"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="">Pilih Nama Perusahaan</option>
                                <option v-for="mitra in mitras" :key="mitra.id" :value="mitra.nama_perusahaan">
                                    {{ mitra.nama_perusahaan }}
                                </option>
                            </select>
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

                        <!-- Item Kualitas (Multi-item) -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Detail Item Purchase Order</h3>
                                <button
                                    type="button"
                                    @click="addKualitasItem"
                                    class="px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    + Tambah Item
                                </button>
                            </div>

                            <div v-for="(item, index) in form.kualitas_items" :key="index" class="mb-6 p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-md font-medium text-gray-800">Item {{ index + 1 }}</h4>
                                    <button
                                        v-if="form.kualitas_items.length > 1"
                                        type="button"
                                        @click="removeKualitasItem(index)"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Hapus Item
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <!-- Harga -->
                                    <div>
                                        <InputLabel :for="`harga_${index}`" value="Harga (Rp/Kg)" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input
                                                :id="`harga_${index}`"
                                                type="text"
                                                @input="handleHargaInput($event, index)"
                                                :value="formatNumber(item.harga)"
                                                class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="0"
                                                required
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.harga`]" />
                                    </div>

                                    <!-- Kuantum -->
                                    <div>
                                        <InputLabel :for="`kuantum_${index}`" value="Kuantum (Kg)" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input
                                                :id="`kuantum_${index}`"
                                                type="text"
                                                @input="handleKuantumInput($event, index)"
                                                :value="formatNumber(item.kuantum)"
                                                class="block w-full pr-12 py-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="0"
                                                required
                                            />
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Kg</span>
                                            </div>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.kuantum`]" />
                                    </div>
                                </div>

                                <!-- Nilai per item (readonly) -->
                                <div class="mb-4">
                                    <InputLabel value="Nilai Item" />
                                    <div class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 font-medium">
                                        Rp {{ ((parseFloat(item.harga) || 0) * (parseFloat(item.kuantum) || 0)).toLocaleString('id-ID') }}
                                    </div>
                                </div>

                                <!-- Komplek Pergudangan -->
                                <div class="mb-4">
                                    <InputLabel :for="`komplek_pergudangan_${index}`" value="Komplek Pergudangan" />
                                    <select
                                        :id="`komplek_pergudangan_${index}`"
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
                                    <div v-if="showKomplekPergudanganCustom(index)" class="mt-3">
                                        <InputLabel :for="`komplek_pergudangan_custom_${index}`" value="Nama Komplek Pergudangan" />
                                        <TextInput
                                            :id="`komplek_pergudangan_custom_${index}`"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="item.komplek_pergudangan_custom"
                                            placeholder="Masukkan nama komplek pergudangan"
                                        />
                                        <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.komplek_pergudangan_custom`]" />
                                    </div>
                                </div>

                                <!-- Kualitas -->
                                <div>
                                    <InputLabel :for="`kualitas_${index}`" value="Kualitas" />
                                    <select
                                        :id="`kualitas_${index}`"
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
                                    <div v-if="showKualitasCustom(index)" class="mt-3">
                                        <InputLabel :for="`kualitas_custom_${index}`" value="Spesifikasi Kualitas" />
                                        <TextInput
                                            :id="`kualitas_custom_${index}`"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="item.kualitas_custom"
                                            placeholder="Masukkan spesifikasi kualitas lainnya"
                                        />
                                        <InputError class="mt-2" :message="form.errors[`kualitas_items.${index}.kualitas_custom`]" />
                                    </div>
                                </div>
                            </div>

                            <!-- Total Keseluruhan -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-right">
                                    <span class="text-lg font-semibold text-gray-900">
                                        Total Keseluruhan: Rp {{ totalNilai.toLocaleString('id-ID') }}
                                    </span>
                                </div>
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
                                @click="$inertia.visit(route('super-admin.purchase-orders.show', purchaseOrder.id))"
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
    </SuperAdminLayout>
</template>