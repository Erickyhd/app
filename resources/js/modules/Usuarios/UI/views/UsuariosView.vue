<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="blue-lighten-5" size="32" rounded="lg">
              <v-icon color="primary" size="18">mdi-shield-account-outline</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Gestión de Usuarios</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Directorio general de accesos y roles</span>
            </div>
          </div>

          <!-- CONTROLES UNIFICADOS (BÚSQUEDA GLOBAL + ESTADO + BOTÓN) -->
          <div class="d-flex align-center flex-wrap gap-2">
            <v-text-field
              v-model="busquedaGlobal"
              placeholder="Buscar por nombre, DNI, correo..."
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
              Nuevo Usuario
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
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Total Usuarios</div>
                    <div class="text-subtitle-1 font-weight-black text-grey-darken-3 line-height-tight">{{ totalUsuarios }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="blue" variant="flat" class="font-weight-bold">Sistema</v-chip>
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
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Usuarios Activos</div>
                    <div class="text-subtitle-1 font-weight-black text-success line-height-tight">{{ usuariosActivos }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="success" variant="tonal" class="font-weight-bold">+{{ usuariosActivos }} Activos</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="purple-lighten-5" size="36" rounded="lg">
                    <v-icon color="purple" size="18">mdi-sitemap</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Jerarquías</div>
                    <div class="text-subtitle-1 font-weight-black text-purple line-height-tight">{{ totalJerarquias }}</div>
                  </div>
                </div>
                <v-chip size="x-small" color="purple" variant="tonal" class="font-weight-bold">100% Asignado</v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- 3. MENSAJE DE ERROR SI EXISTE -->
        <v-alert v-if="isError" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
          Hubo un problema de conexión con el servidor al obtener los usuarios.
          <v-btn size="x-small" color="error" variant="text" @click="refetch">Reintentar</v-btn>
        </v-alert>

        <!-- 4. TABLA PRO LIMPIA -->
        <TablaUsuarios
          :usuarios="usuariosFiltrados"
          :cargando="isPending || eliminando"
          :modoVista="modoVista"
          @editar="abrirModalEditar"
          @eliminar="confirmarEliminar"
        />

        <!-- 5. DRAWER SLIDE-OVER PARA CREAR / EDITAR -->
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
import { ref, computed } from 'vue';
import { useUsuarios, useMutarUsuario, useEliminarUsuario } from '../../hooks/useUsuarios';
import TablaUsuarios from '../components/TablaUsuarios.vue';
import ModalUsuario from '../components/ModalUsuario.vue';

const { data, isPending, isError, refetch } = useUsuarios();
const mutacionUsuario = useMutarUsuario();
const mutacionEliminar = useEliminarUsuario();

const modalAbierto = ref(false);
const usuarioAEditar = ref(null);
const eliminando = ref(false);

const busquedaGlobal = ref('');
const filtroEstado = ref('todos');
const modoVista = ref('tabla');

const totalUsuarios = computed(() => data.value?.length || 0);
const usuariosActivos = computed(() => data.value?.filter(u => u.estado === 1).length || 0);
const totalJerarquias = computed(() => {
  if (!data.value) return 0;
  const ids = new Set(data.value.map(u => u.jerarquia_id).filter(Boolean));
  return ids.size;
});

const usuariosFiltrados = computed(() => {
  if (!data.value) return [];
  return data.value.filter(u => {
    if (filtroEstado.value !== 'todos') {
      if (String(u.estado) !== filtroEstado.value) return false;
    }
    if (busquedaGlobal.value) {
      const q = busquedaGlobal.value.toLowerCase().trim();
      const n = `${u.trabajador?.nombres || ''} ${u.trabajador?.apellidos || ''}`.toLowerCase();
      const dni = (u.trabajador?.numero_documento || '').toLowerCase();
      const email = (u.email || '').toLowerCase();
      const jerarquia = (u.jerarquia?.nombre || '').toLowerCase();
      if (!n.includes(q) && !dni.includes(q) && !email.includes(q) && !jerarquia.includes(q)) return false;
    }
    return true;
  });
});

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
  const nombre = usuario.trabajador?.nombres || 'este usuario';
  if (confirm(`¿Estás seguro de que deseas eliminar a "${nombre}"?`)) {
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

<style scoped>
.gap-2 { gap: 8px; }
.line-height-tight { line-height: 1.2; }
.shadow-btn { box-shadow: 0 3px 10px 0 rgba(24, 103, 192, 0.35) !important; }
</style>
