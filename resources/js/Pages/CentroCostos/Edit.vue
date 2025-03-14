<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DatetimeInput from '@/Components/DatetimeInput.vue';
import {useForm} from '@inertiajs/vue3';
import {watchEffect, reactive, ref, watch} from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";


const props = defineProps({
    show: Boolean,
    title: String,
    CentroCosto: Object,
    listaSupervisores: Object,
    losSelect: Array,
})

const data = reactive({
    multipleSelect: false,
    SelecSupervisores: []

})

const emit = defineEmits(["close"]);

const form = useForm({
    nombre: '',
    descripcion: '',
    clasificacion: '',
    activo: true,
    selectedUsers: [],
    listaSupervisores: [],
    ValidoParaFacturar: true,
    zona_id: null,
});

function findIndexById(id) {
    for (const [index, value] of Object.entries(props.CentroCosto.todos)) {
        if (value == id) {
            return index;
        }
    }
    return null;
}

const buscarCheckboxes = () => {
    data.SelecSupervisores = props.listaSupervisores?.map(gen => ({label: gen.name, value: gen.id}))
    let indexTrue
    if (props.CentroCosto?.cuantoshijos) {
        if (data.SelecSupervisores) {
            // if (Array.isArray(props.CentroCosto.todos)) {
            data.SelecSupervisores.forEach(idsupervisor => {
                indexTrue = findIndexById(idsupervisor.value)
                if (indexTrue) form.selectedUsers[indexTrue] = true
            });
            // }else{
            //    indexTrue = data.SelecSupervisores.findIndex((supervisor) =>{
            //         return props.CentroCosto.todos["1"] == supervisor.value
            //    })
            //     form.selectedUsers[indexTrue] = true
            // }
        }
    }
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        form.nombre = props.CentroCosto?.nombre
        form.descripcion = props.CentroCosto?.descripcion
        form.clasificacion = props.CentroCosto?.clasificacion
        form.activo = props.CentroCosto?.activo
        form.activo = ref(!!props.CentroCosto?.activo);
        form.ValidoParaFacturar = props.CentroCosto?.ValidoParaFacturar
        form.ValidoParaFacturar = ref(!!props.CentroCosto?.ValidoParaFacturar);
        // form.selectedUsers =
        data.SelecSupervisores.forEach((ElementoLista,index) => {
            props.CentroCosto.ListaSupervisores.forEach((Supervisor) => {
                if (ElementoLista.value == Supervisor['id'])
                    form.selectedUsers[index] = true;
            })
        })
        if(props.CentroCosto.zona_id){
            form.zona_id = { "value": props.CentroCosto.zona_id, "label": props.CentroCosto.Zouna }
        }
    }
})


watch(() => props.show, (newv, oldv) => {
    if (newv && !oldv) {
        form.selectedUsers = data.SelecSupervisores.map(() => false);
        setTimeout(() => buscarCheckboxes(), 30)

    }
}, {deep: true})


function isChecked(userId, index) {
    if (form.selectedUsers && form.selectedUsers.length) {

        return form.selectedUsers.includes(index);
    }
}

const update = () => {
    form.listaSupervisores = props.listaSupervisores
    form.put(route('CentroCostos.update', props.CentroCosto?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            data.multipleSelect = false
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),

        onFinish: () => null,
    })
}
</script>
<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl4'">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 grid grid-cols-2 gap-6">
                    <div>
                        <InputLabel ref="nombre" for="nombre" :value="lang().label.name"/>
                        <TextInput id="nombre" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.nombre"
                                   :placeholder="lang().placeholder.nombre" :error="form.errors.nombre"/>
                    </div>
                    <div class="rounded-xl">
                        <label name="zona">
                            {{ lang().label.zona }}
                        </label>
                        <vSelect v-model="form.zona_id" :options="losSelect['zona']" label="label"></vSelect>
                    </div>
                    <div>
                        <InputLabel ref="descripcion" for="descripcion" :value="lang().label.descripcion"/>
                        <TextInput id="descripcion" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.descripcion"
                                   :placeholder="lang().placeholder.descripcion" :error="form.errors.descripcion"/>
                    </div>
                    <div>
                        <InputLabel ref="clasificacion" for="clasificacion" :value="lang().label.clasificacion"/>
                        <TextInput id="clasificacion" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full"
                                   v-model="form.clasificacion"
                                   :placeholder="lang().placeholder.clasificacion" :error="form.errors.clasificacion"/>
                    </div>

                    <div class="col-span-2 w-full">
                        <InputLabel for="users" :value="lang().label.supervisores"/>
                        <div v-for="(user, index) in data.SelecSupervisores" :key="index" class="mt-1 block w-full">
                            <label :for="'user' + index">
                                <input
                                    type="checkbox"
                                    :id="'user' + index"
                                    :value="user.value"
                                    v-model="form.selectedUsers[index]"
                                    :checked="isChecked(user.id,index)"
                                />
                                {{ user.label }}
                            </label>
                        </div>
                        <InputError class="mt-2" :message="form.errors.users"/>
                    </div>


                    <div class="inline-flex col-span-1 mt-6">
                        <input
                            @click="toggleCheckbox"
                            v-model="form.activo"
                            type="checkbox" id="activo"
                            class="bg-gray-50 dark:bg-gray-600 mt-1 w-7 h-7 p-2 my-auto rounded-xl"
                        />
                        <InputLabel ref="activo" for="activo" :value="lang().label.activo" class="mx-3 my-auto"/>
                    </div>
                    <div class="inline-flex col-span-1 mt-6">
                        <input
                            @click="toggleCheckbox"
                            v-model="form.ValidoParaFacturar"
                            type="checkbox" id="ValidoParaFacturar"
                            class="bg-gray-50 dark:bg-gray-600 mt-1 w-7 h-7 p-2 my-auto rounded-xl"
                        />
                        <InputLabel ref="ValidoParaFacturar" for="ValidoParaFacturar"
                                    :value="lang().label.ValidoParaFacturar" class="mx-3 my-auto"/>
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
