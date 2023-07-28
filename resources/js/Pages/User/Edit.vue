<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
    user: Object,
    roles: Object,
    cargos: Object,
    sexoSelect : Object,
    
})

const emit = defineEmits(["close"]);

const form = useForm({
    name: '',
    email:  '',
    cedula:'',
    telefono: '',
    celular: '',
    fecha_de_ingreso: '',
    sexo :  '',
    salario: '',
    role:  '',
    cargo:  '',
});

const update = () => {
    form.put(route('user.update', props.user?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        form.name = props.user?.name
        form.email = props.user?.email
        form.cedula = props.user?.cedula

        form.telefono = props.user?.telefono
        form.celular = props.user?.celular
        form.fecha_de_ingreso = props.user?.fecha_de_ingreso
        form.sexo = props.user?.sexo
        form.salario = props.user?.salario
        
        form.role = props.user?.roles == 0 ? '' : props.user?.roles[0].name
        form.cargo = props.user?.cargo_id == 0 ? '' : props.user?.cargo.id
        form.errors = {}
    }
    
})

const roles = props.roles?.map(role => ({ label: role.name, value: role.name }))
const cargos = props.cargos?.map(cargo => ({ label: cargo.nombre, value: cargo.id }))

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 space-y-4">
                    <div>
                        <InputLabel for="name" :value="lang().label.name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                            :placeholder="lang().placeholder.name" :error="form.errors.name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="cedula" :value="lang().label.cedula" />
                        <TextInput id="cedula" type="text" class="mt-1 block w-full" v-model="form.cedula" required
                            :placeholder="lang().placeholder.cedula" :error="form.errors.cedula" />
                        <InputError class="mt-2" :message="form.errors.cedula" />
                    </div>
                    <div>
                        <InputLabel for="email" :value="lang().label.email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email"
                            :placeholder="lang().placeholder.email" :error="form.errors.email" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="">
                            <InputLabel for="telefono" :value="lang().label.telefono" />
                            <TextInput id="telefono" type="text" class="mt-1 block w-full" v-model="form.telefono" required
                            :placeholder="lang().placeholder.telefono" :error="form.errors.telefono" />
                            <InputError class="mt-2" :message="form.errors.telefono" />
                        </div>
                        <div class="">
                            <InputLabel for="celular" :value="lang().label.celular" />
                            <TextInput id="celular" type="text" class="mt-1 block w-full" v-model="form.celular" required
                                :placeholder="lang().placeholder.celular" :error="form.errors.celular" />
                            <InputError class="mt-2" :message="form.errors.celular" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="fecha_de_ingreso" :value="lang().label.fecha_de_ingreso" />
                        <TextInput id="fecha_de_ingreso" type="date" class="mt-1 block w-full" v-model="form.fecha_de_ingreso" required
                            :placeholder="lang().placeholder.fecha_de_ingreso" :error="form.errors.fecha_de_ingreso" />
                        <InputError class="mt-2" :message="form.errors.fecha_de_ingreso" />
                    </div>
                    <div>
                        <InputLabel for="salario" :value="lang().label.salario" />
                        <TextInput id="salario" type="number" class="mt-1 block w-full" v-model="form.salario" required
                            :placeholder="lang().placeholder.salario" :error="form.errors.salario" />
                        <InputError class="mt-2" :message="form.errors.salario" />
                    </div>
                    <div>
                        <InputLabel for="sexo" :value="lang().label.sexo" />
                        <SelectInput v-model="form.sexo" :dataSet="props.sexoSelect" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.sexo" />
                    </div>

                    <div>
                        <InputLabel for="role" :value="lang().label.role" />
                        <SelectInput id="role" class="mt-1 block w-full" v-model="form.role" required :dataSet="roles">
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.role" />
                    </div>
                    <div>
                        <InputLabel for="cargo" :value="lang().label.cargo" />
                        <SelectInput id="cargo" class="mt-1 block w-full" v-model="form.cargo" required :dataSet="cargos">
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.cargo" />
                    </div>


                    <!-- <div>
                        <InputLabel for="password" :value="lang().label.password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                            :placeholder="lang().placeholder.password" :error="form.errors.password" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div>
                        <InputLabel for="password_confirmation" :value="lang().label.password_confirmation" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="form.password_confirmation" :placeholder="lang().placeholder.password_confirmation"
                            :error="form.errors.password_confirmation" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div> -->
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
