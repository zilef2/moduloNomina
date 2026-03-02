<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, computed, ref } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

import Create from '@/Pages/CentroCostos/Create.vue';
import Edit from '@/Pages/CentroCostos/Edit.vue';
import Delete from '@/Pages/CentroCostos/Delete.vue';

import { ChevronUpDownIcon, DocumentIcon, EyeIcon, PencilIcon, TrashIcon, ListBulletIcon } from '@heroicons/vue/24/solid';
import { formatPesosCol } from '@/global.ts';

import InfoButton from '@/Components/InfoButton.vue';

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";


const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    fromController: Object,
    breadcrumbs: Object,
    numberPermissions: Object,
    perPage: Number,
    nombresTabla: Array,
    listaSupervisores: Object,
    losSelect: Array,
})

const data = reactive({
    params: {
        search: props.filters?.search, //por nombre o descrip
        // searchSCC: props.filters?.searchSCC, //por supervisor
        search2: props.filters?.search2, //ver todos (solo super)
        search3: props.filters?.search3, //zona
        field: props.filters?.field,
        order: props.filters?.order,
        perPage: props.perPage,
        columnFilters: props.filters?.columnFilters || {},
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    generico: null,
    dataSet: usePage().props.app.perpage,
    filtrosvue:{
        searchSCC:'',
    }
})

// <!--<editor-fold desc="temp">-->
// Todos los datos cargados del servidor
const allData = ref(props.fromController.data || [])

// Página actual para paginación frontend
const currentPage = ref(1)

// Datos filtrados localmente
const filteredData = computed(() => {
    let result = [...allData.value];

    // Filtro por nombre o descripción
    if (data.params.search) {
        const search = data.params.search.toLowerCase();
        result = result.filter(item =>
            (item.nombre || '').toLowerCase().includes(search) ||
            (item.descripcion || '').toLowerCase().includes(search)
        );
    }

    // Filtro por supervisor
    if (data.filtrosvue.searchSCC) {
        const searchSCC = data.filtrosvue.searchSCC.toLowerCase();
        result = result.filter(item => {
            if (item.supervi && item.supervi.length) {
                return item.supervi.some(sup => (sup || '').toLowerCase().includes(searchSCC));
            }
            return false;
        });
    }

    // Filtro por zona
    if (data.params.search3) {
        const search3 = data.params.search3;
        const zonaValue = search3.value || search3;
        result = result.filter(item =>
            item.zona_id === zonaValue || 
            (item.Zouna && item.Zouna.toLowerCase()) === (search3.label || '').toLowerCase()
        );
    }

    // Filtros por columna
    if (data.params.columnFilters) {
        Object.entries(data.params.columnFilters).forEach(([key, value]) => {
            if (value) {
                const filterValue = value.toLowerCase();
                result = result.filter(item => {
                    const itemValue = item[key];
                    if (typeof itemValue === 'string') {
                        return itemValue.toLowerCase().includes(filterValue);
                    }
                    return String(itemValue).toLowerCase().includes(filterValue);
                });
            }
        });
    }

    return result;
});

// Datos paginados
const paginatedData = computed(() => {
    const perPage = data.params.perPage || 10;
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;
    return filteredData.value.slice(start, end);
});

// Total de datos filtrados
const totalFiltered = computed(() => filteredData.value.length);

// Número total de páginas
const totalPages = computed(() => {
    return Math.ceil(totalFiltered.value / (data.params.perPage || 10));
});

// Resetear a página 1 cuando cambian los filtros (excepto perPage)
watch(() => data.params, (newVal, oldVal) => {
    if (oldVal && newVal.perPage !== oldVal.perPage) {
        return;
    }
    currentPage.value = 1;
}, { deep: true });

// Funciones de paginación
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Recargar datos desde el servidor (solo cuando se necesita)
const reloadData = () => {
    let params = pickBy(data.params)
    params.perPage = 99999;
    router.get(route("CentroCostos.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            allData.value = page.props.fromController.data || [];
            currentPage.value = 1;
        }
    })
};

// Traer todos los centros de costo
const loadAll = () => {
    let params = pickBy(data.params)
    params.loadAll = true;
    router.get(route("CentroCostos.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            allData.value = page.props.fromController.data || [];
            currentPage.value = 1;
        }
    })
};

// Observar cambios en el perPage para recargar datos
// watch(() => data.params.perPage, () => {
//     reloadData();
// });

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

const fechaActual = new Date();
const opciones = { month: 'long' }; // 'long' para el nombre completo del mes, 'short' para abreviatura
const nombreMes = fechaActual.toLocaleDateString('es-ES', opciones);
// <!--</editor-fold>-->
</script>

<template>

    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs / Header Section -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
<h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        {{ props.title }} <span
                            class="text-amber-500 text-sm font-medium ml-2 px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 rounded-full">{{
                            totalFiltered }} Total</span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Gestión y control de presupuesto por centros de costo operativos.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <PrimaryButton v-if="can(['isSuper'])"
                        class="shadow-lg shadow-primary/20 transition-all hover:scale-105"
                        @click="data.createOpen = true">
                        <PlusIcon class="w-4 h-4 mr-1" />
                        {{ lang().button.add }} - you are super
                    </PrimaryButton>

                    <PrimaryButton 
                        class="shadow-lg shadow-amber-500/20 transition-all hover:scale-105 bg-amber-500 hover:bg-amber-600"
                        @click="loadAll">
                        <ListBulletIcon class="w-4 h-4 mr-1" />
                        Cargar Todos
                    </PrimaryButton>

                    <Create :show="data.createOpen" @close="data.createOpen = false"
                        :listaSupervisores="props.listaSupervisores" :title="props.title" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false"
                        :listaSupervisores="props.listaSupervisores" :CentroCosto="data.generico"
                        :losSelect="props.losSelect" :title="props.title" />

                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :CentroCosto="data.generico"
                        :title="props.title" />
                </div>
            </div>

            <!-- Stats Bar (Optional, can be added if backend provides data) -->
            <!-- ... -->

            <!-- Main Content Card -->
            <div
                class="bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">

                <!-- Filters Section -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex flex-col lg:flex-row gap-4 justify-between items-center">
                        <div class="flex items-center space-x-3 w-full lg:w-auto">
                            <div class="relative">
                                <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"
                                    class="h-10 pl-3 pr-8 rounded-xl border-gray-200 focus:ring-amber-500" />
                            </div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Por página</span>

                            <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length !== 0"
                                class="rounded-xl h-10 px-4" v-tooltip="lang().tooltip.delete_selected">
                                <TrashIcon class="w-5 h-5" />
                            </DangerButton>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full lg:w-2/3">
                            <TextInput v-model="data.params.search" type="text"
                                class="h-10 rounded-xl border-gray-200 focus:ring-amber-500"
                                :placeholder="lang().placeholder.searchCC" />

                            <v-select v-model="data.params.search3" :options="props.losSelect['zona']" label="label"
                                placeholder="Filtrar por Zona" class="custom-v-select"></v-select>

                            <TextInput v-model="data.filtrosvue.searchSCC" type="text"
                                class="h-10 rounded-xl border-gray-200 focus:ring-amber-500"
                                :placeholder="lang().placeholder.searchSupervisorCC" />
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto scrollbar-table max-h-[65vh]">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 z-10 bg-white dark:bg-gray-800 shadow-sm">
                            <tr
                                class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    @click="order(nombresTabla[1][indiceN])"
                                    class="px-4 py-4 cursor-pointer hover:text-amber-600 transition-colors">
                                    <div class="flex items-center gap-1">
                                        {{ titulos }}
                                        <ChevronUpDownIcon v-if="nombresTabla[1][indiceN]"
                                            class="w-4 h-4 text-gray-400" />
                                    </div>
                                </th>
                            </tr>
                        </thead>
<tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="(clasegenerica, index) in paginatedData" :key="index"
                                class="group hover:bg-amber-50/30 dark:hover:bg-amber-900/5 transition-colors">

                                <!-- Actions Column -->
                                <td v-if="can(['update centroCostos'])" class="px-4 py-4 whitespace-nowrap">
                                    <div
                                        class="flex items-center gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                                        <Link :href="route('CentroCostos.show', clasegenerica)"
                                            class="p-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-blue-500 hover:text-white transition-all transform hover:scale-110"
                                            v-tooltip="lang().tooltip.see">
                                            <EyeIcon class="w-4 h-4" />
                                        </Link>
                                        <Link :href="route('CentroCostos.table', clasegenerica.id)"
                                            class="p-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-emerald-500 hover:text-white transition-all transform hover:scale-110"
                                            v-tooltip="lang().tooltip.Reporte">
                                            <DocumentIcon class="w-4 h-4" />
                                        </Link>
                                        <button @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                            class="p-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-amber-500 hover:text-white transition-all transform hover:scale-110"
                                            v-tooltip="lang().tooltip.edit">
                                            <PencilIcon class="w-4 h-4" />
                                        </button>
                                        <button v-show="can(['delete centroCostos'])"
                                            @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                            class="p-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-red-500 hover:text-white transition-all transform hover:scale-110"
                                            v-tooltip="lang().tooltip.delete">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
</td>

                                <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400 font-medium">#{{
                                    (currentPage - 1) * data.params.perPage + index + 1 }}</td>

                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ clasegenerica.nombre
                                        }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate"
                                        :title="clasegenerica.descripcion">
                                        {{ clasegenerica.descripcion }}
                                    </div>
                                </td>

                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-amber-600 dark:text-amber-400">
                                    {{ formatPesosCol(clasegenerica.mano_obra_estimada) }}
                                </td>

                                <td v-show="can(['update centroCostos'])" class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-md">
                                        {{ clasegenerica.Zouna }}
                                    </span>
                                </td>

                                <td v-show="can(['update centroCostos'])" class="px-4 py-4">
                                    <div v-if="clasegenerica.supervi && clasegenerica.supervi.length"
                                        class="flex flex-wrap gap-1">
                                        <span v-for="(superv, inde) in clasegenerica.supervi" :key="inde"
                                            class="text-[10px] px-1.5 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg border border-blue-100 dark:border-blue-800">
                                            {{ superv.trim() }}
                                        </span>
                                    </div>
                                    <span v-else class="text-[10px] text-gray-400 italic">Sin supervisor</span>
                                </td>

                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-center">
                                    <span
                                        :class="clasegenerica.activo ? 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10' : 'text-red-500 bg-red-50 dark:bg-red-500/10'"
                                        class="p-1 px-2 rounded-full text-lg leading-none inline-block">
                                        {{ clasegenerica.activo ? '●' : '○' }}
                                    </span>
                                </td>

                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-center">
                                    <div :class="clasegenerica.ValidoParaFacturar ? 'text-blue-500 bg-blue-50 dark:bg-blue-500/10' : 'text-gray-400 bg-gray-50 dark:bg-gray-500/10'"
                                        class="p-1 px-2 rounded-full text-xs font-bold inline-block border border-current">
                                        {{ clasegenerica.ValidoParaFacturar ? 'FACTURABLE' : 'INTERNO' }}
                                    </div>
                                </td>

                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-xs font-mono text-gray-400">
                                    {{ clasegenerica.clasificacion }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
</div>

                <!-- Pagination Section -->
                <div
                    class="p-4 bg-gray-50/50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando registros del <span class="font-semibold">{{ (currentPage - 1) * data.params.perPage + 1 }}</span> al <span
                            class="font-semibold">{{ Math.min(currentPage * data.params.perPage, totalFiltered) }}</span> de un total de <span
                            class="font-semibold">{{ totalFiltered }}</span>
                    </p>
                    <div class="flex items-center gap-2">
                        <button
                            @click="currentPage = 1"
                            :disabled="currentPage === 1"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            ««
                        </button>
                        <button
                            @click="prevPage"
                            :disabled="currentPage === 1"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            «
                        </button>

                        <span class="text-sm px-2">
                            Página {{ currentPage }} de {{ totalPages }}
                        </span>

                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            »
                        </button>
                        <button
                            @click="currentPage = totalPages"
                            :disabled="currentPage === totalPages"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            »»
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>


</template>
