<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="indigo-lighten-5" size="32" rounded="lg">
              <v-icon color="primary" size="18">mdi-cogs</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Configuración del Sistema</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Parámetros globales, seguridad y opciones generales</span>
            </div>
          </div>

          <!-- BOTÓN GUARDAR -->
          <v-btn
            color="primary"
            variant="elevated"
            size="small"
            prepend-icon="mdi-check-circle-outline"
            class="rounded-lg font-weight-bold text-capitalize shadow-btn"
            @click="guardarCambios"
          >
            Guardar Cambios
          </v-btn>
        </div>

        <!-- 2. SECCIONES DE CONFIGURACIÓN FABHUB STYLE -->
        <v-row density="compact">
          <v-col cols="12" md="6">
            <v-card class="elevation-2 rounded-lg border bg-white pa-4 mb-3">
              <div class="d-flex align-center gap-2 border-b pb-2 mb-3">
                <v-icon color="primary" size="20">mdi-tune</v-icon>
                <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Parámetros de la Aplicación</span>
              </div>

              <v-text-field
                v-model="config.nombreEmpresa"
                label="Nombre de la Organización / Empresa"
                prepend-inner-icon="mdi-domain"
                variant="solo-filled"
                flat
                density="comfortable"
                class="mb-2"
              ></v-text-field>

              <v-text-field
                v-model="config.emailContacto"
                label="Correo Institucional de Notificaciones"
                prepend-inner-icon="mdi-email-outline"
                variant="solo-filled"
                flat
                density="comfortable"
                class="mb-2"
              ></v-text-field>

              <v-select
                v-model="config.zonaHoraria"
                :items="['America/Lima (UTC-5)', 'America/Bogota (UTC-5)', 'America/Santiago (UTC-4)']"
                label="Zona Horaria Predeterminada"
                prepend-inner-icon="mdi-clock-outline"
                variant="solo-filled"
                flat
                density="comfortable"
              ></v-select>
            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <v-card class="elevation-2 rounded-lg border bg-white pa-4 mb-3">
              <div class="d-flex align-center gap-2 border-b pb-2 mb-3">
                <v-icon color="indigo" size="20">mdi-shield-lock-outline</v-icon>
                <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Políticas de Seguridad y Autenticación</span>
              </div>

              <v-switch
                v-model="config.expiracionSesion"
                label="Cerrar sesión automáticamente por inactividad (30 min)"
                color="primary"
                hide-details
                density="compact"
                class="mb-2"
              ></v-switch>

              <v-switch
                v-model="config.claveCompleja"
                label="Exigir claves complejas (mayúsculas, números y símbolos)"
                color="primary"
                hide-details
                density="compact"
                class="mb-2"
              ></v-switch>

              <v-switch
                v-model="config.registroAuditoria"
                label="Activar registro detallado de auditoría de usuarios (Logs)"
                color="primary"
                hide-details
                density="compact"
              ></v-switch>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';

const config = ref({
  nombreEmpresa: 'FLY SaaS System',
  emailContacto: 'contacto@flysaas.com',
  zonaHoraria: 'America/Lima (UTC-5)',
  expiracionSesion: true,
  claveCompleja: true,
  registroAuditoria: true
});

const guardarCambios = () => {
  alert('Ajustes de configuración guardados correctamente.');
};
</script>

<style scoped>
.gap-2 { gap: 8px; }
.shadow-btn { box-shadow: 0 3px 10px 0 rgba(24, 103, 192, 0.35) !important; }
</style>
