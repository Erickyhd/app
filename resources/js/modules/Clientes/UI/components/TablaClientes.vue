<template>
  <v-card class="elevation-2" rounded="lg">
    <v-card-title class="d-flex align-center bg-primary text-white pa-4">
      <v-icon left size="28" class="mr-2">mdi-account-tie</v-icon>
      <span class="text-h6 font-weight-bold">Directorio de Clientes</span>
      <v-spacer></v-spacer>
      <v-btn color="white" variant="tonal" prepend-icon="mdi-plus" @click="$emit('nuevo')">
        Nuevo Cliente
      </v-btn>
    </v-card-title>

    <v-data-table
      :headers="cabeceras"
      :items="clientes"
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
    clientes: {
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
    { title: 'Nombre', key: 'nombre' },
    { title: 'Email', key: 'email' },
    { title: 'Teléfono', key: 'telefono' },
    { title: 'Dirección', key: 'direccion' },
    { title: 'Estado', key: 'estado', align: 'center' },
    { title: 'Acciones', key: 'acciones', align: 'end', sortable: false },
];
</script>
