<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';
import {useForm} from '@inertiajs/vue3';
// import Checkbox from '@/Components/Checkbox.vue';
import {onMounted, reactive, ref, watchEffect} from 'vue';
import CamposTexto from "@/Pages/CentroCostos/CamposTexto.vue";
import SelectInput from "@/Components/SelectInput.vue";

const props = defineProps({
    show: Boolean,
    title: String,
    listaSupervisores: Object,
})

const emit = defineEmits(["close"]);

const data = reactive({
    multipleSelect: false,
})
const today = new Date();

function fechaInInput(dateit, addDays = 0, addMonths = 0) {
    let mesConCero = addMonths === 0 ? (dateit.getMonth() + 1) : (dateit.getMonth() + 1 + addMonths);
    let diaConCero = addDays === 0 ? (dateit.getDay()) : (dateit.getDay() + addDays);
    if (mesConCero < 10) mesConCero = '0' + mesConCero;
    if (diaConCero < 10) diaConCero = '0' + diaConCero;
    return (dateit.getFullYear()) + "-" + (mesConCero) + '-' + (diaConCero);
}

const form = useForm({
    nombre: '',
    descripcion: '',
    clasificacion: '',
    activo: true,
    selectedUsers: [],
    // ValidoParaFacturar: 1,
});

onMounted(() => {
    form.activo = ref(true);
})
// Definir una función para alternar el estado del checkbox
const toggleCheckbox = () => {
    form.value = !form.value;
};

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})
const SelecSupervisores = props.listaSupervisores?.map(gen => ({label: gen.name, value: gen.id}))

const create = () => {
    form.post(route('CentroCostos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),

        onFinish: () => null,
    })
}
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
                        <InputLabel ref="nombre" for="nombre" :value="lang().label.name"/>
                        <TextInput id="nombre" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.nombre"
                                   :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas"/>
                    </div>
                    <div>
                        <InputLabel ref="descripcion" for="descripcion" :value="lang().label.descripcion"/>
                        <TextInput id="descripcion" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.descripcion"
                                   :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas"/>
                    </div>
                    <div>
                        <InputLabel ref="clasificacion" for="clasificacion" :value="lang().label.clasificacion"/>
                        <TextInput id="clasificacion" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.clasificacion"
                                   :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas"/>
                    </div>
                    <div class="inline-flex mt-6">
                        <input
                            @click="toggleCheckbox"
                            v-model="form.activo"
                            type="checkbox" id="activo"
                            class="bg-gray-50 dark:bg-gray-600 mt-1 w-8 h-8 p-2 my-auto"
                        />
                        <InputLabel ref="activo" for="activo" :value="lang().label.activo" class="mx-3 my-auto"/>
                    </div>
                    <div>
                        <InputLabel for="users" :value="lang().label.supervisores"/>
                        <div v-for="(user, index) in SelecSupervisores" :key="index" class="mt-1 block w-full">
                            <label :for="'user' + index">
                                <input
                                    type="checkbox"
                                    :id="'user' + index"
                                    :value="user.value"
                                    v-model="form.selectedUsers"
                                />
                                {{ user.label }}
                            </label>
                        </div>
                        <InputError class="mt-2" :message="form.errors.users"/>
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
