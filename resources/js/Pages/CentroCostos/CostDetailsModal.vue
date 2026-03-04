<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import { formatPesosCol } from '@/global.ts';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    centroCosto: Object,
});

const emit = defineEmits(['close']);

const costs = ref(null);
const loading = ref(false);

watch(() => props.show, (show) => {
    if (show && props.centroCosto) {
        loading.value = true;
        axios.get(route('centro-costos.cost-details', { id: props.centroCosto.id }))
            .then(response => {
                costs.value = response.data;
            })
            .catch(error => {
                console.error('Error fetching cost details:', error);
            })
            .finally(() => {
                loading.value = false;
            });
    }
});

const closeModal = () => {
    emit('close');
    costs.value = null;
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="xl4">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Detalles de Costos: {{ centroCosto?.nombre }}
            </h2>

            <div v-if="loading" class="mt-6 text-center">
                <svg class="animate-spin h-8 w-8 text-amber-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Cargando detalles...</p>
            </div>

            <div v-if="costs && !loading" class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg text-center">
                    <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Mano de Obra</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ formatPesosCol(costs.mano_de_obra) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg text-center">
                    <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Viáticos</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ formatPesosCol(costs.viaticos) }}</p>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 p-6 rounded-lg text-center border border-amber-200 dark:border-amber-800">
                    <h3 class="text-lg font-medium text-amber-600 dark:text-amber-400">Total General</h3>
                    <p class="mt-2 text-3xl font-bold text-amber-700 dark:text-amber-300">{{ formatPesosCol(costs.total) }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                    Cerrar
                </button>
            </div>
        </div>
    </Modal>
</template>
