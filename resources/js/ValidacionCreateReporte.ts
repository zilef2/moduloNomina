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
    console.log("=>(ValidacionCreateReporte.ts:14) formDate.getDate() ", formDate.getDate() );
    console.log("=>(ValidacionCreateReporte.ts:14) today.getDate()", today.getDate());
    //no futuros
    if (formDate.getDate() > today.getDate()) {
        return "El Reporte no puede ser futuro al día de hoy.";
    }

    //no muy pasados
    if (differenceInDays < -4) {
        return "El Reporte es de mas de 4 días átras";
    }
    
    //no futuros de hoy
    // if (differenceInDays === 0 && (formDate.getHours() < today.getHours())) {
    //     return "El Reporte no puede ser futuro al día de hoy.";
    // }
    return "ok";
}

