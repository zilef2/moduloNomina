<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watchEffect, computed} from 'vue';
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';


const props = defineProps({
    show: Boolean,
    title: String,
    viaticoa: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({
    printForm: [],
    AutoActualizarse: false,
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

    form.user_id
});

props.titulos.forEach(names => {
    data.printForm.push({
        idd: names['order'], label: names['label'], type: names['type']
        , value: form[names['order']]
    })
});


watchEffect(() => {
    if (props.show) {
        form.errors = {}

        if (data.AutoActualizarse) {

            form.gasto = props.viaticoa?.gasto
            form.saldo = props.viaticoa?.saldo
            form.descripcion = props.viaticoa?.descripcion
            form.legalizacion = props.viaticoa?.legalizacion
            form.fecha_legalizacion = props.viaticoa?.fecha_legalizacion
            form.user_id = props.viaticoa?.user_id
            form.centro_costo_id = props.viaticoa?.centro_costo_id

            const datetimeISO = form.fecha_legalizacion; // O puedes usar un string ISO
            const options = {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'};
            data.datetime1 = datetimeISO.toLocaleString('es-ES', options);
            data.AutoActualizarse = false

        }
    } else {
        data.AutoActualizarse = true
    }
})

const update = () => {
    form.put(route('viatico.update', props.viaticoa?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => {
            alert('Hay campos incompletos o erroneos')
        },
        onFinish: () => {
            data.AutoActualizarse = true
        },
    })
}
const selectedUser = computed({
    get: () => props.losSelect[0].find(user => user.id === form.user_id) || null,
    set: (user) => {
        form.user_id = user ? user.id : null;
    }
});
const selectedcc = computed({
    get: () => props.losSelect[1].find(centro => centro.id === form.centro_costo_id) || null,
    set: (centro) => {
        form.centro_costo_id = centro ? centro.id : null;
    }
});
</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl4'" :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    <div id="SelectVue" class="">
                        <label name="labelSelectVue"> Quien necesita el viático </label>
                        <v-select v-model="selectedUser"
                                  :options="props.losSelect[0]"
                                  label="name"
                        ></v-select>
                    </div>
                    <div id="SelectVue" class="">
                        <label name="labelSelectVue2"> Centro de costo </label>
                        <v-select v-model="selectedcc"
                                  :options="props.losSelect[1]"
                                  label="name"
                        ></v-select>
                    </div>


                    <div v-for="(atributosform, indice) in data.printForm" :key="indice">
                        <div v-if="atributosform.type === 'foreign'" class=""></div>
                        <!--                        <div v-if="atributosform.type === 'id'" id="SelectVue">-->
                        <!--                            <label name="labelSelectVue"> {{ atributosform.label }} </label>-->
                        <!--                            <v-select :options="data[atributosform.idd]" label="title" v-model="form[atributosform.idd]"-->
                        <!--                                      :value="data[atributosform.idd][props.viaticoa.viatico_id]"></v-select>-->
                        <!--                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>-->
                        <!--                        </div>-->

                        <div v-else-if="atributosform.type === 'time'" id="SelectVue">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput :id="atributosform.idd" v-model="form[atributosform.idd]"
                                       :error="form.errors[atributosform.idd]"
                                       :placeholder="atributosform.label" :type="atributosform.type"
                                       class="mt-1 block w-full"
                                       required step="3600"/>
                            <InputError :message="form.errors[atributosform.idd]" class="mt-2"/>
                        </div>
                        <!-- datetime -->
                        <div v-else-if="atributosform.type === 'datetime'">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <p id="">{{ data.datetime1 }}</p>
                        </div>


                        <div v-else class="">
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput :id="atributosform.idd" v-model="form[atributosform.idd]"
                                       :error="form.errors[atributosform.idd]"
                                       :placeholder="atributosform.label" :type="atributosform.type"
                                       class="mt-1 block w-full"
                                       required/>
                            <InputError :message="form.errors[atributosform.idd]" class="mt-2"/>
                        </div>
                    </div>

                </div>
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
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
