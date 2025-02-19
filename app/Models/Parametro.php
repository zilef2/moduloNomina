<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
class Parametro extends Model
{
    use HasFactory;

    protected $fillable = [

        's_Dias_gabela',
        'HORAS_ORDINARIAS',
        'HORAS_NECESARIAS_SEMANA',

        'subsidio_de_transporte_dia',
        'salario_minimo',
        'valor_maximo_subsidio_de_transporte',


        'porcentaje_diurno',
        'porcentaje_nocturno',
        'porcentaje_extra_diurno',
        'porcentaje_extra_nocturno',
        'porcentaje_dominical_diurno',
        'porcentaje_dominical_nocturno',
        'porcentaje_dominical_extra_diurno',
        'porcentaje_dominical_extra_nocturno',

        // 'porcentaje_salud_pension', actualmente = 0.04
        
        'minimo_material',
    ];
}
