<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class justMigration extends Command
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
     }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:migra';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $modelName = $this->ask('¿Cuál es el nombre del modelo?');
            $this->updateMigration($modelName);
            $this->info((!!Artisan::call('optimize'))? 'optimize realizado':'fallo la optimizacion');
            $this->info((!!Artisan::call('optimize:clear'))? 'optimize realizado':'fallo la optimizacion');
            $this->info('FINISH');
        } catch (Exception $e) {
            $this->error("FALLÓ ");
        }
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
}
