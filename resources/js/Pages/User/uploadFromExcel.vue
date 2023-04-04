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
    import { useForm } from '@inertiajs/vue3';

    import { BookOpenIcon, ChevronUpDownIcon, PencilIcon, TrashIcon,XCircleIcon,CheckIcon } from '@heroicons/vue/24/solid';

    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'


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

    function getHoursColombianRandom(otraVez) {

        let hora_inicial = '09'
        let vec_final = ['15','21','23','24']
        let num = Math.floor(Math.random() * (vec_final.length-1));

        if(otraVez) return vec_final[num]

        let hora_final = vec_final[num]
        return [hora_inicial,hora_final];
    }

    const horas = getHoursColombianRandom()

    const form = useForm({
        // nombre1: null,
        archivo1: null,
        fecha_ini:'',
        quincena: 1
        // fecha_ini: '2023-03-03T'+horas[0]+':00', //toerase
        // fecha_fin: '2023-04-03T'+horas[1]+':00', //toerase
    });

    const QuincenaArray = [
        {'value' : null, 'label': 'seleccione una quincena'},
        {'value' : 1, 'label': 1},
        {'value' : 2, 'label': 2}
    ]
    
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
    // function downloadExcel() {
    //     form.post(route('reporte1'), {
    //         preserveScroll: true,
    //         onSuccess: () => {
    //             form.reset()
    //         },
    //         onError: () => alert('Error inesperado!'),
    //         // onFinish: () => null,
    //     });
    // }
    const downloadExcel = () => {
        // alert(Date.parse(form.fecha_ini) + ' ____' + form.quincena+'___'+form.fecha_ini.month+'--'+form.fecha_ini.year)
        window.open('users/export/'+form.quincena+'/'+(form.fecha_ini.month)+'/'+form.fecha_ini.year, '_blank')
    }



    // let downloadExcel = async() => {
    //     window.open('/users/export?ini=${form.fecha_ini}/fin=${form.fecha_fin}', '_blank')
    //     // window.open('/users/export?ini=${form.fecha_ini}/fin=${form.fecha_fin}', '_blank')
    // }

    // const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];

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
                        <div v-if="can(['isAdmin'])" class="flex flex-wrap -m-4">
                            <div class="p-4 md:w-1/3">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <BookOpenIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <form @submit.prevent="uploadFile" id="upload">
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
                                                1<!-- todo: numero de admins -->
                                            </span>
                                            <span class="text-gray-400 inline-flex items-center leading-none text-sm">
                                                <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                                                </svg>
                                                2<!-- todo: numero de usuarios -->
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- descargar usuarios -->
                            <div class="p-4 md:w-1/3">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <BookOpenIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <form @submit.prevent="downloadExcel" id="downloadReporte">
                                            <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">Seleccione quincena</h2>
                                            <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Descargar reporte</h3>
                                            <p class="leading-relaxed mb-3"> Este formulario permite descargar el reporte de la quincena</p>
                                            
                                            <InputLabel for="quincena" :value="lang().label.quincena" />
                                            <SelectInput v-model="form.quincena" 
                                            :dataSet="QuincenaArray" class="mt-1 block w-full" />

                                            <VueDatePicker month-picker auto-apply :teleport="true"
                                                id="fecha_ini" type="date" class="mt-1 block w-full" v-model="form.fecha_ini" required
                                                :placeholder="lang().placeholder.fecha_ini" :error="form.errors.fecha_ini" />
                                            <!-- <VueDatePicker month-picker auto-apply :teleport="true"
                                                id="fecha_fin" type="date" class="mt-1 block w-full" v-model="form.fecha_fin" required
                                                :placeholder="lang().placeholder.fecha_fin" :error="form.errors.fecha_fin" /> -->
                                            <PrimaryButton v-show="can(['create user'])"  :disabled="form.fecha_ini == null" class="rounded-none my-4">
                                                {{ lang().label.downloadUsers }}
                                            </PrimaryButton>
                                        </form>
                                       
                                        <!-- <div class="flex items-center flex-wrap my-6">
                                            <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0"> 
                                            </a>
                                            <span class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1">
                                                <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                1
                                            </span>
                                        </div> -->
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
