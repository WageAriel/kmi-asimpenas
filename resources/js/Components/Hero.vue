<script setup>
import { Link } from '@inertiajs/vue3';
import logoImg from '@/../../resources/assets/Images/bulog.png'; 
</script>

<template>
    <section class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header dengan Logo dan Auth Buttons -->
        <div class="w-full px-4 py-6">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                        <img :src="logoImg" alt="Logo Bulog" class="w-7 h-7 object-contain" />
                    </div>
                    <div>
                        <span class="text-2xl font-bold text-gray-800">ASIMPENAS</span>
                        <p class="text-xs text-gray-500">Aplikasi Seleksi Mitra</p>
                    </div>
                </div>

                <div class="flex gap-3">
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
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-4 py-12">
            <!-- Intro Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Selamat Datang di <span class="text-blue-600">ASIMPENAS</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                    Platform digital untuk proses seleksi mitra bisnis yang ingin melakukan Purchase Order (PO) dengan standar dan ketentuan yang ketat
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link 
                        href="/register"
                        class="bg-blue-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-blue-700 transition-colors shadow-lg"
                    >
                        Daftar untuk Seleksi PO
                    </Link>
                    <Link 
                        href="/login"
                        class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-50 transition-colors"
                    >
                        Sudah Punya Akun?
                    </Link>
                </div>
            </div>

            <!-- Main Information Sections -->
            <div class="space-y-16">
                
                <!-- 1. Apa itu ASIMPENAS -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-2xl">❓</span>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Apa itu ASIMPENAS?</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <p class="text-lg text-gray-600 mb-4 leading-relaxed">
                                <strong>ASIMPENAS</strong> (Aplikasi Seleksi Mitra dan Penawaran Komoditas) adalah platform digital 
                                yang digunakan untuk menyeleksi mitra bisnis yang ingin melakukan Purchase Order (PO) sesuai dengan ketentuan dan standar yang berlaku.
                            </p>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Seleksi mitra untuk keperluan Purchase Order
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Evaluasi berdasarkan ketentuan yang berlaku
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pengelolaan penawaran komoditas
                                </li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-xl">
                            <h4 class="font-semibold text-gray-800 mb-3">Tujuan Utama:</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-white p-3 rounded-lg">🎯 Menyeleksi Mitra PO yang Qualified</div>
                                <div class="bg-white p-3 rounded-lg">📋 Memastikan Kepatuhan Ketentuan</div>
                                <div class="bg-white p-3 rounded-lg">💼 Evaluasi Kapasitas Bisnis</div>
                                <div class="bg-white p-3 rounded-lg">✅ Verifikasi Dokumen & Legalitas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Kenapa Perlu Mendaftar -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-2xl">💡</span>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Kenapa Perlu Mengikuti Seleksi?</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center p-6 border border-gray-100 rounded-xl">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">💰</span>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Akses Purchase Order</h4>
                            <p class="text-gray-600 text-sm">
                                Hanya mitra yang lolos seleksi yang berhak mendapatkan dan melaksanakan Purchase Order
                            </p>
                        </div>
                        
                        <div class="text-center p-6 border border-gray-100 rounded-xl">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">🔒</span>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Standar Kualitas Terjamin</h4>
                            <p class="text-gray-600 text-sm">
                                Proses seleksi memastikan hanya mitra berkualitas yang dapat berpartisipasi dalam PO
                            </p>
                        </div>
                        
                        <div class="text-center p-6 border border-gray-100 rounded-xl">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">📈</span>
                            </div>
                            <h4 class="font-semibold text-gray-800 mb-2">Kemitraan Jangka Panjang</h4>
                            <p class="text-gray-600 text-sm">
                                Mitra yang lolos seleksi berpotensi mendapat PO berkelanjutan sesuai performa
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 3. Bagaimana Langkah Seleksi -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-2xl">📋</span>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Tahapan Proses Seleksi Mitra PO</h2>
                    </div>
                    
                    <div class="relative">
                        <!-- Timeline -->
                        <div class="hidden md:block absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-gray-200"></div>
                        
                        <div class="space-y-8">
                            <!-- Step 1 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:text-right md:pr-8">
                                    <div class="bg-blue-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">1. Registrasi & Pengajuan</h4>
                                        <p class="text-gray-600 text-sm">
                                            Daftar akun, lengkapi profil perusahaan, dan submit dokumen persyaratan untuk mengikuti seleksi PO
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
                                        <h4 class="font-semibold text-gray-800 mb-2">2. Verifikasi Kelengkapan</h4>
                                        <p class="text-gray-600 text-sm">
                                            Tim verifikasi akan memeriksa kelengkapan dokumen, legalitas, dan keabsahan data perusahaan
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 3 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:text-right md:pr-8">
                                    <div class="bg-yellow-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">3. Evaluasi Kriteria Seleksi</h4>
                                        <p class="text-gray-600 text-sm">
                                            Penilaian berdasarkan ketentuan yang berlaku: kapasitas produksi, kualitas, pengalaman, dan kemampuan finansial
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
                                        <h4 class="font-semibold text-gray-800 mb-2">4. Pengumuman Hasil Seleksi</h4>
                                        <p class="text-gray-600 text-sm">
                                            Notifikasi hasil seleksi dan informasi selanjutnya untuk mitra yang lolos seleksi
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 5 -->
                            <div class="relative flex items-center">
                                <div class="flex-1 md:text-right md:pr-8">
                                    <div class="bg-emerald-50 p-6 rounded-xl">
                                        <h4 class="font-semibold text-gray-800 mb-2">5. Penerbitan & Pelaksanaan PO</h4>
                                        <p class="text-gray-600 text-sm">
                                            Mitra yang lolos seleksi akan menerima Purchase Order dan melaksanakan sesuai ketentuan kontrak
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
                    <h2 class="text-3xl font-bold mb-4">Siap Mengikuti Seleksi Mitra PO?</h2>
                    <p class="text-xl mb-8 text-blue-100">
                        Daftarkan perusahaan Anda untuk mengikuti proses seleksi dan berpeluang mendapatkan Purchase Order dari kami
                    </p>
                    <Link 
                        href="/register"
                        class="inline-flex items-center bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-50 transition-colors shadow-lg"
                    >
                        Daftar Seleksi Sekarang
                        <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L8 12.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>