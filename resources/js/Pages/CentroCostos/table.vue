<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {onMounted, reactive, watch} from 'vue';
    import pkg from 'lodash';
    import { Head,router,usePage,Link } from '@inertiajs/vue3';
    import { ChevronUpDownIcon, PencilIcon,EyeIcon,DocumentIcon, TrashIcon } from '@heroicons/vue/24/solid';
import {allMonthName, formatPesosCol, monthName} from '@/global.ts';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import vSelect from "vue-select"; import "vue-select/dist/vue-select.css";


    const { _, debounce, pickBy } = pkg
    const props = defineProps({
        elIDD: Number,
        title: String,
        filters: Object,
        fromController: Object,
        // breadcrumbs: Object,
        nombresTabla: Object,
        perPage: Number,
    })

    const data = reactive({
        params: {
            fecha_ini: props?.fecha_ini,
            quincena: props?.quincena,
        },
        elID:props.elIDD,
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

    onMounted(() => {
        aSumarHoras()

      if(!data.params.quincena) data.params.quincena = 1
    });

    const order = (field) => {
        if(field !== undefined){
            data.params.field = field.replace(/ /g, "_")
            data.params.order = data.params.order === "asc" ? "desc" : "asc"
        }
    }

    const aSumarHoras = ()=>{
      data.thoras_trabajadas = 0
      data.tdiurnas = 0
      data.tnocturnas = 0
      data.textra_diurnas = 0
      data.textra_nocturnas = 0
      data.tdominical_diurno = 0
      data.tdominical_nocturno = 0
      data.tdominical_extra_diurno = 0
      data.tdominical_extra_nocturno = 0

      setTimeout(()=> {
      if(props.fromController.data){
        props.fromController.data.forEach(element => {
            data.thoras_trabajadas += parseInt(element.horas_trabajadas)
          console.log("=>(table.vue:47) data.thoras_trabajadas", data.thoras_trabajadas);
            data.tdiurnas += parseInt(element.diurnas)
            data.tnocturnas += parseInt(element.nocturnas)
            data.textra_diurnas += parseInt(element.extra_diurnas)
            data.textra_nocturnas += parseInt(element.extra_nocturnas)
            data.tdominical_diurno += parseInt(element.dominical_diurno)
            data.tdominical_nocturno += parseInt(element.dominical_nocturno)
            data.tdominical_extra_diurno += parseInt(element.dominical_extra_diurno)
            data.tdominical_extra_nocturno += parseInt(element.dominical_extra_nocturno)
        });
      }else{
        aSumarHoras()
      }
      },500)
    }

    watch(() => _.cloneDeep(data.params), debounce(() => {
        let params = pickBy(data.params)
        router.get(route("CentroCostos.table" , data.elID), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {

                aSumarHoras()
            },
        })
    }, 200))

  let Quicenas = [1,2];
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
<!--        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />-->
        <section class="flex text-gray-600 body-font relative text-center mx-auto">
         <div class="px-5 py-4">
            <div class="flex flex-col text-center w-full mb-12 mx-auto">
                <div class="px-4 sm:px-0 mx-auto">
                    <div class="mb-1 overflow-hidden w-fit justify-center mt-8">
                        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">
                            Reporte de centros de costos
                        </h1>
                        <h3 v-show="data.params.quincena"> Seleccione quincena y mes </h3>

                    </div>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex text-center rounded-xl justify-center mx-auto">
                      <div class="grid grid-cols-2 gap-4 px-2 hover:shadow-xl">
                            <label for="quincena" class="text-lg -mb-2 font-bold">Quicena</label>
                            <label for="fecha_ini" class="text-lg -mb-2 font-bold">Mes</label>
                            <v-select v-model="data.params.quincena" :value="1" :options="Quicenas" class="text-lg"></v-select>
<!--                            <v-select-->
<!--                                v-model="data.params.FiltroUser"-->
<!--                                :options="props.userFiltro" :reduce="element => element.value" label="label"-->
<!--                                required class="dark:bg-gray-400 xs:hidden lg:min-w-[240px]"-->
<!--                            ></v-select>-->

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
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center col-span-4">Empleado <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Horas Trabajadas <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Diurnas <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Nocturnas <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Extra diurnas <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Extra nocturnas <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Dominical diurno <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Dominical nocturno <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Dominical extra diurno <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                                <th class="px-2 py-4 cursor-pointer hover:bg-sky-200 dark:hover:bg-sky-800 items-center">Dominical extra nocturno <ChevronUpDownIcon class="w-4 h-4 inline-flex" /></th>
                            </tr>
<!--                            <tr class="dark:bg-gray-900 text-left">-->
<!--                            </tr>-->
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController.data" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-indigo-200 hover:dark:bg-gray-900/20"
                                :class="{ 'bg-gray-100 hover:bg-indigo-300': index % 2 === 0 }">
<!--                                <td v-if="can(['update centroCostos'])"-->
<!--                                    class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">-->
<!--                                    <div class="flex justify-start items-center">-->
<!--                                        <div class="rounded-md overflow-hidden">-->
<!--                                            <Link :href="route('CentroCostos.show',clasegenerica)"-->
<!--                                                v-show="can(['update centroCostos'])" type="button"-->
<!--                                                class="inline-flex  items-center px-2 py-1.5 bg-gray-600 border border-transparent rounded-none font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"-->
<!--                                                v-tooltip="lang().tooltip.see">-->
<!--                                                <EyeIcon class="w-4 h-4" />-->
<!--                                            </Link>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </td>-->
<!--                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (index+1) }}</td>-->

                                <td  class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.usera) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.horas_trabajadas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.diurnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.nocturnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.extra_diurnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.extra_nocturnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.dominical_diurno) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.dominical_nocturno) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.dominical_extra_diurno) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (clasegenerica.dominical_extra_nocturno) }} </td>
                            </tr>
                            <tr class="border-t border-amber-600 bg-gray-400 dark:border-gray-700 hover:bg-gray-300 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 font-bold"> Total </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.thoras_trabajadas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.tdiurnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.tnocturnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.textra_diurnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.textra_nocturnas) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.tdominical_diurno) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.tdominical_nocturno) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.tdominical_extra_diurno) }} </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (data.tdominical_extra_nocturno) }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
<!--                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">-->
<!--                    <Pagination :links="props.fromController" :filters="data.params" />-->
<!--                </div>-->
            </div>
        </div>
        </section>
    </AuthenticatedLayout>
</template>
