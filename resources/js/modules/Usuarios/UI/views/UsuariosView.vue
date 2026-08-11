<template>
  <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
    <v-row>
      <v-col cols="12">
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-4">
          Hubo un problema de conexión con el servidor al obtener los usuarios.
          <v-btn size="small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <TablaUsuarios 
          :usuarios="data || []" 
          :cargando="isPending || eliminando" 
          @nuevo="abrirModalNuevo"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <ModalUsuario
          v-model="modalAbierto"
          :usuarioAEditar="usuarioAEditar"
          :guardando="mutacionUsuario.isPending.value"
          @guardar="guardarUsuario"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useUsuarios, useMutarUsuario, useEliminarUsuario } from '../../hooks/useUsuarios';
import TablaUsuarios from '../components/TablaUsuarios.vue';
import ModalUsuario from '../components/ModalUsuario.vue';

// 1. Hooks de Vue Query
const { data, isPending, isError, refetch } = useUsuarios();
const mutacionUsuario = useMutarUsuario();
const mutacionEliminar = useEliminarUsuario();

// 2. Estado Local de la Vista
const modalAbierto = ref(false);
const usuarioAEditar = ref(null);
const eliminando = ref(false);

// 3. Lógica de UI (Handlers)
const abrirModalNuevo = () => {
  usuarioAEditar.value = null;
  modalAbierto.value = true;
};

const abrirModalEditar = (usuario) => {
  usuarioAEditar.value = { ...usuario };
  modalAbierto.value = true;
};

const guardarUsuario = async (formData) => {
  mutacionUsuario.mutate(formData, {
    onSuccess: () => {
      modalAbierto.value = false;
    },
    onError: (error) => {
      console.error('Error guardando usuario:', error);
      alert('Error al guardar: ' + (error.response?.data?.message || error.message));
    }
  });
};

const confirmarEliminar = async (usuario) => {
  if (confirm(`¿Estás seguro de que deseas eliminar al usuario "${usuario.nombres}"?`)) {
    eliminando.value = true;
    mutacionEliminar.mutate(usuario.id, {
      onSettled: () => {
        eliminando.value = false;
      },
      onError: (error) => {
        console.error('Error eliminando usuario:', error);
        alert('Error al eliminar: ' + (error.response?.data?.message || error.message));
      }
    });
  }
};
</script>
