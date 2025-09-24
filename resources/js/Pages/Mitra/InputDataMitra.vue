<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import axios from 'axios';

// Reactive states untuk form dan pesan
const form = ref({
  nama_perusahaan: "",
  badan_hukum_usaha: "",
  alamat_perusahaan: "",
  kota_kabupaten: "",
  provinsi: "",
  nama_cp: "",
  nik:"",
  tempat_lahir: "",
  tanggal_lahir: "",
  no_telp_perusahaan: "",
  no_telp_cp: "",
  bank_korespondensi: "",
  alamat_bank: "",
  no_rekening: "",
  status_perusahaan: "",
  npwp:"",
  pkp:"",
  surat_kuasa:"",
  tanggal_surat_permohonan:"", // opsional
  tanggal_pakta_integritas:"", // opsional
  email: "",
  no_vms:"",
  kode_mitra: "",
});

const user = ref(null)
const successMessage = ref("");
const errorMessage = ref("");
const isSubmitting = ref(false);
const mitraId = ref(null);

// Notification states
const notification = ref({
  show: false,
  type: '',
  title: '',
  message: ''
});

// Function untuk menampilkan notifikasi
const showNotification = (type, title, message) => {
  notification.value = {
    show: true,
    type,
    title,
    message
  };
  
  // Auto hide notification setelah 5 detik
  setTimeout(() => {
    hideNotification();
  }, 5000);
};

// Function untuk menyembunyikan notifikasi
const hideNotification = () => {
  notification.value.show = false;
};

// Function untuk mendapatkan icon notifikasi
const getNotificationIcon = (type) => {
  switch(type) {
    case 'success':
      return 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z';
    case 'error':
      return 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z';
    default:
      return '';
  }
};

// Computed untuk validasi form
const isFormValid = computed(() => {
  // Field yang wajib diisi (tidak termasuk tanggal_surat_permohonan dan tanggal_pakta_integritas)
  const requiredFields = [
    'nama_perusahaan',
    'badan_hukum_usaha', 
    'alamat_perusahaan',
    'kota_kabupaten',
    'provinsi',
    'nama_cp',
    'nik',
    'tempat_lahir',
    'tanggal_lahir',
    'no_telp_perusahaan',
    'no_telp_cp',
    'bank_korespondensi',
    'alamat_bank',
    'no_rekening',
    'status_perusahaan',
    'npwp',
    'pkp',
    'surat_kuasa',
    'email',
    'no_vms',
    'kode_mitra'
  ];

  // Cek apakah semua field wajib sudah terisi
  return requiredFields.every(field => {
    const value = form.value[field];
    return value !== null && value !== undefined && value.toString().trim() !== '';
  });
});

// Function untuk mengecek field kosong
const getEmptyFields = () => {
  const requiredFields = [
    { key: 'nama_perusahaan', label: 'Nama Perusahaan' },
    { key: 'badan_hukum_usaha', label: 'Badan Hukum Usaha' },
    { key: 'alamat_perusahaan', label: 'Alamat Perusahaan' },
    { key: 'kota_kabupaten', label: 'Kota/Kabupaten' },
    { key: 'provinsi', label: 'Provinsi' },
    { key: 'nama_cp', label: 'Nama Contact Person' },
    { key: 'nik', label: 'NIK' },
    { key: 'tempat_lahir', label: 'Tempat Lahir' },
    { key: 'tanggal_lahir', label: 'Tanggal Lahir' },
    { key: 'no_telp_perusahaan', label: 'No Telp Perusahaan' },
    { key: 'no_telp_cp', label: 'No Telp Contact Person' },
    { key: 'bank_korespondensi', label: 'Bank Korespondensi' },
    { key: 'alamat_bank', label: 'Alamat Bank' },
    { key: 'no_rekening', label: 'No Rekening' },
    { key: 'status_perusahaan', label: 'Status Perusahaan' },
    { key: 'npwp', label: 'NPWP' },
    { key: 'pkp', label: 'PKP' },
    { key: 'surat_kuasa', label: 'Surat Kuasa' },
    { key: 'email', label: 'Email' },
    { key: 'no_vms', label: 'No VMS' },
    { key: 'kode_mitra', label: 'Kode Mitra' }
  ];

  return requiredFields.filter(field => {
    const value = form.value[field.key];
    return value === null || value === undefined || value.toString().trim() === '';
  });
};

onMounted(async () => {
  try {
    const response = await axios.get('/user')
    user.value = response.data
    form.value.nama_perusahaan = user.value.name // sesuaikan field dari user
    form.value.email = user.value.email

    const mitraRes = await axios.get('/data-mitra/my');
    if (mitraRes.data && mitraRes.data.id_mitra) {
      mitraId.value = mitraRes.data.id_mitra;
      Object.keys(form.value).forEach(key => {
        if (mitraRes.data[key] !== undefined) form.value[key] = mitraRes.data[key] ?? "";
      });
    }
  } catch (error) {
    console.error('Gagal mengambil data user:', error)
  }
})

// Function untuk submit form
const submitForm = async () => {
  // Validasi sebelum submit
  if (!isFormValid.value) {
    const emptyFields = getEmptyFields();
    const fieldNames = emptyFields.slice(0, 3).map(f => f.label).join(', ');
    const moreCount = emptyFields.length > 3 ? ` dan ${emptyFields.length - 3} field lainnya` : '';
    
    errorMessage.value = `Mohon lengkapi data berikut: ${fieldNames}${moreCount}`;
    successMessage.value = "";
    return;
  }

  isSubmitting.value = true;
  errorMessage.value = "";

  try {
    let response;
    if (mitraId.value) {
      // update (PUT)
      response = await axios.put(`/data-mitra/${mitraId.value}`, form.value);
    } else {
      // create (POST)
      response = await axios.post('/data-mitra', form.value);
    }
    successMessage.value = "Data mitra berhasil ditambahkan!";
    errorMessage.value = "";
    
    // Tampilkan notifikasi success
    showNotification(
      'success',
      'Berhasil!',
      'Data mitra berhasil ditambahkan dan disimpan ke sistem.'
    );
    
    // Reset form setelah berhasil submit
    Object.keys(form.value).forEach(key => {
      form.value[key] = "";
    });
    
    // Set ulang data user
    if (user.value) {
      form.value.nama_perusahaan = user.value.name;
      form.value.email = user.value.email;
    }
    
  } catch (error) {
    successMessage.value = "";
    errorMessage.value = error.response?.data?.message || "Terjadi kesalahan saat menambah data.";
    
    // Tampilkan notifikasi error
    showNotification(
      'error',
      'Gagal!',
      error.response?.data?.message || "Terjadi kesalahan saat menambah data mitra."
    );
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Head title="Input Data Mitra - ASIMPENAS" />

  <MitraLayout>

    <!-- Notification Toast -->
    <Transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="notification.show"
        class="fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 z-50"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg
                :class="[
                  'h-6 w-6',
                  notification.type === 'success' ? 'text-green-400' : 'text-red-400'
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
            <div class="ml-3 w-0 flex-1 pt-0.5">
              <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
              <p class="mt-1 text-sm text-gray-500">{{ notification.message }}</p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button
                @click="hideNotification"
                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <span class="sr-only">Close</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 max-w-6xl mx-auto p-8">
      <div class="mb-8">
        <h1 class="text-lg font-semibold mb-2">Tambah Data Mitra</h1>
        <p class="text-gray-600">Lengkapi informasi perusahaan mitra</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-8">
        <!-- Section 1: Informasi Perusahaan -->
        <div class="border-b border-gray-200 pb-6">
          <h3 class="text-md font-medium text-gray-900 mb-4">Informasi Perusahaan</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Perusahaan -->
            <div class="space-y-2">
              <label for="nama_perusahaan" class="block text-sm font-medium">
                Nama Perusahaan <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="nama_perusahaan"
                v-model="form.nama_perusahaan"
                required
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.nama_perusahaan.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan nama perusahaan"
              />
            </div>

            <!-- Badan Hukum Usaha -->
            <div class="space-y-2">
              <label for="badan_hukum_usaha" class="block text-sm font-medium">
                Badan Hukum Usaha <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="badan_hukum_usaha"
                v-model="form.badan_hukum_usaha"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.badan_hukum_usaha.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Contoh: PT, CV, UD, dll"
              />
            </div>

            <!-- Status Perusahaan -->
            <div class="space-y-2">
              <label for="status_perusahaan" class="block text-sm font-medium">
                Status Perusahaan <span class="text-red-500">*</span>
              </label>
              <select
                id="status_perusahaan"
                v-model="form.status_perusahaan"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.status_perusahaan === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
              >
                <option value="">Pilih Status Perusahaan</option>
                <option value="penggilingan">Penggilingan</option>
                <option value="distributor">Distributor</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>

            <!-- Email -->
            <div class="space-y-2">
              <label for="email" class="block text-sm font-medium">
                Email <span class="text-red-500">*</span>
              </label>
              <input
                type="email"
                id="email"
                v-model="form.email"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.email.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="contoh@email.com"
              />
            </div>

            <!-- Alamat Perusahaan -->
            <div class="space-y-2 md:col-span-2">
              <label for="alamat_perusahaan" class="block text-sm font-medium">
                Alamat Perusahaan <span class="text-red-500">*</span>
              </label>
              <textarea
                id="alamat_perusahaan"
                v-model="form.alamat_perusahaan"
                rows="2"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors resize-none',
                  form.alamat_perusahaan.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan alamat lengkap perusahaan"
              ></textarea>
            </div>

            <!-- Kota/Kabupaten -->
            <div class="space-y-2">
              <label for="kota_kabupaten" class="block text-sm font-medium">
                Kota/Kabupaten <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="kota_kabupaten"
                v-model="form.kota_kabupaten"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.kota_kabupaten.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan kota/kabupaten"
              />
            </div>

            <!-- Provinsi -->
            <div class="space-y-2">
              <label for="provinsi" class="block text-sm font-medium">
                Provinsi <span class="text-red-500">*</span>
              </label>
              <select
                id="provinsi"
                v-model="form.provinsi"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.provinsi === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
              >
                <option value="">Pilih Provinsi</option>
                <option value="Aceh">Aceh</option>
                <option value="Sumatera Utara">Sumatera Utara</option>
                <option value="Sumatera Barat">Sumatera Barat</option>
                <option value="Riau">Riau</option>
                <option value="Kepulauan Riau">Kepulauan Riau</option>
                <option value="Jambi">Jambi</option>
                <option value="Sumatera Selatan">Sumatera Selatan</option>
                <option value="Kepulauan Bangka Belitung">Kepulauan Bangka Belitung</option>
                <option value="Bengkulu">Bengkulu</option>
                <option value="Lampung">Lampung</option>
                <option value="DKI Jakarta">DKI Jakarta</option>
                <option value="Jawa Barat">Jawa Barat</option>
                <option value="Jawa Tengah">Jawa Tengah</option>
                <option value="DI Yogyakarta">DI Yogyakarta</option>
                <option value="Jawa Timur">Jawa Timur</option>
                <option value="Banten">Banten</option>
                <option value="Bali">Bali</option>
                <option value="Nusa Tenggara Barat">Nusa Tenggara Barat</option>
                <option value="Nusa Tenggara Timur">Nusa Tenggara Timur</option>
                <option value="Kalimantan Barat">Kalimantan Barat</option>
                <option value="Kalimantan Tengah">Kalimantan Tengah</option>
                <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                <option value="Kalimantan Timur">Kalimantan Timur</option>
                <option value="Kalimantan Utara">Kalimantan Utara</option>
                <option value="Sulawesi Utara">Sulawesi Utara</option>
                <option value="Sulawesi Tengah">Sulawesi Tengah</option>
                <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
                <option value="Gorontalo">Gorontalo</option>
                <option value="Sulawesi Barat">Sulawesi Barat</option>
                <option value="Maluku">Maluku</option>
                <option value="Maluku Utara">Maluku Utara</option>
                <option value="Papua">Papua</option>
                <option value="Papua Barat">Papua Barat</option>
                <option value="Papua Selatan">Papua Selatan</option>
                <option value="Papua Tengah">Papua Tengah</option>
                <option value="Papua Pegunungan">Papua Pegunungan</option>
                <option value="Papua Barat Daya">Papua Barat Daya</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Section 2: Contact Person -->
        <div class="border-b border-gray-200 pb-6">
          <h3 class="text-md font-medium text-gray-900 mb-4">Informasi Contact Person</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Contact Person -->
            <div class="space-y-2">
              <label for="nama_cp" class="block text-sm font-medium">
                Nama Contact Person <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="nama_cp"
                v-model="form.nama_cp"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.nama_cp.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan nama contact person"
              />
            </div>

            <!-- NIK -->
            <div class="space-y-2">
              <label for="nik" class="block text-sm font-medium">
                NIK <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="nik"
                v-model="form.nik"
                maxlength="16"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.nik.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan NIK (16 digit)"
              />
            </div>

            <!-- Tempat Lahir -->
            <div class="space-y-2">
              <label for="tempat_lahir" class="block text-sm font-medium">
                Tempat Lahir <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="tempat_lahir"
                v-model="form.tempat_lahir"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.tempat_lahir.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan tempat lahir"
              />
            </div>

            <!-- Tanggal Lahir -->
            <div class="space-y-2">
              <label for="tanggal_lahir" class="block text-sm font-medium">
                Tanggal Lahir <span class="text-red-500">*</span>
              </label>
              <input
                type="date"
                id="tanggal_lahir"
                v-model="form.tanggal_lahir"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.tanggal_lahir === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
              />
            </div>

            <!-- No Telp Perusahaan -->
            <div class="space-y-2">
              <label for="no_telp_perusahaan" class="block text-sm font-medium">
                No Telp Perusahaan <span class="text-red-500">*</span>
              </label>
              <input
                type="tel"
                id="no_telp_perusahaan"
                v-model="form.no_telp_perusahaan"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.no_telp_perusahaan.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Contoh: 021-1234567"
              />
            </div>

            <!-- No Telp Contact Person -->
            <div class="space-y-2">
              <label for="no_telp_cp" class="block text-sm font-medium">
                No Telp Contact Person <span class="text-red-500">*</span>
              </label>
              <input
                type="tel"
                id="no_telp_cp"
                v-model="form.no_telp_cp"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.no_telp_cp.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Contoh: 0812-3456789"
              />
            </div>
          </div>
        </div>

        <!-- Section 3: Informasi Bank -->
        <div class="border-b border-gray-200 pb-6">
          <h3 class="text-md font-medium text-gray-900 mb-4">Informasi Bank</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Bank Korespondensi -->
            <div class="space-y-2">
              <label for="bank_korespondensi" class="block text-sm font-medium">
                Bank Korespondensi <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="bank_korespondensi"
                v-model="form.bank_korespondensi"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.bank_korespondensi.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Contoh: Bank BCA, Bank Mandiri"
              />
            </div>

            <!-- No Rekening -->
            <div class="space-y-2">
              <label for="no_rekening" class="block text-sm font-medium">
                No Rekening <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="no_rekening"
                v-model="form.no_rekening"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.no_rekening.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan nomor rekening"
              />
            </div>

            <!-- Alamat Bank -->
            <div class="space-y-2 md:col-span-2">
              <label for="alamat_bank" class="block text-sm font-medium">
                Alamat Bank <span class="text-red-500">*</span>
              </label>
              <textarea
                id="alamat_bank"
                v-model="form.alamat_bank"
                rows="2"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors resize-none',
                  form.alamat_bank.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan alamat kantor bank"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Section 4: Legal & Pajak -->
        <div class="border-b border-gray-200 pb-6">
          <h3 class="text-md font-medium text-gray-900 mb-4">Informasi Legal & Pajak</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- NPWP -->
            <div class="space-y-2">
              <label for="npwp" class="block text-sm font-medium">
                NPWP <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="npwp"
                v-model="form.npwp"
                maxlength="15"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.npwp.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan NPWP (15 digit)"
              />
            </div>

            <!-- PKP -->
            <div class="space-y-2">
              <label class="block text-sm font-medium">
                PKP (Pengusaha Kena Pajak) <span class="text-red-500">*</span>
              </label>
              <div class="flex space-x-4">
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.pkp"
                    value="pkp"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">PKP</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.pkp"
                    value="non pkp"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Non-PKP</span>
                </label>
              </div>
              <div v-if="form.pkp === ''" class="text-xs text-red-500">Pilih salah satu opsi</div>
            </div>

            <!-- Surat Kuasa -->
            <div class="space-y-2">
              <label class="block text-sm font-medium">
                Surat Kuasa <span class="text-red-500">*</span>
              </label>
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
                    value="tidak ada"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                </label>
              </div>
              <div v-if="form.surat_kuasa === ''" class="text-xs text-red-500">Pilih salah satu opsi</div>
            </div>
          </div>
        </div>

        <!-- Section 5: Tracking & Timeline -->
        <div class="pb-6">
          <h3 class="text-md font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tanggal Surat Permohonan (OPSIONAL) -->
            <div class="space-y-2">
              <label for="tanggal_surat_permohonan" class="block text-sm font-medium">
                Tanggal Surat Permohonan <span class="text-gray-400">(Opsional)</span>
              </label>
              <input
                type="date"
                id="tanggal_surat_permohonan"
                v-model="form.tanggal_surat_permohonan"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
              />
            </div>

            <!-- Tanggal Pakta Integritas (OPSIONAL) -->
            <div class="space-y-2">
              <label for="tanggal_pakta_integritas" class="block text-sm font-medium">
                Tanggal Pakta Integritas <span class="text-gray-400">(Opsional)</span>
              </label>
              <input
                type="date"
                id="tanggal_pakta_integritas"
                v-model="form.tanggal_pakta_integritas"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
              />
            </div>

            <!-- No VMS -->
            <div class="space-y-2">
              <label for="no_vms" class="block text-sm font-medium">
                No VMS <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="no_vms"
                v-model="form.no_vms"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.no_vms.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan nomor VMS"
              />
            </div>

            <!-- Kode Mitra -->
            <div class="space-y-2">
              <label for="kode_mitra" class="block text-sm font-medium">
                Kode Mitra <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="kode_mitra"
                v-model="form.kode_mitra"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.kode_mitra.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan kode mitra"
              />
            </div>
          </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end pt-6 border-t border-gray-200">
          <button 
            type="submit" 
            :disabled="!isFormValid || isSubmitting"
            :class="[
              'px-8 py-3 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors',
              isFormValid && !isSubmitting
                ? 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer'
                : 'bg-gray-400 text-gray-200 cursor-not-allowed'
            ]"
          >
            <span v-if="isSubmitting">Mengirim...</span>
            <span v-else-if="!isFormValid">Lengkapi Data</span>
            <span v-else>Simpan Data</span>
          </button>
        </div>
      </form>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="mt-6 bg-green-50 border border-green-200 rounded-md p-4">
        <div class="flex">
          <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
          </div>
        </div>
      </div>

      <div v-if="errorMessage" class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
        <div class="flex">
          <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <div class="ml-3">
            <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
          </div>
        </div>
      </div>
    </div>
  </MitraLayout>
</template>

<style scoped>
.success-message {
  color: green;
  margin-top: 10px;
}
.error-message {
  color: red;
  margin-top: 10px;
}
form div {
  margin-bottom: 10px;
}
</style>