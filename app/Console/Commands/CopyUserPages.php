<?php
//VERSION 1.0 22ENE2025
        //$ipAddress = $request->ip();

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Exception;

/*
php artisan make:command copy:u
*/

class CopyUserPages extends Command
{
    use Constants;
    protected function generateAttributes(): array
    {
        return [
            'numero_cot' => 'integer',        // Número COT
            'descripcion_cot' => 'string',         // Descripción
            'precio_cot' => 'integer',             // Precio
            'aprobado_cot' => 'boolean',           // Aprobado
            'fecha_aprobacion_cot' => 'date',     // Fecha aprobación
        ];
//            'precio' => 'integer',
//            'nombre' => 'string',
//            'descripcion' => 'text',
//            'precio' => 'decimal',
//            'estado' => 'boolean',
//            'fecha_disponible' => 'date',
    }

    protected $signature = 'copy:u';
    protected $description = 'Copia de la entidad generica';
    public string $generando;
    const string MSJ_EXITO = ' fue realizada con exito ';
    const string MSJ_FALLO = ' Fallo';

    public function handle(): void
    {
        try {
            $this->generando = self::getMessage('generando');

            $contadorMetodos = 0;
            $modelName = $this->ask('¿Cuál es el nombre del modelo?');
            if (!$modelName || $modelName == '') {
                $this->info('Sin modelo');
                return;
            }

        $this->MetodologiaInicial($modelName, 'generic', '');
            if ($this->DoWebphp($modelName)) {

                $this->info('DoWebphp' . self::MSJ_EXITO);
                $contadorMetodos++;
            } else {
                $this->error('DoWebphp ' . self::MSJ_FALLO);
                return;
            }
            if ($this->DoAppLenguaje($modelName)) {

                $this->info('DoAppLenguaje' . self::MSJ_EXITO);
                $contadorMetodos++;
            } else {
                $this->error('DoAppLenguaje ' . self::MSJ_FALLO);
                return;
            }
            if ($this->DoSideBar($modelName)) {

                $this->info('DoSideBar' . self::MSJ_EXITO);
                $contadorMetodos++;
            } else {
                $this->error('DoSideBar ' . self::MSJ_FALLO);
                return;
            }
            $this->DoFillable($modelName);
            $contadorMetodos++;
            $this->updateMigration($modelName);
            $contadorMetodos++;


            $this->info(Artisan::call('optimize'));
            $this->info(Artisan::call('optimize:clear'));
            $this->info('FINISH');
        } catch (Exception $e){
            $this->error("FALLO CONTADOR: ".$contadorMetodos . " excepcion: ". $e->getMessage());

        }
    }


    private function MakeControllerPages($plantillaActual, $modelName): bool
    {
        $folderMayus = ucfirst($modelName);
        $sourcePath = base_path('app/Http/Controllers/' . $plantillaActual . 'Controller.php');
        $destinationPath = base_path("app/Http/Controllers/" . $folderMayus . "sController.php");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '$destinationPath' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        $this->info("- " . $sourcePath);
        $this->info("- " . $destinationPath);

        return true;
    }

    private function MakeVuePages($plantillaActual, $modelName): bool
    {
        $sourcePath = base_path('resources/js/Pages/' . $plantillaActual);
        $destinationPath = base_path("resources/js/Pages/$modelName");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '$modelName' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        return true;
    }

    private function replaceWordInFiles($oldWord, $permiteRemplazo, $modelName, $depende): void
    {
        $folderMayus = ucfirst($modelName);
        $files = File::allFiles(base_path("resources/js/Pages/$modelName"));
        $controller = base_path("app/Http/Controllers/$folderMayus" . 'Controller.php');

        $depende = $depende == '' || $depende == null ? 'no_nada' : $depende;

        if ($permiteRemplazo['vue']) {
            foreach ($files as $file) {

                $content = file_get_contents($file);
                $content = str_replace(array($oldWord, ucfirst($oldWord), 'geeneric'),//ojo aqui, es estatico
                    [$modelName, $folderMayus, $folderMayus],
                    $content
                );
                file_put_contents($file, $content);
            }
        }

        //reemplazo de controlador
        if ($permiteRemplazo['controller']) {
            $sourcePath = base_path('app/Http/Controllers/' . ucfirst($oldWord) . 'Controller.php');
            $content = file_get_contents($sourcePath);
            $content = str_replace(array($oldWord, 'dependex', 'deependex', 'geeneric'),//TODO:ojo aqui, es estatico
                array($modelName, $depende, ucfirst($depende), ucfirst($modelName)),
                $content
            );
            file_put_contents($controller, $content);
        }
    }

    protected function DoFillable($modelName): void
    {
        $attributes = $this->generateAttributes();

        // Generar el fillable
        $fillable = array_keys($attributes);
        $fillableString = implode("', '", $fillable);

        // Ruta del modelo
        $modelPath = app_path("Models/$modelName.php");

        // Verificar si el modelo existe
        if (!File::exists($modelPath)) {
            $this->error("El modelo $modelName no existe.");
            return;
        }

        // Leer el contenido del modelo
        $modelContent = File::get($modelPath);

        // Añadir el fillable y SoftDeletes
        $modelContent = preg_replace('/protected \$fillable = \[.*?\];/s', "protected \$fillable = ['$fillableString'];", $modelContent);
        if (!str_contains($modelContent, 'use SoftDeletes;')) {
            $modelContent = preg_replace('/class ' . $modelName . ' extends/', "use Illuminate\Database\Eloquent\SoftDeletes;\n\n    class $modelName extends", $modelContent);
        }

        // Guardar el contenido modificado
        File::put($modelPath, $modelContent);

        $this->info("El fillable y SoftDeletes han sido añadidos al modelo $modelName.");
    }

    private function DoAppLenguaje($resource): int
    {
        $directory = 'lang/es/app.php';
        $files = glob($directory);

        $insertable = "'$resource' => '$resource',\n\t\t//aquipues";
        $pattern = '/\/\/aquipues/';
        $contadorVerificador = 0;
        foreach ($files as $file) {
            $contadorVerificador++;
            $content = file_get_contents($file);
            if (!str_contains($content, $pattern)) {
                $content2 = preg_replace($pattern, $insertable, $content);
//                $content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content == $content2)
                    $this->info("Language Actualizado: $file\n");
                else
                    $this->info("Language sin cambios: $file\n");
            } else {
                $this->error("No existe aquipues en: $file\n");
                $contadorVerificador = 0;
                break;
            }
        }
        return $contadorVerificador;

    }


    private function DoWebphp($resource): int
    {
        $directory = 'routes';
        $files = glob($directory . '/*.php');

        $insertable = "Route::resource(\"/$resource\", \\App\\Http\\Controllers\\" . ucfirst($resource) . "Controller::class);\n\t//aquipues";

        $pattern = '/\/\/aquipues/';

        $contadorVerificador = 0;
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $contadorVerificador++;

            if (!str_contains($content, $pattern)) {
                $content2 = preg_replace($pattern, $insertable, $content);
//                $content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content == $content2)
                    $this->info("Routes Actualizado: $file\n");
                else
                    $this->info("Routes sin cambios: $file\n");
            } else {
                $this->error("No existe aquipues en: $file\n");
                $contadorVerificador = 0;
                break;
            }
        }
        return $contadorVerificador;
    }

    private function DoSideBar($resource): int
    {
        $directory = 'resources/js/Components/SideBarMenu.vue';
        $files = glob($directory);

        $insertable = "'" . $resource . "',\n\t//aquipuesSide";
        $pattern = '/\/\/aquipuesSide/';

        $contadorVerificador = 0;
        foreach ($files as $file) {
            $content = file_get_contents($file);

            if (!str_contains($content, $pattern)) {
                $contadorVerificador++;
                $content2 = preg_replace($pattern, $insertable, $content);
                //$content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content != $content2)
                    $this->info("SideBarMenu.vue Actualizado: $file\n");
                else
                    $this->info("SideBarMenu.vue sin cambios: $file\n"); //todo: revisar si ya existe
            } else {
                $this->error("No existe aquipues en: $file\n");
                $contadorVerificador = 0;
                break;
            }
        }

        return $contadorVerificador;
    }

    protected function updateMigration($modelName): void
    {
        $atributos = $this->generateAttributes();
        $migrationFile = collect(glob(database_path('migrations/*.php')))
            ->first(fn($file) => str_contains($file, 'create_' . Str::snake(Str::plural($modelName)) . '_table'));

        if (!$migrationFile) {
            $this->error("No se encontró la migración para $modelName");
            return;
        }

        $columns = collect($atributos)->map(function ($type, $name) {
            return "\$table->$type('$name');";
        })->implode("\n            ");

        $content = file_get_contents($migrationFile);
        $content = preg_replace('/Schema::create\(.*?\{/', "$0\n            $columns", $content);
        file_put_contents($migrationFile, $content);

        $this->info("Migración actualizada para $modelName");
    }

    /**
     * @param mixed $modelName
     * @param string $plantillaActual
     * @param mixed $depende
     * @return void
     */
    public function MetodologiaInicial(mixed $modelName, string $plantillaActual, mixed $depende): void
    {
        $this->warn("Empezando make:model");
        Artisan::call('make:model', ['name' => $modelName, '--all' => true]);

        //comandos de dependencias
        $this->warn("Empezando copies");
        Artisan::call('copy:f');// Commands/WriteFillable.php
        $this->warn("Ahora Lang");
        Artisan::call('lang:u ' . $modelName);


        $RealizoVueConExito = $this->MakeVuePages($plantillaActual, $modelName);
        $mensaje = $RealizoVueConExito ? self::getMessage('generando') . ' Vuejs' . self::MSJ_EXITO
            : self::getMessage('generando') . ' Vuejs' . self::getMessage('fallo');
        $this->info($mensaje);


        $RealizoControllerConExito = $this->MakeControllerPages($plantillaActual, $modelName);
        $mensaje = $RealizoControllerConExito ? self::getMessage('generando') . 'el controlador' . self::MSJ_EXITO
            : self::getMessage('generando') . ' controlador ' . self::getMessage('fallo');
        $this->info($mensaje);


        if ($RealizoControllerConExito || $RealizoVueConExito)
            $this->replaceWordInFiles($plantillaActual,
                [
                    'vue' => $RealizoVueConExito,
                    'controller' => $RealizoControllerConExito
                ]
                , $modelName, $depende);
    }

}
