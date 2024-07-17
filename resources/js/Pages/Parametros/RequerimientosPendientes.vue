<template>
  <section class="text-gray-600 body-font">
    <div class="container px-5 py-8 mx-auto">
      <div class="text-center my-8">
        <h2 class="tracking-widest text-2xl title-font font-medium text-black dark:text-white mb-1">
          Pendientes
        </h2>
      </div>
      <div class="flex flex-wrap mx-4 my-8">
        <div class="py-8 px-4 lg:w-1/3">
          <div class="h-full flex items-start">
            <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
              <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">24 mayo</span>
              <span class="font-medium text-lg text-gray-800 title-font leading-none">2024</span>
            </div>
            <div class="flex-grow pl-6">
              <div class="flex">
                <h2 class="tracking-widest text-xl title-font font-medium text-blue-500 mb-1">
                  Reporte de centro de costo
                </h2>
                <div class="flex-grow pl-6">
                    <div class="inline-flex">
                        <h2 class="tracking-widest text-xl title-font font-medium text-amber-600 mb-1">
                            Coste aproximado de la mano de obra de cada Centro de costo
                        </h2>
                    </div>
                    <p class="leading-relaxed text-lg mb-5">
                      Coste aproximado de la mano de obra de cada Centro de costo
                    "1. Agregar item de descripción
                    2. Que se puedan poner activos e inactivos
                    3. Que se pueda seleccionar del centro de costos el supervisor
                    4. que se pueda filtrar ya sea por supervisor o por centro de costo"
<!--                        //todo: organizar esto para que sea legible-->
                    </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
export default {
  name: 'Requerimientos'
}
</script>
