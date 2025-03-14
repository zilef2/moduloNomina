<?php

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadisticasController extends Controller {
    public function CentroObsoleto() {
        $centrosConReportesAntiguos = CentroCosto::whereDoesntHave('reportes', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subYear());
        })
            ->orderby('created_at', 'asc')
            ->orderby('updated_at', 'desc')
            ->get()->take(20);
        dd(
            $centrosConReportesAntiguos->count(),
            $centrosConReportesAntiguos[0]->getAttributes() ?? 'no hay centro',
            $centrosConReportesAntiguos[1]->getAttributes() ?? 'no hay centro',
            $centrosConReportesAntiguos,
        );
    }
}
