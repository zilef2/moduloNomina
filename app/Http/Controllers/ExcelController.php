<?php

namespace App\Http\Controllers;

use App\Exports\GeneratorExport;
use Illuminate\Http\Request;

class ExcelController extends Controller
{

    public function CentroCostos()
    {
        return (new GeneratorExport())->download('CentrosDeCostos.xlsx');
    }
}
