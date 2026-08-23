<template>
  <v-layout style="min-height: 0;">
    <v-navigation-drawer
      v-model="dialog"
      location="right"
      temporary
      width="680"
      class="elevation-24 drawer-futurista"
    >
      <div class="d-flex flex-column h-100 bg-surface">
        <!-- Header -->
        <div class="header-banner pa-4 d-flex align-center justify-space-between text-white">
          <div class="d-flex align-center gap-3">
            <v-avatar color="white" size="44" class="elevation-3">
              <v-icon color="primary" size="24">mdi-shield-key-outline</v-icon>
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-bold line-height-tight">
                Gestión de Accesos
              </div>
              <div class="text-caption text-white-opacity-80">
                {{ usuario?.trabajador ? `${usuario.trabajador.nombres} ${usuario.trabajador.apellidos}` : usuario?.email }}
              </div>
            </div>
          </div>

          <v-btn icon="mdi-close" variant="text" color="white" density="comfortable" @click="dialog = false">
            <v-icon>mdi-close</v-icon>
            <v-tooltip activator="parent" location="bottom">Cerrar panel</v-tooltip>
          </v-btn>
        </div>

        <!-- Cuerpo del Formulario Continuo -->
        <div class="flex-grow-1 overflow-y-auto pa-5">
          <div v-if="cargandoUsuario || cargandoRoles || cargandoPermisos" class="text-center pa-8">
            <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
            <p class="mt-3 text-caption text-grey">Cargando política de accesos...</p>
          </div>

          <div v-else>
            <!-- SECCIÓN 1: ROLES GENERALES -->
            <v-card variant="outlined" class="mb-4 rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="primary" size="20">mdi-account-badge-outline</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  1. Roles Asignados (Plantillas de Accesos)
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-autocomplete
                  v-model="rolesSeleccionados"
                  :items="rolesData || []"
                  item-title="name"
                  item-value="name"
                  label="Seleccionar Rol(es)"
                  prepend-inner-icon="mdi-shield-account"
                  multiple
                  chips
                  variant="solo-filled"
                  flat
                  density="comfortable"
                  hide-details
                  clearable
                ></v-autocomplete>
                <div class="text-caption text-grey mt-2">
                  <v-icon size="14" class="mr-1">mdi-information-outline</v-icon>
                  Los roles heredan automáticamente un paquete de permisos recomendados.
                </div>
              </v-card-text>
            </v-card>

            <!-- SECCIÓN 2: PERMISOS ESPECÍFICOS POR MÓDULO -->
            <v-card variant="outlined" class="rounded-xl border-grey-lighten-3">
              <v-card-item class="bg-grey-lighten-5 border-b py-2">
                <template #prepend>
                  <v-icon color="indigo" size="20">mdi-lock-open-check-outline</v-icon>
                </template>
                <v-card-title class="text-subtitle-2 font-weight-bold">
                  2. Permisos Específicos por Submódulo
                </v-card-title>
              </v-card-item>

              <v-card-text class="pa-4">
                <v-expansion-panels v-model="panelActivo" multiple variant="accordion">
                  <v-expansion-panel
                    v-for="moduloPadre in estructuraModulos"
                    :key="moduloPadre.name"
                    class="mb-3 border rounded-xl overflow-hidden"
                  >
                    <v-expansion-panel-title class="bg-grey-lighten-5 py-2">
                      <v-icon :icon="moduloPadre.icon" class="mr-2 text-primary" size="20"></v-icon>
                      <span class="font-weight-bold text-subtitle-2 text-grey-darken-3">{{ moduloPadre.name }}</span>
                    </v-expansion-panel-title>

                    <v-expansion-panel-text class="pt-3 bg-white">
                      <div v-for="submodulo in moduloPadre.submodules" :key="submodulo" class="mb-4 last:mb-0">
                        <div class="d-flex align-center justify-space-between mb-2 pb-1 border-b">
                          <span class="text-caption font-weight-bold text-grey-darken-2 text-capitalize d-flex align-center">
                            <v-icon size="14" class="mr-1 text-grey">mdi-subdirectory-arrow-right</v-icon>
                            {{ submodulo.replace('-', ' ') }}
                          </span>
                          <div class="d-flex gap-1">
                            <v-btn size="x-small" variant="tonal" color="primary" @click="marcarSubmodulo(submodulo)">
                              Marcar Todo
                            </v-btn>
                            <v-btn size="x-small" variant="tonal" color="error" @click="desmarcarSubmodulo(submodulo)">
                              Desmarcar
                            </v-btn>
                          </div>
                        </div>

                        <v-row density="compact">
                          <template v-if="permisosData[submodulo] && permisosData[submodulo].length">
                            <v-col v-for="permiso in permisosData[submodulo]" :key="permiso.id" cols="12" sm="6">
                              <v-checkbox
                                v-model="permisosSeleccionados"
                                :value="permiso.name"
                                :label="permiso.action.toUpperCase()"
                                color="primary"
                                density="compact"
                                hide-details
                                class="mt-0"
                              ></v-checkbox>
                            </v-col>
                          </template>
                          <template v-else>
                            <v-col cols="12">
                              <span class="text-caption text-grey italic">Sin permisos específicos configurados</span>
                            </v-col>
                          </template>
                        </v-row>
                      </div>
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>
              </v-card-text>
            </v-card>
          </div>
        </div>

        <!-- Sticky Footer -->
        <div class="pa-4 bg-white border-t d-flex align-center justify-space-between">
          <v-btn variant="text" color="grey-darken-1" @click="dialog = false" :disabled="guardando" prepend-icon="mdi-arrow-left">
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
            Guardar Accesos
          </v-btn>
        </div>
      </div>
    </v-navigation-drawer>
  </v-layout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useRoles, usePermisos, useUserPermissions, useSyncUserPermissions } from '../../hooks/useRolesPermisos';

const props = defineProps({
  modelValue: Boolean,
  usuario: Object
});

const emit = defineEmits(['update:modelValue', 'guardado']);

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const estructuraModulos = [
  {
    name: 'Módulo de Usuarios & Seguridad',
    icon: 'mdi-account-group-outline',
    submodules: ['usuarios', 'roles', 'jerarquias', 'rangos', 'trabajadores']
  },
  {
    name: 'Módulo de Clientes',
    icon: 'mdi-briefcase-account-outline',
    submodules: ['clientes']
  },
  {
    name: 'Reportes & Analítica',
    icon: 'mdi-chart-box-outline',
    submodules: ['reportes']
  },
  {
    name: 'Configuración del Sistema',
    icon: 'mdi-cog-outline',
    submodules: ['configuracion']
  }
];

const { data: rolesData, isLoading: cargandoRoles } = useRoles();
const { data: permisosData, isLoading: cargandoPermisos } = usePermisos();

const userId = computed(() => props.usuario?.id);
const { data: userData, isLoading: cargandoUsuario } = useUserPermissions(userId);
const mutacionSync = useSyncUserPermissions();

const rolesSeleccionados = ref([]);
const permisosSeleccionados = ref([]);
const panelActivo = ref([]);

watch(userData, (newVal) => {
  if (newVal) {
    rolesSeleccionados.value = [...(newVal.roles || [])];
    permisosSeleccionados.value = [...(newVal.direct_permissions || [])];
  }
}, { immediate: true });

watch(permisosData, (newVal) => {
  if (newVal && panelActivo.value.length === 0) {
    panelActivo.value = [0];
  }
});

const marcarSubmodulo = (submoduloNombre) => {
  if (!permisosData.value || !permisosData.value[submoduloNombre]) return;
  const nuevosPermisos = [...permisosSeleccionados.value];
  const acciones = permisosData.value[submoduloNombre];
  acciones.forEach(p => {
    if (!nuevosPermisos.includes(p.name)) {
      nuevosPermisos.push(p.name);
    }
  });
  permisosSeleccionados.value = nuevosPermisos;
};

const desmarcarSubmodulo = (submoduloNombre) => {
  if (!permisosData.value || !permisosData.value[submoduloNombre]) return;
  const acciones = permisosData.value[submoduloNombre];
  permisosSeleccionados.value = permisosSeleccionados.value.filter(
    (name) => !acciones.some(p => p.name === name)
  );
};

const guardando = computed(() => mutacionSync.isPending.value);

const guardar = () => {
  mutacionSync.mutate({
    userId: props.usuario.id,
    data: {
      roles: rolesSeleccionados.value,
      permissions: permisosSeleccionados.value
    }
  }, {
    onSuccess: () => {
      dialog.value = false;
      emit('guardado');
    },
    onError: (error) => {
      alert('Error al sincronizar accesos: ' + (error.response?.data?.message || error.message));
    }
  });
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

.gap-1 { gap: 4px; }
.gap-3 { gap: 12px; }
</style>
