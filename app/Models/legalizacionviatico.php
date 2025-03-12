<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

    class legalizacionviatico extends Model
{
    use HasFactory;
    protected $fillable = ['valor_legalizacion', 'fecha', 'cuota', 'final'];
    
    public function viatico(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Viatico::class);
    }

}
