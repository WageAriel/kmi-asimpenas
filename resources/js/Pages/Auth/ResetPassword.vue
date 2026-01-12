<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import logoImg from '@/../../resources/assets/Images/bulog.png'; 

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmation = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password - ASIMPENAS" />

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
                        <h2 class="text-3xl font-bold mb-4">Reset Password Anda</h2>
                        <p class="text-blue-100 text-lg leading-relaxed">
                            Buat password baru yang kuat untuk melindungi akun Anda. Password harus memiliki minimal 8 karakter.
                        </p>
                    </div>
                </div>

                <!-- Back to Login -->
                <Link 
                    :href="route('login')" 
                    class="inline-flex items-center text-blue-200 hover:text-white transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Kembali ke Login
                </Link>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="flex-1 flex items-center justify-center px-8 bg-white">
            <div class="w-full max-w-sm">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-12">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <img :src="logoImg" alt="Logo Bulog" class="w-7 h-7 object-contain" />
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">ASIMPENAS</h1>
                </div>

                <!-- Header -->
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Buat Password Baru</h2>
                    <p class="text-gray-500">Silakan masukkan password baru Anda</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email (Read-only) -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-gray-50 cursor-not-allowed"
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password Baru
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Masukkan password baru"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.password }"
                            />
                            <button
                                type="button"
                                @click="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <!-- Hide Password Icon -->
                                <svg v-if="showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23.474 11.4375C21.1131 7.34985 16.7167 4.81055 11.9999 4.81055C7.28312 4.81055 2.88675 7.34985 0.525789 11.4375C0.324737 11.7854 0.324737 12.2146 0.525789 12.5625C2.88675 16.6502 7.28312 19.1895 11.9999 19.1895C16.7167 19.1895 21.1131 16.6502 23.474 12.5625C23.6751 12.2146 23.6751 11.7854 23.474 11.4375ZM11.9999 16.9395C8.30558 16.9395 4.84745 15.0626 2.8223 12C4.84745 8.93738 8.30558 7.06055 11.9999 7.06055C15.6942 7.06055 19.1524 8.93738 21.1775 12C19.1524 15.0626 15.6942 16.9395 11.9999 16.9395Z" fill="currentColor"/>
                                    <path d="M12 7.83472C9.70312 7.83472 7.83472 9.70312 7.83472 12C7.83472 14.2969 9.70312 16.1653 12 16.1653C14.2969 16.1653 16.1653 14.2969 16.1653 12C16.1653 9.70312 14.2969 7.83472 12 7.83472ZM12 13.9153C10.9438 13.9153 10.0847 13.0561 10.0847 12C10.0847 10.9438 10.9438 10.0847 12 10.0847C13.0561 10.0847 13.9153 10.9438 13.9153 12C13.9153 13.0561 13.0561 13.9153 12 13.9153Z" fill="currentColor"/>
                                </svg>
                                
                                <!-- Show Password Icon -->
                                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21.375 16.4062C21.2893 16.4551 21.1948 16.4866 21.097 16.4989C20.9991 16.5112 20.8998 16.504 20.8047 16.4779C20.7097 16.4518 20.6207 16.4072 20.5428 16.3466C20.465 16.2861 20.3999 16.2107 20.3512 16.125L18.57 13.0125C17.5344 13.7127 16.392 14.2402 15.1875 14.5744L15.7378 17.8762C15.754 17.9734 15.7509 18.0729 15.7287 18.1689C15.7065 18.2649 15.6656 18.3556 15.6083 18.4358C15.551 18.516 15.4784 18.5841 15.3948 18.6362C15.3112 18.6884 15.2181 18.7235 15.1209 18.7397C15.0809 18.7462 15.0405 18.7497 15 18.75C14.8225 18.7497 14.6509 18.6866 14.5157 18.5717C14.3804 18.4569 14.2903 18.2978 14.2612 18.1228L13.7203 14.8809C12.5795 15.0397 11.4223 15.0397 10.2815 14.8809L9.74058 18.1228C9.71147 18.2982 9.62103 18.4575 9.48538 18.5723C9.34974 18.6872 9.1777 18.7502 8.99995 18.75C8.9585 18.7498 8.91712 18.7464 8.8762 18.7397C8.77898 18.7235 8.6859 18.6884 8.60227 18.6362C8.51864 18.5841 8.44611 18.516 8.38882 18.4358C8.33153 18.3556 8.29061 18.2649 8.26839 18.1689C8.24617 18.0729 8.24309 17.9734 8.25933 17.8762L8.81245 14.5744C7.60835 14.2391 6.66664 13.7107 5.43183 13.0097L3.6562 16.125C3.55675 16.2983 3.39252 16.425 3.19965 16.4772C3.00678 16.5294 2.80107 16.5029 2.62776 16.4034C2.45446 16.304 2.32777 16.1397 2.27555 15.9469C2.22333 15.754 2.24987 15.5483 2.34933 15.375L4.22433 12.0937C3.56573 11.5247 2.96013 10.8972 2.41495 10.2187C2.34696 10.1428 2.29516 10.0539 2.2627 9.95726C2.23024 9.86066 2.2178 9.75846 2.22616 9.65689C2.23451 9.55533 2.26347 9.45654 2.31127 9.36654C2.35908 9.27654 2.42472 9.19723 2.5042 9.13344C2.58367 9.06966 2.67531 9.02274 2.77353 8.99555C2.87174 8.96836 2.97446 8.96147 3.07542 8.9753C3.17639 8.98913 3.27347 9.02339 3.36075 9.07599C3.44804 9.12859 3.52368 9.19843 3.58308 9.28124C5.13933 11.2069 7.86183 13.5 12 13.5C16.1381 13.5 18.8606 11.204 20.4168 9.28124C20.4755 9.19673 20.551 9.12522 20.6386 9.07113C20.7261 9.01705 20.8238 8.98157 20.9257 8.96688C21.0275 8.95219 21.1313 8.95862 21.2306 8.98576C21.3298 9.0129 21.4224 9.06018 21.5026 9.12465C21.5828 9.18912 21.6489 9.2694 21.6968 9.3605C21.7446 9.45161 21.7732 9.55158 21.7807 9.6542C21.7883 9.75683 21.7746 9.8599 21.7406 9.95702C21.7066 10.0541 21.653 10.1432 21.5831 10.2187C21.0379 10.8972 20.4323 11.5247 19.7737 12.0937L21.6487 15.375C21.699 15.4605 21.7319 15.5552 21.7454 15.6535C21.7588 15.7518 21.7527 15.8519 21.7272 15.9478C21.7017 16.0437 21.6575 16.1336 21.597 16.2123C21.5366 16.291 21.4611 16.3569 21.375 16.4062Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password }}
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Password minimal 8 karakter</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Konfirmasi password baru"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.password_confirmation }"
                            />
                            <button
                                type="button"
                                @click="togglePasswordConfirmation"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <!-- Hide Password Icon -->
                                <svg v-if="showPasswordConfirmation" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23.474 11.4375C21.1131 7.34985 16.7167 4.81055 11.9999 4.81055C7.28312 4.81055 2.88675 7.34985 0.525789 11.4375C0.324737 11.7854 0.324737 12.2146 0.525789 12.5625C2.88675 16.6502 7.28312 19.1895 11.9999 19.1895C16.7167 19.1895 21.1131 16.6502 23.474 12.5625C23.6751 12.2146 23.6751 11.7854 23.474 11.4375ZM11.9999 16.9395C8.30558 16.9395 4.84745 15.0626 2.8223 12C4.84745 8.93738 8.30558 7.06055 11.9999 7.06055C15.6942 7.06055 19.1524 8.93738 21.1775 12C19.1524 15.0626 15.6942 16.9395 11.9999 16.9395Z" fill="currentColor"/>
                                    <path d="M12 7.83472C9.70312 7.83472 7.83472 9.70312 7.83472 12C7.83472 14.2969 9.70312 16.1653 12 16.1653C14.2969 16.1653 16.1653 14.2969 16.1653 12C16.1653 9.70312 14.2969 7.83472 12 7.83472ZM12 13.9153C10.9438 13.9153 10.0847 13.0561 10.0847 12C10.0847 10.9438 10.9438 10.0847 12 10.0847C13.0561 10.0847 13.9153 10.9438 13.9153 12C13.9153 13.0561 13.0561 13.9153 12 13.9153Z" fill="currentColor"/>
                                </svg>
                                
                                <!-- Show Password Icon -->
                                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21.375 16.4062C21.2893 16.4551 21.1948 16.4866 21.097 16.4989C20.9991 16.5112 20.8998 16.504 20.8047 16.4779C20.7097 16.4518 20.6207 16.4072 20.5428 16.3466C20.465 16.2861 20.3999 16.2107 20.3512 16.125L18.57 13.0125C17.5344 13.7127 16.392 14.2402 15.1875 14.5744L15.7378 17.8762C15.754 17.9734 15.7509 18.0729 15.7287 18.1689C15.7065 18.2649 15.6656 18.3556 15.6083 18.4358C15.551 18.516 15.4784 18.5841 15.3948 18.6362C15.3112 18.6884 15.2181 18.7235 15.1209 18.7397C15.0809 18.7462 15.0405 18.7497 15 18.75C14.8225 18.7497 14.6509 18.6866 14.5157 18.5717C14.3804 18.4569 14.2903 18.2978 14.2612 18.1228L13.7203 14.8809C12.5795 15.0397 11.4223 15.0397 10.2815 14.8809L9.74058 18.1228C9.71147 18.2982 9.62103 18.4575 9.48538 18.5723C9.34974 18.6872 9.1777 18.7502 8.99995 18.75C8.9585 18.7498 8.91712 18.7464 8.8762 18.7397C8.77898 18.7235 8.6859 18.6884 8.60227 18.6362C8.51864 18.5841 8.44611 18.516 8.38882 18.4358C8.33153 18.3556 8.29061 18.2649 8.26839 18.1689C8.24617 18.0729 8.24309 17.9734 8.25933 17.8762L8.81245 14.5744C7.60835 14.2391 6.66664 13.7107 5.43183 13.0097L3.6562 16.125C3.55675 16.2983 3.39252 16.425 3.19965 16.4772C3.00678 16.5294 2.80107 16.5029 2.62776 16.4034C2.45446 16.304 2.32777 16.1397 2.27555 15.9469C2.22333 15.754 2.24987 15.5483 2.34933 15.375L4.22433 12.0937C3.56573 11.5247 2.96013 10.8972 2.41495 10.2187C2.34696 10.1428 2.29516 10.0539 2.2627 9.95726C2.23024 9.86066 2.2178 9.75846 2.22616 9.65689C2.23451 9.55533 2.26347 9.45654 2.31127 9.36654C2.35908 9.27654 2.42472 9.19723 2.5042 9.13344C2.58367 9.06966 2.67531 9.02274 2.77353 8.99555C2.87174 8.96836 2.97446 8.96147 3.07542 8.9753C3.17639 8.98913 3.27347 9.02339 3.36075 9.07599C3.44804 9.12859 3.52368 9.19843 3.58308 9.28124C5.13933 11.2069 7.86183 13.5 12 13.5C16.1381 13.5 18.8606 11.204 20.4168 9.28124C20.4755 9.19673 20.551 9.12522 20.6386 9.07113C20.7261 9.01705 20.8238 8.98157 20.9257 8.96688C21.0275 8.95219 21.1313 8.95862 21.2306 8.98576C21.3298 9.0129 21.4224 9.06018 21.5026 9.12465C21.5828 9.18912 21.6489 9.2694 21.6968 9.3605C21.7446 9.45161 21.7732 9.55158 21.7807 9.6542C21.7883 9.75683 21.7746 9.8599 21.7406 9.95702C21.7066 10.0541 21.653 10.1432 21.5831 10.2187C21.0379 10.8972 20.4323 11.5247 19.7737 12.0937L21.6487 15.375C21.699 15.4605 21.7319 15.5552 21.7454 15.6535C21.7588 15.7518 21.7527 15.8519 21.7272 15.9478C21.7017 16.0437 21.6575 16.1336 21.597 16.2123C21.5366 16.291 21.4611 16.3569 21.375 16.4062Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors"
                    >
                        <span v-if="form.processing">Memproses...</span>
                        <span v-else>Reset Password</span>
                    </button>
                </form>

                <!-- Back to Login Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Ingat password Anda? 
                        <Link 
                            :href="route('login')" 
                            class="text-blue-600 hover:text-blue-800 font-medium"
                        >
                            Kembali ke Login
                        </Link>
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>
