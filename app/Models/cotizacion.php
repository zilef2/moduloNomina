<?php

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 */
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
        'fecha_aprobacion_cot',
        'centro_costo_id',
    ];
    
    public function centro(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }
        public function centros2()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_user');
    }

}
