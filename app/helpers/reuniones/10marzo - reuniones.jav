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


ORDEN DE LAS MIGRACIONES SEGUN RELACIONES


CARLOS (11mar2023)
{
     centro de costo
        centro de costo arribao el nombre del proyecto-nombre-numero
        cuando registra, que muestre como le quedó
        que muestre en tiempo real las horas de entrada y salida
    
    users
        tabla de usuarios tendra resetear contraseña

    reporte
        validar/autorizar las horas de cada trabajador

    ROL
    {
        admin(3)
        validador
        trabajador
    }

    Permisos  Discapacidad  Ausento  LicenciaNoRemunerada
    {

    }

    Tintero
    {
        descargar excel
        login: cedula contraseña

    }


    DESPLIEGUE
    {
        el tiene un dominio
        
    }
    15-21 dias | inicial (10marzo) -> (31marzo)
}