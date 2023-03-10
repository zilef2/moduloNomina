<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';

import { useForm } from '@inertiajs/vue3';
import { watchEffect, reactive } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    project: Object,
})

const data = reactive({
    multipleSelect: false,
})

const emit = defineEmits(["close"]);

const today = new Date();
function isDate(variable) {
  return Object.prototype.toString.call(variable) === '[object Date]';
}
function DayMonthYearTOHtml(date){
    //toma la fecha en string y la retorna en formato input:date
    let splitedDate = date.split('-');
    const year = splitedDate[0];
    const mes = splitedDate[1];
    const dia = splitedDate[2].substring(0,2);
    let returnedString = year+"-"+mes+"-"+dia;
    let dateRetuned = new Date(returnedString);
    if(isDate(dateRetuned)){
        return dateRetuned;
    }
    return returnedString;
}
function fechaInInput(dateit,addDays=0,addMonths=0){
    if(isDate(dateit)){
        let mesConCero = addMonths == 0 ? (dateit.getMonth()+1) : (dateit.getMonth()+1+addMonths);
        let diaConCero = addDays == 0 ? (dateit.getDay()) : (dateit.getDay()+addDays);
        if(mesConCero < 10) mesConCero = '0'+mesConCero;
        if(diaConCero < 10) diaConCero = '0'+diaConCero;
        console.log("🚀 ~ file: Edit.vue:49 ~ fechaInInput ~ dateit:", dateit)
        return (dateit.getFullYear())+"-"+(mesConCero)+'-'+(diaConCero);
    }
    return DayMonthYearTOHtml(dateit); // TODO: turn DATE => string
} 

const form = useForm({
    nombre: '',
    cliente: '',
    valor_tentativo: '',
    valor_acordado: '',
    valor_primer_pago: '',
    fecha_primera_reunion: '',
    fecha_primer_pago: '',
    fecha_entrega: '',
});

const printForm = [
    // {label: 'nombre', type:'text', value:form.nombre},
    {idd: 'nombre',label: 'nombre', type:'text', value:form.nombre},
    {idd: 'cliente',label: 'cliente', type:'text', value:form.cliente},
    {idd: 'valor_tentativo',label: 'valor tentativo', type:'number', value:''+form.valor_tentativo},
    {idd: 'valor_acordado',label: 'valor acordado', type:'number', value:''+form.valor_acordado},
    {idd: 'valor_primer_pago',label: 'valor primer pago', type:'number', value:''+form.valor_primer_pago},
];
const printFormDate = [
    {idd: 'fecha_primera_reunion',label: 'fecha primera reunion', type:'date', value:(form.fecha_primera_reunion)},
    {idd: 'fecha_primer_pago',label: 'fecha primer pago', type:'date', value:(form.fecha_primer_pago)},
    {idd: 'fecha_entrega',label: 'fecha entrega', type:'date', value:(form.fecha_entrega)},
];

const update = () => {
    form.put(route('projects.update', props.project?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () => null,
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        form.nombre = props.project?.nombre
        form.cliente = props.project?.cliente
        form.valor_tentativo = ''+props.project?.valor_tentativo
        form.valor_acordado = ''+props.project?.valor_acordado
        form.valor_primer_pago = ''+props.project?.valor_primer_pago
        form.fecha_primera_reunion = fechaInInput(props.project?.fecha_primera_reunion)
        form.fecha_primer_pago = fechaInInput(props.project?.fecha_primer_pago)
        form.fecha_entrega = fechaInInput(props.project?.fecha_entrega)
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div v-for="(atributosform, indice) in printForm" :key="indice">
                        <InputLabel :for="atributosform.label" :value="atributosform.value" />
                        <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                            v-model="form[atributosform.idd]" required
                            :placeholder="atributosform.label" :error="form.errors[atributosform.idd]" />
                    </div>
                    <div v-for="(atributosformDate, indice) in printFormDate" :key="indice">
                        <InputLabel :for="atributosformDate.idd" :value="atributosformDate.label" />
                        <DatetimeInput :id="atributosformDate.idd" :type="atributosformDate.type" class="mt-1 block w-full"
                            v-model="form[atributosformDate.idd]" required
                            :placeholder="atributosformDate.label" :error="form.errors[atributosformDate.idd]" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
