<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch,computed} from 'vue';

import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';

import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, PencilIcon, TrashIcon, CurrencyDollarIcon, ShieldExclamationIcon} from '@heroicons/vue/24/solid';
import Create from '@/Pages/viatico/Create.vue';
import Edit from '@/Pages/viatico/Edit.vue';
import Delete from '@/Pages/viatico/Delete.vue';
import Aprobar from '@/Pages/viatico/Aprobar.vue';
import Legalizar from '@/Pages/viatico/Legalizar.vue';
import DeleteBulk from '@/Pages/viatico/DeleteBulk.vue';

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
    totalsaldo: Number,
    totallegalizado: Number,
})

const data = reactive({
    params: {
        search: props.filters.search,
        search2: props.filters.search2,
        // search3: props.filters.search3, //mostrar todo para el admin
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    viaticoo: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    AprobarOpen: false,
    LegalizarOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    dataSet: usePage().props.app.perpage,
})

// <!--<editor-fold desc="order, watchclone, select">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("viatico.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((viatico) => {
            data.selectedId.push(viatico.id)
        })
    }
}
const select = () => data.multipleSelect = props.fromController?.data.length === data.selectedId.length;
// <!--</editor-fold>-->


// const form = useForm({ })
// watchEffect(() => { })

const totalSaldo = computed(() => {
  return props.fromController.data.reduce((acc, item) => acc + (item.saldo || 0), 0);
});


// text // number // dinero // date // datetime // foreign
const titulos = [
    // { order: 'codigo', label: 'codigo', type: 'text' },
    {order: 'gasto', label: 'gasto', type: 'number'},
    {order: 'user_id', label: 'user_id', type: 'foreign', nameid: 'userino'},
    {order: 'descripcion', label: 'descripcion', type: 'text2'},
    {order: 'Consignaciona', label: 'Consignaciona', type: 'number'},
    {order: 'fechaconsig', label: 'fechaconsig', type: 'date'},
    {order: 'saldo', label: 'saldo', type: 'number'},
    {order: 'fecha_inicial', label: 'fecha_inicial', type: 'date'},
    {order: 'fecha_final', label: 'fecha_final', type: 'date'},
    {order: 'numerodias', label: 'numerodias', type: 'number'},
    // {order: 'legalizacion', label: 'legalizacion', type: 'text'},
    {order: 'valor_legalizacion', label: 'valor_legalizacion', type: 'dinero'},
    {order: 'descripcion_legalizacion', label: 'descripcion_legalizacion', type: 'text'},
    {order: 'fecha_legalizacion', label: 'fecha_legalizacion', type: 'datetime'},
    {order: 'centro_costo_id', label: 'centro_costo_id', type: 'foreign', nameid: 'centrou'},
    // { order: 'inventario', label: 'inventario', type: 'foreign',nameid:'nombre'},
];

let classbotones = "w-6 h-6"
</script>

<template>
    <Head :title="props.title" />
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true"
                                   v-if="can(['create viatico'])">
                        {{ lang().button.new }}
                    </PrimaryButton>

                    <Create v-if="can(['create viatico'])" :numberPermissions="props.numberPermissions"
                            :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false"
                            :title="props.title"
                            :losSelect=props.losSelect />

                    <Edit v-if="can(['update viatico'])" :titulos="titulos"
                          :numberPermissions="props.numberPermissions" :show="data.editOpen"
                          @close="data.editOpen = false"
                          :viaticoa="data.viaticoo" :title="props.title" :losSelect=props.losSelect />
                    <Aprobar v-if="can(['isAdmin'])" :titulos="titulos"
                             :numberPermissions="props.numberPermissions" :show="data.AprobarOpen"
                             @close="data.AprobarOpen = false"
                             :viaticoa="data.viaticoo" :title="props.title" :losSelect=props.losSelect />
                    <Legalizar v-if="can(['isadministrativo'])" :titulos="titulos"
                             :numberPermissions="props.numberPermissions" :show="data.LegalizarOpen"
                             @close="data.LegalizarOpen = false"
                             :viaticoa="data.viaticoo" :title="props.title" :losSelect=props.losSelect />

                    <Delete v-if="can(['delete viatico'])" :numberPermissions="props.numberPermissions"
                            :show="data.deleteOpen" @close="data.deleteOpen = false" :viaticoa="data.viaticoo"
                            :title="props.title" />
                      <DeleteBulk v-if="can(['isSuper'])" :show="data.deleteBulkOpen"
                        @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                        :selectedId="data.selectedId" :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="grid grid-cols-3 justify-between p-2">
                    <div class="flex gap-2 ">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton  v-if="can(['isSuper'])" @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length != 0 && can(['delete viatico'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                    <div class="col-span-1"></div>
                    <div class="inline-flex">
                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                               class="block w-4/6 md:w-3/6 lg:w-full mx-1 rounded-lg" placeholder="Descripción" />
                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search2" type="text"
                               class="block w-4/6 md:w-3/6 lg:w-full mx-1 rounded-lg" placeholder="Persona" />
<!--                      <p class="text-sm -mx-1 px-4">Mostrar todos</p>-->
<!--                        <checkbox v-if="props.numberPermissions > 8" v-model="data.params.search3"-->
<!--                                  class="p-2 mx-12 my-3"/>-->
                    </div>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table v-if="props.total > 0" class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                        <tr class="dark:bg-gray-900/50 text-left">
                            <th class="px-2 py-4 text-center">
                                <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                            </th>
                            <th v-if="numberPermissions > 1" class="px-2 py-4">Accion</th>

<!--                            <th class="px-2 py-4 text-center">#</th>-->
                            
                            <th v-for="titulo in titulos" class="px-2 py-4 cursor-pointer"
                                v-on:click="order(titulo['order'])">
                                <div class="flex justify-between items-center">
                                    <span>{{ lang().label[titulo['label']] }}</span>
                                    <ChevronUpDownIcon class="w-4 h-4" />
                                </div>
                            </th>
                            

                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(claseFromController, indexu) in props.fromController.data" :key="indexu"
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">

                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                <input
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"
                                    type="checkbox" @change="select" :value="claseFromController.id"
                                    v-model="data.selectedId" />
                            </td>
                            <td v-if="numberPermissions > 1" class="whitespace-nowrap py-4 w-12 px-2 sm:py-3">
                                <div class="flex justify-center items-center">
                                    <div class="rounded-md overflow-hidden">
                                        <InfoButton v-show="can(['update viatico'])" type="button"
                                                    @click="(data.editOpen = true), (data.viaticoo = claseFromController)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                            <PencilIcon :class="classbotones" />
                                        </InfoButton>
                                        <InfoButton v-show="can(['isAdmin'])" type="button" :thecolor="'green'"
                                                    @click="(data.AprobarOpen = true), (data.viaticoo = claseFromController)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.consignar">
                                            <CurrencyDollarIcon :class="classbotones" />
                                        </InfoButton>
                                        <InfoButton v-show="can(['isadministrativo']) && claseFromController.Consignaciona.length !== 0" type="button" :thecolor="'gray'"
                                                    @click="(data.LegalizarOpen = true), (data.viaticoo = claseFromController)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.legalizar">
                                            <ShieldExclamationIcon :class="classbotones" />
                                        </InfoButton>
                                        <DangerButton v-show="can(['isSuper'])" type="button"
                                                      @click="(data.deleteOpen = true), (data.viaticoo = claseFromController)"
                                                      class="px-2 py-1.5 rounded-none"
                                                      v-tooltip="lang().tooltip.delete">
                                            <TrashIcon :class="classbotones" />
                                        </DangerButton>
                                    </div>
                                </div>
                            </td>

<!--{{claseFromController}}-->
<!--                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>-->
                            <td class="whitespace-nowrap py-2 px-2">{{ formatPesosCol(claseFromController['gasto']) }}</td>
                            <td class="whitespace-nowrap py-2 px-2"> {{ claseFromController['userino'] }}</td>
                            <td class="min-w-[440px] py-2 px-2"> {{ claseFromController['descripcion'] }}</td>
                            <td class="whitespace-nowrap py-2 px-2">
                                <p v-for="item in claseFromController['Consignaciona']" :key="item.id" 
                                   class="whitespace-nowrap py-2 px-2">
                                    {{ formatPesosCol(item['valor']) }}
                                </p>
                            </td>
                            <td class="whitespace-nowrap py-2 px-2">
                                <p v-for="item in claseFromController['Consignaciona']" :key="item.id" 
                                   class="whitespace-nowrap py-2 px-2">
                                    {{ formatDate(item['fecha']) }}
                                </p>
                            </td>
                            <td class="whitespace-nowrap py-2 px-8">{{ formatPesosCol(claseFromController['saldo']) }}</td>
                            <td class="whitespace-nowrap py-2 px-8">{{ formatDate(claseFromController['fecha_inicial']) }}</td>
                            <td class="whitespace-nowrap py-2 px-8">{{ formatDate(claseFromController['fecha_final']) }}</td>
                            <td class="whitespace-nowrap py-2 px-8">{{ (claseFromController['numerodias']) }}</td>
                            
                             <td class="whitespace-nowrap py-2 px-2">
                                <p v-for="item in claseFromController['Consignaciona']" :key="item.id" 
                                   class="whitespace-nowrap py-2 px-2">
<!--                                    {{item}}-->
                                    {{ formatPesosCol(item['valor_legalizacion']) }}
<!--                                    {{ formatPesosCol(item['valor_legalizado']) }}-->
                                </p>
                            </td>
                             <td class="whitespace-nowrap py-2 px-2">
                                <p v-for="item in claseFromController['Consignaciona']" :key="item.id" 
                                   class="whitespace-nowrap py-2 px-2">
                                    {{ (item['descripcion_legalizacion']) }}
                                </p>
                            </td>
                             <td class="whitespace-nowrap py-2 px-2">
                                <p v-for="item in claseFromController['Consignaciona']" :key="item.id" 
                                   class="whitespace-nowrap py-2 px-2">
                                    {{ formatDate(item['fecha_legalizacion']) }}
<!--                                    {{ (item['fecha_legalizacion']) }}-->
<!--                                    {{ formatDate(item['fecha_legalizado']) }}-->
                                </p>
                            </td>
                            
<!--                            <td class="whitespace-nowrap py-2 px-2"> {{ formatPesosCol(claseFromController['valor_legalizacion'])}} {{ claseFromController['legalizacion'] ? '✅': '❌'}}</td>-->
<!--                            <td class="whitespace-nowrap py-2 px-2"> {{ claseFromController['descripcion_legalizacion']}}</td>-->
<!--                            <td class="whitespace-nowrap py-2 px-2">{{ formatDate(claseFromController['fecha_legalizacion']) }}</td>-->
                            <td class="whitespace-nowrap py-2 px-2"> {{ claseFromController['centrou'] }}</td>
                            
                        </tr>
                        
                        
                        <tr class="border-t border-gray-600">
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td><strong class="text-sm">Total saldo: <br></strong> {{ formatPesosCol(props.totalsaldo) }}</td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>
                            <td><strong class="text-sm">Total legalizado: <br></strong> {{ formatPesosCol(totallegalizado) }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <h2 v-else class="text-center text-xl my-8">Sin Registros</h2>
                </div>
                <div v-if="props.total > 0"
                     class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
