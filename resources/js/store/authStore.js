import { defineStore } from 'pinia';
import axios from 'axios';

// Automatically send credentials (cookies) with each request
axios.defaults.withCredentials = true;
// Set headers for Laravel Sanctum/Session Auth
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        roles: [],
        permissions: [],
        isAuthenticated: false,
        attemptedFetch: false
    }),
    
    getters: {
        
    },
    
    actions: {
        async login(email, password) {
            try {
                // Ensure CSRF cookie is set
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/login', { email, password });
                this.user = response.data.user;
                this.roles = response.data.roles;
                this.permissions = response.data.permissions;
                this.isAuthenticated = true;
                return true;
            } catch (error) {
                console.error("Login Error:", error.response?.data?.message || error.message);
                throw error;
            }
        },
        
        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data.user;
                this.roles = response.data.roles;
                this.permissions = response.data.permissions;
                this.isAuthenticated = true;
            } catch (error) {
                this.user = null;
                this.roles = [];
                this.permissions = [];
                this.isAuthenticated = false;
            } finally {
                this.attemptedFetch = true;
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error("Logout Error", error);
            } finally {
                this.user = null;
                this.roles = [];
                this.permissions = [];
                this.isAuthenticated = false;
            }
        }
    }
});
