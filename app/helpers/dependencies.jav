--------------------- PROBANDO --------------------- 
//? composer
    composer require mrdebug/crudgen --dev (probando)
//?npm

//?para livewire
    composer require laravelcollective/html
        npm install --save-dev @defstudio/vite-livewire-plugin
        php artisan vendor:publish --provider="Mrdebug\Crudgen\CrudgenServiceProvider"

        //por silas
        "devDependencies": {
            // "@defstudio/vite-livewire-plugin": "^1.0.7",
// fin livewire


//! ---------------------  instaladas en este proyecto (modulnomina) --------------------- 
// composer
composer require laraveles/spanish
    php artisan laraveles:install-lang
composer require maatwebsite/excel
// node
npm install @vuepic/vue-datepicker
npm i vue-chartjs chart.js



//# --------------------- otros proyectos --------------------- 

    composer global require laravel/installer
    composer require laravel/jetstream

//?helps
    //composer
    composer global require laravel/installer
    composer require laravel/jetstream
    composer install && npm install
//? heramientas
    aun no se instalan{
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