<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {computed,watch,ref,nextTick, onMounted, reactive, watchEffect} from 'vue';
import "vue-select/dist/vue-select.css";
import {formatPesosCol} from '@/global.ts';


const props = defineProps({
    show: Boolean,
    title: String,
    viaticoa: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    printForm: [],
    AutoActualizarse: false,
    valorNumerico: 0,
    valorFormateado: '',
    valorConsig: '0',
})


const justNames = [
    'valor_legalizacion',
    'descripcion_legalizacion',
]

const form = useForm({...Object.fromEntries(justNames.map(field => [field, '']))});
onMounted(() => {
    if (props.numberPermissions > 9) {
        form.valor_legalizacion = Math.floor(Math.random() * 9 + 10000)
        // form.nombre = 'admin orden trabajo ' + (valueRAn);
        // form.codigo = (valueRAn);
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha = '2023-06-01'
    }
});

props.titulos.forEach(names => {
    data.printForm.push({
        idd: names['order'], label: names['label'], type: names['type']
        , value: form[names['order']]
    })
});

//hijo de watch(() => form.valor_legalizacion
const validarValor = () => {
    let estamal = form.valor_legalizacion > props.viaticoa.gasto
    if (estamal) data.mensajeError_saldo = 'Valor a consignar es superior al saldo'
    else data.mensajeError_saldo = ''
    return estamal
}
watch(() => form.valor_legalizacion, (newVal) => {
    validarValor()
});

watchEffect(() => {
    if (props.show) {
        data.valorConsig = formatPesosCol(form.valor_legalizacion)
        form.errors = {}

        form.valor_legalizacion = form.valor_legalizacion < 0 ? form.valor_legalizacion * -1 : form.valor_legalizacion 
        if (data.AutoActualizarse) {
            form.valor_legalizacion = props.viaticoa?.valor_legalizacion
            form.valor_legalizacion = props.viaticoa?.valor_legalizacion
            form.descripcion_legalizacion = props.viaticoa?.descripcion_legalizacion
            data.valorConsig = formatPesosCol(form.valor_legalizacion)
            
            data.AutoActualizarse = false
        }
    } else {
        data.AutoActualizarse = true
    }
})
watch(() => props.show, (newVal) => {
        if (newVal) {
            // Espera a que el componente se renderice para enfocar
            nextTick(() => {
                valorConsigInput.value.focus();
            });
        }
    }
);

const update = () => {
    form.put(route('legalizarviatico', props.viaticoa?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => {
            alert('Hay campos incompletos o erroneos')
        },
        onFinish: () => {
            data.AutoActualizarse = true
        },
    })
}

const valorConsigInput = ref(null);
</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl4'" :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Legalizar {{ props.title }}
                </h2>
                <p class="texto-legal">
                    Por favor, digite el valor que se ha legalizado con documentos u otras pruebas
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
                    <div class="">
                        <InputLabel for="valor_legalizacionid" :value="lang().label['valor_legalizacion']"/>
                        <TextInput id="valor_legalizacionid" v-model="form.valor_legalizacion"
                                   :error="form.errors.valor_legalizacion"
                                   placeholder="valor_legalizacion" type="number"
                                   class="my-2 block w-full"
                                   required
                                   ref="valorConsigInput"
                        />
                        <InputError :message="form.errors.valor_legalizacion" class="mt-2"/>
                        <label class="my-2 py-4 text-black dark:text-white text-lg"> {{ data.valorConsig }} Pesos
                            colombianos</label>
                    </div>
                    <div class="">
                        <InputLabel for="descripcion_legalizacionid" :value="lang().label['descripcion_legalizacion'] + ' (opcional)'"/>
                        <TextInput id="descripcion_legalizacionid" v-model="form.descripcion_legalizacion"
                                   :error="form.errors.descripcion_legalizacion"
                                   placeholder="descripcion_legalizacion" type="text"
                                   class="my-2 block w-full"
                                   required
                        />
                        <InputError :message="form.errors.descripcion_legalizacion" class="mt-2"/>
                    </div>

                </div>
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-3"
                                   @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
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
