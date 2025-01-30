<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import "vue-select/dist/vue-select.css";
import {onMounted, watchEffect} from "vue";
import vSelect from "vue-select";
// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    numberPermissions: Number,
});
const emit = defineEmits(["close"]);

// const data = reactive({});

//very usefull
let justNames = props.titulos.map((names) => {
    if (names['order'] !== 'descripcion'
        // && names['order'] !== 'fecha_aprobacion_cot'
    )
        return names["order"];
});

const form = useForm({
    ...Object.fromEntries(justNames.map((field) => [field, ""])),
});
onMounted(() => {
    if (props.numberPermissions > 9) {
        const valueRAn = Math.floor(Math.random() * 9 + 1);
        form.gasto = valueRAn * 1000;
        form.saldo = 0;
        form.descripcion = "descripcion prueba " + valueRAn;
        form.legalizacion = valueRAn;
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        form.fecha_legalizacion = "2023-01-01";
    }
});

const printForm = [];
props.titulos.forEach((names) => {
    if (names['order'] !== 'descripcion'
        // && names['order'] !== 'centro_costo_id'
    )
        printForm.push({
            idd: names["order"],
            label: names["label"],
            type: names["type"],
        });
});

function ValidarVacios() {
    let result = true;
    printForm.forEach((element) => {
        if (!form[element.idd] && form[element.idd] !== 0) {
            console.log(
                "=>(Create.vue:70) falta esto papa element.idd",
                element.idd
            );
            result = false;
            return result;
        }
    });
    return result;
}

const create = () => {
    if (ValidarVacios()) {
        // console.log("🧈 debu pieza_id:", form.pieza_id);
        form.post(route("viatico.store"), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close");
                form.reset();
            },
            onError: () => null,
            onFinish: () => null,
        });
    } else {
        console.log("Hay campos vacios");
        alert("Hay campos vacios");
    }
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
        console.log("=>(Create.vue:99) form", form.user_id);
    }
});

</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl5'" :show="props.show" @close="emit('close')">
            <form class="px-6 pt-6 pb-56" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-50 my-2">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!--                    <div id="SelectVue" class="">-->
                    <!--                        <label name="labelSelectVue dark:text-gray-50">-->
                    <!--                            Quien necesita el viático-->
                    <!--                        </label>-->
                    <!--                        <v-select-->
                    <!--                            v-model="form.user_id"-->
                    <!--                            :options="props.losSelect[0]"-->
                    <!--                            label="name"-->
                    <!--                        ></v-select>-->
                    <!--                    </div>-->
                    <div id="SelectVue" class="">
                        <label name="labelSelectVue2" class="dark:text-gray-50"> Centro de costo </label>
                        <v-select
                            v-model="form.centro_costo_id"
                            :options="props.losSelect[1]"
                            label="name"
                            class="dark:text-gray-700"
                        ></v-select>
                    </div>
                    <div
                        v-for="(atributosform, indice) in printForm"
                        :key="indice"
                    >
                        <div v-if="atributosform.type === 'foreign'" class=""></div>
                        <div v-else-if="atributosform.type.endsWith('2')" class=""></div>
                        <!--                        <div v-if="atributosform.type === 'foreign'" id="SelectVue" class="">-->
                        <!--                            <label name="labelSelectVue"> {{ atributosform.label }} </label>-->
                        <!--                            <v-select :options="props.losSelect[0]"-->
                        <!--                                      v-model="form[atributosform.idd]"-->
                        <!--                                      :reduce="element => element.value" label="name"-->
                        <!--                            ></v-select>-->
                        <!--                            <InputError class="mt-2" :message="form.errors[atributosform.idd]"/>-->
                        <!--                        </div>-->

                        <!-- tiempo -->
                        <div
                            v-else-if="atributosform.type === 'time'"
                            id="SelectVue"
                        >
                            <InputLabel :for="atributosform.label" :value="lang().label[atributosform.label]"/>
                            <TextInput
                                :id="atributosform.idd"
                                v-model="form[atributosform.idd]"
                                :error="form.errors[atributosform.idd]"
                                :placeholder="atributosform.label"
                                :type="atributosform.type"
                                class="mt-1 block w-full"
                                required
                                step="3600"
                            />
                            <InputError
                                :message="form.errors[atributosform.idd]"
                                class="mt-2"
                            />
                        </div>
                        <!-- datetime -->
                        <div v-else-if="atributosform.type === 'datetime'" id="SelectVue"></div>

                        <!-- normal -->
                        <div v-else class="col-span-full">
                            <InputLabel
                                :for="atributosform.label"
                                :value="lang().label[atributosform.label]"
                            />
                            <TextInput
                                :id="atributosform.idd"
                                v-model="form[atributosform.idd]"
                                :error="form.errors[atributosform.idd]"
                                :placeholder="atributosform.label"
                                :type="atributosform.type"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors[atributosform.idd]"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div class="col-span-full">
                        <InputLabel
                            :for="'descripcion'"
                            :value="lang().label['descripcion']"
                        />
                        <TextInput
                            :id="'descripcion'"
                            v-model="form['descripcion']"
                            :error="form.errors['descripcion']"
                            placeholder="descripcion"
                            :type="'text'"
                            class="mt-4 py-2 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors['descripcion']"
                            class="mt-2"
                        />
                    </div>
                </div>
                <div class="my-8 flex justify-end">
                    <SecondaryButton
                        :disabled="form.processing"
                        @click="emit('close')"
                    >
                        {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="ml-3"
                        @click="create"
                    >
                        {{ lang().button.add }}
                        {{ form.processing ? "..." : "" }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
<style scoped>
@import './vssearch.css';
</style>