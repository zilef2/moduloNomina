<script setup>
// import Breadcrumb from '@/Components/Breadcrumb.vue';
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import {ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon, BanknotesIcon} from '@heroicons/vue/24/solid';
// import {Head, Link} from '@inertiajs/vue3';
//
// import {Bar} from 'vue-chartjs'
// import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'
// import {watchEffect, onMounted, defineAsyncComponent, Suspense, reactive, ref} from "vue";


</script>

<template>

    <header class="p-6 flex justify-between items-center w-full max-w-7xl mx-auto">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-md"></div> <span class="font-bold text-xl text-indigo-900">Proton Mail</span>
        </div>
        <button class="text-sm font-medium flex items-center gap-2 text-gray-600">
            <i class="fa-solid fa-globe"></i> Español (España) <i class="fa-solid fa-chevron-down text-xs"></i>
        </button>
    </header>

    <main class="flex-grow flex flex-col items-center px-4 pt-10">
        <h1 class="text-4xl font-bold text-center text-indigo-950 mb-4">
            Un servicio de correo electrónico cifrado <br> que pone tu privacidad de primero
        </h1>

        <div class="mt-12 w-full max-w-4xl flex flex-col md:flex-row gap-12">
            
            <div class="flex-1">
                <h2 class="text-xl font-bold mb-6">Crea tu cuenta de Proton</h2>
                
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nombre de usuario</label>
                        <div class="relative">
                            <input type="text" placeholder="nombre" class="w-full border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                            <span class="absolute right-10 top-2 text-gray-500">@proton.me</span>
                            <i class="fa-solid fa-circle-check absolute right-3 top-3 text-green-500"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Contraseña</label>
                        <div class="relative">
                            <input type="password" value="12345678" class="w-full border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                            <i class="fa-regular fa-eye absolute right-3 top-3 text-gray-400 cursor-pointer"></i>
                        </div>
                    </div>

                    <div class="relative">
                        <input type="password" value="12345678" class="w-full border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <i class="fa-regular fa-eye absolute right-3 top-3 text-gray-400 cursor-pointer"></i>
                    </div>

                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-full transition-colors flex justify-center items-center gap-2">
                        Comenzar a utilizar Proton Mail 
                        <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>

                    <p class="text-center text-sm text-gray-600">
                        ¿Ya tienes una cuenta? <a href="#" class="text-indigo-600 hover:underline">Iniciar sesión</a>
                    </p>

                    <p class="text-[10px] text-center text-gray-400 mt-4 px-10">
                        Al continuar, aceptas nuestros <a href="#" class="underline">términos y condiciones</a> y <a href="#" class="underline">política de privacidad</a>.
                    </p>
                </form>
            </div>

            <div class="flex-1 bg-blue-50/50 border border-blue-100 rounded-2xl p-6 text-sm">
                <h3 class="font-bold text-gray-800 mb-4">El único servicio de correo electrónico con:</h3>
                
                <ul class="space-y-4 text-gray-600">
                    <li class="flex gap-3">
                        <i class="fa-solid fa-lock mt-1 text-gray-400"></i>
                        <span>Cifrado de serie</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-shield-halved mt-1 text-gray-400"></i>
                        <span>Bajo la protección de las leyes de privacidad suizas</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-envelope-open-text mt-1 text-gray-400"></i>
                        <span>Alias de correo</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-sliders mt-1 text-gray-400"></i>
                        <span>Características de seguridad avanzadas</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-table-cells-large mt-1 text-gray-400"></i>
                        <span>Incluye calendario, gestor de contraseñas, almacenamiento de archivos y VPN</span>
                    </li>
                </ul>

                <p class="mt-8 font-medium text-gray-700 leading-tight">
                    Únete a más de 100 millones de personas que han elegido Proton para tu seguridad online
                </p>
            </div>
        </div>
    </main>

    <footer class="p-8 w-full max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-4">
        <div class="flex gap-4">
            <i class="fa-solid fa-envelope"></i>
            <i class="fa-solid fa-calendar"></i>
            <i class="fa-solid fa-cloud"></i>
            <i class="fa-solid fa-key"></i>
            <i class="fa-solid fa-shield"></i>
        </div>
        <div class="flex gap-4">
            <span>Proton, Privacy by default.</span>
            <a href="#" class="text-indigo-600 underline">¿Necesitas ayuda?</a>
        </div>
    </footer>

</template>
