<template>
    <div>
      <h1>Tambah Data Mitra</h1>
      <form @submit.prevent="submitForm">
        <div>
          <label for="nama_perusahaan">Nama Perusahaan</label>
          <input
            type="text"
            id="nama_perusahaan"
            v-model="form.nama_perusahaan"
            required
          />
        </div>
        <div>
          <label for="badan_hukum_usaha">Badan Hukum Usaha</label>
          <input
            type="text"
            id="badan_hukum_usaha"
            v-model="form.badan_hukum_usaha"
          />
        </div>
        <div>
          <label for="alamat_perusahaan">Alamat Perusahaan</label>
          <input
            type="text"
            id="alamat_perusahaan"
            v-model="form.alamat_perusahaan"
          />
        </div>
        <div>
          <label for="email">Email</label>
          <input type="email" id="email" v-model="form.email" />
        </div>
        <div>
          <label for="kode_mitra">Kode Mitra</label>
          <input type="text" id="kode_mitra" v-model="form.kode_mitra" />
        </div>
        <button type="submit">Tambah Mitra</button>
      </form>
  
      <div v-if="successMessage" class="success-message">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>
    </div>
  </template>
  
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