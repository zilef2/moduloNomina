<script setup>
defineProps({
    type: String
})
</script>
<template>
<svg width="600" height="800" viewBox="0 0 600 800" xmlns="http://www.w3.org/2000/svg">

  <defs>
    <!-- Gradiente principal -->
    <linearGradient id="bgGradient" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#3da0d8"/>
      <stop offset="50%" stop-color="#1565c0"/>
      <stop offset="100%" stop-color="#0d47a1"/>
    </linearGradient>

    <!-- Curva decorativa -->
    <linearGradient id="waveGradient" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%" stop-color="#1e88e5" stop-opacity="0.4"/>
      <stop offset="100%" stop-color="#42a5f5" stop-opacity="0.2"/>
    </linearGradient>
  </defs>

  <!-- Fondo -->
  <rect width="600" height="800" rx="20" fill="url(#bgGradient)" />

  <!-- Curva inferior decorativa -->
  <path d="M0 500 Q300 620 600 520 L600 800 L0 800 Z" fill="url(#waveGradient)" />

  <!-- Texto EC -->
  <text x="50%" y="280"
        text-anchor="middle"
        font-family="Montserrat, Arial, sans-serif"
        font-size="260"
        font-weight="700"
        fill="#ffffff"
        letter-spacing="10">
    EC
  </text>

  <!-- Ingeniería -->
  <text x="50%" y="480"
        text-anchor="middle"
        font-family="Montserrat, Arial, sans-serif"
        font-size="70"
        font-weight="300"
        fill="#ffffff"
        letter-spacing="3">
    Ingeniería
  </text>

  <!-- Eléctrica -->
  <text x="50%" y="580"
        text-anchor="middle"
        font-family="Montserrat, Arial, sans-serif"
        font-size="110"
        font-weight="700"
        fill="#ffffff">
    Eléctrica
  </text>

</svg>

</template>