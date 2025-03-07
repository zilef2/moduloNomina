<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch} from 'vue';

import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';

import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, PencilIcon, TrashIcon} from '@heroicons/vue/24/solid';
// import { CursorArrowRippleIcon, ChevronUpDownIcon,QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, UserGroupIcon } from '@heroicons/vue/24/solid';
import Create from '@/Pages/desarrollo/Create.vue';
import Edit from '@/Pages/desarrollo/Edit.vue';
import Delete from '@/Pages/desarrollo/Delete.vue';
import Pagodllo from '@/Pages/desarrollo/Pagodllo.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';

import {formatDate, number_format,formatPesosCol} from '@/global.ts';



const {_, debounce, pickBy} = pkg
const props = defineProps({
    fromController: Object,
    total: Number,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

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

</script>

<template>
    <Head :title="props.title"/>

    <AuthenticatedLayout>
<!--        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>-->
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg my-2 overflow-hidden w-fit">
<!--                    create desarrollo-->
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true" v-if="can(['isAdmin'])">
                        {{ lang().button.new }} requisito
                    </PrimaryButton>
                    <p class="text-lg my-2">
                        Cada cotizacion pasa por los siguientes pasos: {{ estados.join(', ') }}
                    </p>

                    <Create v-if="can(['isSuper'])" :numberPermissions="props.numberPermissions"
                            :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                            :losSelect=props.losSelect />

                    <Edit v-if="can(['isAdmin'])" :titulos="titulos"
                          :numberPermissions="props.numberPermissions" :show="data.editOpen" @close="data.editOpen = false"
                          :desarrolloa="data.desarrolloo" :title="props.title" :losSelect=props.losSelect />
                    <Pagodllo v-if="can(['isSuper'])" :titulos="titulos"
                          :numberPermissions="props.numberPermissions" :show="data.edit2Open" @close="data.edit2Open = false"
                          :desarrolloa="data.desarrolloo" :title="props.title" 
                          :losSelect=props.losSelect 
                    />

                    <Delete v-if="can(['isAdmin'])" :numberPermissions="props.numberPermissions"
                            :show="data.deleteOpen" @close="data.deleteOpen = false" :desarrolloa="data.desarrolloo"
                            :title="props.title"/>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
<!--                <div class="flex justify-between p-2">-->
<!--                    <div class="flex space-x-2">-->
<!--                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"/>-->
                        <!-- <DangerButton @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length != 0 && can(['delete desarrollo'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton> -->
<!--                    </div>-->
<!--                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"-->
<!--                               class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Nombre, codigo"/>-->
<!--                </div>-->
                <section class="text-gray-600 body-font overflow-hidden">
                    <div class="container px-5 py-24 mx-auto">
                        <div class="my-2 divide-y-2 divide-gray-100 rounded-2xl">
                            <div v-for="(claseFromController, indexu) in props.fromController.data" :key="indexu" 
                                class="py-8 flex flex-wrap md:flex-nowrap hover:bg-blue-100">
                                <div class="md:w-64 md:mb-0 mb-6 flex-shrink-0 flex flex-col">
                                    <span class="font-semibold title-font text-gray-700">{{ claseFromController.nombre }}</span>
                                    <span class="mt-1 text-gray-500 text-sm">Reunión: {{claseFromController.fecha_reunion}}</span>
                                </div>
                                <div class="w-1/2">
                                    <h2 class="text-2xl font-medium text-gray-900 title-font mb-2">
                                        <b>Etapa {{obtenerIndice(claseFromController.estado)}}: {{ claseFromController.estado }}</b>
                                    </h2>
                                    <p class="leading-relaxed">{{claseFromController.descripcion}}</p>
                                    <a class="text-indigo-500 inline-flex items-center mt-4"
                                    @click="(data.editOpen = true), (data.desarrolloo = claseFromController)">
                                        Cotizacion enviada el: {{claseFromController.fecha_cotizacion}}
                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="md:flex-grow">
                                    <p class="leading-relaxed">Valor inicial: {{formatPesosCol(claseFromController.valor_inicial)}}</p>
                                    <p class="leading-relaxed">{{formatPesosCol(claseFromController.valor_inicial)}}</p>
                                    <p class="leading-relaxed">Primer pago acordado: <b>{{ formatPesosCol(claseFromController.valor_parcial1) }}</b></p>
                                    <p class="leading-relaxed"> Pagos: {{claseFromController.valorino}}</p>
                                    <p class="leading-relaxed"> Total: {{claseFromController.totalpagado}}</p>
                                    <p v-if="claseFromController.fecha_cotizacion_aceptada" class="text-indigo-500 inline-flex items-center mt-4">
                                        Cotizacion aceptada el: {{claseFromController.fecha_cotizacion_aceptada}}
                                    </p>
                                    <a class="text-indigo-500 inline-flex items-center mt-4"
                                    @click="(data.edit2Open = true), (data.desarrolloo = claseFromController)">
                                        Ingreso un pago
                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </section>
<!--                <div v-if="props.total > 0"-->
<!--                     class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">-->
<!--                    <Pagination :links="props.fromController" :filters="data.params"/>-->
<!--                </div>-->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
