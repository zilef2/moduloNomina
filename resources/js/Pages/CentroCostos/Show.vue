<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import Breadcrumb from '@/Components/Breadcrumb.vue';
    import TextInput from '@/Components/TextInput.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SelectInput from '@/Components/SelectInput.vue';
    import { reactive, watch } from 'vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import pkg from 'lodash';
    import { Head,router,usePage,Link } from '@inertiajs/vue3';

    import Pagination from '@/Components/Pagination.vue';
    import { DocumentCheckIcon, ChevronUpDownIcon, PencilIcon, TrashIcon,XCircleIcon,CheckIcon } from '@heroicons/vue/24/solid';


    import Checkbox from '@/Components/Checkbox.vue';
    import InfoButton from '@/Components/InfoButton.vue';
import Dashboard from '../Dashboard.vue';

    const { _, debounce, pickBy } = pkg
    const props = defineProps({
        title: String,
        filters: Object,
        fromController: Object,
        // breadcrumbs: Object,
        perPage: Number,
        nombresTabla: Array,

        valoresSelect: Object,
        showSelect: Object,
        showUsers: Object,
        IntegerDefectoSelect: Number,
    })
    
    const data = reactive({
        params: {
            // search: props.filters.search,
            // field: props.filters.field,
            // order: props.filters.order,
            perPage: props.perPage,
        },
        // createOpen: false,
        // editOpen: false,
        // deleteOpen: false,
        generico: null,
        dataSet: usePage().props.app.perpage,
    })
        

    const order = (field) => {
        if(field != undefined){
            data.params.field = field.replace(/ /g, "_")
            data.params.order = data.params.order === "asc" ? "desc" : "asc"
        }
    }

    watch(() => _.cloneDeep(data.params), debounce(() => {
        let params = pickBy(data.params)
        router.get(route("CentroCostos.show"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        })
    }, 150))

    const select = () => {
        if (props.fromController?.data.length == data.selectedId.length) {
            data.multipleSelect = true
        } else {
            data.multipleSelect = false
        }
    }
    function formatDate(date) {
        const validDate = new Date(date)
        const day = validDate.getDate().toString().padStart(2, "0");
        // getMonthName(1)); // January
        const month = monthName((validDate.getMonth() + 1).toString().padStart(2, "0"));
        let year = validDate.getFullYear();
        let anioActual = new Date().getFullYear();
        
        if (anioActual == year){
            return `${day}-${month}`;
        }
        else{
            year = year.toString().slice(-2);
            return `${day}-${month}-${year}`;
        }
    }

    function number_format(amount, decimals, isPesos) {
        amount += '';
        amount = parseFloat(amount.replace(/[^0-9\.]/g, ''));
        decimals = decimals || 0;

        if (isNaN(amount) || amount === 0)
            return parseFloat(0).toFixed(decimals);
        amount = '' + amount.toFixed(decimals);

        var amount_parts = amount.split(' '),
            regexp = /(\d+)(\d{3})/;

        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

        if(isPesos)
            return '$'+amount_parts.join(' ');
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

</script>

<template>
    <Head :title="props.title" ></Head>

    <AuthenticatedLayout>

        <!-- <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" /> -->
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton v-if="can(['create centroCostos'])" class="rounded-none" @click="data.createOpen = true">
                        {{ lang().button.add }}
                    </PrimaryButton>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                    </div>
                    <!-- <TextInput v-model="data.params.search" type="text" class="block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg"
                        :placeholder="lang().placeholder.search" /> -->
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900 text-left">
                                <th v-for="(titulos, indiceN) in nombresTabla[0]" :key="indiceN"
                                    v-on:click="order(nombresTabla[1][indiceN])"
                                    class="px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800">
                                    <div class="flex justify-between items-center">
                                        <span>{{ titulos }}</span>
                                        <ChevronUpDownIcon v-if="nombresTabla[1][indiceN]" class="w-4 h-4" />
                                    </div>
                                </th>
                                <!-- <th class="px-2 py-4 sr-only">Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(clasegenerica, indeFor) in fromController.data" :key="indeFor"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">
                                <td v-if="can(['update centroCostos'])" class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <div class="flex justify-start items-center">
                                        <div class="rounded-md overflow-hidden">
                                            <InfoButton v-show="can(['update centroCostos'])" type="button"
                                                @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton v-show="can(['delete centroCostos'])" type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ (indeFor+1) }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ showSelect[clasegenerica.centro_costo_id] }}</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">{{ showUsers[clasegenerica.user_id] }}</td>

                                <td v-for="(titulo_slug, indi) in nombresTabla[1]" :key="indi" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div v-if="titulo_slug != null" class="">
                                        <div v-if="titulo_slug.substr(0,1) == 's'">{{ (clasegenerica[titulo_slug.substr(2)]) }}</div>
                                        <div v-else-if="titulo_slug.substr(0,1) == 'd'">{{ formatDate(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                        <div v-else-if="titulo_slug.substr(0,1) == 't'">{{ formatDate(clasegenerica[titulo_slug.substr(2)],'conLaHora') }}</div>
                                        
                                        <div v-else-if="titulo_slug.substr(0,1) == 'b'">
                                            <div v-if="clasegenerica[titulo_slug.substr(2)] === 0"> Aun no validada</div>
                                            <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 1"> <CheckIcon class="w-8 h-8 text-green-600" /></div>
                                            <div v-else-if="clasegenerica[titulo_slug.substr(2)] === 2"> <XCircleIcon class="w-8 h-8 text-red-600" /></div>
                                        </div>

                                        <div v-else-if="titulo_slug.substr(0,1) == 'i'">{{ number_format(clasegenerica[titulo_slug.substr(2)]) }}</div>
                                        <div v-else-if="titulo_slug.substr(0,1) == 'm'">{{ number_format(clasegenerica[titulo_slug.substr(2)],0,1) }}</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>    
                    </table>
                </div>
                <div class="flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
