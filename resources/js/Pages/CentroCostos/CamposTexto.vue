<template>
  <div>
    <InputLabel for="nombre" :value="lang().label.name"/>
    <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
               :placeholder="lang().placeholder.nombre" :error="form.errors.nombre"/>
    <InputError class="mt-2" :message="form.errors.nombre"/>
  </div>
</template>
<script>
export default {
  name: 'camposTexto'
}
</script>