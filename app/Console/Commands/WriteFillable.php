<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WriteFillable extends Command
{
    protected $signature = 'copy:f';
    protected $description = 'Escribe en cada modelo, el fillable';

    public function handle(){
        $this->info("Empezando con los fillable");
        $this->DoFillable();
        $this->info("Fin de la operacion");
    }

 
    private function DoFillable(): void
    {
        $directory = 'app/Models';
        $files = glob($directory . '/*.php');

        $fillable = "\n    protected \$fillable = [\n        'id',\n    ];\n"
//            . "protected \$appends = [\n'valorino'\n]"
        ;
        
        $contarEscritos = 0;
        $contarNoEscritos = 0;
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if (strpos($content, 'protected $fillable') === false) {
                $content = preg_replace('/(use HasFactory;)/', "$1$fillable", $content);
                file_put_contents($file, $content);
                $contarEscritos++;
                $this->info("Actualizado: $file\n");
            } else {
                $contarNoEscritos++;
//                $this->info("Ya existe en: $file\n");
            }
        }
        $this->info('Modelos modificacdos: '.$contarEscritos);   
        $this->warn('Modelos intactos: '.$contarNoEscritos);   
    }
}