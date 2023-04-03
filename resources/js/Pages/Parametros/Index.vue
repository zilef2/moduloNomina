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

    const data = reactive({
        createOpen: false,
        editOpen: false,
        generico: null,
    })


    function formatDate(date,isDateTime) {
        const validDate = new Date(date)
        const day = validDate.getDate().toString().padStart(2, "0");
        // getMonthName(1)); // January
        const month = monthName((validDate.getMonth() + 1).toString().padStart(2, "0"));
        let year = validDate.getFullYear();
        let anioActual = new Date().getFullYear();
        if(isDateTime ='conLaHora'){
            let hora = validDate.getHours();
            const AMPM = hora >= 12 ? ' PM' : ' AM';
            hora = hora % 12 || 12;
            let hourAndtime =  hora + ':'+ (validDate.getMinutes() < 10 ? '0': '') + validDate.getMinutes()  + AMPM;
            if (anioActual == year){
                return `${day}-${month} | ${hourAndtime}`;
            }
            else{
                year = year.toString().slice(-2);
                return `${day}-${month}-${year} | ${hourAndtime}`;
            }
        }else{
            if (anioActual == year){
                return `${day}-${month}`;
            }
            else{
                year = year.toString().slice(-2);
                return `${day}-${month}-${year}`;
            }
        }
    }

    function number_format(amount, decimals, isPesos) {
        amount += '';
        amount = parseFloat(amount.replace(/[^0-9\.]/g, ''));
        if(isPesos == 2) amount *= 100;
        decimals = decimals || 0;

        if (isNaN(amount) || amount === 0)
            return parseFloat(0).toFixed(decimals);
        amount = '' + amount.toFixed(decimals);

        var amount_parts = amount.split(' '),
            regexp = /(\d+)(\d{3})/;

        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

        if(isPesos == 1) return '$'+amount_parts.join(' ');
        if(isPesos == 2) return amount_parts.join(' ')+' %';
        return amount_parts.join(' ');
    }


    function monthName(monthNumber){
        if(monthNumber == 1) return 'Enero';
        if(monthNumber == 2) return 'Febrero';
        if(monthNumber == 3) return 'Marzo';
        if(monthNumber == 4) return 'Abril';
        if(monthNumber == 5) return 'Mayo';
        if(monthNumber == 6) return 'Junio';
        if(monthNumber == 7) return 'Julio';
        if(monthNumber == 8) return 'Agosto';
        if(monthNumber == 9) return 'Septiembre';
        if(monthNumber == 10) return 'Octubre';
        if(monthNumber == 11) return 'Noviembre';
        if(monthNumber == 12) return 'Diciembre';
    }

    const form = useForm({
        subsidio_de_transporte: 0,
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

    // const updateThisparametro = (esValido) => {
    //     form.valido = esValido
    //     form.put(route('parametros.update', data.generico?.id), {
    //         preserveScroll: true,
    //         onSuccess: () => {
    //             form.reset()
    //         },
    //         onError: () => null,
    //         onFinish: () => null,
    //     })
    // }
</script>

<template>
    <Head :title="props.title" ></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <Edit :show="data.editOpen" @close="data.editOpen = false" :parametro="data.generico" :title="props.title" />
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
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    class="px-5 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>{{ titulos }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, index) in fromController" :key="index"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <!-- <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div class="flex justify-start items-center">
                                        <div class="flex rounded-md overflow-hidden">
                                            <InfoButton v-if="can(['update parametro'])" type="button"
                                                @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                        </div>
                                    </div>
                                </td> -->
                                <td v-for="(titulo_slug, indi) in nombresTabla[1]" :key="indi" class="whitespace-nowrap py-4 px-5 sm:py-3">
                                    <div v-if="titulo_slug.substr(0,1) == 's'">{{ (clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 'd'">{{ formatDate(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 't'">{{ formatDate(clasegenerica[titulo_slug.substr(2)],'conLaHora') }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 'o'">{{ number_format(clasegenerica[titulo_slug.substr(2)],0,1) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 'p'">{{ (number_format(clasegenerica[titulo_slug.substr(2)],0,2)) }}</div>
                                    
                                    <div v-else-if="titulo_slug.substr(0,1) == 'b'">
                                        <div v-if="clasegenerica[titulo_slug.substr(2)] === 0"> Aun no validada</div>
                                        <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 1"> <CheckIcon class="w-8 h-8 text-green-600" /></div>
                                        <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 2"> <XCircleIcon class="w-8 h-8 text-red-600" /></div>
                                    </div>

                                    <div v-else-if="titulo_slug.substr(0,1) == 'i'">{{ number_format(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                    <div v-else-if="titulo_slug.substr(0,1) == 'm'">{{ number_format(clasegenerica[titulo_slug.substr(2)],0,1) }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
