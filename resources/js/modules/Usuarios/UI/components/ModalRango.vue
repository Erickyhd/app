<template>
  <v-layout style="min-height: 0;">
    <v-navigation-drawer
      v-model="isOpen"
      location="right"
      temporary
      width="540"
      class="elevation-24 drawer-futurista"
    >
      <div class="d-flex flex-column h-100 bg-surface">
        <!-- Header -->
        <div class="header-banner pa-4 d-flex align-center justify-space-between text-white">
          <div class="d-flex align-center gap-3">
            <v-avatar color="white" size="44" class="elevation-3">
              <v-icon color="primary" size="24">
                {{ isEditing ? 'mdi-shield-edit-outline' : 'mdi-shield-plus-outline' }}
              </v-icon>
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-bold line-height-tight">
                {{ isEditing ? 'Editar Rango' : 'Nuevo Rango' }}
              </div>
              <div class="text-caption text-white-opacity-80">
                Ponderación jerárquica y nivel de autoridad
              </div>
            </div>
          </div>

          <v-btn icon="mdi-close" variant="text" color="white" density="comfortable" @click="cerrar">
            <v-icon>mdi-close</v-icon>
            <v-tooltip activator="parent" location="bottom">Cerrar panel</v-tooltip>
          </v-btn>
        </div>

        <!-- Formulario Continuo -->
        <div class="flex-grow-1 overflow-y-auto pa-5">
          <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
            <v-card variant="outlined" class="mb-4 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="primary" size="20">mdi-shield-crown</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  Datos del Rango
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-row density="compact">
                  <v-col cols="12" sm="8">
                    <v-text-field
                      v-model="formData.nombre"
                      label="Nombre del Rango / Cargo *"
                      prepend-inner-icon="mdi-format-title"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      class="mb-2"
                      :rules="[v => !!v || 'El nombre es requerido']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="4">
                    <v-text-field
                      v-model.number="formData.nivel"
                      label="Nivel Jerárquico *"
                      type="number"
                      prepend-inner-icon="mdi-numeric"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      class="mb-2"
                      :rules="[
                        v => !!v || 'Requerido',
                        v => v > 0 || 'Debe ser > 0'
                      ]"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-textarea
                      v-model="formData.descripcion"
                      label="Descripción de Autoridad o Atribuciones"
                      prepend-inner-icon="mdi-text-box-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      rows="3"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-form>
        </div>

        <!-- Sticky Footer -->
        <div class="pa-4 bg-white border-t d-flex align-center justify-space-between">
          <v-btn variant="text" color="grey-darken-1" @click="cerrar" prepend-icon="mdi-arrow-left">
            Cancelar
          </v-btn>
          <v-btn
            color="primary"
            variant="elevated"
            class="px-6 font-weight-bold text-capitalize shadow-btn"
            :loading="guardando"
            prepend-icon="mdi-check-circle-outline"
            @click="guardar"
          >
            {{ isEditing ? 'Guardar Cambios' : 'Crear Rango' }}
          </v-btn>
        </div>
      </div>
    </v-navigation-drawer>
  </v-layout>
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
  if (!newVal && form.value) {
    form.value.resetValidation();
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

<style scoped>
.header-banner {
  background: linear-gradient(135deg, #1867c0 0%, #5cbbf6 100%);
}

.shadow-btn {
  box-shadow: 0 4px 14px 0 rgba(24, 103, 192, 0.39) !important;
}

.line-height-tight {
  line-height: 1.2;
}

.gap-3 {
  gap: 12px;
}
</style>
