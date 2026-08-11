import api from '../../../core/http/api';

/**
 * Servicio de Usuarios
 * 
 * Se encarga EXCLUSIVAMENTE de hacer las peticiones HTTP al backend (Axios).
 * No maneja estados de UI ni lógicas de componentes.
 */
export const UsuarioService = {
    /**
     * Obtiene la lista de usuarios desde el backend
     * @returns {Promise<Array>} Lista de usuarios
     */
    async obtenerTodos() {
        const respuesta = await api.get('/usuarios'); // The route is now /usuarios
        return respuesta.data.data;
    },

    async crear(datosUsuario) {
        const respuesta = await api.post('/usuarios', datosUsuario);
        return respuesta.data.data;
    },

    async actualizar(id, datosUsuario) {
        const respuesta = await api.put(`/usuarios/${id}`, datosUsuario);
        return respuesta.data.data;
    },

    async eliminar(id) {
        const respuesta = await api.delete(`/usuarios/${id}`);
        return respuesta.data;
    }
};
