<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { useForm } from '@inertiajs/vue3';
import {ref, watchEffect, reactive, onMounted, watch} from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import FestivosColombia from 'festivos-colombia';
import {weekNumber} from "@/global";

const props = defineProps({
    show: Boolean,
    title: String,
    valoresSelect:Object,
    IntegerDefectoSelect: Number,
    horasemana: Number,
    startDateMostrar: String,
    endDateMostrar: String,
    numberPermissions: Number,
    ultimoReporte: Number,

    horasTrabajadasHoy: Array, //es por si reportaron el mismo dia
    HorasDeCadaSemana: Array,
})
let label_diurnas = ref(null)


// <!--<editor-fold desc="onMounted - Data - const - useForm">-->
const MAXIMO_HORAS_SEMANALES = 48 //todo: traer de parametro
const HORAS_ESTANDAR = 9
const HORAS_SEMANALES_MENOS_ESTANDAR = MAXIMO_HORAS_SEMANALES - HORAS_ESTANDAR
const LimiteHorasTrabajadas = 23
let ValorRealalmuerzo = 0 //truco para recuperar las horas de almuerzo, cuando se va a maandar el form

onMounted(() => {
  data.TrabajadasSemana = props.HorasDeCadaSemana[props.HorasDeCadaSemana[0]] > HORAS_SEMANALES_MENOS_ESTANDAR ?
      props.HorasDeCadaSemana[props.HorasDeCadaSemana[0]] - HORAS_SEMANALES_MENOS_ESTANDAR : 0

  data.TrabajadasSemana = data.TrabajadasSemana > HORAS_ESTANDAR ? HORAS_ESTANDAR : data.TrabajadasSemana

  if(localStorage.getItem('centroCostoId')){
    form.centro_costo_id = localStorage.getItem('centroCostoId')
  }
});

const emit = defineEmits(["close"]);
const data = reactive({
    respuestaSeguro:'',
    startTime: { hours: 7, minutes: 0 },
    estado2359:false,
    TrabajadasHooy:0,
    TrabajadasSemana:0,
    diaini:1,
    MensajeError:'',
    MostrarConsole:{
        watchEffect:false,
        horas:true,
        dia:false,
        noche:false,
        extradia:false,
        extranoche:false,
        dominicales:false,
        terminaDomingo:false,
        terminaLunes:false,
        EsFestivo:false,

    }

})

const form = useForm({
    fecha_ini: '', fecha_fin: '',
    // fecha_ini: '2023-04-0'+diaoDominicla+'T'+horas[0]+':00', fecha_fin: '2023-04-0'+(diaoDominicla)+'T'+horas[1]+':00', //temp
    // fecha_ini: '2023-06-01T23:00', fecha_fin: '2023-06-01T23:59', //temp

    centro_costo_id: props.IntegerDefectoSelect,
    observaciones: '',
    horas_trabajadas: '',
    almuerzo: '0',

    diurnas: 0,
    nocturnas: 0,
    extra_diurnas: 0,
    extra_nocturnas: 0,

    dominicales: 'no',
    extra: 'no',
    esFestivo: false,

    dominical_diurnas: 0,
    dominical_nocturnas: 0,
    dominical_extra_diurnas: 0,
    dominical_extra_nocturnas: 0
});


if(props.numberPermissions > 8){
    let hora = new Date()
    hora = hora.getHours()
    form.fecha_ini = '2024-01-03T0'+(hora+2)+':00'
    form.fecha_fin = '2024-01-03T'+(hora+12)+':00'
    // form.fecha_fin = '2024-01-04T23:58'
}
// <!--</editor-fold>-->


// <!--<editor-fold desc="Calcular">-->
function estaFechaEsFestivo(fecha){
    let dateFestivos,dateArr,daysfestivo,monthFestivo,result = false;
    var BreakException = {};
    let dayEvaluado = Math.floor(fecha.getDate());
    let MonthEvaluado = (fecha.getMonth());

    if(data.MostrarConsole.EsFestivo){
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
        // if (e !== BreakException) throw e;
    }

    return result
}


let TextFestivo = ''
let Madrugada, Tarde //variables internas para calculo de horas nocturnas

const Reporte11_59 = () => {
    let fin = Date.parse(form.fecha_fin);
    let finDate = new Date(fin);
    const horafin = finDate.getHours()
    const minfin = finDate.getMinutes()
    if(horafin === 23 && minfin === 59){
    //     form.horas_trabajadas++
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

function calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana){
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
        if(form.nocturnas === 0){//hay extra (todo: validar, )
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

function calcularTerminaDomingo(ini,fin,CuandoEmpiezaExtra,ExtrasManana){
    const HoraIni = parseInt(ini.getHours())
    const HoraTermino = parseInt(fin.getHours())
    // if(typeof CuandoEmpiezaExtra !== 'undefined')
    if(CuandoEmpiezaExtra !== null){
        if(CuandoEmpiezaExtra < 6){
            form.dominical_extra_nocturnas = Madrugada - CuandoEmpiezaExtra //Siempre Madrugada > CuandoEmpiezaExtra
            form.extra_nocturnas -= form.dominical_extra_nocturnas
            form.dominical_nocturnas = CuandoEmpiezaExtra
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

function calcularDominicales(ini,fin,CuandoEmpiezaExtra,ExtrasManana){//date,date,int,bool
    let esFestivo = estaFechaEsFestivo(new Date(ini));
    let esFestivo2 = estaFechaEsFestivo(new Date(fin));
    // console.log("ini festivo - fin festivo :", esFestivo,esFestivo2);

    if(ini.getDay() === 0 || fin.getDay() === 0){
        form.dominicales = 'si'
        TextFestivo = 'Dominical'

        if(esFestivo || esFestivo2) TextFestivo = ' y festivo';
    }else{
        if(esFestivo || esFestivo2){
            form.dominicales = 'si'
            TextFestivo = 'Festivo'
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
            calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana) //todo: necesita el ini
        }

        if( fin.getDay() === 0 || esFestivo2){ //termina domingo
            calcularTerminaDomingo(ini,fin,CuandoEmpiezaExtra,ExtrasManana)
        }
    }

    //TODO: aqui solo es dominicales //TODO: restar el almuerzo a el valor mayor entre los 8 (dia,noc,extras,domin)
    if(form.horas_trabajadas > 9){
        form.horas_trabajadas -= 1;
        if(form.diurnas > 0 && form.nocturnas <= form.diurnas){
            form.diurnas -= 1
            form.almuerzo += ' diurno'
        }else{
            if(form.nocturnas > 0 && form.nocturnas > form.diurnas){
                form.nocturnas -= 1
                form.almuerzo += ' nocturno'
            }
        }
    }
}

//# papa = watchEffect
function calcularHoras(inicio,final){
    let ini = new Date(inicio)
    let fin = new Date(final)
    let ExtrasManana = false

    let TemporalDiaAnterior = 0
    TemporalDiaAnterior += props.ultimoReporte
    let ExtrasPrematuras = HORAS_ESTANDAR

    let Dateinii = Date.parse(form.fecha_ini) ?? false;
    if(Dateinii && Date.parse(form.fecha_fin) ){ // son fechas?
      let horasInicioome = parseInt(new Date(form.fecha_ini).getHours())
      let horasFinOme = parseInt(new Date(form.fecha_fin).getHours())
      let CuandoEmpiezaExtra = horasInicioome


      ExtrasPrematuras -= data.TrabajadasSemana
      ExtrasPrematuras -= data.TrabajadasHooy
      ExtrasPrematuras -= TemporalDiaAnterior
      ExtrasPrematuras = ExtrasPrematuras < 0 ? 0 : ExtrasPrematuras

      CuandoEmpiezaExtra += ExtrasPrematuras
      CuandoEmpiezaExtra = CuandoEmpiezaExtra < horasInicioome ? horasInicioome : CuandoEmpiezaExtra


        if(data.MostrarConsole.horas){
            console.log('CuandoEmpiezaExtra',CuandoEmpiezaExtra)
            console.log('extras?',CuandoEmpiezaExtra < horasFinOme)
            console.log('ExtrasPrematuras',ExtrasPrematuras)
            console.log('data.TrabajadasHooy',data.TrabajadasHooy)
            console.log('TemporalDiaAnterior',TemporalDiaAnterior)
            console.log('data.TrabajadasSemana',data.TrabajadasSemana)
        }

        if(CuandoEmpiezaExtra >= horasFinOme){
            form.almuerzo = 'No';
            form.diurnas = Math.abs(calcularDiurnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
            form.nocturnas = Math.abs(calcularNocturnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
        }else{ //extras
            form.almuerzo = form.horas_trabajadas + TemporalDiaAnterior > 8 ?  1 : 0;
            form.almuerzo = form.horas_trabajadas + TemporalDiaAnterior > 16 ? 2 : form.almuerzo;
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
        calcularDominicales(ini,fin,CuandoEmpiezaExtra,ExtrasManana);
    }
}

function calcularDiurnas(Inicio, Fin,CuandoEmpiezaExtra){
    const horasInicio = new Date(Inicio).getHours();
    const horasFin = new Date(Fin).getHours();

    const DiaInicio = new Date(Inicio).getDate();
    const DiaFin = new Date(Fin).getDate();

    let BaseInicial = horasInicio >= 6 ? horasInicio : 6

    if(DiaInicio == DiaFin){
        let HorasExtra = 0
        const BaseFinal = horasFin >= 21 ? 21 : horasFin
        let HorasDiurnas = BaseFinal - BaseInicial;
        HorasDiurnas = HorasDiurnas < 0 ? 0 : HorasDiurnas

        if(CuandoEmpiezaExtra !== null){
            if(CuandoEmpiezaExtra < 21){
                let horasNormales = CuandoEmpiezaExtra - BaseInicial
                horasNormales = horasNormales < 0 ? 0 : horasNormales
                if(HorasDiurnas >= horasNormales){
                    HorasExtra = HorasDiurnas - horasNormales
                    HorasDiurnas = horasNormales
                }else{
                    console.log('imposible!')
                }
            }//cuando las horas extra >= 21, no hay horas extra diurnas
        }
        if(data.MostrarConsole.dia){
            console.log("HorasExtra & ordinarias", HorasExtra,HorasDiurnas,(HorasExtra + HorasDiurnas)); //nottemp
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

function calcularNocturnas(Inicio, Fin,CuandoEmpiezaExtra){
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
        console.log("Madrugada & tarde🚀", Madrugada,Tarde+' = '+(HorasNoc));
        console.log("CuandoEmpiezaExtra🚀", CuandoEmpiezaExtra);
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
                ordinarias = CuandoEmpiezaExtra + Tarde
                extra = Madrugada >= CuandoEmpiezaExtra ? Madrugada - CuandoEmpiezaExtra : 0
                console.log("🚀 1 : extra & noche", extra,ordinarias);

            }else{//empiezan en hora diurna
                if(horasFin > 21){
                    extra = Tarde
                    ordinarias = Madrugada

                    if(data.MostrarConsole.noche){
                        console.log("😶‍🌫️2  extra  ordinarias", extra,ordinarias);
                    }
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
// <!--</editor-fold>-->

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        let Dateinii = Date.parse(form.fecha_ini);
        if(Dateinii && Date.parse(form.fecha_fin) ){ // son fechas?
          data.diaini = parseInt(new Date(form.fecha_ini).getDate())
          data.TrabajadasHooy = (props.horasTrabajadasHoy[data.diaini]) ?? 0
          data.TrabajadasHooy = parseInt(data.TrabajadasHooy) ?? 0



          let ini = Date.parse(form.fecha_ini);
          let fin = Date.parse(form.fecha_fin);

          let WeekN = weekNumber(new Date(form.fecha_ini))
              // 39 => 48 - 9
          data.TrabajadasSemana = props.HorasDeCadaSemana[WeekN] > HORAS_SEMANALES_MENOS_ESTANDAR ?
              props.HorasDeCadaSemana[WeekN] - HORAS_SEMANALES_MENOS_ESTANDAR : 0
          data.TrabajadasSemana = data.TrabajadasSemana > HORAS_ESTANDAR ? HORAS_ESTANDAR : data.TrabajadasSemana
          console.log(data.TrabajadasSemana)


          form.horas_trabajadas = 0
          form.almuerzo = 0

          form.diurnas = 0
          form.nocturnas = 0
          form.extra_diurnas = 0
          form.extra_nocturnas = 0
          form.dominical_diurnas = 0
          form.dominical_nocturnas = 0
          form.dominical_extra_diurnas = 0
          form.dominical_extra_nocturnas = 0
          form.horas_trabajadas = parseInt((fin - ini)/(3600*1000));


          //si es 11:59 minutos -> agrega una hora
          if(data.estado2359){
              form.horas_trabajadas++
              if(data.MostrarConsole.watchEffect){
                  console.log('form.horas_trabajadas',form.horas_trabajadas)
              }
          }

            if(ini > fin){ //jaja no jodas
                form.errors.horas_trabajadas = 'La fecha inicial no debe ser posterior a la final'
            }else{
                if(form.horas_trabajadas >= LimiteHorasTrabajadas){
                    form.errors.horas_trabajadas = 'Las horas trabajadas son demasiadas'
                    form.horas_trabajadas = "0"
                    form.diurnas = "0"
                    form.nocturnas = "0"
                    form.almuerzo = "0"
                }else{
                    const DiaInicio = new Date(ini).getDate();
                    const DiaFin = new Date(fin).getDate();
                    if(DiaInicio !== DiaFin){
                        form.errors.horas_trabajadas = 'Debe realizar dos reportes'
                        form.horas_trabajadas = "0"
                        form.diurnas = "0"
                        form.nocturnas = "0"
                        form.almuerzo = "0"
                    }else{
                        /*
                            la ini < fin
                            horas < LimiteHorasTrabajadas
                            Diainicio == dia final
                        */
                        calcularHoras(ini,fin);
                    }
                }
            }
        }
    }
})

watch(() => form.centro_costo_id, (newX) => {
    localStorage.setItem('centroCostoId',newX)
})
watch(() => form.fecha_ini, (newX) => {
    form.fecha_fin = newX
})
watch(() => form.fecha_fin, (newX) => {
  let FechaFini = new Date(newX)
  let HoraFini = FechaFini.getHours() + ':' + FechaFini.getMinutes()
  if(HoraFini === '23:59'){
    data.estado2359 = true
  }else{
    if(data.estado2359)data.estado2359 = false
  }
})

const create = () => {
    data.MensajeError = ''
    data.respuestaSeguro = true
    if(form.horas_trabajadas <= LimiteHorasTrabajadas && form.horas_trabajadas !== 0){
      if (Object.keys(form.errors).length === 0) {
          if(props.numberPermissions < 9) {
            data.respuestaSeguro = confirm("¿Estás seguro de enviar el formulario?");
          }
          if(data.respuestaSeguro){
            Reporte11_59();
            form.almuerzo = ValorRealalmuerzo
            form.post(route('Reportes.store'), {
              preserveScroll: true,
              onSuccess: () => {
                emit("close")
                form.reset()
              },
              onError: () =>{
                alert(JSON.stringify(form.errors, null, 4));
              },
              onFinish: () => null,
            })
        }
      }else{
        data.MensajeError = 'Verifique de nuevo'

      }
    }else{
        data.MensajeError = 'Horas invalidas'
  }
}

const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'4xl'">
            <form @submit.prevent="create" class="p-6 mb-12">
                <div class="flex space-x-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        <b>
                            <!-- {{ lang().label.add }} -->
                             {{ props.title }}
                        </b>
                    </h2>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Horas semana: {{ props?.horasemana }}
                    </h2>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ props?.startDateMostrar }} -
                        {{ props?.endDateMostrar }}
                    </h2>
                </div>
                <div class="my-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini" />
                        <VueDatePicker :start-time="data.startTime" :is-24="false" :day-names="daynames" auto-apply  :enable-time-picker="true"
                            id="fecha_ini" type="date" class="mt-1 block w-full" v-model="form.fecha_ini" required
                            :placeholder="lang().placeholder.fecha_ini" :error="form.errors.fecha_ini" />
                        <InputError class="mt-2" :message="form.errors.fecha_ini" />
                    </div>
                    <div>
                        <InputLabel for="fecha_fin" :value="lang().label.fecha_fin" />
                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"
                            id="fecha_fin" type="date" class="mt-1 block w-full" v-model="form.fecha_fin" required
                            :placeholder="lang().placeholder.fecha_fin" :error="form.errors.fecha_fin" />
                        <InputError class="mt-2" :message="form.errors.fecha_fin" />
                    </div>
                    <div>
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas" />
                        <TextInput id="horas_trabajadas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 block w-full"
                                   v-model="form.horas_trabajadas" disabled
                            :placeholder="lang().placeholder.horas_trabajadas" :error="form.errors.horas_trabajadas" />
                        <InputError class="bg-gray-100 dark:bg-gray-700 mt-2" :message="form.errors.horas_trabajadas" />
                    </div>

                    <div>
                        <InputLabel for="almuerzo" :value="lang().label.horacomida + ' (+9 horas)'" />
                        <TextInput id="almuerzo" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.almuerzo" disabled
                            :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo" />
                    </div>
                    <!-- mt-80 -->
                    <div class="mt-4 grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="diurnas" :value="lang().label.diurnas" />
                            <TextInput id="diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.diurnas" disabled
                                :placeholder="lang().placeholder.diurnas" :error="form.errors.diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="nocturnas" :value="lang().label.nocturnas" />
                            <TextInput id="nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.nocturnas" disabled
                                :placeholder="lang().placeholder.nocturnas" :error="form.errors.nocturnas" />
                        </div>
                    </div>
                    <!-- mt-80 -->
                    <div v-if="form.extra_diurnas || form.extra_nocturnas || form.dominicales == 'si'" class="mt-4 grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="extra_diurnas" :value="lang().label.extra_diurnas" />
                            <TextInput id="extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_diurnas" disabled
                                :placeholder="lang().placeholder.extra_diurnas" :error="form.errors.extra_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="extra_nocturnas" :value="lang().label.extra_nocturnas" />
                            <TextInput id="extra_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.extra_nocturnas" disabled
                                :placeholder="lang().placeholder.extra_nocturnas" :error="form.errors.extra_nocturnas" />
                        </div>
                    </div>
                    <!-- dominicales -->
                    <div v-if="form.dominicales == 'si'" class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_diurnas" for="dominical_diurnas" :value="lang().label.dominical_diurnas" />
                            <TextInput id="dominical_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_diurnas" disabled
                                :placeholder="lang().placeholder.dominical_diurnas" :error="form.errors.dominical_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_nocturnas" for="dominical_nocturnas" :value="lang().label.dominical_nocturnas" />
                            <TextInput id="dominical_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_nocturnas" disabled
                                :placeholder="lang().placeholder.dominical_nocturnas" :error="form.errors.dominical_nocturnas" />
                        </div>
                    </div>
                    <div v-if="form.dominicales == 'si'" class="grid grid-cols-2 gap-6">
                        <div>
                            <InputLabel ref="label_extra_diurnas" for="dominical_extra_diurnas" :value="lang().label.dominical_extra_diurnas" />
                            <TextInput id="dominical_extra_diurnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_extra_diurnas" disabled
                                :placeholder="lang().placeholder.dominical_extra_diurnas" :error="form.errors.dominical_extra_diurnas" />
                        </div>
                        <div>
                            <InputLabel ref="label_extra_nocturnas" for="dominical_extra_nocturnas" :value="lang().label.dominical_extra_nocturnas" />
                            <TextInput id="dominical_extra_nocturnas" type="number" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.dominical_extra_nocturnas" disabled
                                :placeholder="lang().placeholder.dominical_extra_nocturnas" :error="form.errors.dominical_extra_nocturnas" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="centro_costo_id" :value="lang().label.centro_costo_id" />
                        <SelectInput v-model="form.centro_costo_id" :dataSet="props.valoresSelect" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.centro_costo_id" />
                    </div>

                    <div v-if="form.dominicales === 'si'" class="mt-4">
                        <label class="dark:text-white">Horario</label>
                        <TextInput id="dominicales" type="text" class="bg-gray-100 dark:bg-gray-700 block w-full" v-model="TextFestivo" disabled />
                    </div>
                </div>
                <!-- <div class="my-6 ">
                    <InputLabel for="observaciones" :value="lang().label.observaciones" />
                        <textarea
                            id="observaciones" type="text" v-model="form.observaciones"
                            class="mt-1 block w-full rounded-md shadow-sm dark:bg-black dark:text-white placeholder:text-gray-400 placeholder:dark:text-gray-400/50"
                            cols="30" rows="3" :error="form.errors.observaciones">
                        </textarea>
                    <InputError class="mt-2" :message="form.errors.observaciones" />
                </div> -->

                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create" @keyup.enter="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
                <div class="flex justify-end my-3">
                    <p v-if="props.ultimoReporte > 0" class="mx-2">Hay pendientes {{ props.ultimoReporte }} horas (11:59pm)</p>
                    <p v-if="data.TrabajadasSemana > 0" class="mx-2">Horas extra (semana)</p>
<!--                    <p v-if="data.MensajeError !== ''" class="mx-2 text-red-600 text-lg"> {{ data.MensajeError }} </p>-->
                </div>
            </form>
        </Modal>
    </section>
</template>
