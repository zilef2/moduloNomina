<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { reactive, watch, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { formatPesosCol, number_format } from '@/global.ts';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Bar, Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement);

const props = defineProps({
    elIDD: Number,
    title: String,
    filters: Object,
    fromControllerHoras: Object,
    fromControllerPlata: Object,
    fromControllerAcumulado: Object,
    totalMesHoras: Number,
    totalMesPlata: Number,
    totalAcumulado: Number,
    acumuladoTotals: Object,
    chartData: Object,
    monthlyTrendData: Object,
    nombresTabla: Object,
    UltimoReporteRealizado: String,
    firstReportDate: String,
    yearOptions: Array,
})

const data = reactive({
    params: {
        fecha_ini: props.filters.fecha_ini || { month: new Date().getMonth(), year: new Date().getFullYear() },
        quincena: props.filters.quincena || { value: 3, label: 'Todo el Mes' },
    },
    isMoneyMode: props.filters.plata || false,
    hayUltimoreporte: props.UltimoReporteRealizado !== '_',
})

const monthOptions = [
    { label: 'Enero', value: 0 }, { label: 'Febrero', value: 1 }, { label: 'Marzo', value: 2 },
    { label: 'Abril', value: 3 }, { label: 'Mayo', value: 4 }, { label: 'Junio', value: 5 },
    { label: 'Julio', value: 6 }, { label: 'Agosto', value: 7 }, { label: 'Septiembre', value: 8 },
    { label: 'Octubre', value: 9 }, { label: 'Noviembre', value: 10 }, { label: 'Diciembre', value: 11 },
];

const selectedYear = computed({
    get: () => data.params.fecha_ini.year,
    set: (value) => { data.params.fecha_ini.year = value; }
});

const selectedMonth = computed({
    get: () => monthOptions.find(m => m.value === data.params.fecha_ini.month),
    set: (value) => { data.params.fecha_ini.month = value.value; }
});


watch(() => data.params, (newParams) => {
    router.get(route("CentroCostos.table", props.elIDD), { ...newParams, plata: data.isMoneyMode }, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    });
}, { deep: true });

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        title: { display: true, text: `Costo Acumulado` },
        tooltip: {
            callbacks: {
                label: (context) => `${context.label}: ${formatPesosCol(context.raw)}`
            }
        }
    },
    scales: {
        y: { ticks: { callback: value => formatPesosCol(value) } }
    }
};

const tableHeaders = [
    { label: 'Empleado', align: 'left', sticky: true },
    { label: 'Total', align: 'center' },
    { label: 'Diurnas', align: 'left' },
    { label: 'Nocturnas', align: 'left' },
    { label: 'Ext. Diurnas', align: 'left' },
    { label: 'Ext. Nocturnas', align: 'left' },
    { label: 'Dom. Diurno', align: 'left' },
    { label: 'Dom. Nocturno', align: 'left' },
    { label: 'Dom. Ext. D.', align: 'left' },
    { label: 'Dom. Ext. N.', align: 'left' },
];

const formatValue = (value) => {
    return data.isMoneyMode ? formatPesosCol(value) : number_format(value, 0);
};


</script>
<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <div class="py-4 px-2 sm:px-4 lg:px-6 w-full flex flex-col min-h-0">
            <div class="mb-4 flex flex-col md:flex-row md:items-end justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex-1">
                     <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Reporte: <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">{{ props.title }}</span>
                    </h1>
                </div>
                <div v-if="data.hayUltimoreporte" class="flex flex-wrap items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-2 rounded-xl border border-gray-100 dark:border-gray-700">
                    <v-select v-model="selectedYear" :options="yearOptions" placeholder="Año" class="custom-table-select min-w-[100px]"></v-select>
                    <v-select v-model="selectedMonth" :options="monthOptions" placeholder="Mes" class="custom-table-select min-w-[140px]"></v-select>
                    <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 hidden md:block self-end mb-1"></div>
                    <v-select v-model="data.params.quincena" :clearable="false" :options="[{ value: 1, label: '1ra Quincena' }, { value: 2, label: '2da Quincena' }, { value: 3, label: 'Todo el Mes' }]" class="custom-table-select min-w-[140px]"></v-select>
                    <PrimaryButton @click="data.isMoneyMode = !data.isMoneyMode" class="h-9 px-4 rounded-lg text-xs shadow-none">
                        <span v-if="data.isMoneyMode" class="flex items-center gap-1">Modo Dinero</span>
                        <span v-else class="flex items-center gap-1">Modo Horas</span>
                    </PrimaryButton>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-6">
                <!-- Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border flex flex-col">
                        <h3 class="text-lg font-bold p-4 border-b">Reporte Mensual</h3>
                        <div class="flex-1 overflow-auto scrollbar-table">
                            <table class="w-full text-left border-collapse" style="min-width: 800px;">
                                <thead class="sticky top-0 z-10 bg-gray-50">
                                    <tr class="text-[10px] font-bold uppercase tracking-widest border-b">
                                        <th v-for="header in tableHeaders" :key="header.label"
                                            class="min-w-36 px-4 py-3"
                                            :class="{ 'sticky left-0 z-20 bg-gray-50': header.sticky, 'text-center': header.align === 'center' }">
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-for="item in (data.isMoneyMode ? fromControllerPlata.data : fromControllerHoras.data)" :key="item.user_id">
                                        <td class="min-w-36 px-4 py-3 sticky left-0 bg-white z-10">{{ item.usera }}</td>
                                        <td class="min-w-36 px-4 py-3 text-center font-bold">{{ formatValue(item.horas_trabajadas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.diurnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.nocturnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.extra_diurnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.extra_nocturnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.dominical_diurno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.dominical_nocturno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.dominical_extra_diurno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatValue(item.dominical_extra_nocturno) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="sticky bottom-0 bg-amber-100">
                                    <tr class="font-black text-amber-900">
                                        <td class="min-w-36 px-4 py-3 sticky left-0 bg-amber-100 z-10">TOTALES</td>
                                        <td class="min-w-36 px-4 py-3 text-center">{{ data.isMoneyMode ? formatPesosCol(totalMesPlata) : number_format(totalMesHoras,0) }}</td>
                                        <td colspan="8"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border flex flex-col">
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-bold">Reporte Acumulado</h3>
                            <p v-if="firstReportDate" class="text-xs text-gray-500">Desde {{ firstReportDate }}</p>
                        </div>
                        <div class="flex-1 overflow-auto scrollbar-table max-h-[75vh]">
                             <table class="w-full text-left border-collapse" style="min-width: 800px;">
                                <thead class="sticky top-0 z-10 bg-gray-50">
                                    <tr class="text-[10px] font-bold uppercase tracking-widest border-b">
                                        <th v-for="header in tableHeaders" :key="header.label"
                                            class="min-w-36 px-4 py-3"
                                            :class="{ 'sticky left-0 z-20 bg-gray-50': header.sticky, 'text-center': header.align === 'center' }">
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-for="item in fromControllerAcumulado.data" :key="item.user_id">
                                        <td class="min-w-36 px-4 py-3 sticky left-0 bg-white z-10">{{ item.usera }}</td>
                                        <td class="min-w-36 px-4 py-3 text-center font-bold">{{ formatPesosCol(item.horas_trabajadas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.diurnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.nocturnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.extra_diurnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.extra_nocturnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.dominical_diurno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.dominical_nocturno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.dominical_extra_diurno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.dominical_extra_nocturno) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="sticky bottom-0 bg-amber-100">
                                    <tr class="font-black text-amber-900">
                                        <td class="min-w-36 px-4 py-3 sticky left-0 bg-amber-100 z-10">TOTALES</td>
                                        <td class="min-w-36 px-4 py-3 text-center">{{ formatPesosCol(totalAcumulado) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.diurnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.nocturnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.extra_diurnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.extra_nocturnas) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.dominical_diurno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.dominical_nocturno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.dominical_extra_diurno) }}</td>
                                        <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.dominical_extra_nocturno) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Visualizations -->
                <div class="mt-6">
                    <h2 class="text-xl font-bold mb-4">Visualizaciones</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border p-6">
                            <h3 class="text-lg font-bold mb-4">Gráfica de Costos Acumulados</h3>
                            <div class="h-96">
                                <Bar :data="chartData" :options="chartOptions" />
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border p-6">
                            <h3 class="text-lg font-bold mb-4">Tendencia de Costo Mensual</h3>
                            <div class="h-96">
                                <Line :data="monthlyTrendData" :options="chartOptions" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style>
.scrollbar-table::-webkit-scrollbar { height: 8px; width: 8px; }
.scrollbar-table::-webkit-scrollbar-track { background: transparent; }
.scrollbar-table::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dark .scrollbar-table::-webkit-scrollbar-thumb { background: #334155; }
.custom-table-select .vs__dropdown-toggle { border-radius: 0.5rem; height: 2.25rem; border-color: #e5e7eb; background: white; font-size: 0.75rem; font-weight: 600; }
.dark .custom-table-select .vs__dropdown-toggle { background: #111827; border-color: #374151; }
.dark .custom-table-select .vs__selected { color: #f9fafb; }
.dark .custom-table-select .vs__actions svg { fill: #9ca3af; }
</style>
