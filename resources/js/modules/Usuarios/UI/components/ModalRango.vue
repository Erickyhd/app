<template>
  <v-dialog v-model="isOpen" max-width="500px" persistent>
    <v-card>
      <v-card-title class="px-4 py-3 bg-primary text-white d-flex justify-space-between align-center">
        <span class="text-h6">{{ isEditing ? 'Editar Rango' : 'Nuevo Rango' }}</span>
        <v-btn icon="mdi-close" variant="text" @click="cerrar" color="white"></v-btn>
      </v-card-title>

      <v-card-text class="pt-4">
        <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
          <v-row>
            <v-col cols="12" sm="8">
              <v-text-field
                v-model="formData.nombre"
                label="Nombre *"
                :rules="[v => !!v || 'El nombre es requerido']"
                variant="outlined"
                density="comfortable"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field
                v-model.number="formData.nivel"
                label="Nivel *"
                type="number"
                :rules="[
                  v => !!v || 'Requerido',
                  v => v > 0 || 'Debe ser > 0'
                ]"
                variant="outlined"
                density="comfortable"
                required
              ></v-text-field>
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
  rangoAEditar: Object,
  guardando: Boolean
});

const emit = defineEmits(['update:modelValue', 'guardar']);

const form = ref(null);
const esValido = ref(false);

const getFormState = (data = null) => ({
  id: data?.id || null,
  nombre: data?.nombre || '',
  nivel: data?.nivel || 1,
  descripcion: data?.descripcion || ''
});

const formData = ref(getFormState());

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const isEditing = computed(() => !!formData.value.id);

watch(() => props.rangoAEditar, (newVal) => {
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
