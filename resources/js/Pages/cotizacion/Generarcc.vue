<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    title: String,
    cotizaciona: Object,
})

const emit = defineEmits(["close"]);

const form = useForm({});

const update2 = () => {
    form.put(route('cotizacion.update2', props.cotizaciona?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl2'">
            <form class="p-6" @submit.prevent="destory">
                <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100">
                    Generar centro de costo <b>{{ props.cotizaciona?.numero_cot }}</b> 
                </h2>
                <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">Por favor, vincular los supervisores</p>
                <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">¡Importante!</p>
                <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">
                  No podrá cambiar, el número de la cotización si realiza esta accion. 
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                    <DangerButton v-if="!cotizaciona.centro_costo_id" class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update2">
                        {{ form.processing ? 'Generar Centro de costo' + '...' : 'Generar Centro de costo' }}
                    </DangerButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
