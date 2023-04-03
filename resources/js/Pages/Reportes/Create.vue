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

    
    import { reactive, watchEffect } from 'vue';

    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    show: Boolean,
    title: String,

    valoresSelect:Object,
    IntegerDefectoSelect: Number,

})

const emit = defineEmits(["close"]);

function getHoursColombianRandom(otraVez) {

    let hora_inicial = '09'
    // let vec_inicial = ['00','06','09']
    // let vec_final = ['01','04','10','11']
    let vec_final = ['15','21','23','24']
    let num = Math.floor(Math.random() * (vec_final.length-1));
    
    if(otraVez) return vec_final[num]

    // let hora_inicial = vec_inicial[num]
    let hora_final = vec_final[num]

    // while(hora_final < hora_inicial)
    //     hora_final = getHoursColombianRandom(true);

    return [hora_inicial,hora_final];
}

const horas = getHoursColombianRandom()

const form = useForm({
    // fecha_ini: '',
    // fecha_fin: '',

    fecha_ini: '2023-04-03T'+horas[0]+':00',
    fecha_fin: '2023-04-03T'+horas[1]+':00',


    horas_trabajadas: '',
    centro_costo_id: props.IntegerDefectoSelect,
    observaciones: '',
    almuerzo: '0',

    diurnas: '0',
    nocturnas: '0',
    dominicales: '0',
    extra_diurnas: '0',
    extra_nocturnas: '0',
    extra_dominicales: '0',
});
//TODO: extras por semana
//TODO: dominicales y extra dom


const create = () => {
    if(form.horas_trabajadas <= 24){
        form.post(route('Reportes.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () =>{
                // alert(JSON.stringify(form.errors, null, 4));
                null
            },
            onFinish: () => emit("close"),
        })
    }else{
        alert('Demasiadas horas') //toask
    }
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
    if( Date.parse(form.fecha_ini) && Date.parse(form.fecha_fin) ){
        let ini = Date.parse(form.fecha_ini);
        let fin = Date.parse(form.fecha_fin);
        form.horas_trabajadas = "0"
        form.diurnas = "0"
        form.nocturnas = "0"
        form.extra_nocturnas = "0"
        form.almuerzo = "0"
        form.horas_trabajadas = ''+parseInt((fin - ini)/(3600*1000));

        if( Date.parse(form.fecha_ini) > Date.parse(form.fecha_fin)){
            form.errors.horas_trabajadas = 'La fecha inicial es mayor a la final'
        }else{
            if(form.horas_trabajadas >= 24){
                form.errors.horas_trabajadas = 'Las horas trabajadas son demasiadas'
                form.horas_trabajadas = "0"
                form.diurnas = "0"
                form.nocturnas = "0"
                form.almuerzo = "0"
            }else{// la ini < fin y las horas trabajadas son < 24
                if(form.horas_trabajadas < 8){
                    form.almuerzo = 'No';
                    
                    form.diurnas = Math.abs(calcularDiurnas(form.fecha_ini,form.fecha_fin));
                    form.nocturnas = Math.abs(calcularNocturnas(form.fecha_ini,form.fecha_fin));
                    
                }else{ //extras
                    form.almuerzo = 'Si';
                   
                    if(form.horas_trabajadas > 8){

                        let horasExtrasDiurnas = (calcularDiurnas(form.fecha_ini,form.fecha_fin,'extra'));
                        form.extra_diurnas = horasExtrasDiurnas[0];
                        form.diurnas = horasExtrasDiurnas[1];

                        const CuandoEmpiezaExtra = parseInt(new Date(form.fecha_ini).getHours())+8;
                        
                        let horasExtrasNocturnas = (calcularNocturnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra));
                        form.extra_nocturnas = horasExtrasNocturnas[0];
                        form.nocturnas = horasExtrasNocturnas[1];
                    }
                    form.horas_trabajadas -= 1;
                    if(form.diurnas > 0){
                        form.diurnas -= 1
                    }else{
                        if(form.nocturnas > 0)
                        form.nocturnas -= 1
                    }
                }
            }
        }
    }
})

function calcularDiurnas(Inicio, Fin,extra){
    const horasInicio = new Date(Inicio).getHours();
    const horasFin = new Date(Fin).getHours();

    const DiaInicio = new Date(Inicio).getDate();
    const DiaFin = new Date(Fin).getDate();

    const BaseInicial = horasInicio >= 6 ? horasInicio : 6 
    if(DiaInicio == DiaFin){
        const BaseFinal = horasFin >= 21 ? 21 : horasFin
        let HorasDiurnas = BaseFinal - BaseInicial;
        if(extra === 'extra' && HorasDiurnas > 8){
            return [HorasDiurnas-8,8]
        }
        return HorasDiurnas;


    }else{
        const BaseFinal = horasFin <= 6 ? 21 : horasFin // se asume que 24 es lo maximo que se puede trabajar
        let HorasDiurnas = BaseFinal - BaseInicial;

        if(extra === 'extra' && HorasDiurnas > 8){
            return [HorasDiurnas-8,8]
        }
        return HorasDiurnas;
    }
}

function calcularNocturnas(Inicio, Fin,CuandoEmpiezaExtra){
    const horasInicio = new Date(Inicio).getHours();
    const horasFin = new Date(Fin).getHours();

    const DiaInicio = new Date(Inicio).getDate();
    const DiaFin = new Date(Fin).getDate();

    let Madrugada = 0
    let Tarde = 0
    if(DiaInicio == DiaFin){
        if(horasInicio < 6 && horasFin <= 6){//solo de noche
            Madrugada = horasFin - horasInicio;
        }else{
            if(horasInicio < 6){
                Madrugada = (6 - horasInicio);
            }
        }

        if(horasInicio >= 21 && horasFin >= 21){//solo de noche
            Tarde = horasFin - horasInicio;
        }else{
            if(horasFin > 21){//si existan horas nocturnas, si no son 0
                Tarde = (horasFin - 21);
            }
        }
    }else{ //dias diferentes
        if(horasInicio < 6 && horasFin <= 6){//solo de noche
            Madrugada = horasFin - horasInicio;
            // if(Madrugada < 0) //mucho voleo
        }else{
            if(horasInicio < 6){
                Madrugada = (6 - horasInicio);
            }
            if(horasFin <= 6){
                Madrugada += horasFin;
            }
        }

        if(horasInicio >= 21 && horasFin >= 21){//solo de noche
            Tarde = horasFin - horasInicio;
            //if(Tarde < 0) !mucho voleo
        }else{
            if(horasFin > 21){//si existan horas nocturnas, si no son 0
                Tarde += (horasFin - 21);
            }

            if(horasInicio > 21){
                Tarde += (24 - horasInicio);
            }else{
                Tarde += (24 - 21);
            }
        }
    }

    console.log("🚀🧈 debu calcularNocturnas debu Madrugada:", Madrugada); console.log("🚀🧈 debu calcularNocturnas debu tarde:", Tarde);

    let HorasNoc = Madrugada + Tarde;
    if(CuandoEmpiezaExtra <= 21){ //tiene diurnas extra
        return [HorasNoc,0]
    }else{ //no tiene diurnas extra
        const ordinarias = CuandoEmpiezaExtra - 21
        return [HorasNoc-ordinarias,ordinarias];
    }
}

const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini" />
                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"
                            id="fecha_ini" type="date" class="mt-1 block w-full" v-model="form.fecha_ini" required
                            :placeholder="lang().placeholder.fecha_ini" :error="form.errors.fecha_ini" />
                        <InputError class="mt-2" :message="form.errors.fecha_ini" />
                    </div>
                    <div>
                        <InputLabel for="fecha_fin" :value="lang().label.fecha_fin" />
                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"
                            id="fecha_fin" type="date" class="mt-1 block w-full" v-model="form.fecha_fin" required
                            :placeholder="lang().placeholder.fecha_fin" :error="form.errors.fecha_fin" />
                        <InputError class="mt-2" :message="form.errors.fecha_fin" />
                    </div>

                    <div>
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas" />
                        <TextInput id="horas_trabajadas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 block w-full" v-model="form.horas_trabajadas" disabled
                            :placeholder="lang().placeholder.horas_trabajadas" :error="form.errors.horas_trabajadas" />
                        <InputError class="bg-gray-100 dark:bg-gray-700 mt-2" :message="form.errors.horas_trabajadas" />
                    </div>

                    <div>
                        <InputLabel for="almuerzo" :value="lang().label.almuerzo" />
                        <TextInput id="almuerzo" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.almuerzo" disabled
                            :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo" />
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="diurnas" :value="lang().label.diurnas" />
                            <TextInput id="diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.diurnas" disabled
                                :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas" />
                        </div>
                        <div>
                            <InputLabel for="nocturnas" :value="lang().label.nocturnas" />
                            <TextInput id="nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.nocturnas" disabled
                                :placeholder="lang().placeholder.nocturnas" :error="form.errors.nocturnas" />
                        </div>
                    </div>
                    <div v-if="form.extra_diurnas || form.extra_nocturnas" class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="extra_diurnas" :value="lang().label.extra_diurnas" />
                            <TextInput id="extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_diurnas" disabled
                                :placeholder="lang().placeholder.extra_diurnas" :error="form.errors.extra_diurnas" />
                        </div>
                        <div>
                            <InputLabel for="extra_nocturnas" :value="lang().label.extra_nocturnas" />
                            <TextInput id="extra_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_nocturnas" disabled
                                :placeholder="lang().placeholder.extra_nocturnas" :error="form.errors.extra_nocturnas" />
                        </div>
                    </div>


                    <div>
                        <InputLabel for="centro_costo_id" :value="lang().label.centro_costo_id" />
                        <SelectInput v-model="form.centro_costo_id" :dataSet="props.valoresSelect" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.centro_costo_id" />
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
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
