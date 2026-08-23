<template>
  <v-layout style="min-height: 0;">
    <v-navigation-drawer
      v-model="isOpen"
      location="right"
      temporary
      width="580"
      class="elevation-24 drawer-futurista"
    >
      <div class="d-flex flex-column h-100 bg-surface">
        <!-- Cabecera Futurista -->
        <div class="header-banner pa-4 d-flex align-center justify-space-between text-white">
          <div class="d-flex align-center gap-3">
            <v-avatar color="white" size="44" class="elevation-3">
              <v-icon color="primary" size="24">
                {{ isEditing ? 'mdi-account-edit-outline' : 'mdi-account-plus-outline' }}
              </v-icon>
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-bold line-height-tight">
                {{ isEditing ? 'Editar Trabajador' : 'Nuevo Trabajador' }}
              </div>
              <div class="text-caption text-white-opacity-80">
                Ficha de personal y datos de contacto institucionales
              </div>
            </div>
          </div>

          <v-btn icon="mdi-close" variant="text" color="white" density="comfortable" @click="cerrar">
            <v-icon>mdi-close</v-icon>
            <v-tooltip activator="parent" location="bottom">Cerrar panel</v-tooltip>
          </v-btn>
        </div>

        <!-- Cuerpo del Formulario Continuo -->
        <div class="flex-grow-1 overflow-y-auto pa-5">
          <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
            <!-- SECCIÓN 1: DOCUMENTO E IDENTIDAD -->
            <v-card variant="outlined" class="mb-4 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="primary" size="20">mdi-card-account-details-outline</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  1. Identidad y Documento
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-row density="compact">
                  <v-col cols="12" sm="4">
                    <v-select
                      v-model="formData.tipo_documento"
                      :items="['DNI', 'CE', 'Pasaporte']"
                      label="Tipo Doc. *"
                      prepend-inner-icon="mdi-card-text-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :rules="[v => !!v || 'Requerido']"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" sm="8">
                    <v-text-field
                      v-model="formData.numero_documento"
                      label="Nro. Documento *"
                      prepend-inner-icon="mdi-numeric"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :rules="[v => !!v || 'Requerido']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="formData.nombres"
                      label="Nombres *"
                      prepend-inner-icon="mdi-account-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :rules="[v => !!v || 'Requerido']"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="formData.apellidos"
                      label="Apellidos *"
                      prepend-inner-icon="mdi-account-multiple-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :rules="[v => !!v || 'Requerido']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="formData.fecha_nacimiento"
                      label="Fecha de Nacimiento"
                      type="date"
                      prepend-inner-icon="mdi-calendar-account"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="formData.genero"
                      :items="['Masculino', 'Femenino', 'Otro']"
                      label="Género"
                      prepend-inner-icon="mdi-gender-male-female"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                    ></v-select>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>

            <!-- SECCIÓN 2: CONTACTO Y UBICACIÓN -->
            <v-card variant="outlined" class="mb-4 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="indigo" size="20">mdi-phone-outline</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  2. Contacto y Dirección
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-row density="compact">
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="formData.telefono_principal"
                      label="Teléfono Principal *"
                      prepend-inner-icon="mdi-phone"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :rules="[v => !!v || 'Requerido']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="formData.fecha_contratacion"
                      label="Fecha Contratación *"
                      type="date"
                      prepend-inner-icon="mdi-calendar-check"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :rules="[v => !!v || 'Requerido']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-textarea
                      v-model="formData.direccion"
                      label="Dirección Domiciliaria"
                      prepend-inner-icon="mdi-map-marker-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      rows="2"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>

            <!-- SECCIÓN 3: VÍNCULO CON USUARIOS DEL SISTEMA -->
            <v-card variant="outlined" class="mb-2 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="purple" size="20">mdi-account-network-outline</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  3. Vínculo con Cuenta de Usuario (Opcional)
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-autocomplete
                  v-model="formData.usuario_id"
                  :items="usuarios || []"
                  item-title="email"
                  item-value="id"
                  label="Vincular con Usuario del Sistema"
                  prepend-inner-icon="mdi-account-key-outline"
                  variant="solo-filled"
                  flat
                  density="comfortable"
                  clearable
                >
                  <template #item="{ props, item }">
                    <v-list-item v-bind="props" prepend-icon="mdi-account">
                      <template #subtitle>
                        <span class="text-caption text-grey">ID: #{{ item?.raw?.id || 'N/A' }}</span>
                      </template>
                    </v-list-item>
                  </template>
                </v-autocomplete>
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
            {{ isEditing ? 'Guardar Cambios' : 'Crear Trabajador' }}
          </v-btn>
        </div>
      </div>
    </v-navigation-drawer>
  </v-layout>
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

const { data: usuarios } = useUsuarios();

const form = ref(null);
const esValido = ref(false);

const getFormState = (data = null) => ({
  id: data?.id || null,
  usuario_id: data?.usuario_id || data?.user_id || null,
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
