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
    
    public function reportes() { return $this->hasMany(Reporte::class); }
    public function users() { return $this->hasMany(User::class); }

    public function supervisores($id) { 
		return $this->users->flatMap(function($user) use ($id){
			$rol = $user->roles->pluck('name')[0];
			if($user->centro_costo_id == $id && $rol == 'supervisor')
			return collect($user);
		});
	}
}
