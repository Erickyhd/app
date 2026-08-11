<template>
  <v-card class="elevation-2" rounded="lg">
    <v-card-title class="d-flex align-center bg-primary text-white pa-4">
      <v-icon left size="28" class="mr-2">mdi-account-hard-hat</v-icon>
      <span class="text-h6 font-weight-bold">Directorio de Trabajadores</span>
      <v-spacer></v-spacer>
      <v-btn color="white" variant="tonal" prepend-icon="mdi-plus" @click="$emit('nuevo')">
        Nuevo Trabajador
      </v-btn>
    </v-card-title>

    <v-data-table
      :headers="cabeceras"
      :items="trabajadores"
      :loading="cargando"
      hover
      class="elevation-0"
    >
      <template v-slot:loading>
        <v-skeleton-loader type="table-row@5"></v-skeleton-loader>
      </template>

      <template v-slot:item.estado="{ item }">
        <v-chip
          :color="item.estado === 1 ? 'success' : 'error'"
          size="small"
          variant="flat"
        >
          {{ item.estado === 1 ? 'Activo' : 'Inactivo' }}
        </v-chip>
      </template>

      <template v-slot:item.acciones="{ item }">
        <v-btn icon="mdi-pencil" size="small" color="primary" variant="text" class="mr-2" @click="$emit('editar', item)"></v-btn>
        <v-btn icon="mdi-delete" size="small" color="error" variant="text" @click="$emit('eliminar', item)"></v-btn>
      </template>
    </v-data-table>
  </v-card>
</template>

<script setup>
defineProps({
    trabajadores: {
        type: Array,
        required: true,
        default: () => []
    },
    cargando: {
        type: Boolean,
        default: false
    }
});

defineEmits(['nuevo', 'editar', 'eliminar']);

const cabeceras = [
    { title: 'Doc.', key: 'numero_documento' },
    { title: 'Nombre Completo', key: 'nombre_completo' },
    { title: 'Teléfono', key: 'telefono_principal' },
    { title: 'Fecha Contratación', key: 'fecha_contratacion_formatted' },
    { title: 'Usuario', key: 'usuario_vinculado' },
    { title: 'Estado', key: 'estado', align: 'center' },
    { title: 'Acciones', key: 'acciones', align: 'end', sortable: false },
];
</script>
