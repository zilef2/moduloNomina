export function validacionNoMasDe3Diax(DiaForm:string):string {
    let today:Date = new Date(); // Fecha actual
    // Validar si DiaForm es una fecha válida
    if (!DiaForm) {
        return "El formato no es válido.";
    }

    let formDate:Date = new Date(DiaForm); // Convertir DiaForm a un objeto Date
    let differenceInDays:number = formDate.getDate() - today.getDate(); // Diferencia en días

    // Validaciones
    console.clear()
    console.log("differenceInDays", differenceInDays);
    if (formDate.getDate() > today.getDate()) {
        return "El Reporte no puede ser futuro al día de hoy.";
    }

    if (differenceInDays < -4) {
        return "El Reporte es de mas de 4 días átras";
    }
    return "ok";
}

