<template>
  <div>
    <!-- MODO VISTA 1: TABLA ERP LIMPIA -->
    <v-card v-if="modoVista === 'tabla'" class="elevation-2 rounded-lg border overflow-hidden bg-white">
      <v-data-table
        :headers="cabeceras"
        :items="clientes"
        :loading="cargando"
        density="compact"
        hover
        class="elevation-0 tabla-fabhub"
      >
        <template #item.nombre="{ item }">
          <div class="d-flex align-center py-1">
            <v-avatar color="primary" size="32" class="mr-2 text-white text-caption font-weight-bold elevation-1">
              {{ obtenerIniciales(item) }}
            </v-avatar>
            <span class="font-weight-bold text-caption text-grey-darken-3">{{ item.nombre }}</span>
          </div>
        </template>

        <template #item.email="{ item }">
          <span class="text-caption text-grey-darken-2">{{ item.email || 'N/A' }}</span>
        </template>

        <template #item.telefono="{ item }">
          <span class="text-caption text-grey-darken-2">{{ item.telefono || 'N/A' }}</span>
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
          <div class="d-flex justify-end gap-1">
            <v-btn
              icon="mdi-pencil-outline"
              size="x-small"
              color="primary"
              variant="tonal"
              class="rounded"
              @click="$emit('editar', item)"
            >
              <v-icon size="14">mdi-pencil-outline</v-icon>
              <v-tooltip activator="parent" location="top">Editar cliente</v-tooltip>
            </v-btn>

            <v-btn
              icon="mdi-delete-outline"
              size="x-small"
              color="error"
              variant="tonal"
              class="rounded"
              @click="$emit('eliminar', item)"
            >
              <v-icon size="14">mdi-delete-outline</v-icon>
              <v-tooltip activator="parent" location="top">Eliminar cliente</v-tooltip>
            </v-btn>
          </div>
        </template>

        <template #no-data>
          <div class="text-center pa-6 text-grey text-caption">
            No se encontraron clientes coincidentes.
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- MODO VISTA 2: GRID EN TARJETAS -->
    <div v-else>
      <v-row v-if="clientes && clientes.length > 0" density="compact">
        <v-col
          v-for="cliente in clientes"
          :key="cliente.id"
          cols="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-card class="user-card elevation-1 rounded-lg border h-100 d-flex flex-column">
            <div class="pa-2 bg-grey-lighten-5 border-b d-flex justify-space-between align-center">
              <span
                class="status-pulse-dot"
                :class="cliente.estado === 1 ? 'bg-success' : 'bg-error'"
              ></span>
              <v-chip
                :color="cliente.estado === 1 ? 'success' : 'error'"
                size="x-small"
                variant="tonal"
                class="font-weight-bold"
              >
                {{ cliente.estado === 1 ? 'Activo' : 'Inactivo' }}
              </v-chip>
            </div>

            <v-card-text class="text-center pa-3 flex-grow-1">
              <v-avatar color="primary" size="44" class="mb-2 text-white font-weight-bold elevation-1">
                {{ obtenerIniciales(cliente) }}
              </v-avatar>

              <div class="text-subtitle-2 font-weight-bold text-grey-darken-3 text-truncate">
                {{ cliente.nombre }}
              </div>

              <div class="text-caption text-grey text-truncate mb-1" style="font-size: 11px;">
                {{ cliente.email || 'Sin correo' }}
              </div>
            </v-card-text>

            <v-card-actions class="px-2 py-1 bg-white border-t justify-space-between">
              <span class="text-caption text-grey" style="font-size: 11px;">ID: #{{ cliente.id }}</span>
              <div class="d-flex gap-1">
                <v-btn icon="mdi-pencil-outline" size="x-small" color="primary" variant="text" @click="$emit('editar', cliente)"></v-btn>
                <v-btn icon="mdi-delete-outline" size="x-small" color="error" variant="text" @click="$emit('eliminar', cliente)"></v-btn>
              </div>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <v-card v-else flat class="text-center pa-8 bg-white rounded-lg border">
        <div class="text-caption text-grey">No se encontraron clientes</div>
      </v-card>
    </div>
  </div>
</template>

<script setup>
defineProps({
  clientes: {
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
  { title: 'CLIENTE / RAZÓN SOCIAL', key: 'nombre', align: 'start' },
  { title: 'CORREO ELECTRÓNICO', key: 'email', align: 'start' },
  { title: 'TELÉFONO', key: 'telefono', align: 'start' },
  { title: 'DIRECCIÓN', key: 'direccion', align: 'start' },
  { title: 'ESTADO', key: 'estado', align: 'center', width: '110px' },
  { title: 'ACCIONES', key: 'acciones', align: 'end', sortable: false, width: '90px' }
];

const obtenerIniciales = (item) => {
  const n = item.nombre || '';
  if (!n) return 'CL';
  const parts = n.split(' ');
  if (parts.length >= 2) {
    return `${parts[0].charAt(0)}${parts[1].charAt(0)}`.toUpperCase();
  }
  return n.slice(0, 2).toUpperCase();
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
