<template>
  <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
    <v-row>
      <v-col cols="12">
        <!-- Alerta de Error Global -->
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-4">
          Hubo un problema de conexión con el servidor.
          <v-btn size="small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <!-- Componente Tonto: Tabla -->
        <TablaJerarquias 
          :jerarquias="jerarquias" 
          :cargando="isPending || eliminando" 
          @nuevo="abrirModalNuevo"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <!-- Componente Tonto: Modal de Formulario -->
        <ModalJerarquia
          v-model="modalAbierto"
          :jerarquiaAEditar="jerarquiaAEditar"
          :todasLasJerarquias="data"
          :guardando="mutacionJerarquia.isPending.value"
          @guardar="guardarJerarquia"
        />

      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useJerarquias, useMutarJerarquia, useEliminarJerarquia } from '../../hooks/useJerarquias';
import TablaJerarquias from '../components/TablaJerarquias.vue';
import ModalJerarquia from '../components/ModalJerarquia.vue';

// 1. Hooks de Vue Query
const { data, jerarquias, isPending, isError, refetch } = useJerarquias();
const mutacionJerarquia = useMutarJerarquia();
const mutacionEliminar = useEliminarJerarquia();

// 2. Estado Local de la Vista
const modalAbierto = ref(false);
const jerarquiaAEditar = ref(null);
const eliminando = ref(false);

// 3. Lógica de UI (Handlers)
const abrirModalNuevo = () => {
  jerarquiaAEditar.value = null;
  modalAbierto.value = true;
};

const abrirModalEditar = (jerarquia) => {
  // Pasamos una copia para no mutar directamente el estado de la tabla
  jerarquiaAEditar.value = { ...jerarquia };
  modalAbierto.value = true;
};

const guardarJerarquia = async (formData) => {
  mutacionJerarquia.mutate(formData, {
    onSuccess: () => {
      modalAbierto.value = false;
      // Aquí podrías integrar tu sistema de Notificaciones/Toasts
    },
    onError: (error) => {
      console.error('Error guardando jerarquía:', error);
      alert('Error al guardar: ' + (error.response?.data?.message || error.message));
    }
  });
};

const confirmarEliminar = async (jerarquia) => {
  if (confirm(`¿Estás seguro de que deseas eliminar la jerarquía "${jerarquia.nombre}"?`)) {
    eliminando.value = true;
    mutacionEliminar.mutate(jerarquia.id, {
      onSettled: () => {
        eliminando.value = false;
      },
      onError: (error) => {
        console.error('Error eliminando jerarquía:', error);
        alert('Error al eliminar: ' + (error.response?.data?.message || error.message));
      }
    });
  }
};
</script>
