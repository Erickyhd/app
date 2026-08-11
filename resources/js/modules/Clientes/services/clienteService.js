import api from '../../../core/http/api';

export const clienteService = {
    async getAll() {
        const response = await api.get('/clientes');
        return response.data.data;
    },

    async create(data) {
        const response = await api.post('/clientes', data);
        return response.data.data;
    },

    async update(id, data) {
        const response = await api.put(`/clientes/${id}`, data);
        return response.data.data;
    },

    async delete(id) {
        const response = await api.delete(`/clientes/${id}`);
        return response.data;
    }
};
