<script setup>
import pkg from 'lodash';
import {reactive, watch,computed} from 'vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import SelectInput from '@/Components/SelectInput.vue';

const {_, debounce, pickBy} = pkg

const props = defineProps({
    filters: Object,
    search: String,
    perPage: Number,
    numberPermissions: Number,
    
})
const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    dataSet: usePage().props.app.perpage,
})

// const emit = defineEmits(["close"]);

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("viatico.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            data.params.perPage = props.filters.perPage
            data.params.search = props.filters.search

        },

    })
}, 150))

</script>

<template>
    <div class="flex justify-between p-2">
        <div class="flex space-x-2">
            <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"/>
            <!-- <DangerButton @click="data.deleteBulkOpen = true"
                v-show="data.selectedId.length != 0 && can(['delete viatico'])" class="px-3 py-1.5"
                v-tooltip="lang().tooltip.delete_selected">
                <TrashIcon class="w-5 h-5" />
            </DangerButton> -->
        </div>
        <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                   class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Nombre, codigo"/>
    </div>
</template>
