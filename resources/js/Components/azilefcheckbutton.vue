<script setup>
import {computed} from 'vue';



const emit = defineEmits(['update:checked']);
const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
});
const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <!-- From Uiverse.io by njesenberger -->
    <div class="toggle-wrapper">
        <p class="text-lg font-sans mx-2">Cotizacion AIU </p>
        <input
            :value="value"
            v-model="proxyChecked"
            type="checkbox"
            class="toggle-checkbox">
        <div class="toggle-container">
            <div class="toggle-button">
                <div class="toggle-button-circles-container">
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                    <div class="toggle-button-circle"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* From Uiverse.io by njesenberger */
.toggle-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    border-radius: .5em;
    padding: .125em;
    background-image: linear-gradient(to bottom, #d5d5d5, #e8e8e8);
    box-shadow: 0 1px 1px rgb(255 255 255 / .6);
    /* resize for demo */
    font-size: 1.5em;
}

.toggle-checkbox {
    appearance: none;
    position: absolute;
    z-index: 1;
    border-radius: inherit;
    width: 100%;
    height: 100%;
    /* fix em sizing */
    font: inherit;
    opacity: 0;
    cursor: pointer;
}

.toggle-container {
    display: flex;
    align-items: center;
    position: relative;
    border-radius: .375em;
    width: 3em;
    height: 1.5em;
    background-color: #e8e8e8;
    box-shadow: inset 0 0 .0625em .125em rgb(255 255 255 / .2), inset 0 .0625em .125em rgb(0 0 0 / .4);
    transition: background-color .4s linear;
}

.toggle-checkbox:checked + .toggle-container {
    background-color: #f3b519;
}

.toggle-button {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    left: .0625em;
    border-radius: .3125em;
    width: 1.375em;
    height: 1.375em;
    background-color: #e8e8e8;
    box-shadow: inset 0 -.0625em .0625em .125em rgb(0 0 0 / .1), inset 0 -.125em .0625em rgb(0 0 0 / .2), inset 0 .1875em .0625em rgb(255 255 255 / .3), 0 .125em .125em rgb(0 0 0 / .5);
    transition: left .4s;
}

.toggle-checkbox:checked + .toggle-container > .toggle-button {
    left: 1.5625em;
}

.toggle-button-circles-container {
    display: grid;
    grid-template-columns: repeat(3, min-content);
    gap: .125em;
    position: absolute;
    margin: 0 auto;
}

.toggle-button-circle {
    border-radius: 50%;
    width: .125em;
    height: .125em;
    background-image: radial-gradient(circle at 50% 0, #f5f5f5, #c4c4c4);
}
</style>