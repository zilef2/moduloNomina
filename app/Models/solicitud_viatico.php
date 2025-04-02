<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class solicitud_viatico extends Model {
	
	use HasFactory;
	
	protected $appends = [
		'centrou',
		'Consignaciona',
		'Totalsolicitado',
		'TotalConsignado',
		'Totallegalizadou',
		'Losviaticos',
		'Userino',
	];
	
	protected $fillable = [
		'Solicitante',
		'Fechasol',
		'Ciudad',
		'ObraServicio',
		'user_id',
		'saldo_sol',
	];
	
	public function user(): BelongsTo {return $this->belongsTo(User::class); }
	public function consignacion(): \Illuminate\Database\Eloquent\Relations\HasMany {return $this->hasMany(consignarViatico::class); }
	public function viaticos(): \Illuminate\Database\Eloquent\Relations\HasMany {return $this->hasMany(viatico::class); }
	
	public function getTotallegalizadouAttribute(): int {
		return $this->consignacion()->get()->sum('valor_legalizado');
	}
	
	
	public function getLosviaticosAttribute(): array {
		 $viaticos = $this->viaticos()->get()->toArray();
		foreach ($viaticos as $index => $viatico) {
			$viatico['useri'] = User::find($viatico['user_id'])->name; 
		 }
		 return $viaticos;
	}
	public function getUserinoAttribute(): string {
		return $this->user()->first()->name ?? 'No hay responsable';
	}
	
	
	public function getTotalsolicitadoAttribute(): int {
		return $this->viaticos()->get()->sum('gasto');
	}
	public function getTotalConsignadoAttribute(): int {
		return $this->consignacion()->get()->sum('valor_consig');
	}
	
	public function getCentrouAttribute(): string //igual que el vector pero con mayus
	{
		$viatico1 = $this->viaticos()->first();
		return $viatico1->centro ? $viatico1->centro->nombre : '';
	}//tiene que existir la funcion centro
	
	public function getConsignacionaAttribute(): array {
		if ($this->consignacion()->get()) {
			return $this->consignacion()->get()->map(function ($item) {
				//				$item ==> consignarViatico
				return [
					'viatic_id'                => $this->id,
					'consignacion_id'          => $item->id,
					'valor'                    => $item->valor_consig,
					'fecha'                    => $item->created_at,
					'fecha_legalizacion'       => $item->fecha_legalizado ?? '',
					'valor_legalizacion'       => $item->valor_legalizado ?? '',
					'descripcion_legalizacion' => $item->descripcion_legalizacion ?? '',
				];
			})->toArray();
		}
		
		
		return [];
	}
}