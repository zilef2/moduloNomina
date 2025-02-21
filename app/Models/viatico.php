<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class viatico extends Model {
    use HasFactory, SoftDeletes;

    protected $appends = ['userino', 'centrou', 'consignaciona', 'fechaconsig'];
    protected $fillable = [
        'gasto',
        'saldo',
        'descripcion',
        'legalizacion',
        'fecha_legalizacion',
        'valor_legalizacion',
        'descripcion_legalizacion',

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
        $arrayConsignaciones = $this->consignacion->pluck('valor_consig')->toarray();
        if (gettype($arrayConsignaciones) == 'array') {
            return $this->consignacion ? $arrayConsignaciones : ['0'];
        }
        else {
            return ['0'];
        }
    }

    public function getFechaconsigAttribute(): array {
        $arrayConsignaciones = $this->consignacion->pluck('created_at')->toarray();
        if (gettype($arrayConsignaciones) == 'array') {
            return $this->consignacion ? $arrayConsignaciones : ['-'];
        }
        else {
            return ['-'];
        }
    }
}
