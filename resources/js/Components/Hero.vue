<script setup>
import { Link, router, usePage, Head } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import logoImg from '@/../../resources/assets/Images/bulog.png';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAuthenticated = computed(() => !!user.value);
const mobileMenuOpen = ref(false);

// HPP Data State
const hppData = ref([]);
const isLoadingHpp = ref(true);

// Get dashboard route based on user role
const dashboardRoute = computed(() => {
    if (!user.value) return '/login';
    
    if (user.value.role === 'admin') {
        return '/admin/dashboard';
    }
    
    if (user.value.role === 'mitra') {
        return '/mitra/dashboard';
    }
    if (user.value.role === 'super admin') {
        return '/super-admin/dashboard';
    }
    
    return '/dashboard';
});

// Handle logout
const handleLogout = () => {
    router.post('/logout', {}, {
        onSuccess: () => {
            // Redirect to landing page after logout
            router.visit('/');
        }
    });
};

// Toggle mobile menu
const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

// Fetch HPP Data from API
const fetchHppData = async () => {
    try {
        isLoadingHpp.value = true;
        const response = await axios.get('/hpp');
        hppData.value = response.data;
    } catch (error) {
        console.error('Error fetching HPP data:', error);
    } finally {
        isLoadingHpp.value = false;
    }
};

// Computed properties for filtered HPP data
const hppGabahBeras = computed(() => {
    return hppData.value.find(doc => doc.jenis_hpp === 'Gabah dan Beras');
});

const hppJagung = computed(() => {
    return hppData.value.find(doc => doc.jenis_hpp === 'Jagung');
});

// Format currency helper
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};

// Load HPP data on component mount
onMounted(() => {
    fetchHppData();
});
</script>

<template>
    <Head>
        <title>ASIMPENAS - Aplikasi Seleksi Mitra dan Penawaran Komoditas Perum BULOG Surakarta</title>
        <meta name="description" content="Platform digital seleksi mitra pangan dan penawaran komoditas gabah beras Perum BULOG Kantor Cabang Surakarta. Daftar sebagai mitra pangan sekarang!" />
        <meta name="keywords" content="ASIMPENAS, Bulog, Mitra Pangan, Gabah, Beras, Surakarta, Seleksi Mitra, Komoditas" />
    </Head>

    <section class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header dengan Logo dan Auth Buttons -->
        <div class="w-full px-4 py-6">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                        <img :src="logoImg" alt="Logo Bulog" class="w-7 h-7 object-contain" />
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl font-bold text-gray-800">ASIMPENAS</span>
                        <p class="text-xs text-gray-500 hidden sm:block">Aplikasi Seleksi Mitra</p>
                    </div>
                </div>

                <!-- Desktop Menu - Hidden on mobile -->
                <div class="hidden lg:flex gap-3 items-center">
                    <!-- When user is NOT logged in -->
                    <template v-if="!isAuthenticated">
                        <Link 
                            href="/login" 
                            class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
                        >
                            Login
                        </Link>
                        <Link 
                            href="/register" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            Register
                        </Link>
                    </template>

                    <!-- When user IS logged in -->
                    <template v-else>
                        <div class="flex items-center gap-3">
                            <!-- User Info -->
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ user.role }}</p>
                            </div>

                            <!-- Dashboard Button -->
                            <Link 
                                :href="dashboardRoute" 
                                class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>

                            <!-- Logout Button -->
                            <button 
                                @click="handleLogout"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Mobile Menu Button - Visible on mobile only -->
                <button 
                    @click="toggleMobileMenu"
                    class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Dropdown -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileMenuOpen" class="lg:hidden mt-4 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <!-- When user is NOT logged in -->
                    <template v-if="!isAuthenticated">
                        <div class="p-4 space-y-2">
                            <Link 
                                href="/login" 
                                class="block w-full px-4 py-3 text-center text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
                                @click="mobileMenuOpen = false"
                            >
                                Login
                            </Link>
                            <Link 
                                href="/register" 
                                class="block w-full px-4 py-3 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                                @click="mobileMenuOpen = false"
                            >
                                Register
                            </Link>
                        </div>
                    </template>

                    <!-- When user IS logged in -->
                    <template v-else>
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ user.role }}</p>
                        </div>
                        <div class="p-4 space-y-2">
                            <Link 
                                :href="dashboardRoute" 
                                class="flex items-center justify-center gap-2 w-full px-4 py-3 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
                                @click="mobileMenuOpen = false"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <button 
                                @click="handleLogout(); mobileMenuOpen = false"
                                class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </div>
                    </template>
                </div>
            </Transition>
        </div>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-4 py-8">
            <!-- Intro Section with Proper SEO Headings -->
            <div class="text-center mb-8">
                <h1 class="text-xl md:text-3xl font-bold text-gray-900 mb-4">
                    Selamat Datang di <span class="text-blue-600">ASIMPENAS</span>
                </h1>
                <h2 class="text-lg md:text-xl text-gray-700 font-semibold max-w-3xl mx-auto mb-4">
                    Aplikasi Seleksi Mitra dan Penawaran Komoditas 
                    <br />
                    Perum BULOG Kantor Cabang Surakarta
                </h2>
                <p class="text-base text-gray-600 max-w-3xl mx-auto mb-8">
                    Platform digital untuk seleksi calon mitra pangan, klasifikasi mitra, dan penawaran komoditas gabah dan beras
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
                    <!-- When NOT logged in -->
                    <template v-if="!isAuthenticated">
                        <Link 
                            href="/register"
                            class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-semibold text-base hover:bg-blue-700 transition-colors shadow-lg"
                        >
                            Daftar Sebagai Mitra Pangan
                        </Link>
                        <Link 
                            href="/login"
                            class="border-2 border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-semibold text-base hover:bg-gray-50 transition-colors"
                        >
                            Sudah Punya Akun?
                        </Link>
                    </template>

                    <!-- When logged in -->
                    <template v-else>
                        <Link 
                            :href="dashboardRoute"
                            class="bg-blue-600 text-white px-4 py-2 rounded-xl font-semibold text-lg hover:bg-blue-700 transition-colors shadow-lg inline-flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Ke Dashboard
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Main Information Sections -->
            <div class="space-y-16">
                <!-- 1. Pengadaan Gabah dan Beras Dalam Negeri Tahun 2025 -->
                 <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">🌾 PENGADAAN GABAH DAN BERAS DALAM NEGERI</h2>
                        <p class="text-gray-600">Dengan Persyaratan dibawah ini</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- A. Perusahaan Penggilingan Padi -->
                        <div class="bg-blue-50 p-6 rounded-xl">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">A. Perusahaan Penggilingan Padi</h3>
                            <div class="space-y-3">
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">1.</span>
                                        <span><strong>Surat Permohonan</strong> menjadi <strong>Mitra Pangan Pengadaan (MPP)</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">2.</span>
                                        <span><strong>Kartu Tanda Penduduk / KTP</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">3.</span>
                                        <span><strong>NPWP (Nomor Pokok Wajib Pajak)</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">4.</span>
                                        <span><strong>Akta Notaris Pendirian Perusahaan</strong> bagi <strong>MPP</strong> berbadan hukum atau berbadan usaha</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">5.</span>
                                        <span><strong>Nomor Induk Berusaha / NIB</strong> dengan KBLI yang sesuai</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">6.</span>
                                        <span><strong>No Rekening</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">7.</span>
                                        <span><strong>Surat Pengukuhan Pengusaha Kena Pajak (PKP)</strong> dan/atau <strong>Surat Pernyataan Non Pengusaha Kena Pajak (Non PKP) bermaterai</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">8.</span>
                                        <span><strong>Kontrak atau Surat Kuasa</strong> yang dinotarilkan dari pemilik penggilingan, bagi yang tidak memiliki tetapi menguasai sarana penggilingan. menguasai sarana</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-blue-600 mt-0.5">9.</span>
                                        <span><strong>Surat Kuasa</strong> apabila dikuasakan orang lain bermaterai</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- B. Poktan/Gapoktan -->
                        <div class="bg-green-50 p-6 rounded-xl">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">B. Poktan/Gapoktan</h3>
                            <div class="space-y-3">
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">1.</span>
                                        <span><strong>Surat Permohonan</strong> menjadi <strong>Mitra Pangan Pengadaan (MPP)</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">2.</span>
                                        <span><strong>Surat Rekomendasi</strong> dari Dinas yang membidangi Pertanian</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">3.</span>
                                        <span><strong>Daftar Nama dan Alamat petani Anggota Poktan/Gapoktan</strong> sesuai <strong>Kartu Tanda Penduduk (KTP) </strong>yang dibuat oleh Pengurus Poktan/Gapoktan</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">4.</span>
                                        <span>Keterangan lokasi dan luas lahan yang dikuasai yang dikeluarkan setempat <strong>oleh Pemerintah Desa/Kecamatan</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">5.</span>
                                        <span><strong>Poktan/Gapoktan</strong> yang memiliki sarana penggilingan/pengolahan dapat diikutkan dalam pengadaan gabah, beras, dan pangan pokok lainnya seperti kedelai dan jagung</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">6.</span>
                                        <span><strong>Poktan/Gapoktan</strong> yang belum memiliki sarana penggilingan dan pengolahan pangan lainnya hanya diikutkan dalam pengadaan gabah</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">7.</span>
                                        <span><strong>No Rekening</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">8.</span>
                                        <span><strong>NPWP</strong> Gapoktan/Pengurus yang ditunjuk</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-medium text-green-600 mt-0.5">9.</span>
                                        <span><strong>Surat Pengukuhan Pengusaha Kena Pajak (PKP)</strong> dan/atau <strong>Surat Pernyataan Non Pengusaha Kena Pajak (Non PKP) bermaterai</strong></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Harga Pembelian Sesuai Keputusan Pemerintah -->
                 <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">💰 HARGA PEMBELIAN PEMERINTAH (HPP)</h2>
                    </div>
                    
                    <!-- Loading State -->
                    <div v-if="isLoadingHpp" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2 text-gray-600">Memuat data HPP...</p>
                    </div>

                    <!-- HPP Data -->
                    <div v-else class="grid md:grid-cols-2 gap-8">
                        <!-- A. HPP Gabah dan Beras -->
                        <div class="bg-green-50 p-6 rounded-xl">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">A. Harga Pembelian Pemerintah Gabah dan Beras</h3>
                            
                            <template v-if="hppGabahBeras">
                                <p class="text-base text-black mb-2"> 
                                    Sesuai dengan Keputusan Kepala Badan Pangan Nasional Republik Indonesia 
                                    <strong>Nomor {{ hppGabahBeras.nomor_keputusan }} Tahun {{ hppGabahBeras.tahun }}</strong>
                                    tentang {{ hppGabahBeras.tentang }}
                                </p>
                                <div class="space-y-3 mt-4">
                                    <ul class="space-y-3 text-sm text-black">
                                        <li v-for="komoditas in hppGabahBeras.komoditas" :key="komoditas.id" class="flex items-start gap-2">
                                            <span>-</span>
                                            <span>
                                                Harga <strong>{{ komoditas.nama_komoditas }} </strong>
                                                di <strong>{{ komoditas.tempat }} </strong>
                                                sebesar <strong>{{ formatCurrency(komoditas.harga_per_kg) }},-/kg</strong>
                                                <span v-if="komoditas.ketentuan"> dengan ketentuan ({{ komoditas.ketentuan }})</span>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </template>
                            <p v-else class="text-gray-500 text-center py-4">Data HPP Gabah dan Beras belum tersedia</p>
                        </div>

                        <!-- B. HPP Jagung -->
                        <div class="bg-green-50 p-6 rounded-xl">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">B. Harga Pembelian Pemerintah Jagung</h3>
                            
                            <template v-if="hppJagung">
                                <p class="text-base text-black mb-2">
                                    Sesuai dengan Keputusan Kepala Badan Pangan Nasional Republik Indonesia 
                                    <strong>Nomor {{ hppJagung.nomor_keputusan }} Tahun {{ hppJagung.tahun }}</strong>
                                    Tentang {{ hppJagung.tentang }}
                                </p>
                                <div class="space-y-3 mt-4">
                                    <ul class="space-y-3 text-sm text-black">
                                        <li v-for="komoditas in hppJagung.komoditas" :key="komoditas.id" class="flex items-start gap-2">
                                            <span>-</span>
                                            <span>
                                                Harga <strong>{{ komoditas.nama_komoditas }} </strong>
                                                di <strong>{{ komoditas.tempat }} </strong>
                                                sebesar <strong>{{ formatCurrency(komoditas.harga_per_kg) }},-/kg</strong>
                                                <span v-if="komoditas.ketentuan"> ({{ komoditas.ketentuan }})</span>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </template>
                            <p v-else class="text-gray-500 text-center py-4">Data HPP Jagung belum tersedia</p>
                        </div>
                    </div>
                </div>

                
                <!-- 3. Apa itu ASIMPENAS -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-2xl">❓</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Apa itu ASIMPENAS?</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <p class="text-lg text-gray-600 mb-4 leading-relaxed">
                                <strong>ASIMPENAS</strong> (Aplikasi Seleksi Mitra dan Penawaran Komoditas) adalah platform digital yang dikelola oleh Perum BULOG Kantor Cabang Surakarta untuk mengelola proses seleksi calon mitra pangan, klasifikasi mitra berdasarkan kapasitas dan kualitas, serta penawaran komoditas pangan seperti gabah dan beras sesuai dengan standar dan ketentuan yang berlaku.
                            </p>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Seleksi calon mitra pangan yang berkualitas
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Klasifikasi mitra berdasarkan kapasitas dan mutu
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pengelolaan penawaran komoditas gabah dan beras
                                </li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-xl">
                            <h4 class="font-semibold text-gray-800 mb-3">Tujuan Utama:</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-white p-3 rounded-lg">🎯 Seleksi Calon Mitra Pangan</div>
                                <div class="bg-white p-3 rounded-lg">� Klasifikasi Berdasarkan Kapasitas</div>
                                <div class="bg-white p-3 rounded-lg">🌾 Penawaran Komoditas seperti Gabah & Beras</div>
                                <div class="bg-white p-3 rounded-lg">✅ Verifikasi Dokumen & Legalitas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Kenapa Perlu Mendaftar -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-2xl">💡</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Kenapa Perlu Mendaftar?</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center p-6 border border-gray-100 rounded-xl">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">🤝</span>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Menjadi Mitra Resmi BULOG</h4>
                            <p class="text-gray-600 text-sm">
                                Menjadi bagian dari jaringan mitra pangan Perum BULOG Kantor Cabang Surakarta dengan status resmi dan terverifikasi
                            </p>
                        </div>
                        
                        <div class="text-center p-6 border border-gray-100 rounded-xl">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">🌾</span>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Akses Penawaran Komoditas</h4>
                            <p class="text-gray-600 text-sm">
                                Mendapatkan akses penawaran komoditas gabah dan beras dari BULOG secara berkala
                            </p>
                        </div>
                        
                        <div class="text-center p-6 border border-gray-100 rounded-xl">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">📈</span>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Kemitraan Berkelanjutan</h4>
                            <p class="text-gray-600 text-sm">
                                Peluang kemitraan jangka panjang dengan Perum BULOG Kantor Cabang Surakarta untuk stabilitas usaha
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 5. Bagaimana Langkah Seleksi -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-2xl">📋</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Tahapan Proses Menjadi Mitra</h2>
                    </div>
                    
                    <div class="relative">
                        <!-- Timeline -->
                        <div class="hidden md:block absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-gray-200"></div>
                        
                        <div class="space-y-8">
                            <!-- Step 1 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:text-right md:pr-8">
                                    <div class="bg-blue-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">1. Registrasi & Input Data Mitra</h4>
                                        <p class="text-gray-600 text-sm">
                                            Daftar akun, lengkapi data profil perusahaan, dan submit dokumen persyaratan sebagai calon mitra pangan
                                        </p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mx-4 text-white font-bold text-sm z-10">1</div>
                                <div class="flex-1 md:pl-8"></div>
                            </div>
                            
                            <!-- Step 2 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:pr-8"></div>
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mx-4 text-white font-bold text-sm z-10">2</div>
                                <div class="flex-1 md:pl-8">
                                    <div class="bg-green-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">2. Pengajuan Seleksi Mitra</h4>
                                        <p class="text-gray-600 text-sm">
                                            Ajukan permohonan seleksi mitra dengan melengkapi dokumen seperti Surat Permohonan, NIB, NPWP, dan lainnya
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 3 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:text-right md:pr-8">
                                    <div class="bg-yellow-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">3. Verifikasi & Evaluasi Admin</h4>
                                        <p class="text-gray-600 text-sm">
                                            Tim admin memverifikasi kelengkapan dokumen dan mengevaluasi kelayakan berdasarkan kriteria yang berlaku
                                        </p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mx-4 text-white font-bold text-sm z-10">3</div>
                                <div class="flex-1 md:pl-8"></div>
                            </div>
                            
                            <!-- Step 4 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:pr-8"></div>
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mx-4 text-white font-bold text-sm z-10">4</div>
                                <div class="flex-1 md:pl-8">
                                    <div class="bg-purple-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">4. Klasifikasi Mitra Pangan</h4>
                                        <p class="text-gray-600 text-sm">
                                            Mitra yang lolos seleksi akan diklasifikasikan berdasarkan kapasitas dan mutu untuk penawaran komoditas yang sesuai
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 5 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:text-right md:pr-8">
                                    <div class="bg-emerald-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">5. Penawaran Komoditas & Kerjasama</h4>
                                        <p class="text-gray-600 text-sm">
                                            Mitra resmi akan mendapatkan penawaran komoditas gabah dan beras secara berkala sesuai klasifikasi dan kebutuhan
                                        </p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mx-4 text-white font-bold text-sm z-10">5</div>
                                <div class="flex-1 md:pl-8"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="text-center bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-12 text-white">
                    <template v-if="!isAuthenticated">
                        <h2 class="text-xl font-semibold mb-4">Siap Menjadi Mitra Pangan BULOG Kantor Cabang Surakarta?</h2>
                        <p class="text-lg mb-8 text-blue-100">
                            Daftarkan perusahaan Anda sekarang dan bergabung dengan jaringan mitra pangan Perum BULOG Kantor Cabang Surakarta untuk penawaran komoditas gabah dan beras
                        </p>
                        <Link 
                            href="/register"
                            class="inline-flex items-center bg-white text-blue-600 px-6 py-3 rounded-xl font-bold text-base hover:bg-gray-50 transition-colors shadow-lg"
                        >
                            Daftar Sebagai Mitra Sekarang
                            <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L8 12.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </Link>
                    </template>
                    
                    <template v-else>
                        <h2 class="text-3xl font-bold mb-4">Selamat Datang, {{ user.name }}! 👋</h2>
                        <p class="text-xl mb-8 text-blue-100">
                            Anda sudah terdaftar sebagai <span class="font-bold capitalize">{{ user.role }}</span>. Akses dashboard untuk mengelola data dan pengajuan Anda
                        </p>
                        <Link 
                            :href="dashboardRoute"
                            class="inline-flex items-center bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-50 transition-colors shadow-lg"
                        >
                            Buka Dashboard
                            <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L8 12.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>