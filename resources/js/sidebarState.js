import { ref } from 'vue';
// Si es true, la sidebar se verá por defecto. Si es false, estará oculta por defecto.
export const sidebarOpened = ref(true);

export const toggleSidebar = () => {
    sidebarOpened.value = !sidebarOpened.value;
};

export const openSidebar = () => {
    sidebarOpened.value = true;
};

export const closeSidebar = () => {
    sidebarOpened.value = false;
};
