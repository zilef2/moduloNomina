<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watchEffect} from 'vue';
import "vue-select/dist/vue-select.css";


const props = defineProps({
    show: Boolean,
    title: String,
    generica: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    printForm: []
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
    data.printForm.push({
        idd: names['order'], label: names['label'], type: names['type']
        , value: form[names['order']]
    })
});

watchEffect(() => {
    if (props.show) {
        // data.justNames.forEach(element => {
        //     form[element] =  props.generica[element]
        // });
        form.errors = {}
        props.titulos.forEach(names => {
            form[names['order']] = props.generica[names['order']]
        });

        // form.codigo = props.generica?.codigo
    }
})

const update = () => {
    form.put(route('generic.update', props.generica?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}
// const sexos = [ { label: 'Masculino', value: 'Masculino' }, { label: 'Femenino', value: 'Femenino' } ];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl4'">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    
<!--                    <div id="SelectVue" class="">-->
<!--                        <label name="labelSelectVue2"> Centro de costo </label>-->
<!--                        <v-select :options="props.losSelect[0]"-->
<!--                                  v-model="form.centro_costo_id"-->
<!--                                  label="name"-->
<!--                        ></v-select>-->
<!--                    </div>-->
                    
                    
                    
                    <div v-for="(atributosform, indice) in data.printForm" :key="indice">
                        <div v-if="atributosform.type === 'foreign'" id="SelectVue" class="">
                            <label name="labelSelectVue"> {{ atributosform.label }} </label>
                            <v-select :options="props.losSelect"
                                      v-model="form[atributosform.idd]"
                                      :reduce="element => element.value" label="label"
                            ></v-select>
                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>
                        </div>

                        <div v-else-if="atributosform.type === 'time'" id="SelectVue">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                                       v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                                       :error="form.errors[atributosform.idd]" step="3600"/>
                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>
                        </div>
                        <div v-else class="">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput :id="atributosform.idd" :type="atributosform.type" class="mt-1 block w-full"
                                       v-model="form[atributosform.idd]" required :placeholder="atributosform.label"
                                       :error="form.errors[atributosform.idd]"/>
                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>
                        </div>
                    </div>

                </div>
                <div class=" my-8 flex justify-end">
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

<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}

[name="labelSelectVue"],
[name="labelSelectVue"] {
    /* font-size: 22px; */
    font-weight: 600;
}
</style>
