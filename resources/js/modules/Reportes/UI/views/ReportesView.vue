<template>
  <v-container fluid class="pa-3 bg-grey-lighten-5 fill-height align-start">
    <v-row density="compact">
      <v-col cols="12">
        <!-- 1. ENCABEZADO Y BARRA DE CONTROL EN UNA SOLA FILA MINIMALISTA -->
        <div class="d-flex flex-wrap align-center justify-space-between gap-2 mb-3 pb-2 border-b bg-white pa-2 rounded-lg elevation-1">
          <div class="d-flex align-center gap-2">
            <v-avatar color="indigo-lighten-5" size="32" rounded="lg">
              <v-icon color="primary" size="18">mdi-chart-box-outline</v-icon>
            </v-avatar>
            <div>
              <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Centro de Reportes y Analítica</span>
              <span class="text-caption text-grey ml-2 hidden-xs-only">· Generación de informes estratégicos y exportaciones</span>
            </div>
          </div>

          <!-- CONTROLES UNIFICADOS -->
          <div class="d-flex align-center flex-wrap gap-2">
            <v-text-field
              v-model="busquedaGlobal"
              placeholder="Buscar reporte o métrica..."
              prepend-inner-icon="mdi-magnify"
              density="compact"
              variant="outlined"
              hide-details
              clearable
              style="width: 250px;"
            ></v-text-field>

            <v-btn
              color="primary"
              variant="elevated"
              size="small"
              prepend-icon="mdi-file-export-outline"
              class="rounded-lg font-weight-bold text-capitalize shadow-btn"
              @click="generarReporte"
            >
              Nuevo Reporte
            </v-btn>
          </div>
        </div>

        <!-- 2. TARJETAS DE MÉTRICAS KPI -->
        <v-row density="compact" class="mb-3">
          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="blue-lighten-5" size="36" rounded="lg">
                    <v-icon color="primary" size="18">mdi-chart-line</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Reportes Generados</div>
                    <div class="text-subtitle-1 font-weight-black text-grey-darken-3 line-height-tight">128</div>
                  </div>
                </div>
                <v-chip size="x-small" color="blue" variant="flat" class="font-weight-bold">Mes Actual</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="green-lighten-5" size="36" rounded="lg">
                    <v-icon color="success" size="18">mdi-file-excel-outline</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Exportaciones Excel / PDF</div>
                    <div class="text-subtitle-1 font-weight-black text-success line-height-tight">94</div>
                  </div>
                </div>
                <v-chip size="x-small" color="success" variant="tonal" class="font-weight-bold">+18 hoy</v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card class="elevation-1 rounded-lg border bg-white pa-2">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-2">
                  <v-avatar color="purple-lighten-5" size="36" rounded="lg">
                    <v-icon color="purple" size="18">mdi-clock-check-outline</v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-grey font-weight-bold text-uppercase" style="font-size: 10px;">Programados</div>
                    <div class="text-subtitle-1 font-weight-black text-purple line-height-tight">12 Autómatas</div>
                  </div>
                </div>
                <v-chip size="x-small" color="purple" variant="tonal" class="font-weight-bold">Activos</v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- 3. CATÁLOGO DE REPORTES (FABHUB STYLE) -->
        <v-row density="compact">
          <v-col v-for="reporte in reportesFiltrados" :key="reporte.id" cols="12" sm="6" md="4">
            <v-card class="elevation-2 rounded-lg border bg-white h-100 d-flex flex-column pa-3 hover-up">
              <div class="d-flex align-center justify-space-between mb-2">
                <v-avatar :color="reporte.color" size="36" rounded="lg" class="text-white">
                  <v-icon size="20">{{ reporte.icon }}</v-icon>
                </v-avatar>
                <v-chip size="x-small" :color="reporte.badgeColor" variant="tonal" class="font-weight-bold">
                  {{ reporte.categoria }}
                </v-chip>
              </div>

              <div class="text-subtitle-2 font-weight-bold text-grey-darken-3 mb-1">
                {{ reporte.nombre }}
              </div>
              <p class="text-caption text-grey mb-3 flex-grow-1" style="font-size: 11px;">
                {{ reporte.descripcion }}
              </p>

              <div class="d-flex align-center justify-space-between border-t pt-2 mt-auto">
                <span class="text-caption text-grey" style="font-size: 10px;">{{ reporte.frecuencia }}</span>
                <v-btn
                  size="x-small"
                  color="primary"
                  variant="tonal"
                  prepend-icon="mdi-download"
                  class="font-weight-bold text-capitalize rounded"
                  @click="descargar(reporte)"
                >
                  Descargar
                </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue';

const busquedaGlobal = ref('');

const catalogoReportes = [
  {
    id: 1,
    nombre: 'Reporte General de Usuarios y Accesos',
    descripcion: 'Listado consolidado de credenciales, jerarquías, rangos y estado de firmas de acceso al sistema.',
    categoria: 'Seguridad / IAM',
    icon: 'mdi-account-group',
    color: 'indigo',
    badgeColor: 'indigo',
    frecuencia: 'Actualizado en tiempo real'
  },
  {
    id: 2,
    nombre: 'Auditoría de Inicios de Sesión y Actividad',
    descripcion: 'Histórico de accesos por usuario, dirección IP, timestamps y eventos de autenticación.',
    categoria: 'Auditoría',
    icon: 'mdi-shield-history',
    color: 'blue',
    badgeColor: 'blue',
    frecuencia: 'Registrado diariamente'
  },
  {
    id: 3,
    nombre: 'Directorio Consolidado de Personal (RRHH)',
    descripcion: 'Fichas de trabajadores, documentos de identidad, información de contacto y vinculación institucional.',
    categoria: 'Personal',
    icon: 'mdi-account-badge-outline',
    color: 'purple',
    badgeColor: 'purple',
    frecuencia: 'Semanal'
  },
  {
    id: 4,
    nombre: 'Ponderación y Organigrama Jerárquico',
    descripcion: 'Matriz de dependencia administrativa por áreas, jefaturas y dependencias principales.',
    categoria: 'Organización',
    icon: 'mdi-sitemap',
    color: 'teal',
    badgeColor: 'teal',
    frecuencia: 'Mensual'
  },
  {
    id: 5,
    nombre: 'Cartera de Clientes y Contactos',
    descripcion: 'Resumen de empresas registradas, correo de facturación, teléfonos y ubicaciones geográficas.',
    categoria: 'Comercial',
    icon: 'mdi-contacts-outline',
    color: 'success',
    badgeColor: 'success',
    frecuencia: 'Actualizado al momento'
  }
];

const reportesFiltrados = computed(() => {
  if (!busquedaGlobal.value) return catalogoReportes;
  const q = busquedaGlobal.value.toLowerCase().trim();
  return catalogoReportes.filter(r => 
    r.nombre.toLowerCase().includes(q) || 
    r.categoria.toLowerCase().includes(q) ||
    r.descripcion.toLowerCase().includes(q)
  );
});

const generarReporte = () => {
  alert('Iniciando asistente de reporte personalizado...');
};

const descargar = (reporte) => {
  alert(`Descargando "${reporte.nombre}" en formato Excel...`);
};
</script>

<style scoped>
.gap-2 { gap: 8px; }
.line-height-tight { line-height: 1.2; }
.shadow-btn { box-shadow: 0 3px 10px 0 rgba(24, 103, 192, 0.35) !important; }
.hover-up { transition: transform 0.2s ease; }
.hover-up:hover { transform: translateY(-2px); }
</style>
