<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register - ASIMPENAS" />

    <div class="min-h-screen flex">
        <!-- Left Side - Brand -->
        <div class="hidden lg:flex lg:w-1/2 bg-blue-600">
            <div class="flex flex-col justify-center px-16 text-white">
                <!-- Logo -->
                <div class="mb-16">
                    <div class="flex items-center mb-8">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L1 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-11-5z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">ASIMPENAS</h1>
                            <p class="text-blue-100 text-sm">Portal Seleksi Mitra</p>
                        </div>
                    </div>
                    
                    <div>
                        <h2 class="text-3xl font-bold mb-4">Bergabung Sebagai Mitra</h2>
                        <p class="text-blue-100 text-lg leading-relaxed">
                            Daftarkan perusahaan Anda untuk mengikuti proses seleksi dan berpeluang mendapatkan Purchase Order.
                        </p>
                    </div>
                </div>

                <!-- Back to Home -->
                <Link 
                    href="/" 
                    class="inline-flex items-center text-blue-200 hover:text-white transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Kembali ke Beranda
                </Link>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="flex-1 flex items-center justify-center px-8 bg-white">
            <div class="w-full max-w-sm">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-12">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L1 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-11-5z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">ASIMPENAS</h1>
                </div>

                <!-- Header -->
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Get Started Now</h2>
                    <p class="text-gray-500">Please complete your company information</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Company Name
                        </label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Enter company name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-300': form.errors.name }"
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Company Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Enter company email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-300': form.errors.email }"
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Enter your password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-300': form.errors.password }"
                        />
                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repeat your password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-300': form.errors.password_confirmation }"
                        />
                        <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>

                    <!-- Terms Notice -->
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-xs text-blue-700">
                           By registering, you agree to follow the selection process according to the applicable provisions.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors"
                    >
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>Register as Partner</span>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600 mb-3">
                        Have an account?
                        <Link 
                        :href="route('login')" 
                        class="text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Sign in
                    </Link>
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>