<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LanguageCopyU extends Command
{
    protected $signature = 'lang:u {Combinaciones}';
    protected $description = 'Genera las combinaciones posibles y las inserta en es_appphp';

    public function handle(): void
    {
        $frase = strtolower($this->argument('Combinaciones'));

        $directory = 'lang/es/app.php';
        $files = glob($directory);
        

        $pattern = '/\/\/aquipues/';

        foreach ($files as $file) {
            $indiceLang = Str::snake($frase);
            $insertable = "'$indiceLang' => '" . ucfirst($frase) . "',\n\t\t//aquipues";
            $content = file_get_contents($file);
            if (!str_contains($content, $pattern)) {
                $content2 = preg_replace($pattern, $insertable, $content);
                //$content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content == $content2)
                    $this->info("Language updated: $file\n");
                else
                    $this->info("Language file with no changes: $file\n");
            } else {
                $this->error("No existe aquipues F: $file\n");
            }
        }
    }
}
