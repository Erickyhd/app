<template>
  <v-dialog v-model="isOpen" max-width="500px" persistent>
    <v-card>
      <v-card-title class="px-4 py-3 bg-primary text-white d-flex justify-space-between align-center">
        <span class="text-h6">{{ isEditing ? 'Editar Jerarquía' : 'Nueva Jerarquía' }}</span>
        <v-btn icon="mdi-close" variant="text" @click="cerrar" color="white"></v-btn>
      </v-card-title>

      <v-card-text class="pt-4">
        <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.codigo"
                label="Código"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.nombre"
                label="Nombre *"
                :rules="[v => !!v || 'El nombre es requerido']"
                variant="outlined"
                density="comfortable"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-select
                v-model="formData.jerarquia_padre_id"
                :items="jerarquiasPadreDisponibles"
                item-title="nombre"
                item-value="id"
                label="Jerarquía Padre (Opcional)"
                variant="outlined"
                density="comfortable"
                clearable
              ></v-select>
            </v-col>
            <v-col cols="12">
              <v-textarea
                v-model="formData.descripcion"
                label="Descripción"
                variant="outlined"
                density="comfortable"
                rows="3"
              ></v-textarea>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>

      <v-card-actions class="px-4 pb-4">
        <v-spacer></v-spacer>
        <v-btn color="grey-darken-1" variant="text" @click="cerrar">Cancelar</v-btn>
        <v-btn 
          color="primary" 
          variant="elevated" 
          :loading="guardando"
          @click="guardar"
        >
          Guardar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: Boolean, 
  jerarquiaAEditar: Object,
  todasLasJerarquias: Array,
  guardando: Boolean
});

const emit = defineEmits(['update:modelValue', 'guardar']);

const form = ref(null);
const esValido = ref(false);

const getFormState = (data = null) => ({
  id: data?.id || null,
  nombre: data?.nombre || '',
  descripcion: data?.descripcion || '',
  jerarquia_padre_id: data?.jerarquia_padre_id || null,
  codigo: data?.codigo || ''
});

const formData = ref(getFormState());

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const isEditing = computed(() => !!formData.value.id);

// Exclude itself from being its own parent to prevent circular loops
const jerarquiasPadreDisponibles = computed(() => {
  if (!props.todasLasJerarquias) return [];
  if (!isEditing.value) return props.todasLasJerarquias;
  return props.todasLasJerarquias.filter(j => j.id !== formData.value.id);
});

// Update form state when edit prop changes
watch(() => props.jerarquiaAEditar, (newVal) => {
  formData.value = getFormState(newVal);
}, { immediate: true });

watch(isOpen, (newVal) => {
  if (!newVal) {
    if (form.value) form.value.resetValidation();
  }
});

const cerrar = () => {
  isOpen.value = false;
};

const guardar = async () => {
  const { valid } = await form.value.validate();
  if (!valid) return;
  
  emit('guardar', { ...formData.value });
};
</script>
