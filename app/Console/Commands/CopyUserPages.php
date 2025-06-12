<?php
//$ipAddress = $request->ip();

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/*
php artisan make:command copy:u
*/

class CopyUserPages extends Command {
	
	use Constants;
	
	const MSJ_EXITO = ' fue realizada con exito ';
	const MSJ_FALLO = ' Fallo';
	public $generando;
	protected $signature = 'copy:u';
	protected $description = 'Copia de la entidad generica';
	protected int $contadorMetodos;
	
	//notacion de notas:
	// //todo:
	//very usefull
	//heyRemember:
	// nexttochange:
	// todo: sync: añadir a los demas repos
	// justtesting: cuando hay que qutiar cosas que solo deberian aparecer en la version de pruebas
	//thisisnew!!!
	protected function aagenerateAttributes(): array {
		//string text number dinero date datetime boolean foreign json
		return [
			'llave_proceso'          => 'number',
			'id_proceso'             => 'number',
			'id_conexion'            => 'text',
			'fecha_proceso'          => 'datetime',
			'fecha_ultima_actuacion' => 'datetime',
			'despacho'               => 'string',
			'departamento'           => 'string',
			'sujetos_procesales'     => 'text',
			'es_privado'             => 'string',
			'cant_filas'             => 'number',
			'validacioncini'         => 'bool',
			'pdf_name'               => 'string',
			'pdf_size'               => 'string',
			'pdf_sumarized'          => 'string',
			'pdf_path'               => 'string',
		];
	}
	public function handle(): int {
		try {
			$this->generando = self::getMessage('generando');
			
			$this->contadorMetodos = 0;
			$submetodo['Lenguaje'] = 0;
			
			$modelName = $this->ask('¿Cuál es el nombre del modelo? Recuerde revisar los atributos.');
			if (!$modelName || $modelName == '') {
				$this->info('Sin modelo');
				
				
				return 0;
			}
			
			$progressBar = $this->output->createProgressBar(2);
			$progressBar->start();
			
			$this->MetodologiaInicial($modelName, 'generic', '');
			$this->AddAttributesVue($modelName);
			$this->Paso2($modelName, $submetodo);
			$progressBar->advance();
			
			$this->info(Artisan::call('optimize'));
			$this->info(Artisan::call('optimize:clear'));
			$progressBar->advance();
			$progressBar->finish();
			
			
			return 1;
		} catch (Exception $e) {
			$this->error("FALLO CONTADOR: " . $this->contadorMetodos . "FALLO Lenguaje: " . $submetodo['Lenguaje'] . " excepcion: " . $e->getMessage());
			
			
			return 0;
		}
		
	}
	
	/**
	 * @param mixed $modelName
	 * @param string $plantillaActual
	 * @param mixed $depende
	 * @return int
	 */
	public function MetodologiaInicial(mixed $modelName, string $plantillaActual, mixed $depende): int {
		$this->warn("Empezando make:model");
		Artisan::call('make:model', ['name' => $modelName, '--all' => true]);
		
		//comandos de dependencias
		$this->warn("Empezando copies");
		Artisan::call('copy:f');// Commands/WriteFillable.php
		$this->warn("Ahora Lang");
		Artisan::call('lang:u ' . $modelName);
		
		$EsValidoSeguir = $this->ValidatePages($plantillaActual, $modelName);
		
		$RealizoVueConExito = $this->MakeVuePages($plantillaActual, $modelName);
		$mensaje = $RealizoVueConExito ? self::getMessage('generando') . ' Vuejs' . self::MSJ_EXITO : self::getMessage('generando') . ' Vuejs' . self::getMessage('fallo');
		$this->info($mensaje);
		
		$RealizoControllerConExito = $this->MakeControllerPages($plantillaActual, $modelName);
		$mensaje = $RealizoControllerConExito ? self::getMessage('generando') . 'el controlador' . self::MSJ_EXITO : self::getMessage('generando') . ' controlador ' . self::getMessage('fallo');
		$this->info($mensaje);
		
		if ($RealizoControllerConExito || $RealizoVueConExito) {
			$this->replaceWordInFiles($plantillaActual, [
				'vue'        => $RealizoVueConExito,
				'controller' => $RealizoControllerConExito
			],                        $modelName, $depende);
		}
		
		
		return 1;
	}
	
	private function ValidatePages($plantillaActual, $modelName): bool {
		$folderMayus = ucfirst($modelName);
		
		//validaciones del controlador
		$ObjetoEnMira = 'Controller.php';
		$RutaDelArchivo = 'app/Http/Controllers/';
		$controllerExiste = $this->ExisteOno($RutaDelArchivo, $plantillaActual, $ObjetoEnMira);
		if (!$controllerExiste) {
			return false;
		}
		
		//vue 
		$ObjetoEnMira = '';
		$RutaDelArchivo = 'resources/js/Pages/';
		$vueExiste = $this->ExisteOno($RutaDelArchivo, $plantillaActual, $ObjetoEnMira);
		if (!$vueExiste) {
			return false;
		}
		
		
		//todo: falta los app.es y demas
		
		return true;
	}
	
	private function ExisteOno($primeraParte, $plantillaActual, $ObjetoEnMira): bool {
		$sourcePath = base_path($primeraParte . $plantillaActual . $ObjetoEnMira);
		
		if (!File::exists($sourcePath)) {
			$this->error("El $ObjetoEnMira de origen '$sourcePath' no existe.");
			
			
			return false;
		}
		
		
		return true;
	}
	
	private function MakeVuePages($plantillaActual, $modelName): bool {
		$sourcePath = base_path('resources/js/Pages/' . $plantillaActual);
		$destinationPath = base_path("resources/js/Pages/$modelName");
		
		// Add this validation
		if (!File::exists($sourcePath)) {
			$this->error("La carpeta de origen '$plantillaActual' no existe.");
			
			
			return false;
		}
		if (File::exists($destinationPath)) {
			$this->warn("La carpeta de destino '$modelName' ya existe.");
			
			
			return false;
		}
		File::copyDirectory($sourcePath, $destinationPath);
		
		
		return true;
	}
	
	private function MakeControllerPages($plantillaActual, $modelName): bool {
		$folderMayus = ucfirst($modelName);
		$sourcePath = base_path('app/Http/Controllers/' . $plantillaActual . 'Controller.php');
		if (!File::exists($sourcePath)) {
			$this->error("El controlador de origen '$sourcePath' no existe.");
			
			
			return false;
		}
		
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
	
	private function replaceWordInFiles($oldWord, $permiteRemplazo, $modelName, $depende): int {
		$folderMayus = ucfirst($modelName);
		$files = File::allFiles(base_path("resources/js/Pages/$modelName"));
		$controller = base_path("app/Http/Controllers/$folderMayus" . 'Controller.php');
		
		$depende = $depende == '' || $depende == null ? 'no_nada' : $depende;
		
		if ($permiteRemplazo['vue']) {
			foreach ($files as $file) {
				
				$content = file_get_contents($file);
				$content = str_replace(array($oldWord, ucfirst($oldWord), 'geeneric'),//ojo aqui, es estatico
				                       [$modelName, $folderMayus, $folderMayus], $content);
				file_put_contents($file, $content);
			}
		}
		
		//reemplazo de controlador
		if ($permiteRemplazo['controller']) {
			$sourcePath = base_path('app/Http/Controllers/' . ucfirst($oldWord) . 'Controller.php');
			$content = file_get_contents($sourcePath);
			$content = str_replace(array($oldWord, 'dependex', 'deependex', 'geeneric'),//TODO:ojo aqui, es estatico
			                       array($modelName, $depende, ucfirst($depende), ucfirst($modelName)), $content);
			file_put_contents($controller, $content);
		}
		
		
		return 1;
	}
	
	private function AddAttributesVue($modelName): int {
		$vueFilePath = resource_path("js/Pages/$modelName/Index.vue");
		
		if (!File::exists($vueFilePath)) {
			$this->error('El archivo Index.vue no existe.');
			
			
			return 0;
		}
		
		$content = File::get($vueFilePath);
		
		// Convertir los atributos en formato Vue
		$titulosArray = collect($this->aagenerateAttributes())->map(function ($type, $key) {
			return "    { order: '$key', label: '$key', type: '$type' },";
		})->implode("\n");
		
		// Expresión regular para encontrar la sección `const titulos = [`
		$pattern = '/const titulos = \[\s*([\s\S]*?)\s*\];/';
		
		// Reemplazar con los nuevos valores
		$replacement = "const titulos = [\n$titulosArray\n];";
		
		$newContent = preg_replace($pattern, $replacement, $content);
		
		if ($newContent) {
			File::put($vueFilePath, $newContent);
			$this->info('Archivo Index.vue actualizado correctamente.');
		}
		else {
			$this->error('No se pudo actualizar Index.vue.');
			
			
			return 0;
		}
		
		
		return 1;
	}
	
	
	
	private function Paso2($modelName, &$submetodo): int {
		//estos metodos para abajo tienen validacion
		if ($this->DoWebphp($modelName)) {
			
			$this->info('DoWebphp' . self::MSJ_EXITO);
			$this->contadorMetodos ++;
		}
		else {
			$this->error('DoWebphp ' . self::MSJ_FALLO);
			
			
			return 0;
		}
		
		if ($this->L2_LenguajeInsert($modelName, $submetodo) === 0) {
			return 0;
		}
		
		if ($this->DoSideBar($modelName)) {
			
			$this->info('DoSideBar' . self::MSJ_EXITO);
			$this->contadorMetodos ++;
		}
		else {
			$this->error('DoSideBar ' . self::MSJ_FALLO);
			
			
			return 0;
		}
		$this->DoFillable($modelName);
		$this->contadorMetodos ++;
		$this->updateMigration($modelName);
		$this->contadorMetodos ++;
		
		
		return 1;
	}
	
	private function DoWebphp($resource): int {
		$directory = 'routes';
		$files = glob($directory . '/*.php');
		
		$insertable = "Route::resource(\"/$resource\", \\App\\Http\\Controllers\\" . ucfirst($resource) . "Controller::class);\n\t//aquipues";
		
		$pattern = '/\/\/aquipues/';
		
		$contadorVerificador = 0;
		foreach ($files as $file) {
			$content = file_get_contents($file);
			$contadorVerificador ++;
			
			if (!str_contains($content, $pattern)) {
				$content2 = preg_replace($pattern, $insertable, $content);
				//                $content2 = preg_replace($pattern, "$0$insertable", $content);
				file_put_contents($file, $content2);
				if ($content == $content2) {
					$this->info("Routes Actualizado: $file\n");
				}
				else {
					$this->info("Routes sin cambios: $file\n");
				}
			}
			else {
				$this->error("No existe aquipues en: $file\n");
				$contadorVerificador = 0;
				break;
			}
		}
		
		
		return $contadorVerificador;
	}
	
	public function L2_LenguajeInsert($modelName, &$submetodo): int {
		if ($this->DoAppLenguaje($modelName)) {
			$submetodo['Lenguaje'] = 0;
			$this->info('DoAppLenguaje' . self::MSJ_EXITO);
			$this->contadorMetodos ++;
			
			foreach ($this->aagenerateAttributes() as $key => $generateAttribute) {
				$this->DoAppLenguaje($key);
				$submetodo['Lenguaje'] ++;
			}
			foreach ($this->generateForeign() as $generateAttribute) {
				$this->DoAppLenguaje($generateAttribute, 'mochar_id');
				$submetodo['Lenguaje'] ++;
			}
			
			
			return 1;
		}
		else {
			$this->error('DoAppLenguaje ' . self::MSJ_FALLO);
			$this->error('$this->contadorMetodos = ' . $this->contadorMetodos);
			$this->error('$submetodo = ' . $submetodo['Lenguaje']);
			
			
			return 0;
		}
	}
	
	private function DoAppLenguaje($resource, $mochar = 'no'): int {
		$directory = 'lang/es/app.php';
		$files = glob($directory);
		
		if ($mochar == 'mochar_id') {
			$resource_Sin_Id = substr($resource, 0, - 3);
			$insertable = "'$resource' => '$resource_Sin_Id',\n\t\t//aquipues";
		}
		else {
			$insertable = "'$resource' => '$resource',\n\t\t//aquipues";
		}
		$pattern = '/\/\/aquipues/';
		$contadorVerificador = 0;
		foreach ($files as $file) {
			$contadorVerificador ++;
			$content = file_get_contents($file);
			if (!str_contains($content, $pattern)) {
				$content2 = preg_replace($pattern, $insertable, $content);
				// $content2 = preg_replace($pattern, "$0$insertable", $content);
				file_put_contents($file, $content2);
				if ($content == $content2) {
					$this->info("Language Actualizado: $file\n");
				}
				else {
					$this->info("Language sin cambios: $file\n");
				}
			}
			else {
				$this->error("No existe aquipues en: $file\n");
				$contadorVerificador = 0;
				break;
			}
		}
		
		
		return $contadorVerificador;
		
	}
	
	protected function generateForeign(): array {
		return [//			'oferta_id' => 'oferta_id',
		];
		/*
	//            'valor_consig' => 'biginteger',
	//            'texto' => 'text',
	//            'fecha_legalizacion' => 'datetime',
	//            'descripcion' => 'text',
	//            'precio' => 'decimal',
		*/
		
	}
	
	private function DoSideBar($resource): int {
		$directory = 'resources/js/Components/SideBarMenu.vue';
		$files = glob($directory);
		
		$insertable = "'" . $resource . "',\n\t//aquipuesSide";
		$pattern = '/\/\/aquipuesSide/';
		
		$contadorVerificador = 0;
		foreach ($files as $file) {
			$content = file_get_contents($file);
			
			if (!str_contains($content, $pattern)) {
				$contadorVerificador ++;
				$content2 = preg_replace($pattern, $insertable, $content);
				//$content2 = preg_replace($pattern, "$0$insertable", $content);
				file_put_contents($file, $content2);
				if ($content != $content2) {
					$this->info("SideBarMenu.vue Actualizado: $file\n");
				}
				else {
					$this->info("SideBarMenu.vue sin cambios: $file\n");
				} //todo: revisar si ya existe
			}
			else {
				$this->error("No existe aquipues en: $file\n");
				$contadorVerificador = 0;
				break;
			}
		}
		
		
		return $contadorVerificador;
	}
	
	protected function DoFillable($modelName): int {
		$attributes = array_merge($this->aagenerateAttributes(), $this->generateForeign());
		
		// Generar el fillable
		$fillable = array_keys($attributes);
		$fillableString = implode("', '", $fillable);
		
		// Ruta del modelo
		$modelPath = app_path("Models/$modelName.php");
		
		// Verificar si el modelo existe
		if (!File::exists($modelPath)) {
			$this->error("El modelo $modelName no existe.");
			
			
			return 0;
		}
		
		// Leer el contenido del modelo
		$modelContent = File::get($modelPath);
		
		// Añadir el fillable y SoftDeletes
		$modelContent = preg_replace('/protected \$fillable = \[.*?\];/s', "protected \$fillable = ['$fillableString'];", $modelContent);
		if (!str_contains($modelContent, 'use SoftDeletes;')) {
			$modelContent = preg_replace('/class ' . $modelName . ' extends/', "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n\n    class $modelName extends", $modelContent);
		}
		
		// Generar relaciones belongsTo
		$foreignKeys = $this->generateForeign();
		foreach ($foreignKeys as $key => $value) {
			$functionName = lcfirst(str_replace('_id', '', $key)); // Nombre de la función
			$relatedModel = ucfirst(str_replace('_id', '', $key)); // Nombre del modelo relacionado
			
			// Verificar si la relación ya existe
			if (!str_contains($modelContent, "function $functionName(")) {
				$relationMethod = "\n    public function $functionName(): BelongsTo\n    {\n        return \$this->belongsTo($relatedModel::class);\n    }\n";
				
				// Insertar la relación antes del final de la clase
				$modelContent = preg_replace('/}\s*$/', "$relationMethod\n}", $modelContent);
			}
		}
		
		// Guardar el contenido modificado
		File::put($modelPath, $modelContent);
		
		$this->info("El fillable, SoftDeletes y relaciones han sido añadidos al modelo $modelName.");
		
		
		return 1;
	}
	
	protected function updateMigration($modelName): int {
		$atributos = $this->generateAttributes();
		$migrationFile = collect(glob(database_path('migrations/*.php')))->first(fn($file) => str_contains($file, 'create_' . Str::snake(Str::plural($modelName)) . '_table'))
		;
		$atributos = $this->aagenerateAttributes();
		$migrationFile = collect(glob(database_path('migrations/*.php')))->first(fn($file) => str_contains($file, 'create_' . Str::snake(Str::plural($modelName)) . '_table'));
		
		if (!$migrationFile) {
			$this->error("No se encontró la migración para $modelName");
			
			
			return 0;
		}
		
		$columns = collect($atributos)->map(function ($type, $name) {
			if ($type === 'dinero') {
				return "\$table->decimal('$name', 62, 2)->default(0);"; 
			}
			if ($type === 'dateTime') {
				return "\$table->dateTime('$name')->default(now());"; 
			}
			if ($type === 'boolean') {
				return "\$table->boolean('$name')->default(false);"; 
			}
			
			if ($type === 'number') {
				$type = 'integer';
			}
			
			
			return "\$table->$type('$name')->nullable();";
		})->implode("\n            ");
		
		$content = file_get_contents($migrationFile);
		$content = preg_replace('/Schema::create\(.*?\{/', "$0\n            $columns", $content);
		file_put_contents($migrationFile, $content);
		
		$this->info("Migración actualizada para $modelName");
		
		
		return 1;
	}
	
}
