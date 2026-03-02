<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch, computed} from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, PencilIcon, TrashIcon} from '@heroicons/vue/24/solid';
import Create from '@/Pages/desarrollo/Create.vue';
import Edit from '@/Pages/desarrollo/Edit.vue';
import Delete from '@/Pages/desarrollo/Delete.vue';
import Pagodllo from '@/Pages/desarrollo/Pagodllo.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';

import {formatDate, formatPesosCol} from '@/global.ts';


const {_, debounce, pickBy} = pkg
const props = defineProps({
    fromController: Object,
    total: Number,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,
    saldodllo: Number,

    title: String,

    numberPermissions: Number,
    losSelect: Object,//normally used by headlessui
})

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    desarrolloo: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    edit2Open: false,
    deleteOpen: false,
    // deleteBulkOpen: false,
    dataSet: usePage().props.app.perpage,
})

// <!--<editor-fold desc="order, watchclone, select">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("desarrollo.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((desarrollo) => {
            data.selectedId.push(desarrollo.id)
        })
    }
}
const select = () => data.multipleSelect = props.fromController?.data.length === data.selectedId.length;

// <!--</editor-fold>-->


// const form = useForm({ })
// watchEffect(() => { })


// text - string // number // dinero // date // datetime // foreign
const titulos = [
    {order: 'nombre', label: 'nombre', type: 'string'},
    {order: 'descripcion', label: 'descripcion', type: 'string'},
    {order: 'valor_inicial', label: 'valor_inicial', type: 'integer'},
    {order: 'valor_parcial1', label: 'valor_parcial1', type: 'integer'},

    {order: 'fecha_reunion', label: 'fecha_reunion', type: 'date'},
    {order: 'fecha_cotizacion', label: 'fecha_cotizacion', type: 'date'},
    {order: 'fecha_cotizacion_aceptada', label: 'fecha_cotizacion_aceptada', type: 'date'},
    {order: 'estado', label: 'estado', type: 'string'},
];
const estados = [
    'Cotizando',
    'Desarrollando',
    'Esperando pago parcial',
    'Pagada totalmente',
    'Finalizada'
];

function obtenerIndice(estado) {
    const index = estados.indexOf(estado);
    return index !== -1 ? index + 1 : -1; // Retorna -1 si no se encuentra
}

const itemsFiltrados = computed(() => {
    return props.fromController.data.filter(item =>
        item.estado !== 'Finalizada' &&
        item.estado !== 'Pagada totalmente'
    )
})
const soloFinalizadas = computed(() => {
    return props.fromController.data.filter(item =>
        item.estado === 'Finalizada' ||
        item.estado === 'Pagada totalmente'
    )
})

</script>

<template>
    <Head :title="props.title"/>

    <AuthenticatedLayout>
        <!-- Header Section -->
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="px-4 mt-6 sm:px-0">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold">Gestión de Requisitos</h1>
                            <p class="text-indigo-100 mt-2">Sistema de seguimiento de desarrollo y cotizaciones</p>
                        </div>
                        <PrimaryButton @click="data.createOpen = true" v-if="can(['isAdmin'])"
                                      class="bg-indigo-200 text-indigo-600 hover:bg-indigo-50 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Nuevo Requisito
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="px-4 sm:px-0">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pendiente por Cobrar</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatPesosCol(props.saldodllo) }}</p>
                            </div>
                            <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Requisitos</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.fromController?.data?.length || 0 }}</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Flujo de Trabajo</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ estados.join(' → ') }}</p>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <Create v-if="can(['isSuper'])" :numberPermissions="props.numberPermissions"
                            :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false"
                            :title="props.title"
                            :losSelect=props.losSelect />

                    <Edit v-if="can(['isAdmin'])" :titulos="titulos"
                          :numberPermissions="props.numberPermissions" :show="data.editOpen"
                          @close="data.editOpen = false"
                          :desarrolloa="data.desarrolloo" :title="props.title" :losSelect=props.losSelect />
                    <Pagodllo v-if="can(['isSuper'])" :titulos="titulos"
                              :numberPermissions="props.numberPermissions" :show="data.edit2Open"
                              @close="data.edit2Open = false"
                              :desarrolloa="data.desarrolloo" :title="props.title"
                              :losSelect=props.losSelect
                    />

                    <Delete v-if="can(['isAdmin'])" :numberPermissions="props.numberPermissions"
                            :show="data.deleteOpen" @close="data.deleteOpen = false" :desarrolloa="data.desarrolloo"
                            :title="props.title"
                    />
                </div>
            </div>
            <!-- Requirements List -->
            <div class="px-4 sm:px-0">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Lista de Requisitos</h2>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div
                             v-for="(item, index) in itemsFiltrados" :key="index"
                             class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div v-if="item.estado !== 'Finalizada' && item.estado !=='Pagada totalmente'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <!-- Project Info -->
                                <div class="lg:col-span-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                                {{ obtenerIndice(item.estado) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ item.nombre }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Reunión: {{ item.fecha_reunion }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ item.HaceCuanto }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Description -->
                                <div class="lg:col-span-5">
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': item.estado === 'Cotizando',
                                                  'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': item.estado === 'Desarrollando',
                                                  'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400': item.estado === 'Esperando pago parcial',
                                                  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': item.estado === 'Pagada totalmente',
                                                  'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400': item.estado === 'Finalizada'
                                              }">
                                            {{ item.estado }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed line-clamp-3">{{ item.descripcion }}</p>
                                    <div class="mt-3 space-y-1">
                                        <a class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium cursor-pointer inline-flex items-center"
                                           @click="data.editOpen = true; data.desarrolloo = item">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Cotización: {{ item.fecha_cotizacion }}
                                        </a>
                                        <p v-if="item.fecha_cotizacion_aceptada"
                                           class="text-green-600 dark:text-green-400 text-sm font-medium">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Aceptada: {{ item.fecha_cotizacion_aceptada }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Financial Info & Actions -->
                                <div class="lg:col-span-3">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Valor Inicial:</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ formatPesosCol(item.valor_inicial) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Primer Pago:</span>
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ formatPesosCol(item.valor_parcial1) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Pagado:</span>
                                            <span class="font-semibold text-green-600 dark:text-green-400">{{ formatPesosCol(item.totalpagado) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-600">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Deuda:</span>
                                            <span class="font-bold text-red-600 dark:text-red-400">{{ formatPesosCol(item.Deudau) }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button @click="data.edit2Open = true; data.desarrolloo = item"
                                                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            Registrar Pago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-16 mb-5">
                     <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Finalizados</h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="(item, index) in soloFinalizadas" :key="index"
                             class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <!-- Project Info -->
                                <div class="lg:col-span-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                                {{ obtenerIndice(item.estado) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ item.nombre }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Reunión: {{ item.fecha_reunion }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ item.HaceCuanto }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Description -->
                                <div class="lg:col-span-5">
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': item.estado === 'Cotizando',
                                                  'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': item.estado === 'Desarrollando',
                                                  'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400': item.estado === 'Esperando pago parcial',
                                                  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': item.estado === 'Pagada totalmente',
                                                  'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400': item.estado === 'Finalizada'
                                              }">
                                            {{ item.estado }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed line-clamp-3">{{ item.descripcion }}</p>
                                    <div class="mt-3 space-y-1">
                                        <a class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium cursor-pointer inline-flex items-center"
                                           @click="data.editOpen = true; data.desarrolloo = item">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Cotización: {{ item.fecha_cotizacion }}
                                        </a>
                                        <p v-if="item.fecha_cotizacion_aceptada"
                                           class="text-green-600 dark:text-green-400 text-sm font-medium">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Aceptada: {{ item.fecha_cotizacion_aceptada }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Financial Info & Actions -->
                                <div class="lg:col-span-3">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Valor Inicial:</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ formatPesosCol(item.valor_inicial) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Primer Pago:</span>
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ formatPesosCol(item.valor_parcial1) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Pagado:</span>
                                            <span class="font-semibold text-green-600 dark:text-green-400">{{ formatPesosCol(item.totalpagado) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-600">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Deuda:</span>
                                            <span class="font-bold text-red-600 dark:text-red-400">{{ formatPesosCol(item.Deudau) }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button @click="data.edit2Open = true; data.desarrolloo = item"
                                                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            Registrar Pago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!props.fromController?.data?.length" class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay requisitos</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando un nuevo requisito de desarrollo.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
