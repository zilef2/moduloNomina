<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {watch, ref, nextTick, onMounted, reactive, watchEffect} from 'vue';
import "vue-select/dist/vue-select.css";
import {formatPesosCol, DateTime_to_html} from '@/global.ts';


const props = defineProps({
    show: Boolean,
    title: String,
    solicitud_viaticoa: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    AutoActualizarse: false,
    valorNumerico: 0,
    valorFormateado: '',
    valorConsig: '0',
    consignaciones: '0',
})

const form = useForm({
    consignacion_id: [],
    valor_legalizacion: [],
    descripcion_legalizacion: [],
    fecha_legalizacion: [],
});


// <!--<editor-fold desc="onmounted, watchers y mas">-->
onMounted(() => {
    // setTimeout(() => {
    //     if (props.numberPermissions > 9) {
    //         setFecha(0, '2025-04-01T10:39')
    //     }
    // }, 1500)
});

//hijo de watch(() => form.valor_legalizacion
const validarValor = () => {
    let estamal = form.valor_legalizacion > props.solicitud_viaticoa.gasto
    if (estamal) data.mensajeError_saldo = 'Valor a consignar es superior al saldo'
    else data.mensajeError_saldo = ''
    return estamal
}

// watch(() => form.valor_legalizacion, (newVal) => {});
watch(() => props.show, () => {
        if (props.show) {
            ensureArraySize();
        } else {
            data.AutoActualizarse = true //avisa que hay que se muestra otro viatico, por tanto, hay que actualizar el form
        }
    },
    {immediate: true, deep: true}
);

watchEffect(() => {
    if (props.show) {

        form.errors = {}

        form.valor_legalizacion = form.valor_legalizacion < 0 ? form.valor_legalizacion * -1 : form.valor_legalizacion
        if (data.AutoActualizarse) {
            data.AutoActualizarse = false
        }
    } else {
        data.AutoActualizarse = true
    }
})

watch(() => props.show, (newVal) => {
        if (newVal) {
            form.valor_legalizacion = props.cotizaciona?.valor_legalizacion ?? 0
        }
    }
);


const formattedValor = {
    get: (index) => {
        return form.valor_legalizacion && form.valor_legalizacion[index]
            ? formatPesosCol(form.valor_legalizacion[index])
            : "";
    },
    set: (index, value) => {
        // Asegurar que valor_legalizacion es un array
        if (!Array.isArray(form.valor_legalizacion)) {
            form.valor_legalizacion = [];
        }

        // Permitir solo números y actualizar el form sin espacios ni símbolos
        form.valor_legalizacion[index] = value.replace(/\D/g, "");
    }
};
const valoconsignacion = (arrayValor) => {
    if (arrayValor && arrayValor['valor']) {

        return '(Consignados: ' + formatPesosCol(arrayValor['valor']) + ')'
    }
    return ''
}
// <!--</editor-fold>-->

// <!--<editor-fold desc="getters and setter">-->
const getDescripcion = (index) => {
    if (!Array.isArray(form.descripcion_legalizacion)) {
        form.descripcion_legalizacion = [];
    }

    // Asegúrate de que el array tenga suficientes elementos
    while (form.descripcion_legalizacion.length <= index) {
        form.descripcion_legalizacion.push('');
    }

    return form.descripcion_legalizacion[index];
};
const setDescripcion = (index, value) => {
    if (!Array.isArray(form.descripcion_legalizacion)) {
        form.descripcion_legalizacion = [];
    }

    // Asegúrate de que el array tenga suficientes elementos
    while (form.descripcion_legalizacion.length <= index) {
        form.descripcion_legalizacion.push('');
    }

    form.descripcion_legalizacion[index] = value;
};

const getFecha = (index) => {
    if (!Array.isArray(form.fecha_legalizacion)) {
        form.fecha_legalizacion = [];
    }

    // Asegúrate de que el array tenga suficientes elementos
    while (form.fecha_legalizacion.length <= index) {
        form.fecha_legalizacion.push('');
    }

    return form.fecha_legalizacion[index];
};
const setFecha = (index, value) => {
    if (!Array.isArray(form.fecha_legalizacion)) {
        form.fecha_legalizacion = [];
    }

    // Asegúrate de que el array tenga suficientes elementos
    while (form.fecha_legalizacion.length <= index) {
        form.fecha_legalizacion.push('');
    }

    form.fecha_legalizacion[index] = value;
    console.log("=>(Legalizar.vue:169) value", value);
};
// <!--</editor-fold>-->


const ensureArraySize = () => {
    const cantidadCotizaciones = props.solicitud_viaticoa?.Consignaciona?.length || 10;
    // if (!Array.isArray(form.descripcion_legalizacion)) {
    form.consignacion_id = Array(cantidadCotizaciones).fill(0);
    form.valor_legalizacion = Array(cantidadCotizaciones).fill('');
    form.descripcion_legalizacion = Array(cantidadCotizaciones).fill('');
    form.fecha_legalizacion = Array(cantidadCotizaciones).fill('');

    // }


    // Crear un nuevo array del tamaño correcto
    form.descripcion_legalizacion = Array(cantidadCotizaciones)
        .fill('')
        .map((val, i) => {
            return props.solicitud_viaticoa.Consignaciona[i].descripcion_legalizacion || ''
        })

    data.consignaciones = Object.values(props.solicitud_viaticoa?.Consignaciona); //transforma en array

    data.consignaciones.map((val, inde) => {
        console.log("=>(Legalizar.vue:181) inde", inde);
        console.table(val);
        form.consignacion_id[inde] = val.consignacion_id
        if (val.valor_legalizacion) {
            form.valor_legalizacion[inde] = val.valor_legalizacion
            setFecha(inde, DateTime_to_html(val.fecha_legalizacion))
            form.descripcion_legalizacion[inde] = val.descripcion_legalizacion
        } else {
            //     form.valor_legalizacion[inde] = ''
            //     form.fecha_legalizacion[inde] = ''
            //     form.descripcion_legalizacion[inde] = ''
        }

    });
};

const validarForm = () => {
    let isRight = true
    for (let consignacionesKey in data.consignaciones) {
        if (form.valor_legalizacion[consignacionesKey]) {
            isRight = parseInt(data.consignaciones[consignacionesKey].valor) >= parseInt(form.valor_legalizacion[consignacionesKey])
        } else {
            continue
        }
        if (!isRight) return isRight
    }
    return isRight
}

const update = () => {
    if (validarForm())
        form.put(route('legalizarviatico', props.solicitud_viaticoa?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => {
                alert('Hay campos incompletos o erroneos')
            },
            onFinish: () => {
                data.AutoActualizarse = true //avisa que hay que se muestra otro viatico, por tanto, hay que actualizar el form
            },
        })
}

// const valorConsigInput = ref(null);
</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl8'" :show="props.show" @close="emit('close')">
            <form class="px-4 py-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Legalizar {{ props.title }}</h2>
                <p class="texto-legal">Por favor, digite el valor que se ha legalizado con documentos u otras
                    pruebas</p>
                <div v-for="(valor, index) in data.consignaciones" :key="index"
                     class="grid xs:grid-cols-1 lg:grid-cols-6 xs:gap-2 lg:gap-2 2xl:gap-16 my-6">

                    <div class="lg:col-span-2 mt-2">
                        <InputLabel for="valor_legalizacionid"
                                    :value="lang().label['valor_legalizacion'] + valoconsignacion(valor)"
                                    class=""
                        />

                        <TextInput v-if="valor.valor_legalizacion"
                                   :id="`valor_legalizacion_${index}`"
                                   v-model="valor.valor_legalizacion"
                                   placeholder="Valor legalización" type="text"
                                   class="my-2 block w-full bg-gray-300" disabled
                        />
                        <TextInput v-else
                                   :id="`valor_legalizacion_${index}`"
                                   :modelValue="formattedValor.get(index)"
                                   @update:modelValue="(value) => formattedValor.set(index, value)"
                                   :error="form.errors[`valor_legalizacion.${index}`]"
                                   placeholder="Valor legalización" type="text"
                                   class="my-2 block w-full"
                        />

                        <InputError :message="form.errors.valor_legalizacion" class="mt-2"/>
                    </div>
                    <div class="lg:col-span-2 mt-2">
                        <InputLabel for="descripcion_legalizacionid"
                                    :value="lang().label['descripcion_legalizacion'] + ' (opcional)'"/>
                        <!--                        <TextInput -->
                        <!--                                :id="`descripcion_legalizacionid_${index}`"-->
                        <!--                            v-model="form.descripcion_legalizacion['index']"-->
                        <!--                                   placeholder="descripcion_legalizacion" type="text" required-->
                        <!--                                   class="my-2 block w-full"-->

                        <input :disabled="!!valor.valor_legalizacion"
                               type="text"
                               :id="`descripcion_legalizacionid_${index}`"
                               :value="getDescripcion(index)"
                               @input="(event) => setDescripcion(index, event.target.value)"
                               class="my-2 w-full rounded-md shadow-sm placeholder:text-gray-400 placeholder:dark:text-gray-400/50 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary"
                        />
                    </div>
                    <div class="lg:col-span-2 mt-2">
                        <InputLabel for="fecha_legalizacion"
                                    :value="lang().label['fecha_legalizacion']"/>

                        <input :disabled="!!valor.valor_legalizacion"
                               type="datetime-local"
                               :id="`fecha_legalizacionid_${index}`"
                               :value="getFecha(index)"
                               @input="(event) => setFecha(index, event.target.value)"
                               class="my-2 w-full rounded-md shadow-sm placeholder:text-gray-400 placeholder:dark:text-gray-400/50 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary"
                        />
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
