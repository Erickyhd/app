<template>
  <v-dialog v-model="isOpen" max-width="800px" persistent>
    <v-card>
      <v-card-title class="px-4 py-3 bg-primary text-white d-flex justify-space-between align-center">
        <span class="text-h6">{{ isEditing ? 'Editar Trabajador' : 'Nuevo Trabajador' }}</span>
        <v-btn icon="mdi-close" variant="text" @click="cerrar" color="white"></v-btn>
      </v-card-title>

      <v-card-text class="pt-4">
        <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
          <v-row>
            <v-col cols="12" sm="4">
              <v-select
                v-model="formData.tipo_documento"
                :items="['DNI', 'CE', 'Pasaporte']"
                label="Tipo Doc. *"
                :rules="[v => !!v || 'Requerido']"
                variant="outlined"
                density="comfortable"
              ></v-select>
            </v-col>
            <v-col cols="12" sm="8">
              <v-text-field
                v-model="formData.numero_documento"
                label="Nro. Documento *"
                :rules="[v => !!v || 'Requerido']"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.nombres"
                label="Nombres *"
                :rules="[v => !!v || 'Requerido']"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.apellidos"
                label="Apellidos *"
                :rules="[v => !!v || 'Requerido']"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.fecha_nacimiento"
                label="Fecha de Nacimiento"
                type="date"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="formData.genero"
                :items="['M', 'F', 'Otro']"
                label="Género"
                variant="outlined"
                density="comfortable"
              ></v-select>
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.telefono_principal"
                label="Teléfono Principal *"
                :rules="[v => !!v || 'Requerido']"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="formData.fecha_contratacion"
                label="Fecha Contratación *"
                type="date"
                :rules="[v => !!v || 'Requerido']"
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
                rows="2"
              ></v-textarea>
            </v-col>

            <v-col cols="12">
              <v-select
                v-model="formData.user_id"
                :items="usuarios"
                item-title="nombres"
                item-value="id"
                label="Vincular con Usuario del Sistema (Opcional)"
                variant="outlined"
                density="comfortable"
                clearable
              ></v-select>
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
import { useUsuarios } from '../../hooks/useUsuarios';

const props = defineProps({
  modelValue: Boolean, 
  trabajadorAEditar: Object,
  guardando: Boolean
});

const emit = defineEmits(['update:modelValue', 'guardar']);

// Obtener lista de usuarios para vincular
const { data: usuarios } = useUsuarios();

const form = ref(null);
const esValido = ref(false);

const getFormState = (data = null) => ({
  id: data?.id || null,
  user_id: data?.user_id || null,
  tipo_documento: data?.tipo_documento || 'DNI',
  numero_documento: data?.numero_documento || '',
  nombres: data?.nombres || '',
  apellidos: data?.apellidos || '',
  fecha_nacimiento: data?.fecha_nacimiento || '',
  genero: data?.genero || '',
  telefono_principal: data?.telefono_principal || '',
  direccion: data?.direccion || '',
  fecha_contratacion: data?.fecha_contratacion || new Date().toISOString().substring(0,10),
});

const formData = ref(getFormState());

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const isEditing = computed(() => !!formData.value.id);

watch(() => props.trabajadorAEditar, (newVal) => {
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
