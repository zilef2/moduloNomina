<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, router } from '@inertiajs/vue3';
import { computed, watch, ref, nextTick, onMounted, reactive, watchEffect } from 'vue';
import "vue-select/dist/vue-select.css";
import { formatPesosCol, dd } from '@/global.ts';


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
        if (props.solicitud_viaticoa) {
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

const consignarTodo = (index, saldo) => {
    if (!Array.isArray(form.valor_consig)) {
        form.valor_consig = [];
    }
    form.valor_consig[index] = String(saldo);
    onValorConsignadoChange(index, String(saldo), saldo);
    calcularTotalConsignado();
};

const consignarGlobal = () => {
    props.solicitud_viaticoa?.Losviaticos.forEach((viaticu, index) => {
        if (viaticu.saldo > 0) {
            consignarTodo(index, viaticu.saldo);
        }
    });
};


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
}, { immediate: true, deep: true });


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
                window.location.reload()
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
                <div class="flex justify-between items-center mb-4">
                    <p class="xs:text-sm md:text-md">
                        Monto máximo a consignar:
                        <b class="text-blue-700">{{ formatPesosCol(props.solicitud_viaticoa.saldo_sol) }} </b>
                    </p>
                    <button type="button" @click="consignarGlobal"
                        class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg border border-indigo-100 dark:border-indigo-800 hover:bg-indigo-100 transition-colors">
                        Consignar todos los saldos
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="(viaticu, index) in props.solicitud_viaticoa?.Losviaticos" :key="index"
                        class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20">
                        <InputLabel :value="viaticu.userino" class="text-indigo-600 dark:text-indigo-400 font-bold" />
                        <p class="text-[10px] text-gray-500 uppercase font-medium mb-2">{{ viaticu.descripcion }}</p>

                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-gray-400 uppercase">Saldo disponible:</span>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-gray-700 dark:text-gray-300 italic">{{
                                    formatPesosCol(viaticu.saldo) }}</span>
                                <button v-if="viaticu.saldo > 0" type="button"
                                    @click="consignarTodo(index, viaticu.saldo)"
                                    class="text-[9px] font-black uppercase text-blue-600 hover:underline">
                                    Consignar todo
                                </button>
                            </div>
                        </div>

                        <TextInput :id="`valor_consig_${index}`"
                            :class="{ 'bg-gray-200 opacity-50': viaticu.saldo === 0 }" :disabled="viaticu.saldo === 0"
                            :modelValue="formattedValor.get(index)" @update:modelValue="(value) => {
                                formattedValor.set(index, value);
                                onValorConsignadoChange(index, value, viaticu.saldo);
                            }" placeholder="0" type="text" class="my-1 block w-full text-right font-black" required />
                        <InputError :message="data.mensaje_saldo_individual[index]" class="mt-2" />
                        <InputError :message="form.errors.valor_consig" class="mt-2" />
                        <InputError :message="data.mensajeError_saldo" class="mt-2" />
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
