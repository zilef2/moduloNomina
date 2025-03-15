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
    viaticoa: Object,
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
    valor_legalizacion: [''],
    descripcion_legalizacion: [''],
    fecha_legalizacion: [''],
    consignacion_id: [0],
});


onMounted(() => {

    if (props.numberPermissions > 9) {
        form.valor_legalizacion = Math.floor(Math.random() * 9 + 10000)
        // form.nombre = 'admin orden trabajo ' + (valueRAn);
        // form.codigo = (valueRAn);
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha = '2023-06-01'
    }
});

//hijo de watch(() => form.valor_legalizacion
const validarValor = () => {
    let estamal = form.valor_legalizacion > props.viaticoa.gasto
    if (estamal) data.mensajeError_saldo = 'Valor a consignar es superior al saldo'
    else data.mensajeError_saldo = ''
    return estamal
}

watch(() => form.valor_legalizacion, (newVal) => {
    // validarValor()
});


const ensureArraySize = () => {
    if (!Array.isArray(form.descripcion_legalizacion)) {
        form.descripcion_legalizacion = [];
    }

    const cantidadCotizaciones = props.viaticoa?.Consignaciona?.length || 10;

    // Crear un nuevo array del tamaño correcto
    form.descripcion_legalizacion = Array(cantidadCotizaciones)
        .fill('')
        .map((val, i) => form.descripcion_legalizacion[i] || '');
    
    form.consignacion_id.map((val, i) => {
            form.consignacion_id[i] = props.viaticoa.Consignaciona[i].consignacion_id
    });
        console.log("=>(Legalizar.vue:77) form.consignacion_id", form.consignacion_id);
};
watch(() => [props.show, props.viaticoa?.Consignaciona], () => {
        if (props.show) {
            ensureArraySize();
        }
    },
    {immediate: true, deep: true}
);
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
            data.consignaciones = Object.entries(props.viaticoa?.Consignaciona); //transforma en array

        }
    } else {
        data.AutoActualizarse = true
    }
})

watch(() => props.show, (newVal) => {
        if (newVal) {
            // nextTick(() => {
            //     valorConsigInput.value.focus();
            // });
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
const valorArray = (valor) => {
    if (valor && valor[1]) {

        return '( ' + formatPesosCol(valor[1]['valor']) + ' )'
    }
    return ''
}
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
};

const update = () => {
    form.put(route('legalizarviatico', props.viaticoa?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            // form.reset() //todo: justtesting
        },
        onError: () => {
            alert('Hay campos incompletos o erroneos')
        },
        onFinish: () => {
            data.AutoActualizarse = true
        },
    })
}

// const valorConsigInput = ref(null);
</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl5'" :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Legalizar {{ props.title }}</h2>
                <p class="texto-legal">Por favor, digite el valor que se ha legalizado con documentos u otras
                    pruebas</p>
                <div v-for="(valor, index) in data.consignaciones" :key="index"
                     class="grid xs:grid-cols-1 md:grid-cols-3 xs:gap-2 md:gap-8 2xl:gap-12 my-6">
                    <div class="">
<!--                        {{ valor }}-->
                        <InputLabel for="valor_legalizacionid"
                                    :value="lang().label['valor_legalizacion'] + valorArray(valor)"
                        />

                        <TextInput v-if="valor.valor_legalizacion"
                                   :id="`valor_legalizacion_${index}`"
                                   :modelValue="formattedValor.get(index)"
                                   @update:modelValue="(value) => formattedValor.set(index, value)"
                                   :error="form.errors[`valor_legalizacion.${index}`]"
                                   placeholder="Valor legalización" type="text" required
                                   class="my-2 block w-full bg-gray-300" disabled
                        />
                        <TextInput v-else
                                   :id="`valor_legalizacion_${index}`"
                                   :modelValue="formattedValor.get(index)"
                                   @update:modelValue="(value) => formattedValor.set(index, value)"
                                   :error="form.errors[`valor_legalizacion.${index}`]"
                                   placeholder="Valor legalización" type="text" required
                                   class="my-2 block w-full"
                        />

                        <InputError :message="form.errors.valor_legalizacion" class="mt-2"/>
                    </div>
                    <div class="mt-2">
                        <InputLabel for="descripcion_legalizacionid"
                                    :value="lang().label['descripcion_legalizacion'] + ' (opcional)'"/>
                        <!--                        <TextInput -->
                        <!--                                :id="`descripcion_legalizacionid_${index}`"-->
                        <!--                            v-model="form.descripcion_legalizacion['index']"-->
                        <!--                                   placeholder="descripcion_legalizacion" type="text" required-->
                        <!--                                   class="my-2 block w-full"-->

                        <input
                            type="text"
                            :id="`descripcion_legalizacionid_${index}`"
                            :value="getDescripcion(index)"
                            @input="(event) => setDescripcion(index, event.target.value)"
                            class="w-full rounded-md shadow-sm placeholder:text-gray-400 placeholder:dark:text-gray-400/50 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary"
                        />
                    </div>
                    <div class="mt-2">
                        <InputLabel for="fecha_legalizacion"
                                    :value="lang().label['fecha_legalizacion']"/>

                        <input
                            type="date"
                            :id="`fecha_legalizacionid_${index}`"
                            :value="getFecha(index)"
                            @input="(event) => setFecha(index, event.target.value)"
                            class="w-full rounded-md shadow-sm placeholder:text-gray-400 placeholder:dark:text-gray-400/50 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary"
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
