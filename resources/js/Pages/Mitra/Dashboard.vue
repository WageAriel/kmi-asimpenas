<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { ref } from 'vue';

// Data dummy untuk dashboard
const statistik = ref({
    pengajuan_total: 3,
    pengajuan_approved: 1,
    pengajuan_pending: 2,
    po_aktif: 5
});
</script>

<template>
    <Head title="Dashboard Mitra - ASIMPENAS" />

    <MitraLayout>
        <!-- Dashboard Content -->
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Dashboard Mitra</h2>
                <p class="text-gray-600">Selamat datang di portal mitra ASIMPENAS.</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stats cards here -->
            </div>

            <!-- Content sections here -->
        </div>
    </MitraLayout>
</template>