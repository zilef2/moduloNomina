<script setup>
import { computed } from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {
    formatDate,
    formatDateTimeToHuman,
    formatPesosCol
} from '@/global.ts';
import {
    UserIcon,
    MapPinIcon,
    CalendarIcon,
    BriefcaseIcon,
    CurrencyDollarIcon,
    TicketIcon,
    CreditCardIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
    title: String,
    viaticoa: Object,
    maintitle: String,
})

const emit = defineEmits(["close"]);

const hasConsignaciones = computed(() => props.viaticoa?.Consignaciona?.length > 0);

const generalInfo = computed(() => [
    { label: 'Solicitante', value: props.viaticoa?.Solicitante, icon: UserIcon },
    { label: 'Ciudad', value: props.viaticoa?.Ciudad, icon: MapPinIcon },
    { label: 'Fecha Solicitud', value: props.viaticoa?.Fechasol, icon: CalendarIcon },
    { label: 'Obra / Servicio', value: props.viaticoa?.ObraServicio, icon: BriefcaseIcon },
    { label: 'Centro de Costo', value: props.viaticoa?.centrou, icon: MapPinIcon },
]);

const totalSolicitado = computed(() =>
    props.viaticoa?.Losviaticos?.reduce((acc, v) => acc + (Number(v.gasto) || 0), 0) || 0
);

const totalConsignado = computed(() =>
    props.viaticoa?.Consignaciona?.reduce((acc, c) => acc + (Number(c.valor) || 0), 0) || 0
);

const totalLegalizado = computed(() =>
    props.viaticoa?.Consignaciona?.reduce((acc, c) => acc + (Number(c.valor_legalizacion) || 0), 0) || 0
);

const porcentajeLegalizado = computed(() => {
    if (totalConsignado.value === 0) return 0;
    return Math.round((totalLegalizado.value / totalConsignado.value) * 100);
});

</script>

<template>
    <Modal :show="props.show" @close="emit('close')" maxWidth="xl4">
        <div
            class="relative bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[95vh] transition-all duration-300">

            <!-- Header -->
            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                <div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-gray-100">
                        Detalle de Solicitud
                    </h2>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">
                        {{ props.title }}
                    </p>
                </div>
                <button @click="emit('close')"
                    class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200 hover:rotate-90">
                    <XMarkIcon class="w-6 h-6 text-gray-400" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">

                <!-- Sección superior: Info General & Totales -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Datos de la solicitud -->
                    <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                        <div v-for="info in generalInfo" :key="info.label" class="flex items-start gap-3 group">
                            <div
                                class="p-2.5 bg-gray-100 dark:bg-gray-800 rounded-xl text-gray-500 dark:text-gray-400 shrink-0 group-hover:bg-green-100 dark:group-hover:bg-green-900/30 group-hover:text-green-600 transition-all duration-300">
                                <component :is="info.icon" class="w-4 h-4" />
                            </div>
                            <div>
                                <p
                                    class="text-[9px] uppercase font-black text-gray-400 dark:text-gray-500 tracking-tighter mb-1">
                                    {{ info.label }}
                                </p>
                                <p class="text-sm font-bold text-gray-700 dark:text-gray-200 leading-tight">
                                    {{ info.value || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen Financiero Estilizado -->
                    <div class="lg:col-span-4 flex flex-col gap-4">
                        <div
                            class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-3xl p-5 border border-green-100 dark:border-green-800/50 shadow-sm relative overflow-hidden group">
                            <!-- Barra de progreso miniatura -->
                            <div class="absolute top-0 left-0 h-1 bg-green-500/20 w-full">
                                <div class="h-full bg-green-500 transition-all duration-1000 ease-out"
                                    :style="`width: ${porcentajeLegalizado}%`"></div>
                            </div>

                            <div class="flex justify-between items-end mb-4">
                                <div>
                                    <p
                                        class="text-[10px] uppercase font-black text-green-600/60 dark:text-green-400/60 tracking-wider">
                                        Monto Legalizado</p>
                                    <p
                                        class="text-2xl font-black text-green-700 dark:text-green-300 tabular-nums leading-none mt-1">
                                        {{ formatPesosCol(totalLegalizado) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-xs font-black text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/50 px-2 py-1 rounded-lg">
                                        {{ porcentajeLegalizado }}%
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2 border-t border-green-200/50 dark:border-green-700/50 pt-4">
                                <div
                                    class="flex justify-between text-xs font-bold text-green-800/60 dark:text-green-200/40">
                                    <span>Consignado</span>
                                    <span>{{ formatPesosCol(totalConsignado) }}</span>
                                </div>
                                <div class="flex justify-between text-xs font-bold text-gray-400">
                                    <span>Solicitado</span>
                                    <span>{{ formatPesosCol(totalSolicitado) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card de Saldo -->
                        <div
                            class="bg-gray-50 dark:bg-gray-800/40 rounded-2xl px-5 py-3 border border-gray-100 dark:border-gray-800 flex justify-between items-center transition-all hover:border-amber-200 dark:hover:border-amber-900/50">
                            <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Saldo
                                Pendiente</span>
                            <span class="text-lg font-black text-amber-600 dark:text-amber-500 tabular-nums">
                                {{ formatPesosCol(props.viaticoa?.saldo_sol) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tabs Desglose -->
                <TabGroup>
                    <TabList class="flex space-x-2 rounded-2xl bg-gray-100 dark:bg-gray-800/80 p-1.5 mb-6 max-w-sm">
                        <Tab as="template" v-slot="{ selected }">
                            <button :class="[
                                'w-full rounded-xl py-2.5 text-xs font-black uppercase tracking-widest transition-all duration-300 outline-none',
                                selected
                                    ? 'bg-white dark:bg-gray-700 text-green-700 dark:text-green-300 shadow-md transform scale-[1.02]'
                                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                            ]">
                                <div class="flex items-center justify-center gap-2">
                                    <TicketIcon class="w-3.5 h-3.5" />
                                    Viáticos
                                </div>
                            </button>
                        </Tab>
                        <Tab as="template" v-slot="{ selected }">
                            <button :class="[
                                'w-full rounded-xl py-2.5 text-xs font-black uppercase tracking-widest transition-all duration-300 outline-none',
                                selected
                                    ? 'bg-green-600 text-white shadow-lg shadow-green-500/20 transform scale-[1.02]'
                                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                            ]">
                                <div class="flex items-center justify-center gap-2">
                                    <CreditCardIcon class="w-3.5 h-3.5" />
                                    Consignaciones
                                </div>
                            </button>
                        </Tab>
                    </TabList>

                    <TabPanels>
                        <!-- Panel de Viáticos -->
                        <TabPanel class="outline-none focus:outline-none">
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4 animate-in fade-in slide-in-from-bottom-2 duration-500">
                                <div v-for="(viatico, index) in props.viaticoa?.Losviaticos" :key="index"
                                    class="p-4 rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800/20 hover:border-green-200 dark:hover:border-green-900/50 transition-all duration-300 group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <p
                                                class="text-[9px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-tighter mb-0.5">
                                                {{ viatico.descripcion || 'Sin descripción' }}
                                            </p>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-gray-100 leading-tight">
                                                {{ viatico.user?.name }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p
                                                class="text-base font-black text-gray-900 dark:text-gray-100 tabular-nums">
                                                {{ formatPesosCol(viatico.gasto) }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase">
                                                {{ viatico.numerodias }} días
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="pt-3 border-t border-gray-50 dark:border-gray-700/50 flex justify-between items-center text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-tight">
                                        <span>Ida: <b class="text-gray-900 dark:text-gray-200">{{
                                                formatDate(viatico.fecha_inicial) }}</b></span>
                                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                        <span>Regreso: <b class="text-gray-900 dark:text-gray-200">{{
                                                formatDate(viatico.fecha_final) }}</b></span>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>

                        <!-- Panel de Consignaciones -->
                        <TabPanel class="outline-none focus:outline-none">
                            <div v-if="!hasConsignaciones"
                                class="py-12 text-center rounded-3xl bg-gray-50/50 dark:bg-gray-800/10 border-2 border-dashed border-gray-200 dark:border-gray-700/50">
                                <p class="text-gray-400 font-medium italic">No se han registrado consignaciones o
                                    legalizaciones.</p>
                            </div>
                            <div v-else class="space-y-4 animate-in fade-in slide-in-from-bottom-2 duration-500">
                                <div v-for="(cons, index) in props.viaticoa?.Consignaciona" :key="index"
                                    class="p-5 rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800/20 flex flex-wrap items-center gap-6 hover:border-green-300 dark:hover:border-green-800 transition-all duration-300">
                                    <div
                                        class="shrink-0 w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 shadow-sm border border-green-100 dark:border-green-800/50">
                                        <CurrencyDollarIcon class="w-6 h-6" />
                                    </div>
                                    <div class="flex-1 min-w-[200px]">
                                        <p
                                            class="text-[9px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-wider mb-1">
                                            Beneficiario</p>
                                        <p class="text-sm font-black text-gray-800 dark:text-gray-200 leading-none">{{
                                            cons.destinatiariu }}</p>
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <p
                                            class="text-[9px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-wider mb-1">
                                            Legalización</p>
                                        <p
                                            class="text-sm font-bold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <span v-if="cons.fecha_legalizacion"
                                                class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            <span v-else class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            {{ cons.fecha_legalizacion ? formatDateTimeToHuman(cons.fecha_legalizacion)
                                            : 'Pendiente' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="mb-1">
                                            <span
                                                class="text-[9px] font-black uppercase text-green-600 dark:text-green-500 tracking-widest mr-2">Monto
                                                Consignado:</span>
                                            <span
                                                class="text-sm font-black text-gray-900 dark:text-gray-100 tabular-nums">
                                                {{ formatPesosCol(cons.valor) }}
                                            </span>
                                        </div>
                                        <div v-if="cons.valor_legalizacion"
                                            class="px-3 py-1 bg-green-50 dark:bg-green-900/10 rounded-lg inline-block border border-green-100 dark:border-green-800/30">
                                            <span
                                                class="text-[9px] font-black uppercase text-green-600 dark:text-green-400 mr-2">Legalizado:</span>
                                            <span
                                                class="text-sm font-black text-green-700 dark:text-green-300 tabular-nums">
                                                {{ formatPesosCol(cons.valor_legalizacion) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>

            <!-- Footer -->
            <div
                class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex justify-end">
                <SecondaryButton @click="emit('close')"
                    class="hover:bg-gray-200 dark:hover:bg-gray-700 transition-all font-bold text-xs uppercase tracking-widest px-8">
                    Cerrar Detalle
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #334155;
}

@keyframes slide-in-from-bottom-2 {
    0% {
        transform: translateY(0.5rem);
        opacity: 0;
    }

    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-in {
    animation-duration: 400ms;
    animation-fill-mode: forwards;
}
</style>
