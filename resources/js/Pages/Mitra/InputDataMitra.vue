<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
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
  jabatan: "",
  nik:"",
  tempat_lahir: "",
  tanggal_lahir: "",
  no_telp_perusahaan: "",
  no_telp_cp: "",
  bank_korespondensi: "",
  alamat_bank: "",
  no_rekening: "",
  nama_pemilik_rekening: "",
  status_perusahaan: "",
  npwp:"",
  pkp:"",
  surat_kuasa:"",
  nama_yang_dikuasakan:"",
  nik_yang_dikuasakan:"",
  alamat_yang_dikuasakan:"",
  email: "",
  no_vms:"",
  kode_mitra: "",
  tanggal_surat_permohonan:"",
  tanggal_pakta_integritas:"",
});

const user = ref(null)
const successMessage = ref("");
const errorMessage = ref("");
const isSubmitting = ref(false);
const mitraId = ref(null);
const samaDenganCP = ref(false);

// Surat Kuasa modal states
const showSuratKuasaModal = ref(false);
const suratKuasaForm = ref({
  tempat: '',
  tanggal: new Date().toISOString().split('T')[0] // Default to today
});

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
    'jabatan',
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
    'email',
    // 'no_vms',
    // 'kode_mitra'
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
    { key: 'jabatan', label: 'Jabatan' },
    { key: 'nik', label: 'NIK' },
    { key: 'tempat_lahir', label: 'Tempat Lahir' },
    { key: 'tanggal_lahir', label: 'Tanggal Lahir' },
    { key: 'no_telp_perusahaan', label: 'No Telp Perusahaan' },
    { key: 'no_telp_cp', label: 'No Telp Contact Person' },
    { key: 'bank_korespondensi', label: 'Bank Korespondensi' },
    { key: 'alamat_bank', label: 'Alamat Bank' },
    { key: 'no_rekening', label: 'No Rekening' },
    { key: 'nama_pemilik_rekening', label: 'Nama Pemilik Rekening' },
    { key: 'status_perusahaan', label: 'Status Perusahaan' },
    { key: 'npwp', label: 'NPWP' },
    { key: 'pkp', label: 'PKP' },
    { key: 'email', label: 'Email' },
    // { key: 'no_vms', label: 'No VMS' },
    // { key: 'kode_mitra', label: 'Kode Mitra' }
  ];

  return requiredFields.filter(field => {
    const value = form.value[field.key];
    return value === null || value === undefined || value.toString().trim() === '';
  });
};

const handleUppercase = (field, event) => {
  form.value[field] = event.target.value.toUpperCase();
};

onMounted(async () => {
  try {
    const response = await axios.get('/user')
    user.value = response.data

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
    const isUpdate = !!mitraId.value;
    
    if (isUpdate) {
      // update (PUT)
      response = await axios.put(`/data-mitra/${mitraId.value}`, form.value);
    } else {
      // create (POST)
      response = await axios.post('/data-mitra', form.value);
      // Set mitraId dari response setelah POST berhasil
      if (response.data && response.data.id_mitra) {
        mitraId.value = response.data.id_mitra;
      }
    }
    
    successMessage.value = isUpdate ? "Data mitra berhasil diperbarui!" : "Data mitra berhasil ditambahkan!";
    errorMessage.value = "";
    
    // Tampilkan notifikasi success
    showNotification(
      'success',
      'Berhasil!',
      isUpdate ? 'Data mitra berhasil diperbarui dan disimpan ke sistem.' : 'Data mitra berhasil ditambahkan dan disimpan ke sistem. Anda dapat mengunduh dokumen sekarang.'
    );

    // Jika create, tidak perlu reload - button sudah bisa digunakan
    // Jika update, reload setelah delay
    if (isUpdate) {
      setTimeout(() => {
        router.reload();
      }, 800);
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

const generateSuratPermohonanPdf = async () => {
    if (!mitraId.value) {
        errorMessage.value = "Silakan simpan data mitra terlebih dahulu sebelum generate PDF";
        return;
    }

    isSubmitting.value = true;
    try {
        const response = await axios.get(`/mitra/data-mitra/${mitraId.value}/surat-permohonan`, {
            responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Surat-Permohonan-MPP-${form.value.nama_perusahaan}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        showNotification('success', 'Berhasil!', 'PDF Surat Permohonan berhasil diunduh');
    } catch (error) {
        console.error('Error generating PDF:', error);
        showNotification('error', 'Gagal!', 'Terjadi kesalahan saat generate PDF');
    } finally {
        isSubmitting.value = false;
    }
};

const generateSuratPernyataanNonPkpPdf = async () => {
    if (!mitraId.value) {
        errorMessage.value = "Silakan simpan data mitra terlebih dahulu sebelum generate PDF";
        return;
    }

    isSubmitting.value = true;
    try {
        const response = await axios.get(`/mitra/data-mitra/${mitraId.value}/surat-pernyataan-non-pkp`, {
            responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Surat-Pernyataan-Non-PKP-${form.value.nama_perusahaan}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        showNotification('success', 'Berhasil!', 'PDF Surat Pernyataan Non PKP berhasil diunduh');
    } catch (error) {
        console.error('Error generating PDF:', error);
        showNotification('error', 'Gagal!', 'Terjadi kesalahan saat generate PDF');
    } finally {
        isSubmitting.value = false;
    }
};

const openSuratKuasaModal = () => {
    if (!mitraId.value) {
        errorMessage.value = "Silakan simpan data mitra terlebih dahulu sebelum generate PDF";
        return;
    }
    
    // Reset form with today's date
    suratKuasaForm.value = {
        tempat: '',
        tanggal: new Date().toISOString().split('T')[0]
    };
    
    showSuratKuasaModal.value = true;
};

const generateSuratKuasaPdf = async () => {
    if (!suratKuasaForm.value.tempat || !suratKuasaForm.value.tanggal) {
        showNotification('error', 'Gagal!', 'Mohon lengkapi tempat dan tanggal');
        return;
    }

    isSubmitting.value = true;
    try {
        const response = await axios.post(
            `/mitra/data-mitra/${mitraId.value}/surat-kuasa`,
            suratKuasaForm.value,
            { responseType: 'blob' }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Surat-Kuasa-${form.value.nama_perusahaan}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        showSuratKuasaModal.value = false;
        showNotification('success', 'Berhasil!', 'PDF Surat Kuasa berhasil diunduh');
    } catch (error) {
        console.error('Error generating Surat Kuasa PDF:', error);
        showNotification('error', 'Gagal!', 'Terjadi kesalahan saat generate PDF');
    } finally {
        isSubmitting.value = false;
    }
};

const generatePaktaIntegritasPdf = async () => {
    if (!mitraId.value) {
        errorMessage.value = "Silakan simpan data mitra terlebih dahulu sebelum generate PDF";
        return;
    }

    isSubmitting.value = true;
    try {
        const response = await axios.get(`/mitra/data-mitra/${mitraId.value}/pakta-integritas`, {
            responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Pakta-Integritas-${form.value.nama_perusahaan}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        showNotification('success', 'Berhasil!', 'PDF Pakta Integritas berhasil diunduh');
    } catch (error) {
        console.error('Error generating PDF:', error);
        showNotification('error', 'Gagal!', 'Terjadi kesalahan saat generate PDF');
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase" 
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.status_perusahaan === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
              >
                <option value="">Pilih Status Perusahaan</option>
                <option value="Penggilingan Padi">Penggilingan Padi</option>
                <option value="Poktan/Gapoktan">Poktan/Gapoktan</option>
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.nama_cp.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan nama contact person"
              />
            </div>

            <!-- Jabatan -->
            <div class="space-y-2">
              <label for="jabatan" class="block text-sm font-medium">
                Jabatan <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="jabatan"
                v-model="form.jabatan"
                class="uppercase"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.jabatan.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Contoh: Direktur, Manajer, dll"
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
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
                class="uppercase"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  form.no_rekening.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Masukkan nomor rekening"
              />
            </div>

            <!-- Nama Pemilik Rekening -->
            <div class="space-y-2">
              <label for="nama_pemilik_rekening" class="block text-sm font-medium">
                Nama Pemilik Rekening <span class="text-red-500">*</span>
              </label>
              <div class="space-y-2">
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    id="sama_dengan_cp"
                    v-model="samaDenganCP"
                    @change="(e) => { if (e.target.checked) form.nama_pemilik_rekening = form.nama_cp; else form.nama_pemilik_rekening = ''; }"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label for="sama_dengan_cp" class="ml-2 text-sm text-gray-600">
                    Sama dengan nama Contact Person
                  </label>
                </div>
                <input
                  type="text"
                  id="nama_pemilik_rekening"
                  v-model="form.nama_pemilik_rekening"
                  :readonly="samaDenganCP"
                  class="uppercase"
                  :class="[
                    'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none text-sm transition-colors',
                    samaDenganCP ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-blue-500 focus:border-blue-500',
                    form.nama_pemilik_rekening.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                  placeholder="Masukkan nama pemilik rekening"
                />
              </div>
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
                class="uppercase"
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
                class="uppercase"
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
                    value="Pkp"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">PKP</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.pkp"
                    value="Non Pkp"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Non-PKP</span>
                </label>
              </div>
              <div v-if="form.pkp === ''" class="text-xs text-red-500">Pilih salah satu opsi</div>
            </div>
          </div>
        </div>

        <!-- Section 5: Data Kuasa -->
        <div class="border-b border-gray-200 pb-6">
          <h3 class="text-md font-medium text-gray-900 mb-4">Data Kuasa</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Surat Kuasa -->
            <div class="space-y-2 md:col-span-2">
              <label class="block text-sm font-medium">
                Apakah Ada Surat Kuasa?
              </label>
              <div class="flex space-x-4">
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.surat_kuasa"
                    value="Ada"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Ada</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.surat_kuasa"
                    value="Tidak Ada"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Tidak Ada</span>
                </label>
              </div>
            </div>

            <!-- Fields yang muncul jika surat kuasa Ada -->
            <template v-if="form.surat_kuasa === 'Ada'">
              <!-- Nama Yang Dikuasakan -->
              <div class="space-y-2">
                <label for="nama_yang_dikuasakan" class="block text-sm font-medium">
                  Nama Yang Dikuasakan
                </label>
                <input
                  type="text"
                  id="nama_yang_dikuasakan"
                  v-model="form.nama_yang_dikuasakan"
                  class="uppercase"
                  :class="[
                    'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                    form.nama_yang_dikuasakan.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                  placeholder="Masukkan nama yang dikuasakan"
                />
              </div>

              <!-- NIK Yang Dikuasakan -->
              <div class="space-y-2">
                <label for="nik_yang_dikuasakan" class="block text-sm font-medium">
                  NIK Yang Dikuasakan
                </label>
                <input
                  type="text"
                  id="nik_yang_dikuasakan"
                  v-model="form.nik_yang_dikuasakan"
                  class="uppercase"
                  maxlength="16"
                  :class="[
                    'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                    form.nik_yang_dikuasakan.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                  placeholder="Masukkan NIK (16 digit)"
                />
              </div>

              <!-- Alamat Yang Dikuasakan -->
              <div class="space-y-2 md:col-span-2">
                <label for="alamat_yang_dikuasakan" class="block text-sm font-medium">
                  Alamat Yang Dikuasakan
                </label>
                <textarea
                  id="alamat_yang_dikuasakan"
                  v-model="form.alamat_yang_dikuasakan"
                  rows="3"
                  class="uppercase"
                  :class="[
                    'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors resize-none',
                    form.alamat_yang_dikuasakan.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                  placeholder="Masukkan alamat lengkap yang dikuasakan"
                ></textarea>
              </div>
            </template>
          </div>
        </div>

        <!-- Section 6: Tracking & Timeline -->
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
                No VMS 
              </label>
              <input
                type="text"
                id="no_vms"
                readonly
                v-model="form.no_vms"
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  // form.no_vms.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300'
                ]"
                placeholder="Diinputkan Oleh Admin"
              />
            </div>

            <!-- Kode Mitra -->
            <div class="space-y-2">
              <label for="kode_mitra" class="block text-sm font-medium">
                Kode Mitra
              </label>
              <input
                type="text"
                id="kode_mitra"
                v-model="form.kode_mitra"
                readonly
                :class="[
                  'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors',
                  // form.kode_mitra.trim() === '' ? 'border-red-300 bg-red-50' : 'border-gray-300' 
                ]"
                placeholder="Diinput Oleh Admin"
              />
            </div>
          </div>
        </div>
        
        <!-- Submit Button -->
        <h3 class="text-md font-medium text-gray-900 mb-4">Download Dokumen yang Dibutuhkan</h3>
        
        <div class="space-y-4">
          <!-- Document Download Buttons -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <button 
                type="button" 
                @click="generateSuratPermohonanPdf"
                :disabled="!mitraId || isSubmitting"
                :class="[
                  'w-full px-4 py-3 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors',
                  mitraId && !isSubmitting
                    ? 'bg-green-600 text-white hover:bg-green-700 cursor-pointer'
                    : 'bg-gray-400 text-gray-200 cursor-not-allowed'
                ]"
            >
                <span v-if="isSubmitting">Generating...</span>
                <span v-else-if="!mitraId">Simpan Data Dulu</span>
                <span v-else>Surat Permohonan MPP</span>
            </button>

            <button 
                v-if="form.pkp === 'Non Pkp'"
                type="button" 
                @click="generateSuratPernyataanNonPkpPdf"
                :disabled="!mitraId || isSubmitting"
                :class="[
                  'w-full px-4 py-3 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors',
                  mitraId && !isSubmitting
                    ? 'bg-purple-600 text-white hover:bg-purple-700 cursor-pointer'
                    : 'bg-gray-400 text-gray-200 cursor-not-allowed'
                ]"
            >
                <span v-if="isSubmitting">Generating...</span>
                <span v-else-if="!mitraId">Simpan Data Dulu</span>
                <span v-else>Surat Non PKP</span>
            </button>

            <button 
                type="button" 
                @click="generatePaktaIntegritasPdf"
                :disabled="!mitraId || isSubmitting"
                :class="[
                  'w-full px-4 py-3 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors',
                  mitraId && !isSubmitting
                    ? 'bg-indigo-600 text-white hover:bg-indigo-700 cursor-pointer'
                    : 'bg-gray-400 text-gray-200 cursor-not-allowed'
                ]"
            >
                <span v-if="isSubmitting">Generating...</span>
                <span v-else-if="!mitraId">Simpan Data Dulu</span>
                <span v-else>Pakta Integritas</span>
            </button>

            <button 
                v-if="form.surat_kuasa === 'Ada'"
                type="button" 
                @click="openSuratKuasaModal"
                class="w-full px-4 py-3 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors bg-orange-600 text-white hover:bg-orange-700 cursor-pointer"
            >
                <span class="flex items-center justify-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Generate Surat Kuasa
                </span>
            </button>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end pt-2">
            <button 
              type="submit" 
              :disabled="!isFormValid || isSubmitting"
              :class="[
                'w-full sm:w-auto px-8 py-3 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors',
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

    <!-- Modal Generate Surat Kuasa -->
    <div v-if="showSuratKuasaModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showSuratKuasaModal = false"></div>

        <!-- Modal panel -->
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-orange-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                  Generate Surat Kuasa
                </h3>
                <div class="mt-4 space-y-4">
                  <!-- Tempat Input -->
                  <div>
                    <label for="tempat" class="block text-sm font-medium text-gray-700">
                      Tempat <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="tempat"
                      v-model="suratKuasaForm.tempat"
                      type="text"
                      placeholder="Contoh: Surakarta"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2 border"
                      required
                    />
                  </div>

                  <!-- Tanggal Input -->
                  <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">
                      Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="tanggal"
                      v-model="suratKuasaForm.tanggal"
                      type="date"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2 border"
                      required
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Modal footer -->
          <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="generateSuratKuasaPdf"
              :disabled="isSubmitting"
              class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isSubmitting" class="flex items-center">
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generating...
              </span>
              <span v-else>Generate PDF</span>
            </button>
            <button
              type="button"
              @click="showSuratKuasaModal = false"
              :disabled="isSubmitting"
              class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Batal
            </button>
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