<template>
  <v-card class="elevation-2 rounded-lg border overflow-hidden bg-white">
    <v-data-table
      :headers="cabeceras"
      :items="rangos"
      :loading="cargando"
      density="compact"
      hover
      class="elevation-0 tabla-fabhub"
    >
      <template #item.nivel="{ item }">
        <v-chip size="x-small" color="purple" variant="flat" class="font-weight-black">
          Nivel {{ item.nivel }}
        </v-chip>
      </template>

      <template #item.nombre="{ item }">
        <span class="font-weight-bold text-caption text-grey-darken-3">{{ item.nombre }}</span>
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
            <v-tooltip activator="parent" location="top">Editar rango</v-tooltip>
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
            <v-tooltip activator="parent" location="top">Eliminar rango</v-tooltip>
          </v-btn>
        </div>
      </template>

      <template #no-data>
        <div class="text-center pa-6 text-grey text-caption">No hay rangos registrados aún.</div>
      </template>
    </v-data-table>
  </v-card>
</template>

<script setup>
defineProps({
  rangos: {
    type: Array,
    required: true,
    default: () => []
  },
  cargando: {
    type: Boolean,
    default: false
  }
});

defineEmits(['editar', 'eliminar']);

const cabeceras = [
  { title: 'NIVEL', key: 'nivel', width: '100px' },
  { title: 'CARGO / RANGO', key: 'nombre' },
  { title: 'DESCRIPCIÓN', key: 'descripcion' },
  { title: 'ESTADO', key: 'estado', align: 'center', width: '110px' },
  { title: 'ACCIONES', key: 'acciones', align: 'end', sortable: false, width: '90px' },
];
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
</style>
