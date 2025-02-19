<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch} from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import {Head, Link, router, usePage} from '@inertiajs/vue3';

import Create from '@/Pages/CentroCostos/Create.vue';
import Edit from '@/Pages/CentroCostos/Edit.vue';
import Delete from '@/Pages/CentroCostos/Delete.vue';

import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, DocumentIcon, EyeIcon, PencilIcon, TrashIcon} from '@heroicons/vue/24/solid';
import {formatPesosCol} from '@/global.ts';

import InfoButton from '@/Components/InfoButton.vue';

const {_, debounce, pickBy} = pkg
const props = defineProps({
    title: String,
    filters: Object,
    fromController: Object,
    breadcrumbs: Object,
    numberPermissions: Object,
    perPage: Number,
    nombresTabla: Array,
    listaSupervisores: Object,
})

const data = reactive({
    params: {
        search: props.filters?.search, //por nombre o descrip
        searchSCC: props.filters?.searchSCC, //por supervisor
        searchh2: props.filters?.searchh2, //ver todos (solo super)
        field: props.filters?.field,
        order: props.filters?.order,
        perPage: props.perPage,
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
    dataSet: usePage().props.app.perpage,
})

const order = (field) => {
    if (field !== undefined) {
        data.params.field = field.replace(/ /g, "_")
        data.params.order = data.params.order === "asc" ? "desc" : "asc"
    }
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("CentroCostos.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <!--        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />-->
        <h2 class="text-lg mb-1">Esta vista se recarga cada 5 minutos</h2>
        <h2 class="mb-1">Mano de obra estimada de este mes. Se tiene en cuenta el salario de la persona.
            <div v-if="can(['isSuper'])" class="mt-1 block w-full">
                <label :for="'searchh2'">
                    <input
                        type="checkbox"
                        :id="'searchh2'"
                        value="false"
                        v-model="data.params.searchh2"
                    />
                    Ver mas de 80
                </label>
            </div>
        </h2>
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton v-if="can(['isSuper'])" class="rounded-none"
                                   @click="data.createOpen = true">
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <Create :show="data.createOpen" @close="data.createOpen = false"
                            :listaSupervisores="props.listaSupervisores" :title="props.title"/>
                    <Edit :show="data.editOpen" @close="data.editOpen=false"
                          :listaSupervisores="props.listaSupervisores"
                          :CentroCosto="data.generico"
                          :title="props.title"/>
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :CentroCosto="data.generico"
                            :title="props.title"/>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"/>
                        <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length != 0"
                                      class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5"/>
                        </DangerButton>
                    </div>
                    <div class="flex gap-2">
                        <TextInput v-model="data.params.search" type="text" class="block w-1/2 rounded-lg"
                                   :placeholder="lang().placeholder.searchCC"/>
                        <TextInput v-model="data.params.searchSCC" type="text" class="block w-1/2 rounded-lg"
                                   :placeholder="lang().placeholder.searchSupervisorCC"/>
                    </div>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                        <tr class="dark:bg-gray-900 text-left">
                            <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                v-on:click="order(nombresTabla[1][indiceN])"
                                class="px-2 py-4 cursor-pointer hover:bg-indigo-200/30 dark:hover:bg-sky-800">
                                <div class="flex justify-between items-center">
                                    <span>{{ titulos }}</span>
                                    <ChevronUpDownIcon v-if="nombresTabla[1][indiceN]" class="w-4 h-4"/>
                                </div>
                            </th>
                            <!-- <th class="px-2 py-4 sr-only">Action</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-indigo-200 hover:dark:bg-gray-900/20">
                            <td v-if="can(['update centroCostos'])"
                                class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                <div class="flex justify-start items-center">
                                    <div class="rounded-md overflow-hidden">
                                        <Link :href="route('CentroCostos.show',clasegenerica)"
                                              v-show="can(['update centroCostos'])" type="button"
                                              class="inline-flex  items-center px-2 py-1.5 bg-gray-600 border border-transparent rounded-none font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                              v-tooltip="lang().tooltip.see">
                                            <EyeIcon class="w-4 h-4"/>
                                        </Link>
                                        <Link :href="route('CentroCostos.table',clasegenerica.id)"
                                              v-show="can(['update centroCostos'])" type="button"
                                              class="inline-flex  items-center px-2 py-1.5 bg-gray-600 border border-transparent rounded-none font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                              v-tooltip="lang().tooltip.Reporte">
                                            <DocumentIcon class="w-4 h-4"/>
                                        </Link>
                                        <InfoButton v-show="can(['update centroCostos'])" type="button"
                                                    @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                            <PencilIcon class="w-4 h-4"/>
                                        </InfoButton>
                                        <DangerButton v-show="can(['delete centroCostos'])" type="button"
                                                      @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                      class="px-2 py-1.5 rounded-sm" v-tooltip="lang().tooltip.delete">
                                            <TrashIcon class="w-4 h-4"/>
                                        </DangerButton>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ parseInt(index, 10) + 1 }}</td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.nombre) }}</td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                {{ formatPesosCol(clasegenerica.mano_obra_estimada) }}
                            </td>
                            <td v-show="can(['update centroCostos'])" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                {{ (clasegenerica.cuantoshijos) }}
                            </td>
                            <td v-show="can(['update centroCostos'])" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                <p v-for="(superv, inde) in clasegenerica.supervi.split(',')" :key="inde">
                                    <span v-if="superv.trim()">{{ inde + 1 + ') ' + superv }}</span>
                                    <span v-else class="border-b-blue-300 border-2">Sin supervisor</span>
                                </p>
                            </td>
                            <!--                                <td v-show="can(['update centroCostos'])" class="whitespace-nowrap py-4 px-2 sm:py-3">({{clasegenerica.supervi.split(',').length}}) {{ (clasegenerica.supervi) }} </td>-->
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{
                                    (clasegenerica.activo ? '✅' : '❌')
                                }}
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                {{ (clasegenerica.ValidoParaFacturar ? '✅' : '❌') }}
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.descripcion) }}</td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.clasificacion) }}</td>
                            <!--                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.ValidoParaFacturar) }} </td>-->
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
