<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

    class material extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'unidad_de_medida', 'cantidad', 'precio_unitario', 'fecha_adquisicion', 'miniatura', 'stock_minimo', 'ubicacion'];

}
