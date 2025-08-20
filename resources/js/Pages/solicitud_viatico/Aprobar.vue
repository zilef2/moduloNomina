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
import {formatPesosCol, dd} from '@/global.ts';


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
    AutoActualizarse: false, // es true cuando se cierra el modal
    valorNumerico: 0,
    valorFormateado: '',
    valorConsig: '0',

    consignaciones: '0',
    totalConsig: 0,

    // Mensajes de error
    mensajeError_saldo: '',
    mensaje_saldo_individual: [],
})

const form = useForm({
    valor_consig: [],
    routeadmin: '',
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

watchEffect(() => {
    if (props.show) {
        data.valorConsig = formatPesosCol(form.valor_consig)
        form.errors = {}

        if (data.AutoActualizarse) data.AutoActualizarse = false

        nextTick()
        if(props.solicitud_viaticoa){
            props.solicitud_viaticoa?.Losviaticos.forEach(() => {
                data.mensaje_saldo_individual.push('');
            })
        }
    } else {
        data.AutoActualizarse = true
    }
})

// watch(() => props.show, (newVal) => {
//         if (newVal) {
//             // Espera a que el componente se renderice para enfocar
//             nextTick(() => {
//                 valorConsigInput.value.focus();
//             });
//         }
//     }
// );


// <!--<editor-fold desc="selected items">-->
function calcularTotalConsignado() {
    data.totalConsig = 0;
    form.valor_consig.forEach(v => {
        const NumericValue = parseFloat(v)
        console.log("🚀 ~ calcularTotalConsignado ~ NumericValue: ", NumericValue);
        data.totalConsig += NumericValue || 0;
    });
}


// <!--</editor-fold>-->

const onValorConsignadoChange = (index, value, saldo) => {
    const valueInput = parseFloat(form.valor_consig[index] || 0);
    if (valueInput > saldo) data.mensaje_saldo_individual[index] = 'El valor consignado supera el saldo disponible'
    else data.mensaje_saldo_individual[index] = ''
}


// <!--<editor-fold desc="Validaciones y update">-->
const consigEstaMal = (newVal) => {
    calcularTotalConsignado()
    let estamal = (data.totalConsig > props.solicitud_viaticoa.saldo_sol) || (data.totalConsig < 0)

    if (estamal) data.mensajeError_saldo = 'Valor a consignar es superior al saldo'
    else data.mensajeError_saldo = ''
    return estamal
}
watch(() => form.valor_consig, (newVal) => {
    if (newVal) {
        consigEstaMal(newVal)
    }
}, {immediate: true, deep: true});


const update = () => {
    if (props.route) form.routeadmin = 'index2'

    if (consigEstaMal(form.valor_consig)) {
        alert("El valor consignado supera lo solicitado");
    } else {
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
// <!--</editor-fold>-->

const valorConsigInput = ref(null);
const formattedValor = {
    get: (index) => {
        return form.valor_consig && form.valor_consig[index]
            ? formatPesosCol(form.valor_consig[index])
            : "";
    },
    set: (index, value) => {
        // Asegurar que valor_consig es un array
        if (!Array.isArray(form.valor_consig)) {
            form.valor_consig = [];
        }

        // Permitir solo números y actualizar el form sin espacios ni símbolos
        form.valor_consig[index] = value.replace(/\D/g, "");
    }
};

</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl4'" :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Aprobar {{ props.title }}
                </h2>
                <p class="xs:text-sm md:text-md mb-8">
                    Al proceder con la acción de "Guardar", el monto consignado no deberia superar el siguiente monto:
                    <b
                        class="text-blue-700">{{ formatPesosCol(props.solicitud_viaticoa.saldo_sol) }} </b>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="(viaticu, index) in props.solicitud_viaticoa?.Losviaticos" :key="index">
                        <InputLabel for="valor_consigid"
                                    :value="viaticu.userino 
                                    + '. Descripción: ' + viaticu.descripcion "
                        />
                        <InputLabel for="valor_consigid2"
                                    :value="'Saldo: ' + formatPesosCol(viaticu.saldo)"/>
                        
                        <TextInput :id="`valor_consig_${index}`"
                                   :class="{'bg-gray-300' : viaticu.saldo === 0 }"
                                   :disabled="viaticu.saldo === 0"
                                   :modelValue="formattedValor.get(index)"
                                   @update:modelValue="(value) => {
                                        formattedValor.set(index, value);
                                        onValorConsignadoChange(index, value,viaticu.saldo); // tu función
                                    }"
                                   placeholder="valor a consignar" type="text"
                                   class="my-2 block w-full"
                                   required
                        />
                        
                        <InputError :message="data.mensaje_saldo_individual[index]" class="mt-2"/>
                        <InputError :message="form.errors.valor_consig" class="mt-2"/>
                        <InputError :message="data.mensajeError_saldo" class="mt-2"/>
                    </div>

                </div>
                <p class="mx-auto flex md:text-md lg:text-lg mt-8">
                    Total consignado:
                    <b class="text-blue-700 mx-2">
                        {{ formatPesosCol(data.totalConsig) }}
                    </b>
                </p>
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')">
                        {{ lang().button.close }}
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
