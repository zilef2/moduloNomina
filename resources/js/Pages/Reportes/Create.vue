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

const data = reactive({
    multipleSelect: false,
})

const form = useForm({
    // fecha_ini: '2018-06-07T01:00',
    fecha_ini: '',
    fecha_fin: '',
    horas_trabajadas: '',
    centro_costo_id: props.IntegerDefectoSelect,
    observaciones: '',
})

const create = () => {
    form.post(route('Reportes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () =>{
            // alert(JSON.stringify(form.errors, null, 4));
            null
        },
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
    if( Date.parse(form.fecha_ini) && Date.parse(form.fecha_fin) ){
        if( Date.parse(form.fecha_ini) > Date.parse(form.fecha_fin) ){
            form.horas = "0"
            // form.horas_trabajadas = form.fecha_fin.substr(1,3);
        }else{
            form.horas_trabajadas = ''+parseInt((Date.parse(form.fecha_fin) - Date.parse(form.fecha_ini))/(3600*1000) );
        }
    }
    //#TOASK: can be hours superior than 24?
})

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
                        <TextInput id="horas_trabajadas" type="number" class="mt-1 block w-full" v-model="form.horas_trabajadas" disabled
                            :placeholder="lang().placeholder.horas_trabajadas" :error="form.errors.horas_trabajadas" />
                        <InputError class="mt-2" :message="form.errors.horas_trabajadas" />
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
