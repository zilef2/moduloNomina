<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon, BanknotesIcon } from '@heroicons/vue/24/solid';
import { Head, Link } from '@inertiajs/vue3';

import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { watchEffect, onMounted, defineAsyncComponent, Suspense, reactive, ref } from "vue";

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
    responsive: true,
    maintainAspectRatio: false,
    color: "#808080",
    plugins: {
        legend: {
            display: true,
            position: 'top',
        },
    },
}
const chartOptions5 = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || '';
                    if (label) label += ': ';
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { callback: value => value.toLocaleString() },
        },
    },
};

const chartData = {
    labels: ['Reportes'],
    datasets: Object.entries(props.ultimos5dias || {}).map(([label, value], index) => ({
        label: label,
        data: [value],
        backgroundColor: ['#137ea5', '#17a2b8', '#20c997'][index] || '#137ea5',
    }))
};

const chartData3 = {
    labels: ['Reportes no validos'],
    datasets: Object.entries(props.diasNovalidos || {}).map(([label, value], index) => ({
        label: label,
        data: [value],
        backgroundColor: ['#810409', '#2619bd', '#e74a3b', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'][index] || '#810409',
    }))
};

const chartData4 = { //roles
    labels: ['Personas por rol'],
    datasets: Object.entries(props.conteoPorRol || {}).map(([label, value], index) => ({
        label: label,
        data: [value],
        backgroundColor: [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'
        ][index] || '#814449',
    }))
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
    { id: 'my-chart-id4', data: chartData4 },
    { id: 'my-chart-id', data: chartData },
    { id: 'my-chart-id3', data: chartData3 },
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

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <Breadcrumb :title="'Panel Principal'" :breadcrumbs="[]" />
        <div class="min-h-screen w-full space-y-10">
            <div v-if="props.numberPermissions > 0"
                class="w-full bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <BanknotesIcon class="w-6 h-6 text-emerald-500" />
                        Top 10 Centros de Costo
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">
                        Centros con mano de obra estimada superior a
                        <span
                            class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-lg text-xs font-bold">$15,000</span>
                    </p>
                </div>
                <div class="h-96">
                    <Bar id="chart-top-centros" :options="chartOptions5" :data="chartData5" />
                </div>
            </div><!-- 🔹 Gráficos secundarios en grid -->
            <div v-if="props.numberPermissions > 0"
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 px-4 pb-8 mt-24">
                <div v-for="(chart, index) in charityArray" :key="chart.id"
                    class="p-4 h-80 bg-white rounded-2xl shadow-sm">
                    <Bar :id="chart.id" :options="chartOptions" :data="chart.data" />
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>
