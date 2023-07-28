<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\CentroCosto;
use App\Http\Requests\CentroCostoRequest;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CentroCostosController extends Controller {
    public function index(Request $request) {
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        Log::info(' U -> '.Auth::user()->name. ' Accedio a la vista ' .$nombreC);

        $centroCostos = centroCosto::query();
        if ($request->has('search')) {
            $centroCostos->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $centroCostos->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;

        $permissions = Auth()->user()->roles->pluck('name')[0];
        if($permissions === "empleado") { //admin | administrativo
            $nombresTabla =[//[0]: como se ven //[1] como es la BD
                ["#","nombre"],
                [null,"nombre"]
            ];
        }else{
            $nombresTabla =[//[0]: como se ven //[1] como es la BD
                ["#","nombre","Acciones"],
                [null,"nombre",null]
            ];
        }
        return Inertia::render('CentroCostos/Index', [ //carpeta
            'title'          =>  __('app.label.CentroCostos'),
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $centroCostos->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.CentroCostos'), 'href' => route('CentroCostos.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }

    public function create() { }

    public function store(CentroCostoRequest $request) {
        DB::beginTransaction();
        try {
            $centroCostos = new centroCosto;
            $centroCostos->nombre = $request->nombre;
            $centroCostos->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    public function show( $id) {
        // $centroCostos = centroCosto::findOrFail($id);
        $Reportes = Reporte::query();
        
        $titulo = __('app.label.Reportes');
        $permissions = Auth()->user()->roles->pluck('name')[0];
        $Reportes->Where('centro_costo_id',$id);
        $valoresSelectConsulta = CentroCosto::orderBy('nombre')->get();
        $IntegerDefectoSelect = $valoresSelectConsulta->first()->id;
        foreach ($valoresSelectConsulta as $value) {
            $valoresSelect[] = [
                'label' => $value->nombre, //centro de costos
                'value' => intval($value->id),
            ];
            $showSelect[intval($value->id)] = $value->nombre;
        }
        $usuariosSelectConsulta = User::orderBy('name')->get();
        foreach ($usuariosSelectConsulta as $value) {
            $showUsers[intval($value->id)] = $value->name;
        }
        
        if($permissions === "empleado") { //admin | administrativo
        }else{ // not empleado
            // $ReportesEsteMes = Reporte::WhereMonth('fecha_ini',$esteMes)->get()->count();
            $titulo = $this->CalcularTituloQuincena($permissions);
            
            $Reportes->orderBy('fecha_ini'); $perPage = 15;

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio",       "fin",        "horas trabajadas",   "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,null,null,null,null,null,null,null,null,null,null] //campos ordenables
            ];
        }
        $sumhoras_trabajadas = $Reportes->sum('horas_trabajadas');
        return Inertia::render('Reportes/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  null,
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Reportes->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,

            'valoresSelect'   =>  $valoresSelect,
            'showSelect'   =>  $showSelect,
            'IntegerDefectoSelect'   =>  $IntegerDefectoSelect,
            'showUsers'   =>  $showUsers,
            'sumhoras_trabajadas'   =>  $sumhoras_trabajadas,
        ]);
    }

    public function CalcularTituloQuincena($permissions) {
        $esteMes = date("m");
        $diaquincena = date("d");
        if($permissions === "empleado") { //NO admin | administrativo
            $userid = Auth::user()->id;
            if($diaquincena <= 15){
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)
                    ->where('user_id',$userid)
                    ->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)
                    ->where('user_id',$userid)
                    ->sum('horas_trabajadas');
            }
        }else{
            if($diaquincena <= 15){
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','<=',15)
                    ->sum('horas_trabajadas');
            }else{
                $horasTrabajadas = Reporte::WhereMonth('fecha_ini',$esteMes)->WhereDay('fecha_ini','>',15)
                    ->sum('horas_trabajadas');
            }
        }

        return 'Horas trabajadas quincena: '.$horasTrabajadas;
    }


    public function edit($id) {
        $centroCostos = centroCosto::findOrFail($id);
        return Inertia::render('centroCostos.edit',['centroCostos'=>$centroCostos]);
    }

    public function update(CentroCostoRequest $request, $id) {
         DB::beginTransaction();

        try {
            $centroCostos = centroCosto::findOrFail($id);
            $centroCostos->nombre = $request->input('nombre');
            $centroCostos->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        DB::beginTransaction();

        try {
            $centroCostos = CentroCosto::findOrFail($id);
            $centroCostos->delete();

            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $centroCostos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.centroCostos')]) . $th->getMessage());
        }
    }
}
