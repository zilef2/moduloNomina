<template>
  <div class="border-0 border-gray-200 rounded-lg">
    <InputLabel :for="ellabel" :value="ellabel"/>
    <input class="bg-gray-50 dark:bg-gray-700 mt-1 w-full no-outline"
           :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)" ref="input" />

    <!-- TODO: como traer el form, para mostrar el error en inputerror-->
    <InputError class="mt-2" :message="elerror"/>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";

defineProps({
    elerror: String,
    ellabel: String,
    error: {type: String, default: null},
    modelValue: String,
});
defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});


defineExpose({ focus: () => input.value.focus() });
</script>
