<?php

namespace App\Models;

use App\Http\Controllers\CentroTableController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @method static Collection all()
 * @method static Model|static find($id)
 * @method static Model|static findOrFail($id)
 * @method static Model|static first()
 * @method static Model|static firstOrFail()
 * @method static Model|static firstOrNew(array $attributes = [])
 * @method static Model|static updateOrCreate(array $attributes, array $values = [])
 * @method static Model|static create(array $attributes)
 * @method static Model|static make(array $attributes = [])
 * @method static Collection|static get()
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
 */
class CentroCosto extends Model {
    use HasFactory;

    protected $fillable = [
        'nombre',
        'mano_obra_estimada',
        'activo', //finish_at 27mayo2024
        'descripcion',
        'clasificacion',
        'ValidoParaFacturar',
    ];


    public function reportes(): HasMany {
        return $this->hasMany(Reporte::class);
    }

    public function users(): BelongsToMany {
        return $this->BelongstoMany(User::class, 'centro_user');
    }

    public function ArrayListaSupervisores($supervisores): array {
        $centroid = (int)$this->id;
        return $supervisores->map(function (User $user) use ($centroid) {
            if ($user->TieneEsteCentro($centroid)) {
                return $user->name;
            }
            return null;
        })->filter()->toArray();
    }


    /**
     * @param Collection<int, User> $PosiblesSupervisores
     */
    public function ArraySupervisores($centroid, Collection $PosiblesSupervisores): array {
        $result = [];
        if ($PosiblesSupervisores->count()) {
            foreach ($PosiblesSupervisores as $supervisor) {
                $result[] = $supervisor->ArrayCentrosID();
            }
        }

        return array_unique($result);
    }

    public function ArraySupervIDs(): array {
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
    public function actualizarEstimado($anio, $mes, $parametros): void {
        $elSelect = [
            'user_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(horas_trabajadas) as horas_trabajadas'),
            DB::raw('SUM(almuerzo) as almuerzo'),
            DB::raw('SUM(diurnas) as diurnas'),
            DB::raw('SUM(nocturnas) as nocturnas'),
            DB::raw('SUM(extra_diurnas) as extra_diurnas'),
            DB::raw('SUM(extra_nocturnas) as extra_nocturnas'),
            DB::raw('SUM(dominical_diurno) as dominical_diurno'),
            DB::raw('SUM(dominical_nocturno) as dominical_nocturno'),
            DB::raw('SUM(dominical_extra_diurno) as dominical_extra_diurno'),
            DB::raw('SUM(dominical_extra_nocturno) as dominical_extra_nocturno'),
        ];

        $Reportes = Reporte::Select($elSelect)
            ->Where('centro_costo_id', $this->id)
            ->WhereYear('fecha_ini', $anio)
            ->WhereMonth('fecha_ini', $mes)
            ->Where('valido', 1)
            ->groupBy('user_id')
            ->get();

        $CTC = new CentroTableController();
        [$Reportes, $mano_obra_estimada] = $CTC->MultiplicarPorSalario($Reportes, $this->id);

        $this->update([
            'mano_obra_estimada' => $mano_obra_estimada,
        ]);
    }
}
