<script setup>
import {BanknotesIcon, CheckBadgeIcon, PresentationChartLineIcon, ShieldCheckIcon,} from "@heroicons/vue/24/solid";


import {Link} from '@inertiajs/vue3';
import {reactive} from 'vue';

const data = reactive({
    showContent: false,
    showContent2: true,
    showContent3: false,
    showContent4: true,
})
const toggleContent = () => data.showContent = !data.showContent
const toggleContent2 = () => data.showContent2 = !data.showContent2
const toggleContent3 = () => data.showContent3 = !data.showContent3
const toggleContent4 = () => data.showContent4 = !data.showContent4

const ButtonsConfig = [ //SAME AS WEB.PHP
    'Parametros',
    'ubicacion',
	  'desarrollo',
    // 'pagodesarrollo',
];
const ButtonsAdministrativo = [ //SAME AS WEB.PHP
    'user',
    'role',
    'cotizacion',
    // 'viatico',
	// 'consignarViatico',
	'material',
	'zona',
	// 'legalizacionviatico',
	'solicitud_viatico',
	// 'peusuario',
	//aquipuesSide
];
const ButtonsInformes = [ //SAME AS WEB.PHP
    // 'deuda',
];

</script>
<template>
    <div class="text-gray-300 pt-5 pb-12">
        <div class="flex justify-center">
            <div
                class="rounded-full flex items-center justify-center bg-primary text-gray-300 w-20 h-20 text-2xl uppercase">
                <!-- imagen del nombre -->
                {{ $page.props.auth.user.name.match(/(^\S\S?|\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("") }}
            </div>
        </div>
        <div class="text-center py-3 px-4 border-b border-gray-700 dark:border-gray-800">
            <span class="flex items-center justify-center">
                <p class="truncate text-md">{{ $page.props.auth.user.name }}</p>
                <div>
                    <CheckBadgeIcon class="ml-[2px] w-4 h-4" v-show="$page.props.auth.user.email_verified_at"/>
                </div>
            </span>
            <span class="block text-sm font-medium truncate">{{ $page.props.auth.user.roles[0].name }}</span>
        </div>
        <ul class="space-y-2 my-4">
            <!--            <li class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"-->
            <!--                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('dashboard') }">-->
            <!--                <Link :href="route('dashboard')" class="flex items-center py-2 px-4">-->
            <!--                    <HomeIcon class="w-6 h-5" />-->
            <!--                    <span class="ml-3">Tablero principal</span>-->
            <!--                </Link>-->
            <!--            </li>-->
            <!--            <li v-show="can(['read user'])" class="py-2">-->
            <!--                <p>{{ lang().label.data }}</p>-->
            <!--            </li>-->

            <!--            <li v-show="can(['read user'])"-->
            <!--                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"-->
            <!--                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('user.index') }">-->
            <!--                <Link :href="route('user.index')" class="flex items-center py-2 px-4">-->
            <!--                    <UserIcon class="w-6 h-5" />-->
            <!--                    <span class="ml-3">{{ lang().label.user }}</span>-->
            <!--                </Link>-->
            <!--            </li>-->

            <li v-show="can(['isSuper'])" class="py-2">
                <p>{{ lang().label.access }}</p>
            </li>
            <li v-show="can(['isSuper'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('permission.index') }">
                <Link :href="route('permission.index')" class="flex items-center py-2 px-4">
                    <ShieldCheckIcon class="w-6 h-5"/>
                    <span class="ml-3">{{ lang().label.permission }} <br>solo super</span>
                </Link>
            </li>
            <li v-show="can(['read centroCostos'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('CentroCostos.index') }">
                <Link :href="route('CentroCostos.index')" class="flex items-center py-2 px-4">
                    <PresentationChartLineIcon class="w-6 h-5"/>
                    <span class="ml-3">{{ lang().label.CentroCostos }}</span>
                </Link>
            </li>
            <li v-show="can(['read reporte'])"
                class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                :class="{ 'bg-sky-600 dark:bg-sky-600': route().current('Reportes.index') }">
                <Link :href="route('Reportes.index')" class="flex items-center py-2 px-4">
                    <BanknotesIcon class="w-6 h-5"/>
                    <span class="ml-3">{{ lang().label.Reportes }}</span>
                </Link>
            </li>

        </ul>
        <button @click="toggleContent2" v-show="can(['isAdmin','isadministrativo','issupervisor'])"
                class="text-blue-500">{{ (data.showContent2 ? '' : 'Ver ') + 'Administrativo' }}
        </button>
        
        <ul v-if="data.showContent2" v-show="can(['isAdmin','isadministrativo','issupervisor'])" 
            class="space-y-1 my-1">
            <div class="" v-for="value in ButtonsAdministrativo">
                <li class="text-white rounded-lg hover:bg-primary"
                    :class="route().current(value + '.index') ? 'bg-primary' : 'bg-gray-700'">
                    <Link :href="route(value+'.index')" class="flex items-center py-2 px-4">
                        <PresentationChartLineIcon class="w-5 h-auto"/>
                        <span class="ml-3">{{ lang().side[value] }}</span>
                    </Link>
                </li>
            </div>
            <div class="">
                <li class="text-white rounded-lg hover:bg-primary"
                    :class="route().current('peusuario.index') ? 'bg-primary' : 'bg-gray-700'">
                    <Link :href="route('peusuario.index')" class="flex items-center py-2 px-4">
                        <PresentationChartLineIcon class="w-5 h-auto"/>
                        <span class="ml-3">Empresas y clientes</span>
                    </Link>
                </li>
            </div>
        </ul>
        <button @click="toggleContent3" v-show="can(['isAdmin', 'isadministrativo'])" class="mt-1 text-blue-400">
            {{ (data.showContent3 ? '' : 'Ver ') + 'Administrador' }}
        </button>
        <ul v-if="data.showContent3" v-show="can((['isAdmin', 'isadministrativo']))" class="space-y-2 my-1">
            <div class="" v-for="value in ButtonsConfig">
                <li v-show="can(['isAdmin','isadministrativo'])"
                    class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"
                    :class="{ 'bg-blue-900 dark:bg-blue-900': route().current(value+'.index') }">
                    <Link :href="route(value+'.index')" class="flex items-center py-2 px-4">
                        <PresentationChartLineIcon class="w-6 h-5"/>
                        <span class="ml-3">{{ lang().label[value] }}</span>
                    </Link>
                </li>
            </div>
        </ul>
<!--        <div class="mt-1">-->
<!--            <button @click="toggleContent4" v-show="can(['isAdmin'])" class=" text-blue-400">-->
<!--                {{ (data.showContent4 ? '' : 'Ver ') + 'Informes' }}-->
<!--            </button>-->
<!--            <ul v-if="data.showContent4" v-show="can((['isAdmin']))" class="space-y-2 my-1">-->
<!--                <div class="" v-for="value in ButtonsInformes">-->
<!--                    <li class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"-->
<!--                        :class="{ 'bg-red-900 dark:bg-red-900': route().current(value+'.index') }">-->
<!--                        <Link :href="route(value+'.index')" class="flex items-center py-2 px-4">-->
<!--                            <PresentationChartLineIcon class="w-6 h-5"/>-->
<!--                            <span class="ml-3">{{ lang().label[value] }}</span>-->
<!--                        </Link>-->
<!--                    </li>-->
<!--                </div>-->
<!--            </ul>-->
<!--        </div>-->
        
        <div class="mt-1">
<!--            <ul v-if="can(['isAdmin'])" class="space-y-2 my-1">-->
<!--                <div class="" -->
<!--                >-->
<!--                    <li class="bg-gray-700/40 dark:bg-gray-800/40 text-white rounded-lg hover:bg-primary dark:hover:bg-primary"-->
<!--                        :class="{ 'bg-red-900 dark:bg-red-900': route().current('viatico2') }">-->
<!--                        <Link :href="route('viatico2')" class="flex items-center py-2 px-4">-->
<!--                            <PresentationChartLineIcon class="w-6 h-5"/>-->
<!--                            <span class="ml-3"> Pendientes </span>-->
<!--                        </Link>-->
<!--                    </li>-->
<!--                </div>-->
<!--            </ul>-->
        </div>
    </div>
</template>
