/*


TERMINA_LUNES_O_DOMINGO
calcularTerminaLunes
calcularTerminaDomingo

//FIN INDEX


export function estaFechaEsFestivo(
    fecha,dataMostrarConsole:boolean,FestivosColombia
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



//<editor-fold desc="TERMINA_LUNES_O_DOMINGO">
export function calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana,form){
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
export function calcularHoras(data,form,inicio,final){
    let ini = new Date(inicio)
    let fin = new Date(final)
    let ExtrasManana = false

    data.TemporalDiaAnterior = data.HorasDelDiaAnterior59 ? data.HorasDelDiaAnterior59 : 0 //7jul: esta desactivado
    let ExtrasPrematuras = HORAS_ESTANDAR

    let Dateinii = Date.parse(form.fecha_ini) ?? false;
    if(Dateinii && Date.parse(form.fecha_fin) ){ //??: son fechas?
        let horasInicioome = parseInt(new Date(form.fecha_ini).getHours())
        let horasFinOme = parseInt(new Date(form.fecha_fin).getHours())
        let CuandoEmpiezaExtra = horasInicioome

        ExtrasPrematuras -= (data.TrabajadasSemana-1)//machete aqui
        let diaSemana = ini.getDay();
        // Verificar si el día es sábado (6)
        if (diaSemana === 6) {
            CuandoEmpiezaExtra -= 1
        }

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

            console.log('data.TrabajadasSemana',data.TrabajadasSemana) // si ya cumplio las horas de semana
            console.log('data.TrabajadasHooy',data.TrabajadasHooy) //si reporto hoy
            console.log('TemporalDiaAnterior',data.TemporalDiaAnterior) // si reporto ayer
            console.log('%cFIN: CuandoEmpiezaExtra', "color:blue;font-family:system-ui;font-size:15px;-webkit-text-stroke: 0.5px black;font-weight:bold")
        }


        if(CuandoEmpiezaExtra >= horasFinOme){
            if(data.MostrarConsole.dia)
                console.log(CuandoEmpiezaExtra,horasFinOme);

            form.almuerzo = 'No';
            form.diurnas = Math.abs(calcularDiurnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
            form.nocturnas = Math.abs(calcularNocturnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
        }else{ //extras
            form.almuerzo = form.horas_trabajadas + data.TemporalDiaAnterior > 8 ?  1 : 0;
            form.almuerzo = form.horas_trabajadas + data.TemporalDiaAnterior > 16 ? 2 : form.almuerzo;
            ValorRealalmuerzo = form.almuerzo
            form.almuerzo += ' horas'

            if(CuandoEmpiezaExtra >= 24){
                CuandoEmpiezaExtra = 24 //version horasmismodia
                // CuandoEmpiezaExtra -= 24 //version diaAyB
                ExtrasManana = true
            }

            let horasExtrasDiurnas = (calcularDiurnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra));
            form.extra_diurnas = horasExtrasDiurnas[0];
            form.diurnas = horasExtrasDiurnas[1];

            let horasExtrasNocturnas = (calcularNocturnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra));
            form.extra_nocturnas = horasExtrasNocturnas[0];
            form.nocturnas = horasExtrasNocturnas[1];
        }
        // if(data.estado2359){
        //     if (form.nocturnas < 8 && form.nocturnas >= 0) {
        //         form.nocturnas++
        //     } else
        //       form.extra_nocturnas++
        // }


        setDominical(ini,fin,CuandoEmpiezaExtra,ExtrasManana);
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
                    console.log('HorasDiurnas',HorasDiurnas)
                    console.log('horasNormales',horasNormales)

                }
            }//cuando las horas extra >= 21, no hay horas extra diurnas
        }
        if(data.MostrarConsole.dia){
            console.log("HorasExtra & ordinarias, su suma", HorasExtra,HorasDiurnas,(HorasExtra + HorasDiurnas)); //nottemp
            console.log("CuandoEmpiezaExtra", CuandoEmpiezaExtra)
        }
        return [HorasExtra,HorasDiurnas];
    }else{
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

export function calcularNocturnas(data,form,Inicio, Fin,CuandoEmpiezaExtra){
    const horasInicio = new Date(Inicio).getHours();
    const horasFin = new Date(Fin).getHours();

    const DiaInicio = new Date(Inicio).getDate();
    const DiaFin = new Date(Fin).getDate();

    Madrugada = 0
    Tarde = 0
    if(DiaInicio === DiaFin){
        if(horasInicio < 6 && horasFin <= 6){//solo de noche
            Madrugada = horasFin - horasInicio;
        }else{
            if(horasInicio < 6){
                Madrugada = (6 - horasInicio);
            }
        }

        if(horasInicio >= 21 && horasFin >= 21){//solo de noche
            Tarde = horasFin - horasInicio;
        }else{
            if(horasFin > 21){//si existan horas nocturnas, si no son 0
                Tarde = (horasFin - 21);
                console.log("=>(Create.vue:512) Tarde", Tarde);
            }
        }
    }else{
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

    let HorasNoc = Madrugada + Tarde;
    if(data.MostrarConsole.noche) {
        console.log("PRIMERO NOCHE 🚀");
        console.log("Madrugada & tarde🚀", Madrugada,Tarde+' = '+(HorasNoc));
        console.log("horasInicio🚀", horasInicio);
        console.log("horasFin🚀", horasFin);
    }
    let extra = 0, ordinarias = 0;

    // --------------- calculo extra ---------------
    if (CuandoEmpiezaExtra === null || typeof CuandoEmpiezaExtra === 'undefined' || CuandoEmpiezaExtra > 23) {
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



