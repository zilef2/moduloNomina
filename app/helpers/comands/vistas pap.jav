//#------------------- misterdebug - crud-generator-laravel -------------------
php artisan make:crud Parametro "auxiliar:string"
php artisan make:crud Tienda "nombre:string" -n
php artisan make:crud Inventario "codigo:string" -n
php artisan make:crud Producto "codigo:string,nombre:string,descripcion:string,cantidad:integer,um:string,precioBS:double,descuento:double,precioConDescuento" -n
php artisan make:crud Proveedor "nombre:string,direccion:string,ciudad:string,telefono:string,correo:string" -n
php artisan make:crud Cliente "nombre:string,direccion:string,ciudad:string,telefono:string,correo:string" -n
php artisan make:crud Venta "nombre:string" -n
php artisan make:crud Compra "nombre:string" -n
php artisan make:crud Mascota "nombre:string" -n
php artisan make:crud Categoria_producto "nombre:string" -n
php artisan make:crud Venta_detalle "fecha:date,valor:double,cantidad:double,total:double" -n
php artisan make:crud Compra "fecha:date,valor:double,cantidad:double,total:double" -n
php artisan make:crud Movimiento_financiero "fecha:date,valor:double,cantidad:double,total:double" -n
php artisan make:crud Gasto "fecha:date,valor:double,cantidad:double,total:double" -n
php artisan make:crud Tipo_venta "tipo:string" -n
php artisan make:crud Tipo_gasto "tipo:string" -n
php artisan make:crud Categoria_producto "tipo:string" -n

//faltan por correr(12feb2024 : ninguna)


//#------------------- just model -------------------
php artisan make:model ModeloEjemplo -all


//# ------------------- Utilidades -------------------
para borrar:  php artisan rm:crud post --force
    php artisan rm:crud Parametro --force


//# -------------------laravel excel (not yet) -------------------
php artisan make:impoort PersonalImport --model=User





//# -------------------Historico de comandos -------------------
php artisan make:middleware ErrorHandlerMiddleware

php artisan make:command CopyUserPages
