<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watchEffect} from 'vue';
import "vue-select/dist/vue-select.css";

const props = defineProps({
    show: Boolean,
    title: String,
    cotizaciona: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    printForm: []
})

//very usefull
const justNames = [
    'factura',
    'fecha_factura',
]
const form = useForm({...Object.fromEntries(justNames.map(field => [field, '']))});

onMounted(() => {
    if (props.numberPermissions > 9) {
        const valueRAn = Math.floor(Math.random() * 911 + 1)
        form.factura = valueRAn
    }
});

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})

const update3 = () => {
    form.put(route('cotizacion.update3', props.cotizaciona?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}
// const sexos = [ { label: 'Masculino', value: 'Masculino' }, { label: 'Femenino', value: 'Femenino' } ];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl2'">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Facturar {{ props.title }} : {{cotizaciona.numero_cot}}
                </h2>
                <h3 class="text my-4 font-medium text-gray-900 dark:text-gray-100">
                    Recuerde que al facturar, el centro de costo quedará inactivo.
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-xl"><label name="factura"> {{ lang().label.factura }} </label>
                        <TextInput v-model="form.factura" :error="form.errors.factura" :placeholder="lang().label.factura" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="fecha_factura"> {{ lang().label.fecha_factura }} </label>
                        <TextInput v-model="form.fecha_factura" :error="form.errors.fecha_factura" :placeholder="lang().label.fecha_factura" class="mt-1 block w-full"
                                   required type="date"/>
                    </div>
                </div>
                
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing"
                                   @click="update3">
                        Facturar
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>

<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}

[name="labelSelectVue"],
.muted {
    color: #1b416699;
}

[name="labelSelectVue"] {
    /* font-size: 22px; */
    font-weight: 600;
}
</style>
