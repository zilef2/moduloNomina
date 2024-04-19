<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, watchEffect,onMounted } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { useForm, router } from '@inertiajs/vue3';

import { BookOpenIcon, ArrowUpCircleIcon, ArrowDownCircleIcon } from '@heroicons/vue/24/solid';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import FestivosColombia from 'festivos-colombia';
import { CuantosFestivosEstaQuincena2 } from '@/global'


const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    // fromController: Object,
    breadcrumbs: Object,
    NumUsers: Number,
    NumReportes: Number,
    ini: Date,
    fin: Date,
    flash: String,
    NumReportesRecha: Number,
    NumReportesSinval: Number,
    haySinsalario: Number,

    //sigo
    NumReportesSigo: Number,
    NumReportesRechaSigo: Number,
    NumReportesSinvalSigo: Number,
})

const data = reactive({
    respuesta: '',
    params:{
        fecha_ini: '',
        quincena: '',
        arrayFestivos: '',
    },
    paramsSigo:{
        fecha_ini: '',
        quincena: '',
        arrayFestivos: '',
        year: '',
        month: '',
    },
    warnn:'',
    tiposSiigo:[],

})

onMounted(() => { });

watch(() => _.cloneDeep(data.params), debounce(() => {
        let params = pickBy(data.params)
        router.get(route("user.uploadexcel"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        })
    }, 30)
)
watch(() => _.cloneDeep(data.paramsSigo), debounce(() => {
        let paramsS = pickBy(data.paramsSigo)
        router.get(route("user.uploadexceSigo"), paramsS, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        })
    }, 30)
)


//formulario para importar usuarios
const formUp = useForm({
    archivo1: null,
});
//formulario para exportar el informe quincenal
const form = useForm({
    // nombre1: null,
    archivo1: null,
    fecha_ini: '',
    quincena: 1,
    NumeroDiasFestivos: 0
});

const form2 = useForm({// formulario para sigo
    archivosigo: null,
    quincena_sigo: 1,
    fecha_ini_sigo: '',
    NumeroDiasFestivos: 0
});

watch(() => form.fecha_ini, (newX) => {
      data.params.fecha_ini = form.fecha_ini
      data.params.quincena = form.quincena
      data.params.arrayFestivos =
          CuantosFestivosEstaQuincena2(data.params.quincena, newX.month, newX.year)
      // console.log('festivos: ',data.params.arrayFestivos)
      form.NumeroDiasFestivos = data.params.arrayFestivos.length
      console.log('festivos:',data.params.arrayFestivos)
})


watch(() => form2.fecha_ini_sigo,
    (newX) => {
      data.paramsSigo.year = form2.fecha_ini_sigo.year
      data.paramsSigo.month = form2.fecha_ini_sigo.month
      data.paramsSigo.quincena = form2.quincena_sigo
      data.paramsSigo.arrayFestivos = -1
})


watchEffect(() => {
    if (props.flash.warning) {
        data.warnn = props.flash.warning
    }
    if(form2.fecha_ini_sigo){
        // form2.NumeroDiasFestivos = CuantosFestivosEstaQuincena(form2.quincena_sigo,form2.fecha_ini_sigo.month,form2.fecha_ini_sigo.year)
        // console.log("🧈 debu form2.NumeroDiasFestivos:", form2.NumeroDiasFestivos);
    }
    // if(form.fecha_ini){
    //     form.NumeroDiasFestivos = CuantosFestivosEstaQuincena(form.quincena,form.fecha_ini.month,form.fecha_ini.year)
    // }
})

function uploadFile() {
    formUp.post(route('user.uploadexcelpost'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
            // data.respuesta = $page.props.flash.success
        },
        onError: () => null,
        onFinish: () => null,
    });
}
const downloadExcel = () => { window.open('users/export/' + form.NumeroDiasFestivos + '/' + form.quincena + '/' + (form.fecha_ini.month) + '/' + form.fecha_ini.year, '_blank') }
const downloadsigo = () => { window.open('users/downloadsigo/' + form2.NumeroDiasFestivos + '/' + form2.quincena_sigo + '/' + (form2.fecha_ini_sigo.month) + '/' + form2.fecha_ini_sigo.year, '_blank') }


// <!--<editor-fold desc="constates">-->
const QuincenaArray = [
    { 'value': null, 'label': 'seleccione una quincena' },
    { 'value': 1, 'label': 1 },
    { 'value': 2, 'label': 2 }
]
// const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];
const columnasImportarUser = [
    {value:'nombre',rule:'Obligatorio'},
    {value:'correo',rule:'Debe ser unico'},
    {value:'cedula',rule:'Debe ser un número'},
    {value:'telefono',rule:'Obligatorio'},
    {value:'celular',rule:'Obligatorio'},
    {value:'rol',rule:'empleado o administrativo '},
    {value:'cargo',rule:'Verificar que el rol exista'},
    {value:'fecha ingreso',rule:'fecha ingreso a la empresa'},
    {value:'sexo',rule:'Masculino o Femenino'},
    {value:'salario',rule:'Sin puntos ni comas'}
];

data.tiposSiigo = [
    '10- Horas extras diurnas 125%- Ingreso', //1 extra diurna
    '11- Horas extras nocturnas 175%- Ingreso', //2 extra noc

    // '25- Recargo dominical o festivo- Ingreso', //3 dom diurna
    '08- Hora extra recargo dominical o festivo- Ingreso', //3 dom diurna
    '06- Hora dominical o festiva nocturna- Ingreso', //4 dominical noc

    '07- Hora extra diurna dominical o festiva- Ingreso', //5 dom extra diurna
    '12- Horas extras nocturnas dominical o festiva- Ingreso', //6 dom extra noc

    '26- Recargo nocturno- Ingreso', //7 noc
];
// <!--</editor-fold>-->

</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">

            <div v-if="data.warnn" class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <div class="flex max-w-screen-xl shadow-lg rounded-lg">
                        <div class="bg-yellow-600 py-4 px-6 rounded-l-lg flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="fill-current text-white" width="20" height="20"> <path fill-rule="evenodd" d="M8.22 1.754a.25.25 0 00-.44 0L1.698 13.132a.25.25 0 00.22.368h12.164a.25.25 0 00.22-.368L8.22 1.754zm-1.763-.707c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0114.082 15H1.918a1.75 1.75 0 01-1.543-2.575L6.457 1.047zM9 11a1 1 0 11-2 0 1 1 0 012 0zm-.25-5.25a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z"> </path> </svg>
                        </div>
                        <div
                            class="px-8 py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200">
                            <div>
                                {{ data.warnn ? data.warnn : '' }}
                            </div>
                            <!-- <button>
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-gray-700" viewBox="0 0 16 16" width="20" height="20"> <path fill-rule="evenodd" d="M3.72 3.72a.75.75 0 011.06 0L8 6.94l3.22-3.22a.75.75 0 111.06 1.06L9.06 8l3.22 3.22a.75.75 0 11-1.06 1.06L8 9.06l-3.22 3.22a.75.75 0 01-1.06-1.06L6.94 8 3.72 4.78a.75.75 0 010-1.06z"> </path> </svg>
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-12 mx-auto">
                        <div v-if="can(['update user'])" class="flex flex-wrap -m-4">
                            <!-- descargar quincena -->
                            <div class="p-4 w-full sm:w-1/2">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <ArrowDownCircleIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <form @submit.prevent="downloadExcel" id="downloadReporte" class="text-center">
                                            <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                                Formato xlsx</h2>
                                            <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Descargar reporte
                                            </h3>
                                            <p class="leading-relaxed mb-3"> Este formulario permite descargar el reporte de la quincena</p>

                                            <div class="">
                                                <div class="">
                                                    <!-- <InputLabel for="quincena" :value="lang().label.quincena" /> -->
                                                    <SelectInput v-model="form.quincena" :dataSet="QuincenaArray"
                                                        class="my-2 py-3 block w-full" />
                                                </div>
                                                <div class="col-span-1 lg:col-span-2">
                                                    <VueDatePicker month-picker auto-apply :teleport="true" id="fecha_ini"
                                                        type="date" class="mt-1 block w-full" v-model="form.fecha_ini" required
                                                        :placeholder="lang().placeholder.fecha_ini"
                                                        :error="form.errors.fecha_ini" />
                                                </div>
                                            </div>

                                            <PrimaryButton v-show="can(['create user'])" v-if="form.fecha_ini && props.NumReportes > 0 && props.haySinsalario == false"
                                                :disabled="form.fecha_ini == null"
                                                class="rounded-none my-4">
                                                Exportar Quincena
                                            </PrimaryButton>
                                        </form>
                                    </div>
                                    <section v-if="props.haySinsalario === 0" class="text-gray-600 body-font">
                                        <div class="container p-5 mx-auto">
                                            <div class="text-center mb-1">
                                                <h1 class="text-xl font-medium text-center title-font text-gray-900 mb-4">
                                                    Recuerde</h1>
                                                <p class="text-base leading-relaxed w-full mx-auto">
                                                    Solo se descargarán, Los reportes que sean validos.</p>
                                                <p v-if="props.NumReportes != 0" class="text-xl leading-relaxed w-full mx-auto">
                                                    Para esta fecha, hay <b>{{ props.NumReportes }}</b> reportes.</p>
                                                <p v-if="props.NumReportes != 0" class="text-base leading-relaxed w-full mx-auto">
                                                    <small>{{ props.ini }}</small> - <small>{{ props.fin }}</small>
                                                </p>
                                                <p v-if="props.NumReportes != 0" class="text-xl leading-relaxed w-full mx-auto">
                                                    Existen <b>{{ props.NumReportesRecha }}</b> rechazados
                                                    y <b>{{ props.NumReportesSinval }}</b> sin aprobar.</p>
                                            </div>
                                        </div>
                                    </section>
                                  <section v-else class="text-gray-600 body-font">
                                    <div class="container p-5 mx-auto">
                                      <div class="text-center mb-1">
                                        <h1 class="text-xl font-medium text-center title-font text-gray-900 mb-4">
                                          Hay empleados con salario en cero</h1>
                                      </div>
                                    </div>
                                  </section>
                                </div>
                            </div>



                            <!-- sigoo -->
                            <div class="p-4 w-full sm:w-1/2">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <BookOpenIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <form @submit.prevent="downloadsigo" id="downloadReporte" class="text-center">
                                            <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                                Seleccione quincena</h2>
                                            <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Descargar siigo </h3>
                                            <p class="leading-relaxed mb-3"> Este formulario permite descargar el reporte de la quincena para siigo</p>

                                            <div class="flex">
                                                <!-- <InputLabel for="quincena_si" :value="lang().label.quincena_sigo" /> -->
                                                <SelectInput v-model="form2.quincena_sigo" id="quincena_si"
                                                             :dataSet="QuincenaArray" class="mt-1 block w-1/3 mx-1" />
                                                <VueDatePicker month-picker auto-apply :teleport="true" id="fecha_ini_sigo"
                                                               type="date" v-model="form2.fecha_ini_sigo"
                                                               required :placeholder="lang().placeholder.fecha_ini_sigo"
                                                               :error="form2.errors.fecha_ini_sigo"
                                                               class="mt-1 w-1/3"/>
                                            </div>

                                            <PrimaryButton v-if="form2.fecha_ini_sigo.year"
                                                           :disabled="form2.fecha_ini_sigo == null" class="rounded-none my-4">
                                                Descargar formato siigo
                                            </PrimaryButton>
                                        </form>
                                    </div>

                                    <section class="text-gray-600 body-font">
                                        <div class="container p-5 mx-auto">
                                            <div
                                                v-if="props.NumReportesSigo || props.NumReportesRechaSigo || props.NumReportesSinvalSigo"
                                                class="text-center mb-1">
                                                <p># Reportes validos: {{ props.NumReportesSigo }}</p>
                                                <p># Reportes rechazados: {{ props.NumReportesRechaSigo }}</p>
                                                <p># Reportes sin validar: {{ props.NumReportesSinvalSigo }}</p>
                                                <h1 v-if="!props.NumReportesSigo" class="text-xl font-medium text-center title-font text-gray-900 mb-4">
                                                    Este formato incluye
                                                </h1>
                                                <p class="text-base leading-relaxed w-full mx-auto">
                                                    <!-- Solo se descargarán, Los reportes que sean validos. -->
                                                </p>

                                                <p v-for="tiposigo in data.tiposSiigo" class="text-base leading-relaxed w-full mx-auto">
                                                    <p>{{ tiposigo }}</p>
                                                </p>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>

                            <!-- subir usuarios-->
                            <div class="p-4 w-full sm:w-1/2">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <ArrowUpCircleIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <h3 class="mx-auto  text-center title-font text-xl font-medium text-gray-900 mb-2">Subir empleados</h3>
                                        <h2 class="mb-4 tracking-widest text-center text-sm title-font font-medium text-gray-400 dark:text-gray-100">
                                            Formato xlsx</h2>
                                        <p class="leading-relaxed mb-3"> Este formulario solo permite cargar empleados o administrativos</p>
                                        <form @submit.prevent="uploadFile" id="upload">
                                            <input type="file" @input="formUp.archivo1 = $event.target.files[0]"
                                                   accept="application/vnd.openxmlformUpats-officedocument.spreadsheetml.sheet" />
                                            <progress v-if="formUp.progress" :value="formUp.progress.percentage" max="100">
                                                {{ formUp.progress.percentage }}%
                                            </progress>

                                            <div class="w-full">
                                                <PrimaryButton v-show="can(['create user'])" :disabled="formUp.archivo1 == null"
                                                               class="rounded-none my-4">
                                                    {{ lang().button.subir }}
                                                </PrimaryButton>
                                            </div>
                                        </form>

                                        <div class="flex items-center flex-wrap my-6">
                                            <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">
                                                Numero de Empleados: {{props.NumUsers}}
                                            </a>

                                            <section class="text-gray-600 body-font">
                                                <div class="container p-5 mx-auto">
                                                    <div class="text-center mb-1">
                                                        <h1 class="text-xl font-medium text-center title-font text-gray-900 mb-4">
                                                            Formato del excel
                                                        </h1>
                                                        <p class="text-base leading-relaxed w-full mx-auto">
                                                            Solo la primera fila debe ser la misma
                                                        </p>
                                                    </div>
                                                    <div class="flex flex-wrap sm:mx-auto sm:mb-2 -mx-2">
                                                        <div v-for="columna in columnasImportarUser" class="px-2 w-full">
                                                            <div class="bg-gray-50 rounded flex p-1 h-full items-center">
                                                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" class="text-indigo-500 w-6 h-6 flex-shrink-0 mr-4" viewBox="0 0 24 24"> <path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path> <path d="M22 4L12 14.01l-3-3"></path> </svg>
                                                                <span class="title-font font-medium">
                                                                      {{columna.value}} : {{ columna.rule }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

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
