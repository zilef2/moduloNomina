<template>
  <section class="text-gray-600 body-font">
    <div class="container px-5 py-8 mx-auto">
      <div class="text-center my-8">
        <h2 class="text-2xl title-font font-medium text-black dark:text-white mb-1">
          Parametros del aplicativo
        </h2>
      </div>
      <div class="flex flex-wrap -mx-4 my-8">
        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
              <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">9</span>
              <span class="font-medium text-lg text-gray-800 title-font">nov 2023</span>
            </div>
            <div class="flex-grow pl-6">
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Salario hora ✅
                </h2>
                <!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
              </div>
              <!-- <h1 class="title-font text-xl font-medium text-gray-900 mb-3">The 400 Blows</h1> -->
              <p class="leading-relaxed text-lg mb-5">
                Salrio Hora = salario mensual / (235)
              </p>
            </div>
          </div>
        </div>
        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
              <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7</span>
              <span class="font-medium text-lg text-gray-800 title-font leading-none">sept 2023</span>
            </div>
            <div class="flex-grow pl-6">
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Hora de comida ❌
                </h2>
                <!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
              </div>
              <p class="leading-relaxed text-lg mb-5">
                Horas trabajadas > 8 <b>entonces</b> Comida = 1
              </p>
              <p class="leading-relaxed text-lg mb-5">
                Horas trabajadas > 16 <b>entonces</b> Comida = 2
              </p>
            </div>
          </div>
        </div>
        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="w-12 flex-shrink-0 flex flex-col text-center leading-none">
              <span class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7</span>
              <span class="font-medium text-lg text-gray-800 title-font leading-none">sept 2023</span>
            </div>
            <div class="flex-grow pl-6">
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Subsidio de transporte ❔
                </h2>
                <!--                                    <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
              </div>
              <h1 class="title-font text-xl font-medium text-gray-900 mb-3">¿ Cumplió la quincena ?</h1>
              <p class="leading-relaxed text-lg mb-5">
                Si el salario supera el doble del SMLV <b>Subsidio Transporte = 0</b>
              </p>
              <p class="leading-relaxed text-lg mb-5">
                Si no <b>Subsidio Transporte = dias * subsidio transporte (dia)</b>
              </p>
            </div>
          </div>
        </div>
        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="flex-grow pl-6">
              <p class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">14 diciembre 2023</p>
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Horas de los administrativos ✅
                </h2>
                <!--                                    <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
              </div>
              <p class="leading-relaxed text-lg mb-5">
                Los administrativos no tienen que reportar
              </p>
            </div>
          </div>
        </div>
        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="flex-grow pl-6">
          <p class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7 septiembre 2023</p>
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Calculo de salud y pension ✅
                </h2>
                <!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
              </div>
              <h1 class="title-font text-xl font-medium text-gray-900 mb-3">¿ Cumplió la quincena ?</h1>
              <p class="leading-relaxed text-lg mb-5">
                Salario quincena * 4% ✅
              </p>
              <p class="leading-relaxed text-lg mb-5">
                No tiene derecho ❔
              </p>
            </div>
          </div>
        </div>

        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="flex-grow pl-6">
              <p class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">4 abril 2024</p>
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Horas ingenieros  ❔
                </h2>
                <!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
              </div>
              <p class="leading-relaxed text-lg mb-5">
                Los ingenieros ganan horas extras?
              </p>
            </div>
          </div>
        </div>


        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="flex-grow pl-6">
              <p class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">15 de julio 2024</p>
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Horas extras el dia anterior
                </h2>
                <!--                                        <input type="checkbox" name="" id="" class="m-2 p-4 inline-flex">-->
              </div>
              <p class="leading-relaxed text-lg mb-5 text-red-600">Aun sin correo enviado</p>
            </div>
          </div>
        </div>
        <div :class="claseImportante">
          <div class="h-full flex items-start">
            <div class="flex-grow pl-6">
              <p class="text-gray-500 pb-2 mb-2 border-b-2 border-gray-200">7 septiembre 2023</p>
              <div class="inline-flex">
                <h2 :class="clasetitulos">
                  Horas extras por la semana
                </h2>
              </div>
            <p class="leading-relaxed text-lg mb-5 text-sky-700">Pendiente por aprobacion</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
export default {
    name: 'Condicionales',
    data() {
        return {
          claseImportante: 'py-8 px-4 w-full md:w-1/2 lg:w-1/3 xl:w-1/4',
          clasetitulos: 'tracking-widest text-lg title-font font-medium text-blue-500 mb-1',
        };
    }
}
</script>
