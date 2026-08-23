<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="indigo-lighten-5" size="32" rounded="lg">
              <v-icon color="indigo" size="18">mdi-sitemap</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Jerarquías Institucionales</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Estructura organizacional y dependencias de áreas</span>
            </div>
          </div>

          <!-- CONTROLES UNIFICADOS -->
          <div class="d-flex align-center flex-wrap gap-2">
            <v-text-field
              v-model="busquedaGlobal"
              placeholder="Buscar por área, código, dependencia..."
              prepend-inner-icon="mdi-magnify"
              density="compact"
              variant="outlined"
              hide-details
              clearable
              style="width: 260px;"
            ></v-text-field>

            <v-btn
              color="primary"
              variant="elevated"
              size="small"
              prepend-icon="mdi-plus"
              class="rounded-lg font-weight-bold text-capitalize shadow-btn"
              @click="abrirModalNuevo"
            >
              Nueva Jerarquía
            </v-btn>
          </div>
        </div>

        <!-- 2. TARJETAS DE MÉTRICAS KPI -->
        <v-row density="compact" class="mb-3">
          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="indigo-lighten-5" size="36" rounded="lg">
                    <v-icon color="indigo" size="18">mdi-sitemap-outline</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Total Jerarquías</div>
                    <div class="text-subtitle-1 font-weight-black text-grey-darken-3 line-height-tight">{{ totalJerarquias }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="indigo" variant="flat" class="font-weight-bold">Nodos</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="blue-lighten-5" size="36" rounded="lg">
                    <v-icon color="primary" size="18">mdi-source-branch</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Principales</div>
                    <div class="text-subtitle-1 font-weight-black text-primary line-height-tight">{{ jerarquiasPrincipales }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="primary" variant="tonal" class="font-weight-bold">Raíz</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="purple-lighten-5" size="36" rounded="lg">
                    <v-icon color="purple" size="18">mdi-office-building-cog-outline</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Sub-Áreas</div>
                    <div class="text-subtitle-1 font-weight-black text-purple line-height-tight">{{ subJerarquias }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="purple" variant="tonal" class="font-weight-bold">Dependientes</v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- 3. ALERTA DE ERROR -->
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
          Hubo un problema al obtener las jerarquías.
          <v-btn size="x-small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <!-- 4. TABLA ERP -->
        <TablaJerarquias
          :jerarquias="jerarquiasFiltradas"
          :cargando="isPending || eliminando"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <!-- 5. DRAWER -->
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
import { ref, computed } from 'vue';
import { useJerarquias, useMutarJerarquia, useEliminarJerarquia } from '../../hooks/useJerarquias';
import TablaJerarquias from '../components/TablaJerarquias.vue';
import ModalJerarquia from '../components/ModalJerarquia.vue';

const { data, jerarquias, isPending, isError, refetch } = useJerarquias();
const mutacionJerarquia = useMutarJerarquia();
const mutacionEliminar = useEliminarJerarquia();

const modalAbierto = ref(false);
const jerarquiaAEditar = ref(null);
const eliminando = ref(false);
const busquedaGlobal = ref('');

const totalJerarquias = computed(() => data.value?.length || 0);
const jerarquiasPrincipales = computed(() => data.value?.filter(j => !j.jerarquia_padre_id).length || 0);
const subJerarquias = computed(() => data.value?.filter(j => !!j.jerarquia_padre_id).length || 0);

const jerarquiasFiltradas = computed(() => {
  if (!jerarquias.value) return [];
  if (!busquedaGlobal.value) return jerarquias.value;
  const q = busquedaGlobal.value.toLowerCase().trim();
  return jerarquias.value.filter(j => {
    const cod = (j.codigo || '00' + j.id).toLowerCase();
    const nom = (j.nombre || '').toLowerCase();
    const padre = (j.padre_nombre || '').toLowerCase();
    return cod.includes(q) || nom.includes(q) || padre.includes(q);
  });
});

const abrirModalNuevo = () => {
  jerarquiaAEditar.value = null;
  modalAbierto.value = true;
};

const abrirModalEditar = (jerarquia) => {
  jerarquiaAEditar.value = { ...jerarquia };
  modalAbierto.value = true;
};

const guardarJerarquia = async (formData) => {
  mutacionJerarquia.mutate(formData, {
    onSuccess: () => {
      modalAbierto.value = false;
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

<style scoped>
.gap-2 { gap: 8px; }
.line-height-tight { line-height: 1.2; }
.shadow-btn { box-shadow: 0 3px 10px 0 rgba(24, 103, 192, 0.35) !important; }
</style>
