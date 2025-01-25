<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {watchEffect, reactive, watch, onMounted} from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
    user: Object,
    centros: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    numberCentro: 1,
    primeraVez: true,
    mensajeError: '',
})

const form = useForm({
    centroids: [],
});

onMounted(() => {
})

watch(() => data.numberCentro, (newX) => {
    if (data.numberCentro > props.centros.length) {
        data.numberCentro = 0
    }
    for (let i = 0; i < data.centroids; i++) {
        form.centroids[i] = 0
    }
})
watch(() => form.centroids, (newX) => {
})

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        if (data.primeraVez) {
            data.primeraVez = false
        }
        form.errors = {}
    } else {
        data.primeraVez = true
    }

})

const validarUpdate = () => {
    let valido = true;
    if (data.numberCentro > 0) {
        form.centroids.forEach(element => {
            valido = element !== null
        });
    }

    valido = (form.centroids.length - 1) === data.numberCentro
    return valido
}
const update = () => {
    form.centroids.map(centroid => parseInt(centroid))
    form.centroids.filter(centroid => centroid != null)
    let valido = validarUpdate()
    valido = true
    if (valido) {
        data.mensajeError = ''
        form.put(route('user.update', props.user?.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.centroids = new Set([1])
                emit("close")
                form.reset()
            },
            onError: () => {
                alert('Hay campos incompletos o erroneos')
            },
            onFinish: () => {
                data.primeraVez = true
            },
        })
    } else {
        data.mensajeError = 'Falta informacion'
    }

}

const centros = props.centros?.map(centro => ({label: centro.nombre, value: centro.id}))
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl4'">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Centros de costos de {{ props.user.name }}
                </h2>
                <p>Recuerde que hay como maximo {{ centros.length }} centros de costos</p>
                <p v-if="data.mensajeError !== ''" class="text-red-600 text-lg my-4">{{ data.mensajeError }}</p>
                <div class="my-6 space-y-4">

                    <div class="grid grid-cols-2 gap-6">

                        <div>
                            <InputLabel for="role" :value="'Número de centros'"/>
                            <TextInput autocomplete="off" id="cedula" type="number" class="mt-1 block w-full"
                                       v-model="data.numberCentro" required
                                       :placeholder="'Número de centros'" min="1" :max="centros.length"/>
                        </div>
                        <div v-for="index in parseInt(data.numberCentro)" :key="index">
                            <InputLabel for="centro" :value="lang().label.centro"/>
                            <SelectInput id="centro" class="mt-1 block w-full" v-model="form.centroids[index]"
                                         :dataSet="centros">
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
            </form>
        </Modal>
    </section>
</template>
