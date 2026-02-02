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

const form = useForm({
    fecha_ini: '2023-03-22T13:00',
    fecha_fin: '2023-03-23T06:00',


    horas_trabajadas: '',
    centro_costo_id: props.IntegerDefectoSelect,
    observaciones: '',
    diurnas: '',
    nocturnas: '',
    almuerzo: '0',
})

const create = () => {
        alert('Demasiadas horas')
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
    
})


const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];

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
                    <div class="grid grid-cols-3 gap-1">
                        <div>
                            <InputLabel for="diurnas" :value="lang().label.diurnas" />
                            <TextInput id="diurnas" type="number" class="mt-1 w-full" v-model="form.diurnas" disabled
                                :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas" />
                        </div>
                        <div>
                            <InputLabel for="nocturnas" :value="lang().label.nocturnas" />
                            <TextInput id="nocturnas" type="number" class="mt-1 w-full" v-model="form.nocturnas" disabled
                                :placeholder="lang().placeholder.nocturnas" :error="form.errors.nocturnas" />
                        </div>
                        <div>
                            <InputLabel for="almuerzo" :value="lang().label.almuerzo" />
                            <TextInput id="almuerzo" type="number" class="mt-1 w-full" v-model="form.almuerzo" disabled
                                :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo" />
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
