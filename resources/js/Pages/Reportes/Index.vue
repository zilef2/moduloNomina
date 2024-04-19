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
    import CreateMass from '@/Pages/Reportes/CreateMassive.vue';
    import Edit from '@/Pages/Reportes/Edit.vue';
    import Delete from '@/Pages/Reportes/Delete.vue';
    import DeleteBulk from "@/Pages/Reportes/DeleteBulk.vue";


    import {formatDate, number_format, monthName} from '@/global.ts';

    import { Bar } from 'vue-chartjs'
    import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
    import InputError from "@/Components/InputError.vue";
    import InputLabel from "@/Components/InputLabel.vue";
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
        userFiltro: Object,
        IntegerDefectoSelect: Number,
        horasemana: Number,
        startDateMostrar: String,
        endDateMostrar: String,
        sumhoras_trabajadas: Number,


        quincena: Object,
        nombrePersona: String,
        numberPermissions: Number,
        ArrayOrdinarias: Array,

        sumdiurnas: Number,
        sumnocturnas: Number,
        sumextra_diurnas: Number,
        sumextra_nocturnas: Number,
        sumdominical_diurno: Number,
        sumdominical_nocturno: Number,
        sumdominical_extra_diurno: Number,
        sumdominical_extra_nocturno: Number,
        horasTrabajadasHoy: Array, //horas que lleva trabajadas solo el dia de seleccionado(index)
        HorasDeCadaSemana: Array,  //horas de las semanas proximas a la actual
        ArrayHorasSemanales: Array,  //parametros

    })

    // <!--<editor-fold desc="Charts">-->
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        color: "#000",
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
    // <!--</editor-fold>-->

    const data = reactive({
        params: {
            soloValidos: props.filters?.soloValidos,
            FiltroUser: props.filters?.FiltroUser,

            search: props.filters?.search,
            searchDDay: props.filters?.searchDDay,
            field: props.filters?.field,
            order: props.filters?.order,
            perPage: props.perPage,

        },
        selectedId: [],
        multipleSelect: false,
        createOpen: false,
        createMassOpen: false,
        editOpen: false,
        editCorregirOpen: false,
        deleteOpen: false,
        deleteBulkOpen: false,
        generico: null,
        nope: null,
        dataSet: usePage().props.app.perpage,
    })



    // <!--<editor-fold desc="Order clonedeep select">-->
    const order = (field) => {
        if(field !== undefined && field !== null && props.userFiltro){

            field = field.substr(2);
            data.params.field = field.replace(/ /g, "_")

            data.params.order = data.params.order === "asc" ? "desc" : "asc"
        }else{
          alert('No esta permitido organizar la tabla en esta vista')
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
    // <!--</editor-fold>-->

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
                    <div class="mt-4 inline-flex">
                        <PrimaryButton v-if="can(['create reporte'])" class="rounded-md mx-2 h-10 mt-6" @click="data.createOpen = true">
                            {{ lang().button.add }}
                        </PrimaryButton>
                        <PrimaryButton v-if="can(['isAdmin'])" class="rounded-md mx-2 h-10 mt-6" @click="data.createMassOpen = true">
                            {{ lang().button.add }} Masivamente
                        </PrimaryButton>

                        <div v-if="props.userFiltro && props.userFiltro.length > 0" v-show="can(['isingeniero','isadmin','isadministrativo','issupervisor']) && props.userFiltro" class="mx-4">
                            <InputLabel for="centro_costo_id" value="Filtrar por trabajador"/>
                            <SelectInput v-model="data.params.FiltroUser" :dataSet="props.userFiltro"
                                         class="mt-1 block w-full"/>
                        </div>

                        <div v-else v-show="can(['isingeniero','isadmin','isadministrativo','issupervisor']) && props.userFiltro" class="mx-4">
                          <p class="pt-1 mt-6 font-extrabold">Sin reportes</p>
                        </div>
                    </div>

                    <Create :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        :valoresSelect="props.valoresSelect" :IntegerDefectoSelect="props.IntegerDefectoSelect"
                        :horasemana="props.horasemana" :startDateMostrar="props.startDateMostrar"
                        :endDateMostrar="props.endDateMostrar" :numberPermissions="props.numberPermissions"
                        :ArrayOrdinarias="props.ArrayOrdinarias" :horasTrabajadasHoy = "props.horasTrabajadasHoy"
                        :HorasDeCadaSemana="props.HorasDeCadaSemana"
                        :ArrayHorasSemanales="props.ArrayHorasSemanales"
                        />
                    <CreateMass :show="data.createMassOpen" @close="data.createMassOpen = false" :title="props.title"
                        :valoresSelect="props.valoresSelect" :IntegerDefectoSelect="props.IntegerDefectoSelect"
                        :horasemana="props.horasemana" :startDateMostrar="props.startDateMostrar"
                        :endDateMostrar="props.endDateMostrar" :numberPermissions="props.numberPermissions"
                        :ArrayOrdinarias="props.ArrayOrdinarias"
                        />

                    <Edit :show="data.editOpen" @close="data.editOpen = false" :Reporte="data.generico" :title="props.title"
                        :valoresSelect="props.valoresSelect" :showUsers="props.showUsers" :correccionUsuario="false" />

                    <!-- solo el usuario puede corregir sus propios reportes -->
                    <!-- <Edit :show="data.editCorregirOpen" @close="data.editCorregirOpen = false" :Reporte="data.generico"
                        :title="props.title" :valoresSelect="props.valoresSelect" :showUsers="props.showUsers"
                        :correccionUsuario="true" /> -->

                    <Delete :show="data.deleteOpen" @close="data.deleteOpen = false" :Reporte="data.generico" v-show="can(['delete reporte'])"
                        :title="props.title" />

                    <DeleteBulk :show="data.deleteBulkOpen" v-if="can(['delete reporte']) && can(['updateCorregido reporte'])"
                                @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                                :selectedId="data.selectedId" :title="props.title" />
                </div>
            </div>

            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <!-- paginacion, borrado y buscado -->
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-if="filters !== null" v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length != 0 && can(['delete reporte'])"
                            class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                    <div  class="flex">
                    <!--                        solo mes-->
                    <TextInput v-model="data.params.search"
                        type="number" min="0" max="12" class="block w-full rounded-lg"
                        :placeholder="lang().placeholder.searchDates" />
                    <TextInput v-model="data.params.searchDDay"
                        type="number" min="0" max="31" class="block w-full rounded-lg"
                        :placeholder="lang().placeholder.searchDDay" />
                    <label for="soloval" class="mx-3">Solo validos</label>
                    <input v-model="data.params.soloValidos" id="soloval" type="checkbox"
                        class="bg-gray-100 h-7 w-7 mt-2 ml-5" />
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
                                                    class="px-2 py-1.5 rounded-sm" v-tooltip="lang().tooltip.edit">
                                                    <PencilIcon class="w-6 h-6" />
                                                </InfoButton>
                                                <SuccessButton v-if="can(['update reporte'])" type="button" class="mx-1 w-12 h-10"
                                                    :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                                    @click=" (data.generico = clasegenerica),(updateThisReporte(true))"
                                                    v-tooltip="'Aprobar'">
                                                    <CheckIcon class="w-6 h-6" />
                                                </SuccessButton>
                                            </form>
                                            <!-- <InfoButton v-if="(can(['delete reporte'])) && (clasegenerica.valido === 2)"
                                                type="button"
                                                @click="(data.editCorregirOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-lg" v-tooltip="'Corregir'">
                                                <DocumentCheckIcon class="w-6 h-6" />
                                            </InfoButton> -->
                                            <DangerButton v-if="(can(['delete reporte']))" type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-sm" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-6 h-6" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (index+1) }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ showSelect[clasegenerica.centro_costo_id] }}</td>
                                <td v-show="can(['updateCorregido reporte'])" class="whitespace-nowrap py-4 px-2 sm:py-3">{{ showUsers[clasegenerica.user_id] }}</td>

                                <td v-for="(titulo_slug, indi) in nombresTabla[1]" :key="indi" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div v-if="titulo_slug.substr(0,1) === 's'">{{ (clasegenerica[titulo_slug.substr(2)]) }} </div>
                                    <div v-else-if="titulo_slug.substr(0,1) === 'd'">{{ formatDate(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) === 't'">{{ formatDate(clasegenerica[titulo_slug.substr(2)],'conLaHora') }}</div>

                                    <div v-else-if="titulo_slug.substr(0,1) === 'b'">
                                        <div v-if="clasegenerica[titulo_slug.substr(2)] === 0"> Aun no validada</div>
                                        <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 1">
                                            <CheckIcon class="w-8 h-8 text-green-600" />
                                        </div>
                                        <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 2">
                                            <XCircleIcon class="w-8 h-8 text-red-600" />
                                        </div>
                                    </div>

                                    <div v-else-if="titulo_slug.substr(0,1) == 'i'">{{ number_format(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 'm'">{{ number_format(clasegenerica[titulo_slug.substr(2)],0,1) }}</div>
                                </td>
                            </tr>
                            <tr v-show="props.sumhoras_trabajadas != 0" class="my-2 py-4 border-t border-gray-200 dark:border-gray-900 hover:bg-sky-200 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td v-show="can(['updateCorregido reporte'])" class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> Totales </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td v-if="numberPermissions > 1" class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> Trabajadas: {{ props.sumhoras_trabajadas }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumdiurnas ? 'Diurnas: ' + props.sumdiurnas : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumnocturnas ? 'Nocturnas: ' + props.sumnocturnas : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumextra_diurnas ? 'Extra diurnas: ' + props.sumextra_diurnas : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumextra_nocturnas ? 'Extra nocturnas: ' + props.sumextra_nocturnas : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumdominical_diurno ? 'Dominical diurno: ' + props.sumdominical_diurno : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumdominical_nocturno ? 'Dominical nocturno: ' + props.sumdominical_nocturno : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumdominical_extra_diurno ? 'Dominical extra diurno: ' + props.sumdominical_extra_diurno : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> {{ props.sumdominical_extra_nocturno ? 'Dominical extra nocturno: ' + props.sumdominical_extra_nocturno : ''}} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"> </td>
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
                            <span class="inline-block py-1 px-2 rounded bg-indigo-50 text-indigo-500 text-xs font-medium tracking-widest">
                                Este mes
                            </span>
                            <h2 v-if="props.nombrePersona" class="sm:text-3xl text-2xl title-font font-medium text-gray-900 mt-4 mb-4">
                              Reportes de este mes: <b>{{ props.nombrePersona }}</b>
                            </h2>
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
