<script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        form: {
          nama_perusahaan: "",
          badan_hukum_usaha: "",
          alamat_perusahaan: "",
          email: "",
          kode_mitra: "",
        },
        successMessage: "",
        errorMessage: "",
      };
    },
    methods: {
      async submitForm() {
        try {
          const response = await axios.post("/data-mitra", this.form);
          this.successMessage = "Data mitra berhasil ditambahkan!";
          this.errorMessage = "";
          this.form = {
            nama_perusahaan: "",
            badan_hukum_usaha: "",
            alamat_perusahaan: "",
            email: "",
            kode_mitra: "",
          };
        } catch (error) {
          this.successMessage = "";
          this.errorMessage =
            error.response?.data?.message || "Terjadi kesalahan saat menambah data.";
        }
      },
    },
  };
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 max-w-2xl mx-auto p-8">
      <div class="mb-8">
        <h1 class="text-lg font-semibold mb-2">Tambah Data Mitra</h1>
        <p class="text-gray-600">Lengkapi informasi perusahaan mitra</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <div class="space-y-2">
          <label for="nama_perusahaan" class="block text-sm font-medium">Nama Perusahaan</label>
          <input
            type="text"
            id="nama_perusahaan"
            v-model="form.nama_perusahaan"
            required
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
            placeholder="Masukkan nama perusahaan"
          />
        </div>
        <div class="space-y-2">
          <label for="badan_hukum_usaha" class="block text-sm font-medium">Badan Hukum Usaha</label>
          <input
            type="text"
            id="badan_hukum_usaha"
            v-model="form.badan_hukum_usaha"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
            placeholder="Contoh: PT, CV, UD, dll"
          />
        </div>
        <div class="space-y-2">
          <label for="alamat_perusahaan" class="block text-sm font-medium">Alamat Perusahaan</label>
          <input
            type="text"
            id="alamat_perusahaan"
            v-model="form.alamat_perusahaan"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
            placeholder="Masukkan alamat perusahaan"
          />
        </div>
        <div class="space-y-2">
          <label for="email" class="block text-sm font-medium">Email</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
            placeholder="Masukkan email"
          />
        </div>
        <div class="space-y-2">
          <label for="kode_mitra" class="block text-sm font-medium">Kode Mitra</label>
          <input
            type="text"
            id="kode_mitra"
            v-model="form.kode_mitra"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
            placeholder="Masukkan kode mitra"
          />
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 ml-auto block">Tambah Mitra</button>
      </form>
  
      <div v-if="successMessage" class="success-message">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>
    </div>
  </template>
  
  <style>
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