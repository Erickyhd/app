<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../store/authStore';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

const stats = [
    { title: 'Usuarios Totales', value: '1,234', icon: 'mdi-account-group', color: 'text-indigo-600', bg: 'bg-indigo-50' },
    { title: 'Suscripciones Activas', value: '856', icon: 'mdi-star-circle', color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { title: 'Ingresos del Mes', value: '$12,450', icon: 'mdi-currency-usd', color: 'text-blue-600', bg: 'bg-blue-50' },
];
</script>

<template>
    <div class="w-full">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-800 tracking-tight">Panel Principal</h1>
                <p class="text-sm text-slate-500 mt-1">Resumen de la plataforma y métricas clave.</p>
            </div>
            <v-btn color="indigo-darken-1" prepend-icon="mdi-plus" class="hidden sm:flex text-none text-sm">
                Nuevo Reporte
            </v-btn>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div 
                v-for="(stat, index) in stats" 
                :key="index"
                class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-start justify-between hover:shadow-md transition-shadow"
            >
                <div>
                    <h3 class="text-slate-500 text-sm font-medium mb-1">{{ stat.title }}</h3>
                    <p class="text-2xl font-semibold text-slate-800">{{ stat.value }}</p>
                </div>
                <div :class="[stat.bg, 'p-3 rounded-xl']">
                    <v-icon :icon="stat.icon" :class="stat.color" size="x-large"></v-icon>
                </div>
            </div>
        </div>

        <!-- Main Banner -->
        <div class="bg-indigo-600 dark:bg-indigo-900 rounded-3xl p-8 sm:p-10 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-center justify-between">
            <div class="relative z-10 max-w-xl">
                <h2 class="text-2xl sm:text-3xl font-semibold mb-3 tracking-tight">¡Bienvenido de nuevo, {{ user?.nombres || 'Administrador' }}!</h2>
                <p class="text-indigo-100 text-sm sm:text-base mb-6 leading-relaxed">
                    Tu sistema está operando con total normalidad. Tienes 5 nuevos reportes generados desde tu última sesión. ¿Deseas revisarlos ahora?
                </p>
                <div class="flex gap-4">
                    <v-btn color="white" class="text-indigo-700 font-medium text-none text-sm" rounded="pill">
                        Ver Reportes
                    </v-btn>
                    <v-btn variant="outlined" color="white" class="font-medium text-none text-sm" rounded="pill">
                        Descartar
                    </v-btn>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute right-0 top-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute right-20 bottom-0 w-48 h-48 bg-indigo-400 opacity-20 rounded-full blur-2xl translate-y-1/2"></div>
            
            <!-- Illustration Placeholder -->
            <div class="hidden md:flex relative z-10 ml-8 shrink-0">
                <v-icon icon="mdi-rocket-launch" size="100" class="text-indigo-200 opacity-80"></v-icon>
            </div>
        </div>
    </div>
</template>
