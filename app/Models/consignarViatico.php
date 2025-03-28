<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class consignarViatico extends Model {
	
	use HasFactory;
	
	protected $appends = [
		'ValoresCoinciden'
	];
	
	protected $fillable = [
		'valor_consig',
		'fecha_consig',
		'user_id',
		//        'viatico_id',
		'solicitud_viatico_id',
		
		'valor_legalizado',
		'fecha_legalizado',
		'descripcion_legalizacion',
	];
	
	public function getValoresCoincidenAttribute(): bool {
		return $this->valor_consig === $this->valor_legalizado;
	}
	
	public function viatico(): BelongsTo {
		return $this->belongsTo(solicitud_viatico::class);
	}
	
}