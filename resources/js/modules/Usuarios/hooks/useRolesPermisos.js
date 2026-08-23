import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { rolesPermisosService } from '../services/rolesPermisosService';

export function useRoles() {
    return useQuery({
        queryKey: ['roles'],
        queryFn: () => rolesPermisosService.getRoles(),
        staleTime: 1000 * 60 * 60, // 1 hora
    });
}

export function usePermisos() {
    return useQuery({
        queryKey: ['permisos'],
        queryFn: () => rolesPermisosService.getPermisos(),
        staleTime: 1000 * 60 * 60, // 1 hora
    });
}

export function useUserPermissions(userId) {
    return useQuery({
        queryKey: ['user-permissions', userId],
        queryFn: () => rolesPermisosService.getUserPermissions(userId.value || userId),
        enabled: !!(userId.value || userId),
    });
}

export function useSyncUserPermissions() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: ({ userId, data }) => rolesPermisosService.syncUserPermissions(userId, data),
        onSuccess: (_, variables) => {
            queryClient.invalidateQueries(['user-permissions', variables.userId]);
            queryClient.invalidateQueries(['usuarios']); // por si se muestran roles en la tabla
        }
    });
}
