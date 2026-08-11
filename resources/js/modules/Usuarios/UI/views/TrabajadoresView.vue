<template>
  <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
    <v-row>
      <v-col cols="12">
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-4">
          Hubo un problema de conexión con el servidor al obtener los trabajadores.
          <v-btn size="small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <TablaTrabajadores 
          :trabajadores="trabajadores" 
          :cargando="isPending || eliminando" 
          @nuevo="abrirModalNuevo"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <ModalTrabajador
          v-model="modalAbierto"
          :trabajadorAEditar="trabajadorAEditar"
          :guardando="mutacionTrabajador.isPending.value"
          @guardar="guardarTrabajador"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useTrabajadores, useMutarTrabajador, useEliminarTrabajador } from '../../hooks/useTrabajadores';
import TablaTrabajadores from '../components/TablaTrabajadores.vue';
import ModalTrabajador from '../components/ModalTrabajador.vue';

// 1. Hooks de Vue Query
const { trabajadores, isPending, isError, refetch } = useTrabajadores();
const mutacionTrabajador = useMutarTrabajador();
const mutacionEliminar = useEliminarTrabajador();

// 2. Estado Local de la Vista
const modalAbierto = ref(false);
const trabajadorAEditar = ref(null);
const eliminando = ref(false);

// 3. Lógica de UI (Handlers)
const abrirModalNuevo = () => {
  trabajadorAEditar.value = null;
  modalAbierto.value = true;
};

const abrirModalEditar = (trabajador) => {
  trabajadorAEditar.value = { ...trabajador };
  modalAbierto.value = true;
};

const guardarTrabajador = async (formData) => {
  mutacionTrabajador.mutate(formData, {
    onSuccess: () => {
      modalAbierto.value = false;
    },
    onError: (error) => {
      console.error('Error guardando trabajador:', error);
      alert('Error al guardar: ' + (error.response?.data?.message || error.message));
    }
  });
};

const confirmarEliminar = async (trabajador) => {
  if (confirm(`¿Estás seguro de que deseas eliminar a "${trabajador.nombres} ${trabajador.apellidos}"?`)) {
    eliminando.value = true;
    mutacionEliminar.mutate(trabajador.id, {
      onSettled: () => {
        eliminando.value = false;
      },
      onError: (error) => {
        console.error('Error eliminando trabajador:', error);
        alert('Error al eliminar: ' + (error.response?.data?.message || error.message));
      }
    });
  }
};
</script>
