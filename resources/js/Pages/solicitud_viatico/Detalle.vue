<script type="module" setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {formatDate, formatDateTimeToHuman, formatPesosCol} from '@/global.ts';
import { reactive, watchEffect} from 'vue';

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
        if(!props.viaticoa?.Consignaciona.length)
            data.mostrarGeneral = 0
    }
})
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl8'">
            <section class="text-gray-600 body-font overflow-hidden">
                <div class="container px-5 pb-8 mx-auto">
                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                    </div>
                    <div class="lg:w-11/12 mx-auto flex flex-wrap">
                        <div v-if="data.mostrarGeneral === 0" v-for="(viatico, index) in props.viaticoa?.Losviaticos" :key="index"
                             class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                            <h2 class="text-sm title-font text-gray-500 tracking-widest">
                                Viático N°{{ index + 1 }}
                            </h2>
                            <h1 class="text-gray-900 text-2xl title-font font-medium mb-4">
                                {{ viatico?.user.name }}</h1>
                            <div class="flex mb-4">
                                <a @click="data.mostrarGeneral = 0"
                                   class="flex-grow  py-2 text-lg px-1"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 0 }">
                                    General
                                </a>
                                <a v-if="props.viaticoa?.Consignaciona.length"
                                    @click="data.mostrarGeneral = 1"
                                   class="flex-grow border-b-2 border-gray-300 py-2 text-lg px-1"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 1 }">
                                    Consignaciones
                                </a>
                            </div>
                            <p class="leading-relaxed mb-4">
                                Del {{ formatDate(viatico.fecha_inicial) }} a {{ formatDate(viatico.fecha_final) }}
                            </p>
                            <div v-show="data.mostrarGeneral === 0" class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-500">Numero de días</span>
                                <span class="ml-auto text-gray-900">{{ viatico.numerodias }}</span>
                            </div>
                            <div v-show="data.mostrarGeneral === 0" class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-500">Valor</span>
                                <span class="ml-auto text-gray-900">{{ formatPesosCol(viatico.gasto) }}</span>
                            </div>
                            <div v-show="data.mostrarGeneral === 0"
                                 class="flex border-t border-b mb-6 border-gray-200 py-2">
                                <span class="text-gray-500 mr-8">Descripción </span>
                                <span class="ml-auto text-sm text-gray-900">{{ viatico.descripcion }}</span>
                            </div>
                            <div v-if="index === 0" class="flex">
                                <span class="title-font font-medium text-2xl text-gray-900">Saldo {{
                                        formatPesosCol(props.viaticoa.saldo_sol)
                                    }}</span>
                            </div>
                        </div>
                        
                        <div v-if="data.mostrarGeneral === 1" v-for="(consignacion, index) in props.viaticoa?.Consignaciona" :key="index"
                             class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                            <h2 class="text-sm title-font text-gray-500 tracking-widest">
                                Consignación N°{{ index + 1 }}
                            </h2>
                            <h1 class="text-gray-900 text-2xl title-font font-medium mb-4">
                                {{ consignacion.fecha_legalizacion ? formatDateTimeToHuman(consignacion.fecha_legalizacion) : 'No hay legalización' }}</h1>
                            <div class="flex mb-4">
                                <a @click="data.mostrarGeneral = 0"
                                   class="flex-grow  py-2 text-lg px-1"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 0 }">
                                    General
                                </a>
                                <a @click="data.mostrarGeneral = 1"
                                   class="flex-grow border-b-2 border-gray-300 py-2 text-lg px-1"
                                   :class="{ 'text-indigo-500 border-b-2 border-indigo-500': data.mostrarGeneral === 1 }">
                                    Consignaciones
                                </a>
                            </div>
                            <div class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-500">Consignado</span>
                                <span class="ml-auto text-gray-900">{{ formatPesosCol(consignacion.valor) }}</span>
                            </div>
                            <div class="flex border-t border-gray-200 py-2">
                                <span class="text-gray-500">Valor legalizado</span>
                                <span class="ml-auto text-gray-900">{{ formatPesosCol(consignacion.valor_legalizacion) }}</span>
                            </div>
                            <div class="flex border-t border-b mb-6 border-gray-200 py-2">
                                <span class="text-gray-500">Descripción</span>
                                <span class="ml-auto text-gray-900">{{ consignacion.descripcion_legalizacion }}</span>
                            </div>
                            <div v-if="index === 0" class="flex">
                                <span class="title-font font-medium text-2xl text-gray-900">Saldo {{
                                        formatPesosCol(props.viaticoa.saldo_sol)
                                    }}</span>
                            </div>
                            
                        </div>
                        
                        <img alt="ecommerce" class="xs:mx-1 lg:mx-24 2xl:mx-48 w-full lg:h-auto h-64 object-cover object-center rounded"
                             src="https://dummyimage.com/350x150/cccccc/000000&text=Comprobante">
                    </div>
                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="emit('close')"> {{ lang().button.close }}</SecondaryButton>
                    </div>
                </div>
            </section>
        </Modal>
    </section>
</template>
