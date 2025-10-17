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

const chartData = {
    name: 'Numero de reportes',
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
const chartData5 = { //
    name: 'Mayores gastos',
    datasets: [{
        label: 'Centros de Costos',
        data: props.topCentros,
        backgroundColor: '#811111',
    }]
};
const charityArray = [
  { id: 'my-chart-id4', data: chartData4 },
  // { id: 'my-chart-id5', data: chartData5 },
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
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <Breadcrumb :title="'Panel Principal'" :breadcrumbs="[]"/>
        <div class="h-screen">
<!--            <div-->
<!--                class="text-white dark:text-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6 ">-->
<!--                <div>-->
<!--                    <div-->
<!--                        class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-sky-600/70 dark:bg-sky-500/80 items-center overflow-hidden">-->
<!--                        <div class="flex flex-col">-->
<!--                            <p class="text-4xl font-bold">{{ props.reportes }}</p>-->
<!--                            <p class="text-md md:text-lg uppercase">{{ lang().label.reporte }}</p>-->
<!--                        </div>-->
<!--                        <div>-->
<!--                            <BanknotesIcon class="w-16 h-auto"/>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div-->
<!--                        class="bg-blue-600 dark:bg-blue-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-blue-600/90 dark:hover:bg-blue-600/70">-->
<!--                        <Link :href="route('Reportes.index')" class="flex justify-between items-center">-->
<!--                            <p>{{ lang().label.more }}</p>-->
<!--                            <ChevronRightIcon class="w-5 h-5"/>-->
<!--                        </Link>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div v-show="can(['isAdmin'])">-->
<!--                    <div-->
<!--                        class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-blue-700/70 dark:bg-blue-500/80 items-center overflow-hidden">-->
<!--                        <div class="flex flex-col">-->
<!--                            <p class="text-4xl font-bold">{{ props.users }}</p>-->
<!--                            <p class="text-md md:text-lg uppercase">{{ lang().label.user }}</p>-->
<!--                        </div>-->
<!--                        <div>-->
<!--                            <UserIcon class="w-16 h-auto"/>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div-->
<!--                        class="bg-blue-600 dark:bg-blue-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-blue-800/90 dark:hover:bg-blue-800/70">-->
<!--                        <Link :href="route('user.index')" class="flex justify-between items-center">-->
<!--                            <p>{{ lang().label.more }}</p>-->
<!--                            <ChevronRightIcon class="w-5 h-5"/>-->
<!--                        </Link>-->
<!--                    </div>-->
<!--                </div>-->
<!--               -->
<!--            </div>-->
            <div v-if="props.numberPermissions > 0"
                 class="flex mt-26 w-full h-80">
                <div class="my-2 mx-5 p-1 h-full max-h-80">
                    <Bar
                        :id="'my-chart-id5'"
                        :options="chartOptions"
                        :data="chartData5"
                    />
                </div>
            </div>
            <div v-if="props.numberPermissions > 0"
                 class="grid xs:grid-cols-1 md:grid-cols-2 mt-26 gap-8 max-h-24">
                <div
                    v-for="(chart, index) in charityArray"
                    :key="chart.id"
                    class="my-2 mx-5 p-1 h-full max-h-92"
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
