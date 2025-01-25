<?php

namespace App\helpers;

class MyModels {
    public static function getPermissionToNumber($permissions): int{
        
        if ($permissions === 'empleado') return 1;
        if ($permissions === 'administrativo') return 2;// no reportan
        if ($permissions === 'supervisor') return 3;// no reportan
        if ($permissions === 'ingeniero') return 3; //no reporta ni crea cc
        if ($permissions === 'admin') return 9;
        if ($permissions === 'superadmin') return 10;
        return 0;
    }
}
