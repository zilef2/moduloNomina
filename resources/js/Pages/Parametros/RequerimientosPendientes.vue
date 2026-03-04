<script setup>
import { Head } from '@inertiajs/vue3';
import { CalendarIcon, ChatBubbleBottomCenterTextIcon, ClockIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';

const requirements = [
  {
    date: "10",
    month: "Marzo",
    year: "2026",
    title: "En desarrollo y pruebas",
    description: "El formulario de viaticos cambió, se espera que en marzo se aclaren los inconvenientes",
    status: "Pendiente",
    priority: "Media",
    color: "blue"
  },
  {
    date: "10",
    month: "Febrero",
    year: "2026",
    title: "En desarrollo y pruebas",
    description: "Informe de centro de costos, ahora debe ser acumulativo.",
    status: "Pendiente",
    priority: "Media",
    color: "amber"
  },
  {
    date: "10",
    month: "Junio",
    year: "2025",
    title: "Pendiente de Pruebas",
    description: "Solicitud de viáticos: el módulo aún no ha sido probado. Pendiente requisitos para añadir los nuevos cambios solicitados en 2025.",
    status: "En espera",
    priority: "Alta",
    color: "sky"
  }
];
</script>

<template>

  <Head title="Requerimientos Pendientes" />

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
      <!-- Header Section -->
      <div class="mb-12 text-center animate-fade-in-down">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4 tracking-tight">
          Requerimientos <span
            class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Pendientes</span>
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
          Seguimiento de las próximas reuniones, actualizaciones y módulos por testear en el sistema.
        </p>
        <div class="mt-4 flex justify-center space-x-2">
          <span class="h-1 w-12 bg-amber-500 rounded-full"></span>
          <span class="h-1 w-4 bg-amber-300 rounded-full"></span>
        </div>
      </div>

      <!-- Timeline Section -->
      <div
        class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-300 before:to-transparent dark:before:via-gray-700">

        <div v-for="(item, index) in requirements" :key="index"
          class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">

          <!-- Icono en el centro de la línea -->
          <div
            class="flex items-center justify-center w-10 h-10 rounded-full border border-white dark:border-gray-800 bg-amber-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
            <CalendarIcon class="w-5 h-5" />
          </div>

          <!-- Contenido de la Tarjeta -->
          <div
            class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200/50 dark:shadow-none hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 ease-out">
            <div
              class="flex items-center justify-between mb-3 text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
              <div class="flex items-center">
                <ClockIcon class="w-4 h-4 mr-1" />
                <span>{{ item.month }} {{ item.year }}</span>
              </div>
              <span :class="[
                'px-2 py-1 rounded-full text-white',
                item.priority === 'Alta' ? 'bg-red-500' : 'bg-sky-500'
              ]">
                {{ item.priority }}
              </span>
            </div>

            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 flex items-center">
              {{ item.title }}
            </h3>

            <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
              {{ item.description }}
            </p>

            <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-gray-700">
              <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <InformationCircleIcon class="w-4 h-4 mr-1" />
                <span>Estado: {{ item.status }}</span>
              </div>
              <div class="flex -space-x-2">
                <div v-for="i in 3" :key="i"
                  class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-[10px] text-gray-500 font-bold">
                  {{ i }}
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
</template>

<style scoped>
.animate-fade-in-down {
  animation: fadeInDown 0.8s ease-out;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Glassmorphism subtle effect for dark mode cards */
.dark .shadow-xl {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
}
</style>
