<script setup>
import InputError from "@/Components/InputError.vue";
import Thex from "@/Components/imfeng/thex.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import "vue-select/dist/vue-select.css";
import {onMounted, watchEffect} from "vue";
import {formatPesosCol} from '@/global.ts';
import vSelect from "vue-select";
// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    numberPermissions: Number,
    route: String,
});
const emit = defineEmits(["close"]);

// const data = reactive({});

// let justNames = props.titulos.map((names) => {
//     if (names['order'] !== 'legalizacion'
//         && names['order'] !== 'saldo'
//         && names['order'] !== 'Consignaciona'
//         && names['order'] !== 'user_id'
//         && names['order'] !== 'fecha_legalizacion'
//         && names['order'] !== 'valor_legalizacion'
//         && names['order'] !== 'fechaconsig'
//         && names['order'] !== 'descripcion_legalizacion'
//     )
//         return names["order"];
// });
// const printForm = [];
// props.titulos.forEach((names) => {
//     if (names['order'] !== 'descripcion'
//         && names['order'] !== 'legalizacion'
//         && names['order'] !== 'Consignaciona'
//         && names['order'] !== 'saldo'
//         && names['order'] !== 'user_id'
//         && names['order'] !== 'fecha_legalizacion'
//         && names['order'] !== 'valor_legalizacion'
//         && names['order'] !== 'fechaconsig'
//         && names['order'] !== 'descripcion_legalizacion'
//     )
//         printForm.push({
//             idd: names["order"],
//             label: names["label"],
//             type: names["type"],
//         });
// });

const form = useForm({
    user_id: [null],
    centro_costo_id: [null],
    descripcion: [null],
    gasto: [null],
    routeadmin: '', //este componente se usa para los supervisores y el admin
});
const addUser = () => {
    form.user_id.push(null);
    form.centro_costo_id.push(null);
    form.descripcion.push(null);
    form.gasto.push(0);
};

const removeUser = (index) => {
    if (form.user_id.length > 1) {
        form.user_id.splice(index, 1);
        form.centro_costo_id.splice(index, 1);
        form.descripcion.splice(index, 1);
        form.gasto.splice(index, 1);
    }
    form.gasto.forEach((ele, inde) => {
        formattedValor.set(inde, 0)
    })
};

onMounted(() => {
    if (props.numberPermissions > 9) {
        // const valueRAn = Math.floor(Math.random() * 9 + 1);
        // form.gasto = valueRAn * 1000;
        // form.saldo = 0;
        // form.descripcion = "descripcion prueba " + valueRAn;
        // form.legalizacion = valueRAn;
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha_legalizacion = "2023-01-01";
    }
});
watchEffect(() => {
    if (props.show) {
        form.errors = {};
        console.log("=>(Create.vue:99) form", form.user_id);
    }
});


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

// function ValidarVacios() {
//     let result = true;
//     printForm.forEach((element) => {
//         if (!form[element.idd] && form[element.idd] !== 0) {
//             console.log(
//                 "=>(Create.vue:70) falta esto papa element.idd",
//                 element.idd
//             );
//             result = false;
//             return result;
//         }
//     });
//     return result;
// }


const create = () => {
    // if (ValidarVacios()) {
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
    // } else {
    //     console.log("Hay campos vacios");
    //     alert("Hay campos vacios");
    // }
};


</script>

<template>
    <section class="space-y-6">
        <Modal :maxWidth="'xl8'" :show="props.show" @close="emit('close')">
            <form class="px-6 pt-6 pb-56" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-50 my-2">
                    {{ lang().label.add }} {{ props.title }}
                </h2>


                <div v-for="(user, index) in form.user_id" :key="index"
                     class="flex flex-wrap items-center gap-2 mb-2"
                     :class="{'mt-8':index !== 0}"
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
                        <label class="dark:text-gray-50">Descripcion</label>
                        <TextInput v-model="form.descripcion[index]" class="py-1 mx-1 w-full"></TextInput>


                    </div>
                    <button type="button" v-if="form.user_id.length > 1"
                            @click="removeUser(index)"
                            v-tooltip="'Eliminar viático'"
                            class="w-7 mt-5 bg-red-600 text-white rounded">
                        <Thex></Thex>
                    </button>
                    <hr>
                </div>
                <button type="button" @click="addUser" class="p-1 m-2 bg-green-700 text-white rounded">Agregar Persona
                </button>


                <!--                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">-->
                <!--                    <div id="SelectVue" class="">-->
                <!--                        <label name="labelSelectVue dark:text-gray-50">Quién necesita el viático</label>-->
                <!--                        <v-select-->
                <!--                            v-model="form.user_id"-->
                <!--                            :options="props.losSelect[0]"-->
                <!--                            label="name"-->
                <!--                        ></v-select>-->
                <!--                    </div>-->
                <!--                    <div id="SelectVue" class="">-->
                <!--                        <label name="labelSelectVue2" class="dark:text-gray-50"> Centro de costo </label>-->
                <!--                        <v-select-->
                <!--                            v-model="form.centro_costo_id"-->
                <!--                            :options="props.losSelect[1]"-->
                <!--                            label="name"-->
                <!--                            class="dark:text-gray-700"-->
                <!--                        ></v-select>-->
                <!--                    </div>-->

                <!--                </div>-->
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