<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { HomeIcon, DocumentIcon, DocumentTextIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';
import logoImg from '@/../../resources/assets/Images/bulog.png'; 

const page = usePage();
const user = computed(() => page.props.auth?.user);

// State untuk mobile sidebar dan user dropdown
const showingSidebar = ref(false);
const showingUserDropdown = ref(false);

// Ref untuk dropdown element
const userDropdownRef = ref(null);

// Helper untuk mengecek route aktif
const isActiveRoute = (routeName) => {
    return route().current(routeName);
};

// Function untuk menutup dropdown ketika klik di luar
const handleClickOutside = (event) => {
    if (userDropdownRef.value && !userDropdownRef.value.contains(event.target)) {
        showingUserDropdown.value = false;
    }
};

// Event listeners
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Menu items
const menuItems = [
    {
        name: 'Dashboard',
        route: 'admin.dashboard',
        icon: HomeIcon
    },
    {
        name: 'Daftar Mitra',
        route: 'admin.daftar-mitra.index',
        icon: DocumentIcon
    },
    {
        name: 'Daftar Seleksi Mitra',
        route: 'admin.seleksi-mitra.index',
        icon: DocumentTextIcon
    },
    {
        name: 'Daftar Klasifikasi Mitra',
        route: 'admin.klasifikasi-mitra.index',
        icon: DocumentTextIcon
    },
    {
        name: 'Daftar Hasil Seleksi Mitra',
        route: 'admin.hasil-seleksi-mitra.index',
        icon: CheckCircleIcon
    }
];
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar untuk Desktop -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-66">
                <!-- Sidebar -->
                <div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
                    <!-- Logo & Brand -->
                    <div class="flex items-center flex-shrink-0 px-4 py-2">
                        <Link href="/" class="flex items-center">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                                <img :src="logoImg" alt="Logo Bulog" class="w-7 h-7 object-contain" />
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900">ASIMPENAS</h1>
                                <p class="text-xs text-gray-500">Portal Admin</p>
                            </div>
                        </Link>
                    </div>

                    <!-- Navigation -->
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <Link
                            v-for="item in menuItems"
                            :key="item.route"
                            :href="route(item.route)"
                            :class="[
                                'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                                isActiveRoute(item.route)
                                    ? 'bg-blue-100 text-blue-600 border-r-4'
                                    : 'text-black hover:bg-gray-50 hover:text-blue-500'
                            ]"
                        >
                            <component
                                :is="item.icon"
                                class="mr-3 flex-shrink-0 h-6 w-6"
                                :class="isActiveRoute(item.route) ? 'text-blue-600' : 'text-black group-hover:text-blue-500'"
                            />
                            {{ item.name }}
                        </Link>
                    </nav>

                    <!-- User Section di Bottom Sidebar -->
                    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                        <div class="flex-shrink-0 w-full group block" ref="userDropdownRef">
                            <div class="flex items-center">
                                <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ user?.name || 'PT. Mitra Teknologi' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ user?.email || 'admin@mitra.com' }}</p>
                                </div>
                                <button 
                                    @click="showingUserDropdown = !showingUserDropdown"
                                    class="ml-2 flex-shrink-0 p-1 text-gray-400 hover:text-gray-600 focus:outline-none"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- User Dropdown -->
                            <div v-show="showingUserDropdown" 
                                 class="absolute bottom-16 left-4 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900">{{ user?.name || 'PT. Mitra Teknologi' }}</p>
                                    <p class="text-xs text-gray-500">{{ user?.email || 'admin@mitra.com' }}</p>
                                </div>

                                <!-- Menu Items -->
                                <Link 
                                    :href="route('profile.edit')"
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                    @click="showingUserDropdown = false"
                                >
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                                    </svg>
                                    Pengaturan Akun
                                </Link>

                                <Link 
                                    :href="route('logout')" 
                                    method="post" 
                                    as="button"
                                    class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors"
                                    @click="showingUserDropdown = false"
                                >
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                                    </svg>
                                    Keluar
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar overlay -->
        <div v-show="showingSidebar" class="fixed inset-0 flex z-40 lg:hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="showingSidebar = false"></div>
            
            <!-- Mobile sidebar -->
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button
                        @click="showingSidebar = false"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    >
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile sidebar content -->
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center px-4 mb-8">
                        <Link href="/" class="flex items-center">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                                    <img :src="logoImg" alt="Logo Bulog" class="w-6 h-6 object-contain" />
                                </div>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900">ASIMPENAS</h1>
                                <p class="text-xs text-gray-500">Portal Mitra</p>
                            </div>
                        </Link>
                    </div>

                    <!-- Mobile Navigation -->
                    <nav class="mt-5 px-2 space-y-1">
                        <Link
                            v-for="item in menuItems"
                            :key="item.route"
                            :href="route(item.route)"
                            :class="[
                                'group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors duration-200',
                                isActiveRoute(item.route)
                                    ? 'bg-blue-100 text-blue-900'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                            @click="showingSidebar = false"
                        >
                            <svg 
                                :class="[
                                    'mr-4 flex-shrink-0 h-6 w-6',
                                    isActiveRoute(item.route) ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'
                                ]" 
                                fill="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path :d="item.icon"/>
                            </svg>
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>

                <!-- Mobile User Section -->
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-base font-medium text-gray-800">{{ user?.name || 'PT. Mitra Teknologi' }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ user?.email || 'admin@mitra.com' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top bar untuk mobile -->
            <div class="lg:hidden">
                <div class="flex items-center justify-between bg-white border-b border-gray-200 px-4 py-2">
                    <button
                        @click="showingSidebar = true"
                        class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">ASIMPENAS Portal Mitra</h1>
                    <div></div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <slot />
            </main>
        </div>
    </div>
</template>