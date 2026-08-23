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
        <!-- Cabecera Futurista con Gradiente -->
        <div class="header-banner pa-5 d-flex align-center justify-space-between text-white">
          <div class="d-flex align-center gap-3">
            <v-avatar color="white" size="46" class="elevation-4">
              <v-icon color="primary" size="26">
                {{ isEditing ? 'mdi-account-edit-outline' : 'mdi-account-plus-outline' }}
              </v-icon>
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-bold line-height-tight">
                {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
              </div>
              <div class="text-caption text-white-opacity-80">
                {{ isEditing ? 'Actualiza la información general y credenciales' : 'Registra un nuevo usuario en el sistema' }}
              </div>
            </div>
          </div>

          <v-btn
            icon="mdi-close"
            variant="text"
            color="white"
            density="comfortable"
            @click="cerrar"
          >
            <v-icon>mdi-close</v-icon>
            <v-tooltip activator="parent" location="bottom">Cerrar panel</v-tooltip>
          </v-btn>
        </div>

        <!-- Cuerpo del Formulario Continuo en Secciones -->
        <div class="flex-grow-1 overflow-y-auto pa-5">
          <v-form ref="form" v-model="esValido" @submit.prevent="guardar">
            <!-- AVATAR / CARD RESUMEN PERFIL -->
            <div class="d-flex flex-column align-center mb-5 pa-4 bg-grey-lighten-4 rounded-xl border-dashed">
              <v-avatar size="76" color="primary" class="elevation-3 mb-2 avatar-glow">
                <span class="text-h4 font-weight-bold text-white">
                  {{ obtenerIniciales }}
                </span>
              </v-avatar>
              <div class="text-subtitle-2 font-weight-bold text-grey-darken-3">
                {{ formData.nombres ? `${formData.nombres} ${formData.apellidos}` : 'Nuevo Perfil' }}
              </div>
              <div class="text-caption text-grey">
                {{ formData.email || 'correo@ejemplo.com' }}
              </div>
            </div>

            <!-- SECCIÓN 1: DNI E IDENTIDAD -->
            <v-card variant="outlined" class="mb-4 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="primary" size="20">mdi-account-details-outline</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  1. Identidad y Datos Personales
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-row density="compact">
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.dni"
                      label="DNI / Nro. Documento *"
                      prepend-inner-icon="mdi-card-account-details-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      class="mb-2"
                      :rules="[v => !!v || 'El documento es requerido']"
                    >
                      <template #append-inner>
                        <span class="cursor-pointer">
                          <v-icon size="16" color="grey">mdi-help-circle-outline</v-icon>
                          <v-tooltip activator="parent" location="top">Número de DNI único del usuario</v-tooltip>
                        </span>
                      </template>
                    </v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.telefono"
                      label="Teléfono Móvil"
                      prepend-inner-icon="mdi-phone-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      class="mb-2"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.nombres"
                      label="Nombres *"
                      prepend-inner-icon="mdi-account-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      class="mb-2"
                      :rules="[v => !!v || 'Los nombres son requeridos']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.apellidos"
                      label="Apellidos *"
                      prepend-inner-icon="mdi-account-multiple-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      class="mb-2"
                      :rules="[v => !!v || 'Los apellidos son requeridos']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-autocomplete
                      v-model="formData.genero"
                      :items="[
                        { title: 'Masculino', value: 'Masculino', icon: 'mdi-gender-male' },
                        { title: 'Femenino', value: 'Femenino', icon: 'mdi-gender-female' }
                      ]"
                      label="Género"
                      prepend-inner-icon="mdi-gender-male-female"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      clearable
                    >
                      <template #item="{ props, item }">
                        <v-list-item v-bind="props" :prepend-icon="item?.raw?.icon || 'mdi-account'"></v-list-item>
                      </template>
                    </v-autocomplete>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>

            <!-- SECCIÓN 2: ORGANIZACIÓN Y JERARQUÍA -->
            <v-card variant="outlined" class="mb-4 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="indigo" size="20">mdi-domain</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  2. Organización y Asignación de Cargo
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-row density="compact">
                  <v-col cols="12">
                    <v-autocomplete
                      v-model="formData.jerarquia_id"
                      :items="jerarquias || []"
                      item-title="nombre"
                      item-value="id"
                      label="Jerarquía Institucional"
                      prepend-inner-icon="mdi-sitemap-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :loading="cargandoJerarquias"
                      clearable
                      class="mb-2"
                    >
                      <template #item="{ props, item }">
                        <v-list-item v-bind="props" prepend-icon="mdi-sitemap">
                          <template #subtitle>
                            <span class="text-caption text-grey">Código: {{ item?.raw?.codigo || 'N/A' }}</span>
                          </template>
                        </v-list-item>
                      </template>
                    </v-autocomplete>
                  </v-col>

                  <v-col cols="12">
                    <v-autocomplete
                      v-model="formData.rango_id"
                      :items="rangos || []"
                      item-title="nombre"
                      item-value="id"
                      label="Rango o Cargo Específico"
                      prepend-inner-icon="mdi-shield-crown-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :loading="cargandoRangos"
                      clearable
                    >
                      <template #item="{ props, item }">
                        <v-list-item v-bind="props" prepend-icon="mdi-shield-crown">
                          <template #append>
                            <v-chip size="x-small" color="primary" variant="flat">
                              Nivel {{ item?.raw?.nivel || 1 }}
                            </v-chip>
                          </template>
                        </v-list-item>
                      </template>
                    </v-autocomplete>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>

            <!-- SECCIÓN 3: SEGURIDAD Y ACCESO -->
            <v-card variant="outlined" class="mb-2 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="purple" size="20">mdi-shield-lock-outline</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  3. Credenciales y Seguridad de Acceso
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-row density="compact">
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.email"
                      label="Correo Electrónico Corporativo *"
                      type="email"
                      prepend-inner-icon="mdi-email-outline"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      class="mb-2"
                      :rules="[
                        v => !!v || 'El correo es requerido',
                        v => /.+@.+\..+/.test(v) || 'El correo debe ser válido'
                      ]"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.password"
                      :label="isEditing ? 'Nueva Contraseña (Opcional)' : 'Contraseña de Acceso *'"
                      :type="mostrarPassword ? 'text' : 'password'"
                      prepend-inner-icon="mdi-lock-outline"
                      :append-inner-icon="mostrarPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                      @click:append-inner="mostrarPassword = !mostrarPassword"
                      variant="solo-filled"
                      flat
                      density="comfortable"
                      :rules="[
                        v => isEditing ? true : !!v || 'La clave es requerida',
                        v => !v || v.length >= 6 || 'Debe contener al menos 6 caracteres'
                      ]"
                    ></v-text-field>
                  </v-col>

                  <!-- Medidor de fortaleza de contraseña -->
                  <v-col cols="12" v-if="formData.password">
                    <div class="d-flex align-center justify-space-between mb-1">
                      <span class="text-caption font-weight-bold text-grey-darken-1">Fortaleza de contraseña</span>
                      <span class="text-caption font-weight-bold" :class="colorFortalezaText">
                        {{ textoFortaleza }}
                      </span>
                    </div>
                    <v-progress-linear
                      :model-value="valorFortaleza"
                      :color="colorFortaleza"
                      height="6"
                      rounded
                    ></v-progress-linear>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-form>
        </div>

        <!-- Barra de Acciones Inferior Pegada (Sticky Footer) -->
        <div class="pa-4 bg-white border-t d-flex align-center justify-space-between">
          <v-btn
            variant="text"
            color="grey-darken-1"
            @click="cerrar"
            prepend-icon="mdi-arrow-left"
          >
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
            {{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}
          </v-btn>
        </div>
      </div>
    </v-navigation-drawer>
  </v-layout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useJerarquias } from '../../hooks/useJerarquias';
import { useRangos } from '../../hooks/useRangos';

const props = defineProps({
  modelValue: Boolean,
  usuarioAEditar: Object,
  guardando: Boolean
});

const emit = defineEmits(['update:modelValue', 'guardar']);

// Cargar listas
const { data: jerarquias, isLoading: cargandoJerarquias } = useJerarquias();
const { data: rangos, isLoading: cargandoRangos } = useRangos();

const form = ref(null);
const esValido = ref(false);
const mostrarPassword = ref(false);

const getFormState = (data = null) => ({
  id: data?.id || null,
  dni: data?.trabajador?.numero_documento || '',
  nombres: data?.trabajador?.nombres || '',
  apellidos: data?.trabajador?.apellidos || '',
  email: data?.email || '',
  telefono: data?.trabajador?.telefono_principal || '',
  genero: data?.trabajador?.genero || null,
  jerarquia_id: data?.jerarquia_id || null,
  rango_id: data?.rango_id || null,
  password: ''
});

const formData = ref(getFormState());

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const isEditing = computed(() => !!formData.value.id);

const obtenerIniciales = computed(() => {
  const n = formData.value.nombres || '';
  const a = formData.value.apellidos || '';
  if (!n && !a) return 'US';
  return `${n.charAt(0)}${a.charAt(0)}`.toUpperCase();
});

// Medidor de Fortaleza de Clave
const valorFortaleza = computed(() => {
  const p = formData.value.password || '';
  if (!p) return 0;
  let score = 0;
  if (p.length >= 6) score += 33;
  if (/[A-Z]/.test(p)) score += 33;
  if (/[0-9]/.test(p) || /[^A-Za-z0-9]/.test(p)) score += 34;
  return score;
});

const colorFortaleza = computed(() => {
  if (valorFortaleza.value <= 33) return 'error';
  if (valorFortaleza.value <= 66) return 'warning';
  return 'success';
});

const colorFortalezaText = computed(() => {
  if (valorFortaleza.value <= 33) return 'text-error';
  if (valorFortaleza.value <= 66) return 'text-warning';
  return 'text-success';
});

const textoFortaleza = computed(() => {
  if (valorFortaleza.value <= 33) return 'Débil';
  if (valorFortaleza.value <= 66) return 'Media';
  return 'Fuerte';
});

watch(() => props.usuarioAEditar, (newVal) => {
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

.avatar-glow {
  box-shadow: 0 0 20px rgba(24, 103, 192, 0.3);
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
