<?php
/*
//pendietnets 2025 enero
php artisan migrate
php artisan db:seed
*/
$directory = __DIR__ . '/app/Models'; // Ruta de los modelos en Laravel.
$annotation = "/**\n * @method static \\Illuminate\\Database\\Eloquent\\Collection all()\n */";

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getRealPath();
        $contents = file_get_contents($filePath);

        // Verifica si ya existe la anotación para evitar duplicados.
        if (strpos($contents, '@method static \\Illuminate\\Database\\Eloquent\\Collection all()') === false) {
            $contents = preg_replace(
                '/^<\?php\s*/',
                "<?php\n\n$annotation\n",
                $contents
            );

            file_put_contents($filePath, $contents);
            echo "Anotación agregada en: $filePath\n";
        } else {
            echo "Anotación ya existe en: $filePath\n";
        }
    }
}
