<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

    class peusuario extends Model
{
    use HasFactory;
    protected $fillable = [
		'nombre_solicitante_PE'
    ];

}
