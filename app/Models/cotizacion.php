<?php

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class cotizacion extends Model {
    use HasFactory;

    protected $fillable = [
        'numero_cot', //cotizacion
        'descripcion_cot',
        'precio_cot', //antes del iva
        'aprobado_cot',
        'fecha_aprobacion_cot',
        'centro_costo_id',

        'estado_cliente',
        'estado',
        'factura',
        'fecha_factura',
        'fecha_solicitud',
        'mes_pedido',
        'lugar',
        'tipo',
        'tipo_de_mantenimiento',
        'por_a',
        'por_i',
        'por_u',
        'admi',
        'impr',
        'util',
        
        'subtotal',
        'iva',
        'total',
        
        'persona_que_realiza_la_pe',
        'cliente',
        'persona_que_solicita_la_propuesta_economica',
        'orden_de_compra',
        'hes',
        'observaciones',
        'zona_id',
    ];
    //19feb2025
    /*
     * estos cambios implican una lista de clientes y TIPO DE MANTENIMIENTO 

     */
    protected $appends = [
        'Zouna',
        'Prealiza',
//        'Psolicita',
    ];

    public function centro(): BelongsTo {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function zona(): BelongsTo {
        return $this->belongsTo(zona::class, 'zona_id');
    }


    public function getZounaAttribute(): string {return $this->zona ? $this->zona->nombre : '';}
    
    public function getPrealizaAttribute(): string {
        $user = $this->belongsTo(User::class, 'persona_que_realiza_la_pe')->first();
        return $user ? $user->name : '';
    }
//    public function getPsolicitaAttribute(): string {
//        $user = $this->belongsTo(User::class, 'persona_que_solicita_la_propuesta_economica')->first();
//        return $user ? $user->name : '';
//    }
}
