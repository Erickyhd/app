import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { rangoService } from '../services/rangoService';
import { computed } from 'vue';

export const useRangos = () => {
    const query = useQuery({
        queryKey: ['rangos'],
        queryFn: rangoService.getAll,
    });

    const rangosFormateados = computed(() => {
        if (!query.data.value) return [];
        return query.data.value.map(item => ({
            ...item,
            created_at_formatted: item.created_at ? new Date(item.created_at).toLocaleDateString('es-ES') : '-',
        }));
    });

    return {
        ...query,
        rangos: rangosFormateados
    };
};

export const useMutarRango = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data) => {
            if (data.id) {
                return await rangoService.update(data.id, data);
            } else {
                return await rangoService.create(data);
            }
        },
        onSuccess: () => {
            queryClient.invalidateQueries(['rangos']);
        }
    });
};

export const useEliminarRango = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: (id) => rangoService.delete(id),
        onSuccess: () => {
            queryClient.invalidateQueries(['rangos']);
        }
    });
};
