<script setup>
import {computed, ref} from 'vue';

const props = defineProps({
    numberPermissions:Number
})

const emit = defineEmits(['update:checked']);

const checkedValues = ref([false, false,false]);

const handleChange = () => {
  emit('update:checked', checkedValues.value);
};

</script>

<template>
    <ul class="example-2">
      <li class="icon-content">
          <label class="container">
            <input type="checkbox" v-model="checkedValues[0]" @change="handleChange">
            <div class="checkmark"></div>
            </label>
        <div class="tooltip">Solo validos</div>
      </li>
      <li class="icon-content">
          <label class="container">
            <input type="checkbox" v-model="checkedValues[1]" @change="handleChange">
            <div class="checkmark"></div>
            </label>
        <div class="tooltip">Esta quincena</div>
      </li>
      <li class="icon-content" v-show="props.numberPermissions > 1">
          <label class="container">
            <input type="checkbox" v-model="checkedValues[2]" @change="handleChange" class="bg-red-300">
            <div class="checkmark"></div>
            </label>
<!--        <div class="tooltip">Siigo</div>-->
        <div class="tooltip">(HD + HN) No son las H trabajadas</div>
      </li>
      <li class="icon-content" v-show="props.numberPermissions > 1">
          <label class="container">
            <input type="checkbox" v-model="checkedValues[3]" @change="handleChange" class="bg-red-300">
            <div class="checkmark"></div>
            </label>
        <div class="tooltip">Siigo (solo extras)</div>
<!--        <div class="tooltip">(HD + HN) No son las H trabajadas</div>-->
      </li>
    </ul>
</template>

<style>

/* Hide the default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.container {
  display: block;
  position: relative;
  cursor: pointer;
  font-size: 1.7rem;
  user-select: none;
}

/* Create a custom checkbox */
.checkmark {
  --clr: #0B6E4F;
  position: relative;
  top: 0;
  left: 0;
  height: 1.3em;
  width: 1.3em;
  background-color: #ccc;
  border-radius: 50%;
  transition: 300ms;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: var(--clr);
  border-radius: .5rem;
  animation: pulse 500ms ease-in-out;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 0.45em;
  top: 0.25em;
  width: 0.25em;
  height: 0.5em;
  border: solid #E0E0E2;
  border-width: 0 0.15em 0.15em 0;
  transform: rotate(45deg);
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 #0B6E4F90;
    rotate: 20deg;
  }

  50% {
    rotate: -20deg;
  }

  75% {
    box-shadow: 0 0 0 10px #0B6E4F60;
  }

  100% {
    box-shadow: 0 0 0 13px #0B6E4F30;
    rotate: 0;
  }
}



//divisionn
ul {
  list-style: none;
}

.example-2 {
  display: flex;
  justify-content: center;
  align-items: center;
}
.example-2 .icon-content {
  margin: 0 10px;
  position: relative;
}
.example-2 .icon-content .tooltip {
  position: absolute;
  width: 130px;
  top: -60px;
  left: 50%;
  transform: translateX(-50%);
  color: #fff;
  background: #636363;
  padding: 6px 10px;
  border-radius: 15px;
  opacity: 0;
  visibility: hidden;
  font-size: 16px;
  transition: all 0.2s ease;
}
.example-2 .icon-content:hover .tooltip {
  opacity: 1;
  visibility: visible;
  top: -80px;
}
.example-2 .icon-content a {
  position: relative;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 50px;
  height: 50px;
  border-radius: 20%;
  color: #4d4d4d;
  background-color: #ffff;
  transition: all 0.3s ease-in-out;
}
.example-2 .icon-content a:hover {
  box-shadow: 3px 2px 45px 0px rgb(0 0 0 / 50%);
}
.example-2 .icon-content a svg {
  position: relative;
  z-index: 1;
  width: 30px;
  height: 30px;
}
.example-2 .icon-content a:hover {
  color: white;
}
.example-2 .icon-content a .filled {
  position: absolute;
  top: auto;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 0;
  background-color: #000;
  transition: all 0.3s ease-in-out;
}
.example-2 .icon-content a:hover .filled {
  height: 100%;
}
.example-2 .icon-content a[data-social="spotify"] .filled,
.example-2 .icon-content a[data-social="spotify"] ~ .tooltip {
  background-color: #1db954;
}
.example-2 .icon-content a[data-social="pinterest"] .filled,
.example-2 .icon-content a[data-social="pinterest"] ~ .tooltip {
  background-color: #bd081c;
}
.example-2 .icon-content a[data-social="dribbble"] .filled,
.example-2 .icon-content a[data-social="dribbble"] ~ .tooltip {
  background-color: #ea4c89;
}
.example-2 .icon-content a[data-social="telegram"] .filled,
.example-2 .icon-content a[data-social="telegram"] ~ .tooltip {
  background-color: #0088cc;
}

</style>
