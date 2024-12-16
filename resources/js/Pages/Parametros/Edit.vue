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

    parametros: Object,
})
const emit = defineEmits(["close"]);
const form = useForm({
    s_Dias_gabela: props.parametros?.s_Dias_gabela,
    subsidio_de_transporte_dia: props.parametros?.subsidio_de_transporte_dia,
    salario_minimo: props.parametros?.salario_minimo,
    HORAS_NECESARIAS_SEMANA: props.parametros?.HORAS_NECESARIAS_SEMANA,
    HORAS_ORDINARIAS: props.parametros?.HORAS_ORDINARIAS,
});

const update = () => {
    form.put(route('Parametros.update', props.parametros?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => {
            alert(JSON.stringify(form.errors, null, 4));
            // null
        },
        onFinish: () => null,
    })
}


watchEffect(() => {

    if (props.show) {
        form.errors = {}
        // form.fecha_fin = props.Paramatro?.fecha_fin
        // form.fecha_fin = TransformTdate(props.Paramatro?.fecha_fin)
        // if(form.fecha_ini == '') form.fecha_ini = TransformTdate(props.Paramatro?.fecha_ini)
        // if(form.fecha_fin == '') form.fecha_fin = TransformTdate(props.Paramatro?.fecha_fin)
        // form.centro_costo_id = props.Paramatro?.centro_costo_id
        // form.observaciones = props.Paramatro?.observaciones
        // form.horas_trabajadas = props.Paramatro?.horas_trabajadas
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="s_Dias_gabela" :value="lang().label.s_Dias_gabela" />
                        <TextInput id="s_Dias_gabela" type="number" class="mt-1 block w-full"
                            v-model="form.s_Dias_gabela" required
                            :placeholder="lang().placeholder.s_Dias_gabela"
                            :error="form.errors.s_Dias_gabela" />
                        <InputError class="mt-2" :message="form.errors.s_Dias_gabela" />
                    </div>
                    <div>
                        <InputLabel for="HORAS_ORDINARIAS" :value="lang().label.HORAS_ORDINARIAS" />
                        <TextInput id="HORAS_ORDINARIAS" type="number" class="mt-1 block w-full"
                            v-model="form.HORAS_ORDINARIAS" required
                            :placeholder="lang().placeholder.HORAS_ORDINARIAS"
                            :error="form.errors.HORAS_ORDINARIAS" />
                        <InputError class="mt-2" :message="form.errors.HORAS_ORDINARIAS" />
                    </div>
                    <div>
                        <InputLabel for="HORAS_NECESARIAS_SEMANA" :value="lang().label.HORAS_NECESARIAS_SEMANA" />
                        <TextInput id="HORAS_NECESARIAS_SEMANA" type="number" class="mt-1 block w-full"
                            v-model="form.HORAS_NECESARIAS_SEMANA" required
                            :placeholder="lang().placeholder.HORAS_NECESARIAS_SEMANA"
                            :error="form.errors.HORAS_NECESARIAS_SEMANA" />
                        <InputError class="mt-2" :message="form.errors.HORAS_NECESARIAS_SEMANA" />
                    </div>

                    <div>
                        <InputLabel for="subsidio_de_transporte_dia" :value="lang().label.subsidio_de_transporte_dia" />
                        <TextInput id="subsidio_de_transporte_dia" type="number" class="mt-1 block w-full"
                            v-model="form.subsidio_de_transporte_dia" required
                            :placeholder="lang().placeholder.subsidio_de_transporte_dia"
                            :error="form.errors.subsidio_de_transporte_dia" />
                        <InputError class="mt-2" :message="form.errors.subsidio_de_transporte_dia" />
                    </div>
                    <div>
                        <InputLabel for="salario_minimo" :value="lang().label.salario_minimo" />
                        <TextInput id="salario_minimo" type="number" class="mt-1 block w-full" v-model="form.salario_minimo"
                            required :placeholder="lang().placeholder.salario_minimo" :error="form.errors.salario_minimo" />
                        <InputError class="mt-2" :message="form.errors.salario_minimo" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? '...' : lang().button.corect }}
                    </PrimaryButton>
                </div>
            </form>

        </Modal>
</section></template>
