<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class viatico extends Model
{
    use HasFactory;

    protected $fillable = [
        'gasto',
        'saldo',
        'descripcion',
        'legalizacion',
        'fecha_legalizacion',
        'user_id',
        'centro_costo_id',
    ];

}
