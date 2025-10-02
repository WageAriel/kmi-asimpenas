<!-- filepath: resources/js/Pages/Admin/DaftarMitra/Index.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Contoh data, ganti dengan data dari API/props sesuai kebutuhan
const mitras = ref([
    {
        id: 1,
        nama_perusahaan: 'PT Sumber Pangan',
        badan_hukum: 'PT',
        alamat: 'Jl. Raya No. 1, Jakarta',
        nama_cp: 'Budi Santoso',
        no_telp_perusahaan: '02112345678',
        no_telp_cp: '081234567890',
        status: 'Distributor'
    },
    // ...data mitra lain
]);

const getStatusClass = (status) => {
    switch (status) {
        case 'Penggilingan': return 'bg-green-100 text-green-800';
        case 'Distributor': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const lihatMitra = (mitra) => {
    // Arahkan ke halaman detail mitra, sesuaikan route
    window.location.href = `/admin/daftar-mitra/${mitra.id}`;
};
</script>

<template>
    <Head title="Dashboard Admin - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Mitra</h2>
                <p class="text-gray-600">Kelola mitra yang terdaftar di sistem.</p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-xl font-bold text-white mb-2">Daftar Mitra</h3>
                        <p class="text-blue-100">
                            Berikut adalah daftar mitra yang terdaftar di sistem ASIMPENAS.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button
                            @click="goToForm('new')"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Mitra Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Mitra -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Mitra</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badan Hukum Usaha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama CP</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Telp Perusahaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Telp CP</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="mitra in mitras" :key="mitra.id" class="hover:bg-gray-50">
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.nama_perusahaan }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.badan_hukum }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.alamat }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.nama_cp }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.no_telp_perusahaan }}</td>
                                <td class="text-sm px-4 py-3 whitespace-nowrap">{{ mitra.no_telp_cp }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusClass(mitra.status)]">
                                        {{ mitra.status }}
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
        </div>
    </AdminLayout>
</template>