<?php

namespace App\Exports;

use App\Models\CentroCosto;
use Generator;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
class GeneratorExport implements FromGenerator
{
    use Exportable;

    public function generator(): Generator
    {
        $todosCC = CentroCosto::select(['nombre', 'descripcion','activo','ValidoParaFacturar'])
            ->orderByDesc('activo')
//            ->Where('activo', 1)
            ->get();

        $i = 0;
        yield [
            'Numero',
            'Centro',
            'Descripcion',
            'Activo',
            'ValidoParaFacturar',
        ];
        foreach ($todosCC as $index => $item) {
            $i++;
            yield [
                $i,
                $item['nombre'],
                $item['descripcion'],
                $item['activo'] == 1 ? 'Si' : 'No',
                $item['ValidoParaFacturar'] == 1 ? 'Si' : 'No',
            ];
        }

    }
}
