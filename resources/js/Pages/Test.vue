<template>
  <form @submit.prevent="submitForm">
    <div>
      <label>Nama Perusahaan</label>
      <input type="text" v-model="form.nama_perusahaan" />
    </div>
    <div>
      <label>Email</label>
      <input type="email" v-model="form.email" />
    </div>
    <button type="submit">Simpan</button>
  </form>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        nama_perusahaan: '',
        email: '',
        // properti lain sesuai kebutuhan
      },
      user: null,
    };
  },
  mounted() {
    // Contoh mengambil user login dari API /api/user (Laravel default)
    axios.get('/user').then(response => {
      this.user = response.data;
      this.form.nama_perusahaan = this.user.name; // Bisa disesuaikan fieldnya dari user
      this.form.email = this.user.email;
    });
  },
  methods: {
    submitForm() {
      axios.post('/data-mitra', this.form)
        .then(response => {
          alert('Data mitra berhasil disimpan');
          // reset form atau aksi lain
        })
        .catch(error => {
          console.error(error);
          alert('Gagal menyimpan data mitra');
        });
    }
  }
}
</script>
