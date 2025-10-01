<script setup>
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>

<template>
    <Head title="Dashboard Admin - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
             <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Mitra</h2>
                <p class="text-gray-600">Kelola mitra yang terdaftar di sistem.</p>
             </div>
        </div>
    </AdminLayout>
</template>