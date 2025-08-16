<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, router, usePage} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch,onMounted} from 'vue';

import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';

import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, PencilIcon, TrashIcon, LockClosedIcon, RocketLaunchIcon} from '@heroicons/vue/24/solid';
import Create from '@/Pages/cotizacion/Create.vue';
import Edit from '@/Pages/cotizacion/Edit.vue';
import Facturar from '@/Pages/cotizacion/Facturar.vue';
import Delete from '@/Pages/cotizacion/Delete.vue';
import DeleteBulk from '@/Pages/cotizacion/DeleteBulk.vue';
import Generarcc from '@/Pages/cotizacion/Generarcc.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import {Now_Date_to_html, formatDateToHuman, formatDateTimeToHuman, number_format} from '@/global.ts';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";



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
    CentrosRepetidos: Array,
    consecutivoCotizacion: Number,
    cotizacionInicial2: Number,
})


const data = reactive({
    params: {
        search: props.filters.search,
        search2: props.filters.search2, //zona
        search3: props.filters.search3,
        search4: props.filters.search4,
        // search5: props.filters.search5, //zonaid
        search6: props.filters.search6, //tipo
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    ocultar1: false,
    cotizaciono: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    generarOpen: false,
    edit3Open: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    dataSet: usePage().props.app.perpage,
})

onMounted(() => {
    if(!data.params.search2){
        data.params.search2 = props.losSelect['zonas'][0]
    }
})
// <!--<editor-fold desc="order, watchclone, select">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("cotizacion.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((cotizacion) => {
            data.selectedId.push(cotizacion.id)
        })
    }
}

const select = () => data.multipleSelect = props.fromController?.data.length === data.selectedId.length;
// <!--</editor-fold>-->


// const form = useForm({ })
// watchEffect(() => {})

// text // number - porcentaje - dinero // date // datetime // foreign //list?
let titulos = [
    // { order: 'codigo', label: 'codigo', type: 'text' },
    
    {order: 'numero_cot', label: 'numero_cot', type: 'text'},
    {order: 'centro_costo_id', label: 'centro_costo', type: 'id', nameid: 'nombre'},
    {order: 'Zouna', label: 'zona', type: 'text'},
    {order: 'estado_cliente', label: 'estado_cliente', type: 'text'},
    {order: 'estado', label: 'estado', type: 'text'},
    {order: 'factura', label: 'factura', type: 'text'},
    {order: 'fecha_factura', label: 'fecha_factura', type: 'date'},
    {order: 'fecha_solicitud', label: 'fecha_solicitud', type: 'date'},
    {order: 'fecha_aprobacion_cot', label: 'fecha_aprobacion_cot', type: 'date'},
    {order: 'mes_pedido', label: 'mes_pedido', type: 'text'},
    {order: 'lugar', label: 'lugar', type: 'text'},
    {order: 'descripcion_cot', label: 'descripcion_cot', type: 'text'},
    {order: 'tipo', label: 'tipo', type: 'text'},
    // {order: 'tipo_de_mantenimiento', label: 'tipo_de_mantenimiento', type: 'text'},
    
    {order: 'precio_cot', label: 'precio_cot', type: 'number'},
    {order: 'por_a', label: 'por_a', type: 'porcentaje'},
    {order: 'por_i', label: 'por_i', type: 'porcentaje'},
    {order: 'por_u', label: 'por_u', type: 'porcentaje'},
    {order: 'admi', label: 'admi', type: 'dinero'},
    {order: 'impr', label: 'impr', type: 'dinero'},
    {order: 'util', label: 'util', type: 'dinero'},
    {order: 'subtotal', label: 'subtotal', type: 'dinero'},
    {order: 'iva', label: 'iva', type: 'dinero'},
    {order: 'total', label: 'total', type: 'dinero'},
    {order: 'Prealiza', label: 'persona_que_realiza_la_pe', type: 'text'},
    {order: 'cliente', label: 'cliente', type: 'text'},
    {
        order: 'persona_que_solicita_la_propuesta_economica',
        label: 'persona_que_solicita_la_propuesta_economica',
        type: 'text'
    },
    {order: 'orden_de_compra', label: 'orden_de_compra', type: 'text'},
    {order: 'hes', label: 'hes', type: 'text'},
    {order: 'observaciones', label: 'observaciones', type: 'text'},
];


const ocultar11 = () => {
    if (data.ocultar1) { //aqui se ocultan varias columnas como por_a
        titulos = [
            {order: 'numero_cot', label: 'numero_cot', type: 'text'},
            {order: 'centro_costo_id', label: 'centro_costo', type: 'id', nameid: 'nombre'},
            {order: 'Zouna', label: 'zona', type: 'text'},
            {order: 'estado_cliente', label: 'estado_cliente', type: 'text'},
            {order: 'estado', label: 'estado', type: 'text'},
            {order: 'factura', label: 'factura', type: 'text'},
            {order: 'fecha_factura', label: 'fecha_factura', type: 'date'},
            {order: 'fecha_solicitud', label: 'fecha_solicitud', type: 'date'},
            {order: 'fecha_aprobacion_cot', label: 'fecha_aprobacion_cot', type: 'date'},
            {order: 'mes_pedido', label: 'mes_pedido', type: 'text'},
            {order: 'lugar', label: 'lugar', type: 'text'},
            // {order: 'descripcion_cot', label: 'descripcion_cot', type: 'text'},
            // {order: 'tipo', label: 'tipo', type: 'text'},
            {order: 'precio_cot', label: 'precio_cot', type: 'number'},
            {order: 'admi', label: 'admi', type: 'number'},
            {order: 'impr', label: 'impr', type: 'number'},
            {order: 'util', label: 'util', type: 'number'},
            {order: 'subtotal', label: 'subtotal', type: 'dinero'},
            {order: 'iva', label: 'iva', type: 'dinero'},
            {order: 'total', label: 'total', type: 'dinero'},
            {order: 'Prealiza', label: 'Prealiza', type: 'text'},
            {order: 'cliente', label: 'cliente', type: 'text'},
            {
                order: 'persona_que_solicita_la_propuesta_economica',
                label: 'persona_que_solicita_la_propuesta_economica',
                type: 'text'
            },
            {order: 'orden_de_compra', label: 'orden_de_compra', type: 'text'},
            {order: 'hes', label: 'hes', type: 'text'},
            {order: 'observaciones', label: 'observaciones', type: 'text'},
        ];
    } else {
        titulos = [
            {order: 'numero_cot', label: 'numero_cot', type: 'text'},
            {order: 'centro_costo_id', label: 'centro_costo', type: 'id', nameid: 'nombre'},
            {order: 'Zouna', label: 'zona', type: 'text'},
            {order: 'estado_cliente', label: 'estado_cliente', type: 'text'},
            {order: 'estado', label: 'estado', type: 'text'},
            {order: 'factura', label: 'factura', type: 'text'},
            {order: 'fecha_factura', label: 'fecha_factura', type: 'date'},
            {order: 'fecha_solicitud', label: 'fecha_solicitud', type: 'date'},
            {order: 'fecha_aprobacion_cot', label: 'fecha_aprobacion_cot', type: 'date'},
            {order: 'mes_pedido', label: 'mes_pedido', type: 'text'},
            {order: 'lugar', label: 'lugar', type: 'text'},
            {order: 'descripcion_cot', label: 'descripcion_cot', type: 'text'},
            {order: 'tipo', label: 'tipo', type: 'text123'},
            // {order: 'tipo_de_mantenimiento', label: 'tipo_de_mantenimiento', type: 'text'},
            {order: 'precio_cot', label: 'precio_cot', type: 'number'},
            {order: 'por_a', label: 'por_a', type: 'number'},
            {order: 'por_i', label: 'por_i', type: 'number'},
            {order: 'por_u', label: 'por_u', type: 'number'},
            {order: 'admi', label: 'admi', type: 'number'},
            {order: 'impr', label: 'impr', type: 'number'},
            {order: 'util', label: 'util', type: 'number'},
            {order: 'subtotal', label: 'subtotal', type: 'dinero'},
            {order: 'iva', label: 'iva', type: 'dinero'},
            {order: 'total', label: 'total', type: 'dinero'},
            {order: 'Prealiza', label: 'Prealiza', type: 'text'},
            {order: 'cliente', label: 'cliente', type: 'text'},
            {
                order: 'persona_que_solicita_la_propuesta_economica',
                label: 'persona_que_solicita_la_propuesta_economica',
                type: 'text'
            },
            {order: 'orden_de_compra', label: 'orden_de_compra', type: 'text'},
            {order: 'hes', label: 'hes', type: 'text'},
            {order: 'observaciones', label: 'observaciones', type: 'text'},
        ];
    }
}

watch(() => data.ocultar1, (newX) => {
    ocultar11()
})

const tipoSelectable = [
    {label: 'Matenimiento', value: 'Preventivo'},
    {label: 'Servicio', value: 'Correctivo'},
    {label: 'Proyecto', value: 'Predictivo'},
]
</script>

<template>
    <Head :title="props.title"/>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>
        <div class="space-y-4">
            <!-- {{ props.fromController.data[2] }} -->
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true"
                                   v-if="can(['create cotizacion'])">
                        {{ lang().button.new }}
                    </PrimaryButton>
                    <Link :href="route('peusuario.index')" class="inline-flex">
                        <PrimaryButton class="rounded-none"
                                       v-if="can(['create cotizacion'])">
                                Empresas y Clientes a
                        </PrimaryButton>
                    </Link>

                    <Create v-if="can(['create cotizacion'])" :numberPermissions="props.numberPermissions"
                            :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false"
                            :title="props.title"
                            :losSelect=props.losSelect
                            :CentrosRepetidos=props.CentrosRepetidos
                            :consecutivoCotizacion=props.consecutivoCotizacion
                            :cotizacionInicial2=props.cotizacionInicial2
                    />

                    <Edit v-if="can(['update cotizacion'])" :titulos="titulos"
                          :numberPermissions="props.numberPermissions" :show="data.editOpen"
                          @close="data.editOpen = false"
                          :cotizaciona="data.cotizaciono"
                          :title="props.title"
                          :losSelect=props.losSelect
                          :CentrosRepetidos=props.CentrosRepetidos
                          :consecutivoCotizacion=props.consecutivoCotizacion
                          :cotizacionInicial2=props.cotizacionInicial2
                    />
                    <Facturar v-if="can(['update3 cotizacion'])"
                              :numberPermissions="props.numberPermissions" :show="data.edit3Open"
                              @close="data.edit3Open = false"
                              :cotizaciona="data.cotizaciono"
                              :title="props.title"
                              :losSelect=props.losSelect
                    />

                    <Generarcc v-if="can(['update2 cotizacion'])" :numberPermissions="props.numberPermissions"
                               :show="data.generarOpen" @close="data.generarOpen = false" :cotizaciona="data.cotizaciono"
                               :title="props.title"
                    />
                    <Delete v-if="can(['delete cotizacion'])" :numberPermissions="props.numberPermissions"
                            :show="data.deleteOpen" @close="data.deleteOpen = false" :cotizaciona="data.cotizaciono"
                            :title="props.title"
                    />
                  <DeleteBulk :show="data.deleteBulkOpen"
                                @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                                :selectedId="data.selectedId" :title="props.title"/>

                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between py-2 xs:px-1 2xl:pr-12 pl-3">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"/>
                        <DangerButton @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length !== 0 && can(['delete cotizacion'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                    <div class="flex space-x-3">
                        <p class="hidden 2xl:flex text-xs 2xl:text-sm mt-1 px-1 w-48">Solo Números</p>
                        <checkbox v-if="props.numberPermissions > 1" v-model="data.params.search4"
                                  class="hidden 2xl:flex p-2 mx-12 mt-3"/>
                        <p class="hidden 2xl:flex text-sm mt-1 px-1">Ocultar porcentajes</p>
                        <checkbox v-if="props.numberPermissions > 9" v-model="data.ocultar1"
                                  class="hidden 2xl:flex p-2 mx-12 mt-3"/>
                        
                        <v-select v-model="data.params.search2" :options="props.losSelect['zonas']" label="label"
                                  class="w-full mt-1 h-8">
                        </v-select>
                        
                        <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                                   class="hidden xl:flex xs:w-32 md:w-44 rounded-xl h-9 mt-1" placeholder="Número"/>
                        <v-select v-model="data.params.search6" :options="tipoSelectable" label="label"
                                  class="min-w-44 mt-1 h-8">
                        </v-select>
                        <!--                        <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search2" type="text"-->
                        <!--                                   class="block w-full lg:w-5/6 rounded-xl mx-2" placeholder="Descripción"/>-->
                        <p class="text-sm mt-3 ml-5 mr-0 px-2">Aprobación</p>
                        <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search3" type="date"
                                   class="block w-full lg:w-5/6 rounded-xl mx-4 h-9 mt-1" placeholder=""/>
                    </div>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table v-if="props.total > 0" class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                        <tr class="dark:bg-gray-900/50 text-left">
                            <th class="px-2 py-4 text-center">
                                <Checkbox v-model:checked="data.multipleSelect" @change="selectAll"/>
                            </th>
                            <th v-if="numberPermissions > 1" class="px-2 py-4">Accion</th>

                            <th class="px-2 py-4 text-center">#</th>
                            <th v-for="titulo in titulos" class="px-2 py-4 cursor-pointer"
                                v-on:click="order(titulo['order'])">
                                <div class="flex justify-between items-center">
                                    <span>{{ lang().label[titulo['label']] }}</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th
                                v-on:click="order('centro_costo_id')"
                                class="px-2 py-4 cursor-pointer"
                            >
                                <div class="flex justify-between items-center">
                                    <span>Tiene Centro de costo</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <!-- <th class="px-2 py-4 cursor-pointer" v-on:click="order('fecha_nacimiento')">
                                <div class="flex justify-between items-center"> <span>{{ lang().label.edad }}</span>
                                    <ChevronUpDownIcon class="w-4 h-4" />
                                </div>
                            </th> -->

                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(claseFromController, indexu) in props.fromController.data" :key="indexu"
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                <input
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"
                                    type="checkbox" @change="select" :value="claseFromController.id"
                                    v-model="data.selectedId"/>
                            </td>
                            <td v-if="numberPermissions > 1" class="whitespace-nowrap py-4 w-12 px-2 sm:py-3">
                                <div class="flex justify-center items-center">
                                    <div class="rounded-md overflow-hidden">
                                        <InfoButton v-show="can(['update cotizacion']) && !claseFromController.factura" type="button"
                                                    @click="(data.editOpen = true), (data.cotizaciono = claseFromController)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                            <PencilIcon class="w-4 h-4"/>
                                        </InfoButton>
                                        <DangerButton v-show="can(['update2 cotizacion']) && !claseFromController.centro_costo_id" type="button"
                                                      @click="(data.generarOpen = true), (data.cotizaciono = claseFromController)"
                                                      class="px-2 py-1.5 rounded-none"
                                                      v-tooltip="lang().permissions['update2 cotizacion']">
                                            <RocketLaunchIcon class="w-4 h-4"/>
                                        </DangerButton>
                                        <InfoButton v-show="can(['update3 cotizacion']) && claseFromController.centro_costo_id && !claseFromController.factura" type="button"
                                                    @click="(data.edit3Open = true), (data.cotizaciono = claseFromController)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.update2_cotizacion">
                                            <LockClosedIcon class="w-4 h-4"/>
                                        </InfoButton>
                                        <DangerButton v-show="can(['delete cotizacion','issuper'])" type="button"
                                                      @click="(data.deleteOpen = true), (data.cotizaciono = claseFromController)"
                                                      class="px-2 py-1.5 rounded-none"
                                                      v-tooltip="lang().tooltip.delete">
                                            <TrashIcon class="w-4 h-4"/>
                                        </DangerButton>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>
                            <td v-for="titulo in titulos" class="py-4 px-2 sm:py-3">
                                <span v-if="titulo['type'] === 'text123'" class=""> 
                                    {{ claseFromController['tipo'] }} 
                                    {{ claseFromController['tipo_de_mantenimiento'] }} 
                                </span>
                                <span v-if="titulo['type'] === 'text'" class="whitespace-nowrap min-w-72"> {{
                                        claseFromController[titulo['order']]
                                    }} </span>
                                <span v-if="titulo['type'] === 'string'" class="whitespace-nowrap"> {{
                                        claseFromController[titulo['order']]
                                    }} </span>
                                <span v-if="titulo['type'] === 'number'" class="whitespace-nowrap"> {{
                                        number_format(claseFromController[titulo['order']], 0, false)
                                    }} </span>
                                <span v-if="titulo['type'] === 'porcentaje'" class="whitespace-nowrap"> {{
                                        number_format(claseFromController[titulo['order']], 2, false)
                                    }} %</span>
                                <span v-if="titulo['type'] === 'dinero'" class="whitespace-nowrap"> {{
                                        number_format(claseFromController[titulo['order']], 0, true)
                                    }} </span>
                                <span v-if="titulo['type'] === 'date'" class="whitespace-nowrap"> {{
                                        formatDateToHuman(claseFromController[titulo['order']], false)
                                    }} </span>
                                <span v-if="titulo['type'] === 'datetime'" class="whitespace-nowrap"> {{
                                        formatDateTimeToHuman(claseFromController[titulo['order']], true)
                                    }} </span>
                                <span v-if="titulo['type'] === 'id'">
                                    {{ claseFromController[titulo['order'] + '2'] }}
                                </span>
                            </td>
                            <!--                            <td>{{claseFromController['centro_costo_id2']}}</td>-->
                            <td>{{ claseFromController['centro_costo_id2'] ? '✅' : '❌' }}</td>
                        </tr>
                        <tr class="border-t border-gray-600">
                            <td v-if="numberPermissions > 1"
                                class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> -
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"> Total:</td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                {{ props.total }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <h2 v-else class="text-center text-xl my-8">Sin Registros</h2>
                </div>
                <div v-if="props.total > 0"
                     class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
