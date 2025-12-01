<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';

const props = defineProps({
    purchaseOrder: Object
});

// Debug: log data yang diterima dari backend
console.log('Purchase Order Data:', props.purchaseOrder);
console.log('Total Harga:', props.purchaseOrder.total_harga, typeof props.purchaseOrder.total_harga);
console.log('Total Kuantum:', props.purchaseOrder.total_kuantum, typeof props.purchaseOrder.total_kuantum);
console.log('Total Nilai:', props.purchaseOrder.total_nilai, typeof props.purchaseOrder.total_nilai);

const formatCurrency = (value) => {
    // Handle null, undefined, or NaN values
    if (value === null || value === undefined || isNaN(value)) {
        return '0';
    }
    
    // Ensure value is a number
    const numValue = Number(value);
    if (isNaN(numValue)) {
        return '0';
    }
    
    return new Intl.NumberFormat('id-ID').format(numValue);
};

const downloadSuratPermohonan = () => {
    window.open(route('super-admin.purchase-orders.surat-permohonan', props.purchaseOrder.id), '_blank');
};

const downloadFormPenawaran = () => {
    window.open(route('super-admin.purchase-orders.form-penawaran', props.purchaseOrder.id), '_blank');
};

const downloadCombinedPdf = () => {
    window.open(route('super-admin.purchase-orders.combined-pdf', props.purchaseOrder.id), '_blank');
};

// Extract nomor urut saja dari no_surat
// Format: NO_VMS/001/MITRA/11/2025 -> ambil hanya 001
const getPoNumber = () => {
    if (!props.purchaseOrder.no_surat) return '-';
    const parts = props.purchaseOrder.no_surat.split('/');
    // Ambil bagian kedua (index 1) yang merupakan nomor urut
    return parts[1] || '-';
};
</script>

<template>
    <Head title="Detail Purchase Order - ASIMPENAS" />

    <SuperAdminLayout>
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button 
                                    @click="$inertia.visit(route('super-admin.purchase-orders.index'))"
                                    class="text-gray-400 hover:text-gray-600"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">Detail Purchase Order</h1>
                                    <p class="text-gray-600 mt-1">PO ID #{{ getPoNumber() }} - {{ purchaseOrder.nama_perusahaan }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <Link 
                                    :href="route('super-admin.purchase-orders.edit', purchaseOrder.id)"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Edit</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Information -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Purchase Order</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Nama Perusahaan</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.nama_perusahaan }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Jenis Komoditas</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.jenis_komoditas }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Jenis Pengadaan</h3>
                                <span :class="[
                                    'inline-flex px-3 py-1 text-sm font-semibold rounded-full mt-1',
                                    purchaseOrder.jenis_pengadaan === 'PSO' 
                                        ? 'bg-blue-100 text-blue-800' 
                                        : 'bg-green-100 text-green-800'
                                ]">
                                    {{ purchaseOrder.jenis_pengadaan }}
                                </span>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Jumlah Kualitas</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.items.length }} Kualitas Berbeda</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Dibuat Oleh</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.created_by || 'System' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Keuangan Total</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-blue-600 uppercase tracking-wide">Total Harga</h3>
                                <p class="mt-2 text-2xl font-bold text-blue-900">Rp {{ formatCurrency(purchaseOrder.total_harga || 0) }}</p>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-green-600 uppercase tracking-wide">Total Kuantum</h3>
                                <p class="mt-2 text-2xl font-bold text-green-900">{{ formatCurrency(purchaseOrder.total_kuantum || 0) }} <span class="text-lg">Kg</span></p>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-purple-600 uppercase tracking-wide">Total Nilai</h3>
                                <p class="mt-2 text-2xl font-bold text-purple-900">Rp {{ formatCurrency(purchaseOrder.total_nilai || 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quality Items -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Detail Kualitas Barang</h2>
                        <p class="text-gray-600 mt-1">{{ purchaseOrder.items.length }} item kualitas berbeda</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="(item, index) in purchaseOrder.items" :key="item.id" class="bg-gray-50 p-4 rounded-lg border">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-md font-medium text-gray-800">Kualitas {{ index + 1 }}</h3>
                                    <span class="text-sm text-gray-500">ID: #{{ item.id }}</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kualitas</span>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ item.kualitas }}</p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Harga/Kg</span>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">Rp {{ formatCurrency(item.harga) }}</p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kuantum</span>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ formatCurrency(item.kuantum) }} Kg</p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nilai Total</span>
                                        <p class="mt-1 text-sm font-semibold text-green-600">Rp {{ formatCurrency(item.nilai) }}</p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Komplek</span>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ item.komplek_pergudangan }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div v-if="purchaseOrder.agenda_no || purchaseOrder.tanggal_terima || purchaseOrder.paraf || purchaseOrder.kontrak_yll" class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Tambahan</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-if="purchaseOrder.agenda_no">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Agenda No</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.agenda_no }}</p>
                            </div>
                            
                            <div v-if="purchaseOrder.tanggal_terima">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tanggal Terima</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.tanggal_terima }}</p>
                            </div>
                            
                            <div v-if="purchaseOrder.paraf">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Paraf</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.paraf }}</p>
                            </div>
                            
                            <div v-if="purchaseOrder.kontrak_yll">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Kontrak YLL</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.kontrak_yll }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Metadata</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">ID Purchase Order</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">#{{ getPoNumber() }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tanggal Dibuat</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseOrder.created_at }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Generate Dokumen PDF</h2>
                        <p class="text-gray-600 mt-1">Download dokumen surat permohonan dan form penawaran</p>
                    </div>
                    <div class="p-6">
                        <!-- Combined PDF Button (Featured) -->
                        <div class="mb-6">
                            <button
                                @click="downloadCombinedPdf"
                                class="w-full flex items-center justify-center space-x-3 bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-lg font-medium transition-colors duration-200 shadow-lg"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="text-center">
                                    <div class="font-semibold text-lg">Download Purchase Order Lengkap</div>
                                    <div class="text-sm text-blue-200">Surat Permohonan + Form Penawaran dalam 1 PDF</div>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Separator -->
                        <div class="relative mb-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">atau download terpisah</span>
                            </div>
                        </div>
                        
                        <!-- Individual PDF Buttons -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button
                                @click="downloadSuratPermohonan"
                                class="flex items-center justify-center space-x-3 bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-lg font-medium transition-colors duration-200"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="text-left">
                                    <div class="font-semibold">Surat Permohonan</div>
                                    <div class="text-sm text-red-200">Download PDF surat permohonan</div>
                                </div>
                            </button>
                            
                            <button
                                @click="downloadFormPenawaran"
                                class="flex items-center justify-center space-x-3 bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg font-medium transition-colors duration-200"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="text-left">
                                    <div class="font-semibold">Form Penawaran</div>
                                    <div class="text-sm text-green-200">Download PDF form penawaran</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>