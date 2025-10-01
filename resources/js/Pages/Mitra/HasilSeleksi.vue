<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { ref } from 'vue';

defineProps({
    hasilSeleksi: {
        type: Object,
        default: () => ({
            status: 'Dalam Review',
            tanggal_pengajuan: '15/01/2024',
            tanggal_review: null,
            skor_total: null,
            keterangan: 'Pengajuan Anda sedang dalam proses review oleh tim kami.',
            detail_penilaian: null,
            dokumen_hasil: null,
            tahap_seleksi: 'review_dokumen'
        })
    }
});

// Data dummy untuk simulasi
const timelineData = ref([
    {
        title: 'Pengajuan Diterima',
        date: '15 Januari 2024',
        time: '14:30',
        status: 'completed',
        description: 'Pengajuan seleksi mitra telah diterima dan terdaftar dalam sistem'
    },
    {
        title: 'Review Dokumen',
        date: '16 Januari 2024',
        time: '09:00',
        status: 'current',
        description: 'Tim sedang melakukan review kelengkapan dan validitas dokumen'
    },
    {
        title: 'Verifikasi Data',
        date: '-',
        time: '-',
        status: 'pending',
        description: 'Verifikasi data perusahaan dan legalitas'
    },
    {
        title: 'Hasil Akhir',
        date: '-',
        time: '-',
        status: 'pending',
        description: 'Pengumuman hasil seleksi'
    }
]);

const timelineKlasifikasi = ref([
    {
        title: 'Input Data Klasifikasi',
        date: '18 Januari 2024',
        time: '10:00',
        status: 'completed',
        description: 'Data klasifikasi mitra telah diinput dan disimpan.'
    },
    {
        title: 'Verifikasi Klasifikasi',
        date: '19 Januari 2024',
        time: '13:00',
        status: 'current',
        description: 'Tim sedang melakukan verifikasi data klasifikasi mitra.'
    },
    {
        title: 'Penilaian Klasifikasi',
        date: '-',
        time: '-',
        status: 'pending',
        description: 'Penilaian kelayakan klasifikasi mitra.'
    },
    {
        title: 'Hasil Klasifikasi',
        date: '-',
        time: '-',
        status: 'pending',
        description: 'Pengumuman hasil klasifikasi mitra.'
    }
]);


const dokumenReview = ref([
    { nama: 'Surat Permohonan', status: 'approved', catatan: 'Lengkap dan sesuai format' },
    { nama: 'Akta Notaris', status: 'approved', catatan: 'Valid dan terbaru' },
    { nama: 'NPWP', status: 'approved', catatan: 'Valid' },
]);

const getStatusIcon = (status) => {
    switch (status) {
        case 'completed':
            return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'current':
            return 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
        default:
            return 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case 'completed': return 'text-green-600';
        case 'current': return 'text-blue-600';
        default: return 'text-gray-400';
    }
};

const getDocStatusBadge = (status) => {
    switch (status) {
        case 'approved': return 'bg-green-100 text-green-800';
        case 'review': return 'bg-yellow-100 text-yellow-800';
        case 'revision': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getDocStatusText = (status) => {
    switch (status) {
        case 'approved': return 'Disetujui';
        case 'review': return 'Review';
        case 'revision': return 'Perlu Revisi';
        default: return 'Pending';
    }
};
</script>

<template>
    <Head title="Hasil Seleksi Mitra - ASIMPENAS" />

    <MitraLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Hasil Seleksi Mitra</h2>
                <p class="text-gray-600">Pantau status dan hasil seleksi mitra Anda</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Status Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Status Pengajuan</h3>
                                <span :class="[
                                    'px-3 py-1 text-sm font-medium rounded-full',
                                    hasilSeleksi.status === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                    hasilSeleksi.status === 'Ditolak' ? 'bg-red-100 text-red-800' :
                                    'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ hasilSeleksi.status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-sm text-gray-600">Tanggal Pengajuan</p>
                                    <p class="font-medium text-gray-900">{{ hasilSeleksi.tanggal_pengajuan }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tahap Saat Ini</p>
                                    <p class="font-medium text-gray-900">Review Dokumen</p>
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Keterangan</h4>
                                        <p class="mt-1 text-sm text-blue-700">{{ hasilSeleksi.keterangan }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Progress -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Timeline Seleksi</h3>
                            
                            <div class="space-y-6">
                                <div v-for="(item, index) in timelineData" :key="index" class="relative flex items-start">
                                    <div class="flex-shrink-0">
                                        <div :class="[
                                            'w-10 h-10 rounded-full flex items-center justify-center border-2',
                                            item.status === 'completed' ? 'bg-green-100 border-green-500' :
                                            item.status === 'current' ? 'bg-blue-100 border-blue-500' :
                                            'bg-gray-100 border-gray-300'
                                        ]">
                                            <svg :class="[
                                                'w-5 h-5',
                                                getStatusColor(item.status)
                                            ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusIcon(item.status)" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 :class="[
                                                'text-sm font-medium',
                                                item.status === 'current' ? 'text-blue-900' : 'text-gray-900'
                                            ]">{{ item.title }}</h4>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ item.date }}</p>
                                                <p class="text-xs text-gray-500">{{ item.time }}</p>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ item.description }}</p>
                                    </div>

                                    <!-- Connector line -->
                                    <div v-if="index < timelineData.length - 1" :class="[
                                        'absolute left-5 top-10 w-0.5 h-6',
                                        item.status === 'completed' ? 'bg-green-300' : 'bg-gray-300'
                                    ]"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Klasifikasi -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Timeline Klasifikasi</h3>
                            <div class="space-y-6">
                                <div
                                    v-for="(item, idx) in timelineKlasifikasi"
                                    :key="idx"
                                    class="relative flex items-start"
                                >
                                    <div class="flex-shrink-0">
                                        <div :class="[
                                            'w-10 h-10 rounded-full flex items-center justify-center border-2',
                                            item.status === 'completed' ? 'bg-green-100 border-green-500' :
                                            item.status === 'current' ? 'bg-blue-100 border-blue-500' :
                                            'bg-gray-100 border-gray-300'
                                        ]">
                                            <svg :class="[
                                                'w-5 h-5',
                                                item.status === 'completed' ? 'text-green-600' :
                                                item.status === 'current' ? 'text-blue-600' :
                                                'text-gray-400'
                                            ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    :d="getStatusIcon(item.status)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 :class="[
                                                'text-sm font-medium',
                                                item.status === 'current' ? 'text-blue-900' : 'text-gray-900'
                                            ]">{{ item.title }}</h4>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ item.date }}</p>
                                                <p class="text-xs text-gray-500">{{ item.time }}</p>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ item.description }}</p>
                                    </div>
                                    <!-- Connector line -->
                                    <div v-if="idx < timelineKlasifikasi.length - 1"
                                        :class="[
                                            'absolute left-5 top-10 w-0.5 h-6',
                                            item.status === 'completed' ? 'bg-green-300' : 'bg-gray-300'
                                        ]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Cepat</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Estimasi Proses</span>
                                    <span class="text-sm font-medium text-gray-900">7-14 hari</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Dokumen Disubmit</span>
                                    <span class="text-sm font-medium text-gray-900">5 dari 5</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Progress</span>
                                    <span class="text-sm font-medium text-gray-900">40%</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 40%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Butuh Bantuan?</h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="text-sm text-gray-600">mitra@asimpenas.go.id</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="text-sm text-gray-600">(021) 123-4567</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-600">Jam kerja: 08:00-17:00</span>
                                </div>
                            </div>

                            <button class="mt-4 w-full bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                Hubungi Tim Support
                            </button>
                        </div>
                    </div>

                    <!-- Download -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumen</h3>
                            
                            <div class="space-y-3">
                                <button class="w-full flex items-center justify-between p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">Pengajuan Seleksi</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <button class="w-full flex items-center justify-between p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">Klasifikasi Mitra</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MitraLayout>
</template>