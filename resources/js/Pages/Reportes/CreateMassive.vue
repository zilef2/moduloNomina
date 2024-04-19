<script setup>
import Modal from '@/Components/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import SelectInput from '@/Components/SelectInput.vue';
    // import Checkbox from '@/Components/Checkbox.vue';
import { useForm } from '@inertiajs/vue3';


import {ref, watchEffect, reactive, onMounted, watch} from 'vue';

    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'

import FestivosColombia from 'festivos-colombia';

const props = defineProps({
  show: Boolean,
  title: String,
  valoresSelect:Object,
  IntegerDefectoSelect: Number,
  horasemana: Number,
  startDateMostrar: String,
  endDateMostrar: String,
  numberPermissions: Number,
  ultimoReporte: Number,

})
// <!--<editor-fold desc="Data - const - useForm - onMounted">-->

let CurrentlyYear = new Date().getFullYear()
let holidays2022 = FestivosColombia.getHolidaysByYear(CurrentlyYear);
let label_diurnas = ref(null)
// const startTime = ref({ hours: 0, minutes: 0 });

//constantes intuitivas
const MAXIMO_HORAS_SEMANALES = 48
const LimiteHorasTrabajadas = 22
const TrabajoConAlmuerzo = 9 - props.ultimoReporte
//fin intuitivas
let ValorRealalmuerzo = 0
const emit = defineEmits(["close"]);
const data = reactive({
    respuestaSeguro:'',
    startTime: { hours: 7, minutes: 0 },

})

const form = useForm({
    fecha_ini: '', fecha_fin: '',
    // fecha_ini: '2023-04-0'+diaoDominicla+'T'+horas[0]+':00', fecha_fin: '2023-04-0'+(diaoDominicla)+'T'+horas[1]+':00', //temp
    // fecha_ini: '2023-06-01T23:00', fecha_fin: '2023-06-01T23:59', //temp

    centro_costo_id: props.IntegerDefectoSelect,
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


if(props.numberPermissions > 8){
    // form.fecha_ini = '2023-11-01T00:00'
    // form.fecha_fin = '2023-11-01T05:00'
}

onMounted(() => {
    if(localStorage.getItem('centroCostoId')){
        form.centro_costo_id = localStorage.getItem('centroCostoId')
    }
    const startDate = new Date();
});



// <!--</editor-fold>-->


// <!--<editor-fold desc="Calcular">-->



let TextFestivo = ''
let Madrugada, Tarde //variables internas para calculo de horas nocturnas

const Reporte11_59 = () => {
    //si es 11:59 minutos -> agrega una hora
    let fin = Date.parse(form.fecha_fin);
    let finDate = new Date(fin);
    const horafin = finDate.getHours()
    const minfin = finDate.getMinutes()
    if(horafin == 23 && minfin == 59){
        // finDate.setMinutes(finDate.getMinutes() + 1)
        // console.log("🧈 debu finDate:", finDate);
        form.horas_trabajadas++
        form.nocturnas++
    }
}


watch(() => form.centro_costo_id, (newX) => {
    localStorage.setItem('centroCostoId',newX)
})
watch(() => form.fecha_ini, (newX) => {
    form.horas_trabajadas = 8
})
const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];

const create = () => {
    form.fecha_fin
    Reporte11_59();
    if(form.horas_trabajadas <= LimiteHorasTrabajadas && form.horas_trabajadas !== 0){
        if (Object.keys(form.errors).length === 0) {
            form.almuerzo = ValorRealalmuerzo

            form.post(route('MassiveReportes'), {
                preserveScroll: true,
                onSuccess: () => {
                    // emit("close")
                    // form.reset()
                },
                onError: () =>{
                    alert(JSON.stringify(form.errors, null, 4));
                    // null
                },
                onFinish: () =>{

                }
            })

        }else{
            alert('Verifique de nuevo')

        }
    }else{
        alert('Horas invalidas') //toask
    }
}
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'4xl'">
            <form class="p-6 mb-20">
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
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini" />
<!--                                :start-time="data.startTime" :is-24="false" :day-names="daynames"-->
<!--                                :enable-time-picker="true"-->
                        <VueDatePicker range

                            id="fecha_ini" type="date" class="mt-1 block w-full" v-model="form.fecha_ini"
                            :placeholder="lang().placeholder.fecha_ini" :error="form.errors.fecha_ini" />
                        <InputError class="mt-2" :message="form.errors.fecha_ini" />
                    </div>
<!--                    <div>-->
<!--                        <InputLabel for="fecha_fin" :value="lang().label.fecha_fin" />-->
<!--                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"-->
<!--                            id="fecha_fin" type="date" class="mt-1 block w-full" v-model="form.fecha_fin" required-->
<!--                            :placeholder="lang().placeholder.fecha_fin" :error="form.errors.fecha_fin" />-->
<!--                        <InputError class="mt-2" :message="form.errors.fecha_fin" />-->
<!--                    </div>-->
                    <div>
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas" />
                        <TextInput id="horas_trabajadas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 block w-full" v-model="form.horas_trabajadas" disabled
                            :placeholder="lang().placeholder.horas_trabajadas" :error="form.errors.horas_trabajadas" />
                        <InputError class="bg-gray-100 dark:bg-gray-700 mt-2" :message="form.errors.horas_trabajadas" />
                    </div>

                    <div>
                        <InputLabel for="almuerzo" :value="lang().label.horacomida + ' (+9 horas)'" />
                        <TextInput id="almuerzo" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.almuerzo" disabled
                            :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo" />
                    </div>
                    <!-- mt-80 -->
                    <div class="mt-4 grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="diurnas" :value="lang().label.diurnas" />
                            <TextInput id="diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.diurnas" disabled
                                :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="nocturnas" :value="lang().label.nocturnas" />
                            <TextInput id="nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.nocturnas" disabled
                                :placeholder="lang().placeholder.nocturnas" :error="form.errors.nocturnas" />
                        </div>
                    </div>
                    <!-- mt-80 -->
                    <div v-if="form.extra_diurnas || form.extra_nocturnas || form.dominicales == 'si'" class="mt-4 grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="extra_diurnas" :value="lang().label.extra_diurnas" />
                            <TextInput id="extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_diurnas" disabled
                                :placeholder="lang().placeholder.extra_diurnas" :error="form.errors.extra_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="extra_nocturnas" :value="lang().label.extra_nocturnas" />
                            <TextInput id="extra_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_nocturnas" disabled
                                :placeholder="lang().placeholder.extra_nocturnas" :error="form.errors.extra_nocturnas" />
                        </div>
                    </div>
                    <!-- dominicales -->
                    <div v-if="form.dominicales == 'si'" class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="dominical_diurnas" :value="lang().label.dominical_diurnas" />
                            <TextInput id="dominical_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_diurnas" disabled
                                :placeholder="lang().placeholder.dominical_diurnas" :error="form.errors.dominical_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="dominical_nocturnas" :value="lang().label.dominical_nocturnas" />
                            <TextInput id="dominical_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_nocturnas" disabled
                                :placeholder="lang().placeholder.dominical_nocturnas" :error="form.errors.dominical_nocturnas" />
                        </div>
                    </div>
                    <div v-if="form.dominicales == 'si'" class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="dominical_extra_diurnas" :value="lang().label.dominical_extra_diurnas" />
                            <TextInput id="dominical_extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_extra_diurnas" disabled
                                :placeholder="lang().placeholder.dominical_extra_diurnas" :error="form.errors.dominical_extra_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="dominical_extra_nocturnas" :value="lang().label.dominical_extra_nocturnas" />
                            <TextInput id="dominical_extra_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_extra_nocturnas" disabled
                                :placeholder="lang().placeholder.dominical_extra_nocturnas" :error="form.errors.dominical_extra_nocturnas" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="centro_costo_id" :value="lang().label.centro_costo_id" />
                        <SelectInput v-model="form.centro_costo_id" :dataSet="props.valoresSelect" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.centro_costo_id" />
                    </div>

                    <div v-if="form.dominicales == 'si'" class="mt-4">
                        <label class="dark:text-white">Horario</label>
                        <TextInput id="dominicales" type="text" class="bg-gray-100 dark:bg-gray-700 block w-full" v-model="TextFestivo" disabled />
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

                <div class="flex justify-end">

                    <p v-if="props.ultimoReporte > 0" class="m-2">Hay pendientes {{ props.ultimoReporte }} horas</p>

                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create" @keyup.enter="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
