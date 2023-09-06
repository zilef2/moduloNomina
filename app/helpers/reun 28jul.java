

//# nuevos modulos
se pide el modulo de despido y recontratacion
rol: administrativo{
    operativo
    administrativo{
        no generan horas extra
        ejemplos: coordinadora ,ingenieros, aux ingenieria, asistente adm, gerente...
        solo el basico
        el otro programa sacaba el basico sin registros de tiempos()


        relacion muchos a muchos entre usuario y CC

        los ingenieros, auxiliares de ingenieria, deben ver los tiempos de los CC: {
            encargado del CC
            puede ver los tiempos de ese CC
            pueden tener mas de un CC

            nuevos datos del CC{
                clientes
                num_propuesta
                responsable
                valor
                fecha ini
                fecha fin

            }
        }
    }   
}