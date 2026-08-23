<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="purple-lighten-5" size="32" rounded="lg">
              <v-icon color="purple" size="18">mdi-shield-crown-outline</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Rangos Administrativos</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Catálogo de cargos y niveles de autoridad</span>
            </div>
          </div>

          <!-- CONTROLES UNIFICADOS -->
          <div class="d-flex align-center flex-wrap gap-2">
            <v-text-field
              v-model="busquedaGlobal"
              placeholder="Buscar por cargo o nivel..."
              prepend-inner-icon="mdi-magnify"
              density="compact"
              variant="outlined"
              hide-details
              clearable
              style="width: 220px;"
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

            <v-btn
              color="primary"
              variant="elevated"
              size="small"
              prepend-icon="mdi-plus"
              class="rounded-lg font-weight-bold text-capitalize shadow-btn"
              @click="abrirModalNuevo"
            >
              Nuevo Rango
            </v-btn>
          </div>
        </div>

        <!-- 2. TARJETAS DE MÉTRICAS KPI -->
        <v-row density="compact" class="mb-3">
          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="purple-lighten-5" size="36" rounded="lg">
                    <v-icon color="purple" size="18">mdi-shield-crown</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Total Rangos</div>
                    <div class="text-subtitle-1 font-weight-black text-grey-darken-3 line-height-tight">{{ totalRangos }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="purple" variant="flat" class="font-weight-bold">Cargos</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="blue-lighten-5" size="36" rounded="lg">
                    <v-icon color="primary" size="18">mdi-star-circle</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Máximo Nivel</div>
                    <div class="text-subtitle-1 font-weight-black text-primary line-height-tight">Nivel {{ maxNivel }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="primary" variant="tonal" class="font-weight-bold">Autoridad</v-chip>
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
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Rangos Activos</div>
                    <div class="text-subtitle-1 font-weight-black text-success line-height-tight">{{ rangosActivos }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="success" variant="tonal" class="font-weight-bold">+{{ rangosActivos }} Activos</v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- 3. ALERTA DE ERROR -->
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
          Hubo un problema al obtener los rangos.
          <v-btn size="x-small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <!-- 4. TABLA ERP -->
        <TablaRangos
          :rangos="rangosFiltrados"
          :cargando="isPending || eliminando"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <!-- 5. DRAWER -->
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
import { ref, computed } from 'vue';
import { useRangos, useMutarRango, useEliminarRango } from '../../hooks/useRangos';
import TablaRangos from '../components/TablaRangos.vue';
import ModalRango from '../components/ModalRango.vue';

const { rangos, isPending, isError, refetch } = useRangos();
const mutacionRango = useMutarRango();
const mutacionEliminar = useEliminarRango();

const modalAbierto = ref(false);
const rangoAEditar = ref(null);
const eliminando = ref(false);

const busquedaGlobal = ref('');
const filtroEstado = ref('todos');

const totalRangos = computed(() => rangos.value?.length || 0);
const maxNivel = computed(() => {
  if (!rangos.value || rangos.value.length === 0) return 0;
  return Math.max(...rangos.value.map(r => r.nivel || 0));
});
const rangosActivos = computed(() => rangos.value?.filter(r => r.estado === 1).length || 0);

const rangosFiltrados = computed(() => {
  if (!rangos.value) return [];
  return rangos.value.filter(r => {
    if (filtroEstado.value !== 'todos') {
      if (String(r.estado) !== filtroEstado.value) return false;
    }
    if (busquedaGlobal.value) {
      const q = busquedaGlobal.value.toLowerCase().trim();
      const nom = (r.nombre || '').toLowerCase();
      const nivel = String(r.nivel || '');
      if (!nom.includes(q) && !nivel.includes(q)) return false;
    }
    return true;
  });
});

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

<style scoped>
.gap-2 { gap: 8px; }
.line-height-tight { line-height: 1.2; }
.shadow-btn { box-shadow: 0 3px 10px 0 rgba(24, 103, 192, 0.35) !important; }
</style>
