<?php

namespace App\helpers;

class CargosModelos {

    //JUST THIS PROJECT
    public static function CargosYModelos() {
        $MycrudSemiCompleto = ['update', 'read', 'create','download','aprobar'];
        $LasVainasDeEsteProyecto = ['sugerencia','egreso','ingreso','firmar'];
        $crudSemiCompleto = array_merge($MycrudSemiCompleto, $LasVainasDeEsteProyecto);
        
        $crudCompleto = array_merge(['delete'], $crudSemiCompleto);
        //otros cargos NO_ADMIN
        $nombresDeCargos = [
//            'Rector',//1
//            'ViceRector', //2
//            'Lider', //3
//            'Vinculado', //4
//            'Contratista', //5
        ];//heyRemember: userseeder, RoleSeeder
        $isSome = [];
        foreach ($nombresDeCargos as $key => $value) {
            $isSome[] = 'is' . $value;
        }
        $elcore = 'llaves'; //todo: esto no es asi
        $Models = [
            'role',
            'permission',
            'user',
            'parametro',
            $elcore,
        ];
        
        return [
            'nombresDeCargos' => $nombresDeCargos,
            'Models' => $Models,
            'isSome' => $isSome,
            'core' => $elcore,
            'crudCompleto' => $crudCompleto,
            'crudSemiCompleto' => $crudSemiCompleto,
        ];
    }
}
?>
