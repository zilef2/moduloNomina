<x-mail::message>
# ⏱️ Reporte de Horas Extras — {{ $fecha }}

Durante la quincena, se detectaron los siguientes usuarios que superaron las **44 horas semanales**:

@if($usuarios->isNotEmpty())
<x-mail::table>
| Usuario | Semana ISO | Total de Horas |
|----------|-------------|----------------|
@foreach($usuarios as $u)
| {{ $u->usuario }} | {{ $u->semana_iso }} | {{ $u->total_horas }} |
@endforeach
</x-mail::table>
@else
✅ Ningún usuario superó las {{$MaxDiasSemanales}} horas semanales en esta quincena.
@endif

<x-mail::panel>
🔔 Este reporte se genera automáticamente cada quincena para mantener un seguimiento adecuado de las horas trabajadas por los usuarios.
</x-mail::panel>

Gracias,  
**El alejo**
</x-mail::message>
