<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuntheticationIllustration from '@/Components/AuntheticationIllustration.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

import { onMounted } from 'vue';

onMounted(() => {
    const savedEmail = localStorage.getItem('remembered_email');
    if (savedEmail) {
        form.email = savedEmail;
        form.remember = true;
    }
});

const submit = () => {
    if (form.remember) {
        localStorage.setItem('remembered_email', form.email);
    } else {
        localStorage.removeItem('remembered_email');
    }
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head :title="lang().label.login" />

        <template #illustration>
            <AuntheticationIllustration type="login" class="w-full max-w-[320px] h-auto drop-shadow-2xl" />
        </template>

        <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ lang().label.welcome_back || 'Bienvenido de nuevo' }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">
                    {{ lang().label.login_subtitle || 'Ingresa tus credenciales para acceder al sistema.' }}
                </p>
            </div>

            <div v-if="status"
                class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl">
                <p class="text-sm font-medium text-green-700 dark:text-green-400">
                    {{ status }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="space-y-2">
                    <InputLabel for="email" :value="lang().label.email" class="text-sm font-semibold ml-1" />
                    <div class="relative group">
                        <TextInput id="email" type="email"
                            class="block w-full px-4 py-3 bg-gray-50/50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-primary focus:border-primary transition-all duration-300"
                            v-model="form.email" required autofocus autocomplete="username"
                            :placeholder="lang().placeholder.email" :error="form.errors.email" />
                    </div>
                    <InputError class="mt-1" :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <InputLabel for="password" :value="lang().label.password" class="text-sm font-semibold" />
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-xs font-medium text-primary hover:text-primary-darker transition-colors duration-200">
                            {{ lang().label.lost_password }}
                        </Link>
                    </div>
                    <div class="relative group">
                        <TextInput id="password" type="password"
                            class="block w-full px-4 py-3 bg-gray-50/50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-primary focus:border-primary transition-all duration-300"
                            v-model="form.password" required autocomplete="current-password"
                            :placeholder="lang().placeholder.password" :error="form.errors.password" />
                    </div>
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <div class="flex items-center px-1">
                    <label class="flex items-center cursor-pointer group">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span
                            class="ml-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors duration-200 select-none">
                            {{ lang().label.remember_me }}
                        </span>
                    </label>
                </div>

                <div class="pt-2">
                    <PrimaryButton
                        class="w-full justify-center py-4 bg-primary hover:bg-primary-darker text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:shadow-primary/40 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }" :disabled="form.processing">
                        <span v-if="form.processing" class="flex items-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>{{ lang().button.login }}...</span>
                        </span>
                        <span v-else>{{ lang().button.login }}</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
