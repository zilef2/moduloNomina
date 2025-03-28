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
import {onMounted, reactive,watchEffect,watch} from "vue";

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
    form.user_id.push(null);
    form.centro_costo_id.push(null);
    form.descripcion.push(null);
    form.gasto.push(0);
    form.fecha_inicial.push(0);
    form.fecha_final.push(0);
    form.numerodias.push(0);
};

const removeUser = (index) => {
    if (form.user_id.length > 1) {
        form.user_id.splice(index, 1);
        form.centro_costo_id.splice(index, 1);
        form.descripcion.splice(index, 1);
        formattedValor.gasto(index, 0)
        form.gasto.splice(index, 1);
        form.fecha_inicial.splice(index, 1);
        form.fecha_final.splice(index, 1);
        form.numerodias.splice(index, 1);
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
        
        form.descripcion[0] = valueRAn + ''
        form.gasto[0] = valueRAn
    }
});

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
}, { deep: true });
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
}, { deep: true });


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
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl4'">
            <form class="p-6 mb-36" @submit.prevent="create" >
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
                        <TextInput v-model="form.Ciudad" class="py-1 mx-1 w-full"></TextInput>
                    </div>
                    <div class="w-full">
                        <label class="dark:text-gray-50">Obra o Servicio</label>
                        <TextInput v-model="form.ObraServicio" class="py-1 mx-1 w-full"></TextInput>
                    </div>

                </div>

                <div v-for="(user, index) in form.user_id" :key="index"
                     class="flex flex-wrap items-center gap-2 mb-4"
                     :class="{'mt-16':index !== 0}"
                >
                    <div class="w-4/12">
                        <label class="dark:text-gray-50">Quién necesita el viático</label>
                        <vSelect v-model="form.user_id[index]" :options="props.losSelect[0]"
                                 label="name"></vSelect>
                    </div>
                    <div class="w-4/12">
                        <label class="dark:text-gray-50">Centro de costo</label>
                        <vSelect v-model="form.centro_costo_id[index]" :options="props.losSelect[1]"
                                 label="name"></vSelect>
                    </div>
                    <div class="w-3/12">
                        <label class="dark:text-gray-50">Cuanto necesita</label>
                        <TextInput
                            :modelValue="formattedValor.get(index)"
                            @update:modelValue="(value) => formattedValor.set(index, value)"
                            :error="form.errors[`gasto.${index}`]"
                            class="py-1 mx-1 w-full"></TextInput>
                    </div>
                    <div class="w-10/12">
                        <label class="dark:text-gray-50">Descripción</label>
                        <TextInput v-model="form.descripcion[index]" class="py-1 mx-1 w-full"></TextInput>
                    </div>
                    <div class="w-6/12 mt-5">
                        <InputLabel :for="'fecha_ini'+index" value="Rango de Fechas"/>
                        <VueDatePicker
                            :enable-time-picker="false" :time-picker="false"
                            :range="{ autoRange: 4 }" auto-apply
                            v-model="form.fecha_inicial[index]"
                            :day-names="daynames" required :id="'fecha_inicial'+index"
                            class="mt-1 block w-full border-0 border-white"
                        />
                    </div>
                    <div class="w-2/12 ml-4 mt-4">
                        <label class="ml-2 dark:text-gray-50">Numero de días</label>
                        <TextInput disabled type="number" v-model="form.numerodias[index]"
                                   class="py-1 mx-1 w-full bg-gray-400"></TextInput>
                    </div>

                    <button type="button" v-if="form.user_id.length > 1"
                            @click="removeUser(index)"
                            v-tooltip="'Eliminar viático'"
                            class="w-7 mt-10 bg-red-600 text-white rounded">
                        <Thex></Thex>
                    </button>
                    <hr>
                </div>
                <button type="button" @click="addUser" class="p-1 m-2 bg-green-700 text-white rounded">Agregar Persona
                </button>


                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
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
