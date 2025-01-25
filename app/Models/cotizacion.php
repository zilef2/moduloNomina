<?php

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function centro(): BelongsTo
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

}
