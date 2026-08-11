import api from '../../../core/http/api';

export const trabajadorService = {
    async getAll() {
        const response = await api.get('/trabajadores');
        return response.data.data;
    },

    async create(data) {
        const response = await api.post('/trabajadores', data);
        return response.data.data;
    },

    async update(id, data) {
        const response = await api.put(`/trabajadores/${id}`, data);
        return response.data.data;
    },

    async delete(id) {
        const response = await api.delete(`/trabajadores/${id}`);
        return response.data;
    }
};
