<template>
  <v-dialog v-model="isOpen" max-width="500px" persistent>
    <v-card>
      <v-card-title class="px-4 py-3 bg-primary text-white d-flex justify-space-between align-center">
        <span class="text-h6">{{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}</span>
        <v-btn icon="mdi-close" variant="text" @click="cerrar" color="white"></v-btn>
      </v-card-title>

      <v-card-text class="pt-4">
        <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="formData.nombres"
                label="Nombres *"
                :rules="[v => !!v || 'El nombre es requerido']"
                variant="outlined"
                density="comfortable"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                v-model="formData.email"
                label="Correo Electrónico *"
                type="email"
                :rules="[
                  v => !!v || 'El correo es requerido',
                  v => /.+@.+\..+/.test(v) || 'El correo debe ser válido'
                ]"
                variant="outlined"
                density="comfortable"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                v-model="formData.password"
                :label="isEditing ? 'Nueva Clave (Opcional)' : 'Clave *'"
                :rules="[
                  v => isEditing ? true : !!v || 'La clave es requerida',
                  v => !v || v.length >= 6 || 'Debe tener al menos 6 caracteres'
                ]"
                type="password"
                variant="outlined"
                density="comfortable"
                :required="!isEditing"
              ></v-text-field>
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
  usuarioAEditar: Object,
  guardando: Boolean
});

const emit = defineEmits(['update:modelValue', 'guardar']);

const form = ref(null);
const esValido = ref(false);

const getFormState = (data = null) => ({
  id: data?.id || null,
  nombres: data?.nombres || '',
  email: data?.email || '',
  password: ''
});

const formData = ref(getFormState());

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const isEditing = computed(() => !!formData.value.id);

watch(() => props.usuarioAEditar, (newVal) => {
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
