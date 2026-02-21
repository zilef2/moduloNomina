<script setup>

import { useForm } from "@inertiajs/vue3";
import { onMounted, reactive } from "vue";

const data = reactive({
    // Ya no necesitamos guardar la ciudad localmente
})
const form = useForm({});

const props = defineProps({
    userid: Number
})


const enviarCiudad = () => {
    // El backend ahora detecta la ciudad automáticamente usando geoip
    form.post(route('guardarCiudad'), {
        preserveScroll: true,
        onSuccess: () => {
            // Guardar el timestamp del último registro exitoso
            localStorage.setItem('lastCityCheck', Date.now().toString());
            console.log('Ubicación actualizada correctamente.');
        },
        onError: (err) => console.error('Error al guardar ubicación:', err)
    })
};

onMounted(() => {
    // Solo registrar en el dashboard (opcional, según tu código anterior)
    if (route().current('dashboard')) {
        const lastCheck = localStorage.getItem('lastCityCheck');
        const twelveHours = 12 * 60 * 60 * 1000; //12 horas
        const now = Date.now();

        if (!lastCheck || (now - parseInt(lastCheck)) > twelveHours) {
            enviarCiudad();
        } else {
            const hoursLeft = Math.round((twelveHours - (now - parseInt(lastCheck))) / (60 * 60 * 1000));
            console.log(`Ubicación ya registrada. Próximo intento en aprox. ${hoursLeft}h`);
        }
    }
});
</script>
<template>
    <footer class="text-gray-500 dark:text-gray-300 sticky top-full">
        <div class="flex items-center justify-center sm:justify-end w-full xs:py-1 sm:py-4 lg:py-12 px-6 sm:px-12">
            <p class="text-center">
                <a href="" class="font-bold text-primary">
                    <!--                    {{ $page.props.app.name }}-->
                </a> ©️ {{ new Date().getFullYear() }}
                <a href="" target="_blank" class="font-bold text-primary">Ec Ingenieria Electrica S.A.S.</a>
            </p>
            <!--            <div v-if="route().current('dashboard')">-->
            <!--                <p class="text-center mx-4"> Repontandose desde <b>{{ data?.thecity }}</b></p>-->
            <!--            </div>-->
        </div>
    </footer>
</template>
