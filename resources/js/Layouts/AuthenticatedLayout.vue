<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import logoImg from '@/../../resources/assets/Images/bulog.png';
import { computed } from 'vue';

// Get user role from auth props
const role = computed(() => usePage().props.auth.user.role);

// Get dashboard route based on user role
const dashboardRoute = computed(() => {
    if (role.value === 'admin') {
        return route('admin.dashboard');
    } else if (role.value === 'mitra') {
        return route('mitra.dashboard');
    }
    return route('dashboard');
});

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-50">
            <!-- Top Navigation Bar -->
            <nav class="bg-white shadow-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between items-center">
                        <!-- Left side: Logo and Brand -->
                        <div class="flex items-center">
                            <Link :href="dashboardRoute" class="flex items-center">
                                <img :src="logoImg" alt="Logo Bulog" class="w-8 h-8 object-contain" />
                                <span class="ml-3 text-xl font-semibold text-gray-900">ASIMPENAS</span>
                            </Link>
                        </div>

                        <!-- Right side: User Menu -->
                        <div class="hidden sm:flex sm:items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 focus:outline-none">
                                        <div class="text-sm">
                                            {{ $page.props.auth.user.name }}
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm text-gray-500">Login sebagai</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $page.props.auth.user.email }}</p>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-2">
                                        <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Profil
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 w-full text-left">
                                        <svg class="mr-3 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Keluar
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="flex sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" 
                                    class="text-gray-500 hover:text-gray-600 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div v-show="showingNavigationDropdown" class="sm:hidden border-t border-gray-200">
                    <div class="space-y-1 px-4 py-4">
                        <div class="flex items-center mb-3">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</div>
                                <div class="text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                            </div>
                        </div>
                        <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                            Profil
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-red-600">
                            Keluar
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="py-6">
                <slot />
            </main>
        </div>
    </div>
</template>