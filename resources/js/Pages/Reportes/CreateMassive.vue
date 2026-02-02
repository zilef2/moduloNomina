<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {useForm} from '@inertiajs/vue3';
import {ref, watchEffect, reactive, onMounted, watch} from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import FestivosColombia from 'festivos-colombia';
import {estaFechaEsFestivo} from '@/CreateReporte/HelpingCreate.ts';

const props = defineProps({
    show: Boolean,
    title: String,
    valoresSelect: Object,
    IntegerDefectoSelect: Number,
    horasemana: Number,
    startDateMostrar: String,
    endDateMostrar: String,
    numberPermissions: Number,
    ultimoReporte: Number,

})
// <!--<editor-fold desc="const - useForm - onMounted">-->

let holidays = FestivosColombia.getHolidaysByYear(new Date().getFullYear());
let label_diurnas = ref(null)

//constantes intuitivas
const LimiteHorasTrabajadas = 9
//fin intuitivas


const emit = defineEmits(["close"]);

const form = useForm({
    fecha_ini: [],

    centro_costo_id: props.IntegerDefectoSelect,
    observaciones: '',
    horas_trabajadas: 8,
    almuerzo: 0,

    diurnas: 0,
    nocturnas: 0,
    extra_diurnas: 0,
    extra_nocturnas: 0,

    dominicales: 'no',
    extra: 'no',
    esFestivo: false,
    horas_validas: true,
    user_id: 1
});


if (props.numberPermissions > 8) {
    // form.fecha_ini = '2023-11-01T00:00'
    // form.fecha_fin = '2023-11-01T05:00'
}

onMounted(() => {
    if (localStorage.getItem('centroCostoId')) {
        form.centro_costo_id = localStorage.getItem('centroCostoId')
    }
});
// <!--</editor-fold>-->


// <!--<editor-fold desc="Calcular">-->

watch(() => form.centro_costo_id, (newX) => {
    localStorage.setItem('centroCostoId', newX)
})
watch(() => form.fecha_ini, (newX) => {
    const fechaInicio = new Date(newX[0]);
    const fechaFin = new Date(newX[1]);

    // Clonamos para no modificar el original
    const fechaActual = new Date(fechaInicio);
    form.esFestivo = false
    while (fechaActual <= fechaFin && !form.esFestivo) {

        const fechaActualClonada = new Date(fechaActual);

        if (estaFechaEsFestivo(fechaActualClonada,FestivosColombia)) {
            console.log("Es festivo:", fechaActualClonada);
            alert('El siguiente dia es festivo: ' + fechaActualClonada.toLocaleDateString());
            form.esFestivo = true;
        }

        // Avanzamos al día siguiente
        fechaActual.setDate(fechaActual.getDate() + 1);
    }
    
    form.horas_validas = true
    let horaini, horafin
    newX.forEach((fecha, indice) => {
        console.log("🚀🚀 ~ fecha: ", fecha);
        if (!(fecha instanceof Date)){
          console.log("🚀🚀 ~ no es DAte!!!!!: ", fecha);
            
            return;
        } 

        // Si los minutos no son 0, los llevamos a 0
        if (fecha.getMinutes() !== 0) {
            const fechaCorregida = new Date(fecha);
            fechaCorregida.setMinutes(0);
            fechaCorregida.setSeconds(0);

            form.fecha_ini[indice] = fechaCorregida;
        }
        
    });
    horaini = newX[0].getHours();
    horafin = newX[1].getHours();
    form.horas_trabajadas = horafin - horaini;
    if(form.horas_trabajadas > 9){
        form.horas_validas = false
    }
    if(form.horas_trabajadas === 9 ){
        form.almuerzo = 1
        form.horas_trabajadas--
    }else{
        form.almuerzo = 0
    }
    
}, { deep: true })
const daynames = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];
const minTime = { hours: 6, minutes: 0 }   // 06:00 AM
const maxTime = { hours: 18, minutes: 0 }  // 06:00 PM
const deshabilitarFinesDeSemana = (date) => {
    const day = date.getDay()  // 0=domingo, 6=sábado
    return day === 0 || day === 6 || day === 5
}
//todo: falta desactivar las semanas futuras, nocturnas y viernes
const create = () => {
    if (form.horas_trabajadas <= LimiteHorasTrabajadas && form.horas_trabajadas !== 0) {
        if (Object.keys(form.errors).length === 0) {

            form.post(route('MassiveReportes'), {
                preserveScroll: true,
                onSuccess: () => {
                    emit("close")
                    form.reset()
                    setTimeout(() => {
                            location.reload();
                        }, 1400);
                },
                onError: () => {
                    alert(JSON.stringify(form.errors, null, 4));
                },
                onFinish: () => null
            })
        } else {
            alert('Verifique de nuevo')
        }
    } else {
        alert('Horas invalidas') //toask
    }
}

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl'">
            <form class="p-6 mb-40">
                <div class="flex space-x-4">
                    <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100">
                        <b>
                            Solo usar para horas ordinarias!!
                        </b>
                    </h2>
                    <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100">
                        <b>
                            Máximo 8 horas
                        </b>
                    </h2>
                </div>
                <div class="my-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini"/>
                        <VueDatePicker
                            :utc="false"
                            :disabled-dates="deshabilitarFinesDeSemana"
                            :is-24="false"
                            append-to-body
                            range
                            :day-names="daynames" :enable-time-picker="true"
                            :min-time="minTime"
                            :max-time="maxTime"
                            v-model="form.fecha_ini"
                            class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.fecha_ini"/>
                    </div>
                    <div class="mt-4">
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas"/>
                        <TextInput id="horas_trabajadas" type="number"
                                   class="bg-gray-100 dark:bg-gray-700 mt-1 block w-full"
                                   v-model="form.horas_trabajadas" disabled
                                   :placeholder="lang().placeholder.horas_trabajadas"
                                   :error="form.errors.horas_trabajadas"/>
                        <InputError class="bg-gray-100 dark:bg-gray-700 mt-2" :message="form.errors.horas_trabajadas"/>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="almuerzo" :value="lang().label.horacomida + ' (+9 horas)'"/>
                        <TextInput id="almuerzo" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.almuerzo" disabled
                                   :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo"/>
                    </div>

                    <div class="mt-4 col-span-2">
                        <InputLabel for="centro _costo_id" :value="lang().label.centro_costo_id"/>
                        <SelectInput v-model="form.centro_costo_id" :dataSet="props.valoresSelect"
                                     class="mt-1 block w-full"/>
                        <InputError class="mt-2" :message="form.errors.centro_costo_id"/>
                    </div>
<!--                    <div class="mt-4">-->
<!--                        <label class="dark:text-white">Userid</label>-->
<!--                        <TextInput id="user_id" type="number" class="bg-gray-100 dark:bg-gray-700 block w-full"-->
<!--                                   v-model="form.user_id" required/>-->
<!--                    </div>-->
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

                <div v-if="!form.esFestivo && form.horas_validas" class="flex justify-end">
                    <p v-if="props.ultimoReporte > 0" class="m-2">Hay pendientes {{ props.ultimoReporte }} horas</p>
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="create" @keyup.enter="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
                <div v-if="form.esFestivo" class="flex justify-end text-red-600">
                    <p class="m-2">¡Atención! El rango de fechas incluye días festivos.</p>
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                </div>
                <div v-if="!form.horas_validas" class="flex justify-end text-red-600">
                    <p class="m-2">Muchas Horas</p>
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                </div>
                
            </form>
        </Modal>
    </section>
</template>
