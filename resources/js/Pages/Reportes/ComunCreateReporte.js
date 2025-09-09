
/*
INDICE

CALCULAR AND RETURN


FUNCIONES NO USADAS
 */

// ### ###  ### ### variables ### ###  ### ###

export const consolelog  = {
    watchEffect: false,
    CuandoEiezaExtra: false,
    dia: false,
    noche: false,
    extradia: false,
    extranoche: false,
    dominicales: false,
    terminaDomingo: false,
    terminaLunes: false,
    EsFestivo: false,

    MostrarTrabajadaSemana: false,
    MostrarAlmuersini: false,
    ValorRealalmuerzo: 0,
}
// ### ###  ### ### CALCULAR AND RETURN ### ###  ### ### 
export function calcularSinExtras(Inicio, Fin) {
    const horasInicio = new Date(Inicio).getHours();
    const horasFin = new Date(Fin).getHours();

    let BaseInicial = horasInicio >= 6 ? horasInicio : 6
    const BaseFinal = horasFin >= 21 ? 21 : horasFin

    let HorasDiurnas = BaseFinal - BaseInicial;
    HorasDiurnas = HorasDiurnas < 0 ? 0 : HorasDiurnas

    //calcularnocturnas
    let Madrugada = 0
    let Tarde = 0
    let Resta = horasFin - horasInicio;
    if (horasInicio < 6 && horasFin <= 6) {//solo de noche
        Madrugada = Resta;
    } else {
        if (horasInicio < 6) {
            Madrugada = (6 - horasInicio);
        }
    }

    if (horasInicio >= 21 && horasFin >= 21) {//solo de noche
        Tarde = Resta;
    } else {
        if (horasFin > 21) {//si existan horas nocturnas, si no son 0
            Tarde = (horasFin - 21);
        }
    }
    return [HorasDiurnas, (Madrugada + Tarde)]
}

// ### ###  ### ### FUNCIONES NO USADAS ### ###  ### ### 

const Reporte11_59 = () => {
    let fin = Date.parse(form.fecha_fin);
    let finDate = new Date(fin);
    const horafin = finDate.getHours()
    const minfin = finDate.getMinutes()
    if (horafin === 23 && minfin === 59) {
        if (form.extra_nocturnas > 0) form.extra_nocturnas++
        else {
            if (form.dominical_nocturnas > 0) form.dominical_nocturnas++
            else {
                if (form.dominical_extra_nocturnas > 0) form.dominical_extra_nocturnas++
                else {
                    form.nocturnas++
                }
            }

        }
    }
}