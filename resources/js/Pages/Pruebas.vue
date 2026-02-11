<!-- NewsSection.vue -->
<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-slate-50 via-white to-slate-100 py-14 px-6">
    <!-- Decorative blobs -->
    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-indigo-200/40 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-emerald-200/40 blur-3xl"></div>

    <div class="relative mx-auto max-w-6xl">
      <!-- Header -->
      <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-slate-900">
            ¡Hola, Carlos!
          </h1>
          <p class="mt-2 text-slate-600">
            Esto es lo nuevo que ha pasado en <span class="font-semibold text-indigo-600">Modulo nomina</span>
          </p>
        </div>

        <!-- Avatars -->
        <!-- <div class="flex -space-x-3">
          <img class="h-12 w-12 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/180?img=12" />
          <img class="h-12 w-12 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/180?img=22" />
          <img class="h-12 w-12 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/180?img=38" />
        </div> -->
      </div>

      <!-- Highlight -->
      <div
        :class="[
          'mt-10 rounded-2xl border p-8 backdrop-blur transition-all',
          highlight
            ? 'border-indigo-400 bg-indigo-50/70 shadow-xl shadow-indigo-200/50 scale-[1.01]'
            : 'border-slate-200 bg-white/70'
        ]"
      >
        <h2 class="text-xl font-semibold text-slate-900">
          🔥 Punto clave en desarrollo
        </h2>
        <p class="mt-2 text-slate-700">
          Medición del <strong>gasto acumulado de un proyecto</strong> en porcentaje, en tiempo real.
        </p>
        <p class="mt-1 text-sm text-slate-500">
          Esta funcionalidad conectará tiempos, cotizaciones y centros de trabajo.
        </p>
      </div>

      <!-- News grid -->
      <div class="mt-10 grid gap-6 md:grid-cols-2">
        <article class="group rounded-xl border border-slate-200 bg-white/80 p-6 backdrop-blur transition hover:-translate-y-1 hover:shadow-lg">
          <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600">
            ⏱️ Cálculo de tiempos de empleados
          </h3>
          <p class="mt-2 text-sm text-slate-600">
            Mejoras en precisión, validaciones y consistencia por centro de trabajo.
          </p>
        </article>

        <article class="group rounded-xl border border-slate-200 bg-white/80 p-6 backdrop-blur transition hover:-translate-y-1 hover:shadow-lg">
          <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600">
            💼 Cotizaciones más estructuradas
          </h3>
          <p class="mt-2 text-sm text-slate-600">
            Base sólida para enlazar costos estimados vs costos reales.
          </p>
        </article>

        <a href="CentroCostos" class="group rounded-xl border border-slate-200 bg-white/80 p-6 backdrop-blur transition hover:-translate-y-1 hover:shadow-lg">
          <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600">
            🏗️ Centros de trabajo
          </h3>
          <p class="mt-2 text-sm text-slate-600">
            Organización clara para imputar horas y gastos por proyecto.
          </p>
        </a>

        <a href="CentroCostos" class="group rounded-xl border border-dashed border-indigo-300 bg-indigo-50/60 p-6 backdrop-blur transition hover:shadow-lg">
          <h3 class="font-semibold text-indigo-700">
            📊 Control de gasto (próximamente)
          </h3>
          <p class="mt-2 text-sm text-indigo-700">
            Visualización porcentual de ejecución presupuestal.
          </p>
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const highlight = ref(false)

onMounted(() => {
  // Resalta automáticamente la sección más importante
  setTimeout(() => {
    highlight.value = true
  }, 600)
})
</script>
