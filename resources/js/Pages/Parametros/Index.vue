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
import { router, usePage, useForm } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { DocumentCheckIcon, ChevronUpDownIcon, PencilIcon, TrashIcon, XCircleIcon, CheckIcon } from '@heroicons/vue/24/solid';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import SuccessButton from '@/Components/SuccessButton.vue';
import { number_format, formatDate, monthName } from '@/global.ts';

import Create from '@/Pages/Parametros/Create.vue';
import Edit from '@/Pages/Parametros/Edit.vue';
import Delete from '@/Pages/Parametros/Delete.vue';

const { _, debounce, pickBy } = pkg

const props = defineProps({
    title: String,
    fromController: Object,
    breadcrumbs: Object,
    nombresTabla: Array,
})

const PasarDiaAQuincena = (valorDiario) => {
    return number_format(valorDiario * 15, 0, true);
}

const data = reactive({
    createOpen: false,
    editOpen: false,
    generico: null,
})

const form = useForm({
    subsidio_de_transporte_dia: 0,
    salario_minimo: 0,
    porcentaje_diurno: 0,
    porcentaje_nocturno: 0,
    porcentaje_extra_diurno: 0,
    porcentaje_extra_nocturno: 0,
    porcentaje_dominical_diurno: 0,
    porcentaje_dominical_nocturno: 0,
    porcentaje_dominical_extra_diurno: 0,
    porcentaje_dominical_extra_nocturno: 0,
});

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        Ultima actualización: {{ formatDate(fromController[0]['updated_at']) }}
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :title="props.title"
                        :parametros="props.fromController[0]" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <!-- <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th> -->
                                <!-- <th class="px-2 py-4 text-center"> <td> - </td> </th> -->
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    class="px-5 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>{{ titulos }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- por si hay mas filas en la tabla parametros -->
                            <tr v-for="(clasegenerica, index) in fromController" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <td v-if="can(['update parametros'])"
                                    class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center">
                                        <div class="flex rounded-md overflow-hidden">
                                            <InfoButton type="button" @click="(data.editOpen = true)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                        </div>
                                    </div>
                                </td>
                                <td v-for="(titulo_slug, indi) in nombresTabla[1]" :key="indi"
                                    class="whitespace-nowrap py-4 px-5 sm:py-3">
                                    <div v-if="titulo_slug.substr(0, 1) === 's'">{{ (clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0, 1) === 'd'">{{ formatDate(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0, 1) === 't'">{{ formatDate(clasegenerica[titulo_slug.substr(2)], 'conLaHora') }}</div>
                                    <div v-else-if="titulo_slug.substr(0, 1) === 'o'">{{ number_format(clasegenerica[titulo_slug.substr(2)], 0, 1) }}</div>
                                    <div v-else-if="titulo_slug.substr(0, 1) === 'p'">{{ (number_format(clasegenerica[titulo_slug.substr(2)], 2, 0)) }} </div>

                                    <div v-else-if="titulo_slug.substr(0, 1) === 'i'">{{ number_format(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0, 1) === 'm'">{{ number_format(clasegenerica[titulo_slug.substr(2)], 0, 1) }}</div>
                                </td>
                            </tr>
                            <tr
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <td class="whitespace-nowrap py-4 px-5 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-5 sm:py-3"> </td>
                                <td class="whitespace-nowrap py-4 px-5 sm:py-3"> {{
                                    PasarDiaAQuincena(fromController[0]['subsidio_de_transporte_dia']) }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>



            <section class="text-gray-600 body-font">
                <div class="container px-5 py-8 mx-auto">
                    <div class="text-center my-8">
                        <h2 class="tracking-widest text-2xl title-font font-medium text-black dark:text-white mb-1">
                            Parametros del aplicativo
                        </h2>
                    </div>
                    <div class="flex flex-wrap -mx-4 my-8">
                        <div class="py-8 px-4 lg:w-1/3">
                            <div class="h-full flex items-start">
                                <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                                    <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">9</span>
                                    <span class="font-medium text-lg text-gray-800 title-font leading-none">nov 2023</span>
                                </div>
                                <div class="flex-grow pl-6">
                                    <div class="inline-flex">
                                        <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                            Salario hora
                                        </h2>
<!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                                    </div>
                                    <!-- <h1 class="title-font text-xl font-medium text-gray-900 mb-3">The 400 Blows</h1> -->
                                    <p class="leading-relaxed text-lg mb-5">
                                        Salrio Hora = salario mensual / (235)
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="py-8 px-4 lg:w-1/3">
                            <div class="h-full flex items-start">
                                <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                                    <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7</span>
                                    <span class="font-medium text-lg text-gray-800 title-font leading-none">sept 2023</span>
                                </div>
                                <div class="flex-grow pl-6">
                                    <div class="inline-flex">
                                        <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                            Hora de comida
                                        </h2>
<!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                                    </div>
                                    <p class="leading-relaxed text-lg mb-5">
                                        Horas trabajadas > 8 <b>entonces</b> Comida = 1
                                    </p>
                                    <p class="leading-relaxed text-lg mb-5">
                                        Horas trabajadas > 16 <b>entonces</b> Comida = 2
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="py-8 px-4 lg:w-1/3">
                            <div class="h-full flex items-start">
                                <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                                    <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7</span>
                                    <span class="font-medium text-lg text-gray-800 title-font leading-none">sept 2023</span>
                                </div>
                                <div class="flex-grow pl-6">
                                    <div class="inline-flex">
                                    <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                        Subsidio de transporte
                                    </h2>
<!--                                    <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                                    </div>
                                     <h1 class="title-font text-xl font-medium text-gray-900 mb-3">¿ Cumplió la quincena ?</h1>
                                    <p class="leading-relaxed text-lg mb-5">
                                        Si el salario supera el doble del SMLV  <b>Subsidio Transporte = 0</b>
                                    </p>
                                    <p class="leading-relaxed text-lg mb-5">
                                        Si no  <b>Subsidio Transporte = dias * subsidio transporte (dia)</b>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="py-8 px-4 lg:w-1/3">
                            <div class="h-full flex items-start">
                                <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                                    <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">14</span>
                                    <span class="font-medium text-lg text-gray-800 title-font leading-none">dic 2023</span>
                                </div>
                                <div class="flex-grow pl-6">
                                    <div class="inline-flex">
                                    <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                        Horas de los administrativos
                                    </h2>
<!--                                    <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                                    </div>
                                    <p class="leading-relaxed text-lg mb-5">
                                        Los administrativos no tienen que reportar
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="py-8 px-4 lg:w-1/3">
                            <div class="h-full flex items-start">
                                <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                                    <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7</span>
                                    <span class="font-medium text-lg text-gray-800 title-font leading-none">sept 2023</span>
                                </div>
                                <div class="flex-grow pl-6">
                                    <div class="inline-flex">
                                        <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                            Calculo de salud y pension
                                        </h2>
<!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                                    </div>
                                    <h1 class="title-font text-xl font-medium text-gray-900 mb-3">¿ Cumplió la quincena ?</h1>
                                    <p class="leading-relaxed text-lg mb-5">
                                        Salario quincena * 4%
                                    </p>
                                    <p class="leading-relaxed text-lg mb-5">
                                        No tiene derecho
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="py-8 px-4 lg:w-1/3">
                            <div class="h-full flex items-start">
                                <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                                    <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">4</span>
                                    <span class="font-medium text-lg text-gray-800 title-font leading-none">abril 2024</span>
                                </div>
                                <div class="flex-grow pl-6">
                                    <div class="inline-flex">
                                        <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                            Horas ingenieros
                                        </h2>
<!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                                    </div>
                                    <p class="leading-relaxed text-lg mb-5">
                                        Los ingenieros ganan horas extras?
                                    </p>
                                </div>
                            </div>
                        </div>


                      <div class="py-8 px-4 lg:w-1/3">
                        <div class="h-full flex items-start">
                          <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                            <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7</span>
                            <span class="font-medium text-lg text-gray-800 title-font leading-none">sept 2023</span>
                          </div>
                          <div class="flex-grow pl-6">
                            <div class="inline-flex">
                              <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                Saber si tiene horas extras el dia anterior
                              </h2>
                              <!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                            </div>
                            <p class="leading-relaxed text-lg mb-5">
                              ???
                            </p>
                            <p class="leading-relaxed text-lg mb-5">
                              ???
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="py-8 px-4 lg:w-1/3">
                        <div class="h-full flex items-start">
                          <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
                            <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7</span>
                            <span class="font-medium text-lg text-gray-800 title-font leading-none">sept 2023</span>
                          </div>
                          <div class="flex-grow pl-6">
                            <div class="inline-flex">
                              <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                                Saber si tiene horas extras por la semana
                              </h2>
                              <!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
                            </div>
                            <p class="leading-relaxed text-lg mb-5">
                              ???
                            </p>
                            <p class="leading-relaxed text-lg mb-5">
                              ???
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
            </section>

    </div>
</AuthenticatedLayout></template>
