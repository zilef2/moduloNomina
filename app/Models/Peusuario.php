<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

    class Peusuario extends Model
{
    use HasFactory;
    protected $fillable = [
		'nombre_solicitante_PE',
		'clasificacion',
    ];

}
