<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watchEffect} from 'vue';
import '@vuepic/vue-datepicker/dist/main.css'
import "vue-select/dist/vue-select.css";

// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    numberPermissions: Number,
    CentrosRepetidos: Array,
    consecutivoCotizacion: Number,
    cotizacionInicial2: Number,

})
const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
    existe:false,
})

let justNames = props.titulos.map(names => {
    if (names['order'] !== 'centro_costo_id' &&
        names['order'] !== 'fecha_aprobacion_cot')
        return names['order']
})
justNames = justNames.filter(item => item !== undefined);
const form = useForm({...Object.fromEntries(justNames.map(field => [field, '']))});

onMounted(() => {
    let fecha1 = new Date()
    let aniofecha1 = fecha1.getFullYear()
    form.numero_cot = ('PE '+aniofecha1+'-'+props.consecutivoCotizacion);
    if (props.numberPermissions > 9) {
        const valueRAn = Math.floor(Math.random() * (900) + 1)
        form.descripcion_cot = "holi" + (valueRAn);
        form.precio_cot = (valueRAn) * 1000;
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha = '2023-06-01'

    }else{
        
    }
});

const printForm = [];
props.titulos.forEach(names => {
    if (names['order'] !== 'centro_costo_id' &&
        names['order'] !== 'fecha_aprobacion_cot')
        printForm.push({
            idd: names['order'], label: names['label'], type: names['type']
        })
});

function ValidarVacios() {
    let result = true
    printForm.forEach(element => {
        if (!form[element.idd]) {
            result = false
            return result
        }
    });
    return result
}

const create = () => {
    if (ValidarVacios()) {
        // console.log("🧈 debu pieza_id:", form.pieza_id);
        form.post(route('cotizacion.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    } else {
        console.log('Hay campos vacios')
    }
}

watchEffect(() => {
    if (props.show) {

        form.errors = {}
        
        data.existe = props.CentrosRepetidos.some(item =>{
            return (item === (''+form.numero_cot).trim())
        });
    }
})

//very usefull
const sexos = [{label: 'Masculino', value: 0}, {label: 'Femenino', value: 1}];
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl9'">
            <form class="px-6 pt-4 pb-48" @submit.prevent="create">
                <h2 class="mb-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.adda }} {{ props.title }}
                </h2>
                <p class="my-1 text-md text-gray-800 dark:text-gray-50">Contador inicial: 7334</p>
                <p class="mb-4 text-md text-gray-800 dark:text-gray-50">Numero de cotizaciones realizadas: {{ props.cotizacionInicial2 }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-12">
                    <div v-for="(atributosform, indice) in printForm" :key="indice"
                         :class="atributosform.type === 'id' ? 'col-span-2' : 'bg-blue-50/50'"
                         class="rounded-xl"

                    >
                        <div v-if="atributosform.type === 'foreign'" id="SelectVue" class="">
                            <label name="labelSelectVue"> {{ atributosform.label }} </label>
                            <v-select :options="props.losSelect[0]"
                                      v-model="form[atributosform.idd]"
                                      :reduce="element => element.value" label="name"
                            ></v-select>
                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>
                        </div>


                        <!-- tiempo -->
                        <div v-else-if="atributosform.type === 'time'" id="SelectVue" class="">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                                       v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                                       :error="form.errors[atributosform.idd]" step="3600"/>
                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>
                        </div>
                        <!-- number -->
                        <div v-else-if="atributosform.type === 'number'" id="SelectVue" class="">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput :id="atributosform.idd" type="text" class="mt-1 w-full"
                                       v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                                       :error="form.errors[atributosform.idd]"
                                       @focus="borrarNumber(atributosform.idd)"/>
                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>
                        </div>


                        <!-- normal -->
                        <div v-else class="">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                                       v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                                       :error="form.errors[atributosform.idd]"/>
                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>
                        </div>
                    </div>
                </div>
                <div class=" my-8 flex justify-end">
                    
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                    
                    <p v-if="data.existe" class="mx-auto text-red-700">Ya existe un centro de costo con ese numero</p>
                    <PrimaryButton v-else class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="create">
                        {{ lang().button.add }} {{ form.processing ? '...' : '' }}
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
