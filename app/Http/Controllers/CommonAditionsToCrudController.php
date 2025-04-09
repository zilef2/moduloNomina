<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CommonAditionsToCrudController extends Controller
{
    public function PagePaginate($request,&$page,$divisionPAge, $model) {
        $perPage = $request->has('perPage') ? $request->perPage : $divisionPAge;
        $page = request('page', 1);
	    
	    
	    return new LengthAwarePaginator(
	        $model->forPage($page, $perPage),
	        $model->count(),
	        $perPage,
	        $page,
	        ['path' => request()->url()]
	    );
    }
	
	
	public function FiltrosDesarrollo($request,$estados) {
		$desarrollos = \App\Models\desarrollo::query();
		
		if ($request->has('search')) {
			$desarrollos = $desarrollos->where(function ($query) use ($request) {
				$query->where('nombre', 'LIKE', "%" . $request->search . "%")
					//                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
					//                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
				;
			});
		}
		
		if ($request->has([
			                  'field',
			                  'order'
		                  ])
		) {
			$desarrollos = $desarrollos->orderBy($request->field, $request->order);
		}
		else {
			$desarrollos = $desarrollos->orderBy('created_at', 'DESC');
		}
		
		
		return $desarrollos->WhereNot('estado', end($estados))->get();
	}
}
