Cordial saludo <b></b>{{ $data['empleado'] ?? 'usuario' }},
<br><br>
El supervisor {{ $data['supervisor'] }} modificó tu reporte el {{ now()->format('d/m/Y g:i a') }}.

<br><br>

Puedes ver los cambios realizados en el reporte haciendo clic en el botón de abajo.

<br>
<a href="{{ $url ?? '#' }}" style="color:#007BFF; text-decoration:underline; font-weight:bold;">
    Ver reporte
</a>

<br><br>
Gracias,<br>
EC ingenieria
