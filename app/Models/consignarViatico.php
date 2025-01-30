<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class consignarViatico extends Model
{
    use HasFactory;

    protected $fillable = ['valor_consig', 'fecha_consig', 'user_id', 'viatico_id'];


    public function viatico(): BelongsTo
    {
        return $this->belongsTo(Viatico::class);
    }

}