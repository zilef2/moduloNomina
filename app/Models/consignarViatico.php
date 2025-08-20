<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class consignarViatico extends Model {
	
	use HasFactory;
	
	protected $appends = [
		'ValoresCoinciden',
		'destinatiariu',
	];
	
	protected $fillable = [
		'valor_consig',
		'fecha_consig',
		'solicitud_viatico_id',
		'valor_legalizado',
		'fecha_legalizado',
		'descripcion_legalizacion',
		'remitente_user_id',
		'destinatiario_user_id',
	];
	
	public function getValoresCoincidenAttribute(): bool {
		return $this->valor_consig === $this->valor_legalizado;
	}
	public function getDestinatiariuAttribute(): string {
		$user = User::find($this->destinatiario_user_id);
		if($user)
			return $user->name;
		return 'No hay destinatario';
	}
	
	public function viatico(): BelongsTo {
		return $this->belongsTo(solicitud_viatico::class);
	}
	
}