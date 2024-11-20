# acutalizando a laravel 11
composer require laravel/sanctum:^4.0
composer require illuminate/view:^11.0 --with-dependencies
composer require laravelcollective/html
composer remove illuminate/view

composer update --ignore-platform-reqs --with-all-dependencies


//
composer remove tightenco/ziggy

composer require tightenco/ziggy:^2.0



        "laravel/framework": "9.52.16",
        "spatie/laravel-permission": "^5.9",
        "php": "^8.1",
        "spatie/laravel-backup": "^7.0",


        -----------  TO -----------
        -----------  TO -----------
        -----------  TO -----------

        "laravel/framework": "^11",
        "spatie/laravel-permission": "^5.9",
        "php": "^8.3",
        "spatie/laravel-backup": "8.8.2",
        "spatie/laravel-ignition": "2.4",


composer require monolog/monolog:^3.0 --with-all-dependencies
composer remove spatie/laravel-ignition
composer require spatie/laravel-ignition:^2.0 --with-all-dependencies
LISTO LARAVEL 10, AHORA POR LARAVEL 11

composer require laravel/framework:^11.0 laravel/sanctum:^4.0 --with-all-dependencies
composer require spatie/laravel-ignition:^2.4


composer require nunomaduro/collision:^8.0
composer require phpunit/phpunit:^10.0
composer require symfony/console:^7.0.1