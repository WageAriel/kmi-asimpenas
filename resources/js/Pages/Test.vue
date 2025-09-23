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

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const form = ref({
  nama_perusahaan: '',
  email: '',
  // properti lain sesuai kebutuhan
})

const user = ref(null)

onMounted(async () => {
  try {
    const response = await axios.get('/user')
    user.value = response.data
    form.value.nama_perusahaan = user.value.name // sesuaikan field dari user
    form.value.email = user.value.email
  } catch (error) {
    console.error('Gagal mengambil data user:', error)
  }
})

const submitForm = async () => {
  try {
    await axios.post('/data-mitra', form.value)
    alert('Data mitra berhasil disimpan')
    // reset form atau aksi lain jika perlu
  } catch (error) {
    console.error(error)
    alert('Gagal menyimpan data mitra')
  }
}
</script>




