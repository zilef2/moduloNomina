<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watchEffect, reactive, ref, watch } from 'vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'
import { CheckCircleIcon, UserIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    show: Boolean,
    title: String,
    CentroCosto: Object,
    listaSupervisores: Object,
    losSelect: Array,
})

const emit = defineEmits(["close"]);

const data = reactive({
    SelecSupervisores: []
})

const form = useForm({
    nombre: '',
    descripcion: '',
    clasificacion: '',
    activo: true,
    selectedUsers: {}, // Cambiado a objeto para mapear ID -> Boolean
    listaSupervisores: [],
    ValidoParaFacturar: true,
    zona_id: null,
});

// Preparar lista de supervisores para renderizar
const prepareSupervisorsList = () => {
    if (props.listaSupervisores) {
        data.SelecSupervisores = props.listaSupervisores.map(user => ({
            label: user.name,
            value: user.id
        }));
    }
};

watchEffect(() => {
    if (props.show) {
        form.clearErrors();
        prepareSupervisorsList();

        form.nombre = props.CentroCosto?.nombre || '';
        form.descripcion = props.CentroCosto?.descripcion || '';
        form.clasificacion = props.CentroCosto?.clasificacion || '';
        form.activo = !!props.CentroCosto?.activo;
        form.ValidoParaFacturar = !!props.CentroCosto?.ValidoParaFacturar;

        // Resetear seleccionados
        form.selectedUsers = {};

        // Marcar los supervisores actuales
        if (props.CentroCosto?.users) {
            props.CentroCosto.users.forEach(user => {
                form.selectedUsers[user.id] = true;
            });
        }

        // Manejo del Select de Zona
        if (props.CentroCosto?.zona_id) {
            // Buscamos el objeto completo en losSelect para que v-select lo muestre correctamente
            const zonaEncontrada = props.losSelect['zona']?.find(z => z.value === props.CentroCosto.zona_id);
            form.zona_id = zonaEncontrada || { "value": props.CentroCosto.zona_id, "label": props.CentroCosto.Zouna };
        } else {
            form.zona_id = null;
        }
    }
})

const update = () => {
    form.listaSupervisores = props.listaSupervisores; // Necesario para el controlador actual
    form.put(route('CentroCostos.update', props.CentroCosto?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
            window.location.reload()

        },
        onError: () => {
            // Opcional: Vibrar o feedback visual extra
        },
        onFinish: () => null,
    })
}
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'xl3'">
            <form class="p-6 bg-white dark:bg-gray-800 rounded-lg transition-all duration-300" @submit.prevent="update">

                <!-- Header -->
                <div class="mb-6 pb-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </span>
                        {{ lang().label.edit }} {{ props.title }}
                    </h2>
                    <div class="text-xs text-gray-500 font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                        ID: {{ props.CentroCosto?.id }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="col-span-2 md:col-span-1">
                        <InputLabel for="nombre" :value="lang().label.name" />
                        <TextInput id="nombre" type="text"
                                   class="mt-1 block w-full transition-shadow focus:ring-2 focus:ring-amber-500/50"
                                   v-model="form.nombre"
                                   :placeholder="lang().placeholder.nombre"
                                   :error="form.errors.nombre" />
                        <InputError :message="form.errors.nombre" class="mt-1" />
                    </div>

                    <!-- Clasificación -->
                    <div class="col-span-2 md:col-span-1">
                        <InputLabel for="clasificacion" :value="lang().label.clasificacion" />
                        <TextInput id="clasificacion" type="text"
                                   class="mt-1 block w-full transition-shadow focus:ring-2 focus:ring-amber-500/50"
                                   v-model="form.clasificacion"
                                   :placeholder="lang().placeholder.clasificacion"
                                   :error="form.errors.clasificacion" />
                         <InputError :message="form.errors.clasificacion" class="mt-1" />
                    </div>

                    <!-- Zona -->
                    <div class="">
                        <InputLabel value="Zona Geográfica" />
                        <vSelect
                            v-model="form.zona_id"
                            :options="losSelect['zona']"
                            label="label"
                            class="mt-1 custom-select-style"
                            placeholder="Seleccione una zona..."
                        >
                            <template #open-indicator="{ attributes }">
                                <span v-bind="attributes">▼</span>
                            </template>
                        </vSelect>
                    </div>

                    <!-- Descripción -->
                    <div class="">
                        <InputLabel for="descripcion" :value="lang().label.descripcion" />
                        <textarea
                            id="descripcion"
                            v-model="form.descripcion"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"
                            rows="2"
                            :placeholder="lang().placeholder.descripcion"
                        ></textarea>
                        <InputError :message="form.errors.descripcion" class="mt-1" />
                    </div>

                    <!-- Supervisores (Lista con Scroll) -->
                    <div class="col-span-2">
                        <InputLabel :value="lang().label.supervisores" class="mb-2" />
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-500 uppercase">
                                Seleccionar encargados
                            </div>
                            <div class="max-h-48 overflow-y-auto p-2 space-y-1 bg-white dark:bg-gray-900 scrollbar-thin">
                                <label
                                    v-for="(user, index) in data.SelecSupervisores"
                                    :key="user.value"
                                    class="flex items-center p-2 rounded-lg cursor-pointer transition-colors hover:bg-amber-50 dark:hover:bg-amber-900/10 group"
                                    :class="{'bg-amber-50/50 dark:bg-amber-900/20': form.selectedUsers[user.value]}"
                                >
                                    <div class="relative flex items-center">
                                        <input
                                            type="checkbox"
                                            :value="user.value"
                                            v-model="form.selectedUsers[user.value]"
                                            class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600"
                                        />
                                    </div>
                                    <UserIcon class="w-4 h-4 mx-3 text-gray-400 group-hover:text-amber-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white select-none">
                                        {{ user.label }}
                                    </span>
                                </label>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 text-right">{{ Object.keys(form.selectedUsers).filter(k => form.selectedUsers[k]).length }} seleccionados</p>
                    </div>

                    <!-- Toggles (Headless UI) -->
                    <div class="col-span-2 grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <!-- Activo Switch -->
                        <SwitchGroup>
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 p-3 rounded-xl">
                                <SwitchLabel class="mr-4 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                    {{ lang().label.activo }}
                                </SwitchLabel>
                                <Switch
                                    v-model="form.activo"
                                    :class="form.activo ? 'bg-amber-600' : 'bg-gray-200 dark:bg-gray-600'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2"
                                >
                                    <span
                                        :class="form.activo ? 'translate-x-6' : 'translate-x-1'"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                    />
                                </Switch>
                            </div>
                        </SwitchGroup>

                        <!-- Facturable Switch -->
                        <SwitchGroup>
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 p-3 rounded-xl">
                                <SwitchLabel class="mr-4 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                    {{ lang().label.ValidoParaFacturar }}
                                </SwitchLabel>
                                <Switch
                                    v-model="form.ValidoParaFacturar"
                                    :class="form.ValidoParaFacturar ? 'bg-amber-500' : 'bg-gray-200 dark:bg-gray-600'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2
                                     focus:ring-amber-500 focus:ring-offset-2"
                                >
                                    <span
                                        :class="form.ValidoParaFacturar ? 'translate-x-6' : 'translate-x-1'"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                    />
                                </Switch>
                            </div>
                        </SwitchGroup>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-8 flex justify-end gap-3">
                <p v-show="!form.ValidoParaFacturar" class="text-center mt-2 text-xs text-red-500"> No habran horas extras </p>
                    <SecondaryButton :disabled="form.processing" @click="emit('close')">
                        {{ lang().button.close }}
                    </SecondaryButton>

                    <PrimaryButton
                        class="relative pl-8 pr-8 transition-all duration-200 ease-in-out"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing, 'hover:scale-105 shadow-lg shadow-amber-500/30': !form.processing }"
                        :disabled="form.processing"
                        @click="update"
                    >
                        <!-- Loading Spinner -->
                        <div v-if="form.processing" class="absolute left-3 top-1/2 -translate-y-1/2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <span :class="{'ml-2': form.processing}">
                            {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                        </span>
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
.dark .scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: #475569;
}
</style>
