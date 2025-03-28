<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $pagos
 */
class desarrollo extends Model {
	
	use HasFactory;
	
	protected $appends = [
		'valorino',
		'Numcuotas',
		'totalpagado',
		'Deudau',
	];
	
	protected $fillable = [
		'nombre',
		'descripcion',
		'valor_inicial',
		'valor_parcial1',
		'valor_parcial2',
		'valor_parcial3',
		'fecha_reunion',
		'fecha_cotizacion',
		'fecha_cotizacion_aceptada',
		'estado'
	];
	
	public function pagos(): \Illuminate\Database\Eloquent\Relations\HasMany {
		return $this->HasMany(pagodesarrollo::class);
	}
	
	public function getValorinoAttribute(): string {
		if (!$this->pagos || $this->pagos->isEmpty()) {
			return '0';
		}
		
		
		return implode(', ', $this->pagos->flatMap(fn($pago) => [
			$this->formatPesosCol($pago->valor)
		])->toArray());
	}
	
	function formatPesosCol($number): string {
		$number = round($number);
		$formattedNumber = number_format($number, 0, '.', ',');
		
		
		return '$ ' . $formattedNumber;
	}
	
	public function getNumcuotasAttribute(): int {
		return pagodesarrollo::Where('desarrollo_id', $this->id)->count();
	}
	public function getDeudauAttribute(): int {
		return (int)$this->valor_inicial - (int)pagodesarrollo::Where('desarrollo_id', $this->id)->sum('valor');
	}
	
	public function getTotalpagadoAttribute(): int {
		return pagodesarrollo::Where('desarrollo_id', $this->id)->sum('valor');
	}
	
}
