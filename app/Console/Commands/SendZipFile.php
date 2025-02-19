<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Command\Command as CommandAlias;
use ZipArchive;

class SendZipFile extends Command {
    protected $signature = 'send:zip';
    protected $description = 'Enviar archivo ZIP por correo diariamente';

    public function handle(): int {
        try {
            $zip = new ZipArchive;//ModNom_06oct2023
            $zipFileName = public_path('ModNom_zilef2025.zip');

            if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
                $zip->setCompressionIndex(0, ZipArchive::CM_DEFLATE, 9);

                $directory = storage_path('app/ModNom_zilef2025');
//                /home/wwecno/repo/modnom2/storage/app/ModNom_zilef2025
//                $directory = storage_path('app/' . env('APP_NAME','Modnom') . '_zilef2025');
                $pattern = '*.zip';

                $matchingFiles = glob($directory . DIRECTORY_SEPARATOR . $pattern);
                $archivosEncontrados = count($matchingFiles);
                $this->info('directory ' . $directory . ' | Archivos encontrados: ' . $archivosEncontrados);
                if ($archivosEncontrados) {

                    foreach ($matchingFiles as $file) {
                        $Fulldate = basename(substr($file, 0, -4));
                        $Digits3 = substr($Fulldate, 0, 10);
                        $dateString = str_replace('-', '/', $Digits3);
                        $thedate[] = Carbon::parse($dateString)->format('Y/m/d');
                    }

                    $carbo = new Carbon();
                    $greatestDate = $carbo->max(...$thedate);

                    foreach ($matchingFiles as $file) {
                        $Fulldate = basename(substr($file, 0, -4));
                        $Digits3 = substr($Fulldate, 0, 10);
                        $dateString = str_replace('-', '/', $Digits3);
                        $thedate = Carbon::parse($dateString);
                        if ($thedate->isSameDay($greatestDate)) {
                            $archivosListos[] = $file;
                            $zip->addFile(($file), 'backup ' . env('APP_NAME'));
                        }
                        $thedate->addDay(-1);
                        if ($thedate->isSameDay($greatestDate)) {
                            $archivosListos[] = $file;
                            $zip->addFile(($file), 'backup ' . env('APP_NAME'));
                        }
                    }
                }
                else {
                    $this->error('Carpeta del backup no encontrada');
                    return 0;
                }
                $zip->close();

//                Mail::send([], [], function ($message) use ($zipFileName) {
//                    $message->to('ajelof2@gmail.com')
//                        ->subject('Respaldou ' . env('APP_NAME'))
//                        ->attach($zipFileName);
//                });
                Mail::raw('Adjunto el respaldo en formato ZIP.', function ($message) use ($zipFileName) {
                    $message->to('ajelof2@gmail.com')
                        ->subject('Respaldou ' . env('APP_NAME'))
                        ->attach($zipFileName);
                });

                $this->info('Archivo ZIP enviado por correo.');
                return CommandAlias::SUCCESS;

            }
            else {
                $this->warn('Error al comprimir el archivo');
                return 0;
            }
        } catch (\Throwable $th) {
            $this->warn('Ocurrio un error| ' . $th->getMessage() . ' L: ' . $th->getLine());
            return 0;
        }
    }
}
