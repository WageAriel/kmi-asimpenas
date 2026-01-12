<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import logoImg from '@/../../resources/assets/Images/bulog.png';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const emailWarning = ref('');

// Validasi email real-time
watch(() => form.email, (newEmail) => {
    emailWarning.value = '';
    
    if (!newEmail || !newEmail.includes('@')) {
        return;
    }

    const domain = newEmail.split('@')[1]?.toLowerCase();
    
    if (!domain) {
        return;
    }

    // Deteksi typo Gmail
    const typoGmail = ['gamail.com', 'gmai.com', 'gmial.com', 'gmaill.com', 'gmil.com', 'gmal.com', 'gmaik.com', 'gnail.com', 'gamil.com'];
    if (typoGmail.includes(domain)) {
        emailWarning.value = '⚠️ Sepertinya Anda salah ketik. Maksud Anda "gmail.com"?';
        return;
    }

    // Deteksi typo Yahoo
    const typoYahoo = ['yaho.com', 'yahooo.com', 'yhoo.com', 'yahho.com', 'yaoo.com'];
    if (typoYahoo.includes(domain)) {
        emailWarning.value = '⚠️ Sepertinya Anda salah ketik. Maksud Anda "yahoo.com"?';
        return;
    }

    // Deteksi typo Hotmail/Outlook
    const typoHotmail = ['hotmal.com', 'hotmial.com', 'hotmil.com', 'htmail.com', 'homail.com'];
    if (typoHotmail.includes(domain)) {
        emailWarning.value = '⚠️ Sepertinya Anda salah ketik. Maksud Anda "hotmail.com"?';
        return;
    }

    // List domain valid
    const validDomains = [
        'gmail.com', 'yahoo.com', 'yahoo.co.id', 'hotmail.com', 'outlook.com',
        'live.com', 'icloud.com', 'mail.com', 'aol.com', 'protonmail.com',
        'zoho.com', 'yandex.com', 'gmx.com', 'tutanota.com'
    ];

    // Cek domain Indonesia
    const indonesianDomains = ['co.id', '.id', 'or.id', 'ac.id', 'go.id', 'net.id', 'web.id'];
    const isIndonesianDomain = indonesianDomains.some(d => domain.endsWith(d));

    if (!validDomains.includes(domain) && !isIndonesianDomain) {
        emailWarning.value = `⚠️ Domain email "${domain}" mungkin tidak valid. Pastikan Anda mengetik dengan benar.`;
    }
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
                            <img :src="logoImg" alt="Logo Bulog" class="w-7 h-7 object-contain" />
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
                    <div class="w-12 h-12rounded-lg flex items-center justify-center mx-auto mb-4">
                        <img :src="logoImg" alt="Logo Bulog" class="w-7 h-7 object-contain" />
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">ASIMPENAS</h1>
                </div>

                <!-- Header -->
                <div class="text-center mt-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Bergabung Sebagai Mitra</h2>
                    <p class="text-gray-500">Silakan lengkapi informasi akun anda</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Username
                        </label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Masukkan username"
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
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="contoh@gmail.com"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 
                                'border-red-300': form.errors.email,
                                'border-yellow-300': emailWarning && !form.errors.email
                            }"
                        />
                        <!-- Warning real-time untuk typo -->
                        <div v-if="emailWarning && !form.errors.email" class="mt-1 text-sm text-yellow-600 flex items-start">
                            <span>{{ emailWarning }}</span>
                        </div>
                        <!-- Error dari server -->
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </div>
                        <!-- Helper text -->
                        <p class="mt-1 text-xs text-gray-500">Gunakan email yang valid seperti Gmail, Yahoo, atau email perusahaan</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Kata Sandi
                        </label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Masukkan kata sandi"
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
                            Konfirmasi Kata Sandi
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi Anda"
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
                           Dengan mendaftar, Anda setuju untuk mengikuti proses seleksi sesuai dengan ketentuan yang berlaku.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors"
                    >
                        <span v-if="form.processing">Memproses...</span>
                        <span v-else>Daftar sebagai Mitra</span>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600 mb-3">
                        Sudah memiliki akun?
                        <Link 
                        :href="route('login')" 
                        class="text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Masuk
                    </Link>
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>