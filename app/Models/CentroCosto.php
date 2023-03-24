<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    use HasFactory;
    protected $fillable = [
		'nombre'
	];
    
    public function reportes()
	{
		return $this->hasMany(Reporte::class);
		// return $this->hasMany('App\Models\Reporte');
	}
}
