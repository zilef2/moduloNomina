<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, router } from '@inertiajs/vue3';
import '@vuepic/vue-datepicker/dist/main.css'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import VueDatePicker from '@vuepic/vue-datepicker';
import Thex from "@/Components/imfeng/thex.vue";
import { formatPesosCol } from '@/global.ts';
import { onMounted, onUnmounted, reactive, ref, watchEffect, watch, nextTick, onBeforeUpdate, computed } from "vue";

// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    solicitud_viaticoa: Object,
    titulos: Object,
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
    alertaFechaRepetida: '',
    puedeEditar: true,
    mensajeBloqueo: '',
})

const form = useForm({
    Solicitante: '',
    Fechasol: '',
    Ciudad: '',
    ObraServicio: '',
    centro_costo_id: null,
    user_id: [null],
    descripcion: [null],
    valor_unitario: [null],
    gasto: [null],
    numerodias: [null],
    fecha_inicial: [null],
    fecha_final: [null],
    routeadmin: '',
});

const tiposTransporte = ['Transporte (ida)', 'Transporte (regreso)'];
const usaValorUnitario = (index) => !tiposTransporte.includes(form.descripcion[index]);

const handleKeyDown = (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'd') {
        e.preventDefault();
        addUser();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    
    // Verificar si puede editar - solo si NO hay consignaciones y saldo > 0
    if (props.solicitud_viaticoa) {
        const tieneConsignaciones = props.solicitud_viaticoa.consignacion && 
            props.solicitud_viaticoa.consignacion.length > 0;
        const saldoMayorCero = props.solicitud_viaticoa.saldo_sol > 0;
        
        if (tieneConsignaciones) {
            data.puedeEditar = false;
            data.mensajeBloqueo = 'Esta solicitud ya tiene consignaciones realizadas. No se puede editar.';
        } else if (!saldoMayorCero) {
            data.puedeEditar = false;
            data.mensajeBloqueo = 'El saldo de esta solicitud ya está en cero. No se puede editar.';
        }
        
        // Cargar datos de la solicitud
        form.Solicitante = props.solicitud_viaticoa.Solicitante;
        form.Fechasol = props.solicitud_viaticoa.Fechasol;
        form.Ciudad = props.solicitud_viaticoa.Ciudad;
        form.ObraServicio = props.solicitud_viaticoa.ObraServicio;
        
        // Cargar datos de los viáticos asociados
        const viaticos = props.solicitud_viaticoa.viaticos || props.solicitud_viaticoa.Losviaticos || [];
        
        if (viaticos.length > 0) {
            form.user_id = viaticos.map(v => ({ id: v.user_id, name: v.userino || 'Usuario' }));
            form.descripcion = viaticos.map(v => v.descripcion);
            form.gasto = viaticos.map(v => v.gasto);
            form.numerodias = viaticos.map(v => v.numerodias);
            form.fecha_inicial = viaticos.map(v => [v.fecha_inicial, v.fecha_final]);
            form.valor_unitario = viaticos.map(v => v.numerodias > 0 ? Math.round(v.gasto / v.numerodias) : v.gasto);
        }
    }
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});

// <!-- <editor-fold desc="mafunctions"> -->

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
        if (form.numerodias[index]) {
            form.gasto[index] = raw * form.numerodias[index];
        }
    }
};

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

const centroCostoDesc = computed(() => {
    if (!form.centro_costo_id || !form.centro_costo_id.id) return ''
    return form.centro_costo_id.descripcion ?? ''
})

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


const addUserError = ref('')

const addUser = () => {
    const lenghtt = form.user_id.length - 1
    const faltaFecha = usaValorUnitario(lenghtt) && !form.fecha_inicial[lenghtt]
    if (!form.user_id[lenghtt]
        || !form.gasto[lenghtt]
        || faltaFecha
        || !form.descripcion[lenghtt]
    ) {
        addUserError.value = 'Complete todos los campos del viático actual antes de agregar uno nuevo'
        return
    }
    addUserError.value = ''

    const currentType = form.descripcion[lenghtt];
    const currentIndex = tipoViaticos.indexOf(currentType);
    const nextType = currentIndex !== -1 && currentIndex < tipoViaticos.length - 1
        ? tipoViaticos[currentIndex + 1]
        : currentType;

    if (props.numberPermissions > 9) {
        form.user_id.push(form.user_id[lenghtt]);
        form.descripcion.push(nextType);
        const valueRAn = Math.floor(Math.random() * (900) + 1) * 1000
        form.valor_unitario.push(valueRAn);
        form.gasto.push(valueRAn * form.numerodias[lenghtt]);
        form.fecha_inicial.push(form.fecha_inicial[lenghtt]);
        form.fecha_final.push(form.fecha_final[lenghtt]);
        form.numerodias.push(form.numerodias[lenghtt]);
    } else {
        form.user_id.push(form.user_id[lenghtt]);
        form.descripcion.push(nextType);
        form.valor_unitario.push(null);
        form.gasto.push(0);
        form.fecha_inicial.push(form.fecha_inicial[lenghtt]);
        form.fecha_final.push(form.fecha_final[lenghtt]);
        form.numerodias.push(form.numerodias[lenghtt]);
    }

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
// <!--</editor-fold>-->


// ─── Totales reactivos ───────────────────────────────────────────────────────

const totalesPorBeneficiario = computed(() => {
    const map = new Map()
    form.user_id.forEach((user, i) => {
        if (!user) return
        const nombre = user.name ?? `Beneficiario ${i + 1}`
        const gasto = Number(form.gasto[i]) || 0
        map.set(nombre, (map.get(nombre) ?? 0) + gasto)
    })
    return [...map.entries()].map(([name, total]) => ({ name, total }))
})

const totalesPorCategoria = computed(() => {
    const map = new Map()
    form.descripcion.forEach((tipo, i) => {
        if (!tipo) return
        const gasto = Number(form.gasto[i]) || 0
        map.set(tipo, (map.get(tipo) ?? 0) + gasto)
    })
    return [...map.entries()]
        .filter(([, total]) => total > 0)
        .map(([tipo, total]) => ({ tipo, total }))
})

const totalGeneral = computed(() =>
    form.gasto.reduce((acc, g) => acc + (Number(g) || 0), 0)
)

// <!--<editor-fold desc="waches">-->
const line = ref(null)
watchEffect(() => {
    if (props.show) {
        form.errors = {}
        requestAnimationFrame(() => {
            if (line.value) line.value.classList.remove('scale-y-[3]')
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
        if (!thedate) return;

        if (!Array.isArray(thedate)) {
            form.numerodias[index] = 1;
            return;
        }

        if (thedate[0] && thedate[1]) {
            const fechaInicio = new Date(thedate[0]);
            const fechaFin = new Date(thedate[1]);
            const diffDias = Math.ceil((fechaFin - fechaInicio) / (1000 * 60 * 60 * 24)) + 1;
            form.numerodias[index] = diffDias;
            if (usaValorUnitario(index) && form.valor_unitario[index]) {
                form.gasto[index] = form.valor_unitario[index] * diffDias;
            }
        }
    });
}, { deep: true });

watch(() => form.descripcion, (newDesc) => {
    newDesc.forEach((tipo, index) => {
        if (!tiposTransporte.includes(tipo)) {
            if (form.valor_unitario[index] && form.numerodias[index]) {
                form.gasto[index] = form.valor_unitario[index] * form.numerodias[index];
            }
        }
    });
}, { deep: true });

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
    
    // Validar campos principales
    ValidarForm.forEach(element => {
        if (!form[element.idd]) {
            alert("Falta El siguiente campo: " + element.label);
            result = false
            return false
        }
    });

    if (!form.centro_costo_id) {
        alert("Debe agregar un centro de costo");
        result = false
        return false;
    }

    // Validar arrays
    ValidarArrayForm.forEach(element => {
        form[element.idd].forEach(ele => {
            if (!ele) {
                alert("Falta el siguiente campo: " + element.label);
                result = false
                return false
            }
        });
    });

    if (!result) return false;

    // Validación: Fecha de ida y regreso no pueden ser la misma
    const viajesPorUsuario = {};
    form.user_id.forEach((user, index) => {
        if (!user || !user.id) return;
        const tipo = form.descripcion[index];
        const fecha = Array.isArray(form.fecha_inicial[index])
            ? form.fecha_inicial[index][0]
            : form.fecha_inicial[index];

        if (!viajesPorUsuario[user.id]) viajesPorUsuario[user.id] = {};

        if (tipo === 'Transporte (ida)') viajesPorUsuario[user.id].ida = fecha;
        if (tipo === 'Transporte (regreso)') viajesPorUsuario[user.id].regreso = fecha;
    });
    data.alertaFechaRepetida = ''
    for (const userId in viajesPorUsuario) {
        const viaje = viajesPorUsuario[userId];
        if (viaje.ida && viaje.regreso && viaje.ida === viaje.regreso) {
            const nombreUser = form.user_id.find(u => u.id == userId)?.name || 'el beneficiario';
            data.alertaFechaRepetida = `Para ${nombreUser}, la fecha de ida y regreso no pueden ser la misma.`
            result = false;
            break;
        }
    }

    return result
}

const update = () => {
    // Verificar permisos de edición antes de proceder
    if (!data.puedeEditar) {
        alert(data.mensajeBloqueo);
        return;
    }
    
    if (ValidarVacios()) {
        form.transform((data) => ({
            ...data,
            // Asegurar que todas las fechas salgan como rango [inicio, fin]
            fecha_inicial: data.fecha_inicial.map(f => (f && !Array.isArray(f)) ? [f, f] : f)
        })).put(route('solicitud_viatico.update', props.solicitud_viaticoa.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
                router.reload()
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
            <form class="p-4 mb-36" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Editar {{ props.title }}
                </h2>

                <!-- Mensaje de bloqueo si no puede editar -->
                <div v-if="!data.puedeEditar" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-medium">{{ data.mensajeBloqueo }}</span>
                    </div>
                </div>

                <div class="grid xs:grid-cols-1 md:grid-cols-3 2xl:grid-cols-6 gap-4 gap-y-5 mb-8 mt-4">
                    <!-- Solicitante -->
                    <div class="w-full col-span-2">
                        <label class="dark:text-gray-50">Quién realiza la solicitud</label>
                        <vSelect v-model="form.Solicitante" :options="props.losSelect[3]" label="name" :disabled="!data.puedeEditar"></vSelect>
                    </div>

                    <!-- Fecha -->
                    <div class="w-full">
                        <label class="dark:text-gray-50">Fecha Solicitud</label>
                        <TextInput type="date" v-model="form.Fechasol" class="py-1 mx-1 w-full" :disabled="!data.puedeEditar"></TextInput>
                    </div>

                    <!-- Ciudad -->
                    <div class="w-full">
                        <label class="dark:text-gray-50">Ciudad</label>
                        <vSelect v-model="form.Ciudad" :options="ciudades" label="name"
                            placeholder="Seleccione una ciudad" :disabled="!data.puedeEditar"></vSelect>
                    </div>

                    <!-- Centro de costo -->
                    <div class="">
                        <label class="dark:text-gray-50">Centro de costo</label>
                        <vSelect v-model="form.centro_costo_id" :options="props.losSelect[1]" label="name" :disabled="!data.puedeEditar">
                            <template #option="opt">
                                <div class="flex flex-col py-0.5">
                                    <span class="font-medium text-sm leading-tight">{{ opt.name }}</span>
                                    <span v-if="opt.descripcion"
                                        class="text-xs text-gray-400 dark:text-gray-500 leading-tight truncate">
                                        {{ opt.descripcion }}
                                    </span>
                                </div>
                            </template>
                        </vSelect>
                    </div>
                    <!-- Obra o Servicio -->
                    <div class="w-full">
                        <label class="dark:text-gray-50">Obra o Servicio</label>
                        <TextInput v-model="form.ObraServicio" class="py-1 mx-1 w-full" :disabled="!data.puedeEditar"></TextInput>
                    </div>
                    <!-- Descripción del centro -->
                    <div class="w-full col-span-3">
                        <label class="dark:text-gray-50">Descripción centro</label>
                        <TextInput :modelValue="centroCostoDesc" disabled
                            placeholder="Se completa al seleccionar centro"
                            class="py-1 mx-1 w-full bg-gray-50 dark:bg-gray-700/40 text-gray-500 dark:text-gray-400 cursor-not-allowed italic" />
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
                        <button type="button" v-if="form.user_id.length > 1 && data.puedeEditar" @click="removeUser(index)"
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
                            <vSelect v-model="form.user_id[index]" :options="props.losSelect[0]" label="name" :disabled="!data.puedeEditar"></vSelect>
                        </div>

                        <!-- Tipo de viático -->
                        <div class="col-span-3">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Tipo
                                de viático</label>
                            <vSelect v-model="form.descripcion[index]" :options="tipoViaticos" label="name" :disabled="!data.puedeEditar"></vSelect>
                        </div>

                        <!-- Rango de fechas -->
                        <div class="col-span-3" v-if="usaValorUnitario(index)">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Rango
                                de fechas</label>
                            <VueDatePicker :enable-time-picker="false" :time-picker="false" :range="{ autoRange: 4 }"
                                auto-apply v-model="form.fecha_inicial[index]" :day-names="daynames" required
                                :id="'fecha_inicial' + index" class="block w-full border-0" :disabled="!data.puedeEditar" />
                        </div>

                        <!-- Número de días -->
                        <div class="col-span-1" v-if="usaValorUnitario(index)">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Días</label>
                            <TextInput disabled type="number" v-model="form.numerodias[index]"
                                class="py-1.5 w-full text-center font-semibold bg-gray-100 dark:bg-gray-700/50 cursor-not-allowed"
                                title="Se calcula automáticamente del rango de fechas" />
                        </div>
                        <div v-else class="col-span-4">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                                Fecha del viaje
                            </label>
                            <TextInput type="date" v-model="form.fecha_inicial[index]" required
                                :id="'fecha_inicial' + index" class="py-1.5 w-full" :disabled="!data.puedeEditar" />
                        </div>

                        <!-- Valor unitario -->
                        <div class="col-span-2" v-if="usaValorUnitario(index)">
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Valor
                                unitario / día</label>
                            <TextInput :modelValue="formattedUnitario.get(index)"
                                @update:modelValue="(v) => formattedUnitario.set(index, v)" placeholder="$ 0"
                                class="py-1.5 w-full" :disabled="!data.puedeEditar" />
                        </div>

                        <!-- Valor total -->
                        <div :class="usaValorUnitario(index) ? 'col-span-1' : 'col-span-3'">
                            <label class="block text-xs font-medium mb-1 uppercase tracking-wide" :class="usaValorUnitario(index)
                                ? 'text-gray-400 dark:text-gray-500'
                                : 'text-gray-500 dark:text-gray-400'">
                                {{ usaValorUnitario(index) ? 'Total' : 'Valor total' }}
                            </label>
                            <div v-if="usaValorUnitario(index)" class="flex items-center h-9 px-3 rounded-lg
                                        bg-gradient-to-r from-indigo-50 to-blue-50
                                        dark:from-indigo-900/30 dark:to-blue-900/20
                                        border border-indigo-200 dark:border-indigo-700/50
                                        font-bold text-indigo-700 dark:text-indigo-300 text-sm whitespace-nowrap">
                                {{ formatPesosCol(form.gasto[index] ?? 0) }}
                            </div>
                            <TextInput v-else :ref="el => gastoInputs[index] = el"
                                :modelValue="formattedValor.get(index)"
                                @update:modelValue="(value) => formattedValor.set(index, value)"
                                :error="form.errors[`gasto.${index}`]" placeholder="$ 0" class="py-1.5 w-full" :disabled="!data.puedeEditar" />
                        </div>

                    </div>
                </div>
                <!-- Botón agregar + error inline -->
                <div class="flex items-center gap-3 mt-2" v-if="data.puedeEditar">
                    <button type="button" @click="addUser" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
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
                    <Transition enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="opacity-0 -translate-x-2" enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
                        <p v-if="addUserError"
                            class="flex items-center gap-1.5 text-xs text-red-500 dark:text-red-400 font-medium">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ addUserError }}
                        </p>
                    </Transition>
                </div>


                <!-- ─── Panel de alertas ─────────────────────────────────────── -->
                <Transition enter-active-class="transition-all duration-1500 ease-out"
                    enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="data.alertaFechaRepetida" class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700
                                bg-gray-50/80 dark:bg-gray-800/50 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3
                                    border-b border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-800">
                            <span
                                class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                Alerta de fecha repetida
                            </span>
                            <span class="text-base font-bold text-red-700 dark:text-red-300">
                                {{ data.alertaFechaRepetida }}
                            </span>
                        </div>
                    </div>
                </Transition>
                <!-- ─── Panel de resumen ─────────────────────────────────────── -->
                <Transition enter-active-class="transition-all duration-1500 ease-out"
                    enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="totalGeneral > 0" class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700
                                bg-gray-50/80 dark:bg-gray-800/50 overflow-hidden">

                        <!-- Header del resumen -->
                        <div class="flex items-center justify-between px-4 py-3
                                    border-b border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-800">
                            <span
                                class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                Resumen de la solicitud
                            </span>
                            <span class="text-base font-bold text-indigo-700 dark:text-indigo-300">
                                Total: {{ formatPesosCol(totalGeneral) }}
                            </span>
                        </div>

                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-0 divide-y md:divide-y-0 md:divide-x divide-gray-200 dark:divide-gray-700">

                            <!-- Por beneficiario -->
                            <div class="p-4">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                                    Por beneficiario
                                </p>
                                <ul class="space-y-2">
                                    <li v-for="b in totalesPorBeneficiario" :key="b.name"
                                        class="flex items-center justify-between">
                                        <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                                         bg-indigo-100 dark:bg-indigo-900/40
                                                         text-indigo-700 dark:text-indigo-300
                                                         text-xs font-bold shrink-0">
                                                {{ b.name.charAt(0).toUpperCase() }}
                                            </span>
                                            {{ b.name }}
                                        </span>
                                        <span
                                            class="font-semibold text-sm text-gray-800 dark:text-gray-200 tabular-nums">
                                            {{ formatPesosCol(b.total) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Por categoría -->
                            <div class="p-4">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                                    Por categoría
                                </p>
                                <ul class="space-y-2">
                                    <li v-for="c in totalesPorCategoria" :key="c.tipo"
                                        class="flex items-center justify-between">
                                        <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0"
                                                :class="colorByTipo(c.tipo).border.replace('border-', 'bg-')">
                                            </span>
                                            {{ c.tipo }}
                                        </span>
                                        <span
                                            class="font-semibold text-sm text-gray-800 dark:text-gray-200 tabular-nums">
                                            {{ formatPesosCol(c.total) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </Transition>
                <!-- ──────────────────────────────────────────────────────────── -->

                <div class="my-8 flex justify-start">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3 uppercase" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing || !data.puedeEditar" @click="update">
                        {{ lang().button.update }} {{ form.processing ? '...' : '' }}
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
    font-weight: 600;
}
</style>
