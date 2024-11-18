export function validacionNoMasDe3Diax(DiaForm:string):string {
    let today:Date = new Date(); // Fecha actual
    // Validar si DiaForm es una fecha válida
    if (!DiaForm) {
        return "El formato no es válido.";
    }

    let formDate:Date = new Date(DiaForm); // Convertir DiaForm a un objeto Date
    let differenceInMilliseconds = formDate.getTime() - today.getTime(); // Diferencia en milisegundos
    let differenceInDays = differenceInMilliseconds / (1000 * 60 * 60 * 24); // Diferencia en días

    // Validaciones
    if (differenceInDays > 0) {
        return "El Reporte no puede ser futuro al día de hoy.";
    }

    if (differenceInDays < -3) {
        return "El Reporte es de mas de 4 días en el pasado";
    }
    return "ok";
}

