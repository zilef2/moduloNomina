//# ---------------------  uso constante --------------------- 
    /home/aplicativoswebco/public_html
    // ?install
        php artisan migrate:fresh --seed
        composer dump-autoload
    //fin  install laravel
        
// FIN  uso constante
//(ver archivo: vistas pap)
    Php artisan make:model ClaseGenerica --all
//middleware
    // php artisan make:middleware IsAdmin
//#---------------------  correo - EXPORT AND IMPORTS--------------------- 
    //CORREO
    php artisan make:mail ExampleMail
    //#-- excel export
    php artisan make:export ExampleExport --model=Empresa
    //#-- excel import
    php artisan make:import ExampleImport --model=User

//# --------------------- Vue dependencies--------------------- 
    // Vue test
        npm install -g @vue/cli
        vue add @vue/unit-jest
    // datetime
        npm install --save luxon vue-datetime weekstart
        npm install --save luxon vue-datetime weekstart --force

//# --------------------- despliegue --------------------- 
php artisan migrate:fresh --seed
composer dump-autoload
php artisan key:generate

rm -r /public_html/modulonom/moduloNomina
rm -r /home/aplicativoswebco/public_html/modulonom/moduloNomina
rm -r /home/aplicativoswebco/public_html/modulonom/storage/logs/laravel.log
*/

//# --------------------- <!-- linux -->--------------------- 
    // <!-- borrar -->
        rm -r "direccion"
    // <!-- permisos recursivamente -->
        chmod a+rwx folder_name -R
        chmod -R 555 /home/aplicativoswebco/public_html/modulonom/config
        chmod -R 555 /home/aplicativoswebco/public_html/modulonom/app
        chmod -R 775 /home/aplicativoswebco/public_html/modulonom/storage
        chmod -R 775 /home/aplicativoswebco/public_html/modulonom/bootstrap
        sudo chmod -R ugo+rw /home/aplicativoswebco/public_html/modulonom/storage
        sudo chmod -R ugo+rw /home/aplicativoswebco/public_html/modulonom/bootstrap
        chmod -R 555 /home/aplicativoswebco/public_html/modulonom/*
        */

    mv /home/aplicativoswebco/public_html/modulonom/bootstrap/cache /home/aplicativoswebco/public_html/modulonom/bootstrap/cache_2
    mv /home/aplicativoswebco/public_html/modulonom/bootstrap/cache_2 /home/aplicativoswebco/public_html/modulonom/bootstrap/cache
    mkdir /home/aplicativoswebco/public_html/modulonom/storage/framework/cache/data
        