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
    nombre: '',
})

const create = () => {
    form.post(route('CentroCostos.store'), {
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
})


</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-1 gap-6">
                    <div>
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                            :placeholder="lang().placeholder.nombre" :error="form.errors.nombre" />
                        <InputError class="mt-2" :message="form.errors.nombre" />
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
