<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $fillable = [
        'subsidio_de_transporte',
        'salario_minimo',
        'porcentaje_diurno',
        'porcentaje_nocturno',
        'porcentaje_extra_diurno',
        'porcentaje_extra_nocturno',
        'porcentaje_dominical_diurno',
        'porcentaje_dominical_nocturno',
        'porcentaje_dominical_extra_diurno',
        'porcentaje_dominical_extra_nocturno',
    ];
}
