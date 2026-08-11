<template>
  <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
    <v-row>
      <v-col cols="12">
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-4">
          Hubo un problema de conexión con el servidor al obtener los rangos.
          <v-btn size="small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <TablaRangos 
          :rangos="rangos" 
          :cargando="isPending || eliminando" 
          @nuevo="abrirModalNuevo"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <ModalRango
          v-model="modalAbierto"
          :rangoAEditar="rangoAEditar"
          :guardando="mutacionRango.isPending.value"
          @guardar="guardarRango"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useRangos, useMutarRango, useEliminarRango } from '../../hooks/useRangos';
import TablaRangos from '../components/TablaRangos.vue';
import ModalRango from '../components/ModalRango.vue';

// 1. Hooks de Vue Query
const { rangos, isPending, isError, refetch } = useRangos();
const mutacionRango = useMutarRango();
const mutacionEliminar = useEliminarRango();

// 2. Estado Local de la Vista
const modalAbierto = ref(false);
const rangoAEditar = ref(null);
const eliminando = ref(false);

// 3. Lógica de UI (Handlers)
const abrirModalNuevo = () => {
  rangoAEditar.value = null;
  modalAbierto.value = true;
};

const abrirModalEditar = (rango) => {
  rangoAEditar.value = { ...rango };
  modalAbierto.value = true;
};

const guardarRango = async (formData) => {
  mutacionRango.mutate(formData, {
    onSuccess: () => {
      modalAbierto.value = false;
    },
    onError: (error) => {
      console.error('Error guardando rango:', error);
      alert('Error al guardar: ' + (error.response?.data?.message || error.message));
    }
  });
};

const confirmarEliminar = async (rango) => {
  if (confirm(`¿Estás seguro de que deseas eliminar el rango "${rango.nombre}"?`)) {
    eliminando.value = true;
    mutacionEliminar.mutate(rango.id, {
      onSettled: () => {
        eliminando.value = false;
      },
      onError: (error) => {
        console.error('Error eliminando rango:', error);
        alert('Error al eliminar: ' + (error.response?.data?.message || error.message));
      }
    });
  }
};
</script>
