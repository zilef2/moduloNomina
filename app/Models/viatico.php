<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class viatico extends Model {
    use HasFactory, SoftDeletes;

    protected $appends = [
        'userino',
        'centrou',
        'Consignaciona',
        'Totallegalizadou',
    ];

    protected $fillable = [
        'gasto',
        'saldo',
        'descripcion',
        'legalizacion',
        'fecha_inicial',
        'fecha_final',
        'numerodias',
//        'fecha_legalizacion',
//        'valor_legalizacion',
//        'descripcion_legalizacion',

        'user_id',
        'centro_costo_id',
    ];


    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function centro(): BelongsTo {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function consignacion(): HasMany {
        return $this->hasMany(consignarViatico::class);
    }

    public function getUserinoAttribute(): string {
        return $this->user ? $this->user->name : '';
    }

    public function getCentrouAttribute(): string //igual que el vector pero con mayus
    {
        return $this->centro ? $this->centro->nombre : '';
    }//tiene que existir la funcion centro

    public function getConsignacionaAttribute(): array {
        return $this->consignacion()->get()->map(function ($item) {
            return [
                'viatic_id' => $this->id,
                'consignacion_id' => $item->id,
                'valor' => $item->valor_consig,
                'fecha' => $item->created_at,
                'fecha_legalizacion' => $item->fecha_legalizado ?? '',
                'valor_legalizacion' => $item->valor_legalizado ?? '',
                'descripcion_legalizacion' => $item->descripcion_legalizacion ?? '',
            ];
        })->toArray();

    }
    public function getTotallegalizadouAttribute(): int {
//        dd(
//          $this->consignacion()->get(),  
//          $this->consignacion()->get()[0]->getattributes(),  
//          $this->consignacion()->get()->sum('valor_legalizado'),  
//        );
        return $this->consignacion()->get()->sum('valor_legalizado');
    }

//    public function getFechaconsigAttribute(): array {
//        $arrayConsignaciones = $this->consignacion->pluck('created_at')->toarray();
//        if (gettype($arrayConsignaciones) == 'array') {
//            return $this->consignacion ? $arrayConsignaciones : ['-'];
//        }
//        else {
//            return ['-'];
//        }
//    }
}
