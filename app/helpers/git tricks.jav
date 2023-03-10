git remote -v
git remote rm origin
git remote add origin https://github.com/zilef2/plan.git



// como calcular
valor tentativo de un proyecto

fecha entrega

MODELS
{
    new model -> sprints:{
        fecha_inicio
        fecha_entrega
        valor_inicial
        adicion(){
            valor
            dias_extra
        }
        valor_total
    } -> many to many with: new model -> reuniones:{
        titulo
        fecha
        duracion_estimada
        duracion_real
        requerimientos
        observaciones
        -> has many: new model -> modulo:{
            nombre
            complejidad:1-4
        }
    } 
}

observaciones?

//modelos()

new model -> personas:{
    nombre
    cc
    -> hasmany: new model -> empresa:{
        nombre
        NIT
    }
}
new model -> sprints:{}
//pendientes
// que me dice la diferencia de fechas entre la primera reunion y el primer pago



CARLOS 
{
    centro de costo arribao el nombre del proyecto-nombre-numero
    cuando registra, que muestre como le quedó
    que muestre en tiempo real las horas de entrada y salida
    login: cedula contraseña
    tabla de usuarios tendra resetear contraseña

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
    }


    DESPLIEGUE
    {
        el tiene un dominio
        
    }
    15-21 dias | inicial (10marzo) -> (31marzo)
}