import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { jerarquiaService } from '../services/jerarquiaService';
import { computed } from 'vue';

// 1. Hook para Obtener (Read)
export const useJerarquias = () => {
    const query = useQuery({
        queryKey: ['jerarquias'],
        queryFn: jerarquiaService.getAll,
    });

    // Formateamos directamente aquí sin necesidad de un Modelo extra
    const jerarquiasFormateadas = computed(() => {
        if (!query.data.value) return [];
        return query.data.value.map(item => ({
            ...item,
            padre_nombre: item.parent ? item.parent.nombre : 'Jerarquía Principal',
            created_at_formatted: item.created_at ? new Date(item.created_at).toLocaleDateString('es-ES') : '-',
        }));
    });

    return {
        ...query,
        jerarquias: jerarquiasFormateadas
    };
};

// 2. Hook para Crear o Editar (Create / Update)
export const useMutarJerarquia = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (datos) => {
            if (datos.id) {
                return await jerarquiaService.update(datos.id, datos);
            } else {
                return await jerarquiaService.create(datos);
            }
        },
        onSuccess: () => {
            // Invalida el caché para que la tabla se refresque automáticamente
            queryClient.invalidateQueries(['jerarquias']);
        }
    });
};

// 3. Hook para Eliminar (Delete)
export const useEliminarJerarquia = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: (id) => jerarquiaService.delete(id),
        onSuccess: () => {
            queryClient.invalidateQueries(['jerarquias']);
        }
    });
};
