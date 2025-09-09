<x-mail::message>
# ⚠️ Aviso de Pago Pendiente

Le recordamos que el proyecto **{{ $desarrollo->nombre }}** fue cotizado hace **{{ $haceCuanto }}**,  
y la fecha estimada de cancelación fue el **{{ $fechaVencimiento }}**.  

Actualmente se encuentra un saldo pendiente de pago por:

<x-mail::table>
| -------- | -----:|
| Deuda    | ${{ number_format($desarrollo->Deudau, 0, ',', '.') }} |
</x-mail::table>

<x-mail::panel>
**Páguese a la brevedad** o el proyecto quedará **en suspensión a fin de quincena**.
</x-mail::panel>

Hay {{ $cuantosDesarrollosPendientes }} pagos pendientes
    
<br><br>
Gracias,<br>

</x-mail::message>
