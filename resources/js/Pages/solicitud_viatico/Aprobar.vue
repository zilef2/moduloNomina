<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {computed, watch, ref, nextTick, onMounted, reactive, watchEffect} from 'vue';
import "vue-select/dist/vue-select.css";
import {formatPesosCol} from '@/global.ts';


const props = defineProps({
    show: Boolean,
    title: String,
    route: String,
    solicitud_viaticoa: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    printForm: [],
    AutoActualizarse: false, // es true cuando se cierra el modal
    valorNumerico: 0,
    valorFormateado: '',
    valorConsig: '0',
    mensajeError_saldo: '',
})


const justNames = [
    'valor_consig'
]

const form = useForm({
    ...Object.fromEntries(justNames.map(field => [field, ''])),
    routeadmin: ''
});
onMounted(() => {
    if (props.numberPermissions > 9) {
        // form.valor_consig = Math.floor(Math.random() * 9 + 1000000)
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


watchEffect(() => {
    if (props.show) {
        data.valorConsig = formatPesosCol(form.valor_consig)
        form.errors = {}


        if (data.AutoActualizarse) {

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
// <!--<editor-fold desc="selected items">-->
const selectedUser = computed({
    get: () => props.losSelect[0].find(user => user.id === form.user_id) || null,
    set: (user) => {
        form.user_id = user ? user.id : null;
    }
});
const selectedcc = computed({
    get: () => props.losSelect[1].find(centro => centro.id === form.centro_costo_id) || null,
    set: (centro) => {
        form.centro_costo_id = centro ? centro.id : null;
    }
});

const consigEstaMal = (newVal) => {
    let estamal = (newVal > props.solicitud_viaticoa.saldo_sol) || (newVal < 0) 

    if (estamal) data.mensajeError_saldo = 'Valor a consignar es superior al saldo o es negativo'
    else data.mensajeError_saldo = ''
    return estamal
}

watch(() => form.valor_consig, (newVal) => consigEstaMal(newVal));

// <!--</editor-fold>-->
const update = () => {
    if (props.route) form.routeadmin = 'index2'

    if (consigEstaMal(form.valor_consig)) {
        alert("El valor consignado supera lo solicitado o es negativo");
    }else{
        form.put(route('viaticoupdate2', props.solicitud_viaticoa?.id), {
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
}

const valorConsigInput = ref(null);
</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl4'" :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Aprobar {{ props.title }}
                </h2>
                <p class="xs:text-sm md:text-md ">
                    Al proceder con la acción de "Guardar" en el presente formulario, el/la usuario(a) bajo
                    que:<br><br>
                    1. Ha verificado la información registrada en esta solicitud de viático.<br>
                    2. Certifica que el monto económico especificado ha sido consignado en su totalidad.<br>
                    3. El monto consignado no deberia superar <b
                    class="text-blue-700">{{ formatPesosCol(props.solicitud_viaticoa.saldo_sol) }} </b>
                    <br><br><br>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="">
                        <InputLabel for="valor_consigid" :value="lang().label['valor_consig']"/>
                        <TextInput id="valor_consigid" v-model="form.valor_consig"
                                   :error="form.errors.valor_consig"
                                   placeholder="valor_consig" type="number"
                                   class="my-2 block w-full"
                                   required
                                   ref="valorConsigInput"
                        />
                        <InputError :message="form.errors.valor_consig" class="mt-2"/>
                        <InputError :message="data.mensajeError_saldo" class="mt-2"/>
                        <label class="my-2 py-4 text-black dark:text-white text-lg"> {{ data.valorConsig }} Pesos
                            colombianos</label>
                    </div>

                </div>
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{
                            lang().button.close
                        }}
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
