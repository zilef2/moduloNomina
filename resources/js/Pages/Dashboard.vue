<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon, BanknotesIcon } from '@heroicons/vue/24/solid';
import { Head, Link } from '@inertiajs/vue3';

import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps({
    users: Number,
    // roles: Number,
    permissions: Number,
    reportes: Number,
    conutSessiones: Number,
    ultimaSesion: Number,
    ultimos5dias: Array,
})


const chartData = {
    name: 'Numero de reportes',
    labels: [ 'Antier', 'Ayer', 'Hoy' ],
    datasets: [ { data: props.ultimos5dias } ]
};
const chartOptions = {
    responsive: true
}

</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <Breadcrumb :title="'Dashboard'" :breadcrumbs="[]" />
        <div class="space-y-4">
            <div
                class="text-white dark:text-gray-100 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 sm:gap-4 overflow-hidden shadow-sm">
                <div>
                    <div class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-sky-600/70 dark:bg-sky-500/80 items-center overflow-hidden">
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.reportes }}</p>
                            <p class="text-md md:text-lg uppercase">{{ lang().label.reporte }}</p>
                        </div>
                        <div> <BanknotesIcon class="w-16 h-auto" /> </div>
                    </div>
                    <div
                        class="bg-blue-600 dark:bg-blue-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-blue-600/90 dark:hover:bg-blue-600/70">
                        <Link :href="route('Reportes.index')" class="flex justify-between items-center">
                        <p>{{ lang().label.more }}</p>
                        <ChevronRightIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
                <div v-show="can(['isAdmin'])">
                    <div class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-blue-700/70 dark:bg-blue-500/80 items-center overflow-hidden">
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.users }}</p>
                            <p class="text-md md:text-lg uppercase">{{ lang().label.user }}</p>
                        </div>
                        <div> <UserIcon class="w-16 h-auto" /> </div>
                    </div>
                    <div 
                        class="bg-blue-600 dark:bg-blue-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-blue-800/90 dark:hover:bg-blue-800/70">
                        <Link :href="route('user.index')" class="flex justify-between items-center">
                        <p>{{ lang().label.more }}</p>
                            <ChevronRightIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
                <div v-show="can(['isSuper'])" >
                    <div class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-blue-700/70 dark:bg-blue-500/80 items-center overflow-hidden">
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.conutSessiones }}</p>
                            <p class="text-md md:text-lg uppercase">{{ lang().label.sessions }}</p>
                        </div>
                        <div> <UserIcon class="w-16 h-auto" /> </div>
                    </div>
                    <div
                        class="bg-blue-600 dark:bg-blue-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-blue-800/90 dark:hover:bg-blue-800/70">
                        <Link :href="route('user.index')" class="flex justify-between items-center">
                        <p>{{ lang().label.more }}</p>
                        <ChevronRightIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
                <div v-show="can(['isSuper'])">
                    <div class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-amber-600/70 dark:bg-amber-500/80 items-center overflow-hidden">
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.permissions }}</p>
                            <p class="text-md md:text-lg uppercase">{{ lang().label.permission }}</p>
                        </div>
                        <div> <ShieldCheckIcon class="w-16 h-auto" /> </div>
                    </div>
                    <div
                        class="bg-amber-600 dark:bg-amber-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-amber-600/90 dark:hover:bg-amber-600/70">
                        <Link :href="route('permission.index')" class="flex justify-between items-center">
                        <p>{{ lang().label.more }}</p>
                        <ChevronRightIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="">
                <Bar
                    id="my-chart-id"
                    :options="chartOptions"
                    :data="chartData"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
