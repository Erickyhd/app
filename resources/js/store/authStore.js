import { defineStore } from 'pinia';
import axios from 'axios';

// Automatically send credentials (cookies) with each request
axios.defaults.withCredentials = true;
// Set headers for Laravel Sanctum/Session Auth
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        attemptedFetch: false,
    }),
    
    getters: {
        isAuthenticated: (state) => !!state.user,
    },
    
    actions: {
        async login(email, password) {
            try {
                // Ensure CSRF cookie is set
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/login', { email, password });
                this.user = response.data.user;
                return true;
            } catch (error) {
                console.error("Login Error", error);
                throw error;
            }
        },
        
        async fetchUser() {
            this.attemptedFetch = true;
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
            } catch (error) {
                this.user = null;
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
                this.user = null;
            } catch (error) {
                console.error("Logout Error", error);
            }
        }
    }
});
