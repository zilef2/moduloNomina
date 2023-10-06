<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class SendZipFile extends Command {
    protected $signature = 'send:zip';
    protected $description = 'Enviar archivo ZIP por correo diariamente';

    public function handle() {
        try {
            $zip = new ZipArchive;
            $zipFileName = public_path(env('APP_NAME') . 'BD.zip');

            if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
                $zip->setCompressionIndex(0, ZipArchive::CM_DEFLATE, 9);

                $directory = storage_path('app/' . env('APP_NAME') . '_06oct2023');
                $pattern = '2023*';
                
                $matchingFiles = glob($directory . DIRECTORY_SEPARATOR . $pattern);
                $archivosEncontrados = count($matchingFiles);
                $this->info('directory ' . $directory . ' | Archivos encontrados: '.$archivosEncontrados);
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
                } else {
                    $this->error('Carpeta del backup no encontrada');
                    return 0;

                }
                $zip->close();

                // // Envío del correo electrónico
                Mail::send([], [], function ($message) use ($zipFileName) {
                    $message->to('ajelof2@gmail.com')
                        ->subject('Respaldo ' . env('APP_NAME'))
                        ->attach($zipFileName);
                });
                $this->info('Archivo ZIP enviado por correo.');
                return Command::SUCCESS;

            } else {
                $this->warn('Error al comprimir el archivo');
                return 0;
            }
        } catch (\Throwable $th) {
            $this->warn('Ocurrio un error| ' . $th->getMessage() . ' L: ' . $th->getLine());
            return 0;
        }
    }
}
