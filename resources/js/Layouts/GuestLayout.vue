<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import SwitchDarkMode from '@/Components/SwitchDarkMode.vue';
import SwitchLangNavbar from '@/Components/SwitchLangNavbar.vue';
</script>

<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 to-slate-200 dark:from-gray-950 dark:to-gray-900 text-gray-800 dark:text-gray-200 selection:bg-primary selection:text-white relative overflow-hidden">

        <!-- Decorative Background Elements -->
        <div
            class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/10 rounded-full blur-[120px] pointer-events-none">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/10 rounded-full blur-[120px] pointer-events-none">
        </div>

        <div
            class="w-full sm:max-w-md lg:max-w-4xl my-8 bg-white/70 dark:bg-gray-800/60 backdrop-blur-xl shadow-2xl overflow-hidden sm:rounded-2xl border border-white/20 dark:border-gray-700/30 transition-all duration-500 hover:shadow-primary/5">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="flex flex-col space-y-8 px-8 py-10 lg:py-16">
                    <div class="flex justify-between items-center mb-4">
                        <Link class="flex items-center group" href="/">
                            <div
                                class="p-2.5 bg-primary/10 rounded-xl group-hover:bg-primary/20 transition-colors duration-300">
                                <ApplicationLogo class="w-7 h-7 fill-primary" />
                            </div>
                            <p
                                class="text-xl font-bold ml-3 tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 group-hover:from-primary group-hover:to-primary/80 transition-all duration-300">
                                {{ $page.props.app.name }}
                            </p>
                        </Link>
                        <div class="flex space-x-3 items-center">
                            <SwitchLangNavbar class="hover:scale-105 transition-transform" />
                            <SwitchDarkMode class="hover:scale-105 transition-transform" />
                        </div>
                    </div>

                    <div class="flex-grow">
                        <slot />
                    </div>

                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700/50 text-center lg:text-left">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            &copy; {{ new Date().getFullYear() }} {{ $page.props.app.name }}. {{
                                lang().label.all_rights_reserved }}
                        </p>
                    </div>
                </div>

                <div
                    class="hidden lg:flex lg:flex-col px-12 py-16 justify-center items-center space-y-8 bg-gradient-to-br from-primary to-primary-darker text-white relative overflow-hidden">
                    <!-- Right side background pattern -->
                    <div class="absolute inset-0 opacity-10"
                        style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
                    </div>

                    <div class="relative z-10 transform hover:scale-105 transition-transform duration-700">
                        <slot name="illustration" />
                    </div>

                    <div class="relative z-10 text-center space-y-4 max-w-xs">
                        <h3 class="text-2xl font-bold tracking-tight">Optimiza tu Nómina</h3>
                        <p class="text-primary-foreground/80 text-sm leading-relaxed">
                            Gestión inteligente, eficiente y segura para el recurso más valioso de tu empresa.
                        </p>
                    </div>

                    <!-- Decorative shapes -->
                    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-64 h-64 bg-black/10 rounded-full blur-3xl">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
