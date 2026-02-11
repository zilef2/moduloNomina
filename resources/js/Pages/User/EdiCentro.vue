<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, router } from '@inertiajs/vue3';
import { watchEffect, reactive, computed } from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
    user: Object,
    centros: Object,
    centrosForUser: Array,
})

const emit = defineEmits(["close"]);

const data = reactive({
    numberCentro: 0,
    mensajeError: '',
    isLoading: false,
})

const form = useForm({
    centroids: [],
});

watchEffect(() => {
    if (props.show && props.user?.id) {
        router.get(route('user.index'), { forUserId: props.user.id }, {
            preserveState: true,
            preserveScroll: true,
            only: ['centrosForUser'],
            onStart: () => data.isLoading = true,
            onFinish: () => data.isLoading = false,
        });
        
        data.numberCentro = props.user?.centros?.length ?? 0
    }
});

watchEffect(() => {
    if (props.show && props.centrosForUser) {
        form.centroids = [...props.centrosForUser];
        data.numberCentro = props.centrosForUser.length;
    }else{
        form.centroids = [];
        data.numberCentro = 0;
    }
});


const update = () => {
    form.centroids = form.centroids.slice(0, data.numberCentro).map(c => c ? parseInt(c) : null).filter(c => c !== null);

    data.mensajeError = '';
    form.put(route('user.update', props.user?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
            data.numberCentro = 0;
        },
        onError: () => {
            alert('Hay campos incompletos o erroneos');
        },
    });
}

const centrosOptions = computed(() => props.centros?.map(centro => ({ label: centro.nombre, value: centro.id })) ?? []);

const getCentroLabel = (id) => {
    return centrosOptions.value.find(centro => centro.value == id)?.label ?? id;
}

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl7'">
           <!-- <pre>
            {{form.centroids[0]}}
           </pre> -->
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Centros de costos de {{ props.user?.name }}
                </h2>
                <div v-if="data.isLoading" class="flex justify-center items-center py-10">
                    <svg class="animate-spin h-20 w-20 text-sky-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div v-else>
                    <p>Existen {{ centros.length }} centros de costos</p>
                <p v-if="data.mensajeError !== ''" class="text-red-600 text-lg my-4">{{ data.mensajeError }}</p>


                <div class="my-6 space-y-4">
                    <div class="grid grid-cols-4 gap-2 2xl:gap-8">
                        <div>
                            <InputLabel for="numberCentro" :value="'Número de centros'"/>
                            <TextInput autocomplete="off" id="numberCentro" type="number" class="mt-1 block w-full"
                                       v-model="data.numberCentro" required
                                       placeholder="Número de centros" min="0" :max="centros.length"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-4 4xl:grid-cols-5 6xl:grid-cols-6 gap-2 2xl:gap-8">
                        <div v-for="index in parseInt(data.numberCentro)" :key="index">
                            <InputLabel :for="'centro_' + index" :value="'Centro ' + index + ' (' + getCentroLabel(form.centroids[index-1]) + ')'"/>
                            <SelectInput :id="'centro_' + index" class="mt-1 block w-full" v-model="form.centroids[index-1]"
                                         :dataSet="centrosOptions">
                            </SelectInput>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
                </div>
            </form>
        </Modal>
    </section>
</template>
