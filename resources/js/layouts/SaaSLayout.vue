<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../store/authStore';
import { useRouter } from 'vue-router';
import { usePermissions } from '../modules/Auth/composables/usePermissions';

const authStore = useAuthStore();
const router = useRouter();
const { can, hasRole } = usePermissions();
const isSidebarOpen = ref(true);

const openMenus = ref({
    'Módulo de Usuarios': false,
    'Módulo de Clientes': false,
    'Reportes': false,
    'Configuración': false
});

const user = computed(() => authStore.user);

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'Login' });
};

// Navegación computada filtrada por permisos
const navigation = computed(() => {
    return [
        { name: 'Dashboard', icon: 'mdi-view-dashboard-outline', to: '/', show: true },
        { 
            name: 'Módulo Administrativo', 
            icon: 'mdi-account-group-outline',
            isOpen: openMenus.value['Módulo de Usuarios'],
            show: can('usuarios.ver') || can('jerarquias.ver') || can('rangos.ver') || can('trabajadores.ver') || can('roles.ver'),
            children: [
                { name: 'Usuarios', to: '/usuarios', icon: 'mdi-account-outline', show: can('usuarios.ver') },
                { name: 'Roles y Permisos', to: '/roles', icon: 'mdi-shield-account-outline', show: can('roles.ver') },
                { name: 'Jerarquías', to: '/jerarquias', icon: 'mdi-sitemap', show: can('jerarquias.ver') },
                { name: 'Rangos', to: '/rangos', icon: 'mdi-star-outline', show: can('rangos.ver') },
                { name: 'Trabajadores', to: '/trabajadores', icon: 'mdi-briefcase-outline', show: can('trabajadores.ver') },
            ].filter(child => child.show)
        },
        { 
            name: 'Módulo de Clientes', 
            icon: 'mdi-briefcase-account-outline',
            isOpen: openMenus.value['Módulo de Clientes'],
            show: can('clientes.ver'),
            children: [
                { name: 'Directorio', to: '/clientes', icon: 'mdi-contacts-outline', show: can('clientes.ver') }
            ].filter(child => child.show)
        },
        { 
            name: 'Reportes', 
            icon: 'mdi-chart-box-outline',
            isOpen: openMenus.value['Reportes'],
            show: can('reportes.ver'),
            children: [
                { name: 'General', to: '/reportes', icon: 'mdi-chart-bar', show: can('reportes.ver') }
            ].filter(child => child.show)
        },
        { 
            name: 'Configuración', 
            icon: 'mdi-cog-outline',
            isOpen: openMenus.value['Configuración'],
            show: can('configuracion.ver'),
            children: [
                { name: 'Sistema', to: '/configuracion', icon: 'mdi-cogs', show: can('configuracion.ver') }
            ].filter(child => child.show)
        },
    ].filter(item => item.show && (!item.children || item.children.length > 0));
});

const toggleMenu = (item) => {
    if (item.children) {
        if (!isSidebarOpen.value) {
            isSidebarOpen.value = true;
        }
        openMenus.value[item.name] = !openMenus.value[item.name];
    } else {
        router.push(item.to);
    }
};
</script>

<template>
    <div class="flex h-screen w-full bg-slate-50 overflow-hidden">
        <!-- Sidebar -->
        <aside 
            :class="[
                'bg-slate-900 text-slate-300 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800 overflow-hidden',
                isSidebarOpen ? 'w-64' : 'w-20'
            ]"
        >
            <!-- Logo area -->
            <div class="h-16 flex items-center justify-center border-b border-slate-800 shrink-0">
                <v-icon v-if="!isSidebarOpen" icon="mdi-cube-outline" color="indigo-400" size="large"></v-icon>
                <div v-else class="flex items-center gap-2 whitespace-nowrap">
                    <v-icon icon="mdi-cube-outline" color="indigo-400" size="large"></v-icon>
                    <span class="text-lg font-semibold text-white tracking-tight">FLY</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 py-4 flex flex-col gap-1 px-3 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-slate-700">
                <template v-for="item in navigation" :key="item.name">
                    <!-- Single Link or Parent -->
                    <div 
                        @click="toggleMenu(item)"
                        class="flex items-center justify-between px-3 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition-colors group relative whitespace-nowrap cursor-pointer select-none"
                        :title="!isSidebarOpen ? item.name : ''"
                        :class="{'bg-slate-800 text-white': item.isOpen}"
                    >
                        <div class="flex items-center">
                            <v-icon :icon="item.icon" size="small" class="shrink-0" :class="isSidebarOpen ? '' : 'mx-auto'"></v-icon>
                            <span v-if="isSidebarOpen" class="ml-3 font-medium text-sm">{{ item.name }}</span>
                        </div>
                        
                        <v-icon 
                            v-if="isSidebarOpen && item.children" 
                            :icon="item.isOpen ? 'mdi-chevron-down' : 'mdi-chevron-right'" 
                            size="small" 
                            class="text-slate-500 group-hover:text-slate-300 transition-transform"
                        ></v-icon>

                        <!-- Tooltip for collapsed sidebar -->
                        <div v-if="!isSidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                            {{ item.name }}
                        </div>
                    </div>

                    <!-- Children (Dropdown) -->
                    <div 
                        v-if="isSidebarOpen && item.children && item.isOpen"
                        class="flex flex-col gap-1 pl-9 pr-2 py-2 mb-1 border-l-2 border-slate-700 ml-4 animate-fade-in-down"
                    >
                        <router-link 
                            v-for="child in item.children" 
                            :key="child.name"
                            :to="child.to"
                            class="flex items-center px-3 py-2 text-sm text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-md transition-colors"
                            active-class="text-indigo-400 bg-slate-800/50 font-medium"
                        >
                            <v-icon :icon="child.icon" size="x-small" class="mr-3 opacity-70"></v-icon>
                            {{ child.name }}
                        </router-link>
                    </div>
                </template>
            </nav>

            <!-- Bottom User Profile -->
            <div class="p-4 border-t border-slate-800 shrink-0">
                <button @click="handleLogout" class="flex items-center w-full px-3 py-2 text-sm font-medium text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition-colors whitespace-nowrap">
                    <v-icon icon="mdi-logout" size="small" class="shrink-0" :class="isSidebarOpen ? '' : 'mx-auto'"></v-icon>
                    <span v-if="isSidebarOpen" class="ml-3">Cerrar Sesión</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-full min-w-0">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 shrink-0 shadow-sm z-10">
                <div class="flex items-center gap-4">
                    <v-btn
                        icon="mdi-menu"
                        variant="text"
                        color="slate-600"
                        density="comfortable"
                        @click="isSidebarOpen = !isSidebarOpen"
                    ></v-btn>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-medium">Activo</span>
                    </div>
                    
                    <div class="hidden sm:block border-l border-slate-200 h-6 mx-2"></div>
                    
                    <v-menu offset-y>
                        <template v-slot:activator="{ props }">
                            <button 
                                v-bind="props"
                                class="flex items-center gap-3 hover:bg-slate-50 py-1 px-2 rounded-lg transition-colors focus:outline-none"
                            >
                                <div class="hidden sm:flex flex-col text-right">
                                    <span class="text-sm font-medium text-slate-700">{{ user?.nombres || user?.name || 'Administrador' }}</span>
                                    <span class="text-xs text-slate-500">{{ user?.email || user?.correo || 'admin@app.com' }}</span>
                                </div>
                                <v-avatar color="indigo-lighten-4" size="36">
                                    <span class="text-indigo-700 font-bold text-sm">
                                        {{ (user?.nombres || user?.name || 'A').charAt(0).toUpperCase() }}
                                    </span>
                                </v-avatar>
                                <v-icon icon="mdi-chevron-down" size="small" class="text-slate-400"></v-icon>
                            </button>
                        </template>
                        
                        <v-list density="compact" elevation="3" class="mt-2 min-w-[200px] rounded-lg">
                            <v-list-item prepend-icon="mdi-account-outline" title="Mi Perfil" value="profile"></v-list-item>
                            <v-list-item prepend-icon="mdi-cog-outline" title="Ajustes" value="settings"></v-list-item>
                            <v-divider class="my-1"></v-divider>
                            <v-list-item 
                                prepend-icon="mdi-logout" 
                                title="Cerrar Sesión" 
                                value="logout" 
                                class="text-red-600"
                                @click="handleLogout"
                            ></v-list-item>
                        </v-list>
                    </v-menu>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>
<style scoped>
.animate-fade-in-down {
    animation: fadeInDown 0.2s ease-out;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
