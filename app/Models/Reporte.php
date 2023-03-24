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
