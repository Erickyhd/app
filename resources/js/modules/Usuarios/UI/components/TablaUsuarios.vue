<template>
  <div>
    <!-- MODO VISTA 1: TABLA ERP LIMPIA -->
    <v-card v-if="modoVista === 'tabla'" class="elevation-2 rounded-lg border overflow-hidden bg-white">
      <v-data-table
        :headers="cabeceras"
        :items="usuarios"
        :loading="cargando"
        density="compact"
        hover
        class="elevation-0 tabla-fabhub"
      >
        <!-- Columna: Usuario -->
        <template #item.usuario="{ item }">
          <div class="d-flex align-center py-1">
            <v-avatar color="primary" size="32" class="mr-2 text-white text-caption font-weight-bold elevation-1">
              {{ obtenerIniciales(item) }}
            </v-avatar>
            <div>
              <div class="font-weight-bold text-caption text-grey-darken-3">
                {{ item.trabajador ? `${item.trabajador.nombres} ${item.trabajador.apellidos || ''}` : 'Sin nombre' }}
              </div>
              <div class="text-caption text-grey font-weight-medium" style="font-size: 11px;">
                DNI: {{ item.trabajador?.numero_documento || 'N/A' }}
              </div>
            </div>
          </div>
        </template>

        <!-- Columna: Correo -->
        <template #item.email="{ item }">
          <span class="text-caption text-grey-darken-2">{{ item.email }}</span>
        </template>

        <!-- Columna: Organización -->
        <template #item.organizacion="{ item }">
          <div class="d-flex align-center gap-1">
            <v-chip size="x-small" color="indigo" variant="tonal" class="font-weight-medium">
              {{ item.jerarquia?.nombre || 'Sin Jerarquía' }}
            </v-chip>
            <v-chip v-if="item.rango" size="x-small" color="purple" variant="tonal" class="font-weight-medium">
              {{ item.rango.nombre }}
            </v-chip>
          </div>
        </template>

        <!-- Columna: Estado -->
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

        <!-- Columna: Acciones -->
        <template #item.acciones="{ item }">
          <div class="d-flex justify-end gap-1">
            <v-btn
              v-if="can('usuarios.editar')"
              icon="mdi-pencil-outline"
              size="x-small"
              color="primary"
              variant="tonal"
              class="rounded"
              @click="$emit('editar', item)"
            >
              <v-icon size="14">mdi-pencil-outline</v-icon>
              <v-tooltip activator="parent" location="top">Editar usuario</v-tooltip>
            </v-btn>

            <v-btn
              v-if="can('usuarios.eliminar')"
              icon="mdi-delete-outline"
              size="x-small"
              color="error"
              variant="tonal"
              class="rounded"
              @click="$emit('eliminar', item)"
            >
              <v-icon size="14">mdi-delete-outline</v-icon>
              <v-tooltip activator="parent" location="top">Eliminar usuario</v-tooltip>
            </v-btn>
          </div>
        </template>

        <template #no-data>
          <div class="text-center pa-6 text-grey text-caption">
            No se encontraron usuarios coincidentes.
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- MODO VISTA 2: GRID EN TARJETAS -->
    <div v-else>
      <v-row v-if="usuarios && usuarios.length > 0" density="compact">
        <v-col
          v-for="usuario in usuarios"
          :key="usuario.id"
          cols="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-card class="user-card elevation-1 rounded-lg border h-100 d-flex flex-column">
            <div class="pa-2 bg-grey-lighten-5 border-b d-flex justify-space-between align-center">
              <span
                class="status-pulse-dot"
                :class="usuario.estado === 1 ? 'bg-success' : 'bg-error'"
              ></span>
              <v-chip
                :color="usuario.estado === 1 ? 'success' : 'error'"
                size="x-small"
                variant="tonal"
                class="font-weight-bold"
              >
                {{ usuario.estado === 1 ? 'Activo' : 'Inactivo' }}
              </v-chip>
            </div>

            <v-card-text class="text-center pa-3 flex-grow-1">
              <v-avatar color="primary" size="44" class="mb-2 text-white font-weight-bold elevation-1">
                {{ obtenerIniciales(usuario) }}
              </v-avatar>

              <div class="text-subtitle-2 font-weight-bold text-grey-darken-3 text-truncate">
                {{ usuario.trabajador ? `${usuario.trabajador.nombres} ${usuario.trabajador.apellidos || ''}` : 'Sin nombre' }}
              </div>

              <div class="text-caption text-grey text-truncate mb-2" style="font-size: 11px;">
                {{ usuario.email }}
              </div>

              <div class="d-flex flex-wrap justify-center gap-1">
                <v-chip size="x-small" color="indigo" variant="flat">
                  {{ usuario.jerarquia?.nombre || 'Sin Jerarquía' }}
                </v-chip>
              </div>
            </v-card-text>

            <v-card-actions class="px-2 py-1 bg-white border-t justify-space-between">
              <span class="text-caption text-grey" style="font-size: 11px;">ID: #{{ usuario.id }}</span>
              <div class="d-flex gap-1">
                <v-btn
                  v-if="can('usuarios.editar')"
                  icon="mdi-pencil-outline"
                  size="x-small"
                  color="primary"
                  variant="text"
                  @click="$emit('editar', usuario)"
                ></v-btn>
                <v-btn
                  v-if="can('usuarios.eliminar')"
                  icon="mdi-delete-outline"
                  size="x-small"
                  color="error"
                  variant="text"
                  @click="$emit('eliminar', usuario)"
                ></v-btn>
              </div>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <v-card v-else flat class="text-center pa-8 bg-white rounded-lg border">
        <div class="text-caption text-grey">No se encontraron usuarios</div>
      </v-card>
    </div>
  </div>
</template>

<script setup>
import { usePermissions } from '../../../Auth/composables/usePermissions';

const { can } = usePermissions();

defineProps({
  usuarios: {
    type: Array,
    required: true,
    default: () => []
  },
  cargando: {
    type: Boolean,
    default: false
  },
  modoVista: {
    type: String,
    default: 'tabla'
  }
});

defineEmits(['editar', 'eliminar']);

const cabeceras = [
  { title: 'USUARIO', key: 'usuario', align: 'start' },
  { title: 'CORREO ELECTRÓNICO', key: 'email', align: 'start' },
  { title: 'ORGANIZACIÓN', key: 'organizacion', align: 'start' },
  { title: 'ESTADO', key: 'estado', align: 'center', width: '120px' },
  { title: 'ACCIONES', key: 'acciones', align: 'end', sortable: false, width: '90px' }
];

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

.gap-1 { gap: 4px; }
.user-card { transition: transform 0.2s ease; }
.user-card:hover { transform: translateY(-2px); }
</style>
