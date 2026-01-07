<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
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
const selectedYear = ref(''); // Filter tahun

// Get unique years from users
const availableYears = computed(() => {
    const years = sortedUsers.value.map(user => {
        if (user.created_at) {
            return new Date(user.created_at).getFullYear();
        }
        return null;
    }).filter(year => year !== null);
    
    // Return unique years sorted descending
    return [...new Set(years)].sort((a, b) => b - a);
});

// Filter users based on search query and year
const filteredUsers = computed(() => {
    let filtered = sortedUsers.value;
    
    // Filter by year
    if (selectedYear.value) {
        filtered = filtered.filter(user => {
            if (user.created_at) {
                const userYear = new Date(user.created_at).getFullYear();
                return userYear === parseInt(selectedYear.value);
            }
            return false;
        });
    }
    
    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(user => {
            return (
                user.name?.toLowerCase().includes(query) ||
                user.email?.toLowerCase().includes(query) ||
                user.role?.toLowerCase().includes(query)
            );
        });
    }
    
    return filtered;
});

// Add sorting state
const sortBy = ref('created_at');
const sortOrder = ref('desc');

// Pagination state
const currentPage = ref(1);
const itemsPerPage = ref(10);
const itemsPerPageOptions = [10, 20, 50, 100];

// Computed property for total pages
const totalPages = computed(() => {
    return Math.ceil(filteredUsers.value.length / itemsPerPage.value);
});

// Computed property for paginated users
const paginatedUsers = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return filteredUsers.value.slice(startIndex, endIndex);
});

// Computed property for visible page numbers
const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    const current = currentPage.value;
    
    // Always show first page
    pages.push(1);
    
    // Calculate range around current page
    let rangeStart = Math.max(2, current - 2);
    let rangeEnd = Math.min(total - 1, current + 2);
    
    // Add ellipsis after first page if needed
    if (rangeStart > 2) {
        pages.push('...');
    }
    
    // Add pages around current page
    for (let i = rangeStart; i <= rangeEnd; i++) {
        pages.push(i);
    }
    
    // Add ellipsis before last page if needed
    if (rangeEnd < total - 1) {
        pages.push('...');
    }
    
    // Always show last page if there's more than one page
    if (total > 1) {
        pages.push(total);
    }
    
    return pages;
});

// Methods for pagination
const goToPage = (page) => {
    if (typeof page === 'number' && page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const resetPagination = () => {
    currentPage.value = 1;
};

// Method to change items per page
const changeItemsPerPage = (value) => {
    itemsPerPage.value = value;
    resetPagination();
};

// Watch search query to reset pagination
watch(searchQuery, () => {
    resetPagination();
});

// Watch selected year to reset pagination
watch(selectedYear, () => {
    resetPagination();
});

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
            <div class="mb-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <!-- Search Input -->
                <div class="relative w-full sm:flex-1">
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
                    <button 
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        title="Hapus pencarian"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Items Per Page Selector -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="itemsPerPage" class="text-sm text-gray-700 whitespace-nowrap">
                        Tampilkan:
                    </label>
                    <select
                        id="itemsPerPage"
                        v-model="itemsPerPage"
                        @change="changeItemsPerPage(itemsPerPage)"
                        class="block w-full sm:w-auto py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>

                <!-- Year Filter -->
                <div class="relative w-full sm:w-auto">
                    <select 
                        v-model="selectedYear"
                        class="block w-full py-2.5  text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-purple-500 focus:border-purple-500"
                    >
                        <option value="">Semua Tahun</option>
                        <option v-for="year in availableYears" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
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
                            <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm whitespace-nowrap">{{ user.name }}</td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">{{ user.email }}</td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">
                                    <span :class="['px-2 py-1 text-xs rounded-full font-medium', getRoleBadgeClass(user.role)]">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">{{ formatDate(user.created_at) }}</td>
                            </tr>
                            <tr v-if="paginatedUsers.length === 0">
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    {{ searchQuery ? 'Tidak ada user yang sesuai dengan pencarian.' : 'Belum ada data user.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Info Text -->
                        <div class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-medium">{{ ((currentPage - 1) * itemsPerPage) + 1 }}</span>
                            sampai 
                            <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }}</span>
                            dari 
                            <span class="font-medium">{{ filteredUsers.length }}</span>
                            user
                        </div>

                        <!-- Pagination Buttons -->
                        <div class="flex items-center gap-2">
                            <!-- Previous Button -->
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                Previous
                            </button>

                            <!-- Page Numbers -->
                            <template v-for="(page, index) in visiblePages" :key="index">
                                <button
                                    v-if="page === '...'"
                                    disabled
                                    class="min-w-[40px] px-3 py-2 text-sm font-medium text-gray-400 bg-white cursor-default"
                                >
                                    {{ page }}
                                </button>
                                <button
                                    v-else
                                    @click="goToPage(page)"
                                    :class="[
                                        'min-w-[40px] px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                                        page === currentPage
                                            ? 'bg-purple-600 text-white'
                                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </template>

                            <!-- Next Button -->
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>