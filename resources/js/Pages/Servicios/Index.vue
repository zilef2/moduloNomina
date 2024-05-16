<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InfoButton from '@/Components/InfoButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, ref, watch} from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import FilterButtons from '@/Components/tablecomponents/FilterButtons.vue';
import pkg from 'lodash';
import {router} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import {
    ShieldCheckIcon,
    CheckBadgeIcon,
    EyeIcon,
    ChevronUpDownIcon,
    LinkIcon,
    PencilIcon,
    TrashIcon
} from '@heroicons/vue/24/solid';

import {number_format} from '@/global.ts';

import Create from '@/Pages/User/Create.vue';
import Edit from '@/Pages/User/Edit.vue';
import EdiCentro from '@/Pages/User/EdiCentro.vue';
import Delete from '@/Pages/User/Delete.vue';
import DeleteBulk from '@/Pages/User/DeleteBulk.vue';
import Checkbox from '@/Components/Checkbox.vue';
import {usePage, Link} from '@inertiajs/vue3';

const {_, debounce, pickBy} = pkg
const props = defineProps({
    title: String,
    filters: Object,
    users: Object,
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
        search: props.filters.search,
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
    user: null,
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


const toggleColumn = (index) =>
    data.columsVisible[index] = !data.columsVisible[index];


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
    router.get(route("user.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 15))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.users?.data.forEach((user) => {
            data.selectedId.push(user.id)
        })
    }
}
const select = () => {
    if (props.users?.data.length == data.selectedId.length) {
        data.multipleSelect = true
    } else {
        data.multipleSelect = false
    }
}
// <!--</editor-fold>-->

data.params.onlySupervis = data.params.onlySupervis === null ? false : data.params.onlySupervis;

const checkedValues = ref([]);
const handleCheckboxChange = (values) => {
  checkedValues.value = values;
};
</script>

<template>
    <Head :title="props.title"/>

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs"/>
        <p v-if="!props.superviNullCentro" class="text-lg mt-2 mb-5 text-red-600"> Hay supervisores sin centro</p>
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit gap-8">
                    <PrimaryButton v-show="can(['create user'])" class="rounded-md mx-2"
                                   @click="data.createOpen = true">
                        Nuevo Servicio
                    </PrimaryButton>
<!--                    <Link v-show="can(['update user'])" :href="route('IndexTrashed')"-->
<!--                          class=" mx-1 bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary">-->
<!--                        <PrimaryButton v-show="can(['create user'])" class="flex items-center px-4">-->
<!--                            Desvinculados-->
<!--                        </PrimaryButton>-->
<!--                    </Link>-->
<!--                    <Link v-show="can(['update user'])" :href="route('user.uploadexcel')"-->
<!--                          class="mx-2 bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary">-->
<!--                        <PrimaryButton v-show="can(['update user']) && props.superviNullCentro"-->
<!--                                       class="flex items-center px-4">-->
<!--                            Exportar e Importar-->
<!--                            <ShieldCheckIcon class="w-3 h-3 ml-2 mb-1"/>-->
<!--                            &lt;!&ndash; <span class="ml-3">{{ lang().button.importUser }}</span> &ndash;&gt;-->
<!--                        </PrimaryButton>-->

<!--                    </Link>-->
                    <Create :show="data.createOpen" @close="data.createOpen = false" :roles="props.roles"
                            :cargos="props.cargos" :centros="props.centros" :sexoSelect="props.sexoSelect"
                            :title="props.title"/>
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :user="data.user" :roles="props.roles"
                          :cargos="props.cargos" :centros="props.centros" :sexoSelect="props.sexoSelect"
                          :title="props.title"/>
                    <EdiCentro :show="data.EdiCentroOpen" @close="data.EdiCentroOpen = false" :user="data.user"
                               :centros="props.centros" :title="props.title"/>
                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :user="data.user"
                            :title="props.title"/>
                    <DeleteBulk :show="data.deleteBulkOpen"
                                @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                                :selectedId="data.selectedId" :title="props.title"/>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
<!--                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"/>-->
                        <DangerButton @click="data.deleteBulkOpen = true"
                                      v-show="data.selectedId.length != 0 && can(['delete user'])" class="px-3 py-1.5"
                                      v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5"/>
                        </DangerButton>
                    </div>
<!--                                   @change="selectAll"-->
<!--                    <FilterButtons v-model="data.params.onlySupervis"-->
<!--                    />b - {{data.params.onlySupervis}} - a-->
                     <FilterButtons @update:checked="handleCheckboxChange" />
                    <div>Estado de los checkboxes: {{ checkedValues }}</div>

                    <TextInput v-model="data.params.search" type="text" class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg"
                               placeholder="Buscar seguimiento"/>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <div class="container mx-auto py-8">
                        <h1 class="text-3xl font-bold mb-4">Servicios</h1>
                        <div class="overflow-x-auto">
                            <table class="table-auto min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                                <thead class="bg-gray-800 text-white">
                                    <tr>
                                        <th class="px-4 py-2">Seguimiento Servicio</th>
                                        <th class="px-4 py-2">Hora de Entrada</th>
                                        <th class="px-4 py-2">Hora de Salida</th>
                                        <th class="px-4 py-2">Descripción de Actividad</th>
                                        <th class="px-4 py-2">Personal Técnico</th>
                                        <th class="px-4 py-2">Materiales Usados</th>
                                        <th class="px-4 py-2">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                <!-- Aquí van los datos de cada servicio -->
                                <tr>
                                    <td class="border px-4 py-2">1</td>
                                    <td class="border px-4 py-2">09:00 AM</td>
                                    <td class="border px-4 py-2">05:00 PM</td>
                                    <td class="border px-4 py-2">Instalación de red local</td>
                                    <td class="border px-4 py-2">Juan Pérez</td>
                                    <td class="border px-4 py-2">Cables de red, conectores</td>
                                    <td class="border px-4 py-2 bg-green-500 text-white">Ejecución</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2">2</td>
                                    <td class="border px-4 py-2">10:30 AM</td>
                                    <td class="border px-4 py-2">02:00 PM</td>
                                    <td class="border px-4 py-2">Configuración de servidores</td>
                                    <td class="border px-4 py-2">María Gómez</td>
                                    <td class="border px-4 py-2">Memoria RAM, Discos Duros</td>
                                    <td class="border px-4 py-2 bg-yellow-500 text-white">Espera</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2">3</td>
                                    <td class="border px-4 py-2">06:30 AM</td>
                                    <td class="border px-4 py-2">03:00 PM</td>
                                    <td class="border px-4 py-2">Construccion</td>
                                    <td class="border px-4 py-2">Jose Fernandez</td>
                                    <td class="border px-4 py-2">Cemento</td>
                                    <td class="border px-4 py-2 bg-red-400 text-white">Inactivo</td>
                                </tr>
                                <!-- Puedes agregar más filas según sea necesario -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.users" :filters="data.params"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
