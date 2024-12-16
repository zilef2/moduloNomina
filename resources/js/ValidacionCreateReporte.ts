
export function validacionNoMasDe3Diax(DiaForm:string,DiasPermitidoPasado:number):string {
    let today:Date = new Date(); // Fecha actual
    // Validar si DiaForm es una fecha válida
    if (!DiaForm) {
        return "El formato no es válido.";
    }

    let formDate:Date = new Date(DiaForm); // Convertir DiaForm a un objeto Date
    const differenceInTime = formDate.getTime() - today.getTime();

// Convertir la diferencia de tiempo a días
    const differenceInDays:number = Math.ceil(differenceInTime / (1000 * 60 * 60 * 24));
    // let differenceInDays:number = formDate.getDate() - today.getDate(); // Diferencia en días

    // Validaciones
    console.clear()
    console.log("differenceInDays", differenceInDays);
    // console.log("=>(ValidacionCreateReporte.ts:14) formDate.getDate() ", formDate.getDate() );
    // console.log("=>(ValidacionCreateReporte.ts:14) today.getDate()", today.getDate());
    //no futuros
    if (differenceInDays > 0) {
        return "El Reporte no puede ser futuro al día de hoy.";
    }

    //no muy pasados
    if (differenceInDays < -DiasPermitidoPasado) {
        return "El Reporte es de "+ DiasPermitidoPasado +" días átras";
    }
    
    //no futuros de hoy
    // if (differenceInDays === 0 && (formDate.getHours() < today.getHours())) {
    //     return "El Reporte no puede ser futuro al día de hoy.";
    // }
    return "ok";
}

