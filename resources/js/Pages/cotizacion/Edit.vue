<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watchEffect, computed} from 'vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import {Date_to_html} from '@/global.ts';


// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    cotizaciona: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    printForm: [],
    sonSelect: [
        'centro_costo_id',
        'estado_cliente',
        'estado',
        'mes_pedido',
        'tipo',
        'tipo_de_mantenimiento',
    ],
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
            {value: 'por ejecutar', label: 'Por ejecutar'},
            {value: 'en ejecucion', label: 'En ejecución'},
            {value: 'ejecutado', label: 'Ejecutado'},
        ],
        tipo: [
            {value: 'mantenimiento', label: 'Mantenimiento'},
            {value: 'servicio', label: 'Servicio'},
            {value: 'proyecto', label: 'Proyecto'},
        ],
        tipo_de_mantenimiento: [
            {value: 'preventivo', label: 'Preventivo'},
            {value: 'correctivo', label: 'Correctivo'},
            {value: 'nueva instalacion', label: 'Nueva Instalación'},
        ],
    }
})

//very usefull
const justNames = props.titulos.map(names => names['order'])
const form = useForm({...Object.fromEntries(justNames.map(field => [field, '']))});
onMounted(() => {
    if (props.numberPermissions > 9) {
        const valueRAn = Math.floor(Math.random() * 9 + 1)
        form.nombre = 'admin orden trabajo ' + (valueRAn);
        form.codigo = (valueRAn);
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha = '2023-06-01'
    }
    // data.printForm.length -= 1 //dependex
});

props.titulos.forEach(names => {
    if (names['order'] !== 'centro_costo_id' &&
        names['order'] !== 'fecha_aprobacion_cot')
        data.printForm.push({
            idd: names['order'], label: names['label'], type: names['type']
        })
});

watchEffect(() => {
    if (props.show) {
        // data.justNames.forEach(element => {
        //     form[element] =  props.cotizaciona[element]
        // });
        form.errors = {}
        props.titulos.forEach(names => {
            form[names['order']] = props.cotizaciona[names['order']]
        });
        // form.fecha_solicitud = props.cotizaciona.fecha_solicitud

        recuperarLosSelect()
        // form.codigo = props.cotizaciona?.codigo
    }
})

function recuperarLosSelect() {
    form.centro_costo_id = props.losSelect["centros"].find((centro) => {
        return centro.value === props.cotizaciona.centro_costo_id
    })

    console.log("=>(Edit.vue:122) props.cotizaciona.mes_pedido", props.cotizaciona.mes_pedido);
    form.mes_pedido = data.listas.mes_pedido.find((item) => {
        console.log("=>(mes) item.value", item.value);
        return item.value === props.cotizaciona.mes_pedido
    })

    form.estado_cliente = data.listas.estado_cliente.find((item) => item.value === props.cotizaciona.estado_cliente)
    form.estado = data.listas.estado.find((item) => item.value === props.cotizaciona.estado)
    form.tipo = data.listas.tipo.find((item) => item.value === props.cotizaciona.tipo)
    form.tipo_de_mantenimiento = data.listas.tipo_de_mantenimiento.find((item) => item.value === props.cotizaciona.tipo_de_mantenimiento)
}

const update = () => {
    form.put(route('cotizacion.update', props.cotizaciona?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}
const update2 = () => {
    form.put(route('cotizacion.update2', props.cotizaciona?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}

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
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl8'">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-12">
                    <div class="rounded-xl"><label name="numero_cot"> {{ lang().label.numero_cot }} </label>
                        <TextInput v-model="form.numero_cot" :error="form.errors.numero_cot" :placeholder="lang().label.numero_cot" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <!--                    <div class="rounded-xl">-->
                    <!--                        <label name="centro_costo_id">{{ lang().label.centro_costo_id }}</label>-->
                    <!--                        <vSelect v-model="form.centro_costo_id" :options="props.losSelect['centros']"-->
                    <!--                                  label="label"-->
                    <!--                        ></vSelect>-->
                    <!--                    </div>-->

                    <div class="rounded-xl"><label name="estado_cliente">
                        {{ lang().label.estado_cliente }}
                    </label>
                        <vSelect v-model="form.estado_cliente" :options="[
                                              {value:'Por aprobar',label:'Por aprobar'},
                                              {value:'Aprobado',label:'Aprobado'},
                                              {value:'Negado',label:'Negado'}
                                          ]"
                                 label="label"
                        ></vSelect>
                    </div>
                    <div class="rounded-xl"><label name="estado">
                        {{ lang().label.estado }}
                    </label>
                        <vSelect v-model="form.estado" :options="[
                                              {value: 'Por ejecutar',label:'Por ejecutar'},
                                              {value: 'En ejecucion',label:'En ejecución'},
                                              {value: 'Ejecutado',label:'Ejecutado'},
                                          ]"
                                 label="label"
                        ></vSelect>
                    </div>


                    <div class="rounded-xl"><label name="fecha_solicitud"> {{
                            lang().label.fecha_solicitud
                        }} </label>
                        <TextInput v-model="form.fecha_solicitud" :error="form.errors.fecha_solicitud"
                                   class="mt-1 block w-full"
                                   required type="date"/>
                    </div>
                    <div class="rounded-xl">
                        <label name="mes_pedido">{{ lang().label.mes_pedido }}</label>
                        <vSelect v-model="form.mes_pedido" :options="[
                                    {value:'Enero',label:'Enero'},
                                    {value:'Febrero',label:'Febrero'},
                                    {value:'Marzo',label:'Marzo'},
                                    {value:'Abril',label:'Abril'},
                                    {value:'Mayo',label:'Mayo'},
                                    {value:'Junio',label:'Junio'},
                                    {value:'Julio',label:'Julio'},
                                    {value:'Agosto',label:'Agosto'},
                                    {value:'Septiembre',label:'Septiembre'},
                                    {value:'Octubre',label:'Octubre'},
                                    {value:'Noviembre',label:'Noviembre'},
                                    {value:'Diciembre',label:'Diciembre'},
                                  ]"
                                 label="label"
                        ></vSelect>
                    </div>

                    <div class="rounded-xl"><label name="lugar"> {{ lang().label.lugar }} </label>
                        <TextInput v-model="form.lugar" :error="form.errors.lugar" :placeholder="lang().label.lugar" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="descripcion_cot"> {{ lang().label.descripcion_cot }} </label>
                        <TextInput v-model="form.descripcion_cot"
                                   :error="form.errors.descripcion_cot"
                                   :placeholder="lang().label.descripcion_cot" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl">
                        <label name="tipo">
                            {{ lang().label.tipo }}
                        </label>
                        <vSelect v-model="form.tipo" :options="[
                                              {value: 'Mantenimiento',label:'Mantenimiento'},
                                              {value: 'Servicio',label:'Servicio'},
                                              {value: 'Proyecto',label:'Proyecto'},
                                          ]"
                                 label="label"
                        ></vSelect>
                    </div>
                    <div v-if="form.tipo?.value ==='Mantenimiento'" class="rounded-xl"><label
                        name="tipo_de_mantenimiento">
                        {{ lang().label.tipo_de_mantenimiento }}
                    </label>
                        <vSelect v-model="form.tipo_de_mantenimiento" :options="[
                                              {value: 'Preventivo',label:'Preventivo'},
                                              {value: 'Correctivo',label:'Correctivo'},
                                              {value: 'Nueva Instalacion',label:'Nueva Instalación'}, ]"
                                 label="label"></vSelect>
                    </div>
                </div>

                <br>
                <hr>
                <br>

                <div class="my-6 grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-12">

                    <!--                    antes del iva-->
                    <div class="rounded-xl">
                        <label name="precio_cot"> {{ lang().label.precio_cot }} </label>
                        <TextInput v-model="formattedPrice" :error="form.errors.precio_cot" :placeholder="lang().label.precio_cot" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="por_a"> {{ lang().label.por_a }} </label>
                        <TextInput v-model="form.por_a" :error="form.errors.por_a" :placeholder="lang().label.por_a" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="por_i"> {{ lang().label.por_i }} </label>
                        <TextInput v-model="form.por_i" :error="form.errors.por_i" :placeholder="lang().label.por_i" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="por_u"> {{ lang().label.por_u }} </label>
                        <TextInput v-model="form.por_u" :error="form.errors.por_u" :placeholder="lang().label.por_u" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="admi"> {{ lang().label.admi }} </label>
                        <TextInput v-model="form.admi" :error="form.errors.admi" :placeholder="lang().label.admi" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="impr"> {{ lang().label.impr }} </label>
                        <TextInput v-model="form.impr" :error="form.errors.impr" :placeholder="lang().label.impr" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="util"> {{ lang().label.util }} </label>
                        <TextInput v-model="form.util" :error="form.errors.util" :placeholder="lang().label.util" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="subtotal"> {{ lang().label.subtotal }} </label>
                        <TextInput v-model="form.subtotal" :error="form.errors.subtotal" :placeholder="lang().label.subtotal" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="iva"> {{ lang().label.iva }} </label>
                        <TextInput v-model="form.iva" :error="form.errors.iva" :placeholder="lang().label.iva" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="total"> {{ lang().label.total }} </label>
                        <TextInput v-model="form.total" :error="form.errors.total" :placeholder="lang().label.total" class="mt-1 block w-full"
                                   required type="number"/>
                    </div>
                    <div class="rounded-xl"><label name="persona_que_realiza_la_pe">
                        {{ lang().label.persona_que_realiza_la_pe }} </label>
                        <TextInput v-model="form.persona_que_realiza_la_pe" :error="form.errors.persona_que_realiza_la_pe" :placeholder="lang().label.persona_que_realiza_la_pe"
                                   class="mt-1 block w-full" required
                                   type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="cliente"> {{ lang().label.cliente }} </label>
                        <TextInput v-model="form.cliente" :error="form.errors.cliente" :placeholder="lang().label.cliente" class="mt-1 block w-full"
                                   required type="text"/>
                    </div>
                    <div class="rounded-xl"><label name="persona_que_solicita_la_propuesta_economica">
                        {{ lang().label.persona_que_solicita_la_propuesta_economica }} </label>
                        <TextInput v-model="form.persona_que_solicita_la_propuesta_economica" :error="form.errors.persona_que_solicita_la_propuesta_economica"
                                   :placeholder="lang().label.persona_que_solicita_la_propuesta_economica" class="mt-1 block w-full"
                                   required
                                   type="text"/>
                    </div>
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
                    <PrimaryButton v-if="!cotizaciona.factura" class="ml-3"
                                   :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                    <!--                    <PrimaryButton v-if="!cotizaciona.centro_costo_id" class="ml-3" :class="{ 'opacity-25': form.processing }"-->
                    <!--                                   :disabled="form.processing"-->
                    <!--                                   @click="update2">-->
                    <!--                        Generar Centro de costo-->
                    <!--                    </PrimaryButton>-->
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
