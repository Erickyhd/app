<template>
  <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
    <v-row>
      <v-col cols="12">
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-4">
          Hubo un problema de conexión con el servidor al obtener los clientes.
          <v-btn size="small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <TablaClientes 
          :clientes="clientes" 
          :cargando="isPending || eliminando" 
          @nuevo="abrirModalNuevo"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <ModalCliente
          v-model="modalAbierto"
          :clienteAEditar="clienteAEditar"
          :guardando="mutacionCliente.isPending.value"
          @guardar="guardarCliente"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useClientes, useMutarCliente, useEliminarCliente } from '../../hooks/useClientes';
import TablaClientes from '../components/TablaClientes.vue';
import ModalCliente from '../components/ModalCliente.vue';

// 1. Hooks de Vue Query
const { clientes, isPending, isError, refetch } = useClientes();
const mutacionCliente = useMutarCliente();
const mutacionEliminar = useEliminarCliente();

// 2. Estado Local de la Vista
const modalAbierto = ref(false);
const clienteAEditar = ref(null);
const eliminando = ref(false);

// 3. Lógica de UI (Handlers)
const abrirModalNuevo = () => {
  clienteAEditar.value = null;
  modalAbierto.value = true;
};

const abrirModalEditar = (cliente) => {
  clienteAEditar.value = { ...cliente };
  modalAbierto.value = true;
};

const guardarCliente = async (formData) => {
  mutacionCliente.mutate(formData, {
    onSuccess: () => {
      modalAbierto.value = false;
    },
    onError: (error) => {
      console.error('Error guardando cliente:', error);
      alert('Error al guardar: ' + (error.response?.data?.message || error.message));
    }
  });
};

const confirmarEliminar = async (cliente) => {
  if (confirm(`¿Estás seguro de que deseas eliminar al cliente "${cliente.nombre}"?`)) {
    eliminando.value = true;
    mutacionEliminar.mutate(cliente.id, {
      onSettled: () => {
        eliminando.value = false;
      },
      onError: (error) => {
        console.error('Error eliminando cliente:', error);
        alert('Error al eliminar: ' + (error.response?.data?.message || error.message));
      }
    });
  }
};
</script>
