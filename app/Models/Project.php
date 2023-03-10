<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable=[
        'nombre',
        'cliente',
        'num_modulos',
        'valor_tentativo',
        'valor_acordado',
        'valor_primer_pago',
        'fecha_primera_reunion',
        'fecha_primer_pago',
        'fecha_entrega',
        'observaciones',
    ];
}
