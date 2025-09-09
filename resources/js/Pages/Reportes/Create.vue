<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, ref, watch, watchEffect} from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import "vue-select/dist/vue-select.css";
import vSelect from "vue-select";

import FestivosColombia from 'festivos-colombia';
import {TransformTdate, weekNumber} from "@/global";
import {calcularHoras} from "@/CreateReporte/HelpingCreate";
import {validacionNoMasDe3Diax} from "@/ValidacionCreateReporte";
import {calcularSinExtras} from "./ComunCreateReporte";
import {consolelog} from "./ComunCreateReporte";


const props = defineProps({
    show: Boolean,
    title: String,
    valoresSelect: Object,
    IntegerDefectoSelect: Number,
    horasemana: Number,
    startDateMostrar: String,
    endDateMostrar: String,
    numberPermissions: Number,
    ArrayOrdinarias: Object, //[] //las ordinarias de esta semana y la pasada
    //tambien tiene las de ayer si terminaron en 11:59pm

    horasTrabajadasHoy: Object, //[]  es por si reportaron el mismo dia
    HorasDeCadaSemana: Object,//[]
    ArrayHorasSemanales: Object,//[]
    ArrayCentrosNoFactura: Object,//[]
})

//theconfig
let label_diurnas = ref(null)

const HORAS_ESTANDAR = props.ArrayHorasSemanales.HORAS_ORDINARIAS // 8horitas
const HORAS_SEMANALES_MENOS_ESTANDAR = props.ArrayHorasSemanales.MAXIMO_HORAS_SEMANALES - HORAS_ESTANDAR //notes: = 40
console.log("=>(Create.vue:42) props.ArrayHorasSemanales.MAXIMO_HORAS_SEMANALES", props.ArrayHorasSemanales.MAXIMO_HORAS_SEMANALES);
console.log("=>(Create.vue:42) HORAS_SEMANALES_MENOS_ESTANDAR", HORAS_SEMANALES_MENOS_ESTANDAR);
const LimiteHorasTrabajadas = 25

const Limite_Horas_Dia_Semana = [HORAS_ESTANDAR, (HORAS_ESTANDAR - 2)] //todo: nexttochange: cuando las semanales cambien de 46 horas a 42, seran 3 en vez de 2


const emit = defineEmits(["close", "reportFinished"]);
const data = reactive({
    respuestaSeguro: '',
    startTime: {hours: 7, minutes: 0}, //valor que por defecto, viene la hora inicial cuando se abre el formulario
    estado2359: false,
    TrabajadasHooy: 0,
    TrabajadasSemana: 0,
    diaini: 1,
    MensajeError: '',
    const: {
        //39 =>
        HORAS_SEMANALES_MENOS_ESTANDAR: props.ArrayHorasSemanales.MAXIMO_HORAS_SEMANALES - props.ArrayHorasSemanales.HORAS_ORDINARIAS
    },
    Horas1159: props.ArrayOrdinarias.length > 1,
    diaSelected: 0,
    TemporalDiaAnterior: 0,
    debugHorasSemana: 0,
    BoolCentrosNoFactura: false, //permite saber si el centro de costos factura o no
    StringRestriccionNoFActura: '',
})

const message = reactive({
    TextFestivo: ''
})
// <!--<editor-fold desc="onMounted - useForm">-->

onMounted(() => {

    //explaining: data.TrabajadasSemana solo se usa cuando se pasa de las horas que debe trabajar por semana 
    data.TrabajadasSemana = props.HorasDeCadaSemana[props.HorasDeCadaSemana[0]] > HORAS_SEMANALES_MENOS_ESTANDAR ?
        props.HorasDeCadaSemana[props.HorasDeCadaSemana[0]] - HORAS_SEMANALES_MENOS_ESTANDAR : 0

    //explaining: data.TrabajadasSemana son las horas que hay que restarle a Cuandocomienzaextras
    data.TrabajadasSemana = data.TrabajadasSemana > HORAS_ESTANDAR ? HORAS_ESTANDAR : data.TrabajadasSemana

    console.clear()
    if (consolelog.MostrarTrabajadaSemana) {
        console.log("=>(Create.vue:86) props.HorasDeCadaSemana", props.HorasDeCadaSemana);
        console.log("=>(Create.vue:86) data.TrabajadasSemana", data.TrabajadasSemana);
    }

    //todo: tosync all repositories
    //explaining: recuperamos el centro de costo que habiamos selecciopnado previamente
    if (localStorage.getItem('centroCostoId')) {
        form.centro_costo_id = props.valoresSelect.find((ele) => {
            return ele.value === localStorage.getItem('centroCostoId')
        })
        // form.centro_costo_id = localStorage.getItem('centroCostoId')
    }
});


const form = useForm({
    fecha_ini: '', fecha_fin: '',
    // fecha_ini: '2023-04-0'+diaoDominicla+'T'+horas[0]+':00', fecha_fin: '2023-04-0'+(diaoDominicla)+'T'+horas[1]+':00', //temp
    // fecha_ini: '2023-06-01T23:00', fecha_fin: '2023-06-01T23:59', //temp

    centro_costo_id: {'value': 0, 'label': 'Seleccione un centro'},
    observaciones: '',
    horas_trabajadas: '',
    almuerzo: '0',

    diurnas: 0,
    nocturnas: 0,
    extra_diurnas: 0,
    extra_nocturnas: 0,

    dominicales: 'no',
    extra: 'no',
    esFestivo: false,

    dominical_diurnas: 0,
    dominical_nocturnas: 0,
    dominical_extra_diurnas: 0,
    dominical_extra_nocturnas: 0
});

// <!--</editor-fold>-->

let newdate = (new Date())
let horahoy = newdate.getHours()
if (props.numberPermissions > 9) {
// if (props.numberPermissions > 8) {
    // form.fecha_ini = '2024-11-15T21:00'
    // form.fecha_fin = '2024-11-15T23:58'
    form.fecha_ini = '2025-02-08T07:00'
    form.fecha_fin = '2025-02-08T14:00'
} else {
    let timedate = TransformTdate(7)//la hora
    let timedate2 = TransformTdate(16)
    form.fecha_ini = timedate
    form.fecha_fin = timedate2
}


watchEffect(() => {
    if (props.show) {
        if (form.centro_costo_id == null
            || (typeof form.centro_costo_id.value === "undefined")
            || form.centro_costo_id.value === 0) {

            form.centro_costo_id = props.valoresSelect.find((ele) => {
                return ele.value == props.IntegerDefectoSelect
            })
        }


        // explaining: El dia anterior trabajo? 
        if (data.diaSelected) {
            data.HorasDelDiaAnterior59 = props.ArrayOrdinarias[data.diaSelected];

            // console.log("=>(Create.vue:171) data anterior", data.diaSelected);
            // console.log("=>(Create.vue:664) props.ArrayOrdinarias", props.ArrayOrdinarias);
        }
        form.errors = {}
        let Dateinii = Date.parse(form.fecha_ini);
        if (Dateinii && Date.parse(form.fecha_fin)) { // son fechas?
            data.diaini = parseInt(new Date(form.fecha_ini).getDate())
            data.TrabajadasHooy = (props.horasTrabajadasHoy[data.diaini]) ?? 0
            data.TrabajadasHooy = parseInt(data.TrabajadasHooy)

            let ini = Date.parse(form.fecha_ini);
            let fin = Date.parse(form.fecha_fin);

            let WeekN = weekNumber(new Date(form.fecha_ini))
            if (consolelog.MostrarTrabajadaSemana) {

                console.log("=>(Create.vue:190) WeekN", WeekN);
                console.log("=>(data.TrabajadasHooy", data.TrabajadasHooy);
            }

            // 38 => 46 - 8
            data.TrabajadasSemana = props.HorasDeCadaSemana[WeekN] > HORAS_SEMANALES_MENOS_ESTANDAR ?
                props.HorasDeCadaSemana[WeekN] - HORAS_SEMANALES_MENOS_ESTANDAR : 0

            data.TrabajadasSemana = data.TrabajadasSemana > HORAS_ESTANDAR ? HORAS_ESTANDAR : data.TrabajadasSemana

            form.horas_trabajadas = 0
            form.almuerzo = 0

            form.diurnas = 0
            form.nocturnas = 0
            form.extra_diurnas = 0
            form.extra_nocturnas = 0
            form.dominical_diurnas = 0
            form.dominical_nocturnas = 0
            form.dominical_extra_diurnas = 0
            form.dominical_extra_nocturnas = 0
            form.horas_trabajadas = parseInt((fin - ini) / (3600 * 1000));


            //si es 11:59 minutos -> agrega una hora
            if (data.estado2359) {
                form.horas_trabajadas++
                if (form.nocturnas < 8 && form.nocturnas >= 0) {
                    form.nocturnas++
                } else
                    form.extra_nocturnas++
            }

            if (ini > fin) {
                form.errors.horas_trabajadas = 'La fecha inicial no debe ser posterior a la final'
            } else {
                if (form.horas_trabajadas >= LimiteHorasTrabajadas) {
                    form.errors.horas_trabajadas = 'Las horas trabajadas son demasiadas'
                    form.horas_trabajadas = "0"
                    form.diurnas = "0"
                    form.nocturnas = "0"
                    form.almuerzo = "0"
                } else {
                    const DiaInicio = new Date(ini).getDate();
                    const DiaFin = new Date(fin).getDate();
                    if (DiaInicio !== DiaFin) {
                        form.errors.horas_trabajadas = 'Debe realizar dos reportes'
                        form.horas_trabajadas = "0"
                        form.diurnas = "0"
                        form.nocturnas = "0"
                        form.almuerzo = "0"
                    } else {
                        /*
                            la ini < fin
                            horas < LimiteHorasTrabajadas
                            Diainicio == dia final
                        */


                        let ccid = parseInt(form.centro_costo_id.value)
                        if (props.ArrayCentrosNoFactura.includes(ccid)) {
                            let dirunasYnocturnas = calcularSinExtras(ini, fin)
                            form.diurnas = dirunasYnocturnas[0];
                            form.nocturnas = dirunasYnocturnas[1];
                            data.BoolCentroNoFactura = true
                            data.mensajeCentroNoFactura = "Este centro no factura"
                            data.BoolCentrosNoFactura = true

                        } else {
                            data.BoolCentroNoFactura = false
                            calcularHoras(
                                data, form,
                                ini, fin,
                                HORAS_ESTANDAR
                                , FestivosColombia, message
                            );
                        }
                    }
                }
            }
        }
    }
})

// <!--<editor-fold desc="Watchers">-->

watch(() => form.centro_costo_id, (newX) => {
    if (newX && newX.value) {

        localStorage.setItem('centroCostoId', newX.value)
        data.BoolCentrosNoFactura = (props.ArrayCentrosNoFactura.includes(parseInt(newX.value)))
        analisarSiFactura()
    }
})

watch(() => form.diurnas, (newX) => {
    data.ordinariasCalculadas = newX + form.nocturnas
})
watch(() => form.nocturnas, (newX) => {
    data.ordinariasCalculadas = newX + form.diurnas
})

watch(() => form.horas_trabajadas, (newX) => {
    // const hoy = new Date();
    analisarSiFactura()
})

function analisarSiFactura() {

    if (data.BoolCentrosNoFactura) {
        let hoy = new Date(form.fecha_ini)
        let esSabado = hoy.getDay() === 6;
        let valormax = esSabado ? Limite_Horas_Dia_Semana[1] : Limite_Horas_Dia_Semana[0] //[8,6]
        if (form.horas_trabajadas > valormax) {
            data.StringRestriccionNoFActura = 'No se puede reportar mas de ' + valormax + ' horas'
        } else {
            data.StringRestriccionNoFActura = ''
        }
    } else {
        data.StringRestriccionNoFActura = ''
    }
}


watch(() => form.fecha_ini, (newX) => {
    if (newX) {
        form.fecha_fin = newX
        let fechaSelected = new Date(newX.getTime());
        fechaSelected.setDate(fechaSelected.getDate() - 1)
        let year = fechaSelected.getFullYear();
        let month = fechaSelected.getMonth() + 1; // Los meses en JavaScript son de 0 a 11, así que sumamos 1
        let day = fechaSelected.getDate();

        data.diaSelected = year + '-' + month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
    }
})
watch(() => form.fecha_fin, (newX) => {
    let FechaFini = new Date(newX)
    let HoraFini = FechaFini.getHours() + ':' + FechaFini.getMinutes()
    if (HoraFini === '23:59') {
        data.estado2359 = true

        if (data.estado2359) {
            form.horas_trabajadas++
            if (form.nocturnas < 8 && form.nocturnas >= 0) {
                //todo: solo se verifica que no se pase de 8 horas nocturnas. deberia verificar con CUANDOEMPIEZANOCTURNAS
                form.nocturnas++
            } else
                form.extra_nocturnas++
        }
    } else {
        if (data.estado2359) {
            data.estado2359 = false
        }
    }
})
// <!--</editor-fold>-->


// <!--<editor-fold desc="create and formats">-->
const create = () => {
    data.MensajeError = ''
    data.respuestaSeguro = true

    if (form.horas_trabajadas <= LimiteHorasTrabajadas && form.horas_trabajadas !== 0) {
        if (Object.keys(form.errors).length === 0) {
            if (props.numberPermissions < 9) {
                data.respuestaSeguro = confirm("¿Estás seguro de enviar el formulario?");
            }
            console.log("111) ArrayHorasSemanales['s_Dias_gabela']", props.ArrayHorasSemanales['s_Dias_gabela']);
            console.log("222) props.ArrayHorasSemanales['s_Dias_gabela']", typeof (props.ArrayHorasSemanales['s_Dias_gabela']));
            let validacionNoMasDe3Dias
            if (props.numberPermissions > 9) validacionNoMasDe3Dias = 'ok'
            else validacionNoMasDe3Dias = validacionNoMasDe3Diax(form.fecha_ini, props.ArrayHorasSemanales['s_Dias_gabela'])
            console.log("=>(Create.vue:302) validacionNoMasDe3Dias", validacionNoMasDe3Dias);
            if (data.respuestaSeguro && validacionNoMasDe3Dias === 'ok') {

                form.almuerzo = data.ValorRealalmuerzo
                emit("reportFinished")
                form.post(route('Reportes.store'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        emit("close")
                        form.reset()
                        setTimeout(() => {
                            location.reload();
                        }, 2500);
                    },
                    onError: () => {
                        alert(JSON.stringify(form.errors, null, 4));
                    },
                    onFinish: () => {
                    }
                })
            } else {
                data.MensajeError = validacionNoMasDe3Dias
            }
        } else {
            data.MensajeError = 'Verifique de nuevo'
        }
    } else {
        data.MensajeError = 'Horas invalidas'
    }
}

const daynames = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];

const formatini = (date) => {
    const day = date.getDate();
    // const month = date.getMonth() + 1;
    const year = date.getFullYear();
    const month = date.toLocaleString('es-CO', {month: 'long', timeZone: 'America/Bogota'});

    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // El '0' se debe mostrar como '12'
    hours = hours.toString().padStart(2, '0');

    return `${day}/${month}/${year}  ${hours}:${minutes} ${ampm}`;
}

const formatfin = (date) => {
    // const month = date.toLocaleString('default', { month: 'long', timeZone: 'America/Bogota' });
    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // El '0' se debe mostrar como '12'
    hours = hours.toString().padStart(2, '0');
    return `${hours}:${minutes} ${ampm}`;
}
// <!--</editor-fold>-->

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl4'">
            <form @submit.prevent="create" class="p-6 xs:mb-24 lg:mb-64">
                <div class="flex space-x-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        <b>
                            <!-- {{ lang().label.add }} -->
                            {{ props.title }}
                        </b>
                    </h2>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Horas semana: {{ props?.horasemana }}
                    </h2>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ props?.startDateMostrar }} -
                        {{ props?.endDateMostrar }}
                    </h2>
                </div>
                <div class="my-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini"/>
                        <VueDatePicker :format="formatini" :start-time="data.startTime" :is-24="false"
                                       :day-names="daynames" auto-apply :enable-time-picker="true"
                                       id="fecha_ini" type="date" class="mt-1 block w-full" v-model="form.fecha_ini"
                                       required
                                       :placeholder="lang().placeholder.fecha_ini" :error="form.errors.fecha_ini"/>
                        <InputError class="mt-2" :message="form.errors.fecha_ini"/>
                    </div>
                    <div>
                        <InputLabel for="fecha_fin" :value="lang().label.fecha_fin"/>
                        <VueDatePicker :format="formatfin" :is-24="false"
                                       :day-names="daynames" auto-apply :flow="['time']" :enable-time-picker="true"
                                       :teleport="true"
                                       time-picker-inline
                                       id="fecha_fin" type="date" class="mt-1 block w-full" v-model="form.fecha_fin"
                                       required
                                       :placeholder="lang().placeholder.fecha_fin" :error="form.errors.fecha_fin"/>
                        <InputError class="mt-2" :message="form.errors.fecha_fin"/>
                    </div>
                    <div>
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas"/>
                        <TextInput id="horas_trabajadas" type="number"
                                   class="bg-gray-100 dark:bg-gray-700 mt-1 block w-full"
                                   v-model="form.horas_trabajadas" disabled
                                   :placeholder="lang().placeholder.horas_trabajadas"
                                   :error="form.errors.horas_trabajadas"/>
                        <InputError class="bg-gray-100 dark:bg-gray-700 mt-2" :message="form.errors.horas_trabajadas"/>
                    </div>

                    <div>
                        <InputLabel for="almuerzo" :value="lang().label.horacomida + ' (+9 horas)'"/>
                        <TextInput id="almuerzo" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.almuerzo" disabled
                                   :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo"/>
                    </div>
                    <!-- mt-80 -->
                    <div class="mt-4 grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="diurnas" :value="lang().label.diurnas"/>
                            <TextInput id="diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                       v-model="form.diurnas" disabled
                                       :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas"/>
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="nocturnas" :value="lang().label.nocturnas"/>
                            <TextInput id="nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                       v-model="form.nocturnas" disabled
                                       :placeholder="lang().placeholder.nocturnas" :error="form.errors.nocturnas"/>
                        </div>
                    </div>
                    <!-- mt-80 -->
                    <div v-if="form.extra_diurnas || form.extra_nocturnas || form.dominicales === 'si'"
                         class="mt-4 grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="extra_diurnas"
                                        :value="lang().label.extra_diurnas"/>
                            <TextInput id="extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                       v-model="form.extra_diurnas" disabled
                                       :placeholder="lang().placeholder.extra_diurnas"
                                       :error="form.errors.extra_diurnas"/>
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="extra_nocturnas"
                                        :value="lang().label.extra_nocturnas"/>
                            <TextInput id="extra_nocturnas" type="number"
                                       class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_nocturnas"
                                       disabled
                                       :placeholder="lang().placeholder.extra_nocturnas"
                                       :error="form.errors.extra_nocturnas"/>
                        </div>
                    </div>
                    <!-- dominicales -->
                    <div v-if="form.dominicales === 'si'" class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="dominical_diurnas"
                                        :value="lang().label.dominical_diurnas"/>
                            <TextInput id="dominical_diurnas" type="number"
                                       class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_diurnas"
                                       disabled
                                       :placeholder="lang().placeholder.dominical_diurnas"
                                       :error="form.errors.dominical_diurnas"/>
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="dominical_nocturnas"
                                        :value="lang().label.dominical_nocturnas"/>
                            <TextInput id="dominical_nocturnas" type="number"
                                       class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                       v-model="form.dominical_nocturnas" disabled
                                       :placeholder="lang().placeholder.dominical_nocturnas"
                                       :error="form.errors.dominical_nocturnas"/>
                        </div>
                    </div>
                    <div v-if="form.dominicales === 'si'" class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="dominical_extra_diurnas"
                                        :value="lang().label.dominical_extra_diurnas"/>
                            <TextInput id="dominical_extra_diurnas" type="number"
                                       class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                       v-model="form.dominical_extra_diurnas" disabled
                                       :placeholder="lang().placeholder.dominical_extra_diurnas"
                                       :error="form.errors.dominical_extra_diurnas"/>
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="dominical_extra_nocturnas"
                                        :value="lang().label.dominical_extra_nocturnas"/>
                            <TextInput id="dominical_extra_nocturnas" type="number"
                                       class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                       v-model="form.dominical_extra_nocturnas" disabled
                                       :placeholder="lang().placeholder.dominical_extra_nocturnas"
                                       :error="form.errors.dominical_extra_nocturnas"/>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span v-if="data.BoolCentroNoFactura"
                              class="mx-2 px-1 text-sky-700 bg-sky-400/10 dark:text-white">
                            {{ data.mensajeCentroNoFactura }}
                        </span>
                        <InputLabel v-else for="centro_costo_id" :value="lang().label.centro_costo_id"/>

                        <!--                        <SelectInput v-model="form.centro_costo_id" :dataSet="props.valoresSelect"-->
                        <!--                                     class="mt-1 block w-full"/>-->
                        <vSelect v-model="form.centro_costo_id" :options="props.valoresSelect"
                                 label="label" class="mt-2"></vSelect>
                        <InputError class="mt-2" :message="form.errors.centro_costo_id"/>
                    </div>

                    <div v-if="form.dominicales === 'si'" class="mt-4">
                        <label class="dark:text-white">Horario</label>
                        <TextInput id="dominicales" type="text" class="bg-gray-100 dark:bg-gray-700 block w-full"
                                   v-model="message.TextFestivo" disabled/>
                    </div>
                </div>
                <!-- <div class="my-6 ">
                    <InputLabel for="observaciones" :value="lang().label.observaciones" />
                        <textarea
                            id="observaciones" type="text" v-model="form.observaciones"
                            class="mt-1 block w-full rounded-md shadow-sm dark:bg-black dark:text-white placeholder:text-gray-400 placeholder:dark:text-gray-400/50"
                            cols="30" rows="3" :error="form.errors.observaciones">
                        </textarea>
                    <InputError class="mt-2" :message="form.errors.observaciones" />
                </div> -->

                <div class="flex justify-end mx-2 mt-1">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{
                            lang().button.close
                        }}
                    </SecondaryButton>
                    <PrimaryButton v-if="data.StringRestriccionNoFActura === ''"
                                   class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @mouseup="create" @keyup.enter="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                    <p v-else class="ml-4 mt-1">{{ data.StringRestriccionNoFActura }}</p>
                </div>
                <div 
                    v-if="props.ArrayOrdinarias[props.ArrayOrdinarias.length] > 8 ||
                        props.ArrayOrdinarias[0] > data.const.HORAS_SEMANALES_MENOS_ESTANDAR && form.diurnas + form.nocturnas > 8 ||
                        data.HorasDelDiaAnterior59 ||
                        data.MensajeError !== '' ||
                        data.TrabajadasSemana ||
                        data.TrabajadasHooy ||
                        data.debugHorasSemana"
                     class="flex justify-end my-3">
                    <p class="mx-1">Hay pendientes</p>
                    
                    <p v-if="props.ArrayOrdinarias[props.ArrayOrdinarias.length] > 8" class="mx-2">
                        
                        {{ props.ArrayOrdinarias[props.ArrayOrdinarias.length] }} horas (11:59pm)</p>
                    <p v-if="props.ArrayOrdinarias[0] > data.const.HORAS_SEMANALES_MENOS_ESTANDAR &&
                                form.diurnas + form.nocturnas > 8"
                       class="mx-2">Horas extra de la semana</p>

                    <p v-if="data.HorasDelDiaAnterior59" class="mx-2">Dia anterior (11:59 p.m.)</p>
                    <p v-if="data.MensajeError !== ''" class="mx-2 text-red-600 text-lg">
                        {{ data.MensajeError }}
                    </p>

                    <p v-if="data.TrabajadasSemana" class="mx-2 px-1 text-sky-700 bg-sky-400/10 dark:text-white">
                        <small>{{ data.TrabajadasSemana }} </small> horas de la semana
                    </p>
                    <p v-if="data.TrabajadasHooy" class="mx-2 px-1 text-sky-700 bg-sky-400/10 dark:text-white">
                        <small>{{ data.TrabajadasHooy }} </small> horas de hoy
                    </p>
                    <!--                    <p v-if="data.TemporalDiaAnterior" class="mx-2 text-amber-700 bg-amber-400/10 rounded-2xl">-->
                    <!--                        <small v-if="numberPermissions > 8">{{ data.TemporalDiaAnterior }}</small>-->
                    <!--                    </p>-->
                    <p v-if="data.debugHorasSemana" class="mx-2 text-amber-700 bg-amber-400/10 rounded-2xl">
                        <small v-if="numberPermissions > 8">{{ data.debugHorasSemana }}</small>
                    </p>

                </div>
            </form>
        </Modal>
    </section>
</template>
