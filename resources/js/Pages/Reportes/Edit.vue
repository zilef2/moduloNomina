<script setup>
import Modal from '@/Components/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import SelectInput from '@/Components/SelectInput.vue';

    import { useForm } from '@inertiajs/vue3';

    import { ref,watchEffect, reactive, watch } from 'vue';

    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'
import FestivosColombia from 'festivos-colombia';
import { TransformTdate } from '@/global.ts';


let CurrentlyYear = new Date().getFullYear()    
let holidays2022 = FestivosColombia.getHolidaysByYear(CurrentlyYear);
let label_diurnas = ref(null)
//constantes intuitivas
const MAXIMO_HORAS_SEMANALES = 48
const LimiteHorasTrabajadas = 22
const TrabajoConAlmuerzo = 9
//fin intuitivas
let TextFestivo = ''
let Madrugada, Tarde //variables internas para calculo de horas nocturnas
let ValorRealalmuerzo = 0

const props = defineProps({
    show: Boolean,
    title: String,
    Reporte: Object,
    valoresSelect: Object,
    showUsers: Object,
    correccionUsuario: Boolean,
})
const emit = defineEmits([ "close", ]);

const data = reactive({
    
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
})

const form = useForm({
    fecha_ini: '',
    fecha_fin: '',
    horas_trabajadas: '',
    centro_costo_id: '',
    observaciones: '',
    almuerzo: '',
    valido: '',

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

const update = () => {
    form.almuerzo = ValorRealalmuerzo

    if(props.correccionUsuario){
        form.valido = 0;
    }else{
        form.valido = 2;
    }
    //! todo 
    if(form.horas_trabajadas <= 48){
        form.put(route('Reportes.update', props.Reporte?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    }else{
        alert('Demasiadas horas')
    }
}

function estaFechaEsFestivo(fecha){
    let dateFestivos,dateArr,daysfestivo,monthFestivo,result = false;
    var BreakException = {};
    let dayEvaluado = Math.floor(fecha.getDate());
    let MonthEvaluado = (fecha.getMonth());
    try{
        holidays2022.forEach(element => {
            dateArr = element.date.split('/');
            dateFestivos = new Date(dateArr[2], dateArr[1] - 1, dateArr[0]);
            daysfestivo = Math.floor(dateFestivos.getDate());
            monthFestivo = (dateFestivos.getMonth());
            
            // console.log(element.date + " - " + element.name); 
            // console.log( 'festivo element:::', daysfestivo + '/'+monthFestivo,
            //     'fecha:::', dayEvaluado + '/'+MonthEvaluado
            // ); 

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

function calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana){
    form.dominical_diurnas = form.diurnas;
    form.dominical_extra_diurnas = form.extra_diurnas;
    const HoraTermino = parseInt(fin.getHours())

    if(HoraTermino <= 6){//termino madrugada
        form.diurnas = 0
        form.extra_diurnas = 0
        if(form.nocturnas == 0){
            form.dominical_nocturnas = form.nocturnas - HoraTermino;
            form.nocturnas = HoraTermino;
        }else{
            form.dominical_extra_nocturnas = form.extra_nocturnas - HoraTermino;
            form.extra_nocturnas = HoraTermino
        }
    }else{// termino de dia
        if(form.nocturnas == 0){//hay extra (todo: validar, )
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
            console.log('camino1')
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
                console.log('camino2') //proved  aqui!!!
            }else{ // extras empezaron el dia anterior
                form.dominical_extra_nocturnas = Madrugada
                form.extra_nocturnas -= Madrugada 
                form.dominical_nocturnas = 0
                // form.nocturnas
                // ------------- las de dia -------------
                form.dominical_diurnas = 0
                console.log('camino3')
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
        console.log('camino4 sin extras')
    }
}

function calcularDominicales(ini,fin,CuandoEmpiezaExtra,ExtrasManana){//date,date,int,bool
    let esFestivo = estaFechaEsFestivo(new Date(ini));
    let esFestivo2 = estaFechaEsFestivo(new Date(fin));
    console.log("ini festivo - fin festivo :", esFestivo,esFestivo2);

    if(ini.getDay() == 0 || fin.getDay() == 0){
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

    if((ini.getDay() == 0 && fin.getDay() == 0) || (esFestivo || esFestivo2)){ 
        form.dominical_diurnas = form.diurnas;
        form.diurnas = 0
        form.dominical_nocturnas = form.nocturnas;
        form.nocturnas = 0
        form.dominical_extra_diurnas = form.extra_diurnas;
        form.extra_diurnas = 0
        form.dominical_extra_nocturnas = form.extra_nocturnas;
        form.extra_nocturnas = 0
    }else{
        if(ini.getDay() == 0 || esFestivo){//    termina lunes 
            calcularTerminaLunes(fin,CuandoEmpiezaExtra,ExtrasManana) //todo: necesita el ini
        }

        if( fin.getDay() == 0 || esFestivo2){ //termina domingo
            calcularTerminaDomingo(ini,fin,CuandoEmpiezaExtra,ExtrasManana)
        }
    }

    if(form.horas_trabajadas >= TrabajoConAlmuerzo){
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
    console.log('Fin!')
}

//# papa = watchEffect
function calcularHoras(inicio,final){
    let ini = new Date(inicio)
    let fin = new Date(final)
    let CuandoEmpiezaExtra = null
    let ExtrasManana = false
    
    if(form.horas_trabajadas < TrabajoConAlmuerzo){
        form.almuerzo = 'No';
        
        form.diurnas = Math.abs(calcularDiurnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
        form.nocturnas = Math.abs(calcularNocturnas(form.fecha_ini,form.fecha_fin,CuandoEmpiezaExtra)[1]);
        
    }else{ //extras
        console.log('extras')
        form.almuerzo = form.horas_trabajadas > 8 ?  1 : 0;
        form.almuerzo = form.horas_trabajadas > 16 ? 2 : form.almuerzo;
        ValorRealalmuerzo = form.almuerzo
        form.almuerzo += ' horas'
        
        let ExtrasPrematuras = 9
        if(props.horasemana > (MAXIMO_HORAS_SEMANALES - 9)){
            ExtrasPrematuras = props.horasemana - MAXIMO_HORAS_SEMANALES
            console.log("🧈 debu ExtrasPrematuras:", ExtrasPrematuras);
        }

        CuandoEmpiezaExtra = parseInt(new Date(form.fecha_ini).getHours()) + ExtrasPrematuras;
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
    console.log("🧈 🧈  CuandoEmpiezaExtra:", CuandoEmpiezaExtra);
    calcularDominicales(ini,fin,CuandoEmpiezaExtra,ExtrasManana);
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
        HorasDiurnas = HorasDiurnas < 0 ? 0 : HorasDiurnas //todo: es mejor calcular bien

        if(CuandoEmpiezaExtra !== null){
            if(CuandoEmpiezaExtra < 21){
                let horasNormales = CuandoEmpiezaExtra - BaseInicial
                horasNormales < 0 ? 0 : horasNormales
                if(HorasDiurnas >= horasNormales){
                    HorasExtra = HorasDiurnas - horasNormales
                    HorasDiurnas = horasNormales
                }else{
                    console.log('imposible!')
                }
            }//cuando las horas extra >= 21, no hay horas extra diurnas
        }
        console.log("mismo DIA, HorasExtra & ordinarias", HorasExtra,HorasDiurnas); //nottemp

        return [HorasExtra,HorasDiurnas];
    }else{ //de un dia a otro
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
        console.log("🧈🧈🧈 DIA HorasExtra & ordinarias", HorasExtra,HorasDiurnasTotal); //nottemp
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
    if(DiaInicio == DiaFin){
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
    console.log("🚀 Madrugada & tarde:", Madrugada,Tarde+' = '+(HorasNoc));
    let extra = 0, ordinarias = 0;

    // --------------- calculo extra ---------------
    if (CuandoEmpiezaExtra === null || typeof CuandoEmpiezaExtra === 'undefined') {
        return [0,HorasNoc]
    }else{
        if(CuandoEmpiezaExtra >= 21){
            if(horasFin >= 21){
                extra = horasFin >= CuandoEmpiezaExtra? horasFin - CuandoEmpiezaExtra : 0
                ordinarias = HorasNoc - extra
                console.log("0a : extra & noche", extra,ordinarias);
            }else{
                extra = 24 - CuandoEmpiezaExtra
                extra += Madrugada
                ordinarias = HorasNoc - extra
                console.log("0a : extra & noche", extra,ordinarias);
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
                    console.log("😶‍🌫️2  extra  ordinarias", extra,ordinarias);
                }else{
                    if(DiaInicio == DiaFin){//todo: validar que si sea optimo preguntar esto aqui
                        extra = 0
                        ordinarias = HorasNoc
                        console.log("😶‍🌫️1  extra  ordinarias", extra,ordinarias);
                    }else{
                        ordinarias = horasInicio < 6 ? 6 - horasInicio : 0 
                        extra = HorasNoc - ordinarias
                        console.log("😶‍🌫️0  extra  ordinarias", extra,ordinarias);
                    }
                }
            }
        }
        return [extra, ordinarias];
    }
}

//si termina a las 11:59pm -> agrega una hora
const Reporte11_59 = () => {
    let fin = Date.parse(form.fecha_fin);
    let finDate = new Date(fin);
    const horafin = finDate.getHours()
    const minfin = finDate.getMinutes()
    if(horafin == 23 && minfin == 59){
        // finDate.setMinutes(finDate.getMinutes() + 1)
        // console.log("🧈 debu finDate:", finDate);
        form.horas_trabajadas++
        form.nocturnas++
    }
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
        // form.fecha_fin = props.Reporte?.fecha_fin
        // form.fecha_fin = TransformTdate(props.Reporte?.fecha_fin)
        if(form.fecha_ini == '') form.fecha_ini = TransformTdate(props.Reporte?.fecha_ini)
        if(form.fecha_fin == '') form.fecha_fin = TransformTdate(props.Reporte?.fecha_fin)
        form.centro_costo_id = props.Reporte?.centro_costo_id
        form.fecha_ini = props.Reporte?.fecha_ini
        form.fecha_fin = props.Reporte?.fecha_fin
        form.observaciones = props.Reporte?.observaciones
        form.horas_trabajadas = props.Reporte?.horas_trabajadas
        Reporte11_59()
    
        if( Date.parse(form.fecha_ini) > Date.parse(form.fecha_fin) ){
            form.horas = '0';
            // form.horas_trabajadas = form.fecha_fin.substr(1,3);
        }else{
            form.horas_trabajadas = (parseInt((Date.parse(form.fecha_fin) - Date.parse(form.fecha_ini))/(3600*1000) ));
        }
        if( Date.parse(form.fecha_ini) && Date.parse(form.fecha_fin) ){ // son fechas?
            let ini = Date.parse(form.fecha_ini);
            let fin = Date.parse(form.fecha_fin);
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

            if( ini > fin){ //jaja no jodas
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
                    if(DiaInicio != DiaFin){
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

const daynames = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];
</script>

<template>
    
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }} <b>{{ props.showUsers[props.Reporte.user_id] }}</b>
                </h2>
                <div class="my-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <InputLabel for="fecha_ini" :value="lang().label.fecha_ini" />
                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"
                            id="fecha_ini" type="date" class="mt-1 block w-full" v-model="form.fecha_ini" disabled
                            :placeholder="lang().placeholder.fecha_ini" :error="form.errors.fecha_ini" />
                        <InputError class="mt-2" :message="form.errors.fecha_ini" />
                    </div>
                    <div>
                        <InputLabel for="fecha_fin" :value="lang().label.fecha_fin" />
                        <VueDatePicker :is-24="false" :day-names="daynames" auto-apply :flow="['calendar', 'time']" :enable-time-picker="true" :teleport="true"
                            id="fecha_fin" type="date" class="mt-1 block w-full" v-model="form.fecha_fin" disabled
                            :placeholder="lang().placeholder.fecha_fin" :error="form.errors.fecha_fin" />
                        <InputError class="mt-2" :message="form.errors.fecha_fin" />
                    </div>
                    <div>
                        <InputLabel for="horas_trabajadas" :value="lang().label.horas_trabajadas" />
                        <TextInput id="horas_trabajadas" type="number" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700" v-model="form.horas_trabajadas" disabled
                            :placeholder="lang().placeholder.horas_trabajadas" :error="form.errors.horas_trabajadas" />
                        <InputError class="mt-2" :message="form.errors.horas_trabajadas" />
                    </div>
                    <div>
                        <InputLabel for="almuerzo" :value="lang().label.horacomida + ' (+9 horas)'" />
                        <TextInput id="almuerzo" type="text" class="bg-gray-100 dark:bg-gray-700 mt-1 w-full" v-model="form.almuerzo" disabled
                            :placeholder="lang().placeholder.almuerzo" :error="form.errors.almuerzo" />
                    </div>
                    <!-- mt-80 -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <div v-if="form.extra_diurnas || form.extra_nocturnas || form.dominicales == 'si'" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <div v-if="form.dominicales == 'si'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <div v-if="form.dominicales == 'si'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                    <div v-if="form.dominicales == 'si'" class="">
                        <label class="dark:text-white">Horario</label>
                        <TextInput id="dominicales" type="text" class="bg-gray-100 dark:bg-gray-700 block w-full" v-model="TextFestivo" disabled />
                    </div>
                </div>
                <div class="my-6 ">
                    <InputLabel for="observaciones" :value="lang().label.observaciones + ' (Porque se esta rechazando)'" />
                        <textarea :disabled="(correccionUsuario)"
                            id="observaciones" type="text" v-model="form.observaciones"
                            class="mt-1 block w-full rounded-md shadow-sm dark:bg-black dark:text-white placeholder:text-gray-400 placeholder:dark:text-gray-400/50"
                            cols="30" rows="3" :error="form.errors.observaciones">
                        </textarea>
                    <InputError class="mt-2" :message="form.errors.observaciones" />
                </div>
                <div class="flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton v-if="correccionUsuario" class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? '...' : lang().button.corect }}
                    </PrimaryButton>
                    <PrimaryButton v-else class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.reject + '...' : lang().button.reject }}
                    </PrimaryButton>
                </div>
            </form>

        </Modal>
    </section>
</template>
