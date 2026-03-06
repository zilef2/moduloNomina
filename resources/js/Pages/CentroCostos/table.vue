<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {reactive, watch, computed} from 'vue';
import {Head, router, Link} from '@inertiajs/vue3';
import {formatPesosCol, number_format} from '@/global.ts';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {
    Chart as ChartJS, Title,
    Tooltip, Legend, BarElement, CategoryScale,
    LinearScale, PointElement, LineElement, ArcElement, Filler
} from 'chart.js';
import {Bar, Line, Doughnut} from 'vue-chartjs'

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
        fecha_ini: props.filters.fecha_ini || {month: new Date().getMonth(), year: new Date().getFullYear()},
        quincena: props.filters.quincena || {value: 3, label: 'Todo el Mes'},
    },
    isMoneyMode: props.filters.plata || false,
    hayUltimoreporte: props.UltimoReporteRealizado !== '_',
})

const monthOptions = [
    {label: 'Enero', value: 0}, {label: 'Febrero', value: 1}, {label: 'Marzo', value: 2},
    {label: 'Abril', value: 3}, {label: 'Mayo', value: 4}, {label: 'Junio', value: 5},
    {label: 'Julio', value: 6}, {label: 'Agosto', value: 7}, {label: 'Septiembre', value: 8},
    {label: 'Octubre', value: 9}, {label: 'Noviembre', value: 10}, {label: 'Diciembre', value: 11},
];

const selectedYear = computed({
    get: () => data.params.fecha_ini.year,
    set: (value) => {
        data.params.fecha_ini.year = value;
    }
});

const selectedMonth = computed({
    get: () => monthOptions.find(m => m.value === data.params.fecha_ini.month),
    set: (value) => {
        data.params.fecha_ini.month = value.value;
    }
});


watch(() => data.params, (newParams) => {
    router.get(route("CentroCostos.table", props.elIDD), {...newParams, plata: data.isMoneyMode}, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    });
}, {deep: true});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {display: false},
        title: {display: true, text: `Costo Acumulado`},
        tooltip: {
            callbacks: {
                label: (context) => `${context.label}: ${formatPesosCol(context.raw)}`
            }
        }
    },
    scales: {
        y: {ticks: {callback: value => formatPesosCol(value)}}
    }
};

const tableHeaders = [
    {label: 'Empleado', align: 'left', sticky: true},
    {label: 'Total', align: 'center'},
    {label: 'Diurnas', align: 'left'},
    {label: 'Nocturnas', align: 'left'},
    {label: 'Ext. Diurnas', align: 'left'},
    {label: 'Ext. Nocturnas', align: 'left'},
    {label: 'Dom. Diurno', align: 'left'},
    {label: 'Dom. Nocturno', align: 'left'},
    {label: 'Dom. Ext. D.', align: 'left'},
    {label: 'Dom. Ext. N.', align: 'left'},
];

const formatValue = (value) => {
    return data.isMoneyMode ? formatPesosCol(value) : number_format(value, 0);
};


ChartJS.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, ArcElement, Tooltip, Legend, Filler)

const COLORS = {
    purple: '#7C6FF7',
    purpleLight: 'rgba(124,111,247,0.15)',
    teal: '#2ED9C3',
    tealLight: 'rgba(46,217,195,0.15)',
    orange: '#FF8C42',
    red: '#FF5C7A',
    gray: '#94A3B8',
}

const barOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {display: false},
        tooltip: {
            backgroundColor: '#1E293B',
            titleColor: '#94A3B8',
            bodyColor: '#F1F5F9',
            padding: 12,
            cornerRadius: 8,
        }
    },
    scales: {
        x: {
            grid: {display: false},
            ticks: {color: '#94A3B8', font: {size: 11}},
            border: {display: false}
        },
        y: {
            grid: {color: 'rgba(148,163,184,0.08)'},
            ticks: {color: '#94A3B8', font: {size: 11}},
            border: {display: false}
        }
    },
    datasets: {
        bar: {
            backgroundColor: COLORS.purple,
            borderRadius: 6,
            borderSkipped: false,
        }
    }
}))

const lineOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {display: false},
        tooltip: {
            backgroundColor: '#1E293B',
            titleColor: '#94A3B8',
            bodyColor: '#F1F5F9',
            padding: 12,
            cornerRadius: 8,
        }
    },
    scales: {
        x: {
            grid: {display: false},
            ticks: {color: '#94A3B8', font: {size: 11}},
            border: {display: false}
        },
        y: {
            grid: {color: 'rgba(148,163,184,0.08)'},
            ticks: {color: '#94A3B8', font: {size: 11}},
            border: {display: false}
        }
    },
    elements: {
        line: {
            borderColor: COLORS.teal,
            borderWidth: 2.5,
            tension: 0.4,
            fill: true,
            backgroundColor: COLORS.tealLight,
        },
        point: {
            backgroundColor: COLORS.teal,
            radius: 4,
            hoverRadius: 6,
        }
    }
}))

const donutData = computed(() => {
    const t = props.acumuladoTotals
    if (!t) return {labels: [], datasets: [{data: []}]}
    return {
        labels: ['Diurnas', 'Nocturnas', 'Extra Diurnas', 'Extra Noct.', 'Dom. Diurno', 'Dom. Noct.'],
        datasets: [{
            data: [
                t.diurnas || 0,
                t.nocturnas || 0,
                t.extra_diurnas || 0,
                t.extra_nocturnas || 0,
                t.dominical_diurno || 0,
                t.dominical_nocturno || 0,
            ],
            backgroundColor: ['#7C6FF7', '#2ED9C3', '#FF8C42', '#FF5C7A', '#60A5FA', '#A78BFA'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    }
})

const donutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '72%',
    plugins: {
        legend: {display: false},
        tooltip: {
            backgroundColor: '#1E293B',
            titleColor: '#94A3B8',
            bodyColor: '#F1F5F9',
            padding: 12,
            cornerRadius: 8,
        }
    }
}

const donutLegend = computed(() => {
    const t = props.acumuladoTotals
    if (!t) return []
    const colors = ['#7C6FF7', '#2ED9C3', '#FF8C42', '#FF5C7A', '#60A5FA', '#000BFA']
    const items = [
        {label: 'Diurnas', key: 'diurnas'},
        {label: 'Nocturnas', key: 'nocturnas'},
        {label: 'Extra Diurnas', key: 'extra_diurnas'},
        {label: 'Extra Noct.', key: 'extra_nocturnas'},
        {label: 'Dom. Diurno', key: 'dominical_diurno'},
        {label: 'Dom. Noct.', key: 'dominical_nocturno'},
    ]
    return items.map((item, i) => ({
        ...item,
        color: colors[i],
        value: props.formatPesosCol ? props.formatPesosCol(t[item.key] || 0) : t[item.key] || 0,
        // trend: Math.floor(Math.random() * 10) - 3,
    }))
})

</script>
<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <div class="py-4 px-2 sm:px-4 lg:px-6 w-full flex flex-col min-h-0">
            <div
                class="mb-4 flex flex-col md:flex-row md:items-end justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex-1">
                    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Reporte: <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">{{
                            props.title
                        }}</span>
                    </h1>
                </div>
                <div v-if="data.hayUltimoreporte"
                     class="flex flex-wrap items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-2 rounded-xl border border-gray-100 dark:border-gray-700">
                    <v-select v-model="selectedYear" :options="yearOptions" placeholder="Año"
                              class="custom-table-select min-w-[100px]"></v-select>
                    <v-select v-model="selectedMonth" :options="monthOptions" placeholder="Mes"
                              class="custom-table-select min-w-[140px]"></v-select>
                    <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 hidden md:block self-end mb-1"></div>
                    <v-select v-model="data.params.quincena" :clearable="false"
                              :options="[{ value: 1, label: '1ra Quincena' }, { value: 2, label: '2da Quincena' }, { value: 3, label: 'Todo el Mes' }]"
                              class="custom-table-select min-w-[140px]"></v-select>
                    <PrimaryButton @click="data.isMoneyMode = !data.isMoneyMode"
                                   class="h-9 px-4 rounded-lg text-xs shadow-none">
                        <span v-if="data.isMoneyMode" class="flex items-center gap-1">Modo Dinero</span>
                        <span v-else class="flex items-center gap-1">Modo Horas</span>
                    </PrimaryButton>
                </div>
            </div>
            <!--bu14 antes de pegar claude code-->
            <!-- Main Content -->
            <div class="viz-wrapper">
                <!-- KPI Cards Row -->
                <div class="kpi-row">
                    <div class="kpi-card kpi-purple">
                        <div class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                        <div class="kpi-content">
                            <span class="kpi-label">Costo Total Acumulado</span>
                            <span class="kpi-value">{{ formatPesosCol(totalAcumulado) }}</span>
                            <span class="kpi-badge positive">↑ Acumulado</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-teal">
                        <div class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="kpi-content">
                            <span class="kpi-label">Costo Mensual</span>
                            <span class="kpi-value">{{
                                    data.isMoneyMode ? formatPesosCol(totalMesPlata) : number_format(totalMesHoras, 0) + ' hrs'
                                }}</span>
                            <span class="kpi-badge neutral">Mes actual</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-orange">
                        <div class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="kpi-content">
                            <span class="kpi-label">Colaboradores</span>
                            <span class="kpi-value">{{
                                    (data.isMoneyMode ? fromControllerPlata.data : fromControllerHoras.data)?.length ?? 0
                                }}</span>
                            <span class="kpi-badge neutral">Activos</span>
                        </div>
                    </div>
                    <div class="kpi-card kpi-red">
                        <div class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                        <div class="kpi-content">
                            <span class="kpi-label">Desde</span>
                            <span class="kpi-value kpi-value-sm">{{ firstReportDate ?? '—' }}</span>
                            <span class="kpi-badge neutral">Primer reporte</span>
                        </div>
                    </div>
                </div>



                 <div class="flex flex-col gap-6 mt-4">
                <!-- Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border flex flex-col">
                        <h3 class="text-lg font-bold p-4 border-b">Reporte Mensual</h3>
                        <div class="flex-1  overflow-auto scrollbar-table max-h-[75vh]">
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
                                <tr v-for="item in (data.isMoneyMode ? fromControllerPlata.data : fromControllerHoras.data)"
                                    :key="item.user_id">
                                    <td class="min-w-36 px-4 py-3 sticky left-0 bg-white z-10">{{ item.usera }}</td>
                                    <td class="min-w-36 px-4 py-3 text-center font-bold">
                                        {{ formatValue(item.horas_trabajadas) }}
                                    </td>
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
                                    <td class="min-w-36 px-4 py-3 text-center">{{
                                            data.isMoneyMode ? formatPesosCol(totalMesPlata) : number_format(totalMesHoras, 0)
                                        }}
                                    </td>
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
                                    <td class="min-w-36 px-4 py-3 text-center font-bold">
                                        {{ formatPesosCol(item.horas_trabajadas) }}
                                    </td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.diurnas) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.nocturnas) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.extra_diurnas) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.extra_nocturnas) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.dominical_diurno) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(item.dominical_nocturno) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{
                                            formatPesosCol(item.dominical_extra_diurno)
                                        }}
                                    </td>
                                    <td class="min-w-36 px-4 py-3">{{
                                            formatPesosCol(item.dominical_extra_nocturno)
                                        }}
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot class="sticky bottom-0 bg-amber-100">
                                <tr class="font-black text-amber-900">
                                    <td class="min-w-36 px-4 py-3 sticky left-0 bg-amber-100 z-10">TOTALES</td>
                                    <td class="min-w-36 px-4 py-3 text-center">{{ formatPesosCol(totalAcumulado) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.diurnas) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{ formatPesosCol(acumuladoTotals.nocturnas) }}</td>
                                    <td class="min-w-36 px-4 py-3">{{
                                            formatPesosCol(acumuladoTotals.extra_diurnas)
                                        }}
                                    </td>
                                    <td class="min-w-36 px-4 py-3">{{
                                            formatPesosCol(acumuladoTotals.extra_nocturnas)
                                        }}
                                    </td>
                                    <td class="min-w-36 px-4 py-3">{{
                                            formatPesosCol(acumuladoTotals.dominical_diurno)
                                        }}
                                    </td>
                                    <td class="min-w-36 px-4 py-3">{{
                                            formatPesosCol(acumuladoTotals.dominical_nocturno)
                                        }}
                                    </td>
                                    <td class="min-w-36 px-4 py-3">
                                        {{ formatPesosCol(acumuladoTotals.dominical_extra_diurno) }}
                                    </td>
                                    <td class="min-w-36 px-4 py-3">
                                        {{ formatPesosCol(acumuladoTotals.dominical_extra_nocturno) }}
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


                <!-- Charts Row -->
                <div class="charts-row">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div>
                                <h3 class="chart-title">Costos Acumulados</h3>
                                <p class="chart-subtitle">Por colaborador</p>
                            </div>
                            <div class="chart-legend">
                                <span class="legend-dot legend-purple"></span><span>Costo</span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <Bar :data="chartData" :options="barOptions"/>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <div>
                                <h3 class="chart-title">Tendencia Mensual</h3>
                                <p class="chart-subtitle">Evolución de costos</p>
                            </div>
                            <div class="chart-legend">
                                <span class="legend-dot legend-teal"></span><span>Tendencia</span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <Line :data="monthlyTrendData" :options="lineOptions"/>
                        </div>
                    </div>

                    <div class="chart-card chart-card-donut max-h-[35vh] overflow-y-auto">
                        <div class="chart-header">
                            <div>
                                <h3 class="chart-title">Distribución Mensual</h3>
                                <p class="chart-subtitle">Horas por tipo</p>
                            </div>
                        </div>
                        <div class="donut-body">
                            <div class="donut-chart-wrap">
                                <Doughnut :data="donutData" :options="donutOptions"/>
                            </div>
                            <div class="donut-legend">
                                <div v-for="(item, i) in donutLegend" :key="i" class="donut-legend-item">
                                    <span class="donut-dot" :style="{ background: item.color }"></span>
                                    <span class="donut-legend-label">{{ item.label }}</span>
                                    <span class="donut-legend-value">{{ formatPesosCol(item.value) }}</span>
<!--                                    <span class="donut-badge" :class="item.trend > 0 ? 'positive' : 'negative'">-->
<!--                                        {{ item.trend > 0 ? '+' : '' }}{{ item.trend }}%-->
<!--                                    </span>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




        </div>
    </AuthenticatedLayout>
</template>
<style>
.scrollbar-table::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.scrollbar-table::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-table::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.dark .scrollbar-table::-webkit-scrollbar-thumb {
    background: #334155;
}

.custom-table-select .vs__dropdown-toggle {
    border-radius: 0.5rem;
    height: 2.25rem;
    border-color: #e5e7eb;
    background: white;
    font-size: 0.75rem;
    font-weight: 600;
}

.dark .custom-table-select .vs__dropdown-toggle {
    background: #111827;
    border-color: #374151;
}

.dark .custom-table-select .vs__selected {
    color: #f9fafb;
}

.dark .custom-table-select .vs__actions svg {
    fill: #9ca3af;
}

.viz-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    font-family: 'DM Sans', 'Nunito', sans-serif;
}

/* KPI Cards */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.kpi-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-radius: 1rem;
    background: #fff;
    border: 1px solid #F1F5F9;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s, box-shadow 0.2s;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.kpi-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.kpi-icon svg {
    width: 1.25rem;
    height: 1.25rem;
}

.kpi-purple .kpi-icon {
    background: rgba(124, 111, 247, 0.12);
    color: #7C6FF7;
}

.kpi-teal .kpi-icon {
    background: rgba(46, 217, 195, 0.12);
    color: #2ED9C3;
}

.kpi-orange .kpi-icon {
    background: rgba(255, 140, 66, 0.12);
    color: #FF8C42;
}

.kpi-red .kpi-icon {
    background: rgba(255, 92, 122, 0.12);
    color: #FF5C7A;
}

.kpi-content {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}

.kpi-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.kpi-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1E293B;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.kpi-value-sm {
    font-size: 1rem;
}

.kpi-badge {
    font-size: 0.65rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    width: fit-content;
}

.kpi-badge.positive {
    background: rgba(46, 217, 195, 0.12);
    color: #2ED9C3;
}

.kpi-badge.neutral {
    background: rgba(148, 163, 184, 0.12);
    color: #94A3B8;
}

/* Charts */
.charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
}

.chart-card {
    background: #fff;
    border: 1px solid #F1F5F9;
    border-radius: 1rem;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.chart-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1E293B;
    margin: 0;
}

.chart-subtitle {
    font-size: 0.72rem;
    color: #94A3B8;
    margin: 0.15rem 0 0;
}

.chart-legend {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    color: #94A3B8;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.legend-purple {
    background: #7C6FF7;
}

.legend-teal {
    background: #2ED9C3;
}

.chart-body {
    height: 220px;
    position: relative;
}

/* Donut */
.chart-card-donut {
}

.donut-body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.donut-chart-wrap {
    height: 100px;
    position: relative;
    max-width: 100px;
    margin: 0 auto;
}

.donut-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.donut-legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
}

.donut-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.donut-legend-label {
    flex: 1;
    color: #64748B;
    font-weight: 500;
}

.donut-legend-value {
    font-weight: 600;
    color: #1E293B;
}

.donut-badge {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.1rem 0.4rem;
    border-radius: 999px;
}

.donut-badge.positive {
    background: rgba(46, 217, 195, 0.12);
    color: #2ED9C3;
}

.donut-badge.negative {
    background: rgba(255, 92, 122, 0.12);
    color: #FF5C7A;
}


@media (max-width: 1280px) {
    .kpi-row {
        grid-template-columns: repeat(2, 1fr);
    }

    .charts-row {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .kpi-row {
        grid-template-columns: 1fr;
    }

    .charts-row {
        grid-template-columns: 1fr;
    }
}
</style>
