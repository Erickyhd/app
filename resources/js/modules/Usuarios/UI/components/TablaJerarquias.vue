<template>
  <v-card>
    <v-card-title class="d-flex align-center justify-space-between px-4 py-3">
      <span class="text-h6">Listado de Jerarquías</span>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="$emit('nuevo')">
        Nueva Jerarquía
      </v-btn>
    </v-card-title>
    
    <v-data-table
      :headers="headers"
      :items="jerarquias"
      :loading="cargando"
      class="elevation-0"
      hover
    >
      <!-- Botones de Acción -->
      <template v-slot:item.acciones="{ item }">
        <div class="d-flex gap-2 justify-end">
          <v-btn
            icon="mdi-pencil"
            variant="text"
            size="small"
            color="info"
            @click="$emit('editar', item)"
          ></v-btn>
          <v-btn
            icon="mdi-delete"
            variant="text"
            size="small"
            color="error"
            @click="$emit('eliminar', item)"
          ></v-btn>
        </div>
      </template>

      <!-- Estado Vacío -->
      <template v-slot:no-data>
        <div class="pa-4 text-center text-body-1 text-grey">
          No hay jerarquías registradas aún.
        </div>
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

defineEmits(['nuevo', 'editar', 'eliminar']);

const headers = [
  { title: 'Código', key: 'codigo', sortable: true },
  { title: 'Nombre', key: 'nombre', sortable: true },
  { title: 'Descripción', key: 'descripcion', sortable: false },
  { title: 'Padre', key: 'padre_nombre', sortable: true },
  { title: 'Creado el', key: 'created_at_formatted', sortable: true },
  { title: 'Acciones', key: 'acciones', sortable: false, align: 'end' },
];
</script>
