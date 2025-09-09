<x-mail::message>
# ⚠️ Aviso de Pago Pendiente

Le recordamos que el proyecto **{{ $desarrollo->nombre }}** fue cotizado hace **{{ $haceCuanto }}**,  
y la fecha estimada de cancelación fue el **{{ $fechaVencimiento }}**.  

Actualmente se encuentra un saldo pendiente de pago por:

${{ number_format($desarrollo->Deudau, 0, ',', '.') }}

<x-mail::panel>
Solicitamos amablemente que el pago sea efectuado a la mayor brevedad. 
De no concretarse antes de fin de quincena, nos veremos en la necesidad de suspender toda asesoria con el personal administrativo.
</x-mail::panel>

    
@if($cuantosDesarrollosPendientes > 1)
Hay {{ $cuantosDesarrollosPendientes }} pagos pendientes
@endif

    
    
<br><br>
Gracias,<br>

</x-mail::message>
