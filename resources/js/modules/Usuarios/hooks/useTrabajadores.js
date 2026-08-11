import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { trabajadorService } from '../services/trabajadorService';
import { computed } from 'vue';

export const useTrabajadores = () => {
    const query = useQuery({
        queryKey: ['trabajadores'],
        queryFn: trabajadorService.getAll,
    });

    const trabajadoresFormateados = computed(() => {
        if (!query.data.value) return [];
        return query.data.value.map(item => ({
            ...item,
            nombre_completo: `${item.nombres} ${item.apellidos}`,
            usuario_vinculado: item.user ? item.user.nombres : 'Sin Usuario',
            fecha_contratacion_formatted: item.fecha_contratacion ? new Date(item.fecha_contratacion).toLocaleDateString('es-ES') : '-',
        }));
    });

    return {
        ...query,
        trabajadores: trabajadoresFormateados
    };
};

export const useMutarTrabajador = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data) => {
            if (data.id) {
                return await trabajadorService.update(data.id, data);
            } else {
                return await trabajadorService.create(data);
            }
        },
        onSuccess: () => {
            queryClient.invalidateQueries(['trabajadores']);
        }
    });
};

export const useEliminarTrabajador = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: (id) => trabajadorService.delete(id),
        onSuccess: () => {
            queryClient.invalidateQueries(['trabajadores']);
        }
    });
};
