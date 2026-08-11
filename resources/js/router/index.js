import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../store/authStore';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../modules/Auth/UI/views/Login.vue'),
        meta: { requiresGuest: true }
    },
    {
        path: '/',
        component: () => import('../layouts/SaaSLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('../pages/Dashboard.vue')
            },
            {
                path: 'usuarios',
                name: 'Usuarios',
                component: () => import('../modules/Usuarios/UI/views/UsuariosView.vue')
            },
            {
                path: 'roles',
                name: 'Roles',
                component: () => import('../modules/Usuarios/UI/views/RolesView.vue')
            },
            {
                path: 'jerarquias',
                name: 'Jerarquias',
                component: () => import('../modules/Usuarios/UI/views/JerarquiasView.vue')
            },
            {
                path: 'rangos',
                name: 'Rangos',
                component: () => import('../modules/Usuarios/UI/views/RangosView.vue')
            },
            {
                path: 'trabajadores',
                name: 'Trabajadores',
                component: () => import('../modules/Usuarios/UI/views/TrabajadoresView.vue')
            },
            {
                path: 'clientes',
                name: 'Clientes',
                component: () => import('../modules/Clientes/UI/views/ClientesView.vue')
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from) => {
    const authStore = useAuthStore();
    
    // Check if user data exists, otherwise try fetching it (in case of page reload)
    if (!authStore.user && !authStore.attemptedFetch) {
        await authStore.fetchUser();
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return { name: 'Login' };
    } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
        return { name: 'Dashboard' };
    }
    
    return true;
});

export default router;
