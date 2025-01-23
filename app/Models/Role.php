<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as ModelsRole;

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
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string|array $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')

 * @method static \Illuminate\Database\Eloquent\Builder|static WhereBetween($column, $array)
 * @method static \Illuminate\Database\Eloquent\Builder|static WhereYear($column, $operator = null, $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|static WhereMonth($column, $operator = null, $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|static orWhere($column, $operator = null, $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|static whereIn($column, $values)
 * @method static \Illuminate\Database\Eloquent\Builder|static whereNotIn($column, $values)
 * @method static \Illuminate\Database\Eloquent\Builder|static whereNull($column)
 * @method static \Illuminate\Database\Eloquent\Builder|static whereNotNull($column)
 * @method static \Illuminate\Database\Eloquent\Builder|static orderBy($column, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|static latest($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|static oldest($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|static paginate($perPage = 15, $columns = ['*'], $pageName = 'page')
 * @method static \Illuminate\Database\Eloquent\Builder|static simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page')
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo belongsTo(string $related, string $foreignKey = null, string $ownerKey = null)
 * @method \Illuminate\Database\Eloquent\Relations\HasMany hasMany(string $related, string $foreignKey = null, string $localKey = null)
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany belongsToMany(string $related, string $table = null, string $foreignPivotKey = null, string $relatedPivotKey = null)
 */

class Role extends ModelsRole
{
    use HasFactory;
    protected $fillable = [
        'id',
    ];


    public function getCreatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
    }
}
