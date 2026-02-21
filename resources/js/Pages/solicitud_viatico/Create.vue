<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import '@vuepic/vue-datepicker/dist/main.css'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import VueDatePicker from '@vuepic/vue-datepicker';
import Thex from "@/Components/imfeng/thex.vue";
import { formatPesosCol } from '@/global.ts';
import { onMounted, reactive, ref, watchEffect, watch, nextTick, onBeforeUpdate } from "vue";

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
    centro_costo_id: null,

    user_id: [null],
    descripcion: [null],
    valor_unitario: [null], // nuevo: valor por día
    gasto: [null],          // = valor_unitario × numerodias (o valor directo si es transporte)
    numerodias: [null],
    fecha_inicial: [null],
    fecha_final: [null],
    routeadmin: '', //este componente se usa para los supervisores y el admin
});

// Tipos que NO tienen valor unitario (se ingresa el total directamente)
const tiposTransporte = ['Transporte (ida)', 'Transporte (regreso)'];
const usaValorUnitario = (index) => !tiposTransporte.includes(form.descripcion[index]);

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


// <!-- <editor-fold desc="mafunctions"> -->

// Valor unitario formateado (para mostrar con separadores de miles)
const formattedUnitario = {
    get: (index) => {
        return form.valor_unitario && form.valor_unitario[index]
            ? formatPesosCol(form.valor_unitario[index])
            : "";
    },
    set: (index, value) => {
        if (!Array.isArray(form.valor_unitario)) form.valor_unitario = [];
        const raw = parseInt(value.replace(/\D/g, "")) || 0;
        form.valor_unitario[index] = raw;
        // Recalcular total automáticamente
        if (form.numerodias[index]) {
            form.gasto[index] = raw * form.numerodias[index];
        }
    }
};

// Valor total formateado — para tipos sin unitario se puede editar directo
const formattedValor = {
    get: (index) => {
        return form.gasto && form.gasto[index]
            ? formatPesosCol(form.gasto[index])
            : "";
    },
    set: (index, value) => {
        if (!Array.isArray(form.gasto)) form.gasto = [];
        form.gasto[index] = parseInt(value.replace(/\D/g, "")) || 0;
    }
};

// Colores por tipo de viático
const colorByTipo = (tipo) => {
    const map = {
        'Transporte (ida y regreso)': { border: 'border-blue-500', badge: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' },
        'Transporte (ida)': { border: 'border-sky-400', badge: 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' },
        'Transporte (regreso)': { border: 'border-cyan-400', badge: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300' },
        'Alimentación': { border: 'border-amber-500', badge: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' },
        'Estadia': { border: 'border-purple-500', badge: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' },
        'Caja menor': { border: 'border-emerald-500', badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' },
    };
    return map[tipo] ?? { border: 'border-gray-300', badge: 'bg-gray-100 text-gray-600' };
};
const daynames = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];
const ciudades = [
    'Medellin', 'Santamarta', 'Cartagena', 'Valledupar', 'Monteria',
    'Bucaramanga', 'Cucuta', 'Neiva', 'Ibague', 'Girardot',
    'Pereira', 'Armenia',
    'Bogota', 'Cali', 'Barranquilla', 'Manizales',
];

const tipoViaticos = [
    'Transporte (ida y regreso)',
    'Transporte (ida)',
    'Transporte (regreso)',
    'Alimentación',
    'Estadia',
    'Caja menor',
]


const addUser = () => {
    const lenghtt = form.user_id.length - 1
    if (!form.user_id[lenghtt]
        || !form.gasto[lenghtt]
        || !form.fecha_inicial[lenghtt]
        || !form.descripcion[lenghtt]
    ) {
        alert('Debe seleccionar los datos antes de agregar una nueva fila'); return
    }

    form.user_id.push(form.user_id[lenghtt]);
    form.descripcion.push(form.descripcion[lenghtt]);
    form.valor_unitario.push(null);
    form.gasto.push(0);
    form.fecha_inicial.push(form.fecha_inicial[lenghtt]);
    form.fecha_final.push(form.fecha_final[lenghtt]);
    form.numerodias.push(form.numerodias[lenghtt]);

    nextTick(() => {
        const lastInput = gastoInputs.value[gastoInputs.value.length - 1];
        if (lastInput) lastInput.focus();
    });
};
const removeUser = (index) => {
    if (form.user_id.length > 1) {
        form.user_id.splice(index, 1);
        form.descripcion.splice(index, 1);
        form.valor_unitario.splice(index, 1);
        form.gasto.splice(index, 1);
        form.fecha_inicial.splice(index, 1);
        form.fecha_final.splice(index, 1);
        form.numerodias.splice(index, 1);
    }
};
function buscarSelects() {
    form.user_id[0] = props.losSelect[0][1]
    form.centro_costo_id = props.losSelect[1][1]
}
// <!--</editor-fold>-->


// <!--<editor-fold desc="waches">-->
const line = ref(null)
watchEffect(() => {
    if (props.show) {
        form.errors = {}
        requestAnimationFrame(() => {
            line.value.classList.remove('scale-y-[3]')
        })
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
            const diffDias = Math.ceil((fechaFin - fechaInicio) / (1000 * 60 * 60 * 24)) + 1;
            form.numerodias[index] = diffDias;
            // Recalcular total si hay valor unitario
            if (usaValorUnitario(index) && form.valor_unitario[index]) {
                form.gasto[index] = form.valor_unitario[index] * diffDias;
            }
        }
    });
}, { deep: true });

// Recalcular total cuando cambia la descripción (tipo de viático)
watch(() => form.descripcion, (newDesc) => {
    newDesc.forEach((tipo, index) => {
        if (!tiposTransporte.includes(tipo)) {
            // Si tiene valor unitario, recalcular
            if (form.valor_unitario[index] && form.numerodias[index]) {
                form.gasto[index] = form.valor_unitario[index] * form.numerodias[index];
            }
        }
    });
}, { deep: true });

// import {formatPesosCol} from '@/global.ts';
// <!--</editor-fold>-->


// <!--<editor-fold desc="validate and send">-->

const ValidarForm = [];
const ValidarArrayForm = [
    { idd: 'user_id', label: 'Persona', type: 'foreign' },
    { idd: 'descripcion', label: 'Descripción', type: 'text' },
    { idd: 'gasto', label: 'Gasto', type: 'text' },
    { idd: 'numerodias', label: 'Numero de dias', type: 'text' },
    { idd: 'fecha_inicial', label: 'Fecha inicial', type: 'text' },
];

props.titulos.forEach(names => {
    if (names['order'] !== 'noquiero') {
        ValidarForm.push({
            idd: names['order'], label: names['label'], type: names['type']
        })
        ValidarForm.push({ idd: 'centro_costo_id', label: 'centro_costo_id', type: 'foreign' })
    }
});

function ValidarVacios() {
    let result = true
    ValidarForm.forEach(element => {
        console.log(" achu, espere, validando: ", element.idd, form[element.idd]);
        console.log("1", !form[element.idd]);
        console.log("0", element.idd);

        if (!form[element.idd]) {
            console.log("Falta el siguiente campo: ", element.label);
            alert("Falta El siguiente campo: " + element.label);
            result = false
            return false
        }
    });

    //asumimos que es true
    if (!form.centro_costo_id) {
        console.log(form.centro_costo_id);
        alert("Debe agregar un centro de costo");
        result = false
        return false;
    }


    ValidarArrayForm.forEach(element => {
        console.log("🚀 ~ ValidarVacios ~ element: ", element);
        form[element.idd].forEach(ele => {
            if (!ele) {
                console.log("En uno de los viaticos. falta el siguiente campo: ", element.label);
                alert("Falta el siguiente campo: " + element.label);
                result = false
                return false
            }
        });
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
    }
}

// <!--</editor-fold>-->


</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl8'">
            <form class="p-4 mb-36" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>

                <div class="grid xs:grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="w-full">
                        <label class="dark:text-gray-50">Quién realiza la solicitud</label>
                        <!--                        SolicitudViaticoController/ Dependencias-->
                        <vSelect v-model="form.Solicitante" :options="props.losSelect[3]" label="name"></vSelect>
                    </div>


                    <div class="w-full">
                        <label class="dark:text-gray-50">Fecha Solicitud</label>
                        <TextInput type="date" v-model="form.Fechasol" class="py-1 mx-1 w-full"></TextInput>
                    </div>
                    <div class="w-full">
                        <label class="dark:text-gray-50">Ciudad</label>
                        <vSelect v-model="form.Ciudad" :options="ciudades" label="name"
                            placeholder="Seleccione una ciudad"></vSelect>
                    </div>
                    <div class="">
                        <label class="dark:text-gray-50">Centro de costo</label>
                        <vSelect v-model="form.centro_costo_id" :options="props.losSelect[1]" label="name"></vSelect>
                    </div>
                    <div class="w-full">
                        <label class="dark:text-gray-50">Obra o Servicio</label>
                        <TextInput v-model="form.ObraServicio" class="py-1 mx-1 w-full"></TextInput>
                    </div>
                </div>
                <hr ref="line" class="border-0 h-0.5 bg-gradient-to-r mb-4
                    from-blue-800 via-blue-600 to-blue-100
                    rounded-full shadow-lg shadow-indigo-500/50
                    origin-center scale-y-[3]
                    transition-transform duration-500 ease-out" />


                <!-- ─── Fila de viático ─────────────────────────────────────────── -->
                <div v-for="(user, index) in form.user_id" :key="index" class="relative rounded-xl border-l-4 bg-white dark:bg-gray-800/60 shadow-sm
                           ring-1 ring-gray-100 dark:ring-gray-700/50 p-4 mb-3"
                    :class="colorByTipo(form.descripcion[index]).border">

                    <!-- Badge del tipo + botón eliminar -->
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide"
                            :class="colorByTipo(form.descripcion[index]).badge">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                            {{ form.descripcion[index] ?? 'Sin tipo' }}
                        </span>
                        <button type="button" v-if="form.user_id.length > 1" @click="removeUser(index)"
                            v-tooltip="'Eliminar viático'" class="flex items-center gap-1 text-xs text-red-500 dark:text-red-400
                                   border border-red-300 dark:border-red-700 rounded-lg px-2 py-1
                                   transition-colors duration-200
                                   hover:bg-red-50 dark:hover:bg-red-900/30">
                            <Thex class="w-3 h-3" /> Eliminar
                        </button>
                    </div>

                    <!-- Grid de campos -->
                    <div class="grid xs:grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-x-4 gap-y-3">

                        <!-- Persona -->
                        <div class="col-span-2">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Beneficiario</label>
                            <vSelect v-model="form.user_id[index]" :options="props.losSelect[0]" label="name"></vSelect>
                        </div>

                        <!-- Tipo de viático -->
                        <div class="col-span-3">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Tipo
                                de viático</label>
                            <vSelect v-model="form.descripcion[index]" :options="tipoViaticos" label="name"></vSelect>
                        </div>

                        <!-- Rango de fechas -->
                        <div class="col-span-3">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Rango
                                de fechas</label>
                            <VueDatePicker :enable-time-picker="false" :time-picker="false" :range="{ autoRange: 4 }"
                                auto-apply v-model="form.fecha_inicial[index]" :day-names="daynames" required
                                :id="'fecha_inicial' + index" class="block w-full border-0" />
                        </div>

                        <!-- Número de días (readonly) -->
                        <div class="col-span-1">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Días</label>
                            <TextInput disabled type="number" v-model="form.numerodias[index]"
                                class="py-1.5 w-full text-center font-semibold bg-gray-100 dark:bg-gray-700/50 cursor-not-allowed"
                                title="Se calcula automáticamente del rango de fechas" />
                        </div>

                        <!-- Valor unitario (oculto para transporte ida / regreso) -->
                        <div class="col-span-2" v-if="usaValorUnitario(index)">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Valor
                                unitario / día</label>
                            <TextInput :modelValue="formattedUnitario.get(index)"
                                @update:modelValue="(v) => formattedUnitario.set(index, v)" placeholder="$ 0"
                                class="py-1.5 w-full" />
                        </div>

                        <!-- Valor total -->
                        <div :class="usaValorUnitario(index) ? 'col-span-1' : 'col-span-3'">
                            <label class="block text-xs font-medium mb-1 uppercase tracking-wide" :class="usaValorUnitario(index)
                                ? 'text-gray-400 dark:text-gray-500'
                                : 'text-gray-500 dark:text-gray-400'">
                                {{ usaValorUnitario(index) ? 'Total' : 'Valor total' }}
                            </label>
                            <!-- Si usa unitario → calculado y readonly -->
                            <div v-if="usaValorUnitario(index)" class="flex items-center h-9 px-3 rounded-lg
                                        bg-gradient-to-r from-indigo-50 to-blue-50
                                        dark:from-indigo-900/30 dark:to-blue-900/20
                                        border border-indigo-200 dark:border-indigo-700/50
                                        font-bold text-indigo-700 dark:text-indigo-300 text-sm whitespace-nowrap">
                                {{ formatPesosCol(form.gasto[index] ?? 0) }}
                            </div>
                            <!-- Si es transporte → editable -->
                            <TextInput v-else :ref="el => gastoInputs[index] = el"
                                :modelValue="formattedValor.get(index)"
                                @update:modelValue="(value) => formattedValor.set(index, value)"
                                :error="form.errors[`gasto.${index}`]" placeholder="$ 0" class="py-1.5 w-full" />
                        </div>

                    </div>
                </div>
                <!-- ──────────────────────────────────────────────────────────────── -->
                <button type="button" @click="addUser" class="inline-flex items-center gap-2 px-4 py-2 mt-1 text-sm font-medium
                           text-emerald-700 dark:text-emerald-300
                           border border-emerald-400 dark:border-emerald-600
                           rounded-lg bg-emerald-50 dark:bg-emerald-900/20
                           transition-colors duration-200
                           hover:bg-emerald-100 dark:hover:bg-emerald-800/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar viático
                </button>


                <div class=" my-8 flex justify-start">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3 uppercase" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing" @click="create">
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
