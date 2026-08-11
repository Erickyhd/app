import api from '../../../core/http/api';

export const rangoService = {
    async getAll() {
        const response = await api.get('/rangos');
        return response.data.data;
    },

    async create(data) {
        const response = await api.post('/rangos', data);
        return response.data.data;
    },

    async update(id, data) {
        const response = await api.put(`/rangos/${id}`, data);
        return response.data.data;
    },

    async delete(id) {
        const response = await api.delete(`/rangos/${id}`);
        return response.data;
    }
};
