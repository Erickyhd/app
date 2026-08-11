import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { UsuarioService } from '../services/UsuarioService';

/**
 * Hook: useUsuarios
 * 
 * Envuelve las llamadas al servicio con Vue Query para manejar
 * caché, estado de carga y estado de error automáticamente.
 */
export function useUsuarios() {
    return useQuery({
        queryKey: ['usuarios'],
        queryFn: () => UsuarioService.obtenerTodos(),
        staleTime: 1000 * 60 * 5,
        retry: 2
    });
}

export function useMutarUsuario() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (datos) => {
            if (datos.id) {
                return await UsuarioService.actualizar(datos.id, datos);
            } else {
                return await UsuarioService.crear(datos);
            }
        },
        onSuccess: () => {
            queryClient.invalidateQueries(['usuarios']);
        }
    });
}

export function useEliminarUsuario() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: (id) => UsuarioService.eliminar(id),
        onSuccess: () => {
            queryClient.invalidateQueries(['usuarios']);
        }
    });
}
