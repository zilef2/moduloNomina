/*

calcularDiurnas
calcularNocturnas
*/
//FIN INDEX

//<editor-fold desc="CALCULAR">
import {calcularTerminaDomingo, calcularTerminaLunes, estaFechaEsFestivo} from "./HelpingCreate";

export function calcularDiurnas(data,form,Inicio, Fin,CuandoEmpiezaExtra){
    const horasInicio = new Date(Inicio).getHours();
    const horasFin = new Date(Fin).getHours();

    const DiaInicio = new Date(Inicio).getDate();
    const DiaFin = new Date(Fin).getDate();

    let BaseInicial = horasInicio >= 6 ? horasInicio : 6

    if(DiaInicio === DiaFin){
        let HorasExtra = 0
        const BaseFinal = horasFin >= 21 ? 21 : horasFin
        let HorasDiurnas = BaseFinal - BaseInicial;
        HorasDiurnas = HorasDiurnas < 0 ? 0 : HorasDiurnas

        if(data.MostrarConsole.dia){
            console.log('BaseFinal',BaseFinal)
            console.log('HorasDiurnas',HorasDiurnas)
        }

        if(CuandoEmpiezaExtra !== null){
            if(CuandoEmpiezaExtra < 21){
                let horasNormales = CuandoEmpiezaExtra - BaseInicial //horas no extra
                horasNormales = horasNormales < 0 ? 0 : horasNormales

                if(data.MostrarConsole.dia){
                    console.log('HorasDiurnas',HorasDiurnas)
                    console.log('horasNormales',horasNormales)
                }

                if(HorasDiurnas >= horasNormales){
                    HorasExtra = HorasDiurnas - horasNormales
                    HorasDiurnas = horasNormales
                }else{
                    //es dominical
                    // console.log('HorasDiurnas',HorasDiurnas)
                    // console.log('horasNormales',horasNormales)

                }
            }//cuando las horas extra >= 21, no hay horas extra diurnas
        }
        if(data.MostrarConsole.dia){
            console.log("HorasExtra & ordinarias, su suma", HorasExtra,HorasDiurnas,(HorasExtra + HorasDiurnas)); //nottemp
            console.log("CuandoEmpiezaExtra", CuandoEmpiezaExtra)
        }
        return [HorasExtra,HorasDiurnas];
    }
    else{
        //de un dia a otro
        let HorasDiurnasTotal = 0
        let HorasExtra = 0

        if(horasFin <= 6){//termino en madrugada
            HorasDiurnasTotal = BaseInicial <= 21 ? 21 - BaseInicial : 0;

            if(CuandoEmpiezaExtra !== null){
                if(CuandoEmpiezaExtra < 21 && CuandoEmpiezaExtra > 6){
                    if(BaseInicial <= CuandoEmpiezaExtra){
                        HorasDiurnasTotal = CuandoEmpiezaExtra - BaseInicial;
                    }else{
                        HorasDiurnasTotal = CuandoEmpiezaExtra - BaseInicial;
                    }//falla en el caso de 21pm -> 6am
                    HorasExtra = 21 - CuandoEmpiezaExtra
                }
            }
        }else{
            if(horasInicio <= 21){
                HorasDiurnasTotal = 21 - horasInicio
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
                        HorasExtra = CuandoEmpiezaExtra <= 21 ? 21 - CuandoEmpiezaExtra : 0
                        HorasExtra += horasFin >= 6 ? horasFin - 6 : 0
                    }
                }
            }
        }
        if(data.MostrarConsole.dia){
            console.log("🧈🧈🧈 DIA HorasExtra & ordinarias", HorasExtra,HorasDiurnasTotal);
        }
        return [HorasExtra, HorasDiurnasTotal]
    }
}

export function calcularNocturnas(data,form,Inicio, Fin,CuandoEmpiezaExtra, HORAS_ESTANDAR){
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

        if(horasInicio >= 21 && horasFin >= 21){//solo de noche
            Tarde = Resta;
        }else{
            if(horasFin > 21){//si existan horas nocturnas, si no son 0
                Tarde = (horasFin - 21);
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
        if(horasInicio >= 21 && horasFin >= 21){//solo de noche
            Tarde = horasFin - horasInicio;
            //if(Tarde < 0) !mucho voleo
        }else{
            if(horasFin > 21){//si existan horas nocturnas, si no son 0
                Tarde += (horasFin - 21);
            }

            if(horasInicio > 21){
                Tarde += (24 - horasInicio);
            }else{
                Tarde += (24 - 21);
            }
        }
    }

    let HorasNoc:number = Madrugada + Tarde;
    if(data.MostrarConsole.noche) {
        // console.clear()
        console.log("PRIMERO NOCHE 🚀");
        console.log("Madrugada & tarde🚀", Madrugada,Tarde,' = ',HorasNoc);
        console.log("horasInicio🚀", horasInicio);
        console.log("horasFin🚀", horasFin);
    }
    let extra:number = 0, ordinarias = 0;

    // --------------- calculo extra ---------------
    if (Resta < HORAS_ESTANDAR || CuandoEmpiezaExtra === null || typeof CuandoEmpiezaExtra === 'undefined' || CuandoEmpiezaExtra > 23) {
        return [0,HorasNoc]
    }else{
        if(CuandoEmpiezaExtra >= 21){
            if(horasFin >= 21){
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

                if(data.MostrarConsole.noche)
                    console.log("‍😶‍🌫️1 : extra & noche", extra,ordinarias);

            }else{//empiezan en hora diurna
                if(horasFin > 21){
                    extra = Tarde
                    ordinarias = Madrugada

                    if(data.MostrarConsole.noche)
                        console.log("😶‍🌫️2  extra  ordinarias", extra,ordinarias);

                }else{
                    if(DiaInicio == DiaFin){
                        extra = 0
                        ordinarias = HorasNoc
                        if(data.MostrarConsole.noche){
                            console.log("😶‍🌫️1  extra  ordinarias", extra,ordinarias);
                        }
                    }else{
                        ordinarias = horasInicio < 6 ? 6 - horasInicio : 0
                        extra = HorasNoc - ordinarias
                        if(data.MostrarConsole.noche){
                            console.log("imposible 😶‍🌫️0  extra  ordinarias", extra,ordinarias);
                        }
                    }
                }
            }
        }
        if(data.MostrarConsole.noche) {
            console.log("extra🚀", extra);
            console.log("ordinarias🚀", ordinarias);
        }
        return [extra, ordinarias];
    }
}
//</editor-fold>


//PRIVATE FUNCTION
function RestarAlmuarzo(form,data){
    let LIMITE_ALMUERZO:number = 8
    let HayAlmuerzo:number = 8
    HayAlmuerzo -= data.TrabajadasHooy
    // LIMITE_ALMUERZO = LIMITE_ALMUERZO - data.TrabajadasHooy < 0 ? 0 : LIMITE_ALMUERZO - data.TrabajadasHooy

    const fechain_i: Date = new Date(form.fecha_ini);
    const diaSemana:number = fechain_i.getDay();
    if (diaSemana === 6) {//sabado
        HayAlmuerzo -= 2
        LIMITE_ALMUERZO -= 2
    }
    let numerador = form.horas_trabajadas + data.TrabajadasHooy
    // -9
    if(form.horas_trabajadas >= HayAlmuerzo){
        form.almuerzo = Math.floor((numerador) / LIMITE_ALMUERZO)

        if(form.almuerzo > 1){
            form.almuerzo = Math.floor((form.horas_trabajadas + data.TrabajadasHooy) / 8)
        }
    }

    data.ValorRealalmuerzo = form.almuerzo
    form.almuerzo += ' horas'

    if(numerador > LIMITE_ALMUERZO) {
        form.horas_trabajadas -= data.ValorRealalmuerzo;

        if(data.MostrarAlmuersini){
            console.log('%cLIMITE_ALMUERZO', "color:red;font-family:system-ui;font-size:1rem;-webkit-text-stroke: 0.5px black;font-weight:bold")
            console.log("=>(HelpingCreate.ts:209) form.almuerzo", form.almuerzo);
            console.log("(F=>LIMITE_ALMUERZO) ", LIMITE_ALMUERZO);
            console.log("achus data.ValorRealalmuerzo) ", data.ValorRealalmuerzo);
            // console.log("(F=>estarAlmuarzo) nocturnas", form.nocturnas);
            // console.log("(F=>estarAlmuarzo) diurnas", form.diurnas);
            // console.log("(F=>estarAlmuarzo) extra_diurnas", form.extra_diurnas);
            // console.log("(F=>estarAlmuarzo) extra_nocturnas", form.extra_nocturnas);
            console.log('%cFIN LIMITE_ALMUERZO', "color:blue;font-family:system-ui;font-size:15px;-webkit-text-stroke: 0.5px black;font-weight:bold")
        }

        //extras
        if(form.extra_nocturnas >= data.ValorRealalmuerzo){ // && form.extra_nocturnas > form.extra_diurnas
            form.extra_nocturnas -= data.ValorRealalmuerzo
            form.almuerzo += ' nocturno'
            // console.log('se quita noctura')
            return true;
        }
        if(form.extra_diurnas >= data.ValorRealalmuerzo){ //todo: hay ocasiones donde no se va a poder restar
            form.extra_diurnas -= data.ValorRealalmuerzo
            form.almuerzo += ' diurno'
            // console.log('ED - Almuerzo')
            return true;
        }

        if(form.nocturnas >= data.ValorRealalmuerzo){ //&& form.nocturnas > form.diurnas
            form.nocturnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' nocturno';
            // console.log(" form.nocturnas", form.nocturnas);
            return true;
        }
        if(form.diurnas >= data.ValorRealalmuerzo) {
            form.diurnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' diurno'
            // console.log(" form.diurnas", form.diurnas);
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
            // console.log("=>(HelpingCreate2.ts:277) form.almuerzo", form.almuerzo);
            return true;
        }
        if(form.dominical_diurnas >= data.ValorRealalmuerzo) {
            form.dominical_diurnas -= data.ValorRealalmuerzo;
            form.almuerzo += ' dominical'
            // console.log("=>(HelpingCreate2.ts:284) form.almuerzo", form.almuerzo);
            return true;
        }


    }
    form.almuerzo = 'No'
}


export function setDominical(data,form,ini,fin,CuandoEmpiezaExtra,ExtrasManana,FestivosColombia,message){//date,date,int,bool
    let esFestivo = estaFechaEsFestivo(new Date(ini),data.MostrarConsole,FestivosColombia);
    let esFestivo2 = estaFechaEsFestivo(new Date(fin),data.MostrarConsole,FestivosColombia);

    if(ini.getDay() === 0 || fin.getDay() === 0){
        form.dominicales = 'si'
        console.log('%cHelpingCreate2 form.dominicales: '+form.dominicales, "color:yellow;font-family:system-ui;font-size:22px;")
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
        console.log("=>(Create.vue:308) form.nocturnas", form.nocturnas);
        form.nocturnas = 0
        form.dominical_extra_diurnas = form.extra_diurnas;
        form.extra_diurnas = 0
        form.dominical_extra_nocturnas = form.extra_nocturnas;
        form.extra_nocturnas = 0
            console.log('aquiEntra')
    }else{
        if(ini.getDay() === 0 || esFestivo){//    termina lunes
            calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana,form)
        }

        if( fin.getDay() === 0 || esFestivo2){ //termina domingo
            calcularTerminaDomingo(ini,fin,CuandoEmpiezaExtra,ExtrasManana,form)
        }
    }
    RestarAlmuarzo(form,data)
}
