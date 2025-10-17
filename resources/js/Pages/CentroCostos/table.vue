<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {onMounted, reactive, watch, watchEffect} from 'vue';
import pkg from 'lodash';
import {Head, router, usePage, Link} from '@inertiajs/vue3';
import {ChevronUpDownIcon, XMarkIcon, EyeIcon, DocumentIcon, TrashIcon} from '@heroicons/vue/24/solid';
import {formatPesosCol, number_format} from '@/global.ts';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import PrimaryButton from "@/Components/PrimaryButton.vue";


const {_, debounce, pickBy} = pkg
const props = defineProps({
    elIDD: Number,
    title: String,
    filters: Object,
    fromController: Object,
    nombresTabla: Object,
    UltimoReporteRealizado: String,
    perPage: Number,
})

const data = reactive({
    params: {
        fecha_ini: props?.filters?.fecha_ini,
        quincena: props?.filters?.quincena,
        plata: props?.filters?.plata,
    },
    hayUltimoreporte: true,
    elID: props.elIDD,
    selectedId: [],
    dataSet: usePage().props.app.perpage,
    thoras_trabajadas: 0,
    tdiurnas: 0,
    tnocturnas: 0,
    textra_diurnas: 0,
    textra_nocturnas: 0,
    tdominical_diurno: 0,
    tdominical_nocturno: 0,
    tdominical_extra_diurno: 0,
    tdominical_extra_nocturno: 0,
})

const sumarLoQueSea = () => {
    if (data.params?.plata)
        SumarPlata()
    else
        aSumarHoras(0)
}


const verificarExistenciaUltimoReporte = () => {
    if (props.UltimoReporteRealizado === '_') data.hayUltimoreporte = false
}
onMounted(() => {
    verificarExistenciaUltimoReporte()
    sumarLoQueSea()
    if (!data.params.quincena) data.params.quincena = 1

    const today = new Date();
    if (typeof data.params.fecha_ini === "undefined") {
        data.params.fecha_ini = reactive({
            month: today.getMonth(),
            year: today.getFullYear(),
        })
    }
});

const AlternarPlata = () => {
    data.params.plata = !data.params.plata
}

const order = (field) => {
    if (field !== undefined) {
        data.params.field = field.replace(/ /g, "_")
        data.params.order = data.params.order === "asc" ? "desc" : "asc"
    }
}

const aSumarHoras = (veces) => { //suma horas, no dinero
    data.thoras_trabajadas = 0
    data.tdiurnas = 0
    data.tnocturnas = 0
    data.textra_diurnas = 0
    data.textra_nocturnas = 0
    data.tdominical_diurno = 0
    data.tdominical_nocturno = 0
    data.tdominical_extra_diurno = 0
    data.tdominical_extra_nocturno = 0
    veces++
    setTimeout(() => {
        if (props.fromController.data) {
            props.fromController.data.forEach(element => {
                data.thoras_trabajadas += parseInt(element.horas_trabajadas)

                data.tdiurnas += element.diurnas ? parseInt(element.diurnas) : 0
                data.tnocturnas += element.nocturnas ? parseInt(element.nocturnas) : 0
                data.textra_diurnas += element.extra_diurnas ? parseInt(element.extra_diurnas) : 0
                data.textra_nocturnas += element.extra_nocturnas ? parseInt(element.extra_nocturnas) : 0
                data.tdominical_diurno += element.dominical_diurno ? parseInt(element.dominical_diurno) : 0
                data.tdominical_nocturno += element.dominical_nocturno ? parseInt(element.dominical_nocturno) : 0
                data.tdominical_extra_diurno += element.dominical_extra_diurno ? parseInt(element.dominical_extra_diurno) : 0
                data.tdominical_extra_nocturno += element.dominical_extra_nocturno ? parseInt(element.dominical_extra_nocturno) : 0
            });
        } else {
            aSumarHoras(veces)
        }
    }, 500)
}
const SumarPlata = () => { ////suma dinero, no horas
    let datathoras_trabajadas = 0
    let datatdiurnas = 0
    let datatnocturnas = 0
    let datatextra_diurnas = 0
    let datatextra_nocturnas = 0
    let datatdominical_diurno = 0
    let datatdominical_nocturno = 0
    let datatdominical_extra_diurno = 0
    let datatdominical_extra_nocturno = 0

    setTimeout(() => {
        if (props.fromController.data) {
            props.fromController.data.forEach(element => {
                datathoras_trabajadas += parseInt(element.horas_trabajadas)
                datatdiurnas += parseInt(element.diurnas)
                datatnocturnas += parseInt(element.nocturnas)
                datatextra_diurnas += parseInt(element.extra_diurnas)
                datatextra_nocturnas += parseInt(element.extra_nocturnas)
                datatdominical_diurno += parseInt(element.dominical_diurno)
                datatdominical_nocturno += parseInt(element.dominical_nocturno)
                datatdominical_extra_diurno += parseInt(element.dominical_extra_diurno)
                datatdominical_extra_nocturno += parseInt(element.dominical_extra_nocturno)
            });
            data.thoras_trabajadas = formatPesosCol(datathoras_trabajadas)
            data.tdiurnas = formatPesosCol(datatdiurnas)
            data.tnocturnas = formatPesosCol(datatnocturnas)
            data.textra_diurnas = formatPesosCol(datatextra_diurnas)
            data.textra_nocturnas = formatPesosCol(datatextra_nocturnas)
            data.tdominical_diurno = formatPesosCol(datatdominical_diurno)
            data.tdominical_nocturno = formatPesosCol(datatdominical_nocturno)
            data.tdominical_extra_diurno = formatPesosCol(datatdominical_extra_diurno)
            data.tdominical_extra_nocturno = formatPesosCol(datatdominical_extra_nocturno)
        } else {
            SumarPlata()
        }
    }, 300)
}
watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("CentroCostos.table", data.elID), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            sumarLoQueSea()
        },
    })
}, 250))
watchEffect(() => {
})
</script>
<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <section class="text-gray-600 body-font text-center mx-auto w-full">
            <div class="px-5 py-2">
                <div class="flex flex-col text-center w-full mb-2 mx-auto">
                    <div class="px-4 sm:px-0 mx-auto">
                        <div class="mb-1 overflow-hidden w-fit justify-center mt-4">
                            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">
                                Reporte de {{ props.title }}
                            </h1>
                            <p class="text-center">{{ props.UltimoReporteRealizado }}</p>

                            <h3 v-if="data.hayUltimoreporte" v-show="data.params.quincena"> Seleccione quincena y
                                mes </h3>

                        </div>
                    </div>
                </div>
                <div v-if="!data.hayUltimoreporte" class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <h1 class="text-2xl">No se encontró un reporte para este centro de costo</h1>

                </div>
                <div v-else class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="flex justify-between p-2">
                        <div class="flex text-center rounded-xl justify-center mx-auto">
                            <div class="grid grid-cols-3 gap-4 px-2 hover:shadow-xl">
                                <label for="dinero" class="text-lg -mb-2 font-bold">Dinero/Horas</label>
                                <label for="quincena" class="text-lg -mb-2 font-bold">Quicena</label>
                                <label for="fecha_ini" class="text-lg -mb-2 font-bold">Mes</label>
                                <PrimaryButton
                                    @click="AlternarPlata"
                                    class="mx-auto my-1 w-28 rounded-xl shadow-lg">
                                    Alternar
                                </PrimaryButton>
                                <!--very usefull-->
                                <v-select v-model="data.params.quincena"
                                          label="texto"
                                          :options="[
                                              {value:1,texto:'Primera Quincena'},
                                              {value:2,texto:'Segunda quincena'},
                                              {value:3,texto:'Mes'}
                                          ]"
                                          class="text-lg"></v-select>

                                <VueDatePicker month-picker auto-apply :teleport="true" id="fecha_ini"
                                               type="date" v-model="data.params.fecha_ini" required
                                               :placeholder="lang().placeholder.fecha_ini"
                                               class="block w-full border-0 bg-transparent -mt-2"/>
                            </div>
                            <!--                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />-->
                            <!--                        <DangerButton @click="data.deleteBulkOpen = true" v-show="data.selectedId.length != 0"-->
                            <!--                            class="px-3 py-1.5" v-tooltip="lang().tooltip.delete_selected">-->
                            <!--                            <TrashIcon class="w-5 h-5" />-->
                            <!--                        </DangerButton>-->
                        </div>
                        <div class="flex gap-2">
                            <!--                        <TextInput v-model="data.params.search" type="text" class="block w-1/2 rounded-lg"-->
                            <!--                            :placeholder="lang().placeholder.search" />-->
                        </div>
                    </div>
                    <div class="overflow-x-auto scrollbar-table">
                        <table class="w-full">
                            <thead class="text-sm border-t border-gray-200 dark:border-gray-700">
                            <!-- <th class="px-2 py-4 sr-only">Action</th> -->
                            <tr class="dark:bg-gray-900 text-left">
                                <!--                                    v-on:click="order()"-->
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center col-span-4">
                                    Empleado
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Horas Trabajadas
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Diurnas
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Nocturnas
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Extra diurnas
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Extra nocturnas
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Dominical diurno
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Dominical nocturno
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Dominical extra diurno
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">
                                    Dominical extra nocturno
                                    <ChevronUpDownIcon class="w-4 h-4 inline-flex"/>
                                </th>
                            </tr>
                            <!--                            <tr class="dark:bg-gray-900 text-left">-->
                            <!--                            </tr>-->
                            </thead>
                            <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 dark:border-black hover:bg-indigo-100"
                                :class="{ 'bg-gray-200 dark:bg-bg-gray-600': index % 2 === 0 }">

                                <td class="whitespace-nowrap -py-4 px-2">{{ clasegenerica.usera }}</td>
                                <td class="whitespace-nowrap -py-4 px-2">
                                    {{ number_format(clasegenerica.horas_trabajadas, 0, 0) }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">{{
                                        number_format(clasegenerica.diurnas, 0, 0)
                                    }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">{{
                                        number_format(clasegenerica.nocturnas, 0, 0)
                                    }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">
                                    {{ number_format(clasegenerica.extra_diurnas, 0, 0) }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">
                                    {{ number_format(clasegenerica.extra_nocturnas, 0, 0) }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">
                                    {{ number_format(clasegenerica.dominical_diurno, 0, 0) }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">
                                    {{ number_format(clasegenerica.dominical_nocturno, 0, 0) }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">
                                    {{ number_format(clasegenerica.dominical_extra_diurno, 0, 0) }}
                                </td>
                                <td class="whitespace-nowrap -py-4 px-2">
                                    {{ number_format(clasegenerica.dominical_extra_nocturno, 0, 0) }}
                                </td>
                            </tr>
                            <tr class="border border-amber-400 bg-white dark:border-gray-700 hover:bg-gray-300 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 font-bold"> Total</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
<!--                                    {{ isNaN(data.thoras_trabajadas) ? 0 : data.thoras_trabajadas }}-->
                                   {{data.thoras_trabajadas}}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ data.tdiurnas }}
<!--                                    {{ isNaN(data.tdiurnas) ? 0 : data.tdiurnas }}-->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{data.tnocturnas }}
<!--                                    {{ isNaN(data.tnocturnas) ? 0 : data.tnocturnas }}-->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{data.textra_diurnas }}
<!--                                    {{ isNaN(data.textra_diurnas) ? 0 : data.textra_diurnas }}-->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{data.textra_nocturnas }}
<!--                                    {{ isNaN(data.textra_nocturnas) ? 0 : data.textra_nocturnas }}-->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{data.tdominical_diurno }}
<!--                                    {{ isNaN(data.tdominical_diurno) ? 0 : data.tdominical_diurno }}-->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{data.tdominical_nocturno }}
<!--                                    {{ isNaN(data.tdominical_nocturno) ? 0 : data.tdominical_nocturno }}-->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{data.tdominical_extra_diurno }}
<!--                                    {{ isNaN(data.tdominical_extra_diurno) ? 0 : data.tdominical_extra_diurno }}-->
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{data.tdominical_extra_nocturno }}
<!--                                    {{ isNaN(data.tdominical_extra_nocturno) ? 0 : data.tdominical_extra_nocturno }}-->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">-->
                    <!--                    <Pagination :links="props.fromController" :filters="data.params" />-->
                    <!--                </div>-->
                </div>
            </div>
            <Link :href="route('CentroCostos.index')" class="block self-center items-center mx-auto my-6">
                <PrimaryButton class="rounded-xl">
                    {{ lang().button.back }}
                </PrimaryButton>
            </Link>
        </section>
    </AuthenticatedLayout>
</template>
