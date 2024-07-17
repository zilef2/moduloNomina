/*
FESTIVOFUNCTIONS
estaFechaEsFestivo


TERMINA_LUNES_O_DOMINGO
calcularTerminaLunes
calcularTerminaDomingo


CALCULAR
calcularHoras
*/

//FIN INDEX

//<editor-fold desc="FESTIVOFUNCTIONS">
//number_format
export function estaFechaEsFestivo(
    fecha,dataMostrarConsole,FestivosColombia
){
    let dateFestivos,dateArr,daysfestivo,monthFestivo,result:boolean = false;
    const BreakException: {} = {};
    let dayEvaluado: number = Math.floor(fecha.getDate());
    let MonthEvaluado: number = (fecha.getMonth());

    if(dataMostrarConsole.EsFestivo){
        console.log('MonthEvaluado',MonthEvaluado)
        console.log('dayEvaluado',dayEvaluado)
        console.log('PARAMETER: fecha',fecha)
    }
    try{
        let holidaysYear = FestivosColombia.getHolidaysByYear(fecha.getFullYear());

        holidaysYear.forEach(element => {
            dateArr = element.date.split('/');
            dateFestivos = new Date(dateArr[2], dateArr[1] - 1, dateArr[0]);
            daysfestivo = Math.floor(dateFestivos.getDate());
            monthFestivo = (dateFestivos.getMonth());

            if(daysfestivo === dayEvaluado && monthFestivo === MonthEvaluado) {
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
export function calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana,form){
    let Madrugada, Tarde //variables internas para calculo de horas nocturnas

    form.dominical_diurnas = form.diurnas;
    form.dominical_extra_diurnas = form.extra_diurnas;
    const HoraTermino = parseInt(fin.getHours())

    if(HoraTermino <= 6){//termino madrugada
        form.diurnas = 0
        form.extra_diurnas = 0
        if(form.nocturnas === 0){
            form.dominical_nocturnas = form.nocturnas - HoraTermino;
            form.nocturnas = HoraTermino;
        }else{
            form.dominical_extra_nocturnas = form.extra_nocturnas - HoraTermino;
            form.extra_nocturnas = HoraTermino
        }
    }else{// termino de dia
        if(form.nocturnas === 0){//hay extra
            form.dominical_extra_nocturnas = Tarde;
            form.extra_nocturnas = Madrugada;
        }else{
            form.dominical_nocturnas = Tarde;
            form.nocturnas = Madrugada - form.extra_nocturnas
        }
        const horasDemas = Math.abs(HoraTermino - 6);
        if(form.diurnas == 0){
            form.dominical_extra_diurnas = form.extra_diurnas - horasDemas;
            form.extra_diurnas = horasDemas
        }else{
            form.dominical_diurnas = form.diurnas - horasDemas;
            form.diurnas = horasDemas;
        }
    }
}

export function calcularTerminaDomingo(ini,fin,CuandoEmpiezaExtra,ExtrasManana,form){
    let Madrugada, Tarde
    const HoraIni = parseInt(ini.getHours())
    const HoraTermino = parseInt(fin.getHours())
    if(CuandoEmpiezaExtra !== null){
        if(CuandoEmpiezaExtra < 6){
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
        }else{//extras > 6
            if(ExtrasManana){//extra empiezan el domingo luego de 6
                // form.dominical_extra_nocturnas
                // form.extra_nocturnas
                form.dominical_nocturnas = Madrugada
                form.nocturnas = Tarde
                // ------------- las de dia -------------
                form.dominical_diurnas = CuandoEmpiezaExtra - 6
                form.dominical_extra_diurnas = HoraTermino - CuandoEmpiezaExtra
                form.diurnas = 0
                form.extra_diurnas = 0
            }else{ // extras empezaron el dia anterior
                form.dominical_extra_nocturnas = Madrugada
                form.extra_nocturnas -= Madrugada
                form.dominical_nocturnas = 0
                // form.nocturnas
                // ------------- las de dia -------------
                form.dominical_diurnas = 0
                if(HoraTermino > 6){
                    form.dominical_extra_diurnas = HoraTermino - 6
                    // form.diurnas = 0
                    form.extra_diurnas -= form.dominical_extra_diurnas
                }
            }
        }
    }else{//sin extras
        form.dominical_nocturnas = Madrugada
        form.nocturnas -= Madrugada

        if(HoraIni >= 21){
            form.dominical_diurnas = parseInt(form.diurnas)
            form.diurnas = 0;
        }else{
            form.diurnas = 21 - HoraIni;
            form.dominical_diurnas = HoraTermino > 6 ? HoraTermino - 6 : 0
        }
    }
}
//</editor-fold>

//<editor-fold desc="CALCULAR">

//# papa = watchEffect
export function calcularHoras(data,form,props,inicio,final,HORAS_ESTANDAR,FestivosColombia,message){
    let ini = new Date(inicio)
    let fin = new Date(final)
    let ExtrasManana = false

    data.TemporalDiaAnterior = data.HorasDelDiaAnterior59 ? data.HorasDelDiaAnterior59 : 0 //7jul: esta desactivado
    let ExtrasPrematuras = HORAS_ESTANDAR

    let Dateinii = Date.parse(form.fecha_ini) ?? false;
    if(Dateinii && Date.parse(form.fecha_fin) ){
        let horasInicioome:number = parseInt(String(new Date(form.fecha_ini).getHours()))
        let horasFinOme = parseInt(String(new Date(form.fecha_fin).getHours()))
        let CuandoEmpiezaExtra = horasInicioome

        ExtrasPrematuras -= (data.TrabajadasSemana-1)//machete aqui
        let diaSemana = ini.getDay();
        if (diaSemana === 6) {
            CuandoEmpiezaExtra -= 1 //TODO: machetaso, deberia venir de las horas semanales
        }

        if(data.TrabajadasSemana == 0)
            ExtrasPrematuras -= data.TrabajadasHooy
        // ExtrasPrematuras -= data.TemporalDiaAnterior //23:59
        ExtrasPrematuras = ExtrasPrematuras < 0 ? 0 : ExtrasPrematuras

        CuandoEmpiezaExtra += ExtrasPrematuras
        CuandoEmpiezaExtra = CuandoEmpiezaExtra < horasInicioome ? horasInicioome : CuandoEmpiezaExtra

        if(data.MostrarConsole.CuandoEiezaExtra){
            console.log('%cPRIMERO: CuandoEmpiezaExtra', "color:red;font-family:system-ui;font-size:1rem;-webkit-text-stroke: 0.5px black;font-weight:bold")
            console.log('CuandoEmpiezaExtra',CuandoEmpiezaExtra)
            console.log('extras?',CuandoEmpiezaExtra < horasFinOme)
            console.log('ExtrasPrematuras',ExtrasPrematuras)
            console.log("SEMANA DEL AÑO:: HorasDeCadaSemana[props.HorasDeCadaSemana[0]]", props.HorasDeCadaSemana[props.HorasDeCadaSemana[0]]);

            console.log('data.TrabajadasSemana',data.TrabajadasSemana) // es mayor a 0 si ya cumplio las horas de semana
            console.log('data.TrabajadasHooy',data.TrabajadasHooy) //si reporto hoy
            console.log('TemporalDiaAnterior',data.TemporalDiaAnterior) // si reporto ayer
            console.log('%cFIN: CuandoEmpiezaExtra', "color:blue;font-family:system-ui;font-size:15px;-webkit-text-stroke: 0.5px black;font-weight:bold")
        }

        if(CuandoEmpiezaExtra >= horasFinOme){
            if(data.MostrarConsole.CuandoEiezaExtra){
                console.log('horasFinOme',horasFinOme);
                console.log('CuandoEmpiezaExtra',CuandoEmpiezaExtra);
                console.log('horasFinOme',horasFinOme);
            }

            form.diurnas = Math.abs(calcularDiurnas(data,form,form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
            form.nocturnas = Math.abs(calcularNocturnas(data,form,form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
        }else{ //extras

            // form.almuerzo = form.horas_trabajadas > 16 ? 2 : form.almuerzo;
            // form.almuerzo = form.horas_trabajadas + data.TemporalDiaAnterior > 8 ?  1 : 0;
            // form.almuerzo = form.horas_trabajadas + data.TemporalDiaAnterior > 16 ? 2 : form.almuerzo;


            if(CuandoEmpiezaExtra >= 24){
                CuandoEmpiezaExtra = 24 //version horasmismodia
                // CuandoEmpiezaExtra -= 24 //version diaAyB
                ExtrasManana = true
            }

            let horasExtrasDiurnas = (calcularDiurnas(data,form,form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra));
            form.extra_diurnas = horasExtrasDiurnas[0];
            form.diurnas = horasExtrasDiurnas[1];

            let horasExtrasNocturnas = (calcularNocturnas(data,form,form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra));
            form.extra_nocturnas = horasExtrasNocturnas[0];
            form.nocturnas = horasExtrasNocturnas[1];
        }
        // if(data.estado2359){
        //     if (form.nocturnas < 8 && form.nocturnas >= 0) {
        //         form.nocturnas++
        //     } else
        //       form.extra_nocturnas++
        // }


        setDominical(data,form,ini,fin,CuandoEmpiezaExtra,ExtrasManana,FestivosColombia,message);
        if(data.estado2359){
        // if(horafin === 23 && minfin === 59){
            if(form.extra_nocturnas > 0) form.extra_nocturnas++
            else{
                if(form.dominical_nocturnas > 0) form.dominical_nocturnas++
                else{
                    if(form.dominical_extra_nocturnas > 0) form.dominical_extra_nocturnas++
                    else{
                        form.nocturnas++
                    }
                }
            }
        }
    }
}
//</editor-fold>


