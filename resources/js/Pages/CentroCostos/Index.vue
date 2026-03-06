<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, onUnmounted, reactive, watch, computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { throttle, pickBy, debounce } from 'lodash';

import Create from '@/Pages/CentroCostos/Create.vue';
import Edit from '@/Pages/CentroCostos/Edit.vue';
import Delete from '@/Pages/CentroCostos/Delete.vue';
import CostDetailsModal from '@/Pages/CentroCostos/CostDetailsModal.vue';

import { ChevronUpDownIcon, DocumentIcon, EyeIcon, PencilIcon, TrashIcon, PlusIcon, CurrencyDollarIcon, StarIcon, ChevronDownIcon } from '@heroicons/vue/24/solid';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { formatPesosCol } from '@/global.ts';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

const props = defineProps({
    title: String,
    fromController: Object,
    breadcrumbs: Object,
    numberPermissions: Object,
    nombresTabla: Array,
    listaSupervisores: Object,
    losSelect: Array,
    filters: Object,
    popularCenters: Object,
});

const data = reactive({
    generico: null,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    costDetailsOpen: false,
    params: {
        field: props.filters.field,
        order: props.filters.order,
        columnFilters: props.filters.columnFilters || {},
    },
    columnFilters: reactive({}),
});

// Load filters from localStorage
const loadFiltersFromStorage = () => {
    const savedFilters = localStorage.getItem('centroCostosFilters');
    if (savedFilters) {
        try {
            const parsed = JSON.parse(savedFilters);
            Object.assign(data.columnFilters, parsed);
            // Sync with params if needed, but params usually come from server/props on initial load
        } catch (e) {
            console.error('Error loading filters', e);
        }
    }
};

props.nombresTabla[1].forEach(field => {
    if (field) data.columnFilters[field] = '';
});

loadFiltersFromStorage(); // Apply saved filters after initialization

const allData = ref(props.fromController.data);
const totalReal = ref(props.fromController.total);
const nextPageUrl = ref(props.fromController.next_page_url);
const loading = ref(false);
const initialLoading = ref(true); // Para el skeleton inicial
const scrollContainer = ref(null);

// Simular carga progresiva inicial
const progressiveLoad = () => {
    if (nextPageUrl.value && !loading.value) {
        loadMore();
        // Seguir cargando progresivamente cada 3-5 segundos para no saturar
        setTimeout(progressiveLoad, 3500);
    }
};

onMounted(() => {
    // Si hay datos iniciales, desactivamos el loading inicial
    if (allData.value.length > 0) {
        initialLoading.value = false;
    }

    scrollContainer.value = document.querySelector('.scrollbar-table');
    if (scrollContainer.value) scrollContainer.value.addEventListener('scroll', handleScroll);

    // Iniciar carga en segundo plano después de 1 segundo
    setTimeout(progressiveLoad, 1000);
});

const clientSideFilteredData = computed(() => {
    let result = [...allData.value];
    Object.entries(data.columnFilters).forEach(([field, value]) => {
        if (value) {
            const filterValue = String(value).toLowerCase().trim();
            if (filterValue === '' || filterValue.startsWith('seleccione ')) return;

            result = result.filter(item => {
                const itemValue = item[field];
                if (field === 'Zouna') return item.zona && String(item.zona.nombre).toLowerCase().includes(filterValue);
                if (field === 'supervisores') return item.users && item.users.some(u => String(u.name).toLowerCase().includes(filterValue));
                if (field === 'activo' || field === 'ValidoParaFacturar') return Boolean(itemValue) === (filterValue === 'si');
                return itemValue && String(itemValue).toLowerCase().includes(filterValue);
            });
        }
    });
    return result;
});

const serverSearch = debounce(() => {
    data.params.columnFilters = pickBy(data.columnFilters, (value) => {
        if (!value) return false;
        const lowVal = String(value).toLowerCase().trim();
        return lowVal !== '' && !lowVal.startsWith('seleccione ');
    });
    localStorage.setItem('centroCostosFilters', JSON.stringify(data.columnFilters)); // Save filters to localStorage
    router.get(route('CentroCostos.index'), pickBy(data.params), {
        replace: true,
        preserveState: true,
        onSuccess: (page) => {
            allData.value = page.props.fromController.data;
            nextPageUrl.value = page.props.fromController.next_page_url;
        }
    });
}, 300);

watch(clientSideFilteredData, (newData) => {
    if (newData.length === 0) {
        serverSearch();
    }
});


const loadMore = throttle(() => {
    if (!nextPageUrl.value || loading.value) return;
    loading.value = true;
    router.get(nextPageUrl.value, pickBy(data.params), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: (page) => {
            allData.value = [...allData.value, ...page.props.fromController.data];
            nextPageUrl.value = page.props.fromController.next_page_url;
            loading.value = false;
        },
        onError: () => loading.value = false,
    });
}, 300);

const handleScroll = () => {
    const container = scrollContainer.value;
    if (container && container.scrollTop + container.clientHeight >= container.scrollHeight - 50) {
        loadMore();
    }
};

onMounted(() => {
    scrollContainer.value = document.querySelector('.scrollbar-table');
    if (scrollContainer.value) scrollContainer.value.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    if (scrollContainer.value) scrollContainer.value.removeEventListener('scroll', handleScroll);
});

const order = (field) => {
    if (field) {
        data.params.field = field.replace(/ /g, "_");
        data.params.order = data.params.order === "asc" ? "desc" : "asc";
    }
};

watch(() => data.params, () => {
    router.get(route('CentroCostos.index'), pickBy(data.params), {
        replace: true,
        preserveState: true,
        onSuccess: (page) => {
            allData.value = page.props.fromController.data;
            nextPageUrl.value = page.props.fromController.next_page_url;
        }
    });
}, { deep: true });

// Hardcoded popular centers (You might want to fetch this from API later)

</script>

<template>

    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-4">
                        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                            {{ props.title }} <span
                                class="text-amber-500 text-sm font-medium ml-2 px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 rounded-full">{{
                                    totalReal }} Total</span>
                        </h1>
                        <Menu as="div" class="relative inline-block text-left z-30">
                            <div>
                                <MenuButton
                                    class="inline-flex w-full justify-center rounded-md bg-white/20 px-3 py-1.5 text-sm font-medium text-amber-600 hover:bg-amber-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 border border-amber-200 shadow-sm">
                                    Mayor mano de obra
                                    <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5 text-amber-400 hover:text-amber-500"
                                        aria-hidden="true" />
                                </MenuButton>
                            </div>

                            <transition enter-active-class="transition duration-100 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0">
                                <MenuItems
                                    class="absolute left-0 mt-2 w-56 origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
                                    <div class="px-1 py-1">
                                        <MenuItem v-for="center in props.popularCenters" :key="center.id"
                                            v-slot="{ active }">
                                        <Link :href="route('CentroCostos.table', center.id)" :class="[
                                            active ? 'bg-amber-500 text-white' : 'text-gray-900',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]">
                                            <DocumentIcon
                                                :class="[active ? 'text-white' : 'text-amber-500', 'mr-2 h-5 w-5']"
                                                aria-hidden="true" />
                                            {{ center.nombre }}
                                        </Link>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Gestión y control de presupuesto por centros de costo operativos.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <PrimaryButton v-if="can(['isSuper'])"
                        class="shadow-lg shadow-primary/20 transition-all hover:scale-105"
                        @click="data.createOpen = true">
                        <PlusIcon class="w-4 h-4 mr-1" />
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <Create :show="data.createOpen" @close="data.createOpen = false"
                        :listaSupervisores="props.listaSupervisores" :title="props.title" />
                    <Edit :show="data.editOpen" @close="data.editOpen = false"
                        :listaSupervisores="props.listaSupervisores" :CentroCosto="data.generico"
                        :losSelect="props.losSelect" :title="props.title" />
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :CentroCosto="data.generico"
                        :title="props.title" />
                    <CostDetailsModal :show="data.costDetailsOpen" @close="data.costDetailsOpen = false"
                        :centroCosto="data.generico" />
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto scrollbar-table max-h-[75vh]">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 z-20 bg-white dark:bg-gray-800 shadow-sm">
                            <tr
                                class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    @click="order(nombresTabla[1][indiceN])"
                                    class="px-4 py-3 cursor-pointer hover:text-amber-600 transition-colors">
                                    <div class="flex items-center gap-1">
                                        {{ titulos }}
                                        <ChevronUpDownIcon v-if="nombresTabla[1][indiceN]"
                                            class="w-4 h-4 text-gray-400" />
                                    </div>
                                </th>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-900/50">
                                <th v-for="(field, index) in nombresTabla[1]" :key="index" class="px-2 py-1.5">
                                    <TextInput
                                        v-if="field && field !== 'supervisores' && field !== 'Zouna' && field !== 'activo' && field !== 'ValidoParaFacturar'"
                                        v-model="data.columnFilters[field]" type="text"
                                        class="h-8 text-xs rounded-lg border-gray-200 focus:ring-amber-500 w-full"
                                        :placeholder="`Filtrar ${nombresTabla[0][index]}`" />
                                    <v-select v-if="field === 'Zouna'" v-model="data.columnFilters[field]"
                                        :options="props.losSelect['zona'].map(z => z.label)" placeholder="Filtrar..."
                                        class="custom-v-select-sm"></v-select>
                                    <TextInput v-if="field === 'supervisores'" v-model="data.columnFilters[field]"
                                        type="text"
                                        class="h-8 text-xs rounded-lg border-gray-200 focus:ring-amber-500 w-full"
                                        placeholder="Filtrar..." />
                                    <v-select v-if="field === 'activo' || field === 'ValidoParaFacturar'"
                                        v-model="data.columnFilters[field]" :options="['Si', 'No']"
                                        placeholder="Filtrar..." class="custom-v-select-sm"></v-select>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <!-- Skeleton Loading State -->
                            <tr v-if="initialLoading" v-for="n in 5" :key="'skeleton-' + n" class="animate-pulse">
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32 mb-2"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-48"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8 mx-auto"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24 mx-auto"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12"></div>
                                </td>
                            </tr>

                            <tr v-else v-for="(clasegenerica, index) in clientSideFilteredData" :key="clasegenerica.id"
                                class="group hover:bg-amber-50/30 dark:hover:bg-amber-900/5 transition-colors">
                                <td v-if="can(['update centroCostos'])" class="px-4 py-4 whitespace-nowrap">
                                    <div
                                        class="flex items-center gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                                        <button @click="(data.costDetailsOpen = true), (data.generico = clasegenerica)"
                                            class="p-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-green-500 hover:text-white transition-all transform hover:scale-110"
                                            v-tooltip="'Ver Detalles de Costos'">
                                            <CurrencyDollarIcon class="w-4 h-4" />
                                        </button>
                                        <!--                                        <Link :href="route('CentroCostos.show', clasegenerica)" class="p-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-blue-500 hover:text-white transition-all transform hover:scale-110" v-tooltip="lang().tooltip.see"><EyeIcon class="w-4 h-4" /></Link>-->
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
                                <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400 font-medium">#{{ index + 1
                                }}</td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ clasegenerica.nombre
                                    }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate"
                                        :title="clasegenerica.descripcion">{{ clasegenerica.descripcion }}</div>
                                </td>
                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-amber-600 dark:text-amber-400">
                                    {{ formatPesosCol(clasegenerica.mano_obra_estimada) }}</td>
                                <td v-show="can(['update centroCostos'])" class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-md">{{
                                            clasegenerica.zona ? clasegenerica.zona.nombre : 'N/A' }}</span>
                                </td>
                                <td v-show="can(['update centroCostos'])" class="px-4 py-4">
                                    <div v-if="clasegenerica.users && clasegenerica.users.length"
                                        class="flex flex-wrap gap-1">
                                        <span v-for="user in clasegenerica.users" :key="user.id"
                                            class="text-[10px] px-1.5 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg border border-blue-100 dark:border-blue-800">{{
                                                user.name.trim() }}</span>
                                    </div>
                                    <span v-else class="text-[10px] text-gray-400 italic">Sin supervisor</span>
                                </td>
                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-center">
                                    <span
                                        :class="clasegenerica.activo ? 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10' : 'text-red-500 bg-red-50 dark:bg-red-500/10'"
                                        class="p-1 px-2 rounded-full text-lg leading-none inline-block">{{
                                            clasegenerica.activo ? '●' : '○' }}</span>
                                </td>
                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-center">
                                    <div :class="clasegenerica.ValidoParaFacturar ? 'text-blue-500 bg-blue-50 dark:bg-blue-500/10' : 'text-gray-400 bg-gray-50 dark:bg-gray-500/10'"
                                        class="p-1 px-2 rounded-full text-xs font-bold inline-block border border-current">
                                        {{ clasegenerica.ValidoParaFacturar ? 'FACTURABLE' : 'INTERNO' }}</div>
                                </td>
                                <td v-show="can(['update centroCostos'])"
                                    class="px-4 py-4 whitespace-nowrap text-xs font-mono text-gray-400">{{
                                        clasegenerica.clasificacion }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="loading" class="flex justify-center items-center p-4">
                        <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="ml-2 text-sm text-gray-500">Cargando más...</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style>
.custom-v-select-sm .vs__dropdown-toggle {
    height: 2rem;
    border-radius: 0.5rem;
}

.custom-v-select-sm .vs__search {
    font-size: 0.75rem;
}

.custom-v-select-sm .vs__selected {
    font-size: 0.75rem;
    margin: 0;
    padding: 0 0.5rem;
}
</style>
