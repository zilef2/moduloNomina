<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';


const props = defineProps({
    show: Boolean,
    title: String,
    parametros: Object,
})

const emit = defineEmits(["close"]);

const form = useForm({});

const destroy = () => {
    form.delete(route('Parametros.destroy', props.parametros?.id), {
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

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'lg'">
            <form class="p-6" @submit.prevent="destroy">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.delete }} {{ props.title }} ({{ props.parametros?.anio }}) #{{ props.parametros?.id
                    }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ lang().label.delete_confirm }}
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="destroy">
                        {{ form.processing ? lang().button.delete + '...' : lang().button.delete }}
                    </DangerButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
