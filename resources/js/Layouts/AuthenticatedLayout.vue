<script setup>
import { ref } from 'vue';
import NavbarVue from '@/Components/Navbar.vue';
import SideBarVue from "@/Components/SideBar.vue";
import Toast from '@/Components/Toast.vue';
import Footer from '@/Components/Footer.vue';

const sidebarOpened = ref(false);
const emit = defineEmits(['close', 'open']);
</script>


<template>
    <div class="flex w-full overflow-hidden">
        <SideBarVue :open="sidebarOpened" @close="sidebarOpened = false" />
        <div class="pl-0 lg:pl-52 w-full min-h-screen block bg-gray-200 dark:bg-gray-900">
            <Toast :flash="$page.props.flash" />
            <NavbarVue :open="sidebarOpened" @open="sidebarOpened = true" />
            <!-- Page Content -->
            <main class="xl:max-w-full lg:max-w-7xl md:max-w-7xl mx-auto sm:px-6 lg:px-8 pb-6 text-gray-900 dark:text-gray-100 dark:bg-gray-900 text-md">
                <slot />
            </main>
            
            <Footer />
        </div>
    </div>
</template>
