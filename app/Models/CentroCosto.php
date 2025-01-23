<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @method HasMany hasMany(string $related, string $foreignKey = null, string $localKey = null)
 * @method BelongsToMany belongsToMany(string $related, string $table = null, string $foreignPivotKey = null, string $relatedPivotKey = null)
 * @method static selectRaw(string $string)
 * @property mixed $id
 */
class CentroCosto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'mano_obra_estimada',
        'activo', //finish_at 27mayo2024
        'descripcion',
        'clasificacion',
        'ValidoParaFacturar',
    ];

    private string|float $nombre;

    private int|float $mano_obra_estimada;

    private int|float $activo;

    private string|float $descripcion;

    private string|float $clasificacion;

    private int|float $ValidoParaFacturar;

    public function reportes(): HasMany{
        return $this->hasMany(Reporte::class);
    }

    public function users(): BelongsToMany{
        return $this->BelongstoMany(User::class, 'centro_user');
    }

    public function ArrayListaSupervisores($supervisores): array{
        $centroid = $this->id;
        $result = $supervisores->map(function ($user) use ($centroid) {
            if ($user->TieneEsteCentro($centroid)) {
                return $user->name;
            }
            return null;
        })->filter()->toArray();

        return $result;
    }

    public function ArraySupervisores($centroid, $PosiblesSupervisores): array
    {
        $result = [];
        if ($PosiblesSupervisores->count()) {
            foreach ($PosiblesSupervisores as $supervisor) {
                $result[] = $supervisor->ArrayCentrosID();
            }
        }

        return array_unique($result);
    }

    public function ArraySupervIDs(): array
    {
        $centroid = $this->id;
        $supervisores = User::UsersWithRol('supervisor')->get();
        $result = $supervisores->map(function ($user) use ($centroid) {
            if ($user->TieneEsteCentro($centroid)) {
                return $user->id;
            }

            return null;
        })->filter()->toArray();

        return $result;
    }

    //deep2 |
    public function actualizarEstimado($anio, $mes,$parametros): void
    {
        $BaseDeReportes = Reporte::Where('centro_costo_id', $this->id)
            ->WhereYear('fecha_ini', $anio)
            ->WhereMonth('fecha_ini', $mes)
            ->Where('valido', 1)
            ->get();

        $vardiurnas = 0;
        $varnocturnas = 0;
        $varextra_diurnas = 0;
        $varextra_nocturnas = 0;
        $vardominical_diurno = 0;
        $vardominical_nocturno = 0;
        $vardominical_extra_diurno = 0;
        $vardominical_extra_nocturno = 0;

        foreach ($BaseDeReportes as $index => $baseDeReporte) {
            $user = User::find($baseDeReporte->user_id);
            if ($user) {
                $sal = $user->salario / 235;
                $porcentaje_diurno = $parametros->porcentaje_diurno * $sal;
                $porcentaje_nocturno = $parametros->porcentaje_nocturno * $sal;
                $porcentaje_extra_diurno = $parametros->porcentaje_extra_diurno * $sal;
                $porcentaje_extra_nocturno = $parametros->porcentaje_extra_nocturno * $sal;
                $porcentaje_dominical_diurno = $parametros->porcentaje_dominical_diurno * $sal;
                $porcentaje_dominical_nocturno = $parametros->porcentaje_dominical_nocturno * $sal;
                $porcentaje_dominical_extra_diurno = $parametros->porcentaje_dominical_extra_diurno * $sal;
                $porcentaje_dominical_extra_nocturno = $parametros->porcentaje_dominical_extra_nocturno * $sal;

                $vardiurnas += ((float) $baseDeReporte->diurnas) * $porcentaje_diurno;
                $varnocturnas += ((float) $baseDeReporte->nocturnas) * $porcentaje_nocturno;
                $varextra_diurnas += ((float) $baseDeReporte->extra_diurnas) * $porcentaje_extra_diurno;
                $varextra_nocturnas += ((float) $baseDeReporte->extra_nocturnas) * $porcentaje_extra_nocturno;
                $vardominical_diurno += ((float) $baseDeReporte->dominical_diurno) * $porcentaje_dominical_diurno;
                $vardominical_nocturno += ((float) $baseDeReporte->dominical_nocturno) * $porcentaje_dominical_nocturno;
                $vardominical_extra_diurno += ((float) $baseDeReporte->dominical_extra_diurno) * $porcentaje_dominical_extra_diurno;
                $vardominical_extra_nocturno += ((float) $baseDeReporte->dominical_extra_nocturno) * $porcentaje_dominical_extra_nocturno;
            }
        }

        $this->mano_obra_estimada = (int) ($vardiurnas + $varnocturnas + $varextra_diurnas + $varextra_nocturnas + $vardominical_diurno + $vardominical_nocturno + $vardominical_extra_diurno + $vardominical_extra_nocturno);
        $this->update([
            'mano_obra_estimada' => $this->mano_obra_estimada,
        ]);
    }
}
