<script type="module" setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {formatDate, formatDateTimeToHuman, formatPesosCol} from '@/global.ts';
import {reactive, watchEffect} from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
    viaticoa: Object,
    maintitle: String,
})

const emit = defineEmits(["close"]);
const data = reactive({
    mostrarGeneral: 0,
})

watchEffect(() => {
    if (props.show) {
        if (!props.viaticoa?.Consignaciona.length)
            data.mostrarGeneral = 0
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl8'">
            <section class="text-gray-600 body-font overflow-x-scroll">
                <div
                    class="container xs:px-6 xl:px-1 pb-8 mx-auto md:min-w-[600px] lg:max-w-[1000px] 2xl:min-w-[1800px]">
                    <div class="mt-4 flex justify-end">
                        <SecondaryButton @click="emit('close')">
                            {{ lang().button.close }}
                        </SecondaryButton>
                    </div>

                    <!-- GRID: máximo 2 columnas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 px-1">

                        <!-- Viáticos -->
                        <div v-if="data.mostrarGeneral === 0"
                             v-for="(viatico, index) in props.viaticoa?.Losviaticos"
                             :key="`v-${index}`"
                             class="bg-white rounded-2xl shadow-md p-6 w-full">

                            <h3 v-if="index === 0" class="title-font font-medium text-xl text-gray-900">
                                Saldo de la solicitud: {{ formatPesosCol(props.viaticoa.saldo_sol) }}
                            </h3>
                            <h3 v-else>&nbsp;</h3>

                            <h2 class="title-font text-gray-700 tracking-widest">
                                Viático N°{{ index + 1 }}
                            </h2>

                            <h1 class="text-gray-900 text-2xl title-font font-medium mb-4">
                                {{ viatico?.user.name }}
                            </h1>

                            <!-- Tabs -->
                            <div class="flex mb-4 border-b">
                                <a @click="data.mostrarGeneral = 0"
                                   class="flex-1 py-2 text-lg text-center cursor-pointer"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 0 }">
                                    General
                                </a>
                                <a v-if="props.viaticoa?.Consignaciona.length"
                                   @click="data.mostrarGeneral = 1"
                                   class="flex-1 py-2 text-lg text-center cursor-pointer"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 1 }">
                                    Consignaciones
                                </a>
                            </div>

                            <p class="leading-relaxed mb-4">
                                Del {{ formatDate(viatico.fecha_inicial) }} a {{ formatDate(viatico.fecha_final) }}
                            </p>

                            <div v-show="data.mostrarGeneral === 0" class="flex border-t py-2">
                                <span class="text-gray-500">Número de días</span>
                                <span class="ml-auto text-gray-900">{{ viatico.numerodias }}</span>
                            </div>
                            <div v-show="data.mostrarGeneral === 0" class="flex border-t py-2">
                                <span class="text-gray-500">Valor</span>
                                <span class="ml-auto text-gray-900">{{ formatPesosCol(viatico.gasto) }}</span>
                            </div>
                            <div v-show="data.mostrarGeneral === 0" class="flex border-t border-b py-2">
                                <span class="text-gray-500">Descripción</span>
                                <span class="ml-auto text-sm text-gray-900">{{ viatico.descripcion }}</span>
                            </div>
                        </div>

                        <!-- Consignaciones -->
                        <div v-if="data.mostrarGeneral === 1"
                             v-for="(consignacion, index) in props.viaticoa?.Consignaciona"
                             :key="`c-${index}`"
                             class="bg-white rounded-2xl shadow-md p-6">

                            <h2 class="text-sm title-font text-gray-500 tracking-widest">
                                Consignación N°{{ index + 1 }}
                            </h2>
                            <h1 class="text-gray-900 text-2xl title-font font-medium mb-4">
                                {{
                                    consignacion.fecha_legalizacion ? formatDateTimeToHuman(consignacion.fecha_legalizacion) : 'No hay legalización'
                                }}
                            </h1>

                            <!-- Tabs -->
                            <div class="flex mb-4 border-b">
                                <a @click="data.mostrarGeneral = 0"
                                   class="flex-1 py-2 text-lg text-center cursor-pointer"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 0 }">
                                    General
                                </a>
                                <a @click="data.mostrarGeneral = 1"
                                   class="flex-1 py-2 text-lg text-center cursor-pointer"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 1 }">
                                    Consignaciones
                                </a>
                            </div>

                            <div class="flex border-t py-2">
                                <span class="text-gray-500">Consignado a <b>{{ consignacion.destinatiariu }}</b></span>
                                <span class="ml-auto text-gray-900">{{ formatPesosCol(consignacion.valor) }}</span>
                            </div>
                            <div class="flex border-t py-2">
                                <span class="text-gray-500">Valor legalizado</span>
                                <span class="ml-auto text-gray-900">{{
                                        formatPesosCol(consignacion.valor_legalizacion)
                                    }}</span>
                            </div>
                            <div class="flex border-t border-b py-2">
                                <span class="text-gray-500">Descripción</span>
                                <span class="ml-auto text-gray-900">{{ consignacion.descripcion_legalizacion }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="emit('close')">
                            {{ lang().button.close }}
                        </SecondaryButton>
                    </div>
                </div>

            </section>
        </Modal>
    </section>
</template>
