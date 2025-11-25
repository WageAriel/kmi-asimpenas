<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import logoImg from '@/../../resources/assets/Images/bulog.png';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Verifikasi Email - ASIMPENAS" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <img :src="logoImg" alt="Logo Bulog" class="w-10 h-10 object-contain" />
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Email Anda</h1>
                <p class="text-gray-600">ASIMPENAS - Portal Seleksi Mitra</p>
            </div>

            <!-- Email Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Main Message -->
            <div class="text-center mb-6">
                <p class="text-gray-700 leading-relaxed mb-4">
                    Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda.
                </p>
                <p class="text-sm text-gray-600">
                    Jika Anda tidak menerima email, kami dengan senang hati akan mengirimkan email verifikasi lainnya.
                </p>
            </div>

            <!-- Success Message -->
            <div
                v-if="verificationLinkSent"
                class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-green-800 font-medium">
                        Link verifikasi baru telah dikirim ke email Anda!
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Resend Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg transition-colors flex items-center justify-center"
                >
                    <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span v-if="form.processing">Mengirim...</span>
                    <span v-else>Kirim Ulang Email Verifikasi</span>
                </button>

                <!-- Logout Link -->
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors"
                >
                    Keluar
                </Link>
            </form>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-800 leading-relaxed">
                    <strong>💡 Tips:</strong> Periksa folder Spam/Junk jika email verifikasi tidak muncul di Inbox Anda. Pastikan email yang Anda daftarkan sudah benar.
                </p>
            </div>
        </div>
    </div>
</template>
