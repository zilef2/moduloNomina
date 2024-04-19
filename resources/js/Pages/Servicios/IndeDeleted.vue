<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import Breadcrumb from '@/Components/Breadcrumb.vue';
    import TextInput from '@/Components/TextInput.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SelectInput from '@/Components/SelectInput.vue';
    import { reactive, watch } from 'vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import pkg from 'lodash';
    import { router } from '@inertiajs/vue3';
    import Pagination from '@/Components/Pagination.vue';
    import { EyeIcon, ChevronUpDownIcon, TrophyIcon } from '@heroicons/vue/24/solid';

    import {number_format} from '@/global.ts';

import Recontratar from '@/Pages/User/Recontratar.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { usePage,Link } from '@inertiajs/vue3';

const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    filters: Object,
    users: Object,
    roles: Object,
    cargos: Object,
    centros: Object,
    breadcrumbs: Object,
    perPage: Number,
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
    RecontratarOpen: false,
    user: null,
    dataSet: usePage().props.app.perpage
})

// <!--<editor-fold desc="Default functions">-->
        const order = (field) => {
            data.params.field = field
            data.params.order = data.params.order === "asc" ? "desc" : "asc"
            console.log(data.params.order)
            console.log(data.params.field)
        }

        watch(() => _.cloneDeep(data.params), debounce(() => {
            let params = pickBy(data.params)
            console.log("🧈 debu params:", params);
            router.get(route("IndexTrashed"), params, {
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
    //fin default functions
// <!--</editor-fold>-->

</script>

<template>
    <Head :title="props.title" />

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit gap-8">
                    <Link v-show="can(['update user'])" :href="route('user.index')"
                          class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary">
                        <PrimaryButton v-show="can(['create user'])" class="flex items-center px-4">
                            Vinculados
                        </PrimaryButton>
                    </Link>
                    <Recontratar :show="data.RecontratarOpen" @close="data.RecontratarOpen = false" :user="data.user"
                            :title="props.title" />
<!--                    <DeleteBulk :show="data.deleteBulkOpen"-->
<!--                        @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"-->
<!--                        :selectedId="data.selectedId" :title="props.title" />-->
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length !== 0 && can(['delete user'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrophyIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                    <TextInput v-model="data.params.search" type="text" class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg"
                        placeholder="Buscar solo por nombre" />
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900/50 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th class="px-2 py-4 text-center">#</th>
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('name')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.name }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('cedula')">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            Identificacion
                                        </span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('cargo_id')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.cargo }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>

                                <th class="px-2 py-4">{{ lang().label.role }}</th>

                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('celular')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.celular }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('fecha_de_ingreso')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.fecha_de_ingreso }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('despedido')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.despedido }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('centro_costo_id')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.centro_costo }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 cursor-pointer" v-on:click="order('salario')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.salario }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>

                                <th class="px-2 py-4 cursor-not-allowed">{{ lang().label.Action }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users.data" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <input
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"
                                        type="checkbox" @change="select" :value="user.id" v-model="data.selectedId" />
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++index }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <span class="flex justify-start items-center">
                                        {{ user.name }}
                                    </span>
                                    <small>{{ user.email }} </small>
                                    <!-- <CheckBadgeIcon class="ml-[2px] w-4 h-4 text-primary dark:text-white" v-show="user.email_verified_at" />  -->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.cedula }}</td>

                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.cargo.length === 0 ? 'No tiene cargo' : user.cargo.nombre }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.roles.length === 0 ? 'No tiene rol' : user.roles[0].name }}</td>
                                <!-- <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.created_at }}</td> -->
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.celular }} <span> {{ user.telefono }} </span></td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.fecha_de_ingreso }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.deleted_at }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ user.cc }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ number_format(user.salario,0,1) }}</td>
                                <td class="whitespace-nowrap p-4 sm:p-3">
                                    <div class="flex justify-center">
                                        <div class="rounded-md overflow-hidden">
                                            <DangerButton v-show="can(['delete user'])" type="button"
                                                @click="(data.RecontratarOpen = true), (data.user = user)"
                                                class="px-2 py-1.5 rounded-none hover:text-sky-400" v-tooltip="'Recontratar'">
                                                <TrophyIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.users" :filters="data.params" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
