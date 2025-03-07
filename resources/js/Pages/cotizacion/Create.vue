<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watchEffect,watch, computed} from 'vue';

import '@vuepic/vue-datepicker/dist/main.css'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import {Now_Date_to_html} from '@/global.ts';

// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    numberPermissions: Number,
    CentrosRepetidos: Array,
    consecutivoCotizacion: Number,
    cotizacionInicial2: Number,

})
let clasedelgrid = 'my-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 gap-12'
const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
    existe: false,
    formattedtotal: '$ 0',
    formattediva: '$ 0',
    modoaiu: false,
    listas: {
        mes_pedido: [
            {value: 'Enero', label: 'Enero'},
            {value: 'Febrero', label: 'Febrero'},
            {value: 'Marzo', label: 'Marzo'},
            {value: 'Abril', label: 'Abril'},
            {value: 'Mayo', label: 'Mayo'},
            {value: 'Junio', label: 'Junio'},
            {value: 'Julio', label: 'Julio'},
            {value: 'Agosto', label: 'Agosto'},
            {value: 'Septiembre', label: 'Septiembre'},
            {value: 'Octubre', label: 'Octubre'},
            {value: 'Noviembre', label: 'Noviembre'},
            {value: 'Diciembre', label: 'Diciembre'},
        ],
        estado_cliente: [
            {value: 'Por aprobar', label: 'Por aprobar'},
            {value: 'Aprobado', label: 'Aprobado'},
            {value: 'Negado', label: 'Negado'}
        ],
        estado: [
            {value: 'Por ejecutar', label: 'Por ejecutar'},
            {value: 'En ejecucion', label: 'En ejecución'},
            {value: 'Ejecutado', label: 'Ejecutado'},
        ],
        tipo: [
            {value: 'Mantenimiento', label: 'Mantenimiento'},
            {value: 'Servicio', label: 'Servicio'},
            {value: 'Proyecto', label: 'Proyecto'},
        ],
        tipo_de_mantenimiento: [
            {value: 'Preventivo', label: 'Preventivo'},
            {value: 'Correctivo', label: 'Correctivo'},
            {value: 'Nueva Instalacion', label: 'Nueva Instalación'},
        ],
    }
})
let CamposExcluidos = [
    'fecha_aprobacion_cot',
    'factura',
    'fecha_factura',
    'zouna',
    'Prealiza',
    // 'Psolicita',
]
let noValidar = [
    'tipo_de_mantenimiento',
    'centro_costo_id',
    'zouna',
    'Prealiza',
    'Psolicita',
    'subtotal',
]

let justNames = props.titulos.map(names => {
    if (!CamposExcluidos.some((excluid) => excluid === names['order'])) return names['order']
})

justNames = justNames.filter(item => item !== undefined);
const form = useForm({
    ...Object.fromEntries(justNames.map(field => [field, ''])),
    zona_id: '',
    persona_que_realiza_la_pe: '',
    persona_que_solicita_la_propuesta_economica: '',
});
const printForm = [];
props.titulos.forEach(names => {
    if (!CamposExcluidos.some((excluid) => excluid === names['order']))
        printForm.push({
            idd: names['order'], label: names['label'], type: names['type']
        })
});

onMounted(() => {
    let fecha1 = new Date()
    let aniofecha1 = fecha1.getFullYear()
    form.numero_cot = ('PE ' + aniofecha1 + '-' + props.consecutivoCotizacion);
    if (props.numberPermissions > 9) {
        const valueRAn = Math.floor(Math.random() * (90) + 1)
        let StringRAn = "ejem " + (valueRAn * Math.floor(Math.random() * (20) + 1))
        form.descripcion_cot = "ejemplo" + (valueRAn);


        form.mes_pedido = {value: 'Febrero', label: 'Febrero'}
        form.lugar = StringRAn

        // form.precio_cot = (valueRAn) * 1000; // antes del iva
        // form.por_a = (valueRAn) * 0.01;
        // form.por_i = (valueRAn) * 0.01;
        // form.por_u = (valueRAn) * 0.01;
        // form.admi = (valueRAn) * 0.01;
        // form.impr = (valueRAn) * 0.01;
        // form.util = (valueRAn) * 0.01;
        // form.subtotal = (valueRAn) * 100000;
        form.persona_que_realiza_la_pe = StringRAn + '  realiza';
        form.cliente = StringRAn + ' cliente';
        form.persona_que_solicita_la_propuesta_economica = StringRAn + ' solicita';
        form.orden_de_compra = (valueRAn) * 1020;
        form.hes = (valueRAn) * 1021;
        form.observaciones = StringRAn + ' observaciones';
        form.zona_id = {"value": 6, "label": "hola"}
        form.tipo = {"value": "Servicio", "label": "Servicio"}

        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha = '2023-06-01'

    } else { //no es super
    }
    form.estado_cliente = {value: 'Por aprobar', label: 'Por aprobar'}
    form.fecha_solicitud = Now_Date_to_html()
    form.estado = {value: 'por ejecutar', label: 'Por ejecutar'}

});


// <!--<editor-fold desc="validar y create">-->
function ValidarVacios() {
    let result = true
    printForm.forEach(element => {
        if (!form[element.idd] && !noValidar.some((excluied) => excluied === element.idd)) {
            console.log("este elemento esta vacio", element.idd);
            result = false
            return result
        }
    });
    return result
}

const create = () => {
    if (ValidarVacios()) {
        // console.log("🧈 debu zona_id:", form.zona_id);
        form.post(route('cotizacion.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    } else {
        console.log('Hay campos vacios')
        alert('Hay campos vacios')
    }
}
// <!--</editor-fold>-->

watchEffect(() => {
    if (props.show) {

        form.errors = {}

        data.existe = props.CentrosRepetidos.some(item => {
            return (item === ('' + form.numero_cot).trim())
        });
        if (form.subtotal && !data.modoaiu) {
            form.iva = 0.19 * form.subtotal
            form.total = 1.19 * form.subtotal
            data.formattedtotal = 1.19 * form.subtotal
            data.formattedtotal = "$ " + Number(data.formattedtotal).toLocaleString("es-CO").replace(/,/g, " ")
            data.formattediva = 0.19 * form.subtotal
            data.formattediva = "$ " + Number(data.formattediva).toLocaleString("es-CO").replace(/,/g, " ")
        }
    }
})


watch(() => form.por_a, (new_a) => {
    if(new_a > 1){
        form.por_a = new_a / 10
    }
    
    data.modoaiu = true
    form.admi = form.precio_cot * new_a
    
    
})
watch(() => form.por_i, (new_i) => {
    form.impr = form.precio_cot * new_i
    data.modoaiu = true
})
watch(() => form.por_u, (new_u) => {
    form.util = form.precio_cot * new_u
    data.modoaiu = true
    
    // if (data.modoaiu) {
        form.iva = 0.19 * form.util
        form.total = 1.19 * form.util
        data.formattedtotal = 1.19 * form.util
        data.formattediva = 0.19 * form.util
        
        data.formattedtotal = "$ " + Number(data.formattedtotal).toLocaleString("es-CO").replace(/,/g, " ")
        data.formattediva = "$ " + Number(data.formattediva).toLocaleString("es-CO").replace(/,/g, " ")
    // }
})
//very usefull

const formattedPrice = computed({
    get() {
        return form.precio_cot
            ? "$ " + Number(form.precio_cot).toLocaleString("es-CO").replace(/,/g, " ")
            : "";
    },
    set(value) {
        // Permitir solo números y actualizar el form sin espacios ni símbolos
        form.precio_cot = value.replace(/\D/g, "");
    }
});
const formattedSubtotal = computed({
    get() {
        return form.subtotal
            ? "$ " + Number(form.subtotal).toLocaleString("es-CO").replace(/,/g, " ")
            : "";
    },
    set(value) {
        // Permitir solo números y actualizar el form sin espacios ni símbolos
        form.subtotal = value.replace(/\D/g, "");
    }
});


</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl9'" :show="props.show" @close="emit('close')">
            <form class="px-6 pt-4 pb-48" @submit.prevent="create">
                <h2 class="mb-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.adda }} {{ props.title }}
                </h2>
                <p class="my-1 text-md text-gray-800 dark:text-gray-50">Contador inicial: 7334</p>
                <p class="mb-4 text-md text-gray-800 dark:text-gray-50">Numero de cotizaciones realizadas:
                    {{ props.cotizacionInicial2 }}</p>
                <div :class="clasedelgrid">
                    <div class="rounded-xl"><label name="numero_cot"> {{ lang().label.numero_cot }} </label>
                        <TextInput v-model="form.numero_cot" :error="form.errors.numero_cot" :placeholder="lang().label.numero_cot" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <!--                    <div class="rounded-xl">-->
                    <!--                        <label name="centro_costo_id">{{ lang().label.centro_costo_id }}</label>-->
                    <!--                        <v-select v-model="form.centro_costo_id" :options="props.losSelect['centros']"-->
                    <!--                                  label="label"-->
                    <!--                        ></v-select>-->
                    <!--                    </div>-->


                    <div class="rounded-xl"><label name="estado_cliente">
                        {{ lang().label.estado_cliente }}
                    </label>
                        <v-select v-model="form.estado_cliente" :options="data.listas.estado_cliente"
                                  label="label"
                        ></v-select>
                    </div>
                    <div class="rounded-xl"><label name="estado">
                        {{ lang().label.estado }}
                    </label>
                        <v-select v-model="form.estado" :options="data.listas.estado"
                                  label="label"
                        ></v-select>
                    </div>


                    <div class="rounded-xl">
                        <label name="fecha_solicitud"> {{ lang().label.fecha_solicitud }} </label>
                        <TextInput v-model="form.fecha_solicitud" :error="form.errors.fecha_solicitud"
                                   class="mt-1 block w-full"
                                   required type="date"/>
                    </div>
                    <div class="rounded-xl">
                        <label name="mes_pedido">{{ lang().label.mes_pedido }}</label>
                        <v-select v-model="form.mes_pedido" :options="data.listas.mes_pedido" label="label"></v-select>
                    </div>

                    <div class="rounded-xl"><label name="lugar"> {{ lang().label.lugar }} </label>
                        <TextInput v-model="form.lugar" :error="form.errors.lugar" :placeholder="lang().label.lugar" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl">
                        <label name="centro_costo_id">{{ lang().label.zona }}</label>
                        <v-select v-model="form.zona_id" :options="props.losSelect['zonas']"
                                  label="label"
                        ></v-select>
                    </div>

                    <div class="rounded-xl"><label name="descripcion_cot"> {{ lang().label.descripcion_cot }} </label>
                        <TextInput v-model="form.descripcion_cot" :error="form.errors.descripcion_cot" :placeholder="lang().label.descripcion_cot" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="tipo">
                        {{ lang().label.tipo }}
                    </label>
                        <v-select v-model="form.tipo" :options="data.listas.tipo"
                                  label="label"
                        ></v-select>
                    </div>
                    <div v-if="form.tipo.value ==='Mantenimiento' " class="rounded-xl"><label
                        name="tipo_de_mantenimiento">
                        {{ lang().label.tipo_de_mantenimiento }}
                    </label>
                        <v-select v-model="form.tipo_de_mantenimiento" :options="data.listas.tipo_de_mantenimiento"
                                  label="label">
                        </v-select>
                    </div>
                </div>

                <br>
                <hr>
                <br>

                <div :class="clasedelgrid">

                    <!--                    antes del iva-->
                    <div class="rounded-xl">
                        <label name="precio_cot"> {{ lang().label.precio_cot }} </label>
                        <TextInput v-model="formattedPrice" :error="form.errors.precio_cot" :placeholder="lang().label.precio_cot" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="col-span-full"></div>
                    
                    
                    <div class="rounded-xl"><label name="por_a"> {{ lang().label.por_a }} </label>
                        <TextInput v-model="form.por_a" :error="form.errors.por_a" :placeholder="lang().label.por_a" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="admi"> {{ lang().label.admi }} </label>
                        <TextInput v-model="form.admi" disabled :error="form.errors.admi" :placeholder="lang().label.admi" 
                                   class="mt-1 block w-full bg-gray-300"
                                   required type="number"/>
                    </div>
                    
                    
                    <div class="rounded-xl"><label name="por_i"> {{ lang().label.por_i }} </label>
                        <TextInput v-model="form.por_i" :error="form.errors.por_i" :placeholder="lang().label.por_i" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="impr"> {{ lang().label.impr }} </label>
                        <TextInput v-model="form.impr" disabled :error="form.errors.impr" :placeholder="lang().label.impr" 
                                   class="mt-1 block w-full bg-gray-300"
                                   required type="number"/>
                    </div>
                    
                    
                    <div class="rounded-xl"><label name="por_u"> {{ lang().label.por_u }} </label>
                        <TextInput v-model="form.por_u" :error="form.errors.por_u" :placeholder="lang().label.por_u" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="util"> {{ lang().label.util }} </label>
                        <TextInput v-model="form.util" disabled :error="form.errors.util" :placeholder="lang().label.util" 
                                   class="mt-1 block w-full bg-gray-300"
                                   required type="number"/>
                    </div>
                    <div v-if="data.modoaiu" class="rounded-xl">
                        <label name="subtotal"> {{ lang().label.subtotal }} </label>
                        <TextInput v-model="formattedSubtotal" disabled :placeholder="lang().label.subtotal"
                                   class="mt-1 block w-full bg-gray-300"
                                   required type="text"/>
                    </div>
                    <div v-else class="rounded-xl"><label name="subtotal"> {{ lang().label.subtotal }} </label>
                        <TextInput v-model="formattedSubtotal" :error="form.errors.subtotal" :placeholder="lang().label.subtotal" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                </div>

                <br>
                <hr>
                <br>

                <div :class="clasedelgrid">
                    <div class="rounded-xl"><label name="iva"> {{ lang().label.iva }} </label>
                        <TextInput v-model="data.formattediva"
                                   disabled type="text"
                                   class="mt-1 block w-full bg-gray-300"
                        />
                    </div>
                    <div class="rounded-xl"><label name="total"> {{ lang().label.total }} </label>
                        <TextInput v-model="data.formattedtotal"
                                   disabled type="text"
                                   class="mt-1 block w-full bg-gray-300"
                        />
                    </div>

                    <div class="rounded-xl col-span-2">
                        <label name="persona_que_realiza_la_pe">
                            {{ lang().label.persona_que_realiza_la_pe }} </label>
                        <v-select v-model="form.persona_que_realiza_la_pe" :options="props.losSelect['listausers']" label="label"></v-select>
                    </div>

                    <div class="rounded-xl"><label name="cliente"> {{ lang().label.cliente }} </label>
                        <TextInput v-model="form.cliente" :error="form.errors.cliente" :placeholder="lang().label.cliente" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="persona_que_solicita_la_propuesta_economica"> {{ lang().label.persona_que_solicita_la_propuesta_economica }} </label>
                        <TextInput v-model="form.persona_que_solicita_la_propuesta_economica" :error="form.errors.persona_que_solicita_la_propuesta_economica" :placeholder="lang().label.persona_que_solicita_la_propuesta_economica" 
                                   class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
<!--                    <div class="rounded-xl col-span-2">-->
<!--                        <label name="persona_que_solicita_la_propuesta_economica">-->
<!--                            {{ lang().label.persona_que_solicita_la_propuesta_economica }} </label>-->
<!--                        <v-select v-model="form.persona_que_solicita_la_propuesta_economica" :options="props.losSelect['listausers']" label="label"></v-select>-->
<!--                    </div>-->
                    <div class="rounded-xl"><label name="orden_de_compra"> {{ lang().label.orden_de_compra }} </label>
                        <TextInput v-model="form.orden_de_compra" :error="form.errors.orden_de_compra" :placeholder="lang().label.orden_de_compra" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="hes"> {{ lang().label.hes }} </label>
                        <TextInput v-model="form.hes" :error="form.errors.hes" :placeholder="lang().label.hes" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="observaciones"> {{ lang().label.observaciones }} </label>
                        <TextInput v-model="form.observaciones" :error="form.errors.observaciones" :placeholder="lang().label.observaciones" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>

                </div>
                <div class=" my-8 flex justify-end">

                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{
                            lang().button.close
                        }}
                    </SecondaryButton>

                    <p v-if="data.existe" class="mx-auto text-red-700">Ya existe un centro de costo con ese numero</p>
                    <PrimaryButton v-else :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   class="ml-3"
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
