<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static Model|static find($id)
 * @method static Model|static findOrFail($id)
 * @method static Model|static first()
 * @method static Model|static firstOrFail()
 * @method static Model|static firstOrNew(array $attributes = [])
 * @method static Model|static updateOrCreate(array $attributes, array $values = [])
 * @method static Model|static create(array $attributes)
 * @method static Model|static make(array $attributes = [])
 * @method static \Illuminate\Database\Eloquent\Collection|static get()
 * @method static bool|null forceDelete()
 * @method static Builder|static query()
 * @method static Builder|static where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|static WhereYear($column, $operator = null, $value = null)
 * @method static Builder|static WhereMonth($column, $operator = null, $value = null)
 * @method static Builder|static orWhere($column, $operator = null, $value = null)
 * @method static Builder|static whereIn($column, $values)
 * @method static Builder|static whereNotIn($column, $values)
 * @method static Builder|static whereNull($column)
 * @method static Builder|static whereNotNull($column)
 * @method static Builder|static orderBy($column, $direction = 'asc')
 * @method static Builder|static latest($column = 'created_at')
 * @method static Builder|static oldest($column = 'created_at')
 * @method static Builder|static paginate($perPage = 15, $columns = ['*'], $pageName = 'page')
 * @method static Builder|static simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page')
 * @method BelongsTo belongsTo(string $related, string $foreignKey = null, string $ownerKey = null)
 * @method HasMany hasMany(string $related, string $foreignKey = null, string $localKey = null)
 * @method BelongsToMany belongsToMany(string $related, string $table = null, string $foreignPivotKey = null, string $relatedPivotKey = null)
 * @property mixed $id
 * @property mixed $roles
 * @property mixed $name
 */
class User extends Authenticatable {
	
	use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'cedula',
		'telefono',
		'celular',
		'cargo_id',
		//24abril
		'fecha_de_ingreso',
		'sexo',
		'salario',
		'centro_costo_id',
		//12 abril
		'numero_contrato',
	];
	/**
	 * * The attributes that should be hidden for serialization. * *
	 *
	 * @var array<int, string>
	 */
	protected $hidden = ['password', 'remember_token'];
	private mixed $salario;
	
	public static function UsersWithRol($rol) {
		return User::whereHas('roles', function ($query) use ($rol) {
			return $query->where('name', $rol);
		});
	}
	
	public static function UsersWithManyRols($ArrayRoles) {
		return User::whereHas('roles', function ($query) use ($ArrayRoles) {
			return $query->whereIn('name', $ArrayRoles);
		});
	}
	
	public static function usuariosConCentros(): array {
		return self::with('centros:id')->get()->mapWithKeys(function ($user) {
				return [$user->id => $user->centros->pluck('id')->toArray()];
			})->toArray()
		;
	}
	
	public static function jefe() {
		return static::where('name', 'Carlos Daniel Anaya Barrios')->first();
	}
	
	public function getFechaIngreso() {
		return date('d-m-Y', strtotime($this->attributes['fecha_ingreso']));
	}
	
	//fin permissions
	
	public function getCreatedAtAttribute() {
		return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
	}
	
	public function getUpdatedAtAttribute() {
		return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
	}
	
	public function getEmailVerifiedAtAttribute() {
		return $this->attributes['email_verified_at'] == null ? null : date('d-m-Y H:i', strtotime($this->attributes['email_verified_at']));
	}
	
	public function getPermissionArray() {
		return $this->getAllPermissions()->mapWithKeys(function ($pr) {
			return [$pr['name'] => true];
		});
	}
	
	public function reportes(): HasMany {
		return $this->hasMany('App\Models\Reporte');
	}
	
	public function cargo(): BelongsTo {
		return $this->belongsTo(Cargo::class);
	}
	
	public function ArrayCentrosID(): array {
		$result = [];
		if (!$this->centros->isEmpty()) {
			foreach ($this->centros as $centro) {
				$result[] = $centro->id;
			}
		}
		
		
		return $result;
	}
	
	public function NotMyCentros(int $numberPermissions): array {
		if ($numberPermissions > 9) {
			return [];
		}
		$misCentrosIDs = $this->centros->pluck('id')->toArray();
		
		
		return CentroCosto::whereNotIn('id', $misCentrosIDs)->pluck('id')->toArray();
	}
	
	public function ArraycentroName($numeroDeCentros = 0): array|string {
		if (!$this->centros->isEmpty()) {
			if ($numeroDeCentros != 0) {
				$arrayNombres = [];
				for ($i = 0; $i < $numeroDeCentros; $i ++) {
					$tempCentro = $this->centros->skip($i)->first();
					if (isset($tempCentro)) {
						$arrayNombres[] = $this->centros->skip($i)->first()->nombre;
					}
					else {
						break;
					}
				}
			}
			else {
				foreach ($this->centros as $index => $centro) {
					$arrayNombres[] = $centro->nombre;
				}
			}
			$result = $arrayNombres;
		}
		else {
			$result = 'Sin centros';
		}
		
		
		return $result;
	}
	
	public function TieneEsteCentro($centroid) {
		$susCentros = $this->centros()->pluck('centro_costos.id')->toArray();
		$result = in_array($centroid, $susCentros);
		
		
		return $result;
	}
	
		public function centros(): BelongsToMany {
		return $this->belongsToMany(CentroCosto::class, 'centro_user');
	}//        public function resetPassword($id) {
	//        $user = User::findOrFail($id);
	//        $user->resetPasswordToCedula();
	//
	//        return response()->json(['message' => 'Contraseña de '.$user->name.' restablecida correctamente']);
	//    }
	
public function resetPasswordToCedula(): void {
		$this->password = \Illuminate\Support\Facades\Hash::make($this->cedula . '*');
		$this->save();
	}
}
