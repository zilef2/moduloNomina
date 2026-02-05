/*
-- FESTIVOFUNCTIONS --
estaFechaEsFestivo


-- TERMINA_LUNES_O_DOMINGO --
calcularTerminaLunes
calcularTerminaDomingo


-- CALCULAR --
calcularHoras
*/

//FIN INDEX

import {consolelog} from "../Pages/Reportes/ComunCreateReporte";
import {RestarAlmuarzo} from "./HelpingCreate2";

//<editor-fold desc="FESTIVOFUNCTIONS">
//number_format
export function estaFechaEsFestivo(fecha, FestivosColombia) {
    
    let dateFestivos, dateArr, daysfestivo, monthFestivo, result: boolean = false;
    const BreakException: {} = {};
    let dayEvaluado: number = Math.floor(fecha.getDate());
    let MonthEvaluado: number = (fecha.getMonth());

    // console.table([MonthEvaluado, dayEvaluado,fecha])
    try {
        let holidaysYear = FestivosColombia.getHolidaysByYear(fecha.getFullYear());

        holidaysYear.forEach(element => {
            dateArr = element.date.split('/');
            dateFestivos = new Date(dateArr[2], dateArr[1] - 1, dateArr[0]);
            daysfestivo = Math.floor(dateFestivos.getDate());
            monthFestivo = (dateFestivos.getMonth());

            if (daysfestivo === dayEvaluado && monthFestivo === MonthEvaluado) {
                result = true;
                throw BreakException
            }
        });
    } catch (e) {
        if (e !== BreakException) throw e;
    }

    return result
}


//</editor-fold>

import {calcularDiurnas, calcularNocturnas, setDominical} from "./HelpingCreate2";

//<editor-fold desc="TERMINA_LUNES_O_DOMINGO">
export function calcularTerminaLunes(fin, CuandoEmpiezaExtra, ExtrasManana, form) {
    let Madrugada, Tarde //variables internas para calculo de horas nocturnas

    form.dominical_diurnas = form.diurnas;
    form.dominical_extra_diurnas = form.extra_diurnas;
    const HoraTermino = parseInt(fin.getHours())

    if (HoraTermino <= 6) {//termino madrugada
        form.diurnas = 0
        form.extra_diurnas = 0
        if (form.nocturnas === 0) {
            form.dominical_nocturnas = form.nocturnas - HoraTermino;
            form.nocturnas = HoraTermino;
        } else {
            form.dominical_extra_nocturnas = form.extra_nocturnas - HoraTermino;
            form.extra_nocturnas = HoraTermino
        }
    } else {// termino de dia
        if (form.nocturnas === 0) {//hay extra
            form.dominical_extra_nocturnas = Tarde;
            form.extra_nocturnas = Madrugada;
        } else {
            form.dominical_nocturnas = Tarde;
            form.nocturnas = Madrugada - form.extra_nocturnas
        }
        const horasDemas = Math.abs(HoraTermino - 6);
        if (form.diurnas == 0) {
            form.dominical_extra_diurnas = form.extra_diurnas - horasDemas;
            form.extra_diurnas = horasDemas
        } else {
            form.dominical_diurnas = form.diurnas - horasDemas;
            form.diurnas = horasDemas;
        }
    }
}

export function calcularTerminaDomingo(ini, fin, CuandoEmpiezaExtra, ExtrasManana, form, empiezanNocturnas) {
    let Madrugada, Tarde
    const HoraIni = parseInt(ini.getHours())
    const HoraTermino = parseInt(fin.getHours())
    if (CuandoEmpiezaExtra !== null) {
        if (CuandoEmpiezaExtra < 6) {
            form.dominical_extra_nocturnas = Madrugada - CuandoEmpiezaExtra //Siempre Madrugada > CuandoEmpiezaExtra
            form.extra_nocturnas -= form.dominical_extra_nocturnas
            form.dominical_nocturnas = CuandoEmpiezaExtra
            console.log("form.dominical_nocturnas", form.dominical_nocturnas);
            form.nocturnas -= CuandoEmpiezaExtra
            // ------------- las de dia -------------
            form.dominical_diurnas = 0
            form.dominical_extra_diurnas = HoraTermino > 6 ? HoraTermino - 6 : 0
            // form.diurnas
            form.extra_diurnas -= form.dominical_extra_diurnas
        } else {//extras > 6
            if (ExtrasManana) {//extra empiezan el domingo luego de 6
                // form.dominical_extra_nocturnas
                // form.extra_nocturnas
                form.dominical_nocturnas = Madrugada
                form.nocturnas = Tarde
                // ------------- las de dia -------------
                form.dominical_diurnas = CuandoEmpiezaExtra - 6
                form.dominical_extra_diurnas = HoraTermino - CuandoEmpiezaExtra
                form.diurnas = 0
                form.extra_diurnas = 0
            } else { // extras empezaron el dia anterior
                form.dominical_extra_nocturnas = Madrugada
                form.extra_nocturnas -= Madrugada
                form.dominical_nocturnas = 0
                // form.nocturnas
                // ------------- las de dia -------------
                form.dominical_diurnas = 0
                if (HoraTermino > 6) {
                    form.dominical_extra_diurnas = HoraTermino - 6
                    // form.diurnas = 0
                    form.extra_diurnas -= form.dominical_extra_diurnas
                }
            }
        }
    } else {//sin extras
        form.dominical_nocturnas = Madrugada
        form.nocturnas -= Madrugada

        if (HoraIni >= empiezanNocturnas) {
            form.dominical_diurnas = parseInt(form.diurnas)
            form.diurnas = 0;
        } else {
            form.diurnas = empiezanNocturnas - HoraIni;
            form.dominical_diurnas = HoraTermino > 6 ? HoraTermino - 6 : 0
        }
    }
}

//</editor-fold>

//<editor-fold desc="CALCULAR">

//# papa = watchEffect
export function calcularHoras(data, form, inicio, final, HORAS_PARA_EXTRAS_DIA, FestivosColombia, message): void {
    let ini = new Date(inicio)
    let fin = new Date(final)
    let ExtrasManana = false //

    data.TemporalDiaAnterior = data.HorasDelDiaAnterior59 ? data.HorasDelDiaAnterior59 : 0 //7jul2023: esta desactivado
    let Dateinii = Date.parse(form.fecha_ini) ?? false;
    if (Dateinii && Date.parse(form.fecha_fin)) {
        let horasInicioome: number = parseInt(String(new Date(form.fecha_ini).getHours()))
        let horasFinOme: number = parseInt(String(new Date(form.fecha_fin).getHours()))
        let CuandoEmpiezaExtra: number = horasInicioome + HORAS_PARA_EXTRAS_DIA

        const diaSemana: number = (new Date(form.fecha_ini)).getDay();
        // if (diaSemana === 6) LIMITE_ALMUERZO -=3

        //razon1: semanal
        if(data.CuantoFaltaParaExtraSemana < HORAS_PARA_EXTRAS_DIA){
            CuandoEmpiezaExtra -= HORAS_PARA_EXTRAS_DIA - data.CuantoFaltaParaExtraSemana
        }
        //razon2: ya trabajo hoy
        CuandoEmpiezaExtra -= data.TrabajadasHooy

        //razon3: es viernes
        if (diaSemana === 5) { //viernes
            CuandoEmpiezaExtra -= 1
        }
        
        // if (diaSemana === 6) { //sabado //esto ya se calcula con las 44 horas semanales
        //     CuandoEmpiezaExtra -= 3
        // }

        CuandoEmpiezaExtra = CuandoEmpiezaExtra < horasInicioome ? horasInicioome : CuandoEmpiezaExtra

        if (consolelog.CuandoEiezaExtra) {
            console.log('%cPRIMERO: CuandoEmpiezaExtra', "color:red;font-family:system-ui;font-size:1rem;-webkit-text-stroke: 0.5px black;font-weight:bold")
            console.log('CuandoEmpiezaExtra', CuandoEmpiezaExtra)
            console.log('data.CuantoFaltaParaExtraSemana', data.CuantoFaltaParaExtraSemana) // es mayor a 0 si ya cumplio las horas de semana
            console.log('data.TrabajadasHooy', data.TrabajadasHooy) //si reporto hoy
            console.log('TemporalDiaAnterior', data.TemporalDiaAnterior) // si reporto ayer
            console.log('%cFIN: CuandoEmpiezaExtra', "color:blue;font-family:system-ui;font-size:15px;-webkit-text-stroke: 0.5px black;font-weight:bold")
        }
        const empiezanNocturnas = 19; //nocturnas empiezan a las 7pm
        

        if (CuandoEmpiezaExtra >= horasFinOme) {
            form.diurnas = Math.abs(calcularDiurnas(form.fecha_ini, form.fecha_fin, CuandoEmpiezaExtra,empiezanNocturnas)[1]);
            form.nocturnas = Math.abs(calcularNocturnas(form.fecha_ini, form.fecha_fin, CuandoEmpiezaExtra, HORAS_PARA_EXTRAS_DIA,empiezanNocturnas)[1]);
        } else { //extras

            if (CuandoEmpiezaExtra >= 24) {
                CuandoEmpiezaExtra = 24 //version horasmismodia
                ExtrasManana = true
            }
            
            
            let horasExtrasDiurnas = (calcularDiurnas(form.fecha_ini, form.fecha_fin, CuandoEmpiezaExtra,empiezanNocturnas));
            form.extra_diurnas = horasExtrasDiurnas[0];
            form.diurnas = horasExtrasDiurnas[1];

            let horasExtrasNocturnas = (calcularNocturnas(form.fecha_ini, form.fecha_fin, CuandoEmpiezaExtra, HORAS_PARA_EXTRAS_DIA,empiezanNocturnas));
            form.extra_nocturnas = horasExtrasNocturnas[0];
            form.nocturnas = horasExtrasNocturnas[1];
        }
        RestarAlmuarzo(form,data)

        //dentro de setdomonical esta RestarAlmuarzo
        setDominical(data, form, ini, fin, CuandoEmpiezaExtra, ExtrasManana, FestivosColombia, message, empiezanNocturnas);

        if (data.estado2359) {
            if (CuandoEmpiezaExtra >= horasFinOme) {
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
            } else {
                if (form.extra_nocturnas >= 0) form.extra_nocturnas++
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
    }
}

//</editor-fold>


