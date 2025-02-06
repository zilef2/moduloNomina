<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    thecolor: {
        type: String,
        default: 'blue',
    },
});
let colorclass
if(props.thecolor === 'blue')
    colorclass = "inline-flex items-center px-2 py-1.5 bg-blue-600" +
    " border border-transparent font-semibold text-xs text-white uppercase" +
    " tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none" +
    " focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" +
    " dark:focus:ring-offset-gray-800 transition ease-in-out duration-150";
else if(props.thecolor === 'green')
    colorclass = "inline-flex items-center px-2 py-1.5 bg-green-600" +
    " border border-transparent font-semibold text-xs text-white uppercase" +
    " tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none" +
    " focus:ring-2 focus:ring-green-500 focus:ring-offset-2" +
    " dark:focus:ring-offset-gray-800 transition ease-in-out duration-150";
else
    colorclass = "inline-flex items-center px-2 py-1.5 bg-"+props.thecolor+"-600" +
    " border border-transparent font-semibold text-xs text-white uppercase" +
    " tracking-widest hover:bg-"+props.thecolor+"-500 active:bg-"+props.thecolor+"-700 focus:outline-none" +
    " focus:ring-2 focus:ring-"+props.thecolor+"-500 focus:ring-offset-2" +
    " dark:focus:ring-offset-gray-800 transition ease-in-out duration-150";
</script>

<template>
    <button
        :type="props.type"
        :class="colorclass"
    >
        <slot />
    </button>
</template>
