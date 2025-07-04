<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\CentroCosto;
use App\Models\Parametro;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CentroTableController extends Controller
{
    private function noumbresTabla($numberPermissions): array
    {
        if ($numberPermissions < 2) { //admin | administrativo
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ['#', 'nombre'],
                [null, 'nombre'],
            ];
        } else {
            $nombresTabla = [//[0]: como se ven //[1] como es la BD
                ['Acciones', '#', 'nombre', 'Mano obra estimada', 'usuarios', 'Supervisor', 'activo', 'descripcion', 'clasificacion'],
                [null, null, 'nombre', 'mano_obra_estimada', null, null, 'activo', 'descripcion', 'clasificacion'],
            ];
        }
        return $nombresTabla;
    }


    private function TheQuery(int $centrocostoid, $request, ?bool $plata = false): array
    {
        if ($request->fecha_ini && $request->quincena) {
            $esteMes = $request->fecha_ini;
            $opcionQuincena = $request->quincena;
            if (!is_string($opcionQuincena)) {
                $opcionQuincena = $request->quincena['value'];
            }
            $esteMes['month'] = intval($esteMes['month']) + 1;

        } else {
            $esteMes = [
                'month' => date('m'),
                'year' => date('Y'),
            ];
            $opcionQuincena = 3; //el mes completo
        }
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
            ->WhereYear('fecha_ini', $esteMes['year'])
            ->WhereMonth('fecha_ini', $esteMes['month'])
            ->Where('valido', 1)
            ->where('centro_costo_id', $centrocostoid);

        if ($opcionQuincena == 1) {
            $Reportes = $Reportes->whereDay('fecha_ini', '<=', 15);
        } else {
            if ($opcionQuincena == 2) {
                $Reportes = $Reportes->whereDay('fecha_ini', '>', 15);
            }
        }
        $Reportes = $Reportes->groupBy('user_id')->get();
        if ($plata) {
            [$Reportes,$mano_obra_estimada] = $this->MultiplicarPorSalario($Reportes,$centrocostoid);
        } else {
            $Reportes->map(function ($reporteu) {
                $reporteu->usera = $reporteu->user->name;
                return $reporteu;
            })->filter();
        }

        $page = request('page', 1); // Current page number
        //        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $perPage = 1000;
        $total = $Reportes->count();
        $paginated = new LengthAwarePaginator(
            $Reportes->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return [$perPage, $paginated];
    }

    public function table(Request $request, $id): Response
    {
        $permissions = Myhelp::EscribirEnLog($this, ' |reportes table| ');
        $numberPermissions = MyModels::getPermissionToNumber($permissions);
        $Authuser = Myhelp::AuthU();
        $titulo = __('app.label.Reportes');

        $nombresTabla = $this->noumbresTabla($numberPermissions);
        $UltimoReporteRealizado = $this->UltimoReporteRealizadx($id); //return _ when no last report found

        [$perPage, $paginated] = $this->TheQuery($id, $request, $request->plata);

        return Inertia::render('CentroCostos/table', [ //carpeta
            'elIDD' => $id,
            'title' => CentroCosto::find($id)->nombre,
            'filters' => $request->all(['fecha_ini', 'quincena', 'plata']),
            'perPage' => (int)$perPage,
            'fromController' => $paginated,
            'nombresTabla' => $nombresTabla,
            'UltimoReporteRealizado' => $UltimoReporteRealizado,
        ]);
    }

    //CentroCosto->actualizarEstimado
    public function MultiplicarPorSalario($Reportes,$id)
    {
        $vectorPruebasuser = [];
        $vectorPruebas=[];
        $parametros = Parametro::find(1);
        $Acum_diurnas = 0;$Acum_nocturnas = 0;$Acum_extra_diurnas = 0;$Acum_extra_nocturnas = 0;$Acum_dominical_diurno = 0;$Acum_dominical_nocturno = 0;$Acum_dominical_extra_diurno = 0;$Acum_dominical_extra_nocturno = 0;
          
//        $Reportes->map(function ($reporteu) use ($parametros,&$vectorPruebasuser) {
        foreach ($Reportes as $index => $reporteu) {
            
            $user = User::find($reporteu->user_id);
            $vectorPruebasuser[] = $user->name;
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

                $vardiurnas = ((float)$reporteu->diurnas) * $porcentaje_diurno;
                $varnocturnas = ((float)$reporteu->nocturnas) * $porcentaje_nocturno;
                $varextra_diurnas = ((float)$reporteu->extra_diurnas) * $porcentaje_extra_diurno;
                $varextra_nocturnas = ((float)$reporteu->extra_nocturnas) * $porcentaje_extra_nocturno;
                $vardominical_diurno = ((float)$reporteu->dominical_diurno) * $porcentaje_dominical_diurno;
                $vardominical_nocturno = ((float)$reporteu->dominical_nocturno) * $porcentaje_dominical_nocturno;
                $vardominical_extra_diurno = ((float)$reporteu->dominical_extra_diurno) * $porcentaje_dominical_extra_diurno;
                $vardominical_extra_nocturno = ((float)$reporteu->dominical_extra_nocturno) * $porcentaje_dominical_extra_nocturno;

                $decoratotal = ($vardiurnas + $varnocturnas + $varextra_diurnas + $varextra_nocturnas + $vardominical_diurno + $vardominical_nocturno + $vardominical_extra_diurno + $vardominical_extra_nocturno);

                $reporteu->horas_trabajadas = $decoratotal;
                $reporteu->diurnas = $vardiurnas;
                $reporteu->nocturnas = $varnocturnas;
                $reporteu->extra_diurnas = $varextra_diurnas;
                $reporteu->extra_nocturnas = $varextra_nocturnas;
                $reporteu->dominical_diurno = $vardominical_diurno;
                $reporteu->dominical_nocturno = $vardominical_nocturno;
                $reporteu->dominical_extra_diurno = $vardominical_extra_diurno;
                $reporteu->dominical_extra_nocturno = $vardominical_extra_nocturno;


                $vectorPruebas[] = $vardiurnas;
                $Acum_diurnas += $vardiurnas;
                $Acum_nocturnas += $varnocturnas;
                $Acum_extra_diurnas += $varextra_diurnas;
                $Acum_extra_nocturnas += $varextra_nocturnas;
                $Acum_dominical_diurno += $vardominical_diurno;
                $Acum_dominical_nocturno += $vardominical_nocturno;
                $Acum_dominical_extra_diurno += $vardominical_extra_diurno;
                $Acum_dominical_extra_nocturno += $vardominical_extra_nocturno;
                
                $reporteu->usera = $reporteu->user->name;
            }
//            return $reporteu;
        }
        $mano_obra_estimada = (int) ($Acum_diurnas + $Acum_nocturnas + $Acum_extra_diurnas + $Acum_extra_nocturnas 
            + $Acum_dominical_diurno + $Acum_dominical_nocturno + $Acum_dominical_extra_diurno 
            + $Acum_dominical_extra_nocturno);
        
//        });
//        if($id == 78)dd($mano_obra_estimada,
//            $Acum_diurnas , $Acum_nocturnas , $Acum_extra_diurnas , $Acum_extra_nocturnas 
//            , $Acum_dominical_diurno , $Acum_dominical_nocturno , $Acum_dominical_extra_diurno 
//            , $Acum_dominical_extra_nocturno, $vectorPruebas
//        );
        
        return [$Reportes,$mano_obra_estimada];
    }

    private function UltimoReporteRealizadx($idcentrocosto): string
    {
        $ultimorepo = Reporte::Where('centro_costo_id', $idcentrocosto)
            ->latest()
            ->first();
		if($ultimorepo && $ultimorepo->fecha_ini){
			
            $returning = Carbon::parse($ultimorepo->fecha_ini)->diffForHumans();
		}else{
			return '_';
		}

        return 'La ultima modificación de este centro de costos fue: ' . $returning;
    }
}
