<template>
    <div>
      <h2>Daftar Data Mitra</h2>
      <ul>
        <li v-for="mitra in mitras" :key="mitra.id_mitra">
          {{ mitra.nama_perusahaan }} - {{ mitra.email }} - {{ mitra.no_telp_cp }} - {{ mitra.badan_hukum_usaha }}
        </li>
      </ul>
      <div v-if="loading">Loading...</div>
      <div v-if="error">{{ error }}</div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        mitras: [],
        loading: false,
        error: null,
      };
    },
    mounted() {
      this.fetchDataMitra();
    },
    methods: {
      async fetchDataMitra() {
        this.loading = true;
        this.error = null;
        try {
          const response = await axios.get('/data-mitra');
          this.mitras = response.data;
        } catch (err) {
          this.error = 'Gagal mengambil data mitra';
          console.error(err);
        } finally {
          this.loading = false;
        }
      },
    },
  };
  </script>
  