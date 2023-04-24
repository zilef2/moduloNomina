<script setup>
import Modal from '@/Components/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import SelectInput from '@/Components/SelectInput.vue';

import { useForm } from '@inertiajs/vue3';

import { watchEffect, reactive, watch } from 'vue';

import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    show: Boolean,
    title: String,
    Reporte: Object,
    valoresSelect: Object,
    showUsers: Object,
    correccionUsuario: Boolean,
})
const emit = defineEmits([ "close", ]);
const form = useForm({
    fecha_ini: '',
    fecha_fin: '',
    horas_trabajadas: '',
    centro_costo_id: '',
    observaciones: '',
    valido: '',
});

const update = () => {
    if(props.correccionUsuario){
        form.valido = 0;
    }else{
        form.valido = 2;
    }
    //! todo 
    if(form.horas_trabajadas <= 48){
        form.put(route('Reportes.update', props.Reporte?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    }else{
        alert('Demasiadas horas')
    }
}


function TransformTdate (dateString){
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const day = ('0' + date.getDate()).slice(-2);
    const hours = ('0' + date.getHours()).slice(-2);
    const minutes = ('0' + date.getMinutes()).slice(-2);
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        // form.fecha_fin = props.Reporte?.fecha_fin
        // form.fecha_fin = TransformTdate(props.Reporte?.fecha_fin)
        if(form.fecha_ini == '') form.fecha_ini = TransformTdate(props.Reporte?.fecha_ini)
        if(form.fecha_fin == '') form.fecha_fin = TransformTdate(props.Reporte?.fecha_fin)
        form.centro_costo_id = props.Reporte?.centro_costo_id
        form.observaciones = props.Reporte?.observaciones
        form.horas_trabajadas = props.Reporte?.horas_trabajadas
    }
    
        if( Date.parse(form.fecha_ini) > Date.parse(form.fecha_fin) ){
            form.horas = '0';
            // form.horas_trabajadas = form.fecha_fin.substr(1,3);
        }else{
            form.horas_trabajadas = (parseInt((Date.parse(form.fecha_fin) - Date.parse(form.fecha_ini))/(3600*1000) ));
        }
})
</script>

<template>
    
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }} <b>{{ props.showUsers[props.Reporte.user_id] }}</b> asda sdasdas
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
                <div class="my-6 ">
                    <InputLabel for="observaciones" :value="lang().label.observaciones + ' (Porque se esta rechazando)'" />
                        <textarea :disabled="(correccionUsuario)"
                            id="observaciones" type="text" v-model="form.observaciones"
                            class="mt-1 block w-full rounded-md shadow-sm dark:bg-black dark:text-white placeholder:text-gray-400 placeholder:dark:text-gray-400/50"
                            cols="30" rows="3" :error="form.errors.observaciones">
                        </textarea>
                    <InputError class="mt-2" :message="form.errors.observaciones" />
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton v-if="correccionUsuario" class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? '...' : lang().button.corect }}
                    </PrimaryButton>
                    <PrimaryButton v-else class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.reject + '...' : lang().button.reject }}
                    </PrimaryButton>
                </div>
            </form>

        </Modal>
    </section>
</template>
