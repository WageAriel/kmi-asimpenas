<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

// Contoh data, ganti dengan data dari API/props sesuai kebutuhan
const seleksiMitras = ref([
    {
        id: 1,
        nama_perusahaan: 'PT Sumber Pangan',
        surat_permohonan: 'ada',
        mb_surat_permohonan: 'tidak ada',
        akta_notaris: 'ada',
        mb_akta_notaris: 'ada',
        nib: 'ada',
        mb_nib: 'tidak ada',
        ktp: 'ada',
        no_rekening: 'ada',
        mb_no_rekening: 'ada',
        npwp: 'tidak ada',
        mb_npwp: 'ada',
        surat_kuasa: 'ada',
        mb_surat_kuasa: 'tidak ada',
        lantai_jemur: 'ada',
        sarana_lainnya: 'tidak ada',
        mesin_memecah_kulit: 'ada',
        mesin_pemisah_gabah: 'ada',
        mesin_penyosoh: 'tidak ada',
        alat_pemisah_beras: 'ada'
    },
    // ...data lain
]);

const getStatusBadge = (val) => {
    return val === 'ada'
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
};

const lihatSeleksi = (item) => {
    // Arahkan ke halaman detail seleksi mitra, sesuaikan route
    window.location.href = `/admin/daftar-seleksi-mitra/${item.id}`;
};
</script>

<template>
    <Head title="Daftar Seleksi Mitra - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Seleksi Mitra</h2>
                <p class="text-gray-600">Kelola seleksi mitra yang terdaftar di sistem.</p>
            </div>

            <!-- Tabel Daftar Seleksi Mitra -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Nama Perusahaan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Surat Permohonan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB Surat Permohonan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Akta Notaris</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB Akta Notaris</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">NIB</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB NIB</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">KTP</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">No Rekening</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB No Rekening</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">NPWP</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB NPWP</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Surat Kuasa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">MB Surat Kuasa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Lantai Jemur</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Sarana Lainnya</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Mesin Pemecah Kulit</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Mesin Pemisah Gabah</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Mesin Penyosoh</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Alat Pemisah Beras</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in seleksiMitras" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">{{ item.nama_perusahaan }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.surat_permohonan)]">
                                    {{ item.surat_permohonan === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mb_surat_permohonan)]">
                                    {{ item.mb_surat_permohonan === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.akta_notaris)]">
                                    {{ item.akta_notaris === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mb_akta_notaris)]">
                                    {{ item.mb_akta_notaris === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.nib)]">
                                    {{ item.nib === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mb_nib)]">
                                    {{ item.mb_nib === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.ktp)]">
                                    {{ item.ktp === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.no_rekening)]">
                                    {{ item.no_rekening === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mb_no_rekening)]">
                                    {{ item.mb_no_rekening === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.npwp)]">
                                    {{ item.npwp === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mb_npwp)]">
                                    {{ item.mb_npwp === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.surat_kuasa)]">
                                    {{ item.surat_kuasa === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mb_surat_kuasa)]">
                                    {{ item.mb_surat_kuasa === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.lantai_jemur)]">
                                    {{ item.lantai_jemur === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.sarana_lainnya)]">
                                    {{ item.sarana_lainnya === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_memecah_kulit)]">
                                    {{ item.mesin_memecah_kulit === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_pemisah_gabah)]">
                                    {{ item.mesin_pemisah_gabah === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.mesin_penyosoh)]">
                                    {{ item.mesin_penyosoh === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="['px-2 py-1 rounded-full', getStatusBadge(item.alat_pemisah_beras)]">
                                    {{ item.alat_pemisah_beras === 'ada' ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button
                                    @click="lihatMitra(mitra)"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-900 text-xs"
                                >
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>