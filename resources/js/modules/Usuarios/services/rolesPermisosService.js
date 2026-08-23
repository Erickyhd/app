import api from '../../../core/http/api';

export const rolesPermisosService = {
    async getRoles() {
        const response = await api.get('/roles-permisos/roles');
        return response.data.data;
    },

    async getPermisos() {
        const response = await api.get('/roles-permisos/permisos');
        return response.data.data;
    },

    async getUserPermissions(userId) {
        const response = await api.get(`/roles-permisos/usuarios/${userId}`);
        return response.data.data;
    },

    async syncUserPermissions(userId, data) {
        // data expected format: { roles: ['Gerente General'], permissions: ['usuarios.ver', 'clientes.crear'] }
        const response = await api.post(`/roles-permisos/usuarios/${userId}/sync`, data);
        return response.data;
    }
};
