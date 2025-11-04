<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Accept data from the controller as props
const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

// Sort users by created_at (newest first)
const sortedUsers = computed(() => {
    let sorted = [...props.users];
    
    if (sortBy.value === 'role') {
        // Custom sorting for roles
        const roleOrder = {
            'super admin': 1,
            'admin': 2,
            'mitra': 3
        };
        
        sorted.sort((a, b) => {
            return roleOrder[a.role] - roleOrder[b.role];
        });
    } else {
        // Default sorting by created_at
        sorted.sort((a, b) => {
            return new Date(b.created_at) - new Date(a.created_at);
        });
    }
    
    return sorted;
});

// Add sort function
const toggleSort = (column) => {
    if (column === 'role') {
        sortBy.value = 'role';
    } else {
        sortBy.value = 'created_at';
    }
};

// Add search functionality
const searchQuery = ref('');

// Filter users based on search query
const filteredUsers = computed(() => {
    if (!searchQuery.value) return sortedUsers.value;
    
    const query = searchQuery.value.toLowerCase();
    return sortedUsers.value.filter(user => {
        return (
            user.name?.toLowerCase().includes(query) ||
            user.email?.toLowerCase().includes(query) ||
            user.role?.toLowerCase().includes(query)
        );
    });
});

// Add sorting state
const sortBy = ref('created_at');
const sortOrder = ref('desc');

// Format date
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

// Get role badge class
const getRoleBadgeClass = (role) => {
    switch (role) {
        case 'admin':
            return 'bg-purple-100 text-purple-800';
        case 'super admin':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-blue-100 text-blue-800';
    }
};
</script>

<template>
    <Head title="Daftar User - ASIMPENAS" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar User</h2>
                <p class="text-gray-600">Kelola daftar user yang terdaftar di sistem ASIMPENAS</p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Daftar User</h3>
                        <p class="text-purple-100">
                            Berikut adalah daftar user yang terdaftar di sistem ASIMPENAS.
                        </p>
                    </div>
                </div>
            </div>

            <!--Search Bar-->
            <div class="mb-4">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari user..."
                        v-model="searchQuery"
                    />
                </div>
            </div>
            
            <!-- Tabel Daftar User -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar User</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th 
                                    @click="toggleSort('role')" 
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                >
                                    Role
                                    <span v-if="sortBy === 'role'" class="ml-1">▼</span>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm whitespace-nowrap">{{ user.name }}</td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">{{ user.email }}</td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">
                                    <span :class="['px-2 py-1 text-xs rounded-full font-medium', getRoleBadgeClass(user.role)]">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">{{ formatDate(user.created_at) }}</td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    {{ searchQuery ? 'Tidak ada user yang sesuai dengan pencarian.' : 'Belum ada data user.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>