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
    import { BookOpenIcon, ChevronUpDownIcon, PencilIcon, TrashIcon,XCircleIcon,CheckIcon } from '@heroicons/vue/24/solid';

    import Checkbox from '@/Components/Checkbox.vue';
    import InfoButton from '@/Components/InfoButton.vue';
    import SuccessButton from '@/Components/SuccessButton.vue';



    const { _, debounce, pickBy } = pkg
    const props = defineProps({
        title: String,
        // fromController: Object,
        breadcrumbs: Object,
    })
    const data = reactive({
        // selectedId: [],
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

    const form = useForm({
        // nombre1: null,
        archivo1: null,
    });
    
    // function handleFileUpload() {
    //     form.archivo1 = this.$refs.fileInput.files[0];
    // }

    function uploadFile() {
        form.post(route('user.uploadexcelpost'), {
            preserveScroll: true,
            onSuccess: () => {
                // emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        });
    }
</script>

<template>
    <Head :title="props.title" ></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">

            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <p class="leading-relaxed mb-3">
                        <!-- Estos formularios permiten -->
                        Para cargar informacion en la base de datos a través de un archivo Excel,
                        solo necesitas seleccionar el archivo y hacer clic en "Subir" 
                    </p>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-24 mx-auto">
                        <div class="flex flex-wrap -m-4">
                            <div class="p-4 md:w-1/3">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <BookOpenIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <form @submit.prevent="uploadFile">
                                            <!-- <input type="text" v-model="form.nombre1" /> -->
                                            <input type="file" @input="form.archivo1 = $event.target.files[0]" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                                {{ form.progress.percentage }}%
                                            </progress>
                                            <PrimaryButton v-show="can(['create user'])"  :disabled="form.archivo1 == null" class="rounded-none my-4">
                                                {{ lang().button.subir }}
                                            </PrimaryButton>
                                        </form>
                                        <!-- <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">Archivos de excel</h2>
                                        <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Subir usuarios</h3>
                                        <p class="leading-relaxed mb-3"> Este formulario permite cargar usuarios. </p>
                                        <input id="archivo1"  type="file" ref="fileInput" @change="handleFileUpload" accept=".xls,.xlsx"> -->
                                       
                                        
                                        <div class="flex items-center flex-wrap my-6">
                                            <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Numero de Usuarios 
                                            </a>
                                            <span class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                1<!-- numero de admins -->
                                            </span>
                                            <span class="text-gray-400 inline-flex items-center leading-none text-sm">
                                                <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                                                </svg>
                                                2<!-- numero de usuarios -->
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
