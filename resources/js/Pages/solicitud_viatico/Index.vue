<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import Create from '@/Pages/solicitud_viatico/Create.vue';
import Edit from '@/Pages/solicitud_viatico/Edit.vue';
import Delete from '@/Pages/solicitud_viatico/Delete.vue';
import Detalle from '@/Pages/solicitud_viatico/Detalle.vue';
import Aprobar from '@/Pages/solicitud_viatico/Aprobar.vue';
import Legalizar from '@/Pages/solicitud_viatico/Legalizar.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import { reactive, watch, onMounted, computed, ref } from 'vue';
import { ChevronUpDownIcon, PencilIcon, TrashIcon, CurrencyDollarIcon, ShieldExclamationIcon, ChartBarIcon } from '@heroicons/vue/24/solid';
import { formatDate, number_format, formatPesosCol } from '@/global.ts';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import DeleteBulk from '@/Pages/viatico/DeleteBulk.vue';


const { _, debounce, pickBy } = pkg

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
        // search: props.filters.search,
        // search2: props.filters.search2, //persona
        // search3: props.filters.search3, //centro_Array
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
        // Filtros por columna
        col_solicitante: props.filters.col_solicitante ?? '',
        col_ciudad: props.filters.col_ciudad ?? '',
        col_obra: props.filters.col_obra ?? '',
        col_saldo: props.filters.col_saldo ?? '',
        col_fecha: props.filters.col_fecha ?? '',
        col_centro: props.filters.col_centro ?? '',
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

    DetalleOpen: false,
    sol_viatico: false,
})

// Todos los datos cargados del servidor
const allData = ref(props.fromController.data || [])

// Página actual para paginación frontend
const currentPage = ref(1)

// Datos filtrados localmente
const filteredData = computed(() => {
    let result = [...allData.value];

    // Filtro de búsqueda general (descripción)
    if (data.params.search) {
        const search = data.params.search.toLowerCase();
        result = result.filter(item =>
            (item.descripcion || '').toLowerCase().includes(search)
        );
    }

    // Filtro por persona
    if (data.params.search2) {
        const search2 = data.params.search2.toLowerCase();
        result = result.filter(item =>
            (item.Solicitante || '').toLowerCase().includes(search2)
        );
    }

    // Filtro por centro de costo
    if (data.params.search3) {
        const search3 = data.params.search3;
        result = result.filter(item =>
            (item.centrou || '') === search3 ||
            (item.centro_costo_id === search3.id)
        );
    }

    // Filtros por columna
    if (data.params.col_solicitante) {
        const filter = data.params.col_solicitante.toLowerCase();
        result = result.filter(item =>
            (item.Solicitante || '').toLowerCase().includes(filter)
        );
    }

    if (data.params.col_ciudad) {
        const filter = data.params.col_ciudad.toLowerCase();
        result = result.filter(item =>
            (item.Ciudad || '').toLowerCase().includes(filter)
        );
    }

    if (data.params.col_fecha) {
        result = result.filter(item =>
            item.Fechasol === data.params.col_fecha
        );
    }

    if (data.params.col_saldo) {
        if (data.params.col_saldo === 'pendiente') {
            result = result.filter(item => (item.saldo_sol || 0) > 0);
        } else if (data.params.col_saldo === 'cerrado') {
            result = result.filter(item => (item.saldo_sol || 0) <= 0);
        }
    }

    if (data.params.col_obra) {
        const filter = data.params.col_obra.toLowerCase();
        result = result.filter(item =>
            (item.ObraServicio || '').toLowerCase().includes(filter)
        );
    }

    if (data.params.col_centro) {
        const centro = data.params.col_centro;
        result = result.filter(item => {
            if (typeof centro === 'object' && centro !== null) {
                return item.centrou === centro.name || item.centro_costo_id === centro.id;
            }
            return (item.centrou || '').toLowerCase().includes(String(centro).toLowerCase());
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
        return; // No resetear cuando cambia perPage
    }
    currentPage.value = 1;
}, { deep: true });

// Funciones de paginación
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

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
    params.perPage = 99999; // Solicitar todos los datos para paginación en frontend
    router.get(route("solicitud_viatico.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            allData.value = page.props.fromController.data || [];
            currentPage.value = 1;
        }
    })
};

// <!--<editor-fold desc="Resumen Local">-->
const dataResumen = reactive({
    search: '',
    show: false
})

const resumenBeneficiarios = computed(() => {
    const map = {};
    allData.value.forEach(item => {
        const key = item.Solicitante || 'Desconocido';
        if (!map[key]) {
            map[key] = { nombre: key, saldo: 0, consignado: 0, solicitado: 0, legalizado: 0 };
        }
        map[key].saldo += Number(item.saldo_sol) || 0;
        map[key].consignado += Number(item.consignacion_sum_valor_consig ?? item.TotalConsignado) || 0;
        map[key].legalizado += Number(item.consignacion_sum_valor_legalizado) || 0;
        map[key].solicitado += Number(item.viaticos_sum_gasto ?? item.Totalsolicitado) || 0;
    });

    let result = Object.values(map).map(r => ({
        ...r,
        porcentaje: r.consignado > 0 ? Math.round((r.legalizado / r.consignado) * 100) : 0
    }));

    if (dataResumen.search) {
        const s = dataResumen.search.toLowerCase();
        result = result.filter(r => r.nombre.toLowerCase().includes(s));
    }
    return result.sort((a, b) => b.consignado - a.consignado);
});

const resumenCentrosCosto = computed(() => {
    const map = {};
    allData.value.forEach(item => {
        const key = item.centrou || 'Sin Centro';
        if (!map[key]) {
            map[key] = { nombre: key, saldo: 0, consignado: 0, solicitado: 0, legalizado: 0 };
        }
        map[key].saldo += Number(item.saldo_sol) || 0;
        map[key].consignado += Number(item.consignacion_sum_valor_consig ?? item.TotalConsignado) || 0;
        map[key].legalizado += Number(item.consignacion_sum_valor_legalizado) || 0;
        map[key].solicitado += Number(item.viaticos_sum_gasto ?? item.Totalsolicitado) || 0;
    });

    let result = Object.values(map).map(r => ({
        ...r,
        porcentaje: r.consignado > 0 ? Math.round((r.legalizado / r.consignado) * 100) : 0
    }));

    if (dataResumen.search) {
        const s = dataResumen.search.toLowerCase();
        result = result.filter(r => r.nombre.toLowerCase().includes(s));
    }
    return result.sort((a, b) => b.consignado - a.consignado);
});

const granTotales = computed(() => {
    const totals = allData.value.reduce((acc, item) => {
        acc.saldo += Number(item.saldo_sol) || 0;
        acc.consignado += Number(item.consignacion_sum_valor_consig ?? item.TotalConsignado) || 0;
        acc.legalizado += Number(item.consignacion_sum_valor_legalizado) || 0;
        acc.solicitado += Number(item.viaticos_sum_gasto ?? item.Totalsolicitado) || 0;
        return acc;
    }, { saldo: 0, consignado: 0, solicitado: 0, legalizado: 0 });

    totals.porcentaje = totals.consignado > 0 ? Math.round((totals.legalizado / totals.consignado) * 100) : 0;
    return totals;
});
// <!--</editor-fold>-->

// <!--<editor-fold desc="order, watchclone, select">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"

    // Ordenar datos localmente
    const fieldMap = {
        'Solicitante': 'Solicitante',
        'Fechasol': 'Fechasol',
        'Ciudad': 'Ciudad',
        'saldo_sol': 'saldo_sol',
        'ObraServicio': 'ObraServicio'
    };

    const sortField = fieldMap[field] || field;
    const sortOrder = data.params.order;

    filteredData.value.sort((a, b) => {
        const aVal = a[sortField] ?? '';
        const bVal = b[sortField] ?? '';

        if (typeof aVal === 'number' && typeof bVal === 'number') {
            return sortOrder === 'asc' ? aVal - bVal : bVal - aVal;
        }

        const aStr = String(aVal).toLowerCase();
        const bStr = String(bVal).toLowerCase();

        if (sortOrder === 'asc') {
            return aStr.localeCompare(bStr);
        } else {
            return bStr.localeCompare(aStr);
        }
    });
}

const clearColFilters = () => {
    data.params.col_solicitante = ''
    data.params.col_ciudad = ''
    data.params.col_obra = ''
    data.params.col_saldo = ''
    data.params.col_fecha = ''
    data.params.col_centro = ''
}
const hasColFilters = computed(() =>
    data.params.col_solicitante || data.params.col_ciudad ||
    data.params.col_obra || data.params.col_saldo ||
    data.params.col_fecha || data.params.col_centro
)

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        paginatedData.value.forEach((viatico) => {
            data.selectedId.push(viatico.id)
        })
    }
}
const select = () => data.multipleSelect = paginatedData.value.length === data.selectedId.length;
// <!--</editor-fold>-->


// const form = useForm({ })
// watchEffect(() => { })

const totalSaldo = computed(() => {
    return filteredData.value.reduce((acc, item) => acc + (item.saldo || 0), 0);
});


// text // number // dinero // date // datetime // foreign
const subtitulos = [
    { order: 'gasto', label: 'gasto', type: 'number' },
    { order: 'user_id', label: 'user_id', type: 'foreign', nameid: 'userino' },
    { order: 'Consignaciona', label: 'Consignaciona', type: 'number' },
    { order: 'fechaconsig', label: 'fechaconsig', type: 'date' },
    { order: 'saldo', label: 'saldo', type: 'number' },
    { order: 'fecha_inicial', label: 'fecha_inicial', type: 'date' },
    { order: 'fecha_final', label: 'fecha_final', type: 'date' },
    { order: 'numerodias', label: 'numerodias', type: 'number' },
    // {order: 'legalizacion', label: 'legalizacion', type: 'text'},
    { order: 'valor_legalizacion', label: 'valor_legalizacion', type: 'dinero' },
    { order: 'descripcion_legalizacion', label: 'descripcion_legalizacion', type: 'text' },
    { order: 'fecha_legalizacion', label: 'fecha_legalizacion', type: 'datetime' },
    { order: 'centro_costo_id', label: 'centro_costo_id', type: 'foreign', nameid: 'centrou' },
    { order: 'descripcion', label: 'descripcion', type: 'text2' },
    // { order: 'inventario', label: 'inventario', type: 'foreign',nameid:'nombre'},
];

const titulos = [
    { order: 'Solicitante', label: 'Solicitante', type: 'text' },
    { order: 'Fechasol', label: 'Fechasol', type: 'date' },
    { order: 'Ciudad', label: 'Ciudad', type: 'text' },
    // {order: 'ObraServicio', label: 'ObraServicio', type: 'text'},
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
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true" v-if="can(['create viatico'])">
                        {{ lang().button.new }}
                    </PrimaryButton>

                    <Create v-if="can(['create viatico'])" :numberPermissions="props.numberPermissions"
                        :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        :losSelect=props.losSelect />

                    <Edit v-if="can(['update viatico'])" :titulos="titulos" :numberPermissions="props.numberPermissions"
                        :show="data.editOpen" @close="data.editOpen = false" :solicitud_viaticoa="data.sol_viatico"
                        :title="props.title" :losSelect=props.losSelect />


                    <Aprobar v-if="can(['update2 viatico'])" :titulos="titulos"
                        :numberPermissions="props.numberPermissions" :show="data.AprobarOpen"
                        @close="data.AprobarOpen = false" :solicitud_viaticoa="data.sol_viatico" :title="props.title"
                        :losSelect=props.losSelect />


                    <Legalizar v-if="can(['update3 viatico'])" :titulos="titulos"
                        :numberPermissions="props.numberPermissions" :show="data.LegalizarOpen"
                        @close="data.LegalizarOpen = false" :solicitud_viaticoa="data.sol_viatico" :title="props.title"
                        :losSelect=props.losSelect />

                    <!--                    <Delete v-if="can(['delete viatico'])" :numberPermissions="props.numberPermissions"-->
                    <!--                            :show="data.deleteOpen" @close="data.deleteOpen = false" :solicitud_viaticoa="data.sol_viatico"-->
                    <!--                            :title="props.title"/>-->
                    <DeleteBulk v-if="can(['delete viatico'])" :show="data.deleteBulkOpen"
                        @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                        :selectedId="data.selectedId" :title="props.title" />

                    <Detalle :show="data.DetalleOpen" :viaticoa="data.sol_viatico" @close="data.DetalleOpen = false"
                        :title="props.title" maintitle="Viaticos" />
                </div>
                <p class="mt-1 text-lg mx-auto">Los viaticos solo se podran eliminar el mismo día que se crearon</p>

            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="grid grid-cols-3 justify-between p-2">
                    <div class="flex gap-2 ">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton v-if="can(['delete viatico'])" @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length !== 0 && can(['delete viatico'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <!-- Fila 1: Títulos con ordenamiento -->
                            <tr class="dark:bg-gray-900/50 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th v-if="numberPermissions > 1" class="px-2 py-4">Accion</th>

                                <!-- Solicitante -->
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('Solicitante')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.Solicitante }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <!-- Fechasol -->
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('Fechasol')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.Fechasol }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <!-- Ciudad -->
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('Ciudad')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.Ciudad }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <!-- Centro de Costo -->
                                <th class="px-2 py-4">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.centrou }}</span>
                                    </div>
                                </th>
                                <!-- Total Solicitado -->
                                <th class="px-2 py-4">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.Totalsolicitado }}</span>
                                    </div>
                                </th>
                                <!-- Consignado -->
                                <th class="px-2 py-4">
                                    <div class="flex justify-between items-center">
                                        <span>Consignado</span>
                                    </div>
                                </th>
                                <!-- Saldo -->
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('saldo_sol')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.saldo_sol }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <!-- ObraServicio -->
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('ObraServicio')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.ObraServicio }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <!-- Ver Detalles -->
                                <th class="px-2 py-4">
                                    <div class="flex justify-between items-center">
                                        <span>Ver detalles</span>
                                    </div>
                                </th>
                            </tr>

                            <!-- Fila 2: Filtros por columna -->
                            <tr class="dark:bg-gray-800/60 bg-gray-50 text-left">
                                <!-- checkbox -->
                                <td class="px-2 py-1"></td>
                                <!-- accion -->
                                <td v-if="numberPermissions > 1" class="px-2 py-1">
                                    <button v-if="hasColFilters" @click="clearColFilters"
                                        title="Limpiar todos los filtros"
                                        class="text-xs text-red-500 hover:text-red-700 whitespace-nowrap font-semibold">
                                        ✕ Limpiar
                                    </button>
                                </td>
                                <!-- Solicitante filter -->
                                <td class="px-1 py-1">
                                    <input v-model="data.params.col_solicitante" type="text" placeholder="Filtrar..."
                                        class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary" />
                                </td>
                                <!-- Fechasol filter -->
                                <td class="px-1 py-1">
                                    <input v-model="data.params.col_fecha" type="date"
                                        class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary" />
                                </td>
                                <!-- Ciudad filter -->
                                <td class="px-1 py-1">
                                    <input v-model="data.params.col_ciudad" type="text" placeholder="Filtrar..."
                                        class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary" />
                                </td>
                                <!-- Centro costo (yes filter) -->
                                <td class="px-1 py-1 " colspan="2">
                                    <vSelect v-if="props.numberPermissions > 1" v-model="data.params.col_centro"
                                        :options="props.losSelect[1]" label="name"
                                        class="block w-5/6 md:w-5/6 lg:w-full mx-1 mt-1 rounded-lg"></vSelect>
                                </td>
                                <!-- Total solicitado (no filter) -->
                                <!-- <td class="px-1 py-1"></td> -->
                                <!-- Consignado (no filter) -->
                                <td class="px-1 py-1"></td>
                                <!-- Saldo filter -->
                                <td class="px-1 py-1">
                                    <select v-model="data.params.col_saldo"
                                        class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary">
                                        <option value="">Todos</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="cerrado">Cerrado</option>
                                    </select>
                                </td>
                                <!-- ObraServicio filter -->
                                <td class="px-1 py-1">
                                    <input v-model="data.params.col_obra" type="text" placeholder="Filtrar..."
                                        class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary" />
                                </td>
                                <!-- Ver detalles (no filter) -->
                                <td class="px-1 py-1"></td>
                            </tr>
                        </thead>
                        <tbody v-if="totalFiltered > 0">
                            <tr v-for="(claseFromController, indexu) in paginatedData" :key="indexu"
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
                                            <!-- <InfoButton v-show="can(['update viatico'])" type="button"
                                                @click="(data.editOpen = true), (data.sol_viatico = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon :class="classbotones" />
                                            </InfoButton> -->

                                            <InfoButton
                                                v-show="can(['update2 viatico']) && claseFromController['saldo_sol'] > 0"
                                                type="button" :thecolor="'green'"
                                                @click="(data.AprobarOpen = true), (data.sol_viatico = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.consignar">
                                                <CurrencyDollarIcon :class="classbotones" />
                                            </InfoButton>
                                            <InfoButton
                                                v-show="can(['update3 viatico']) && claseFromController.Consignaciona?.length !== 0"
                                                type="button" :thecolor="'gray'"
                                                @click="(data.LegalizarOpen = true), (data.sol_viatico = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.legalizar">
                                                <ShieldExclamationIcon :class="classbotones" />
                                            </InfoButton>
                                            <!--                                        <DangerButton v-show="can(['delete viatico'])" type="button"-->
                                            <!--                                                      @click="(data.deleteOpen = true), (data.sol_viatico = claseFromController)"-->
                                            <!--                                                      class="px-2 py-1.5 rounded-none"-->
                                            <!--                                                      v-tooltip="lang().tooltip.delete">-->
                                            <!--                                            <TrashIcon :class="classbotones"/>-->
                                            <!--                                        </DangerButton>-->
                                        </div>
                                    </div>
                                </td>

                                <!--{{claseFromController}}-->
                                <td class="whitespace-nowrap py-2 px-2"> {{ claseFromController['Solicitante'] }}</td>
                                <td class="whitespace-nowrap py-2 px-2"> {{ claseFromController['Fechasol'] }}</td>
                                <td class="whitespace-nowrap py-2 px-2"> {{ claseFromController['Ciudad'] }}</td>
                                <td class="whitespace-nowrap py-2 px-2"> {{ claseFromController['centrou'] }}</td>
                                <td class="whitespace-nowrap py-2 px-2"> {{
                                    formatPesosCol(claseFromController['viaticos_sum_gasto'] ??
                                        claseFromController['Totalsolicitado']) }}</td>
                                <td class="whitespace-nowrap py-2 px-2"> {{
                                    formatPesosCol(claseFromController['consignacion_sum_valor_consig'] ??
                                        claseFromController['TotalConsignado']) }}</td>
                                <td class="whitespace-nowrap py-2 px-2"
                                    :class="claseFromController['saldo_sol'] > 0 ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-green-600 dark:text-green-400'">
                                    {{ formatPesosCol(claseFromController['saldo_sol']) }}</td>
                                <td class="p-2 text-sm"> {{ claseFromController['ObraServicio'] }}</td>
                                <td v-tooltip="lang().tooltip.detail"
                                    @click="(data.DetalleOpen = true), (data.sol_viatico = claseFromController)"
                                    class="whitespace-nowrap py-4 px-2 sm:py-3 cursor-pointer text-blue-600 dark:text-blue-400 font-bold underline">
                                    {{ (claseFromController.Losviaticos ?? claseFromController.viaticos ?? []).length }}
                                    Viaticos
                                </td>
                            </tr>


                            <tr class="border-t border-gray-600">
                                <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> -</td>
                                <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> -</td>
                                <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> -</td>
                                <!--                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>-->
                                <!--                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>-->
                                <!--                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>-->
                                <!--                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>-->
                                <!--                            <td><strong class="text-sm">Total saldo: <br></strong> {{ formatPesosCol(props.totalsaldo) }}</td>-->
                                <!--                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>-->
                                <!--                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>-->
                                <!--                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - </td>-->
                                <!--                            <td><strong class="text-sm">Total legalizado: <br></strong> {{ formatPesosCol(totallegalizado) }}</td>-->
                            </tr>
                        </tbody>
                        <div v-else class="flex flex-col items-center justify-center py-16 mx-auto">
                            <p class="text-3xl font-semibold tracking-wide ml-[10px]
                                bg-gradient-to-r from-slate-900 via-slate-500 to-slate-400
                                bg-clip-text text-transparent">
                                Sin registros 😭
                            </p>
                            <div class="mt-4 ml-[10px] w-[100px] h-1 rounded-full bg-indigo-600" />
                        </div>
                    </table>
                </div>
                <div v-if="totalFiltered > 0"
                    class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Mostrando {{ (currentPage - 1) * data.params.perPage + 1 }} - {{ Math.min(currentPage *
                            data.params.perPage, totalFiltered) }} de {{ totalFiltered }} registros
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="currentPage = 1" :disabled="currentPage === 1"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            ««
                        </button>
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            «
                        </button>

                        <span class="text-sm px-2">
                            Página {{ currentPage }} de {{ totalPages }}
                        </span>

                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            »
                        </button>
                        <button @click="currentPage = totalPages" :disabled="currentPage === totalPages"
                            class="px-2 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            »»
                        </button>
                    </div>
                </div>
            </div>
            <!-- ─── SECCIÓN DE RESUMEN DE TOTALES ───────────────────────────── -->
            <div v-if="numberPermissions > 8"
                class="mt-8 bg-white dark:bg-gray-800/40 rounded-2xl border border-gray-200 dark:border-gray-700/50 overflow-hidden shadow-sm">
                <div
                    class="p-6 border-b border-gray-200 dark:border-gray-700/50 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                            <ChartBarIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Resumen consolidado
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Totales calculados localmente sobre
                                todos los registros cargados</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <TextInput v-model="dataResumen.search" placeholder="Filtrar por persona o centro..."
                            class="py-1.5 text-sm w-64" />
                        <div class="flex gap-2">
                            <div
                                class="px-3 py-1.5 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 text-center flex flex-col items-center">
                                <span
                                    class="block text-[10px] uppercase font-bold text-green-600 dark:text-green-400">Total
                                    Legalizado</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-sm font-black text-green-700 dark:text-green-300">{{
                                        formatPesosCol(granTotales.legalizado) }}</span>
                                    <span class="text-[10px] font-bold text-green-600 opacity-60">({{
                                        granTotales.porcentaje }}%)</span>
                                </div>
                            </div>
                            <div
                                class="px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700/50 text-center">
                                <span class="block text-[10px] uppercase font-bold text-gray-400">Total
                                    Consignado</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{
                                    formatPesosCol(granTotales.consignado) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700/50">
                    <!-- Resumen por Beneficiario -->
                    <div class="p-0">
                        <div
                            class="px-6 py-3 bg-gray-50/50 dark:bg-gray-800/30 border-b border-gray-200 dark:border-gray-700/50">
                            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Por
                                Beneficiario</h4>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="sticky top-0 bg-white dark:bg-gray-800 shadow-sm z-10 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 dark:border-gray-700">
                                    <tr>
                                        <th class="px-6 py-3">Nombre</th>
                                        <th class="px-6 py-3 text-right">Legalizado</th>
                                        <th class="px-6 py-3 text-right text-[9px]">%</th>
                                        <th class="px-6 py-3 text-right">Consignado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/30">
                                    <tr v-for="res in resumenBeneficiarios" :key="res.nombre"
                                        class="hover:bg-green-50/30 dark:hover:bg-green-900/10 transition-colors group">
                                        <td
                                            class="px-6 py-3 font-medium text-gray-700 dark:text-gray-300 group-hover:text-green-700 transition-colors">
                                            {{ res.nombre }}</td>
                                        <td class="px-6 py-3 text-right tabular-nums font-bold text-green-600">
                                            {{ formatPesosCol(res.legalizado) }}
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right tabular-nums text-[10px] font-bold text-green-500/60">
                                            {{ res.porcentaje }}%
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right tabular-nums text-gray-400 font-medium italic group-hover:text-gray-600">
                                            {{ formatPesosCol(res.consignado) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Resumen por Centro de Costo -->
                    <div class="p-0">
                        <div
                            class="px-6 py-3 bg-gray-50/50 dark:bg-gray-800/30 border-b border-gray-200 dark:border-gray-700/50">
                            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Por
                                Centro de Costo</h4>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="sticky top-0 bg-white dark:bg-gray-800 shadow-sm z-10 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 dark:border-gray-700">
                                    <tr>
                                        <th class="px-6 py-3">Centro</th>
                                        <th class="px-6 py-3 text-right">Legalizado</th>
                                        <th class="px-6 py-3 text-right text-[9px]">%</th>
                                        <th class="px-6 py-3 text-right">Consignado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/30">
                                    <tr v-for="res in resumenCentrosCosto" :key="res.nombre"
                                        class="hover:bg-green-50/30 dark:hover:bg-green-900/10 transition-colors group">
                                        <td
                                            class="px-6 py-3 font-medium text-gray-700 dark:text-gray-300 group-hover:text-green-700 transition-colors">
                                            {{ res.nombre }}</td>
                                        <td class="px-6 py-3 text-right tabular-nums font-bold text-green-600">
                                            {{ formatPesosCol(res.legalizado) }}
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right tabular-nums text-[10px] font-bold text-green-500/60">
                                            {{ res.porcentaje }}%
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right tabular-nums text-gray-400 font-medium italic group-hover:text-gray-600">
                                            {{ formatPesosCol(res.consignado) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ──────────────────────────────────────────────────────────── -->
        </div>
    </AuthenticatedLayout>
</template>
