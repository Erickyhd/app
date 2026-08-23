<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="blue-lighten-5" size="32" rounded="lg">
              <v-icon color="primary" size="18">mdi-contacts-outline</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Directorio de Clientes</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Gestión de cartera comercial y contactos</span>
            </div>
          </div>

          <!-- CONTROLES UNIFICADOS -->
          <div class="d-flex align-center flex-wrap gap-2">
            <v-text-field
              v-model="busquedaGlobal"
              placeholder="Buscar por cliente, email, teléfono..."
              prepend-inner-icon="mdi-magnify"
              density="compact"
              variant="outlined"
              hide-details
              clearable
              style="width: 250px;"
            ></v-text-field>

            <v-select
              v-model="filtroEstado"
              :items="[
                { title: 'Todos los Estados', value: 'todos' },
                { title: 'Activos', value: '1' },
                { title: 'Inactivos', value: '0' }
              ]"
              density="compact"
              variant="outlined"
              hide-details
              style="width: 150px;"
            ></v-select>

            <v-btn-toggle
              v-model="modoVista"
              mandatory
              color="primary"
              density="compact"
              variant="outlined"
              class="rounded-lg"
            >
              <v-btn value="tabla" size="small">
                <v-icon size="16">mdi-table</v-icon>
                <v-tooltip activator="parent" location="top">Vista Tabla</v-tooltip>
              </v-btn>
              <v-btn value="grid" size="small">
                <v-icon size="16">mdi-view-grid-outline</v-icon>
                <v-tooltip activator="parent" location="top">Vista Tarjetas</v-tooltip>
              </v-btn>
            </v-btn-toggle>

            <v-btn
              color="primary"
              variant="elevated"
              size="small"
              prepend-icon="mdi-account-plus"
              class="rounded-lg font-weight-bold text-capitalize shadow-btn"
              @click="abrirModalNuevo"
            >
              Nuevo Cliente
            </v-btn>
          </div>
        </div>

        <!-- 2. TARJETAS DE MÉTRICAS KPI -->
        <v-row density="compact" class="mb-3">
          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="blue-lighten-5" size="36" rounded="lg">
                    <v-icon color="primary" size="18">mdi-account-tie</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Total Clientes</div>
                    <div class="text-subtitle-1 font-weight-black text-grey-darken-3 line-height-tight">{{ totalClientes }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="blue" variant="flat" class="font-weight-bold">Cartera</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="green-lighten-5" size="36" rounded="lg">
                    <v-icon color="success" size="18">mdi-check-circle-outline</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Clientes Activos</div>
                    <div class="text-subtitle-1 font-weight-black text-success line-height-tight">{{ clientesActivos }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="success" variant="tonal" class="font-weight-bold">+{{ clientesActivos }} Operativos</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="purple-lighten-5" size="36" rounded="lg">
                    <v-icon color="purple" size="18">mdi-city</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Con Dirección</div>
                    <div class="text-subtitle-1 font-weight-black text-purple line-height-tight">{{ clientesConDireccion }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="purple" variant="tonal" class="font-weight-bold">Registrados</v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- 3. ALERTA DE ERROR -->
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
          Hubo un problema de conexión con el servidor al obtener los clientes.
          <v-btn size="x-small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <!-- 4. TABLA PRO -->
        <TablaClientes
          :clientes="clientesFiltrados"
          :cargando="isPending || eliminando"
          :modoVista="modoVista"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <!-- 5. DRAWER SLIDE-OVER -->
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
import { ref, computed } from 'vue';
import { useClientes, useMutarCliente, useEliminarCliente } from '../../hooks/useClientes';
import TablaClientes from '../components/TablaClientes.vue';
import ModalCliente from '../components/ModalCliente.vue';

const { clientes, isPending, isError, refetch } = useClientes();
const mutacionCliente = useMutarCliente();
const mutacionEliminar = useEliminarCliente();

const modalAbierto = ref(false);
const clienteAEditar = ref(null);
const eliminando = ref(false);

const busquedaGlobal = ref('');
const filtroEstado = ref('todos');
const modoVista = ref('tabla');

const totalClientes = computed(() => clientes.value?.length || 0);
const clientesActivos = computed(() => clientes.value?.filter(c => c.estado === 1).length || 0);
const clientesConDireccion = computed(() => clientes.value?.filter(c => !!c.direccion).length || 0);

const clientesFiltrados = computed(() => {
  if (!clientes.value) return [];
  return clientes.value.filter(c => {
    if (filtroEstado.value !== 'todos') {
      if (String(c.estado) !== filtroEstado.value) return false;
    }
    if (busquedaGlobal.value) {
      const q = busquedaGlobal.value.toLowerCase().trim();
      const nom = (c.nombre || '').toLowerCase();
      const email = (c.email || '').toLowerCase();
      const tel = (c.telefono || '').toLowerCase();
      if (!nom.includes(q) && !email.includes(q) && !tel.includes(q)) return false;
    }
    return true;
  });
});

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

<style scoped>
.gap-2 { gap: 8px; }
.line-height-tight { line-height: 1.2; }
.shadow-btn { box-shadow: 0 3px 10px 0 rgba(24, 103, 192, 0.35) !important; }
</style>
