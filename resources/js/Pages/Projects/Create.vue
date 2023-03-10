<script setup>
import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import Modal from '@/Components/Modal.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import DatetimeInput from '@/Components/DatetimeInput.vue';
    import { useForm } from '@inertiajs/vue3';
    // import Checkbox from '@/Components/Checkbox.vue';
    import { reactive, watchEffect } from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
})

const emit = defineEmits(["close"]);

const data = reactive({
    multipleSelect: false,
})
const today = new Date();
function fechaInInput(dateit,addDays=0,addMonths=0){
    let mesConCero = addMonths == 0 ? (dateit.getMonth()+1) : (dateit.getMonth()+1+addMonths);
    let diaConCero = addDays == 0 ? (dateit.getDay()) : (dateit.getDay()+addDays);
    if(mesConCero < 10) mesConCero = '0'+mesConCero;
    if(diaConCero < 10) diaConCero = '0'+diaConCero;
    return (dateit.getFullYear())+"-"+(mesConCero)+'-'+(diaConCero);
}

const form = useForm({
    nombre: 'nombre proyecto',
    cliente: 'alejo',
    valor_tentativo: '1000000',
    valor_acordado: '800000',
    valor_primer_pago: '200000',
    fecha_primera_reunion: fechaInInput(today),
    fecha_primer_pago: fechaInInput(today,0,1),
    fecha_entrega: fechaInInput(today,0,2),
    observaciones: 'la fecha de entrega puede cambiar dependiendo de los improvistos en los dias de desarrollo',
})

const create = () => {
    form.post(route('projects.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () => alert(errors.create),
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
    if(form.valor_acordado != 0){
        form.valor_primer_pago = ""+(form.valor_acordado/4);
    }
})


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
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
                    </div>
                    <div>
                        <InputLabel for="cliente" :value="lang().label.cliente" />
                        <TextInput id="cliente" type="text" class="mt-1 block w-full" v-model="form.cliente" required
                            :placeholder="lang().placeholder.cliente" :error="form.errors.cliente" />
                        <InputError class="mt-2" :message="form.errors.cliente" />
                    </div>
                    <div>
                        <InputLabel for="valor_tentativo" :value="lang().label.valor_tentativo" />
                        <TextInput id="valor_tentativo" type="number" class="mt-1 block w-full" v-model="form.valor_tentativo" required
                            :placeholder="lang().placeholder.valor_tentativo" :error="form.errors.valor_tentativo" />
                        <InputError class="mt-2" :message="form.errors.valor_tentativo" />
                    </div>
                    <div>
                        <InputLabel for="valor_acordado" :value="lang().label.valor_acordado" />
                        <TextInput id="valor_acordado" type="number" class="mt-1 block w-full" v-model="form.valor_acordado" required
                            :placeholder="lang().placeholder.valor_acordado" :error="form.errors.valor_acordado" />
                        <InputError class="mt-2" :message="form.errors.valor_acordado" />
                    </div>


                    <div>
                        <InputLabel for="valor_primer_pago" :value="lang().label.valor_primer_pago" />
                        <TextInput id="valor_primer_pago" type="number" class="mt-1 block w-full" v-model="form.valor_primer_pago" required
                            :placeholder="lang().placeholder.valor_primer_pago" :error="form.errors.valor_primer_pago" />
                        <InputError class="mt-2" :message="form.errors.valor_primer_pago" />
                    </div>
                    <div>
                        <InputLabel for="fecha_primera_reunion" :value="lang().label.fecha_primera_reunion" />
                        <DatetimeInput id="fecha_primera_reunion" type="date" class="mt-1 block w-full" v-model="form.fecha_primera_reunion" required
                            :placeholder="lang().placeholder.fecha_primera_reunion" :error="form.errors.fecha_primera_reunion" />
                        <InputError class="mt-2" :message="form.errors.fecha_primera_reunion" />
                    </div>
                    <div>
                        <InputLabel for="fecha_primer_pago" :value="lang().label.fecha_primer_pago" />
                        <DatetimeInput id="fecha_primer_pago" type="date" class="mt-1 block w-full" v-model="form.fecha_primer_pago" required
                            :placeholder="lang().placeholder.fecha_primer_pago" :error="form.errors.fecha_primer_pago" />
                        <InputError class="mt-2" :message="form.errors.fecha_primer_pago" />
                    </div>
                    <div>
                        <InputLabel for="fecha_entrega" :value="lang().label.fecha_entrega" />
                        <DatetimeInput id="fecha_entrega" type="date" class="mt-1 block w-full" v-model="form.fecha_entrega" required
                            :placeholder="lang().placeholder.fecha_entrega" :error="form.errors.fecha_entrega" />
                        <InputError class="mt-2" :message="form.errors.fecha_entrega" />
                    </div>
                    
                </div>
                <div class="my-6 ">
                    <InputLabel for="observaciones" :value="lang().label.observaciones" />
                        <textarea
                            id="observaciones" type="text" v-model="form.observaciones" required
                            class="mt-1 block w-full rounded-md shadow-sm dark:bg-black dark:text-white placeholder:text-gray-400 placeholder:dark:text-gray-400/50"
                            cols="30" rows="3" :error="form.errors.observaciones">
                        </textarea>
                    <InputError class="mt-2" :message="form.errors.observaciones" />
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
