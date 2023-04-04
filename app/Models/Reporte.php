<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

	protected $fillable = [
        'fecha_ini',
        'fecha_fin',
        'horas_trabajadas',//admit null

		'diurnas',
		'nocturnas',
		'extra_diurnas',
		'extra_nocturnas',

		'dominical_diurno',
		'dominical_nocturno',
		'dominical_extra_diurno',
		'dominical_extra_nocturno',
        'valido',//admit null
        'observaciones',//admit null

        'centro_costo_id',
        'user_id',
	];

	public function centrocostos()
	{
		return $this->hasOne('App\Models\CentroCosto');
	}
	public function users()
	{
		return $this->hasOne('App\Models\User');
	}
}
