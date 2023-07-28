//temp
    // const diaoDominicla = 8 //temp 7 normal con 8 => finaliza en domingo
    const diaoDominicla = 7 //temp 7 normal con 8 => finaliza en domingo
function getHoursColombianRandom(otraVez) { 
    let vec_inicial,vec_final
    vec_inicial = ['06']; vec_final = ['20']
    let iniLength = Math.floor(Math.random() * (vec_inicial.length-1));
    let fiLength = Math.floor(Math.random() * (vec_final.length-1));
    if(otraVez) return vec_final[fiLength]
    let hora_inicial = vec_inicial[iniLength]
    let hora_final = vec_final[fiLength]
    return [hora_inicial,hora_final];
}
const horas = getHoursColombianRandom() // temp
//fin temp