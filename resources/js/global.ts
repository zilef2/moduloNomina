/*
-- this Project

--FROM others projects

 // --DATE functions
 formatToVue
 formatDate
 monthName
 TransformTdate

 --MATHfunctionsome

 number_format
 CalcularEdad
 CalcularSexo
 CalcularAvg

 --STRING FUNCTIONS

 sinTildes
 NoUnderLines
 ReemplazarTildes
 PrimerosCaracteres
 PrimerasPalabras
 textoSinEspaciosLargos

 --ARRAY

 vectorSelect
*/


//FROM others projects
import FestivosColombia from 'festivos-colombia';

export function CuantosFestivosEstaQuincena2(numQuicena,elmes,anio) {
    let dateFestivos, dateArr, monthFestivo,arrayFechas=[];

    //se llama la libreria
    let holidaysSelect:object[] = FestivosColombia.getHolidaysByYear(anio)
    let diaLimite = numQuicena == 1 ? 14 : 31
    holidaysSelect.forEach(element => {
        // @ts-ignore
        dateArr = element.date.split('/'); //array con la fecha del foreach
        dateFestivos = new Date(dateArr[2], dateArr[1] - 1, dateArr[0]);
        monthFestivo = parseInt((dateFestivos.getMonth()))
        if (monthFestivo == elmes) {
            if (diaLimite == 14) { //primera quincena
                if (diaLimite > dateFestivos.getDate()) {
                    arrayFechas.push(dateFestivos)
                    // contadorResult++;
                }
            } else { //segunda quincena
                if (dateFestivos.getDate() > 14 && diaLimite > dateFestivos.getDate()) {
                    // contadorResult++;
                    arrayFechas.push(dateFestivos)
                }
            }
        }else{
            return
        }
    });
    return arrayFechas
}

//fin FROM others prjects
// this Project
    export function LookForValueInArray(arrayOfObjects:Object[] , searchValue): String {
        //ex: { title: '123', value: 1 },
        let foundObject = '';
        for (const obj of arrayOfObjects) {

            if(typeof searchValue == 'string'){
                // if (obj['title'] === searchValue) {
                //     foundObject = obj['value'];
                // }
                return '';
            }else{
                if (obj['title'] === searchValue['title']) {
                    foundObject = obj['value'];
                    break;
                }
            }
        }

        return foundObject;
    }
// end this Project


// --DATE functions
    export function weekNumber(date) {
        if (!(date instanceof Date) || isNaN(date.getTime())) {
            return 0
        }
        // Clonar la fecha para no modificar el objeto original
        var clonedDate:Date = new Date(date.getTime());

        // Configurar el tiempo al inicio del día para evitar problemas con las zonas horarias
        clonedDate.setHours(0, 0, 0, 0);

        // Obtener la fecha de inicio del año
        var startOfYear:Date = new Date(clonedDate.getFullYear(), 0, 1);

        // Calcular la diferencia en milisegundos entre la fecha dada y el inicio del año
        var timeDiff: any;
        // @ts-ignore
        timeDiff = clonedDate - startOfYear;

        // Calcular el número de semanas completas y redondear hacia abajo
        var weekNumber = Math.floor(timeDiff / (7 * 24 * 60 * 60 * 1000));

        // Agregar 1 para contar la primera semana del año
        weekNumber += 1;

        return weekNumber;
    }

    export function formatToVue(date) : String{
        const day = date.getDate();
        const month = date.getMonth() + 1;
        const year = date.getFullYear();

        return `${day}/${month}/${year}`;
    }
    export function formatDate(date,isDateTime: string) :string {
        let validDate = new Date(date)
        // validDate = new Date(validDate.getTime() + (5 * 60 * 60 * 1000)) //correccion con GTM -5
        // console.log("🧈 debu validDate:", validDate);
        const day = validDate.getDate().toString().padStart(2, "0");
        // getMonthName(1)); // January
        const month = monthName((validDate.getMonth() + 1).toString().padStart(2, "0"));
        let year = validDate.getFullYear();
        let anioActual = new Date().getFullYear();
        if(isDateTime == 'conLaHora'){

            let hora = validDate.getHours();
            const AMPM = hora >= 12 ? ' PM' : ' AM';
            hora = hora % 12 || 12;
            let hourAndtime =  hora + ':'+ (validDate.getMinutes() < 10 ? '0': '') + validDate.getMinutes()  + AMPM;
            if (anioActual == year){
                return `${day}-${month} | ${hourAndtime}`;
            }
            else{
                let Stringyear = year.toString().slice(-2);
                return `${day}-${month}-${Stringyear} | ${hourAndtime}`;
            }
        }else{
            if (anioActual == year){
                return `${day}-${month}`;
            }
            else{
                let Stringyear = year.toString().slice(-2);
                return `${day}-${month}-${Stringyear}`;
            }
        }
    }
    export function TimeTo12Format(timeString) {
        if(timeString === null) return '';
        const [hours, minutes, seconds] = timeString.split(':');

        // Convert the time to 12-hour format and add 'am' or 'pm'
        const timeIn12HourFormat = new Date(2023, 7, 5, parseInt(hours), parseInt(minutes)).toLocaleString('es-CO', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true,
        });
        return timeIn12HourFormat;
    }
    export function formatTime(date) :string {
        let validDate
        if(date){
            validDate = new Date(date)
        }else{
            validDate = new Date()
        }
        // validDate = new Date(validDate.getTime() + (5 * 60 * 60 * 1000))

        let hora = validDate.getHours();
        let hourAndtime =  hora + ':'+ (validDate.getMinutes() < 10 ? '0': '') + validDate.getMinutes() + ':00';

        return `${hourAndtime}`;
    }
    export function TransformTdate (number:any = null,dateString = new Date()){
        let date
        if(dateString){
            date = new Date(dateString);
        }else{
            date = new Date();
        }
        const year = date.getFullYear();
        const month = ('0' + (date.getMonth() + 1)).slice(-2);
        const day = ('0' + date.getDate()).slice(-2);

        let hours
        if(number){
            hours = number >= 10 ? number : '0'+number;
        }else{
            hours = ('0' + date.getHours()+number).slice(-2);
        }
        const minutes = number ? '00' : ('0' + date.getMinutes()).slice(-2);
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }



    export function monthName(monthNumber){
        if(monthNumber == 1) return 'Enero';
        if(monthNumber == 2) return 'Febrero';
        if(monthNumber == 3) return 'Marzo';
        if(monthNumber == 4) return 'Abril';
        if(monthNumber == 5) return 'Mayo';
        if(monthNumber == 6) return 'Junio';
        if(monthNumber == 7) return 'Julio';
        if(monthNumber == 8) return 'Agosto';
        if(monthNumber == 9) return 'Septiembre';
        if(monthNumber == 10) return 'Octubre';
        if(monthNumber == 11) return 'Noviembre';
        if(monthNumber == 12) return 'Diciembre';
    }
    export function allMonthName():{id:number,name:string}[]{
        return [
        {
            'id':1,
            'name':'Enero'
        },
        {
            'id':1,
            'name':'Febrero'
        },
        {
            'id':1,
            'name':'Marzo'
        },
        {
            'id':1,
            'name':'Abril'
        },
        {
            'id':1,
            'name':'Mayo'
        },
        {
            'id':1,
            'name':'Junio'
        },
        {
            'id':1,
            'name':'Julio'
        },
        {
            'id':1,
            'name':'Agosto'
        },
        {
            'id':1,
            'name':'Septiembre'
        },
        {
            'id':1,
            'name':'Octubre'
        },
        {
            'id':1,
            'name':'Noviembre'
        },
        {
            'id':1,
            'name':'Diciembre'
        },
        ]
    }



// fin DATE functions



// MATHfunctionsome

    export function formatPesosCol(number):string{
        number = Math.round(number);
        const formattedNumber = number.toLocaleString('en-US', {
            minimumFractionDigits: 0, // Mínimo de dígitos decimales (en este caso, 0)
            maximumFractionDigits: 0 // Máximo de dígitos decimales (en este caso, 0)
        });

        // Reemplazar la coma por un punto como separador decimal
        return '$ ' + formattedNumber;
        // return '$ ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "'");
    }
    export function CalcularAvg(TheArray,NameValue = '',isTime = false) {
        let sum = 0
        if(NameValue === ''){
            TheArray.forEach((value, index, array) => {
                sum += value;
            })
        }else{
            if(isTime){ //time like: 14:18

                TheArray.forEach((value, index, array) => {
                    let justHour = value[NameValue].split(':')[0];
                    justHour = parseInt(justHour);
                    sum += justHour;
                })
            }else{
                TheArray.forEach((value, index, array) => {
                    sum += value[NameValue];
                })
            }
        }
        const result = number_format(sum/TheArray.length,1,false);
        return result;
    }
    export function number_format(amount, decimals, isPesos):string {
        amount += '';
        amount = parseFloat(amount.replace(/[^0-9\.]/g, ''));
        decimals = decimals || 0;

        if (isNaN(amount) || amount === 0)
            return parseFloat("0").toFixed(decimals);
        amount = '' + amount.toFixed(decimals);

        const amount_parts = amount.split(' '),
            regexp = /(\d+)(\d{3})/;

        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

        if(isPesos)
            return '$'+amount_parts.join(' ');
        return amount_parts.join(' ');
    }

    export function CalcularEdad(nacimiento){
        const anioHoy = new Date().getFullYear();
        const anioNacimiento = new Date(nacimiento).getFullYear();
        return anioHoy - anioNacimiento;
    }

    export function CalcularSexo(value){
        return value == 0 ? 'Masculino' : 'Femenino'
    }



//STRING FUNCTIONS
    export function sinTildes(value){
        let pattern = /[^a-zA-Z0-9\s]/g;
        let replacement = '';
        let result = value.replace(pattern, replacement);
        return result
    }
    export function NoUnderLines(value){
        return value.replace(/[^a-zA-Z0-9]/g, ' ');
    }


    export function ReemplazarTildes(texto){
        return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    export function PrimerosCaracteres(texto,caracteres = 15){
        if(texto){

            if(texto.length > caracteres + 5){

                const primeros = texto.substring(0,caracteres);
                return primeros + '...';
            }
            return texto
        }
    }
    export function PrimerasPalabras(texto,palabras = 10){
        if(texto){
            const firstWords = texto.split(" ");
            if(firstWords.length > palabras){
                const primeros = firstWords.slice(0,palabras).join(" ");
                return primeros + '...';
            }
            return texto
        }
    }

    export function textoSinEspaciosLargos(texto){
        return texto.replace(/\s+/g, ' ');
    }


//array functions
    export function vectorSelect(vectorSelect, propsVector, genero = 'uno'){
        vectorSelect = propsVector.map(
            generico => (
                { label: generico.nombre, value: generico.id }
            )
        )
        vectorSelect.unshift({label: 'Seleccione '+ genero, value:0})
        return vectorSelect;
    }

    /*
    watch(() => form.tipoRes, (newX) => {
        data.selectedPrompID = 'Selecciona un promp'
    })
    */
