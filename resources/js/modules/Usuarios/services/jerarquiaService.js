import api from '../../../core/http/api';

export const jerarquiaService = {
    /**
     * Obtiene la lista completa de jerarquías.
     */
    async getAll() {
        const response = await api.get('/jerarquias');
        // El controlador de Laravel devuelve { success: true, data: [...] }
        return response.data.data;
    },

    /**
     * Crea una nueva jerarquía.
     */
    async create(data) {
        const response = await api.post('/jerarquias', data);
        return response.data.data;
    },

    /**
     * Actualiza una jerarquía existente.
     */
    async update(id, data) {
        const response = await api.put(`/jerarquias/${id}`, data);
        return response.data.data;
    },

    /**
     * Elimina (soft delete) una jerarquía.
     */
    async delete(id) {
        const response = await api.delete(`/jerarquias/${id}`);
        return response.data;
    }
};
