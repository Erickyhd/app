<template>
  <v-dialog v-model="isOpen" max-width="600px" persistent>
    <v-card>
      <v-card-title class="px-4 py-3 bg-primary text-white d-flex justify-space-between align-center">
        <span class="text-h6">{{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}</span>
        <v-btn icon="mdi-close" variant="text" @click="cerrar" color="white"></v-btn>
      </v-card-title>

      <v-card-text class="pt-4">
        <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="formData.nombre"
                label="Nombre/Razón Social *"
                :rules="[v => !!v || 'Requerido']"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.email"
                label="Email"
                type="email"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.telefono"
                label="Teléfono"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>

            <v-col cols="12">
              <v-textarea
                v-model="formData.direccion"
                label="Dirección"
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
  clienteAEditar: Object,
  guardando: Boolean
});

const emit = defineEmits(['update:modelValue', 'guardar']);

const form = ref(null);
const esValido = ref(false);

const getFormState = (data = null) => ({
  id: data?.id || null,
  nombre: data?.nombre || '',
  email: data?.email || '',
  telefono: data?.telefono || '',
  direccion: data?.direccion || ''
});

const formData = ref(getFormState());

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const isEditing = computed(() => !!formData.value.id);

watch(() => props.clienteAEditar, (newVal) => {
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
