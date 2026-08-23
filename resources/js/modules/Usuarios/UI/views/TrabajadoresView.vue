<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="blue-lighten-5" size="32" rounded="lg">
              <v-icon color="primary" size="18">mdi-account-hard-hat</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Directorio de Trabajadores</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Fichas de personal y asignación institucional</span>
            </div>
          </div>

          <!-- CONTROLES UNIFICADOS (BÚSQUEDA GLOBAL + ESTADO + BOTÓN) -->
          <div class="d-flex align-center flex-wrap gap-2">
            <v-text-field
              v-model="busquedaGlobal"
              placeholder="Buscar trabajador, DNI, teléfono..."
              prepend-inner-icon="mdi-magnify"
              density="compact"
              variant="outlined"
              hide-details
              clearable
              style="width: 240px;"
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
              Nuevo Trabajador
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
                    <v-icon color="primary" size="18">mdi-account-group</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Total Personal</div>
                    <div class="text-subtitle-1 font-weight-black text-grey-darken-3 line-height-tight">{{ totalTrabajadores }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="blue" variant="flat" class="font-weight-bold">Personal</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="green-lighten-5" size="36" rounded="lg">
                    <v-icon color="success" size="18">mdi-account-check</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Activos</div>
                    <div class="text-subtitle-1 font-weight-black text-success line-height-tight">{{ trabajadoresActivos }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="success" variant="tonal" class="font-weight-bold">+{{ trabajadoresActivos }} Operativos</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="purple-lighten-5" size="36" rounded="lg">
                    <v-icon color="purple" size="18">mdi-link-variant</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Con Usuario</div>
                    <div class="text-subtitle-1 font-weight-black text-purple line-height-tight">{{ trabajadoresVinculados }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="purple" variant="tonal" class="font-weight-bold">Vinculados</v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- 3. MENSAJE DE ERROR -->
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
          Hubo un problema de conexión al obtener los trabajadores.
          <v-btn size="x-small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <!-- 4. TABLA PRO LIMPIA -->
        <TablaTrabajadores
          :trabajadores="trabajadoresFiltrados"
          :cargando="isPending || eliminando"
          :modoVista="modoVista"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <!-- 5. DRAWER SLIDE-OVER -->
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
import { ref, computed } from 'vue';
import { useTrabajadores, useMutarTrabajador, useEliminarTrabajador } from '../../hooks/useTrabajadores';
import TablaTrabajadores from '../components/TablaTrabajadores.vue';
import ModalTrabajador from '../components/ModalTrabajador.vue';

const { trabajadores, isPending, isError, refetch } = useTrabajadores();
const mutacionTrabajador = useMutarTrabajador();
const mutacionEliminar = useEliminarTrabajador();

const modalAbierto = ref(false);
const trabajadorAEditar = ref(null);
const eliminando = ref(false);

const busquedaGlobal = ref('');
const filtroEstado = ref('todos');
const modoVista = ref('tabla');

const totalTrabajadores = computed(() => trabajadores.value?.length || 0);
const trabajadoresActivos = computed(() => trabajadores.value?.filter(t => t.estado === 1).length || 0);
const trabajadoresVinculados = computed(() => trabajadores.value?.filter(t => t.usuario_id || t.user_id).length || 0);

const trabajadoresFiltrados = computed(() => {
  if (!trabajadores.value) return [];
  return trabajadores.value.filter(t => {
    if (filtroEstado.value !== 'todos') {
      if (String(t.estado) !== filtroEstado.value) return false;
    }
    if (busquedaGlobal.value) {
      const q = busquedaGlobal.value.toLowerCase().trim();
      const n = `${t.nombres || ''} ${t.apellidos || ''}`.toLowerCase();
      const doc = (t.numero_documento || '').toLowerCase();
      const tel = (t.telefono_principal || '').toLowerCase();
      if (!n.includes(q) && !doc.includes(q) && !tel.includes(q)) return false;
    }
    return true;
  });
});

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

<style scoped>
.gap-2 { gap: 8px; }
.line-height-tight { line-height: 1.2; }
.shadow-btn { box-shadow: 0 3px 10px 0 rgba(24, 103, 192, 0.35) !important; }
</style>
