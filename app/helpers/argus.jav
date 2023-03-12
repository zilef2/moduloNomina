
// argus
PRODUCCION

    productos{
        nombre
        precio
        cantidad
        codigo
    }

REPARACIONES
reparacion{
    nombre
    precio_amperaje

    pieza_id
    cliente_id
}
maquina{
    nombre
    amperaje
    num_serie
}

cliente{
    nombre
    empresa
    correo
    celular
}
repuestos{
    nombre
    precio

    maquina_id
}


CONSTRUCCIONES
construcciones{
    nombre
    precio_metro^2

}

piezas{
    nombre
}

