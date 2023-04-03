<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon, BanknotesIcon } from '@heroicons/vue/24/solid';
import { Head, Link } from '@inertiajs/vue3';

import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { watchEffect, onMounted } from "vue";


ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps({
    users: Number,
    // roles: Number,
    permissions: Number,
    reportes: Number,
    ultimos5dias: Array,
    ultimasHoras: Array,
    diasNovalidos: Array,
    trabajadoresHoy: Array,
    centrosHoy: Array,
})
let width;

onMounted(() => {
  width = window.innerWidth;
});

watchEffect(() => {
  window.addEventListener('resize', () => {
    width = window.innerWidth;
    chartOptions.height = width
  });
});
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    color: "#0000f0",
    // todo
    darkcolor: "#fff",
    
}

const chartData = {
    name: 'Numero de reportes',
    datasets: [{
        label: 'Numero de Reportes',
        data: props.ultimos5dias,
        backgroundColor: '#f87979',
    }]
};
const chartData2 = {
    name: 'Total Horas',
    datasets: [{
        label: 'Horas',
        data: props.ultimasHoras,
        backgroundColor: '#187979',
    }]
};
const chartData3 = {
    name: 'Reportes invalidos',
    datasets: [{
        label: 'Reportes no validos',
        data: props.diasNovalidos,
        backgroundColor: '#1ff979',
    }]
};
const chartTrabajadoresHoy = {
    datasets: [{
        label: 'Horas por trabajador',
        data: props.trabajadoresHoy,
        backgroundColor: '#ffd979',
    }]
};
const centrosHoy = {
    datasets: [{
        label: 'Horas por centro',
        data: props.centrosHoy,
        backgroundColor: '#df1f79',
    }]
};

</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <Breadcrumb :title="'Dashboard'" :breadcrumbs="[]" />
        <div class="space-y-4">
            <div
                class="text-white dark:text-gray-100 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 sm:gap-4 overflow-hidden shadow-sm">
                <div>
                    <div class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-sky-600/70 dark:bg-sky-500/80 items-center overflow-hidden">
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.reportes }}</p>
                            <p class="text-md md:text-lg uppercase">{{ lang().label.reporte }}</p>
                        </div>
                        <div> <BanknotesIcon class="w-16 h-auto" /> </div>
                    </div>
                    <div
                        class="bg-blue-600 dark:bg-blue-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-blue-600/90 dark:hover:bg-blue-600/70">
                        <Link :href="route('Reportes.index')" class="flex justify-between items-center">
                        <p>{{ lang().label.more }}</p>
                        <ChevronRightIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
                <div v-show="can(['isAdmin'])">
                    <div class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-blue-700/70 dark:bg-blue-500/80 items-center overflow-hidden">
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.users }}</p>
                            <p class="text-md md:text-lg uppercase">{{ lang().label.user }}</p>
                        </div>
                        <div> <UserIcon class="w-16 h-auto" /> </div>
                    </div>
                    <div 
                        class="bg-blue-600 dark:bg-blue-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-blue-800/90 dark:hover:bg-blue-800/70">
                        <Link :href="route('user.index')" class="flex justify-between items-center">
                        <p>{{ lang().label.more }}</p>
                            <ChevronRightIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
                <div v-show="can(['isSuper'])">
                    <div class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-amber-600/70 dark:bg-amber-500/80 items-center overflow-hidden">
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.permissions }}</p>
                            <p class="text-md md:text-lg uppercase">{{ lang().label.permission }}</p>
                        </div>
                        <div> <ShieldCheckIcon class="w-16 h-auto" /> </div>
                    </div>
                    <div
                        class="bg-amber-600 dark:bg-amber-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-amber-600/90 dark:hover:bg-amber-600/70">
                        <Link :href="route('permission.index')" class="flex justify-between items-center">
                        <p>{{ lang().label.more }}</p>
                        <ChevronRightIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
            </div>

            <div v-show="can(['updateCorregido reporte']) || can(['isAdmin'])" class="grid xs:grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 my-5">
                <div class="my-2 mx-5 p-1">
                    <Bar id="my-chart-id"
                    :options="chartOptions"
                    :data="chartData" />
                </div>
                <div class="my-2 mx-5 p-1">
                    <Bar id="my-chart-id2"
                    :options="chartOptions"
                    :data="chartData2" />
                </div>
                <div class="my-2 mx-5 p-1">
                    <Bar id="my-chart-id3"
                    :options="chartOptions"
                    :data="chartData3" />
                </div>
                <div class="my-2 mx-5 p-1">
                    <Bar id="my-chart-id3"
                    :options="chartOptions"
                    :data="chartTrabajadoresHoy" />
                </div>
                <div class="my-2 mx-5 p-1">
                    <Bar id="my-chart-id3"
                    :options="chartOptions"
                    :data="centrosHoy" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
