<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Data dari backend
const dataSeleksi = ref([]);
const dataKlasifikasi = ref([]);
const dataHasilSeleksi = ref([]);
const loading = ref(true);
const error = ref(null);

// Computed properties untuk timeline
const timelineData = computed(() => {
    if (dataSeleksi.value.length === 0) {
        return [{
            title: 'Belum Ada Pengajuan',
            date: '-',
            time: '-',
            status: 'pending',
            description: 'Anda belum mengajukan seleksi mitra'
        }];
    }

    const latestSeleksi = dataSeleksi.value[0];
    const hasilSeleksi = dataHasilSeleksi.value.find(h => h.id_seleksi_mitra === latestSeleksi.id_seleksi_mitra);

    const timeline = [
        {
            title: 'Pengajuan Diterima',
            date: formatDate(latestSeleksi.created_at),
            time: formatTime(latestSeleksi.created_at),
            status: 'completed',
            description: 'Pengajuan seleksi mitra telah diterima dan terdaftar dalam sistem'
        },
        {
            title: 'Verifikasi Data',
            date: hasilSeleksi ? formatDate(hasilSeleksi.created_at) : '-',
            time: hasilSeleksi ? formatTime(hasilSeleksi.created_at) : '-',
            status: hasilSeleksi ? 'completed' : 'pending',
            description: hasilSeleksi
                ? 'Data telah diverifikasi oleh admin'
                : 'Menunggu verifikasi data oleh admin'
        },
        {
            title: 'Hasil Akhir',
            date: hasilSeleksi && hasilSeleksi.kesimpulan_akhir ? formatDate(hasilSeleksi.updated_at) : '-',
            time: hasilSeleksi && hasilSeleksi.kesimpulan_akhir ? formatTime(hasilSeleksi.updated_at) : '-',
            status: hasilSeleksi && hasilSeleksi.kesimpulan_akhir ? 'completed' : 'pending',
            description: hasilSeleksi && hasilSeleksi.kesimpulan_akhir
                ? `Hasil Seleksi: ${hasilSeleksi.kesimpulan_akhir}. ${hasilSeleksi.kesimpulan_akhir === 'Lolos' ? 'Selamat! Anda dinyatakan lolos seleksi mitra.' : 'Mohon maaf, Anda belum memenuhi kriteria seleksi.'}`
                : 'Menunggu pengumuman hasil seleksi'
        }
    ];

    return timeline;
});

const timelineKlasifikasi = computed(() => {
    if (dataKlasifikasi.value.length === 0) {
        return [{
            title: 'Belum Ada Data Klasifikasi',
            date: '-',
            time: '-',
            status: 'pending',
            description: 'Anda belum mengisi data klasifikasi mitra'
        }];
    }

    const latestKlasifikasi = dataKlasifikasi.value[0];

    return [
        {
            title: 'Input Data Klasifikasi',
            date: formatDate(latestKlasifikasi.created_at),
            time: formatTime(latestKlasifikasi.created_at),
            status: 'completed',
            description: 'Data klasifikasi mitra telah diinput dan disimpan.'
        },
        {
            title: 'Verifikasi Klasifikasi',
            date: latestKlasifikasi.hasil_klasifikasi ? formatDate(latestKlasifikasi.updated_at) : '-',
            time: latestKlasifikasi.hasil_klasifikasi ? formatTime(latestKlasifikasi.updated_at) : '-',
            status: latestKlasifikasi.hasil_klasifikasi ? 'completed' : 'current',
            description: latestKlasifikasi.hasil_klasifikasi
                ? 'Data klasifikasi telah diverifikasi oleh admin'
                : 'Tim sedang melakukan verifikasi data klasifikasi mitra.'
        },
        {
            title: 'Hasil Klasifikasi',
            date: latestKlasifikasi.hasil_klasifikasi ? formatDate(latestKlasifikasi.updated_at) : '-',
            time: latestKlasifikasi.hasil_klasifikasi ? formatTime(latestKlasifikasi.updated_at) : '-',
            status: latestKlasifikasi.hasil_klasifikasi ? 'completed' : 'pending',
            description: latestKlasifikasi.hasil_klasifikasi
                ? `Hasil klasifikasi: Kelas ${latestKlasifikasi.hasil_klasifikasi}`
                : 'Menunggu pengumuman hasil klasifikasi mitra.'
        }
    ];
});

const currentStatus = computed(() => {
    if (dataHasilSeleksi.value.length === 0) {
        return {
            status: 'Belum Ada Hasil',
            tanggal_pengajuan: '-',
            tahap_saat_ini: 'Menunggu Review',
            keterangan: 'Belum ada data hasil seleksi. Pengajuan Anda sedang menunggu review dari admin.'
        };
    }

    const latestHasil = dataHasilSeleksi.value[0];
    
    let status = 'Dalam Review';
    let tahap = 'Verifikasi Data';
    let keterangan = 'Pengajuan Anda sedang dalam proses verifikasi oleh admin.';
    
    if (latestHasil.kesimpulan_akhir) {
        if (latestHasil.kesimpulan_akhir === 'Lolos') {
            status = 'Lolos Seleksi';
            tahap = 'Selesai';
            keterangan = 'Selamat! Pengajuan Anda telah disetujui dan dinyatakan lolos seleksi mitra.';
        } else if (latestHasil.kesimpulan_akhir === 'Tidak Lolos') {
            status = 'Tidak Lolos';
            tahap = 'Selesai';
            keterangan = 'Mohon maaf, pengajuan Anda belum memenuhi kriteria seleksi mitra. Silakan perbaiki dan ajukan kembali.';
        }
    }

    return {
        status: status,
        tanggal_pengajuan: formatDate(latestHasil.created_at),
        tahap_saat_ini: tahap,
        keterangan: keterangan
    };
});

const progressPercentage = computed(() => {
    const completedSteps = timelineData.value.filter(t => t.status === 'completed').length;
    return Math.round((completedSteps / timelineData.value.length) * 100);
});

const estimatedDays = computed(() => {
    if (dataSeleksi.value.length === 0) return '0';
    const latestSeleksi = dataSeleksi.value[0];
    const hasilSeleksi = dataHasilSeleksi.value.find(h => h.id_seleksi_mitra === latestSeleksi.id_seleksi_mitra);
    
    if (hasilSeleksi && hasilSeleksi.kesimpulan_akhir) return '0';
    return '7-14';
});

const documentsSubmitted = computed(() => {
    if (dataSeleksi.value.length === 0) return '0 dari 0';
    
    const latestSeleksi = dataSeleksi.value[0];
    const dokumenFields = [
        'surat_permohonan', 'akta_notaris', 'nib', 'ktp', 
        'no_rekening', 'npwp', 'surat_kuasa'
    ];
    
    const total = dokumenFields.length;
    const submitted = dokumenFields.filter(field => latestSeleksi[field] === 'ada').length;
    
    return `${submitted} dari ${total}`;
});

// Helper functions
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
};

const formatTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

// Fetch data from API
const fetchData = async () => {
    loading.value = true;
    error.value = null;

    try {
        const [seleksiRes, klasifikasiRes, hasilRes] = await Promise.all([
            axios.get('/data-seleksi-mitra/my'),
            axios.get('/klasifikasi-mitra/my'),
            axios.get('/hasil-seleksi-mitra/my')
        ]);

        // Urutkan data dari terbaru ke terlama berdasarkan created_at
        dataSeleksi.value = seleksiRes.data.sort((a, b) => 
            new Date(b.created_at) - new Date(a.created_at)
        );
        dataKlasifikasi.value = klasifikasiRes.data.sort((a, b) => 
            new Date(b.created_at) - new Date(a.created_at)
        );
        dataHasilSeleksi.value = hasilRes.data.sort((a, b) => 
            new Date(b.created_at) - new Date(a.created_at)
        );
    } catch (err) {
        console.error('Error fetching data:', err);
        error.value = 'Gagal memuat data. Silakan refresh halaman.';
    } finally {
        loading.value = false;
    }
};

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

const downloadKlasifikasi = () => {
    if (dataKlasifikasi.value.length > 0) {
        const klasifikasiId = dataKlasifikasi.value[0].id_klasifikasi_mitra;
        window.location.href = `/mitra/klasifikasi/${klasifikasiId}/download`;
    }
};

const downloadSeleksi = () => {
    if (dataSeleksi.value.length > 0) {
        const seleksiId = dataSeleksi.value[0].id_seleksi_mitra;
        window.location.href = `/mitra/pengajuan-seleksi/${seleksiId}/download`;
    }
};

onMounted(() => {
    fetchData();
});
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

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-red-800">Error</h4>
                        <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Status Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Status Pengajuan</h3>
                                <span :class="[
                                    'px-3 py-1 text-sm font-medium rounded-full',
                                    currentStatus.status === 'Lolos Seleksi' ? 'bg-green-100 text-green-800' :
                                    currentStatus.status === 'Tidak Lolos' ? 'bg-red-100 text-red-800' :
                                    'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ currentStatus.status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-sm text-gray-600">Tanggal Pengajuan</p>
                                    <p class="font-medium text-gray-900">{{ currentStatus.tanggal_pengajuan }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tahap Saat Ini</p>
                                    <p class="font-medium text-gray-900">{{ currentStatus.tahap_saat_ini }}</p>
                                </div>
                            </div>

                            <div :class="[
                                'border rounded-lg p-4',
                                currentStatus.status === 'Lolos Seleksi' ? 'bg-green-50 border-green-200' :
                                currentStatus.status === 'Tidak Lolos' ? 'bg-red-50 border-red-200' :
                                'bg-blue-50 border-blue-200'
                            ]">
                                <div class="flex">
                                    <svg :class="[
                                        'w-5 h-5 mt-0.5',
                                        currentStatus.status === 'Lolos Seleksi' ? 'text-green-400' :
                                        currentStatus.status === 'Tidak Lolos' ? 'text-red-400' :
                                        'text-blue-400'
                                    ]" fill="currentColor" viewBox="0 0 20 20">
                                        <path v-if="currentStatus.status === 'Lolos Seleksi'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        <path v-else-if="currentStatus.status === 'Tidak Lolos'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        <path v-else fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <h4 :class="[
                                            'text-sm font-medium',
                                            currentStatus.status === 'Lolos Seleksi' ? 'text-green-800' :
                                            currentStatus.status === 'Tidak Lolos' ? 'text-red-800' :
                                            'text-blue-800'
                                        ]">Keterangan</h4>
                                        <p :class="[
                                            'mt-1 text-sm',
                                            currentStatus.status === 'Lolos Seleksi' ? 'text-green-700' :
                                            currentStatus.status === 'Tidak Lolos' ? 'text-red-700' :
                                            'text-blue-700'
                                        ]">{{ currentStatus.keterangan }}</p>
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
                    <!-- <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Cepat</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Estimasi Proses</span>
                                    <span class="text-sm font-medium text-gray-900">{{ estimatedDays }} hari</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Dokumen Disubmit</span>
                                    <span class="text-sm font-medium text-gray-900">{{ documentsSubmitted }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Progress</span>
                                    <span class="text-sm font-medium text-gray-900">{{ progressPercentage }}%</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" :style="`width: ${progressPercentage}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- Contact Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Butuh Bantuan?</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900">Perum BULOG Kantor Cabang Surakarta</p>
                                        <p class="text-xs mt-1">Jl. L.U. Adi Sucipto No. 17, Blulukan, Colomadu,</p>
                                        <p class="text-xs">Karanganyar 57174 Jawa Tengah, Surakarta</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    <span class="font-medium">(+62 - 271) 716498</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    <span class="font-medium">bulog.bumn.ska@gmail.com</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-medium">www.bulog.co.id</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Download -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumen</h3>
                            
                            <div class="space-y-3">
                                <button 
                                    v-if="dataSeleksi.length > 0"
                                    @click="downloadSeleksi"
                                    class="w-full flex items-center justify-between p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
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

                                <button 
                                    v-if="dataKlasifikasi.length > 0"
                                    @click="downloadKlasifikasi"
                                    class="w-full flex items-center justify-between p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
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

                                <div v-if="dataSeleksi.length === 0 && dataKlasifikasi.length === 0" class="text-center py-4">
                                    <p class="text-sm text-gray-500">Belum ada dokumen tersedia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MitraLayout>
</template>