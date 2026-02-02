/*

calcularDiurnas
calcularNocturnas
*/
//FIN INDEX

//<editor-fold desc="CALCULAR">
import {calcularTerminaDomingo, calcularTerminaLunes, estaFechaEsFestivo} from "./HelpingCreate";
import {consolelog} from "../Pages/Reportes/ComunCreateReporte";


export function calcularDiurnas(Inicio, Fin,CuandoEmpiezaExtra, empiezanNocturnas){
    const horasInicio = new Date(Inicio).getHours();//7--
    const horasFin = new Date(Fin).getHours();//20

    const DiaInicio = new Date(Inicio).getDate();
    const DiaFin = new Date(Fin).getDate();

    let BaseInicial = horasInicio >= 6 ? horasInicio : 6 //7

    if(DiaInicio === DiaFin){
        let HorasExtra = 0
        const BaseFinal = horasFin >= empiezanNocturnas ? empiezanNocturnas : horasFin //19
        let HorasDiurnas = BaseFinal - BaseInicial;
        HorasDiurnas = HorasDiurnas < 0 ? 0 : HorasDiurnas

        if(CuandoEmpiezaExtra !== null){
            if(CuandoEmpiezaExtra < empiezanNocturnas){
                let horasNormales = CuandoEmpiezaExtra - BaseInicial //horas no extra
                horasNormales = horasNormales < 0 ? 0 : horasNormales

                if(consolelog.dia){
                    console.log('horas no extra',horasNormales);
                }

                if(HorasDiurnas >= horasNormales){
                    HorasExtra = HorasDiurnas - horasNormales
                    HorasDiurnas = horasNormales
                }else{
                    //es dominical

                }
            }//cuando las horas extra >= empiezanNocturnas, no hay horas extra diurnas
        }
        if(consolelog.dia){
            console.log('extras y horas diurnas',[HorasExtra,HorasDiurnas]);
            console.log('BaseFinal',BaseFinal);
        }
        return [HorasExtra,HorasDiurnas];
    }
    else{
        //de un dia a otro
        let HorasDiurnasTotal : number
        let HorasExtra = 0

        if(horasFin <= 6){//termino en madrugada
            HorasDiurnasTotal = BaseInicial <= empiezanNocturnas ? empiezanNocturnas - BaseInicial : 0;

            if(CuandoEmpiezaExtra !== null){
                if(CuandoEmpiezaExtra < empiezanNocturnas && CuandoEmpiezaExtra > 6){
                    if(BaseInicial <= CuandoEmpiezaExtra){
                        HorasDiurnasTotal = CuandoEmpiezaExtra - BaseInicial;
                    }else{
                        HorasDiurnasTotal = CuandoEmpiezaExtra - BaseInicial;
                    }//falla en el caso de empiezanNocturnaspm -> 6am
                    HorasExtra = empiezanNocturnas - CuandoEmpiezaExtra
                }
            }
        }else{
            if(horasInicio <= empiezanNocturnas){
                HorasDiurnasTotal = empiezanNocturnas - horasInicio
            }else{
                if(CuandoEmpiezaExtra !== null ){
                    HorasDiurnasTotal = CuandoEmpiezaExtra - 6
                }else{
                    HorasDiurnasTotal = horasInicio == 23? 1 : 0
                }
            }

            if (CuandoEmpiezaExtra !== null) {
                if(CuandoEmpiezaExtra <= 6){
                    HorasExtra = horasFin > 6 ? horasFin - 6 : 0
                }else{
                    if (horasFin >= CuandoEmpiezaExtra) {
                        HorasExtra = horasFin - CuandoEmpiezaExtra
                    } else {
                        HorasExtra = CuandoEmpiezaExtra <= empiezanNocturnas ? empiezanNocturnas - CuandoEmpiezaExtra : 0
                        HorasExtra += horasFin >= 6 ? horasFin - 6 : 0
                    }
                }
            }
        }
        if(consolelog.dia){
        }
        return [HorasExtra, HorasDiurnasTotal]
    }
}

export function calcularNocturnas(Inicio, Fin,CuandoEmpiezaExtra, HORAS_ESTANDAR, empiezanNocturnas){
    const horasInicio: number = new Date(Inicio).getHours();
    let horasFin: number = new Date(Fin).getHours();

    const DiaInicio: number = new Date(Inicio).getDate();
    const DiaFin: number = new Date(Fin).getDate();
    
    
    let Madrugada:number = 0
    let Tarde:number = 0
    let Resta = horasFin - horasInicio;
    if(DiaInicio === DiaFin){
        if(horasInicio < 6 && horasFin <= 6){//solo de noche
            Madrugada = Resta;
        }else{
            if(horasInicio < 6){
                Madrugada = (6 - horasInicio);
            }
        }

        if(horasInicio >= empiezanNocturnas && horasFin >= empiezanNocturnas){//solo de noche
            Tarde = Resta;
        }else{
            if(horasFin > empiezanNocturnas){//si existan horas nocturnas, si no son 0
                Tarde = (horasFin - empiezanNocturnas);
            }
        }
    }
    else{
        // calcular madrugada
        if(horasInicio < 6 && horasFin <= 6 && horasInicio < horasFin){//solo de noche
            Madrugada = horasFin - horasInicio;
        }else{
            if(horasInicio < 6){
                Madrugada = (6 - horasInicio);
            }
            if(horasFin <= 6){
                Madrugada += horasFin;
            }else{
                Madrugada += (6)
            }
        }

        // calcular Tarde
        if(horasInicio >= empiezanNocturnas && horasFin >= empiezanNocturnas){//solo de noche
            Tarde = horasFin - horasInicio;
            //if(Tarde < 0) !mucho voleo
        }else{
            if(horasFin > empiezanNocturnas){//si existan horas nocturnas, si no son 0
                Tarde += (horasFin - empiezanNocturnas);
            }

            if(horasInicio > empiezanNocturnas){
                Tarde += (24 - horasInicio);
            }else{
                Tarde += (24 - empiezanNocturnas);
            }
        }
    }

    let HorasNoc:number = Madrugada + Tarde;
    if(consolelog.noche) {
        console.log("🚀🚀calcularNocturnas ~ HorasNoc: ", HorasNoc);
        
    }
    let extra:number = 0, ordinarias = 0;

    // --------------- calculo extra ---------------
    if (Resta < HORAS_ESTANDAR || CuandoEmpiezaExtra === null || typeof CuandoEmpiezaExtra === 'undefined' || CuandoEmpiezaExtra > 23) {
        return [0,HorasNoc]
    }else{
        if(CuandoEmpiezaExtra >= empiezanNocturnas){
            if(horasFin >= empiezanNocturnas){
                extra = horasFin >= CuandoEmpiezaExtra? horasFin - CuandoEmpiezaExtra : 0
                ordinarias = HorasNoc - extra
            }else{
                extra = 24 - CuandoEmpiezaExtra
                extra += Madrugada
                ordinarias = HorasNoc - extra
            }

        }else{//extras empiezan en la madrugada
            if(CuandoEmpiezaExtra <= 6){
                ordinarias = Madrugada + Tarde
                ordinarias = ordinarias > CuandoEmpiezaExtra ? CuandoEmpiezaExtra : ordinarias
                // ordinarias = CuandoEmpiezaExtra + Tarde
                extra = Madrugada >= CuandoEmpiezaExtra ? Madrugada - CuandoEmpiezaExtra : 0


            }else{//empiezan en hora diurna
                if(horasFin > empiezanNocturnas){
                    extra = Tarde
                    ordinarias = Madrugada
                    
                }else{
                    if(DiaInicio == DiaFin){
                        extra = 0
                        ordinarias = HorasNoc
                        if(consolelog.noche){
                            console.log("[extra, ordinarias]",[extra, ordinarias])
                            
                        }
                    }else{
                        ordinarias = horasInicio < 6 ? 6 - horasInicio : 0
                        extra = HorasNoc - ordinarias
                        if(consolelog.noche){
                            console.log("[extra, ordinarias]",[extra, ordinarias])
                            console.log("🚀🚀calcularNocturnas ~ HorasNoc: ", HorasNoc);
                            
                        }
                    }
                }
            }
        }
        if(consolelog.noche) {
            console.log("[extra, ordinarias]",[extra, ordinarias])
        }
        return [extra, ordinarias];
    }
}
//</editor-fold>


//PRIVATE FUNCTION
export function RestarAlmuarzo(form,data){
    let LIMITE_ALMUERZO:number = 8
    let HayAlmuerzo:number = 8
    if(data.almorzadasHooy){
        HayAlmuerzo -= data.TrabajadasHooy //se restan las que ya trabajo hoy
        HayAlmuerzo = HayAlmuerzo < 0 ? 0 : HayAlmuerzo
    }
    
    const fechain_i: Date = new Date(form.fecha_ini);
    const diaSemana:number = fechain_i.getDay();
    if (diaSemana === 5) {//viernes
        HayAlmuerzo -= 1
    }
    // if (diaSemana === 6) {//sabado
    //     HayAlmuerzo -= 2
    //     LIMITE_ALMUERZO -= 2
    // }
    let numerador = form.horas_trabajadas + data.TrabajadasHooy
    if(form.horas_trabajadas >= HayAlmuerzo){
        //  if (diaSemana === 5) {//viernes
        //     numerador += 1
        // }
        form.almuerzo = Math.floor((numerador) / LIMITE_ALMUERZO)

        if(form.almuerzo > 1){
            form.almuerzo = Math.floor((numerador) / 8)
        }
    }

    data.ValorRealalmuerzo = form.almuerzo
    form.almuerzo += ' horas'

    if(consolelog.MostrarAlmuersini){
        console.log("🚀🚀RestarAlmuarzo ~ data.ValorRealalmuerzo: ", data.ValorRealalmuerzo);
        console.log("🚀🚀RestarAlmuarzo ~ LIMITE_ALMUERZO: ", LIMITE_ALMUERZO);
        console.log("🚀🚀RestarAlmuarzo ~ numerador: ", numerador);
    }
    if(numerador > LIMITE_ALMUERZO && form.horas_trabajadas > 0) {
        form.horas_trabajadas -= data.ValorRealalmuerzo;

        //extras
        if(form.extra_nocturnas >= data.ValorRealalmuerzo && form.extra_nocturnas > form.extra_diurnas - 1){
            form.extra_nocturnas -= data.ValorRealalmuerzo
            form.almuerzo += ' nocturno'
            return true;
        }
        if(form.extra_diurnas >= data.ValorRealalmuerzo && form.extra_diurnas > form.nocturnas && form.extra_diurnas > form.diurnas) {
            form.extra_diurnas -= data.ValorRealalmuerzo
            form.almuerzo += ' diurno'
            return true;
        }

        if(form.nocturnas >= data.ValorRealalmuerzo && form.nocturnas > (form.diurnas - 1)){ //&& form.nocturnas > form.diurnas
            form.nocturnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' nocturno';
            return true;
        }
        if(form.diurnas >= data.ValorRealalmuerzo) {
            // form.diurnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' diurno'
            return true;
        }



        //domini
        //extra dominical
        if(form.dominical_extra_nocturnas >= data.ValorRealalmuerzo ){ //&& form.dominical_extra_nocturnas > form.dominical_extra_diurnas
            form.dominical_extra_nocturnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' dominical extra nocturno';
            return true;
        }
        if(form.dominical_extra_diurnas >= data.ValorRealalmuerzo) {
            form.dominical_extra_diurnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' dominical extra diurno'
            return true;
        }

        if(form.dominical_nocturnas >= data.ValorRealalmuerzo ) { //&& form.dominical_nocturnas > form.dominical_diurnas
            form.dominical_nocturnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' dominical nocturno';
            return true;
        }
        if(form.dominical_diurnas >= data.ValorRealalmuerzo) {
            form.dominical_diurnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' dominical'
            return true;
        }


    }
    form.almuerzo = 'No'
}


export function setDominical(data,form,ini,fin,CuandoEmpiezaExtra,ExtrasManana,FestivosColombia,message, empiezanNocturnas){//date,date,int,bool
    let esFestivo = estaFechaEsFestivo(new Date(ini),FestivosColombia);
    let esFestivo2 = estaFechaEsFestivo(new Date(fin),FestivosColombia);

    if(ini.getDay() === 0 || fin.getDay() === 0){
        form.dominicales = 'si'
        message.TextFestivo = 'Dominical'

        if(esFestivo || esFestivo2) message.TextFestivo += ' y festivo';
    }else{
        if(esFestivo || esFestivo2){
            form.dominicales = 'si'
            message.TextFestivo = 'Festivo'
        }else{
            form.dominicales = 'no'
        }
    }

    if((ini.getDay() === 0 && fin.getDay() === 0) || (esFestivo || esFestivo2)){
        form.dominical_diurnas = form.diurnas;
        form.diurnas = 0
        form.dominical_nocturnas = form.nocturnas;
        form.nocturnas = 0
        form.dominical_extra_diurnas = form.extra_diurnas;
        form.extra_diurnas = 0
        form.dominical_extra_nocturnas = form.extra_nocturnas;
        form.extra_nocturnas = 0
    }else{
        if(ini.getDay() === 0 || esFestivo){//    termina lunes
            calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana,form)
        }

        if( fin.getDay() === 0 || esFestivo2){ //termina domingo
            calcularTerminaDomingo(ini,fin,CuandoEmpiezaExtra,ExtrasManana,form, empiezanNocturnas)
        }
    }
    // RestarAlmuarzo(form,data)
    
}
