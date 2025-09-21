<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

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
        route: 'mitra.dashboard',
        icon: 'M9.12 9.17V5.43C9.12 5.16 9.02 4.91 8.84 4.73C8.66 4.55 8.41 4.45 8.14 4.45H5.37C5.10 4.45 4.85 4.55 4.67 4.73C4.49 4.91 4.39 5.16 4.39 5.43V8.19C4.39 8.46 4.49 8.71 4.67 8.89C4.85 9.07 5.10 9.17 5.37 9.17H9.12ZM5.37 2.46H8.14C8.93 2.46 9.68 2.77 10.24 3.33C10.80 3.89 11.11 4.64 11.11 5.43V10.16C11.11 10.43 11.01 10.68 10.83 10.86C10.65 11.04 10.40 11.14 10.13 11.14H5.37C4.58 11.14 3.83 10.83 3.27 10.27C2.71 9.71 2.40 8.96 2.40 8.19V5.43C2.40 4.64 2.71 3.89 3.27 3.33C3.83 2.77 4.58 2.46 5.37 2.46ZM14.69 4.23V9.17H19.63C19.90 9.17 20.15 9.07 20.33 8.89C20.51 8.71 20.61 8.46 20.61 8.19V4.23C20.61 3.96 20.51 3.71 20.33 3.53C20.15 3.35 19.90 3.25 19.63 3.25H15.67C15.40 3.25 15.15 3.35 14.97 3.53C14.79 3.71 14.69 3.96 14.69 4.23ZM19.63 1.26C20.42 1.26 21.17 1.57 21.73 2.13C22.29 2.69 22.60 3.44 22.60 4.23V8.19C22.60 8.96 22.29 9.71 21.73 10.27C21.17 10.83 20.42 11.14 19.63 11.14H13.71C13.44 11.14 13.19 11.04 13.01 10.86C12.83 10.68 12.73 10.43 12.73 10.16V4.23C12.73 3.44 13.04 2.69 13.60 2.13C14.16 1.57 14.91 1.26 15.67 1.26H19.63ZM14.69 14.58V18.32C14.69 18.59 14.79 18.84 14.97 19.02C15.15 19.20 15.40 19.30 15.67 19.30H18.44C18.71 19.30 18.96 19.20 19.14 19.02C19.32 18.84 19.42 18.59 19.42 18.32V15.56C19.42 15.29 19.32 15.04 19.14 14.86C18.96 14.68 18.71 14.58 18.44 14.58H14.69ZM18.44 21.29H15.67C14.88 21.29 14.13 20.98 13.57 20.42C13.01 19.86 12.70 19.11 12.70 18.32V13.59C12.70 13.32 12.80 13.07 12.98 12.89C13.16 12.71 13.41 12.61 13.68 12.61H18.44C19.23 12.61 19.98 12.92 20.54 13.48C21.10 14.04 21.41 14.79 21.41 15.56V18.32C21.41 19.11 21.10 19.86 20.54 20.42C19.98 20.98 19.23 21.29 18.44 21.29ZM9.14 14.58H4.20C3.93 14.58 3.68 14.68 3.50 14.86C3.32 15.04 3.22 15.29 3.22 15.56V19.52C3.22 19.79 3.32 20.04 3.50 20.22C3.68 20.40 3.93 20.50 4.20 20.50H8.16C8.43 20.50 8.68 20.40 8.86 20.22C9.04 20.04 9.14 19.79 9.14 19.52V14.58ZM4.20 22.49C3.41 22.49 2.66 22.18 2.10 21.62C1.54 21.06 1.23 20.31 1.23 19.52V15.56C1.23 14.77 1.54 14.02 2.10 13.46C2.66 12.90 3.41 12.59 4.20 12.59H10.12C10.39 12.59 10.64 12.69 10.82 12.87C11.00 13.05 11.10 13.30 11.10 13.57V19.52C11.10 20.31 10.79 21.06 10.23 21.62C9.67 22.18 8.92 22.49 8.16 22.49H4.20Z'
    },
    {
        name: 'Pengajuan Seleksi',
        route: 'mitra.pengajuan-seleksi.index',
        icon: 'M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z'
    },
    {
        name: 'Klasifikasi Mitra',
        route: 'mitra.klasifikasi-mitra.index',
        icon: 'M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z'
    },
    {
        name: 'Hasil Seleksi',
        route: 'mitra.hasil-seleksi',
        icon: 'M22.3 4.7L17.5 0.2C17.4 0.1 17.2 0 17 0H6.4C6.1 0 5.8 0.2 5.6 0.5C5.4 0.7 5.2 1.1 5.2 1.6V3.6H2.3C2 3.6 1.7 3.8 1.5 4.1C1.3 4.4 1.1 4.7 1.1 5.2V21.4C1.1 21.9 1.3 22.2 1.5 22.5C1.7 22.8 2 23 2.3 23H17C17.3 23 17.6 22.8 17.8 22.5C18 22.2 18.2 21.9 18.2 21.4V19.4H21.1C21.4 19.4 21.7 19.2 21.9 18.9C22.1 18.6 22.3 18.3 22.3 17.8V5.2C22.3 5 22.2 4.9 22.1 4.8L22.3 4.7ZM16.8 21.4C16.8 21.5 16.7 21.6 16.6 21.7C16.5 21.8 16.4 21.8 16.3 21.8H2.3C2.2 21.8 2.1 21.8 2 21.7C1.9 21.6 1.8 21.5 1.8 21.4V5.2C1.8 5.1 1.9 5 2 4.9C2.1 4.8 2.2 4.8 2.3 4.8H12.2L16.8 9V21.4ZM20.9 17.8C20.9 17.9 20.8 18 20.7 18.1C20.6 18.2 20.5 18.2 20.4 18.2H18.2V8.8C18.2 8.6 18.1 8.5 18 8.4L13.1 3.8C13 3.7 12.9 3.6 12.7 3.6H6.2V1.6C6.2 1.5 6.3 1.4 6.4 1.3C6.5 1.2 6.6 1.2 6.7 1.2H16.4L20.9 5.5V17.8ZM13.3 14.2C13.3 14.4 13.2 14.6 13.1 14.7C13 14.8 12.8 14.9 12.6 14.9H6.4C6.2 14.9 6 14.8 5.9 14.7C5.8 14.6 5.7 14.4 5.7 14.2C5.7 14 5.8 13.8 5.9 13.7C6 13.6 6.2 13.5 6.4 13.5H12.6C12.8 13.5 13 13.6 13.1 13.7C13.2 13.8 13.3 14 13.3 14.2ZM13.3 17.8C13.3 18 13.2 18.2 13.1 18.3C13 18.4 12.8 18.5 12.6 18.5H6.4C6.2 18.5 6 18.4 5.9 18.3C5.8 18.2 5.7 18 5.7 17.8C5.7 17.6 5.8 17.4 5.9 17.3C6 17.2 6.2 17.1 6.4 17.1H12.6C12.8 17.1 13 17.2 13.1 17.3C13.2 17.4 13.3 17.6 13.3 17.8Z'
    },
];
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar untuk Desktop -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <!-- Sidebar -->
                <div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
                    <!-- Logo & Brand -->
                    <div class="flex items-center flex-shrink-0 px-4 mb-8">
                        <Link href="/" class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L1 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-11-5z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900">ASIMPENAS</h1>
                                <p class="text-xs text-gray-500">Portal Mitra</p>
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
                            <svg 
                                :class="[
                                    'mr-3 flex-shrink-0 h-6 w-6',
                                    isActiveRoute(item.route) ? 'text-blue-600' : 'text-black group-hover:text-blue-500'
                                ]" 
                                fill="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path :d="item.icon"/>
                            </svg>
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
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L1 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-11-5z"/>
                                </svg>
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