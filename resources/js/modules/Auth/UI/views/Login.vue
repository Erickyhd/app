<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../../../store/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const email = ref('');
const password = ref('');
const errorMsg = ref('');
const loading = ref(false);
const showPassword = ref(false);

const handleLogin = async () => {
    loading.value = true;
    errorMsg.value = '';
    
    try {
        await authStore.login(email.value, password.value);
        router.push({ name: 'Dashboard' });
    } catch (error) {
        if (error.response && error.response.status === 401) {
            errorMsg.value = error.response.data.message || 'Credenciales inválidas';
        } else {
            errorMsg.value = 'Ocurrió un error. Por favor intente nuevamente.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex w-full bg-slate-50 overflow-hidden">
        
        <!-- Left Panel: Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 sm:px-16 lg:px-24 bg-white z-10 shadow-2xl">
            <div class="max-w-md w-full mx-auto">
                <!-- Logo / Header -->
                <div class="mb-10 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 mb-4">
                        <v-icon icon="mdi-cube-outline" size="x-large"></v-icon>
                    </div>
                    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Iniciar Sesión</h1>
                    <p class="text-slate-500 mt-2 text-sm">Ingresa tus credenciales para acceder a tu espacio de trabajo.</p>
                </div>

                <!-- Error Alert -->
                <div v-if="errorMsg" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-100 flex items-start gap-3">
                    <v-icon icon="mdi-alert-circle" color="red-500" size="small" class="mt-0.5"></v-icon>
                    <p class="text-sm text-red-700 font-medium">{{ errorMsg }}</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleLogin" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo Electrónico</label>
                        <v-text-field
                            v-model="email"
                            type="email"
                            variant="outlined"
                            density="comfortable"
                            color="indigo"
                            bg-color="transparent"
                            placeholder="tu@correo.com"
                            prepend-inner-icon="mdi-email-outline"
                            hide-details
                            required
                        ></v-text-field>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                            <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">¿Olvidaste tu contraseña?</a>
                        </div>
                        <v-text-field
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            variant="outlined"
                            density="comfortable"
                            color="indigo"
                            bg-color="transparent"
                            placeholder="••••••••"
                            prepend-inner-icon="mdi-lock-outline"
                            :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            @click:append-inner="showPassword = !showPassword"
                            hide-details
                            required
                        ></v-text-field>
                    </div>

                    <v-btn
                        type="submit"
                        color="indigo-darken-1"
                        size="x-large"
                        block
                        :loading="loading"
                        class="mt-6 font-medium text-none tracking-wide rounded-lg"
                        elevation="2"
                    >
                        Entrar al Sistema
                    </v-btn>
                </form>
            </div>
        </div>

        <!-- Right Panel: Branding/Illustration -->
        <div class="hidden lg:flex w-1/2 bg-slate-900 relative items-center justify-center overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full blur-[100px] opacity-30 -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full blur-[100px] opacity-20 translate-y-1/2 -translate-x-1/3"></div>

            <div class="relative z-10 max-w-lg text-center px-12">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 mb-8 shadow-2xl">
                    <v-icon icon="mdi-rocket-launch-outline" size="50" color="white"></v-icon>
                </div>
                <h2 class="text-3xl font-semibold text-white mb-4 tracking-tight leading-tight">La plataforma para gestionar tu negocio.</h2>
                <p class="text-slate-400 text-base leading-relaxed">
                    Todo lo que necesitas en un solo lugar. Rápido, seguro y diseñado para escalar contigo y tu equipo.
                </p>
            </div>
        </div>

    </div>
</template>
