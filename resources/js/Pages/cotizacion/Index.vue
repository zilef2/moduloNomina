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
import Create from '@/Pages/cotizacion/Create.vue';
import Edit from '@/Pages/cotizacion/Edit.vue';
import Delete from '@/Pages/cotizacion/Delete.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';

import {formatDate2,formatDateTime, number_format} from '@/global.ts';

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
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    cotizaciono: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
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
const select = () => {
    if (props.fromController?.data.length === data.selectedId.length) {
        data.multipleSelect = true
    } else {
        data.multipleSelect = false
    }
}
// <!--</editor-fold>-->


// const form = useForm({ })
// watchEffect(() => {})

// text // number // dinero // date // datetime // foreign
const titulos = [
    // { order: 'codigo', label: 'codigo', type: 'text' },
    {order: 'numero_cot', label: 'numero_cot', type: 'text'},
    {order: 'centro_costo_id', label: 'centro_costo', type: 'id',nameid:'nombre'},
    {order: 'descripcion_cot', label: 'descripcion_cot', type: 'text'},
    {order: 'precio_cot', label: 'precio_cot', type: 'number'},
    {order: 'fecha_aprobacion_cot', label: 'fecha_aprobacion_cot', type: 'date'},
    
    
    {order: 'estado_cliente', label: 'estado_cliente', type: 'text'},
    {order: 'estado', label: 'estado', type: 'text'},
    {order: 'factura', label: 'factura', type: 'text'},
    {order: 'fecha_factura', label: 'fecha_factura', type: 'date'},
    {order: 'mes_pedido', label: 'mes_pedido', type: 'text'},
    {order: 'lugar', label: 'lugar', type: 'text'},
    {order: 'tipo', label: 'tipo', type: 'text'},
    {order: 'tipo_de_mantenimiento', label: 'tipo_de_mantenimiento', type: 'text'},
    {order: 'por_a', label: 'por_a', type: 'number'},
    {order: 'por_i', label: 'por_i', type: 'number'},
    {order: 'por_u', label: 'por_u', type: 'number'},
    {order: 'admi', label: 'admi', type: 'number'},
    {order: 'impr', label: 'impr', type: 'number'},
    {order: 'util', label: 'util', type: 'number'},
    {order: 'subtotal', label: 'subtotal', type: 'number'},
    {order: 'iva', label: 'iva', type: 'number'},
    {order: 'total', label: 'total', type: 'number'},
    {order: 'persona_que_realiza_la_pe', label: 'persona_que_realiza_la_pe', type: 'text'},
    {order: 'cliente', label: 'cliente', type: 'text'},
    {order: 'persona_que_solicita_la_propuesta_economica', label: 'persona_que_solicita_la_propuesta_economica', type: 'text'},
    {order: 'orden_de_compra', label: 'orden_de_compra', type: 'text'},
    {order: 'hes', label: 'hes', type: 'text'},
    {order: 'observaciones', label: 'observaciones', type: 'text'},
];

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
                          :cotizaciona="data.cotizaciono" :title="props.title" 
                          :losSelect=props.losSelect
                    />

<!--                    <Delete v-if="can(['delete cotizacion'])" :numberPermissions="props.numberPermissions"-->
<!--                            :show="data.deleteOpen" @close="data.deleteOpen = false" :cotizaciona="data.cotizaciono"-->
<!--                            :title="props.title"-->
<!--                    />-->
                    
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"/>
                        <!-- <DangerButton @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length != 0 && can(['delete cotizacion'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton> -->
                    </div>
                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                               class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Nombre, codigo"/>
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
<!--                            <th class="px-2 py-4 cursor-pointer"-->
<!--                                v-on:click="order('centro_costo_id')">-->
<!--                                <div class="flex justify-between items-center">-->
<!--                                    <span>Centro de costo</span>-->
<!--                                    <ChevronUpDownIcon class="w-4 h-4"/>-->
<!--                                </div>-->
<!--                            </th>-->
                            <th class="px-2 py-4 cursor-pointer"
                                v-on:click="order('centro_costo_id')">
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
                                        <InfoButton v-show="can(['update cotizacion'])" type="button"
                                                    @click="(data.editOpen = true), (data.cotizaciono = claseFromController)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                            <PencilIcon class="w-4 h-4"/>
                                        </InfoButton>
<!--                                        <DangerButton v-show="can(['delete cotizacion'])" type="button"-->
<!--                                                      @click="(data.deleteOpen = true), (data.cotizaciono = claseFromController)"-->
<!--                                                      class="px-2 py-1.5 rounded-none"-->
<!--                                                      v-tooltip="lang().tooltip.delete">-->
<!--                                            <TrashIcon class="w-4 h-4"/>-->
<!--                                        </DangerButton>-->
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>
                            <td v-for="titulo in titulos" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                <span v-if="titulo['type'] === 'text'"> {{ claseFromController[titulo['order']] }} </span>
                                <span v-if="titulo['type'] === 'number'"> {{ number_format(claseFromController[titulo['order']], 0, false) }} </span>
                                <span v-if="titulo['type'] === 'dinero'"> {{ number_format(claseFromController[titulo['order']], 0, true) }} </span>
                                <span v-if="titulo['type'] === 'date'"> {{ formatDate2(claseFromController[titulo['order']], false) }} </span>
                                <span v-if="titulo['type'] === 'datetime'"> {{ formatDateTime(claseFromController[titulo['order']], true) }} </span>
                                <span v-if="titulo['type'] === 'id'">
                                    {{claseFromController[titulo['order']+'2']}}
                                </span>
                            </td>
<!--                            <td>{{claseFromController['centro_costo_id2']}}</td>-->
                            <td>{{claseFromController['centro_costo_id2'] ? '✅' : '❌'}}</td>
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
