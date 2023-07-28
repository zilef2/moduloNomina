
export function formatDate(date,isDateTime) {
    const validDate = new Date(date)
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
            year = year.toString().slice(-2);
            return `${day}-${month}-${year} | ${hourAndtime}`;
        }
    }else{
        if (anioActual == year){
            return `${day}-${month}`;
        }
        else{
            year = year.toString().slice(-2);
            return `${day}-${month}-${year}`;
        }
    }
}

export function number_format(amount, decimals, isPesos) {
    amount += '';
    amount = parseFloat(amount.replace(/[^0-9\.]/g, ''));
    decimals = decimals || 0;

    if (isNaN(amount) || amount === 0)
        return parseFloat(0).toFixed(decimals);
    amount = '' + amount.toFixed(decimals);

    var amount_parts = amount.split(' '),
        regexp = /(\d+)(\d{3})/;

    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

    if(isPesos)
        return '$'+amount_parts.join(' ');
    return amount_parts.join(' ');
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


export function TransformTdate (dateString){
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const day = ('0' + date.getDate()).slice(-2);
    const hours = ('0' + date.getHours()).slice(-2);
    const minutes = ('0' + date.getMinutes()).slice(-2);
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}