<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="indigo-lighten-5" size="32" rounded="lg">
              <v-icon color="primary" size="18">mdi-shield-lock-outline</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Gestión de Roles y Permisos (IAM)</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Políticas de seguridad y asignación de accesos por usuario</span>
            </div>
          </div>

          <!-- CONTROLES UNIFICADOS (BÚSQUEDA GLOBAL + ESTADO) -->
          <div class="d-flex align-center flex-wrap gap-2">
            <v-text-field
              v-model="busquedaGlobal"
              placeholder="Buscar por usuario o correo..."
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
          </div>
        </div>

        <!-- 2. TARJETAS DE MÉTRICAS KPI -->
        <v-row density="compact" class="mb-3">
          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="indigo-lighten-5" size="36" rounded="lg">
                    <v-icon color="indigo" size="18">mdi-account-key-outline</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Total Cuentas</div>
                    <div class="text-subtitle-1 font-weight-black text-grey-darken-3 line-height-tight">{{ totalUsuarios }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="indigo" variant="flat" class="font-weight-bold">Cuentas</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="green-lighten-5" size="36" rounded="lg">
                    <v-icon color="success" size="18">mdi-shield-check-outline</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Operativos</div>
                    <div class="text-subtitle-1 font-weight-black text-success line-height-tight">{{ usuariosActivos }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="success" variant="tonal" class="font-weight-bold">+{{ usuariosActivos }} Habilitados</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="purple-lighten-5" size="36" rounded="lg">
                    <v-icon color="purple" size="18">mdi-security</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Seguridad IAM</div>
                    <div class="text-subtitle-1 font-weight-black text-purple line-height-tight">100%</div>
                  </div>
                </div>
                <v-chip size="x-small" color="purple" variant="tonal" class="font-weight-bold">Protegido</v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- 3. ALERTA DE ERROR -->
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
          Hubo un problema al obtener los usuarios para accesos.
          <v-btn size="x-small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <!-- 4. TABLA ERP CON CABECERA LIMPIA -->
        <v-card class="elevation-2 rounded-lg border overflow-hidden bg-white">
          <v-data-table
            :headers="cabeceras"
            :items="usuariosFiltrados"
            :loading="isPending"
            density="compact"
            hover
            class="elevation-0 tabla-fabhub"
          >
            <template #item.usuario="{ item }">
              <div class="d-flex align-center py-1">
                <v-avatar color="primary" size="32" class="mr-2 text-white text-caption font-weight-bold elevation-1">
                  {{ obtenerIniciales(item) }}
                </v-avatar>
                <div>
                  <div class="font-weight-bold text-caption text-grey-darken-3">
                    {{ item.trabajador ? `${item.trabajador.nombres} ${item.trabajador.apellidos || ''}` : 'Usuario' }}
                  </div>
                  <div class="text-caption text-grey font-weight-medium" style="font-size: 11px;">
                    ID: #{{ item.id }}
                  </div>
                </div>
              </div>
            </template>

            <template #item.email="{ item }">
              <span class="text-caption text-grey-darken-2">{{ item.email }}</span>
            </template>
            
            <template #item.estado="{ item }">
              <div class="d-flex align-center justify-center">
                <span
                  class="status-pulse-dot mr-1"
                  :class="item.estado === 1 ? 'bg-success' : 'bg-error'"
                ></span>
                <v-chip
                  :color="item.estado === 1 ? 'success' : 'error'"
                  size="x-small"
                  variant="flat"
                  class="font-weight-bold"
                >
                  {{ item.estado === 1 ? 'Activo' : 'Inactivo' }}
                </v-chip>
              </div>
            </template>

            <template #item.acciones="{ item }">
              <v-btn 
                color="primary" 
                variant="tonal" 
                size="x-small" 
                prepend-icon="mdi-shield-edit-outline"
                class="rounded font-weight-bold text-capitalize"
                @click="abrirAsignador(item)"
              >
                Gestionar Accesos
                <v-tooltip activator="parent" location="top">Configurar privilegios de {{ item.email }}</v-tooltip>
              </v-btn>
            </template>

            <template #no-data>
              <div class="text-center pa-6 text-grey text-caption">No se encontraron usuarios coincidentes.</div>
            </template>
          </v-data-table>
        </v-card>

        <!-- DRAWER SLIDE-OVER DE PERMISOS -->
        <AsignadorPermisos
          v-if="modalAbierto"
          v-model="modalAbierto"
          :usuario="usuarioSeleccionado"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useUsuarios } from '../../hooks/useUsuarios';
import AsignadorPermisos from '../components/AsignadorPermisos.vue';

const { data: usuarios, isPending, isError, refetch } = useUsuarios();

const busquedaGlobal = ref('');
const filtroEstado = ref('todos');

const cabeceras = [
  { title: 'USUARIO', key: 'usuario', align: 'start' },
  { title: 'CORREO ELECTRÓNICO', key: 'email', align: 'start' },
  { title: 'ESTADO', key: 'estado', align: 'center', width: '110px' },
  { title: 'ACCESOS', key: 'acciones', align: 'end', sortable: false, width: '160px' },
];

const totalUsuarios = computed(() => usuarios.value?.length || 0);
const usuariosActivos = computed(() => usuarios.value?.filter(u => u.estado === 1).length || 0);

const usuariosFiltrados = computed(() => {
  if (!usuarios.value) return [];
  return usuarios.value.filter(u => {
    if (filtroEstado.value !== 'todos') {
      if (String(u.estado) !== filtroEstado.value) return false;
    }
    if (busquedaGlobal.value) {
      const q = busquedaGlobal.value.toLowerCase().trim();
      const n = `${u.trabajador?.nombres || ''} ${u.trabajador?.apellidos || ''}`.toLowerCase();
      const email = (u.email || '').toLowerCase();
      if (!n.includes(q) && !email.includes(q)) return false;
    }
    return true;
  });
});

const modalAbierto = ref(false);
const usuarioSeleccionado = ref(null);

const abrirAsignador = (usuario) => {
  usuarioSeleccionado.value = usuario;
  modalAbierto.value = true;
};

const obtenerIniciales = (item) => {
  const t = item.trabajador;
  if (!t || (!t.nombres && !t.apellidos)) return 'US';
  const n = t.nombres || '';
  const a = t.apellidos || '';
  return `${n.charAt(0)}${a.charAt(0)}`.toUpperCase();
};
</script>

<style scoped>
.status-pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
  box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
  animation: pulse 1.8s infinite;
}
@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(76, 175, 80, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(76, 175, 80, 0); }
}
.gap-2 { gap: 8px; }
.line-height-tight { line-height: 1.2; }
</style>
