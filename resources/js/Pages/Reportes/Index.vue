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
        import { router,usePage,useForm } from '@inertiajs/vue3';

        import Pagination from '@/Components/Pagination.vue';
        import { DocumentCheckIcon, ChevronUpDownIcon, PencilIcon, TrashIcon,XCircleIcon,CheckIcon } from '@heroicons/vue/24/solid';

        import Checkbox from '@/Components/Checkbox.vue';
        import InfoButton from '@/Components/InfoButton.vue';
        import SuccessButton from '@/Components/SuccessButton.vue';


    import Create from '@/Pages/Reportes/Create.vue';
    import Edit from '@/Pages/Reportes/Edit.vue'; 
    import Delete from '@/Pages/Reportes/Delete.vue';


    import {formatDate, number_format, monthName} from '@/global.ts';

    import { Bar } from 'vue-chartjs'
    import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
    ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

    const { _, debounce, pickBy } = pkg

    const props = defineProps({
        title: String,
        filters: Object,
        fromController: Object,
        breadcrumbs: Object,
        perPage: Number,
        nombresTabla: Array,
        valoresSelect: Object,
        showSelect: Object,
        showUsers: Object,
        IntegerDefectoSelect: Number,
        horasemana: Number,
        startDateMostrar: String,
        endDateMostrar: String,
        sumhoras_trabajadas: Number,


        quincena: Object,
        nombrePersona: String,
        numberPermissions: Number,
    })

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        color: "#000",
        // todo 
        darkcolor: "#fff",
    }
    const chartData = {
        name: 'Numero de reportes',
        datasets: [{
            label: 'Numero de Reportes',
            data: props.quincena,
            backgroundColor: '#f87979',
        }]
    };
   
    const data = reactive({
        params: {
            soloValidos: props.filters?.soloValidos,
            search: props.filters?.search,
            field: props.filters?.field,
            order: props.filters?.order,
            perPage: props.perPage,
        },
        selectedId: [],
        multipleSelect: false,
        createOpen: false,
        editOpen: false,
        editCorregirOpen: false,
        deleteOpen: false,
        deleteBulkOpen: false,
        generico: null,
        nope: null,
        dataSet: usePage().props.app.perpage,
    })

    const order = (field) => {
        if(field != undefined && field != null){

            field = field.substr(2);
            data.params.field = field.replace(/ /g, "_")

            data.params.order = data.params.order === "asc" ? "desc" : "asc"
        }
    }

    watch(() => _.cloneDeep(data.params), debounce(() => {
        let params = pickBy(data.params)
        router.get(route("Reportes.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        })
    }, 150))

    const selectAll = (event) => {
        if (event.target.checked === false) {
            data.selectedId = []
        } else {
            props.fromController?.data.forEach((generico) => {
                data.selectedId.push(generico.id)
            })
        }
    }
    const select = () => {
        if (props.fromController?.data.length == data.selectedId.length) {
            data.multipleSelect = true
        } else {
            data.multipleSelect = false
        }
    }

    //aprobar o no 
    const form = useForm({
        valido: false,
    });
    const updateThisReporte = (esValido) => {
        form.valido = esValido
        form.put(route('Reportes.update', data.generico?.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    }
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton v-if="can(['create reporte'])" class="rounded-none" @click="data.createOpen = true">
                        {{ lang().button.add }}
                    </PrimaryButton>

                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        :valoresSelect="props.valoresSelect" :IntegerDefectoSelect="props.IntegerDefectoSelect"
                        :horasemana="props.horasemana" :startDateMostrar="props.startDateMostrar"
                        :endDateMostrar="props.endDateMostrar" :numberPermissions="props.numberPermissions" />

                    <Edit :show="data.editOpen" @close="data.editOpen = false" :Reporte="data.generico" :title="props.title"
                        :valoresSelect="props.valoresSelect" :showUsers="props.showUsers" :correccionUsuario="false" />

                    <!-- solo el usuario puede corregir sus propios reportes -->
                    <Edit :show="data.editCorregirOpen" @close="data.editCorregirOpen = false" :Reporte="data.generico"
                        :title="props.title" :valoresSelect="props.valoresSelect" :showUsers="props.showUsers"
                        :correccionUsuario="true" />

                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :Reporte="data.generico"
                        :title="props.title" />
                </div>
            </div>

            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <!-- paginacion, borrado y buscado -->
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-if="filters !== null" v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length != 0"
                            class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                    <TextInput v-if="filters !== null" v-show="can(['update reporte'])" v-model="data.params.search"
                        type="text" class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg"
                        :placeholder="lang().placeholder.searchDates" />

                    <div v-if="props.quincena === null" class="flex space-x-8">
                        <label for="soloval">Solo validos</label>
                        <input v-if="filters !== null" v-model="data.params.soloValidos" id="soloval" type="checkbox"
                            class="bg-black h-7 w-7" />
                    </div>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    v-on:click="order(nombresTabla[2][indiceN])"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>{{ titulos }}</span>
                                        <ChevronUpDownIcon v-if="nombresTabla[2][indiceN] !== null" class="w-4 h-4" />
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <input type="checkbox" @change="select" :value="clasegenerica.id"
                                        v-model="data.selectedId"
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary" />
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center">
                                        <div class="flex rounded-md overflow-hidden">
                                            <form @submit.prevent="updateThisReporte">
                                                <InfoButton v-if="can(['update reporte'])" type="button"
                                                    @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                    <PencilIcon class="w-4 h-4" />
                                                </InfoButton>
                                                <SuccessButton v-if="can(['update reporte'])" type="button" class="ml-3"
                                                    :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                                    @click=" (data.generico = clasegenerica),(updateThisReporte(true))">
                                                    <CheckIcon class="w-4 h-4" />
                                                </SuccessButton>
                                            </form>
                                            <InfoButton v-if="(can(['delete reporte'])) && (clasegenerica.valido === 2)"
                                                type="button"
                                                @click="(data.editCorregirOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <DocumentCheckIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton v-if="(can(['delete reporte']))" type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (index+1) }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ showSelect[clasegenerica.centro_costo_id]
                                                                    }}</td>
                                <td v-show="can(['updateCorregido reporte'])" class="whitespace-nowrap py-4 px-2 sm:py-3">{{
                                                                    showUsers[clasegenerica.user_id] }}</td>

                                <td v-for="(titulo_slug, indi) in nombresTabla[1]" :key="indi"
                                    class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div v-if="titulo_slug.substr(0,1) == 's'">{{ (clasegenerica[titulo_slug.substr(2)]) }}
                                    </div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 'd'">{{
                                                                            formatDate(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 't'">{{
                                                                            formatDate(clasegenerica[titulo_slug.substr(2)],'conLaHora') }}</div>

                                    <div v-else-if="titulo_slug.substr(0,1) == 'b'">
                                        <div v-if="clasegenerica[titulo_slug.substr(2)] === 0"> Aun no validada</div>
                                        <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 1">
                                            <CheckIcon class="w-8 h-8 text-green-600" />
                                        </div>
                                        <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 2">
                                            <XCircleIcon class="w-8 h-8 text-red-600" />
                                        </div>
                                    </div>

                                    <div v-else-if="titulo_slug.substr(0,1) == 'i'">{{
                                                                            number_format(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 'm'">{{
                                                                            number_format(clasegenerica[titulo_slug.substr(2)],0,1) }}</div>
                                </td>
                            </tr>
                            <tr v-show="props.sumhoras_trabajadas != 0"
                                class="my-2 py-4 border-t border-gray-200 dark:border-gray-900 hover:bg-sky-200 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> Total horas </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumhoras_trabajadas }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
                </div>
            </div>

            <section v-if="props.quincena != null"
                v-show="can(['updateCorregido reporte']) || can(['isAdmin']) || can(['isadministrativo'])"
                class="text-gray-600 body-font overflow-hidden">
                <div class="container px-5 py-4 mx-auto">
                    <div class="flex flex-wrap m-2">
                        <div class="p-1 md:w-1/2 flex flex-col items-start">
                            <span
                                class="inline-block py-1 px-2 rounded bg-indigo-50 text-indigo-500 text-xs font-medium tracking-widest">Este
                                mes</span>
                            <h2 class="sm:text-3xl text-2xl title-font font-medium text-gray-900 mt-4 mb-4">Numero de
                                reportes de {{ props.nombrePersona }}</h2>
                            <div class="m-1 p-1 w-full">
                                <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
                            </div>
                        </div>
                        <!-- <div class="p-1 md:w-1/2 flex flex-col items-start">
                            <span class="inline-block py-1 px-2 rounded bg-indigo-50 text-indigo-500 text-xs font-medium tracking-widest">Segunda quincena</span>
                            <h2 class="sm:text-3xl text-2xl title-font font-medium text-gray-900 mt-4 mb-4">Numero de reportes</h2>
                            <div class="m-1 p-1 w-full">
                                <Bar id="my-chart-id"
                                :options="chartOptions"
                                :data="chartData" />
                            </div>
                        </div> -->
                </div>
            </div>
        </section>


    </div>
</AuthenticatedLayout></template>
