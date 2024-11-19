<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo belongsTo(string $related, string $foreignKey = null, string $ownerKey = null)
 * @method \Illuminate\Database\Eloquent\Relations\HasMany hasMany(string $related, string $foreignKey = null, string $localKey = null)
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany belongsToMany(string $related, string $table = null, string $foreignPivotKey = null, string $relatedPivotKey = null)
 */
class Reporte extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'fecha_ini',
        'fecha_fin',
        'horas_trabajadas', //admit null
        'almuerzo',

        'diurnas',
        'nocturnas',
        'extra_diurnas',
        'extra_nocturnas',
        'dominical_diurno',
        'dominical_nocturno',
        'dominical_extra_diurno',
        'dominical_extra_nocturno',
        'valido', //admit null
        'observaciones', //admit null

        'centro_costo_id',
        'user_id',
    ];

    //	public function centrocostos() {
    //		return $this->hasOne('App\Models\CentroCosto');
    //	}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nameUser()
    {
        return $this->user->name;
    }
}
