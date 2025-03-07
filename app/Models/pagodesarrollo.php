<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

    class pagodesarrollo extends Model
{
    use HasFactory;
    
    protected $appends = ['Numcuotas'];
    
    protected $fillable = [
        'valor',
        'fecha',
        'cuota',
        'final',
        'desarrollo_id'
    ];

    public function desarrollo(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(desarrollo::class);
    }

        public function numcoutas() {
            
    }
    
    public function getNumcuotasAttribute(): int {
        return pagodesarrollo::Where('desarrollo_id',$this->desarrollo_id)->count();
    }

}
