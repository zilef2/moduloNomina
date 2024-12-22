<script setup>

import {useForm} from "@inertiajs/vue3";
import {onMounted, reactive} from "vue";

const data = reactive({
    thecity: String,
})
const form = useForm({
    ciudad: String,
});

const props = defineProps({
    userid: Number
})


const enviarCiudad = () => {
    form.ciudad = data.thecity
    if (data.thecity) {
        form.post(route('guardarCiudad'), {
            preserveScroll: true,
            onFinish: () => {
            }
        })
    } else {
        console.error('sin ciudad:');
    }
};
onMounted(async () => {
    data.thecity = ''
    //aquivieneel nuevo gps
    const apiKey = '16fa63102b054e11a4554f7c9ab9ab41';
    const latitude = 6.2442;
    const longitude = -75.5812;

    fetch(`https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${apiKey}`)
        .then(response => response.json())
        .then(datau => {
            const city = datau.results[0].components.city || datau.results[0].components.town;
            console.log("City:", city); // Resultado: Medellín
            data.thecity = city
            enviarCiudad()
        }).catch(error => console.error('Errorsini:',
        error));
});
</script>
<template>
    <footer
        class="text-gray-500 dark:text-gray-300">
        <div class="flex items-center justify-center sm:justify-end max-w-7xl mx-auto p-4 sm:px-6 lg:px-8">
            <p class="text-center">
                <a href=""
                    class="font-bold text-primary">
<!--                    {{ $page.props.app.name }}-->
                </a> ©️ {{ new Date().getFullYear() }}
                <a href="" target="_blank"
                    class="font-bold text-primary">Ec Ingenieria Electrica S.A.S.</a>
            </p>
            <p class="text-center mx-4"> Repontandose desde <b>{{ data?.thecity }}</b></p>
        </div>
    </footer>
</template>
