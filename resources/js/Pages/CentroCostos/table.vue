<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onMounted, reactive, watch, watchEffect } from 'vue';
import pkg from 'lodash';
import { Head, router, usePage, Link } from '@inertiajs/vue3';
import { ChevronUpDownIcon, XMarkIcon, EyeIcon, DocumentIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { formatPesosCol, number_format } from '@/global.ts';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import PrimaryButton from "@/Components/PrimaryButton.vue";


const { _, debounce, pickBy } = pkg
const props = defineProps({
    elIDD: Number,
    title: String,
    filters: Object,
    fromController: Object,
    nombresTabla: Object,
    UltimoReporteRealizado: String,
    perPage: Number,
})

const data = reactive({
    params: {
        fecha_ini: props?.filters?.fecha_ini,
        quincena: props?.filters?.quincena,
        plata: props?.filters?.plata,
    },
    hayUltimoreporte: true,
    elID: props.elIDD,
    selectedId: [],
    dataSet: usePage().props.app.perpage,
    thoras_trabajadas: 0,
    tdiurnas: 0,
    tnocturnas: 0,
    textra_diurnas: 0,
    textra_nocturnas: 0,
    tdominical_diurno: 0,
    tdominical_nocturno: 0,
    tdominical_extra_diurno: 0,
    tdominical_extra_nocturno: 0,
})

const sumarLoQueSea = () => {
    if (data.params?.plata)
        SumarPlata()
    else
        aSumarHoras(0)
}


const verificarExistenciaUltimoReporte = () => {
    if (props.UltimoReporteRealizado === '_') data.hayUltimoreporte = false
}
onMounted(() => {
    verificarExistenciaUltimoReporte()
    sumarLoQueSea()
    if (!data.params.quincena) data.params.quincena = 1

    const today = new Date();
    if (typeof data.params.fecha_ini === "undefined") {
        data.params.fecha_ini = reactive({
            month: today.getMonth(),
            year: today.getFullYear(),
        })
    }
});

const AlternarPlata = () => {
    data.params.plata = !data.params.plata
}

const order = (field) => {
    if (field !== undefined) {
        data.params.field = field.replace(/ /g, "_")
        data.params.order = data.params.order === "asc" ? "desc" : "asc"
    }
}

const aSumarHoras = (veces) => { //suma horas, no dinero
    data.thoras_trabajadas = 0
    data.tdiurnas = 0
    data.tnocturnas = 0
    data.textra_diurnas = 0
    data.textra_nocturnas = 0
    data.tdominical_diurno = 0
    data.tdominical_nocturno = 0
    data.tdominical_extra_diurno = 0
    data.tdominical_extra_nocturno = 0
    veces++
    setTimeout(() => {
        if (props.fromController.data) {
            props.fromController.data.forEach(element => {
                data.thoras_trabajadas += parseInt(element.horas_trabajadas)

                data.tdiurnas += element.diurnas ? parseInt(element.diurnas) : 0
                data.tnocturnas += element.nocturnas ? parseInt(element.nocturnas) : 0
                data.textra_diurnas += element.extra_diurnas ? parseInt(element.extra_diurnas) : 0
                data.textra_nocturnas += element.extra_nocturnas ? parseInt(element.extra_nocturnas) : 0
                data.tdominical_diurno += element.dominical_diurno ? parseInt(element.dominical_diurno) : 0
                data.tdominical_nocturno += element.dominical_nocturno ? parseInt(element.dominical_nocturno) : 0
                data.tdominical_extra_diurno += element.dominical_extra_diurno ? parseInt(element.dominical_extra_diurno) : 0
                data.tdominical_extra_nocturno += element.dominical_extra_nocturno ? parseInt(element.dominical_extra_nocturno) : 0
            });
        } else {
            aSumarHoras(veces)
        }
    }, 500)
}
const SumarPlata = () => { ////suma dinero, no horas
    let datathoras_trabajadas = 0
    let datatdiurnas = 0
    let datatnocturnas = 0
    let datatextra_diurnas = 0
    let datatextra_nocturnas = 0
    let datatdominical_diurno = 0
    let datatdominical_nocturno = 0
    let datatdominical_extra_diurno = 0
    let datatdominical_extra_nocturno = 0

    setTimeout(() => {
        if (props.fromController.data) {
            props.fromController.data.forEach(element => {
                datathoras_trabajadas += parseInt(element.horas_trabajadas)
                datatdiurnas += parseInt(element.diurnas)
                datatnocturnas += parseInt(element.nocturnas)
                datatextra_diurnas += parseInt(element.extra_diurnas)
                datatextra_nocturnas += parseInt(element.extra_nocturnas)
                datatdominical_diurno += parseInt(element.dominical_diurno)
                datatdominical_nocturno += parseInt(element.dominical_nocturno)
                datatdominical_extra_diurno += parseInt(element.dominical_extra_diurno)
                datatdominical_extra_nocturno += parseInt(element.dominical_extra_nocturno)
            });
            data.thoras_trabajadas = formatPesosCol(datathoras_trabajadas)
            data.tdiurnas = formatPesosCol(datatdiurnas)
            data.tnocturnas = formatPesosCol(datatnocturnas)
            data.textra_diurnas = formatPesosCol(datatextra_diurnas)
            data.textra_nocturnas = formatPesosCol(datatextra_nocturnas)
            data.tdominical_diurno = formatPesosCol(datatdominical_diurno)
            data.tdominical_nocturno = formatPesosCol(datatdominical_nocturno)
            data.tdominical_extra_diurno = formatPesosCol(datatdominical_extra_diurno)
            data.tdominical_extra_nocturno = formatPesosCol(datatdominical_extra_nocturno)
        } else {
            SumarPlata()
        }
    }, 300)
}
watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("CentroCostos.table", data.elID), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            sumarLoQueSea()
        },
    })
}, 250))
watchEffect(() => {
})
</script>
<template>

    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <div class="py-4 px-2 sm:px-4 lg:px-6 h-[calc(100vh-64px)] flex flex-col">
            <!-- Header & Action Bar -->
            <div
                class="mb-4 flex flex-col md:flex-row md:items-end justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <Link :href="route('CentroCostos.index')"
                            class="text-amber-600 hover:text-amber-700 transition-colors">
                            <span class="text-xs font-bold uppercase tracking-widest flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Volver
                            </span>
                        </Link>
                    </div>
                    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Reporte: <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">{{
                                props.title }}</span>
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium italic mt-0.5">
                        {{ props.UltimoReporteRealizado }}
                    </p>
                </div>

                <!-- Unified Filter Bar -->
                <div v-if="data.hayUltimoreporte"
                    class="flex flex-wrap items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-2 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 mb-1">Visualización</label>
                        <PrimaryButton @click="AlternarPlata" class="h-9 px-4 rounded-lg text-xs shadow-none">
                            <span v-if="data.params.plata" class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                    <path fill-rule="evenodd"
                                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Modo Dinero
                            </span>
                            <span v-else class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                Modo Horas
                            </span>
                        </PrimaryButton>
                    </div>

                    <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 hidden md:block self-end mb-1"></div>

                    <div class="flex flex-col min-w-[140px]">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 mb-1">Quincena</label>
                        <v-select v-model="data.params.quincena" :clearable="false"
                            :options="[{ value: 1, label: '1ra Quincena' }, { value: 2, label: '2da Quincena' }, { value: 3, label: 'Todo el Mes' }]"
                            class="custom-table-select"></v-select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 mb-1">Mes Seleccionado</label>
                        <VueDatePicker month-picker auto-apply :teleport="true" v-model="data.params.fecha_ini"
                            class="custom-datepicker-table" />
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div v-if="!data.hayUltimoreporte"
                class="flex-1 flex flex-col items-center justify-center bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="p-8 text-center">
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full inline-block mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 17.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">No se encontró información</h2>
                    <p class="text-gray-500 max-w-xs mx-auto mt-2">No hay reportes generados para este centro de costo
                        en el periodo seleccionado.</p>
                </div>
            </div>

            <div v-else
                class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 flex flex-col overflow-hidden">
                <div class="flex-1 overflow-auto scrollbar-table relative">
                    <table class="w-full text-left border-collapse min-w-[1200px]">
                        <thead class="sticky top-0 z-20 bg-gray-50 dark:bg-gray-900 shadow-sm">
                            <tr
                                class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                <th
                                    class="px-4 py-4 sticky left-0 z-30 bg-gray-50 dark:bg-gray-900 min-w-[200px] border-r border-gray-100 dark:border-gray-700">
                                    Empleado</th>
                                <th class="px-4 py-4 text-center">Total</th>
                                <th class="px-4 py-4">Diurnas</th>
                                <th class="px-4 py-4">Nocturnas</th>
                                <th class="px-4 py-4 whitespace-nowrap">Ext. Diurnas</th>
                                <th class="px-4 py-4 whitespace-nowrap">Ext. Nocturnas</th>
                                <th class="px-4 py-4 whitespace-nowrap">Dom. Diurno</th>
                                <th class="px-4 py-4 whitespace-nowrap">Dom. Nocturno</th>
                                <th class="px-4 py-4 whitespace-nowrap">Dom. Ext. D.</th>
                                <th class="px-4 py-4 whitespace-nowrap">Dom. Ext. N.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="hover:bg-amber-50/20 dark:hover:bg-amber-900/5 transition-colors group">
                                <td
                                    class="px-4 py-3 sticky left-0 z-10 bg-white dark:bg-gray-800 border-r border-gray-50 dark:border-gray-700 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                    <span
                                        class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-amber-600 transition-colors uppercase">
                                        {{ clasegenerica.usera }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-center text-xs font-black text-amber-600 dark:text-amber-400 bg-amber-50/10">
                                    {{ number_format(clasegenerica.horas_trabajadas, 0, 0) }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.diurnas, 0, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.nocturnas, 0, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.extra_diurnas, 0, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.extra_nocturnas, 0, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.dominical_diurno, 0, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.dominical_nocturno, 0, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.dominical_extra_diurno, 0, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{
                                    number_format(clasegenerica.dominical_extra_nocturno, 0, 0) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="sticky bottom-0 z-20">
                            <tr
                                class="bg-amber-100 dark:bg-amber-900 shadow-[0_-4px_10px_rgba(0,0,0,0.05)] border-t border-amber-200 dark:border-amber-800">
                                <td
                                    class="px-4 py-4 sticky left-0 z-30 bg-amber-100 dark:bg-amber-900 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-200 dark:border-amber-800 uppercase">
                                    TOTALES DEL PERIODO
                                </td>
                                <td
                                    class="px-4 py-4 text-center text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-200 dark:border-amber-800">
                                    {{ data.thoras_trabajadas }}</td>
                                <td
                                    class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-100 dark:border-amber-800">
                                    {{ data.tdiurnas }}</td>
                                <td
                                    class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-100 dark:border-amber-800">
                                    {{ data.tnocturnas }}</td>
                                <td
                                    class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-100 dark:border-amber-800">
                                    {{ data.textra_diurnas }}</td>
                                <td
                                    class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-100 dark:border-amber-800">
                                    {{ data.textra_nocturnas }}</td>
                                <td
                                    class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-100 dark:border-amber-800">
                                    {{ data.tdominical_diurno }}</td>
                                <td
                                    class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-100 dark:border-amber-800">
                                    {{ data.tdominical_nocturno }}</td>
                                <td
                                    class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100 border-r border-amber-100 dark:border-amber-800">
                                    {{ data.tdominical_extra_diurno }}</td>
                                <td class="px-4 py-4 text-xs font-black text-amber-900 dark:text-amber-100">{{
                                    data.tdominical_extra_nocturno }}</td>
                            </tr>
                        </tfoot>
                    </table>
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

.custom-datepicker-table {
    --dp-font-size: 0.75rem;
    --dp-border-radius: 0.5rem;
    --dp-input-padding: 6px 12px;
}

.dark .custom-datepicker-table {
    --dp-background-color: #111827;
    --dp-text-color: #f9fafb;
    --dp-border-color: #374151;
}
</style>
