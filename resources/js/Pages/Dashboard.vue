<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon, BanknotesIcon} from '@heroicons/vue/24/solid';
import {Head, Link} from '@inertiajs/vue3';

import {Bar} from 'vue-chartjs'
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'
import {watchEffect, onMounted, defineAsyncComponent, Suspense, reactive, ref} from "vue";

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps({
    users: Number,
    // roles: Number,
    permissions: Number,
    reportes: Number,
    ultimos5dias: Object,
    diasNovalidos: Object,
    trabajadoresHoy: Object,
    centrosHoy: Object,
    versionZilef: String,
    numberPermissions: Number,
    userid: Number,
    conteoPorRol: Object,
    topCentros: Object,
    chartLabels: Object,
    chartValues: Object,
})
let width;


onMounted(async () => {
    width = window.innerWidth;
});
watchEffect(() => {
    window.addEventListener('resize', () => {
        width = window.innerWidth;
        if (width < 2000)
            chartOptions.height = width
    });
});


// <!--<editor-fold desc="Chartts">-->

const chartOptions = {
    // responsive: true,
    // maintainAspectRatio: false,
    color: "#808080",
}
const chartOptions5 = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { callback: value => value.toLocaleString() },
    },
  },
};

const chartData = {
    name: 'Reportes',
    datasets: [{
        label: 'Numero de Reportes',
        data: props.ultimos5dias,
        backgroundColor: '#137ea5',
    }]
};
const chartData3 = {
    name: 'Reportes invalidos',
    datasets: [{
        label: 'Reportes no validos',
        data: props.diasNovalidos,
        backgroundColor: '#810409',
    }]
};

const chartData4 = { //roles
    name: 'Personas por rol',
    datasets: [{
        label: 'Roles',
        data: props.conteoPorRol,
        backgroundColor: '#814449',
    }]
};
const chartData5 = {
  labels: props.chartLabels,
  datasets: [
    {
      label: 'Centros con mayor mano de obra estimada',
      data: props.chartValues,
      backgroundColor: '#811111',
    },
  ],
};
const charityArray = [
    {id: 'my-chart-id4', data: chartData4},
    {id: 'my-chart-id', data: chartData},
    {id: 'my-chart-id3', data: chartData3},
    // { id: 'my-chart-id6', data: chartData6 },
    // { id: 'my-chart-id7', data: chartData7 },
    // { id: 'my-chart-id8', data: chartData8 },
    // { id: 'my-chart-id9', data: chartData9 },
    // { id: 'my-chart-id10', data: chartData10 },
];

// # fin chartts
// <!--</editor-fold>-->

</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <Breadcrumb :title="'Panel Principal'" :breadcrumbs="[]"/>
        <div class="min-h-screen w-full space-y-10">
            <div v-if="props.numberPermissions > 0" class="w-full">
                <div class="mx-auto p-4 h-96">
                    <Bar
                        id="chart-top-centros"
                        :options="chartOptions5"
                        :data="chartData5"
                    />
                </div>
            </div>

            <!-- 🔹 Gráficos secundarios en grid -->
            <div
                v-if="props.numberPermissions > 0"
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 px-4 pb-8 mt-24"
            >
                <div
                    v-for="(chart, index) in charityArray"
                    :key="chart.id"
                    class="p-4 h-80 bg-white rounded-2xl shadow-sm"
                >
                    <Bar
                        :id="chart.id"
                        :options="chartOptions"
                        :data="chart.data"
                    />
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>
