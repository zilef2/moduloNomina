<script setup>
import Modal from '@/Components/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import SelectInput from '@/Components/SelectInput.vue';

    import { useForm } from '@inertiajs/vue3';

    import { ref,watchEffect, reactive, watch } from 'vue';

    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'
import FestivosColombia from 'festivos-colombia';
import { TransformTdate } from '@/global.ts';


let CurrentlyYear = new Date().getFullYear()
let holidays2022 = FestivosColombia.getHolidaysByYear(CurrentlyYear);
let label_diurnas = ref(null)
//constantes intuitivas
const MAXIMO_HORAS_SEMANALES = 48
const LimiteHorasTrabajadas = 22
const TrabajoConAlmuerzo = 9
//fin intuitivas
let TextFestivo = ''
let Madrugada, Tarde //variables internas para calculo de horas nocturnas
let ValorRealalmuerzo = 0

const props = defineProps({
    show: Boolean,
    title: String,
    Reporte: Object,
    valoresSelect: Object,
    showUsers: Object,
    correccionUsuario: Boolean,
})
const emit = defineEmits([ "close", ]);

const data = reactive({

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
})

const form = useForm({
    fecha_ini: '',
    fecha_fin: '',
    horas_trabajadas: '',
    centro_costo_id: '',
    observaciones: '',
    almuerzo: '',
    valido: '',

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

const update = () => {
    form.put(route('Reporte_Super_Edit', props.Reporte?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            // form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        if (form.fecha_ini === '') form.fecha_ini = TransformTdate(props.Reporte?.fecha_ini)
        if (form.fecha_fin === '') form.fecha_fin = TransformTdate(props.Reporte?.fecha_fin)
        form.diurnas = props.Reporte?.diurnas
        form.nocturnas = props.Reporte?.nocturnas
        form.extra_diurnas = props.Reporte?.extra_diurnas
        form.extra_nocturnas = props.Reporte?.extra_nocturnas

        form.dominical_diurnas = props.Reporte?.dominical_diurno
        form.dominical_nocturnas = props.Reporte?.dominical_nocturno
        form.dominical_extra_diurnas = props.Reporte?.dominical_extra_diurno
        form.dominical_extra_nocturnas = props.Reporte?.dominical_extra_nocturno

        form.centro_costo_id = props.Reporte?.centro_costo_id
        form.fecha_ini = props.Reporte?.fecha_ini
        form.fecha_fin = props.Reporte?.fecha_fin
        form.observaciones = props.Reporte?.observaciones
        form.horas_trabajadas = props.Reporte?.horas_trabajadas
        form.almuerzo = props.Reporte?.almuerzo
    }
})
const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'2xl'">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <b>{{ props.showUsers[props.Reporte.user_id] }}</b>
                </h2>
                <div class="my-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini" />
                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"
                            id="fecha_ini" type="date" class="mt-1 block w-full" v-model="form.fecha_ini"
                            :placeholder="lang().placeholder.fecha_ini" :error="form.errors.fecha_ini" />
                        <InputError class="mt-2" :message="form.errors.fecha_ini" />
                    </div>
                    <div>
                        <InputLabel for="fecha_fin" :value="lang().label.fecha_fin" />
                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"
                            id="fecha_fin" type="date" class="mt-1 block w-full" v-model="form.fecha_fin"
                            :placeholder="lang().placeholder.fecha_fin" :error="form.errors.fecha_fin" />
                        <InputError class="mt-2" :message="form.errors.fecha_fin" />
                    </div>
                    <div>
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas" />
                        <TextInput id="horas_trabajadas" type="number" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700" v-model="form.horas_trabajadas"
                            :placeholder="lang().placeholder.horas_trabajadas" :error="form.errors.horas_trabajadas" />
                        <InputError class="mt-2" :message="form.errors.horas_trabajadas" />
                    </div>
                    <div>
                        <InputLabel for="almuerzo" :value="lang().label.horacomida + ' (+9 horas)'" />
                        <TextInput id="almuerzo" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.almuerzo"
                            :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo" />
                    </div>
                    <!-- mt-80 -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="diurnas" :value="lang().label.diurnas" />
                            <TextInput id="diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.diurnas"
                                :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="nocturnas" :value="lang().label.nocturnas" />
                            <TextInput id="nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.nocturnas"
                                :placeholder="lang().placeholder.nocturnas" :error="form.errors.nocturnas" />
                        </div>
                    </div>
                    <!-- mt-80 -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="extra_diurnas" :value="lang().label.extra_diurnas" />
                            <TextInput id="extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_diurnas"
                                :placeholder="lang().placeholder.extra_diurnas" :error="form.errors.extra_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="extra_nocturnas" :value="lang().label.extra_nocturnas" />
                            <TextInput id="extra_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_nocturnas"
                                :placeholder="lang().placeholder.extra_nocturnas" :error="form.errors.extra_nocturnas" />
                        </div>
                    </div>
                    <!-- dominicales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="dominical_diurnas" :value="lang().label.dominical_diurnas" />
                            <TextInput id="dominical_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_diurnas"
                                :placeholder="lang().placeholder.dominical_diurnas" :error="form.errors.dominical_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="dominical_nocturnas" :value="lang().label.dominical_nocturnas" />
                            <TextInput id="dominical_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_nocturnas"
                                :placeholder="lang().placeholder.dominical_nocturnas" :error="form.errors.dominical_nocturnas" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="dominical_extra_diurnas" :value="lang().label.dominical_extra_diurnas" />
                            <TextInput id="dominical_extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_extra_diurnas"
                                :placeholder="lang().placeholder.dominical_extra_diurnas" :error="form.errors.dominical_extra_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="dominical_extra_nocturnas" :value="lang().label.dominical_extra_nocturnas" />
                            <TextInput id="dominical_extra_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_extra_nocturnas"
                                :placeholder="lang().placeholder.dominical_extra_nocturnas" :error="form.errors.dominical_extra_nocturnas" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="centro_costo_id" :value="lang().label.centro_costo_id" />
                        <SelectInput v-model="form.centro_costo_id" :dataSet="props.valoresSelect" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.centro_costo_id" />
                    </div>

                    <div v-if="form.dominicales == 'si'" class="">
                        <label class="dark:text-white">Horario</label>
                        <TextInput id="dominicales" type="text" class="bg-gray-100 dark:bg-gray-700 block w-full" v-model="TextFestivo" />
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
