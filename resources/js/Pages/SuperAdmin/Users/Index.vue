<script setup>
import { Head } from '@inertiajs/vue3';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';
import { ref, computed } from 'vue';
import axios from 'axios';

// Accept data from the controller as props
const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

// Sort users by created_at (newest first)
const sortedUsers = computed(() => {
    return [...props.users].sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at);
    });
});

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

// Format date
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    });
};

// Get role badge color
const getRoleBadgeColor = (role) => {
    switch(role) {
        case 'super admin':
        case 'super_admin':
            return 'bg-red-100 text-red-800';
        case 'admin':
            return 'bg-blue-100 text-blue-800';
        case 'mitra':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Get role display name
const getRoleDisplayName = (role) => {
    switch(role) {
        case 'super admin':
        case 'super_admin':
            return 'Super Admin';
        case 'admin':
            return 'Admin';
        case 'mitra':
            return 'Mitra';
        default:
            return role;
    }
};

// Edit functionality
const showEditModal = ref(false);
const selectedUserToEdit = ref(null);
const isUpdating = ref(false);
const updateError = ref(null);
const formData = ref({
    name: '',
    email: '',
    role: '',
    password: ''
});
const formErrors = ref({});

// Success Alert Modal
const showSuccessAlert = ref(false);
const successMessage = ref('');

const showSuccess = (message) => {
    successMessage.value = message;
    showSuccessAlert.value = true;
    setTimeout(() => {
        showSuccessAlert.value = false;
        window.location.reload();
    }, 2000);
};

// Create functionality
const showCreateModal = ref(false);
const isCreating = ref(false);
const createError = ref(null);
const createFormData = ref({
    name: '',
    email: '',
    role: '',
    password: ''
});
const createFormErrors = ref({});

const openCreateModal = () => {
    createFormData.value = {
        name: '',
        email: '',
        role: '',
        password: ''
    };
    createFormErrors.value = {};
    createError.value = null;
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createFormData.value = {
        name: '',
        email: '',
        role: '',
        password: ''
    };
    createFormErrors.value = {};
    createError.value = null;
    isCreating.value = false;
};

const createUser = async () => {
    isCreating.value = true;
    createError.value = null;
    createFormErrors.value = {};

    try {
        await axios.post('/users', createFormData.value);
        
        // Close modal and show success message
        closeCreateModal();
        showSuccess('User berhasil ditambahkan!');
    } catch (error) {
        console.error('Error creating user:', error);
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                createFormErrors.value = error.response.data.errors;
            } else if (error.response.data.message) {
                createError.value = error.response.data.message;
            }
        } else {
            createError.value = 'Terjadi kesalahan saat menambahkan user. Silakan coba lagi.';
        }
        isCreating.value = false;
    }
};

const openEditModal = (user) => {
    selectedUserToEdit.value = { ...user };
    formData.value = {
        name: user.name,
        email: user.email,
        role: user.role,
        password: ''
    };
    formErrors.value = {};
    updateError.value = null;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    selectedUserToEdit.value = null;
    formData.value = {
        name: '',
        email: '',
        role: '',
        password: ''
    };
    formErrors.value = {};
    updateError.value = null;
    isUpdating.value = false;
};

const saveUser = async () => {
    if (!selectedUserToEdit.value) return;

    isUpdating.value = true;
    updateError.value = null;
    formErrors.value = {};

    try {
        const dataToSend = {
            name: formData.value.name,
            email: formData.value.email,
            role: formData.value.role
        };

        // Only include password if it's not empty
        if (formData.value.password) {
            dataToSend.password = formData.value.password;
        }

        await axios.put(`/users/${selectedUserToEdit.value.id}`, dataToSend);
        
        // Close modal and show success message
        closeEditModal();
        showSuccess('User berhasil diperbarui!');
    } catch (error) {
        console.error('Error updating user:', error);
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                formErrors.value = error.response.data.errors;
            } else if (error.response.data.message) {
                updateError.value = error.response.data.message;
            }
        } else {
            updateError.value = 'Terjadi kesalahan saat memperbarui user. Silakan coba lagi.';
        }
        isUpdating.value = false;
    }
};

// Delete functionality
const showDeleteModal = ref(false);
const selectedUserToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref(null);

const openDeleteModal = (user) => {
    selectedUserToDelete.value = user;
    showDeleteModal.value = true;
    deleteError.value = null;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedUserToDelete.value = null;
    deleteError.value = null;
    isDeleting.value = false;
};

const confirmDelete = async () => {
    if (!selectedUserToDelete.value) return;

    isDeleting.value = true;
    deleteError.value = null;

    try {
        await axios.delete(`/users/${selectedUserToDelete.value.id}`);
        
        // Close modal and show success message
        closeDeleteModal();
        showSuccess('User berhasil dihapus!');
    } catch (error) {
        console.error('Error deleting user:', error);
        if (error.response && error.response.data && error.response.data.message) {
            deleteError.value = error.response.data.message;
        } else {
            deleteError.value = 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.';
        }
        isDeleting.value = false;
    }
};

// View detail - opens edit modal
const viewUserDetail = (user) => {
    openEditModal(user);
};
</script>

<template>
    <Head title="Daftar User - Super Admin" />

    <SuperAdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar User</h2>
                    <p class="text-gray-600">Kelola semua user di sistem ASIMPENAS</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium text-sm"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah User
                </button>
            </div>

            <!-- Search Bar -->
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
                        placeholder="Cari user berdasarkan nama, email, atau role..."
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
            </div>

            <!-- Users Table -->
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Daftar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ user.email }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getRoleBadgeColor(user.role)]">
                                        {{ getRoleDisplayName(user.role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="viewUserDetail(user)"
                                            class="inline-flex items-center px-2 py-1 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded transition-colors duration-200 text-xs"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            v-if="user.role !== 'super admin' && user.role !== 'super_admin'"
                                            @click="openDeleteModal(user)"
                                            class="inline-flex items-center px-2 py-1 text-red-600 hover:text-white hover:bg-red-600 border border-red-600 rounded transition-colors duration-200 text-xs"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    {{ searchQuery ? 'Tidak ada user yang sesuai dengan pencarian Anda.' : 'Belum ada data user.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <div v-if="showCreateModal" @click="closeCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 overflow-y-auto">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative my-8">
                <button @click="closeCreateModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h2 class="ml-4 text-xl font-bold text-gray-900">Tambah User Baru</h2>
                </div>

                <!-- Error Message -->
                <div v-if="createError" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ createError }}
                </div>
                
                <form @submit.prevent="createUser" class="space-y-4">
                    <!-- Name Field -->
                    <div>
                        <label for="create-name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="create-name"
                            type="text"
                            v-model="createFormData.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': createFormErrors.name }"
                            placeholder="Masukkan nama lengkap"
                            required
                        />
                        <p v-if="createFormErrors.name" class="mt-1 text-sm text-red-600">{{ createFormErrors.name[0] }}</p>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="create-email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="create-email"
                            type="email"
                            v-model="createFormData.email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': createFormErrors.email }"
                            placeholder="Masukkan email"
                            required
                        />
                        <p v-if="createFormErrors.email" class="mt-1 text-sm text-red-600">{{ createFormErrors.email[0] }}</p>
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label for="create-role" class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="create-role"
                            v-model="createFormData.role"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': createFormErrors.role }"
                            required
                        >
                            <option value="">Pilih Role</option>
                            <option value="mitra">Mitra</option>
                            <option value="admin">Admin</option>
                            <option value="super admin">Super Admin</option>
                        </select>
                        <p v-if="createFormErrors.role" class="mt-1 text-sm text-red-600">{{ createFormErrors.role[0] }}</p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="create-password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="create-password"
                            type="password"
                            v-model="createFormData.password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': createFormErrors.password }"
                            placeholder="Masukkan password"
                            required
                        />
                        <p v-if="createFormErrors.password" class="mt-1 text-sm text-red-600">{{ createFormErrors.password[0] }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Minimal 8 karakter.
                        </p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button
                            type="button"
                            @click="closeCreateModal"
                            :disabled="isCreating"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="isCreating"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                        >
                            <svg v-if="isCreating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="isCreating">Menambahkan...</span>
                            <span v-else>Tambah User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div v-if="showEditModal" @click="closeEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 overflow-y-auto">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative my-8">
                <button @click="closeEditModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h2 class="ml-4 text-xl font-bold text-gray-900">Edit User</h2>
                </div>

                <!-- Error Message -->
                <div v-if="updateError" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ updateError }}
                </div>
                
                <form @submit.prevent="saveUser" class="space-y-4">
                    <!-- Name Field -->
                    <div>
                        <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit-name"
                            type="text"
                            v-model="formData.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': formErrors.name }"
                            placeholder="Masukkan nama lengkap"
                            required
                        />
                        <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ formErrors.name[0] }}</p>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit-email"
                            type="email"
                            v-model="formData.email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': formErrors.email }"
                            placeholder="Masukkan email"
                            required
                        />
                        <p v-if="formErrors.email" class="mt-1 text-sm text-red-600">{{ formErrors.email[0] }}</p>
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit-role"
                            v-model="formData.role"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': formErrors.role }"
                            required
                        >
                            <option value="">Pilih Role</option>
                            <option value="mitra">Mitra</option>
                            <option value="admin">Admin</option>
                            <option value="super admin">Super Admin</option>
                        </select>
                        <p v-if="formErrors.role" class="mt-1 text-sm text-red-600">{{ formErrors.role[0] }}</p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="edit-password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru
                        </label>
                        <input
                            id="edit-password"
                            type="password"
                            v-model="formData.password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': formErrors.password }"
                            placeholder="Kosongkan jika tidak ingin mengubah"
                        />
                        <p v-if="formErrors.password" class="mt-1 text-sm text-red-600">{{ formErrors.password[0] }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.
                        </p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button
                            type="button"
                            @click="closeEditModal"
                            :disabled="isUpdating"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="isUpdating"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                        >
                            <svg v-if="isUpdating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="isUpdating">Menyimpan...</span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" @click="closeDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.stop class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative">
                <button @click="closeDeleteModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h2 class="text-xl font-bold mb-2 text-center text-gray-900">Konfirmasi Hapus Data</h2>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Apakah Anda yakin ingin menghapus user 
                    <strong class="text-gray-900">{{ selectedUserToDelete?.name }}</strong>
                    ({{ selectedUserToDelete?.email }})?
                    <br><br>
                    <span class="text-red-600 font-semibold">Tindakan ini tidak dapat dibatalkan!</span>
                </p>

                <!-- Error Message -->
                <div v-if="deleteError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
                    {{ deleteError }}
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="closeDeleteModal"
                        :disabled="isDeleting"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Batal
                    </button>
                    <button
                        @click="confirmDelete"
                        :disabled="isDeleting"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                    >
                        <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ isDeleting ? 'Menghapus...' : 'Ya, Hapus Data' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Alert Modal -->
        <div v-if="showSuccessAlert" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative animate-bounce-in">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="ml-4 text-xl font-bold text-gray-900">Berhasil!</h2>
                </div>
                
                <p class="text-gray-700 text-center text-lg">
                    {{ successMessage }}
                </p>
                
                <div class="mt-6 flex justify-center">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memuat ulang halaman...
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>

<style scoped>
@keyframes bounce-in {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-bounce-in {
    animation: bounce-in 0.5s ease-out;
}
</style>
