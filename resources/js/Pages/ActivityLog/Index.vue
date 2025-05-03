<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {ChevronDownIcon, ChevronUpIcon, MagnifyingGlassIcon, FunnelIcon} from '@heroicons/vue/24/outline';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import {watchEffect, reactive, watch, ref, onMounted} from 'vue';
import {debounce} from 'lodash'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

const props = defineProps({
    activities: Array,
    filters: Object,
    importantActions: Array,
    breadcrumbs: Object,
    methodusers: Array,
    title: String,
});

const data = reactive({
    activities: '',
    userImportado: '',
    selectedDate: '',
})

onMounted(async () => {
    data.activities = props.activities
})

const expandedItems = ref([]);

const toggleExpand = (index) => {
    const position = expandedItems.value.indexOf(index);
    if (position === -1) {
        expandedItems.value.push(index);
    } else {
        expandedItems.value.splice(position, 1);
    }
};

const isExpanded = (index) => {
    return expandedItems.value.includes(index);
};
const actionColors = {
    login: 'bg-green-100 text-green-800',
    logout: 'bg-gray-100 text-gray-800',
    store: 'bg-blue-100 text-blue-800',
    update: 'bg-yellow-100 text-yellow-800',
    delete: 'bg-red-100 text-red-800',
    approved: 'bg-purple-100 text-purple-800',
    rejected: 'bg-pink-100 text-pink-800',
    report: 'bg-indigo-100 text-indigo-800',
    other: 'bg-gray-100 text-gray-800',
};

// Nuevas variables reactivas
let suggestedUsers = ref([]);
let showSuggestions = ref(false);

// Seleccionar un usuario de las sugerencias
const selectUser = (user) => {
    data.userSearch.value = user;
    search.value = user; // Actualiza el filtro principal
    showSuggestions.value = false;
};

// Limpiar búsqueda
const clearUserSearch = () => {
    data.userSearch.value = '';
    search.value = '';
    suggestedUsers.value = [];
    showSuggestions.value = false;
};

const debouncedSearch = debounce((newUserSearch) => {
    if (newUserSearch) {
        data.activities = props.activities.filter(activity =>
            activity.user.toLowerCase().includes(newUserSearch.toLowerCase())
        );
    } else {
        data.activities = props.activities;
    }
}, 300);

let normalizeDate = (dateStr) => {
    let date = new Date(dateStr);
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
};
const parseLocalDate = (isoDateStr) => {
  const [year, month, day] = isoDateStr.split('-').map(Number);
  return new Date(year, month - 1, day); // mes es base 0
};
const debouncedSearchDate = debounce((newSearchDate) => {
    if (newSearchDate) {
        data.activities = props.activities.filter(activity => {

                const SearchDate = parseLocalDate(newSearchDate);
                console.log("=>(Index.vue:95) newSearchDate", newSearchDate);
                console.log("=>(Index.vue:95) SearchDate", SearchDate);
                
                const activityDate = normalizeDate(activity.date);
                console.log("=>(Index.vue:96) activityDate", activityDate);

                let isSame = SearchDate.getTime() === activityDate.getTime();
                console.log("=>(Index.vue:100) isSame", isSame);
                return isSame
            }
        );
    } else {
        data.activities = props.activities;
    }
}, 30);

watch(() => data.userSearch, (newUserSearch) => debouncedSearch(newUserSearch));

watch(() => data.userImportado, (newUserSearch) => debouncedSearch(newUserSearch.label));
watch(() => data.selectedDate, (newDateselected) => debouncedSearchDate(newDateselected));

</script>

<template>
    <Head :title="title"/>

    <AuthenticatedLayout>
        <!--        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>-->

        <div class="py-6 px-4 sm:px-6 lg:px-8 bg-gray-200">
            <div class="bg-gray-100 dark:bg-gray-800 shadow sm:rounded-lg">
                <!-- Filtros -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="relative">
                            <TextInput
                                v-model="data.userSearch"
                                class="w-full"
                                type="search"
                            >
                            </TextInput>
                        </div>

                        <TextInput
                            v-model="data.selectedDate"
                            type="date"
                            class="w-full"
                            placeholder="Filtrar por fecha..."
                        />
                        <!--                        {{ props.methodusers['User'] }}-->
                        <vSelect v-model="data.userImportado" :options="props.methodusers['User']" label="label"
                                 class="col-span-2"></vSelect>
                        <DangerButton @click="clearFilters" class="h-full w-32 mx-auto px-3">
                            Limpiar filtros
                        </DangerButton>
                    </div>
                </div>

                <!-- Lista de actividades -->
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div v-if="props.activities.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
                        No se encontraron registros de actividad con los filtros actuales.
                    </div>

                    <div v-for="(activity, index) in data.activities" :key="index"
                         class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="grid grid-cols-5 gap-1 items-center justify-between cursor-pointer"
                             @click="toggleExpand(index)">
                            <div class="flex items-center space-x-4 mx-2">
                                <span class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full"
                                          :class="actionColors[activity.action.type]">
                                        <FunnelIcon :name="activity.action.icon" class="h-5 w-5"/>
                                    </span>
                                </span>

                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ activity.user }}
                                        <span class="text-xs text-gray-500">({{ activity.role }})</span>
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <template v-if="activity.details.report_action">
                                            {{ activity.details.report_action }}
                                        </template>
                                        <template v-else-if="activity.details.model_type">
                                            {{ activity.action.description }} ({{ activity.details.model_type }} ID:
                                            {{ activity.details.model_id }})
                                        </template>
                                        <template v-else>
                                            {{ activity.action.description }}
                                        </template>
                                    </p>
                                </div>
                            </div>

                            <div class="col-span-3">
                                <pre v-if="activity.raw.length > 80"
                                     class="text-gray-800 dark:text-gray-300 whitespace-pre-wrap">{{
                                        ((activity.raw).slice(6, 80) + ' ...')
                                    }}</pre>
                                <pre v-else
                                     class="text-xs text-gray-800 dark:text-gray-300 whitespace-pre-wrap">{{
                                        (activity.raw).slice(0, 80)
                                    }}</pre>
                            </div>
                            <div class="flex items-center space-x-4 ml-auto ">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ activity.date }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ activity.time }}
                                    </p>
                                </div>

                                <span class="text-gray-400">
                                    <ChevronDownIcon v-if="!isExpanded(index)" class="h-5 w-5"/>
                                    <ChevronUpIcon v-else class="h-5 w-5"/>
                                </span>
                            </div>
                        </div>

                        <!-- Detalle expandido -->
                        <div v-if="isExpanded(index)" class="mt-4 pl-12">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                <pre class="text-xs text-gray-800 dark:text-gray-300 whitespace-pre-wrap">{{
                                        activity.raw
                                    }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>