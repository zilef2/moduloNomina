<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import '@vuepic/vue-datepicker/dist/main.css'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import VueDatePicker from '@vuepic/vue-datepicker';
import Thex from "@/Components/imfeng/thex.vue";
import {formatPesosCol} from '@/global.ts';
import {onMounted, reactive, ref, watchEffect, watch, nextTick, onBeforeUpdate} from "vue";

// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    numberPermissions: Number,
})
const emit = defineEmits(["close"]);

const gastoInputs = ref([]);
onBeforeUpdate(() => {
    gastoInputs.value = [];
});

const data = reactive({
    params: {
        pregunta: ''
    },
})

const form = useForm({
    Solicitante: '',
    Fechasol: '',
    Ciudad: '',
    ObraServicio: '',

    user_id: [null],
    centro_costo_id: [null],
    descripcion: [null],
    gasto: [null],
    numerodias: [null],
    fecha_inicial: [null],
    fecha_final: [null],
    routeadmin: '', //este componente se usa para los supervisores y el admin
});

const addUser = () => {
    const lenghtt = form.user_id.length - 1
    if (!form.user_id[lenghtt] 
        || !form.gasto[lenghtt]
        || !form.fecha_inicial[lenghtt] 
        || !form.descripcion[lenghtt]
    ){
        alert('Debe seleccionar los datos antes de agregar una nueva fila'); return
    }
    
    form.user_id.push(form.user_id[lenghtt]);
    form.descripcion.push(form.descripcion[lenghtt]);
    form.gasto.push(0);
    form.fecha_inicial.push(form.fecha_inicial[lenghtt]);
    form.fecha_final.push(form.fecha_final[lenghtt]);
    form.numerodias.push(form.numerodias[lenghtt]);
    
    nextTick(() => {
        const lastInput = gastoInputs.value[gastoInputs.value.length - 1];
        if (lastInput) {
            lastInput.focus();
        }
    });
};

const removeUser = (index) => {
    if (form.user_id.length > 1) {
        form.user_id.splice(index, 1);
        form.descripcion.splice(index, 1);
        form.gasto.splice(index, 1);
        form.fecha_inicial.splice(index, 1);
        form.fecha_final.splice(index, 1);
        form.numerodias.splice(index, 1);
        
        formattedValor.gasto(index, 0)
    }
    // form.gasto.forEach((ele, inde) => {
    //     formattedValor.set(inde, 0)
    // })
};

onMounted(() => {
    if (props.numberPermissions > 9) {

        const valueRAn = Math.floor(Math.random() * (90000) + 1)
        form.nombre = 'nombre genenerico ' + (valueRAn);
        form.codigo = (valueRAn);
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha = '2023-06-01'

        form.Fechasol = '2025-03-26';
        form.Ciudad = 'Medellin';
        form.ObraServicio = 'Una obra ejemplo';

        form.descripcion[0] = 'observacion genérica ' + (valueRAn);
        form.gasto[0] = valueRAn
        form.Solicitante = props.losSelect[3][0]

        setTimeout(buscarSelects, 2000);

    }
});

function buscarSelects() {
    form.user_id[0] = props.losSelect[0][1]
    form.centro_costo_id[0] = props.losSelect[1][1]
}


watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})


watch(() => form.numerodias, (new_numerodias, old_numerodias) => {
    new_numerodias.forEach((num, index) => {
        if (num !== old_numerodias?.[index]) {
            form.fecha_inicial[index] = null;
        }
    });
}, {deep: true});
watch(() => form.fecha_inicial, (new_fecha_inicial) => {
    new_fecha_inicial.forEach((thedate, index) => {
        if (thedate && thedate[0] && thedate[1]) {
            const fechaInicio = new Date(thedate[0]);
            const fechaFin = new Date(thedate[1]);

            // Restamos las fechas y convertimos a días
            const diffDias = Math.ceil((fechaFin - fechaInicio) / (1000 * 60 * 60 * 24)) + 1;
            form.numerodias[index] = diffDias;

            console.log("Fecha inicial:", fechaInicio);
            console.log("Fecha final:", fechaFin);
            console.log("Diferencia en días:", diffDias);
        }
    });
}, {deep: true});

// import {formatPesosCol} from '@/global.ts';
const formattedValor = {
    get: (index) => {
        return form.gasto && form.gasto[index]
            ? formatPesosCol(form.gasto[index])
            : "";
    },
    set: (index, value) => {
        // Asegurar que gasto es un array
        if (!Array.isArray(form.gasto)) {
            form.gasto = [];
        }

        // Permitir solo números y actualizar el form sin espacios ni símbolos
        form.gasto[index] = value.replace(/\D/g, "");
    }
};

const daynames = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];


const ValidarForm = [];
props.titulos.forEach(names => {
    if (names['order'] !== 'noquiero'
        // && names['order'] !== 'noquiero1'
    )
        ValidarForm.push({
            idd: names['order'], label: names['label'], type: names['type']
        })

    ValidarForm.push({idd: 'user_id', label: 'user_id', type: 'foreign'})
    ValidarForm.push({idd: 'centro_costo_id', label: 'centro_costo_id', type: 'foreign'})

    ValidarForm.push({idd: 'descripcion', label: 'descripcion', type: 'text'})
    ValidarForm.push({idd: 'gasto', label: 'gasto', type: 'text'})
    ValidarForm.push({idd: 'numerodias', label: 'numerodias', type: 'text'})
    ValidarForm.push({idd: 'fecha_inicial', label: 'fecha_inicial', type: 'text'})
    ValidarForm.push({idd: 'fecha_final', label: 'fecha_final', type: 'text'})

});

function ValidarVacios() {
    let result = true
    ValidarForm.forEach(element => {
        if (!form[element.idd]) {
            console.log("=>(Create.vue:70) falta esto papa element.idd", element.idd);
            result = false
            return result
        }
    });
    return result
}

const create = () => {
    if (ValidarVacios()) {
        // console.log("🧈 debu pieza_id:", form.pieza_id);
        form.post(route('solicitud_viatico.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    } else {
        alert('Faltan campos por diligenciar')
    }
}
const ciudades = [
    'Medellin', 'Santamarta', 'Cartagena', 'Valledupar', 'Monteria',
    'Bucaramanga', 'Cucuta', 'Neiva', 'Ibague', 'Girardot', 'Pereira', 'Armenia'
]
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl8'">
            <form class="p-6 mb-36" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>

                <div class="grid xs:grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <div class="w-full">
                        <label class="dark:text-gray-50">Quién realiza la solicitud</label>
                        <!--                        SolicitudViaticoController/ Dependencias-->
                        <vSelect v-model="form.Solicitante" :options="props.losSelect[3]"
                                 label="name"></vSelect>
                    </div>


                    <div class="w-full">
                        <label class="dark:text-gray-50">Fecha Solicitud</label>
                        <TextInput type="date" v-model="form.Fechasol" class="py-1 mx-1 w-full"></TextInput>
                    </div>
                    <div class="w-full">
                        <label class="dark:text-gray-50">Ciudad</label>
                        <vSelect v-model="form.Ciudad" :options="ciudades"
                                 label="name" placeholder="Seleccione una ciudad"></vSelect>
                    </div>
                    <div class="w-full">
                        <label class="dark:text-gray-50">Obra o Servicio</label>
                        <TextInput v-model="form.ObraServicio" class="py-1 mx-1 w-full"></TextInput>
                    </div>
                    <div class="">
                        <label class="dark:text-gray-50">Centro de costo</label>
                        <vSelect v-model="form.centro_costo_id[index]" :options="props.losSelect[1]"
                                 label="name"></vSelect>
                    </div>
                </div>
                <hr class="border-0 h-0.5 bg-gradient-to-r  mb-4
                from-blue-800 via-blue-600 to-blue-100 animate-pulse delay-200 
                rounded-full shadow-lg shadow-indigo-500/50" />


                <div v-for="(user, index) in form.user_id" :key="index"
                     class="grid xs:grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6"
                     :class="{'mt-16':index !== 0}"
                >
                    <div class="col-span-2">
                        <label class="dark:text-gray-50">Quién necesita el viático</label>
                        <vSelect v-model="form.user_id[index]" :options="props.losSelect[0]"
                                 label="name"></vSelect>
                    </div>
                   
                    <div class="">
                        <label class="dark:text-gray-50">Cuanto necesita</label>
                        <TextInput
                            :ref="el => gastoInputs[index] = el"
                            :modelValue="formattedValor.get(index)"
                            @update:modelValue="(value) => formattedValor.set(index, value)"
                            :error="form.errors[`gasto.${index}`]"
                            class="py-1 mx-1 w-full"></TextInput>
                    </div>
                    <div class="col-span-5">
                        <label class="dark:text-gray-50">Descripción</label>
                        <TextInput v-model="form.descripcion[index]" class="py-1 mx-1 w-full text-sm"></TextInput>
                    </div>
                    <div class="col-span-2">
                        <InputLabel :for="'fecha_ini'+index" value="Rango de Fechas"/>
                        <VueDatePicker
                            :enable-time-picker="false" :time-picker="false"
                            :range="{ autoRange: 4 }" auto-apply
                            v-model="form.fecha_inicial[index]"
                            :day-names="daynames" required :id="'fecha_inicial'+index"
                            class="mt-1 block w-full border-0 border-white"
                        />
                    </div>
                    <div class="">
                        <label class="ml-2 dark:text-gray-50">Numero de días</label>
                        <TextInput disabled type="number" v-model="form.numerodias[index]"
                                   class="py-1 mx-1 w-full bg-gray-400"></TextInput>
                    </div>
                    <div class="">
                    <button type="button" v-if="form.user_id.length > 1"
                            @click="removeUser(index)"
                            v-tooltip="'Eliminar viático'"
                            class="w-8 h-8 mt-6 bg-red-600 text-white rounded">
                        <Thex></Thex>
                    </button>
                    </div>

                    <hr>
                </div>
                <button type="button" @click="addUser" class="p-1 m-2 bg-green-700 text-white rounded">
                    Agregar fila
                </button>


                <div class=" my-8 flex justify-start">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3 uppercase" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
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
