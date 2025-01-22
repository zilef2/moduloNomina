<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch} from 'vue';
import pkg from 'lodash';
import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon} from '@heroicons/vue/24/solid';

const {_, debounce, pickBy} = pkg
const props = defineProps({
    title: String,
    filters: Object,
    ubicacions: Object,
    roles: Object,
    cargos: Object,
    centros: Object,
    breadcrumbs: Object,
    perPage: Number,
    sexoSelect: Object,
    onlySupervis: Number,
    superviNullCentro: Number,
})
const data = reactive({
    params: {
        search: props.filters.search, //nombre
        search2: props.filters.search2, //nombre
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
        onlySupervis: props.onlySupervis,
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    EdiCentroOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    ubicacion: null,
    dataSet: usePage().props.app.perpage,
    columsVisible: {
        0: true,
        1: true,
        2: true,
        3: true,
        4: true,
        5: true,
        6: true,
        7: true,
        8: true,
        9: true,
        10: true,
        11: true,
        12: true,
        13: true,
        14: true,
        15: true,
    }
})


const toggleColumn = (index) => data.columsVisible[index] = !data.columsVisible[index];


// <!--<editor-fold desc="default functions | order, watch, selectall">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
    console.log(data.params.order)
    console.log(data.params.field)
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    console.log("🧈 debu params:", params);
    router.get(route("ubicacion.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 15))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.ubicacions?.data.forEach((ubicacion) => {
            data.selectedId.push(ubicacion.id)
        })
    }
}
const select = () => {
    if (props.ubicacions?.data.length == data.selectedId.length) {
        data.multipleSelect = true
    } else {
        data.multipleSelect = false
    }
}
// <!--</editor-fold>-->
const clonedData = [...props.ubicacions.data]; // Clonamos usando spread

const ubicacionesCiudad = [...new Set(clonedData.map(item => item.ubicacion))] // Eliminamos duplicados
  .slice(0, 7) // Tomamos los primeros 7
  .join(', '); // Unimos en una cadena
const ubicacionesName = [...new Set(clonedData.map(item => item.name))] // Eliminamos duplicados
  .slice(0, 5) // Tomamos los primeros 7
  .join(', '); // Unimos en una cadena


</script>

<template>
    <Head :title="props.title"/>

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs"/>
        <div class="space-y-1">

            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg my-2">
                <div class="flex justify-between p-2">
                    <div class="flex lg:hidden xl:flex space-x-2 gap-1">
                        <TextInput v-model="data.params.search" v-tooltip="'Personas: '+ ubicacionesName + ' y otros...'"
                                   type="text" min="0" class="block w-2/3 md:w-full rounded-lg"
                                   placeholder="Persona o ciudad"/>
                    </div>
                    <div class="flex lg:hidden xl:flex space-x-2 gap-1">
                        <TextInput v-model="data.params.search2"
                                   type="text" min="0" class="block w-2/3 md:w-full rounded-lg"
                                   placeholder="Omitir ciudad"
                                   v-tooltip="'Ciudades: '+ ubicacionesCiudad"
                                    />
                    </div>
                </div>
                
<!--                fin de los filtros-->
                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <SelectInput v-if="filters !== null" v-model="data.params.perPage" :dataSet="data.dataSet"/>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                        <tr class="dark:bg-gray-900/50 text-left">
                            <th class="px-6 py-4 cursor-pointer" v-on:click="order('ubicacion')">
                                <div class="flex justify-between items-center"><span>Ciudad</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th class="px-2 py-4 cursor-pointer" v-on:click="order('name')">
                                <div class="flex justify-between items-center"><span>Persona</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th class="px-2 py-4 cursor-pointer" v-on:click="order('created_at')">
                                <div class="flex justify-between items-center"><span>Registro</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(ubicacion, index) in ubicacions.data" :key="index"
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ ubicacion.ubicacion }}</td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ ubicacion.name }}</td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ ubicacion.created_at }}</td>
                            <!--                            <td class="whitespace-nowrap p-4 sm:p-3">-->
                            <!--                                <div class="flex justify-center">-->
                            <!--                                    <Link :href="route('ubicacion.showReporte',ubicacion.id)"-->
                            <!--                                          v-show="can(['update centroCostos'])" type="button"-->
                            <!--                                          class="inline-flex  items-center p-1.5 bg-gray-600 border border-transparent rounded-l-lg font-semibold text-xs text-white uppercase tracking-widest-->
                            <!--                                             hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-250"-->
                            <!--                                          v-tooltip="lang().tooltip.seeReport">-->
                            <!--                                        <EyeIcon class="w-4 h-4"/>-->
                            <!--                                    </Link>-->
                            <!--                                    <div class="rounded-md overflow-hidden">-->
                            <!--                                        <InfoButton v-show="can(['update ubicacion'])" type="button"-->
                            <!--                                                    @click="(data.editOpen = true), (data.ubicacion = ubicacion)"-->
                            <!--                                                    class="px-2 py-1.5 rounded-sm mx-0.5"-->
                            <!--                                                    v-tooltip="lang().tooltip.edit">-->
                            <!--                                            <PencilIcon class="w-4 h-4"/>-->
                            <!--                                        </InfoButton>-->
                            <!--                                        <InfoButton v-if="ubicacion.roles[0]"-->
                            <!--                                                    v-show="can(['update ubicacion']) && ubicacion.roles[0]?.name === 'supervisor'"-->
                            <!--                                                    type="button"-->
                            <!--                                                    @click="(data.EdiCentroOpen = true), (data.ubicacion = ubicacion)"-->
                            <!--                                                    class="px-2 py-1.5 rounded-sm mx-0.5" v-tooltip="'Asignar'">-->
                            <!--                                            <LinkIcon class="w-4 h-4"/>-->
                            <!--                                        </InfoButton>-->
                            <!--                                        <DangerButton v-show="can(['delete ubicacion'])" type="button"-->
                            <!--                                                      @click="(data.deleteOpen = true), (data.ubicacion = ubicacion)"-->
                            <!--                                                      class="px-2 py-1.5 rounded-none"-->
                            <!--                                                      v-tooltip="lang().tooltip.delete">-->
                            <!--                                            <TrashIcon class="w-4 h-4"/>-->
                            <!--                                        </DangerButton>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            </td>-->
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.ubicacions" :filters="data.params"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
