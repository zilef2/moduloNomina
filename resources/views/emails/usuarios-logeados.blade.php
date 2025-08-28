<x-mail::message>
# 📊 Reporte de Usuarios en Reportes — {{ $fecha }}

Se detectaron accesos a la vista de **reportes** el día **{{ $fecha }}**.

@if(!empty($usuarios['empleado']))
### 👷 Empleados
<x-mail::table>
| Empleados |
| ------- |
@foreach($usuarios['empleado'] as $empleado)
| {{ $empleado }} |
@endforeach
</x-mail::table>
@endif
@if(!empty($usuarios['administrativo']))
### 👷 administrativo
<x-mail::table>
| administrativo |
| ------- |
@foreach($usuarios['administrativo'] as $administrativo)
| {{ $administrativo }} |
@endforeach
</x-mail::table>
@endif

@if(!empty($usuarios['otros']))
### 🧑‍💼 Otros Cargos
<x-mail::table>
| Usuario |
| ------- |
@foreach($usuarios['otros'] as $otro)
| {{ $otro }} |
@endforeach
</x-mail::table>
@endif

<x-mail::panel>
🔔 Este listado se genera automáticamente desde `laravel.log`.
</x-mail::panel>

Gracias,<br>
**Equipo de Sistemas**
</x-mail::message>
