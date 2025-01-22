<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cotizacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_cot',
        'descripcion_cot',
        'precio_cot',
        'aprobado_cot',
        'fecha_aprobacion_cot'
    ];

}
