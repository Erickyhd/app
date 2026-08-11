import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { clienteService } from '../services/clienteService';
import { computed } from 'vue';

export const useClientes = () => {
    const query = useQuery({
        queryKey: ['clientes'],
        queryFn: clienteService.getAll,
    });

    const clientesFormateados = computed(() => {
        if (!query.data.value) return [];
        return query.data.value.map(item => ({
            ...item,
            created_at_formatted: item.created_at ? new Date(item.created_at).toLocaleDateString('es-ES') : '-',
        }));
    });

    return {
        ...query,
        clientes: clientesFormateados
    };
};

export const useMutarCliente = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data) => {
            if (data.id) {
                return await clienteService.update(data.id, data);
            } else {
                return await clienteService.create(data);
            }
        },
        onSuccess: () => {
            queryClient.invalidateQueries(['clientes']);
        }
    });
};

export const useEliminarCliente = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: (id) => clienteService.delete(id),
        onSuccess: () => {
            queryClient.invalidateQueries(['clientes']);
        }
    });
};
