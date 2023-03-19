    /home/aplicativoswebco/public_html

//mark: uso constante -->
    // ?install
        php artisan migrate:fresh --seed
        composer dump-autoload
        php artisan key:generate

        composer global require laravel/installer
        composer require laravel/jetstream
        composer install && npm install


        //probando (aun no se deberian poner en todos los proyectos)
            //composer
                 composer require mrdebug/crudgen --dev
            //npm
                npm i vue-chartjs chart.js
                npm install @vuepic/vue-datepicker

            //para livewire
                composer require laravelcollective/html
                    npm install --save-dev @defstudio/vite-livewire-plugin
                    php artisan vendor:publish --provider="Mrdebug\Crudgen\CrudgenServiceProvider"

                    //por silas
                    "devDependencies": {
                        // "@defstudio/vite-livewire-plugin": "^1.0.7",
            // fin livewire
    //fin  install laravel
    //?helps
        //memory limit
            C:\laragon\bin\php\php-7.4.30-Win32-vc15-x64\php.ini

        //composer
        composer global require laravel/installer
        composer require laravel/jetstream


        composer global require laravel/installer
        composer require laravel/jetstream
        composer install && npm install
        
    //? heramientas
        composer require laraveles/spanish
        php artisan laraveles:install-lang
        aun no se instalan{
            // composer require psr/simple-cache:^1.0 maatwebsite/excel
            // npm install @tailwindcss/forms
        }
        in brive{
            Vue && Inertia && Tailwind
            Hero Icons && HeadlessUI
            Spatie (permisos)
            Floating Vue 
            VueUse
        }
        // REQUIRED
            git clone https://github.com/erikwibowo/Laravel-Brive.git
            cd Laravel-Brive && composer update && npm install && cp .env.example .env && php artisan key:generate
        // FIN REQUIRED

    //? utilidades mias
        url: -> clear
        resource/lang/es.json
        dataseeder
            pendiente: faker comon utilities


// FIN  uso constante
//(ver archivo: vistas pap)
    Php artisan make:model ClaseGenerica --all

//middleware
    // php artisan make:middleware IsAdmin


//#--  correo - EXPORT AND IMPORTS
    //CORREO
    php artisan make:mail ExampleMail

    //#-- excel export

    php artisan make:export ExampleExport --model=Empresa

    //#-- excel import
    php artisan make:import ExampleImport --model=User


//# Vue dependencies
    // Vue test
        npm install -g @vue/cli
        vue add @vue/unit-jest
    // datetime
        npm install --save luxon vue-datetime weekstart
        npm install --save luxon vue-datetime weekstart --force


//despliegue
php artisan migrate:fresh --seed
composer dump-autoload
php artisan key:generate

rm -r /public_html/modulonom/moduloNomina
rm -r /home/aplicativoswebco/public_html/modulonom/moduloNomina
rm -r /home/aplicativoswebco/public_html/modulonom/storage/logs/laravel.log
*/

// <!-- linux -->
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
        