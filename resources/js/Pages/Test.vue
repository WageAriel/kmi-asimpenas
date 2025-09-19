<!-- <template>
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
</script> -->


<template>
  <div>
    <h2>Form Data Seleksi Mitra</h2>
    <form @submit.prevent="submitForm">
      <!-- ID Mitra otomatis dan readonly -->

      <div>
        <label for="surat_permohonan">Surat Permohonan:</label>
        <select v-model="form.surat_permohonan">
          <option value="" disabled>Pilih</option>
          <option value="ada">Ada</option>
          <option value="tidak ada">Tidak Ada</option>
        </select>
      </div>

      <div>
        <label for="mb_surat_permohonan">Tgl MB Surat Permohonan:</label>
        <input type="date" v-model="form.mb_surat_permohonan" />
      </div>

      <div>
        <label for="akta_notaris">Akta Notaris:</label>
        <select v-model="form.akta_notaris">
          <option value="" disabled>Pilih</option>
          <option value="ada">Ada</option>
          <option value="tidak ada">Tidak Ada</option>
        </select>
      </div>

      <div>
        <label for="mb_akta_notaris">Tgl MB Akta Notaris:</label>
        <input type="date" v-model="form.mb_akta_notaris" />
      </div>

      <!-- Tambahkan input lain sesuai kebutuhan -->

      <div>
        <button type="submit">Simpan</button>
        <button type="button" @click="resetForm">Reset</button>
      </div>
    </form>

    <div v-if="responseData">
      <h3>Response API:</h3>
      <pre>{{ responseData }}</pre>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import axios from 'axios';

const form = reactive({
  id_mitra: '',
  surat_permohonan: '',
  mb_surat_permohonan: '',
  akta_notaris: '',
  mb_akta_notaris: '',
  // Tambahkan field lain sesuai kebutuhan
});

const responseData = ref(null);

onMounted(async () => {
  try {
    // Panggil API untuk mendapatkan data mitra user login
    const response = await axios.get('/data-mitra/my');
    const mitra = response.data;
    if (mitra && mitra.id_mitra) {
      form.id_mitra = mitra.id_mitra;
    } else {
      alert('Data mitra user tidak ditemukan.');
    }
  } catch (error) {
    alert('Gagal mengambil data mitra user: ' + (error.response?.data?.message || error.message));
  }
});

async function submitForm() {
  try {
    const response = await axios.post('/data-seleksi-mitra', form);
    responseData.value = response.data;
    alert('Data berhasil disimpan');
    resetForm();
  } catch (error) {
    alert('Gagal menyimpan data: ' + (error.response?.data?.message || error.message));
  }
}

function resetForm() {
  form.surat_permohonan = '';
  form.mb_surat_permohonan = '';
  form.akta_notaris = '';
  form.mb_akta_notaris = '';
  // reset field lain selain id_mitra, karena id_mitra tetap diisi otomatis
  responseData.value = null;
}
</script>

