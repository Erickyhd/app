<template>
  <v-card class="elevation-2 rounded-lg border overflow-hidden bg-white">
    <v-data-table
      :headers="headers"
      :items="jerarquias"
      :loading="cargando"
      density="compact"
      hover
      class="elevation-0 tabla-fabhub"
    >
      <template #item.codigo="{ item }">
        <v-chip size="x-small" color="primary" variant="tonal" class="font-weight-bold">
          {{ item.codigo || '00' + item.id }}
        </v-chip>
      </template>

      <template #item.nombre="{ item }">
        <span class="font-weight-bold text-caption text-grey-darken-3">{{ item.nombre }}</span>
      </template>

      <template #item.padre_nombre="{ item }">
        <v-chip v-if="item.padre_nombre" size="x-small" color="indigo" variant="tonal" class="font-weight-medium">
          <v-icon start size="10">mdi-source-branch</v-icon>
          {{ item.padre_nombre }}
        </v-chip>
        <span v-else class="text-caption text-grey italic" style="font-size: 11px;">Raíz (Principal)</span>
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
            <v-tooltip activator="parent" location="top">Editar jerarquía</v-tooltip>
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
            <v-tooltip activator="parent" location="top">Eliminar jerarquía</v-tooltip>
          </v-btn>
        </div>
      </template>

      <template #no-data>
        <div class="text-center pa-6 text-grey text-caption">No hay jerarquías registradas aún.</div>
      </template>
    </v-data-table>
  </v-card>
</template>

<script setup>
defineProps({
  jerarquias: {
    type: Array,
    required: true
  },
  cargando: {
    type: Boolean,
    default: false
  }
});

defineEmits(['editar', 'eliminar']);

const headers = [
  { title: 'CÓDIGO', key: 'codigo', sortable: true, width: '100px' },
  { title: 'NOMBRE DE ÁREA', key: 'nombre', sortable: true },
  { title: 'DESCRIPCIÓN', key: 'descripcion', sortable: false },
  { title: 'DEPENDENCIA PADRE', key: 'padre_nombre', sortable: true },
  { title: 'ACCIONES', key: 'acciones', sortable: false, align: 'end', width: '90px' },
];
</script>

<style scoped>
.gap-1 { gap: 4px; }
</style>
