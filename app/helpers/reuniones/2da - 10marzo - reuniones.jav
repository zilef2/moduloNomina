MODELS
    centroCosto:{
        nombre
    } 
    reporte:{
        fecha_inicio
        fecha_fin
        observaciones

        valido
        
        user_id
        centroCosto_id
    }



CARLOS (11mar2023) : entrega 17 marzo [ 8 dias]
{
    ing electrica: servicios o mantenimiento montajes electricos

     centro de costo
        // centro de costo arriba el nombre del proyecto-nombre-numero
        // cuando EL TRABAJADOR registra, que muestre como le quedó
        // que muestre en tiempo real las horas de entrada y salida
        // reporte diario de cada empelado
        // ver lo que se reporto cada quincena
        puede correjir su propia info

    user{
        un usuario: puede estar hasta 3 centro de costo
    }

    reporte
        // validar/autorizar las horas de cada trabajador
    ROL
    {
        // admin(3)
        // validador
        // trabajador
    }
    
    Sprint y entregables{
        15-21 dias | inicial sprint (10marzo) -> (31marzo) | 3 entregables
        1) 17 marzo
        2) ?? marzo
        2) 31 marzo
    }
}
// # posponer : Sprint 1 | entrega 2
{
    users
        tabla de usuarios tendra resetear contraseña : si se mantendrá un correo ??

    Permisos  Discapacidad  Ausento  LicenciaNoRemunerada
    {
        
    }

    registrar las jornadas que labora -> asociada a centros de costo

    Calculo de la nomina: {

    }

    herramienta: informe -> mantenimiento ->

}
// # to ask
{
    1 : CONTROLLER()        REPORTES ::: se pueden borrar mientras no se hayan calificado
    2 : CREATE_REPORTES()   REPORTES ::: se pueden reportar mas de 24 horas? cuanto es lo maximo? que detalles a tener en cuenta

}

Tintero 
{
    descargar excel
    subir excel
    login: cedula contraseña

    DESPLIEGUE
    {
        el tiene un dominio
    }
}




