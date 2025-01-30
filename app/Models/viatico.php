<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class viatico extends Model
{
    use HasFactory;
    protected $appends = ['userino','centrou'];
    protected $fillable = [
        'gasto',
        'saldo',
        'descripcion',
        'legalizacion',
        'fecha_legalizacion',
        'user_id',
        
        'centro_costo_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function centro(): BelongsTo
    {
        return $this->belongsTo(CentroCosto::class,'centro_costo_id');
    }

    public function getUserinoAttribute(): string
    {
        return $this->user ? $this->user->name : '';
    }
    public function getCentrouAttribute(): string //igual que el vector pero con mayus
    {
        return $this->centro ? $this->centro->nombre : '';
    }//tiene que existir la funcion centro
}
