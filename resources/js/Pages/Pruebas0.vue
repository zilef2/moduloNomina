<script setup>
// import Breadcrumb from '@/Components/Breadcrumb.vue';
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import {ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon, BanknotesIcon} from '@heroicons/vue/24/solid';
// import {Head, Link} from '@inertiajs/vue3';
//
// import {Bar} from 'vue-chartjs'
// import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'
// import {watchEffect, onMounted, defineAsyncComponent, Suspense, reactive, ref} from "vue";


</script>

<template>

   <div class="min-h-screen bg-black text-white flex flex-col items-center justify-center p-8 space-y-12 font-sans">

  <!-- Título con Fade-in suave -->
  <h1 class="text-5xl font-medium animate-in fade-in slide-in-from-top-4 duration-1000">
    How would you like to start?
  </h1>

  <!-- Contenedor de Cards con Stagger (efecto cascada) -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full max-w-7xl">

    <!-- Card Template -->
    <!-- Repite este bloque 4 veces cambiando el contenido -->
    <div class="group relative p-[1px] rounded-2xl bg-neutral-800 transition-all duration-500 hover:scale-[1.02] active:scale-95 animate-in fade-in slide-in-from-bottom-8 fill-mode-both"
         style="animation-delay: 150ms;">

      <!-- Efecto de Brillo en el Borde (Glow) al hacer hover -->
      <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-neutral-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

      <!-- Contenido de la Card -->
      <div class="relative bg-neutral-900 rounded-[15px] p-8 h-full flex flex-col justify-between min-h-[300px] cursor-pointer">
        <div class="space-y-4">
          <h2 class="text-xl font-semibold group-hover:text-white transition-colors">Trace an Application</h2>
          <p class="text-neutral-400 text-sm leading-relaxed group-hover:text-neutral-300 transition-colors">
            Analyze and debug applications using traces.
          </p>
        </div>

        <!-- Flecha con animación de desplazamiento -->
        <div class="flex justify-end overflow-hidden">
          <svg class="w-6 h-6 text-neutral-500 transform translate-x-[-10px] translate-y-[10px] opacity-0 group-hover:translate-x-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 ease-out"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Card 2 (Cambiar delay para efecto escalonado) -->
    <div class="group relative p-[1px] rounded-2xl bg-neutral-800 transition-all duration-500 hover:scale-[1.02] active:scale-95 animate-in fade-in slide-in-from-bottom-8 fill-mode-both" style="animation-delay: 300ms;">
      <!-- ... mismo contenido ... -->
    </div>

  </div>

  <!-- Botón Skip con animación de pulso sutil -->
  <button class="text-neutral-500 hover:text-white transition-colors flex items-center gap-2 group">
    Skip
    <span class="group-hover:translate-x-1 transition-transform">→</span>
  </button>
</div>

</template>
