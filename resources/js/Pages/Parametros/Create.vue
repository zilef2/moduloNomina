<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
})

const emit = defineEmits(["close"]);

const form = useForm({
    anio: new Date().getFullYear(),
    s_Dias_gabela: 0,
    subsidio_de_transporte_dia: 0,
    salario_minimo: 0,
    HORAS_NECESARIAS_SEMANA: 0,
    HORAS_ORDINARIAS: 0,
    HORAS_DEL_MES_30_DIAS: 240,
    MAXIMO_HORAS_SEMANALES: 48,
    minimo_material: 0,
    porcentaje_diurno: 1,
    porcentaje_nocturno: 1.35,
    porcentaje_extra_diurno: 1.25,
    porcentaje_extra_nocturno: 1.75,
    porcentaje_dominical_diurno: 1.75,
    porcentaje_dominical_nocturno: 2.1,
    porcentaje_dominical_extra_diurno: 2,
    porcentaje_dominical_extra_nocturno: 2.5,
})

const create = () => {
    form.post(route('Parametros.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => {
            alert(JSON.stringify(form.errors, null, 4));
        },
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl4'">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="anio" value="Año" />
                        <TextInput id="anio" type="number" class="mt-1 block w-full" v-model="form.anio" required />
                        <InputError class="mt-2" :message="form.errors.anio" />
                    </div>
                    <div>
                        <InputLabel for="s_Dias_gabela" :value="lang().label.s_Dias_gabela" />
                        <TextInput id="s_Dias_gabela" type="number" class="mt-1 block w-full"
                            v-model="form.s_Dias_gabela" required :placeholder="lang().placeholder.s_Dias_gabela"
                            :error="form.errors.s_Dias_gabela" />
                        <InputError class="mt-2" :message="form.errors.s_Dias_gabela" />
                    </div>
                    <div>
                        <InputLabel for="HORAS_ORDINARIAS" :value="lang().label.HORAS_ORDINARIAS" />
                        <TextInput id="HORAS_ORDINARIAS" type="number" class="mt-1 block w-full"
                            v-model="form.HORAS_ORDINARIAS" required :placeholder="lang().placeholder.HORAS_ORDINARIAS"
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
                        <InputLabel for="MAXIMO_HORAS_SEMANALES" value="Máximo Horas Semanales" />
                        <TextInput id="MAXIMO_HORAS_SEMANALES" type="number" class="mt-1 block w-full"
                            v-model="form.MAXIMO_HORAS_SEMANALES" required />
                        <InputError class="mt-2" :message="form.errors.MAXIMO_HORAS_SEMANALES" />
                    </div>
                    <div>
                        <InputLabel for="HORAS_DEL_MES_30_DIAS" value="Horas del Mes (30 días)" />
                        <TextInput id="HORAS_DEL_MES_30_DIAS" type="number" class="mt-1 block w-full"
                            v-model="form.HORAS_DEL_MES_30_DIAS" required />
                        <InputError class="mt-2" :message="form.errors.HORAS_DEL_MES_30_DIAS" />
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
                        <TextInput id="salario_minimo" type="number" class="mt-1 block w-full"
                            v-model="form.salario_minimo" required :placeholder="lang().placeholder.salario_minimo"
                            :error="form.errors.salario_minimo" />
                        <InputError class="mt-2" :message="form.errors.salario_minimo" />
                    </div>
                    <div>
                        <InputLabel for="minimo_material" value="Mínimo Material" />
                        <TextInput id="minimo_material" type="number" class="mt-1 block w-full"
                            v-model="form.minimo_material" required />
                        <InputError class="mt-2" :message="form.errors.minimo_material" />
                    </div>
                </div>

                <div class="my-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Porcentajes de Recargos y Extras
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <InputLabel for="porcentaje_diurno" value="% Diurno" />
                            <TextInput id="porcentaje_diurno" type="number" step="0.01" class="mt-1 block w-full"
                                v-model="form.porcentaje_diurno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_diurno" />
                        </div>
                        <div>
                            <InputLabel for="porcentaje_nocturno" value="% Nocturno" />
                            <TextInput id="porcentaje_nocturno" type="number" step="0.01" class="mt-1 block w-full"
                                v-model="form.porcentaje_nocturno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_nocturno" />
                        </div>
                        <div>
                            <InputLabel for="porcentaje_extra_diurno" value="% Extra Diurno" />
                            <TextInput id="porcentaje_extra_diurno" type="number" step="0.01" class="mt-1 block w-full"
                                v-model="form.porcentaje_extra_diurno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_extra_diurno" />
                        </div>
                        <div>
                            <InputLabel for="porcentaje_extra_nocturno" value="% Extra Nocturno" />
                            <TextInput id="porcentaje_extra_nocturno" type="number" step="0.01"
                                class="mt-1 block w-full" v-model="form.porcentaje_extra_nocturno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_extra_nocturno" />
                        </div>
                        <div>
                            <InputLabel for="porcentaje_dominical_diurno" value="% Dom. Diurno" />
                            <TextInput id="porcentaje_dominical_diurno" type="number" step="0.01"
                                class="mt-1 block w-full" v-model="form.porcentaje_dominical_diurno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_dominical_diurno" />
                        </div>
                        <div>
                            <InputLabel for="porcentaje_dominical_nocturno" value="% Dom. Nocturno" />
                            <TextInput id="porcentaje_dominical_nocturno" type="number" step="0.01"
                                class="mt-1 block w-full" v-model="form.porcentaje_dominical_nocturno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_dominical_nocturno" />
                        </div>
                        <div>
                            <InputLabel for="porcentaje_dominical_extra_diurno" value="% Dom. Extra Diurno" />
                            <TextInput id="porcentaje_dominical_extra_diurno" type="number" step="0.01"
                                class="mt-1 block w-full" v-model="form.porcentaje_dominical_extra_diurno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_dominical_extra_diurno" />
                        </div>
                        <div>
                            <InputLabel for="porcentaje_dominical_extra_nocturno" value="% Dom. Extra Nocturno" />
                            <TextInput id="porcentaje_dominical_extra_nocturno" type="number" step="0.01"
                                class="mt-1 block w-full" v-model="form.porcentaje_dominical_extra_nocturno" required />
                            <InputError class="mt-2" :message="form.errors.porcentaje_dominical_extra_nocturno" />
                        </div>
                    </div>
                </div>

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
